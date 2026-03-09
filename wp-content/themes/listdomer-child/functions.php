<?php
/**
 * Listdomer Child — Meditate Florida
 * functions.php: enqueue fonts + styles, card enhancements, sticky header JS.
 */

defined('ABSPATH') || exit;

// ─── Enqueue Google Fonts + parent + child stylesheets ────────────────────────

add_action('wp_enqueue_scripts', 'mfl_child_enqueue_styles');

function mfl_child_enqueue_styles(): void
{
    // 1. Google Fonts: Playfair Display (headings) + Lato (body)
    wp_enqueue_style(
        'mfl-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap',
        [],
        null
    );

    // 2. Parent stylesheet
    wp_enqueue_style(
        'listdomer-parent',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme('listdomer')->get('Version')
    );

    // 3. Parent assets stylesheet (where actual CSS lives in the parent)
    wp_enqueue_style(
        'listdomer-parent-assets',
        get_template_directory_uri() . '/assets/css/styles.css',
        ['listdomer-parent'],
        wp_get_theme('listdomer')->get('Version')
    );

    // 4. Child stylesheet (our overrides — must load LAST)
    wp_enqueue_style(
        'mfl-child',
        get_stylesheet_uri(),
        ['listdomer-parent-assets'],
        wp_get_theme()->get('Version')
    );
}

// ─── Sticky header + mobile search toggle (inline JS, < 1 KB) ────────────────

add_action('wp_footer', 'mfl_child_footer_scripts');

function mfl_child_footer_scripts(): void
{
    ?>
    <script>
    (function () {
        'use strict';

        // ── Sticky header scroll shadow ──────────────────────────────────────
        var header = document.getElementById('masthead');
        if (header) {
            var onScroll = function () {
                header.classList.toggle('is-scrolled', window.scrollY > 10);
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll(); // run once on load
        }

        // ── Mobile search toggle ─────────────────────────────────────────────
        var searchToggle = document.getElementById('mfl-search-toggle');
        var searchBar    = document.getElementById('mfl-header-search');
        if (searchToggle && searchBar) {
            searchToggle.addEventListener('click', function () {
                var open = searchBar.classList.toggle('is-open');
                searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) {
                    var input = searchBar.querySelector('input');
                    if (input) input.focus();
                }
            });
        }

        // ── Listing cards: inject rating stars from meta ─────────────────────
        // Listdom renders cards; we look for data-mfl-rating on card wrappers
        // (set by child theme's card template hook) and inject star HTML.
        document.querySelectorAll('.lsd-listing-item[data-mfl-rating]').forEach(function (card) {
            var rating = parseFloat(card.dataset.mflRating || '0');
            var count  = card.dataset.mflCount || '';
            if (!rating) return;

            var body = card.querySelector('.lsd-listing-body');
            if (!body) return;

            // Build star string (filled ★ vs empty ☆)
            var stars = '';
            for (var i = 1; i <= 5; i++) {
                stars += i <= Math.round(rating) ? '★' : '☆';
            }

            var row = document.createElement('div');
            row.className = 'mfl-rating-row';
            row.innerHTML =
                '<span class="mfl-stars" aria-label="' + rating.toFixed(1) + ' out of 5 stars">' + stars + '</span>' +
                (count ? '<span class="mfl-rating-count">(' + count + ' reviews)</span>' : '');

            // Insert after first child (title) of body
            var title = body.querySelector('.lsd-listing-title, .lsd-item-title, .entry-title');
            if (title && title.nextSibling) {
                body.insertBefore(row, title.nextSibling);
            } else {
                body.appendChild(row);
            }
        });
    }());
    </script>
    <?php
}

// ─── Inject rating data attributes onto listing card wrappers ─────────────────
// Listdom renders cards via its shortcode system. We hook into `the_posts` filter
// to prime a lookup table, then use `post_class` to add data attributes.

add_filter('post_class', 'mfl_listing_card_data_attrs', 20, 3);

