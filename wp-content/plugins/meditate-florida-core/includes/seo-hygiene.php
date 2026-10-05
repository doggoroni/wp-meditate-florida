<?php
/**
 * Indexing hygiene — fixes from the Oct 2026 Search Console review.
 *
 * Collapses duplicate / redirecting URL variants onto one canonical form,
 * keeps junk out of the core sitemap, closes the author-archive leak and
 * adds site-level schema to the homepage.
 */

defined('ABSPATH') || exit;

/** Canonical URL for a listing category: /listings/?category={term_id}. */
function mfl_category_url(int $term_id): string
{
    $base = get_post_type_archive_link('listdom-listing') ?: home_url('/listings/');
    return add_query_arg('category', $term_id, $base);
}

/** "Buddhist Center" → "Buddhist Centers"; "Spa & Wellness" stays as-is. */
function mfl_category_plural(string $name): string
{
    return preg_match('/(Center|Studio|Retreat)$/', $name) ? $name . 's' : $name;
}

// ─── Redirects ───────────────────────────────────────────────────────────────

add_action('template_redirect', 'mfl_seo_redirects', 1);
function mfl_seo_redirects(): void
{
    // Single-admin site: author archives only expose the login name.
    if (is_author()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }

    // Leftover pre-launch "Home" page duplicating the front page.
    if (is_page('home') && !is_front_page()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }

    // Listdom term archives (/categories/{slug}/) → filtered archive.
    if (is_tax('listdom-category')) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            wp_safe_redirect(mfl_category_url($term->term_id), 301);
            exit;
        }
    }

    if (!is_post_type_archive('listdom-listing')) {
        return;
    }

    $base = get_post_type_archive_link('listdom-listing') ?: home_url('/listings/');
    $args = wp_unslash($_GET);

    // ?category={name|slug} → ?category={term_id}
    $cat = (string) ($args['category'] ?? '');
    if ($cat !== '' && !ctype_digit($cat)) {
        $raw  = sanitize_text_field($cat);
        $term = get_term_by('slug', sanitize_title($raw), 'listdom-category')
             ?: get_term_by('name', $raw, 'listdom-category');
        if ($term) {
            $args['category'] = $term->term_id;
            wp_safe_redirect(add_query_arg(urlencode_deep($args), $base), 301);
            exit;
        }
    }

    // /listings/page/N/ renders page 1 (the archive paginates via ?paged=).
    $paged = (int) get_query_var('paged');
    if ($paged > 1 && !isset($args['paged']) && preg_match('#/page/\d+/?#', $_SERVER['REQUEST_URI'] ?? '')) {
        $args['paged'] = $paged;
        wp_safe_redirect(add_query_arg(urlencode_deep($args), $base), 301);
        exit;
    }
}

// Hide the users endpoint from anonymous REST requests (enumerates logins).
add_filter('rest_endpoints', function (array $endpoints): array {
    if (!is_user_logged_in()) {
        unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});

// ─── Robots meta ─────────────────────────────────────────────────────────────

add_filter('wp_robots', 'mfl_seo_robots');
function mfl_seo_robots(array $robots): array
{
    $noindex = is_search()
        // Deep archive pagination is thin, near-duplicate content.
        || ((is_post_type_archive('listdom-listing') || is_page('listings'))
            && (int) get_query_var('paged', 1) >= 10);

    if ($noindex) {
        $robots['noindex'] = true;
        $robots['follow']  = true;
        unset($robots['max-image-preview']);
    }
    return $robots;
}

// ─── Core sitemap (wp-sitemap.xml) ───────────────────────────────────────────
// Listings, cities and categories live in /sitemap-listings.xml.

add_filter('wp_sitemaps_add_provider', function ($provider, string $name) {
    return $name === 'users' ? false : $provider;
}, 10, 2);

add_filter('wp_sitemaps_post_types', function (array $types): array {
    unset($types['listdom-listing'], $types['mailpoet_page']);
    return $types;
});

add_filter('wp_sitemaps_taxonomies', function (array $taxonomies): array {
    unset($taxonomies['listdom-category']);
    return $taxonomies;
});

add_filter('wp_sitemaps_posts_query_args', function (array $args, string $post_type): array {
    if ($post_type === 'page') {
        $legacy_home = get_page_by_path('home');
        if ($legacy_home && (int) $legacy_home->ID !== (int) get_option('page_on_front')) {
            $args['post__not_in'] = array_merge($args['post__not_in'] ?? [], [$legacy_home->ID]);
        }
    }
    return $args;
}, 10, 2);

// ─── Homepage site schema ────────────────────────────────────────────────────

add_action('wp_head', 'mfl_output_site_schema');
function mfl_output_site_schema(): void
{
    if (!is_front_page()) {
        return;
    }

    $home = home_url('/');
    $org  = [
        '@type'       => 'Organization',
        '@id'         => $home . '#organization',
        'name'        => 'Meditate Florida',
        'url'         => $home,
        'description' => "Florida's Mindfulness Directory — a free directory of meditation centers, yoga studios and retreats across Florida.",
    ];
    $logo = get_site_icon_url(512);
    if ($logo) {
        $org['logo'] = $logo;
    }

    $graph = [
        [
            '@type'      => 'WebSite',
            '@id'        => $home . '#website',
            'name'       => 'Meditate Florida',
            'url'        => $home,
            'inLanguage' => get_bloginfo('language'),
            'publisher'  => ['@id' => $home . '#organization'],
        ],
        $org,
    ];

    echo '<script type="application/ld+json">'
       . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
       . '</script>' . PHP_EOL;
}