function mfl_listing_card_data_attrs(array $classes, $class, $post_id): array
{
    if (get_post_type($post_id) !== 'listdom-listing') {
        return $classes;
    }

    // Data attributes can't be added via post_class, so we use a footer hook
    // to output a small JS object that the inline script above can consume.
    // Instead, we output data directly on the wrapper via a later Listdom hook.
    return $classes;
}

// Output a JS lookup table of rating data for all listing posts on the page.
add_action('wp_footer', 'mfl_output_rating_data', 5);

function mfl_output_rating_data(): void
{
    global $wp_query;

    // Only on pages that have listings
    if (empty($wp_query->posts)) return;

    $data = [];

    foreach ($wp_query->posts as $post) {
        if ($post->post_type !== 'listdom-listing') continue;

        $rating = get_post_meta($post->ID, '_mfl_rating', true);
        $count  = get_post_meta($post->ID, '_mfl_review_count', true);

        if ($rating) {
            $data[$post->ID] = [
                'rating' => (float) $rating,
                'count'  => (int) $count,
            ];
        }
    }

    if (empty($data)) return;

    echo '<script>window.mflListingRatings = ' . wp_json_encode($data) . ';</script>' . PHP_EOL;
}

// ─── Blog: enqueue CSS on post archives and single posts ─────────────────────

add_action('wp_enqueue_scripts', function () {
    if (is_home() || is_singular('post') || is_category() || is_tag()) {
        $child_uri = get_stylesheet_directory_uri();
        $ver       = wp_get_theme()->get('Version');
        wp_enqueue_style(
            'mfl-blog',
            $child_uri . '/assets/css/blog.css',
            ['mfl-child'],
            $ver
        );
    }
});

// ─── Homepage: enqueue CSS + JS only on the page-home.php template ───────────

add_action('wp_enqueue_scripts', 'mfl_enqueue_home_assets');

function mfl_enqueue_home_assets(): void
{
    if (!is_page_template('page-home.php')) return;

    $child_uri = get_stylesheet_directory_uri();
    $ver       = wp_get_theme()->get('Version');

    // Homepage-specific CSS
    wp_enqueue_style(
        'mfl-home',
        $child_uri . '/assets/css/home.css',
        ['mfl-child'],
        $ver
    );

    // Homepage hero JS (Places Autocomplete)
    wp_enqueue_script(
        'mfl-home-hero',
        $child_uri . '/assets/js/home-hero.js',
        [],
        $ver,
        true   // load in footer
    );

    // Pass API key + config to the script via wp_localize_script
    // (reads the same .env file as the importer — no key in source control)
    wp_localize_script('mfl-home-hero', 'mflHeroConfig', [
        'apiKey'        => mfl_get_maps_api_key(),
        'defaultRadius' => 30,   // km — adjustable
    ]);
}

/**
 * Read GOOGLE_PLACES_API_KEY from the .env file for front-end use.
 * The key IS exposed in the page source (this is normal for JS Maps API keys).
 * Restrict it by HTTP Referrer in Google Cloud Console.
 */
function mfl_get_maps_api_key(): string
{
    $env_file = ABSPATH . '.env';
    if (!file_exists($env_file)) return '';

    foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        if (trim($key) === 'GOOGLE_PLACES_API_KEY') return trim(trim($val), '"\'');
    }

    return '';
}

// ─── WordPress Customizer: Homepage settings ──────────────────────────────────

add_action('customize_register', 'mfl_customizer_homepage');

function mfl_customizer_homepage(WP_Customize_Manager $wp_customize): void
{
    // Section
    $wp_customize->add_section('mfl_homepage', [
        'title'    => __('Homepage Hero', 'listdomer-child'),
        'priority' => 30,
    ]);

    // Hero title
    $wp_customize->add_setting('mfl_hero_title', [
        'default'           => 'Find Meditation & Wellness in Florida',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('mfl_hero_title', [
        'label'   => __('Hero Headline', 'listdomer-child'),
        'section' => 'mfl_homepage',
        'type'    => 'text',
    ]);

    // Hero subtitle
    $wp_customize->add_setting('mfl_hero_subtitle', [
        'default'           => 'Discover studios, retreats, classes, and teachers near you',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('mfl_hero_subtitle', [
        'label'   => __('Hero Subheadline', 'listdomer-child'),
        'section' => 'mfl_homepage',
        'type'    => 'text',
    ]);

    // Hero background image
    $wp_customize->add_setting('mfl_hero_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'mfl_hero_bg_image', [
        'label'   => __('Hero Background Photo', 'listdomer-child'),
        'section' => 'mfl_homepage',
    ]));

    // Listings page URL
    $wp_customize->add_setting('mfl_listings_page_url', [
        'default'           => home_url('/listings/'),
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('mfl_listings_page_url', [
        'label'       => __('Listings Page URL', 'listdomer-child'),
        'description' => __('URL of the page that contains your [listdom] shortcode. Search results will be sent here.', 'listdomer-child'),
        'section'     => 'mfl_homepage',
        'type'        => 'url',
    ]);
}

// ─── Listing archive: enqueue CSS + JS ───────────────────────────────────────
// Loads on the listdom-listing CPT archive (archive-listdom-listing.php).

add_action('wp_enqueue_scripts', 'mfl_enqueue_archive_assets');

function mfl_enqueue_archive_assets(): void
{
    if (!is_post_type_archive('listdom-listing')) return;

    $child_uri = get_stylesheet_directory_uri();
    $ver       = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'mfl-archive-listings',
        $child_uri . '/assets/css/archive-listings.css',
        ['mfl-child'],
        $ver
    );

    wp_enqueue_script(
        'mfl-archive-listings',
        $child_uri . '/assets/js/archive-listings.js',
        [],
        $ver,
        true
    );
}

// ─── Single listing: enqueue CSS + lightbox JS ───────────────────────────────

add_action('wp_enqueue_scripts', 'mfl_enqueue_single_listing_assets');

function mfl_enqueue_single_listing_assets(): void
{
    if (!is_singular('listdom-listing')) return;

    $child_uri = get_stylesheet_directory_uri();
    $ver       = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'mfl-single-listing',
        $child_uri . '/assets/css/single-listing.css',
        ['mfl-child'],
        $ver
    );

    wp_enqueue_script(
        'mfl-single-listing',
        $child_uri . '/assets/js/single-listing.js',
        [],
        $ver,
        true
    );
}

// ─── Enable Listdom CPT archive ───────────────────────────────────────────────
// Listdom registers listdom-listing without has_archive by default.
// This filter adds it so WordPress generates the archive URL and our
// archive-listdom-listing.php template is picked up by the template hierarchy.

add_filter('lsd_ptype_listing_args', function (array $args): array {
    $args['has_archive'] = true;
    return $args;
});

// ─── Register newsletter widget area ─────────────────────────────────────────

add_action('widgets_init', 'mfl_register_sidebars');

function mfl_register_sidebars(): void
{
    register_sidebar([
        'name'          => __('Newsletter Band', 'listdomer-child'),
        'id'            => 'mfl-newsletter',
        'description'   => __('Replaces the default newsletter signup form in the footer. Leave empty to show the built-in placeholder form.', 'listdomer-child'),
        'before_widget' => '<div class="mfl-newsletter-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ]);
}

// ─── Redirect Listdom category archives to filtered browse page ────────────────
add_action('template_redirect', function () {
    if (is_tax('listdom-category')) {
        $term         = get_queried_object();
        $listings_page = get_page_by_path('listings');
        if ($listings_page && $term) {
            $redirect_url = add_query_arg(
                ['sf' => ['listdom-category' => $term->term_id]],
                get_permalink($listings_page)
            );
            wp_redirect($redirect_url, 301);
            exit;
        }
    }
});
