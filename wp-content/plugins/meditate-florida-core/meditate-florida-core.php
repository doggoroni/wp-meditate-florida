<?php
/**
 * Plugin Name:  Meditate Florida Core
 * Description:  Automated Google Places importer for Florida meditation businesses.
 *               Runs weekly via WP-Cron, creates/updates Listdom listings,
 *               and logs all activity to wp-content/logs/places-import.log.
 * Version:      1.0.0
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

// ─── Constants ────────────────────────────────────────────────────────────────

define('MFL_VERSION',   '1.0.0');
define('MFL_DIR',       plugin_dir_path(__FILE__));
define('MFL_CRON_HOOK', 'mfl_weekly_places_import');
define('MFL_LOG_FILE',  WP_CONTENT_DIR . '/logs/places-import.log');
define('MFL_ENV_FILE',  ABSPATH . '.env');

// ─── Autoload ────────────────────────────────────────────────────────────────

require_once MFL_DIR . 'includes/class-logger.php';
require_once MFL_DIR . 'includes/class-places-importer.php';
require_once MFL_DIR . 'includes/class-search-handler.php';

// Register AJAX search handler
add_action('init', function () {
    (new MFL_Search_Handler())->register();
});

// ─── Listing image helper ────────────────────────────────────────────────────

/**
 * Resolve the display image for a listing from locally hosted attachments only.
 *
 * Priority: featured image → lsd_ava attachment (Listdom avatar).
 * Deliberately never falls back to _mfl_image_url: those are Google Places
 * Photo API links that bill per pageview, expire, and embed the API key in
 * the public HTML. Run `wp mfl backfill-images` to populate local images.
 */
function mfl_listing_image_url(int $post_id, string $size = 'medium_large'): string
{
    $url = get_the_post_thumbnail_url($post_id, $size);
    if ($url) {
        return $url;
    }

    $ava_id = (int) get_post_meta($post_id, 'lsd_ava', true);
    if ($ava_id) {
        $url = wp_get_attachment_image_url($ava_id, $size);
        if ($url) {
            return $url;
        }
    }

    return '';
}

// ─── WP-CLI: image backfill ──────────────────────────────────────────────────

if (defined('WP_CLI') && WP_CLI) {
    /**
     * Download Google Places photos into the media library for listings that
     * don't have a local image yet. Fetches a fresh photo_reference per place,
     * so it works even when the stored _mfl_image_url has expired.
     *
     * ## OPTIONS
     *
     * [--limit=<n>]
     * : Max number of listings to process this run (0 = all). Default 0.
     *
     * ## EXAMPLES
     *     wp mfl backfill-images
     *     wp mfl backfill-images --limit=25
     */
    WP_CLI::add_command('mfl backfill-images', function ($args, $assoc_args) {
        $limit    = (int) ($assoc_args['limit'] ?? 0);
        $logger   = new MFL_Logger(MFL_LOG_FILE);
        $importer = new MFL_Places_Importer($logger);
        $stats    = $importer->backfill_images($limit);

        WP_CLI::log(sprintf(
            'Processed: %d | Images added: %d | Skipped (no photo): %d | Errors: %d',
            $stats['processed'], $stats['added'], $stats['skipped'], $stats['errors']
        ));

        if ($stats['errors'] > 0) {
            WP_CLI::warning('Some downloads failed — see wp-content/logs/places-import.log');
        } else {
            WP_CLI::success('Backfill complete.');
        }
    });
}

// ─── Activation / Deactivation ───────────────────────────────────────────────

register_activation_hook(__FILE__, 'mfl_activate');
register_deactivation_hook(__FILE__, 'mfl_deactivate');

function mfl_activate(): void
{
    // Create the logs directory if it doesn't exist yet.
    $log_dir = dirname(MFL_LOG_FILE);
    if (!is_dir($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    // Write a .htaccess so the log file is not publicly accessible.
    $htaccess = $log_dir . '/.htaccess';
    if (!file_exists($htaccess)) {
        file_put_contents($htaccess, "Deny from all\n");
    }

    // Schedule the first run at the next Sunday 03:00 in the site's timezone.
    if (!wp_next_scheduled(MFL_CRON_HOOK)) {
        wp_schedule_event(mfl_next_sunday_3am(), 'weekly', MFL_CRON_HOOK);
    }
}

function mfl_deactivate(): void
{
    $timestamp = wp_next_scheduled(MFL_CRON_HOOK);
    if ($timestamp) {
        wp_unschedule_event($timestamp, MFL_CRON_HOOK);
    }
}

// ─── Cron callback ───────────────────────────────────────────────────────────

add_action(MFL_CRON_HOOK, 'mfl_run_import');

function mfl_run_import(): void
{
    // Prevent PHP from timing out on large imports.
    set_time_limit(0);

    $logger   = new MFL_Logger(MFL_LOG_FILE);
    $importer = new MFL_Places_Importer($logger);
    $importer->run_all_cities();
}

// ─── Contact form handler ─────────────────────────────────────────────────────

// Works for both logged-in and non-logged-in users
add_action('admin_post_nopriv_mfl_listing_contact', 'mfl_handle_listing_contact');
add_action('admin_post_mfl_listing_contact',        'mfl_handle_listing_contact');

function mfl_handle_listing_contact(): void
{
    // 1. Verify nonce
    $listing_id = (int) ($_POST['listing_id'] ?? 0);
    if (!wp_verify_nonce($_POST['mfl_contact_nonce'] ?? '', 'mfl_listing_contact_' . $listing_id)) {
        wp_die('Security check failed.', 'Error', ['response' => 403]);
    }

    // 2. Honeypot check (bot trap — field must be empty)
    if (!empty($_POST['website'])) {
        wp_safe_redirect(add_query_arg('mfl_contact', 'sent', get_permalink($listing_id)));
        exit;
    }

    // 3. Sanitise inputs
    $name     = sanitize_text_field($_POST['contact_name']    ?? '');
    $email    = sanitize_email($_POST['contact_email']        ?? '');
    $phone    = sanitize_text_field($_POST['contact_phone']   ?? '');
    $message  = sanitize_textarea_field($_POST['contact_message'] ?? '');
    $redirect = esc_url_raw($_POST['redirect'] ?? get_permalink($listing_id));

    // 4. Basic validation
    if (!$name || !is_email($email) || !$message) {
        wp_safe_redirect(add_query_arg('mfl_contact', 'error', $redirect));
        exit;
    }

    // 5. Build email
    $listing_title = get_the_title($listing_id);
    $listing_email = sanitize_email(get_post_meta($listing_id, 'lsd_email', true));
    $to            = $listing_email ?: get_option('mfl_contact_email', get_option('admin_email'));
    $subject       = sprintf('[Meditate Florida] Message re: %s', $listing_title);
    $body          = sprintf(
        "New message from the Meditate Florida listing page.\n\n" .
        "Listing: %s\nURL: %s\n\n" .
        "From: %s\nEmail: %s\nPhone: %s\n\n" .
        "Message:\n%s\n",
        $listing_title,
        get_permalink($listing_id),
        $name, $email, $phone ?: 'Not provided',
        $message
    );
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    // 6. Send and redirect
    $sent = wp_mail($to, $subject, $body, $headers);
    wp_safe_redirect(add_query_arg('mfl_contact', $sent ? 'sent' : 'error', $redirect));
    exit;
}

// ─── Newsletter signup handler ────────────────────────────────────────────────

add_action('admin_post_nopriv_mfl_newsletter_signup', 'mfl_handle_newsletter_signup');
add_action('admin_post_mfl_newsletter_signup',        'mfl_handle_newsletter_signup');

function mfl_handle_newsletter_signup(): void
{
    // 1. Nonce check
    if (!wp_verify_nonce($_POST['mfl_newsletter_nonce'] ?? '', 'mfl_newsletter_signup')) {
        wp_die('Security check failed.', 'Error', ['response' => 403]);
    }

    // 2. Sanitise and validate
    $email    = sanitize_email($_POST['mfl_email'] ?? '');
    $redirect = wp_get_referer() ?: home_url('/');

    if (!is_email($email)) {
        wp_safe_redirect(add_query_arg('mfl_newsletter', 'error', $redirect));
        exit;
    }

    // 3. Send notification email to site owner
    $notify_to = get_option('mfl_contact_email', get_option('admin_email'));
    $subject   = '[Meditate Florida] New newsletter subscriber';
    $body      = sprintf(
        "A new subscriber signed up via the Meditate Florida footer form.\n\n" .
        "Email: %s\nDate: %s\nSite: %s\n",
        $email,
        current_time('Y-m-d H:i:s'),
        home_url()
    );
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    wp_mail($notify_to, $subject, $body, $headers);

    // 4. Redirect back with success flag
    wp_safe_redirect(add_query_arg('mfl_newsletter', 'sent', $redirect));
    exit;
}

// ─── Claim listing handler ────────────────────────────────────────────────────

add_action('admin_post_nopriv_mfl_claim_listing', 'mfl_handle_claim_listing');
add_action('admin_post_mfl_claim_listing',        'mfl_handle_claim_listing');

function mfl_handle_claim_listing(): void
{
    // 1. Nonce
    $listing_id = (int) ($_POST['listing_id'] ?? 0);
    if (!wp_verify_nonce($_POST['mfl_claim_nonce'] ?? '', 'mfl_claim_listing_' . $listing_id)) {
        wp_die('Security check failed.', 'Error', ['response' => 403]);
    }

    // 2. Honeypot
    if (!empty($_POST['website'])) {
        wp_safe_redirect(add_query_arg('mfl_claim', 'sent', get_permalink($listing_id)));
        exit;
    }

    // 3. Sanitise
    $name     = sanitize_text_field($_POST['claim_name']     ?? '');
    $email    = sanitize_email($_POST['claim_email']         ?? '');
    $note     = sanitize_textarea_field($_POST['claim_note'] ?? '');
    $redirect = esc_url_raw($_POST['redirect'] ?? get_permalink($listing_id));

    // 4. Validate
    if (!$name || !is_email($email)) {
        wp_safe_redirect(add_query_arg('mfl_claim', 'error', $redirect));
        exit;
    }

    // 5. Build and send email
    $listing_title = get_the_title($listing_id);
    $to      = get_option('mfl_contact_email', get_option('admin_email'));
    $subject = sprintf('[Meditate Florida] Claim request: %s', $listing_title);
    $body    = sprintf(
        "Someone has requested to claim a listing on Meditate Florida.\n\n" .
        "Listing: %s\nURL: %s\n\n" .
        "Claimant name:  %s\n" .
        "Claimant email: %s\n\n" .
        "Note:\n%s\n",
        $listing_title,
        get_permalink($listing_id),
        $name,
        $email,
        $note ?: '(none)'
    );
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    $sent = wp_mail($to, $subject, $body, $headers);
    wp_safe_redirect(add_query_arg('mfl_claim', $sent ? 'sent' : 'error', $redirect));
    exit;
}

// ─── Settings page ────────────────────────────────────────────────────────────

add_action('admin_menu', function () {
    add_options_page(
        'Meditate Florida Settings',
        'Meditate Florida',
        'manage_options',
        'mfl-settings',
        'mfl_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting('mfl_settings', 'mfl_contact_email', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default'           => get_option('admin_email'),
    ]);
});

function mfl_settings_page(): void
{
    ?>
    <div class="wrap">
        <h1>Meditate Florida Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('mfl_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="mfl_contact_email">Contact Form Recipient</label></th>
                    <td>
                        <input type="email" id="mfl_contact_email" name="mfl_contact_email"
                               value="<?php echo esc_attr(get_option('mfl_contact_email', get_option('admin_email'))); ?>"
                               class="regular-text">
                        <p class="description">Listing contact form messages will be sent to this address.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// ─── Canonical URLs & Pagination Noindex ─────────────────────────────────────

add_action('wp_head', 'mfl_output_canonical', 2);
function mfl_output_canonical(): void {
    // Only intervene on the listings archive — WP handles singles automatically.
    if (!is_post_type_archive('listdom-listing') && !is_page('listings')) return;

    $base   = get_post_type_archive_link('listdom-listing') ?: home_url('/listings/');
    $params = [];

    $city = sanitize_text_field($_GET['city'] ?? '');
    if ($city) $params['city'] = $city;

    $cat = absint($_GET['category'] ?? 0);
    if ($cat) $params['category'] = $cat;

    // city= and category= are the only params worth canonicalising to
    $canonical = $params ? add_query_arg($params, $base) : $base;
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . PHP_EOL;
}

// Prevent deep pagination pages from being indexed (thin content)
add_action('wp_head', 'mfl_noindex_deep_pagination', 3);
function mfl_noindex_deep_pagination(): void {
    if (!is_post_type_archive('listdom-listing') && !is_page('listings')) return;
    if ((int) get_query_var('paged', 1) >= 10) {
        echo '<meta name="robots" content="noindex, follow">' . PHP_EOL;
    }
}

// ─── Meta Titles, Descriptions & Open Graph ──────────────────────────────────

add_filter('document_title_parts', 'mfl_custom_title_parts', 20);
function mfl_custom_title_parts(array $parts): array {
    if (is_singular('listdom-listing')) {
        $terms = get_the_terms(get_the_ID(), 'listdom-category');
        $cat   = (!is_wp_error($terms) && !empty($terms)) ? $terms[0]->name . ' · ' : '';
        $addr  = sanitize_text_field(get_post_meta(get_the_ID(), 'lsd_address', true));
        preg_match('/,\s*([^,]+),\s*FL/i', $addr, $m);
        $city        = !empty($m[1]) ? ' in ' . trim($m[1]) : ' — Florida';
        $parts['title'] = get_the_title() . $city . ' | ' . $cat . 'Meditate Florida';
        unset($parts['site'], $parts['tagline']);
    } elseif (is_post_type_archive('listdom-listing') || is_page('listings')) {
        $city     = sanitize_text_field($_GET['city'] ?? '');
        $cat_id   = absint($_GET['category'] ?? 0);
        $cat_term = $cat_id ? get_term($cat_id, 'listdom-category') : null;
        $cat_name = ($cat_term && !is_wp_error($cat_term)) ? $cat_term->name : '';
        $qualifier = array_filter([$cat_name, $city ? "in $city" : 'in Florida']);
        $parts['title'] = 'Meditation & Wellness Locations ' . implode(' ', $qualifier) . ' | Meditate Florida';
        unset($parts['site'], $parts['tagline']);
    } elseif (is_home()) {
        $parts['title'] = 'Meditation & Wellness Blog | Meditate Florida';
        unset($parts['site'], $parts['tagline']);
    }
    return $parts;
}

add_action('wp_head', 'mfl_output_meta_tags', 1);
function mfl_output_meta_tags(): void {
    global $post;
    $desc = '';

    if (is_singular('listdom-listing')) {
        $addr  = sanitize_text_field(get_post_meta($post->ID, 'lsd_address', true));
        $terms = get_the_terms($post->ID, 'listdom-category');
        $cat   = (!is_wp_error($terms) && !empty($terms)) ? $terms[0]->name : 'Wellness';
        preg_match('/,\s*([^,]+),\s*FL/i', $addr ?? '', $m);
        $city  = !empty($m[1]) ? trim($m[1]) . ', FL' : 'Florida';
        $desc  = get_the_title($post->ID) . ' — ' . $cat . ' in ' . $city . '. View hours, contact info, and directions on Meditate Florida.';
    } elseif (is_post_type_archive('listdom-listing') || is_page('listings')) {
        $desc = 'Browse 800+ yoga studios, meditation centers, and retreat spaces across Florida. Filter by city, category, and rating — free directory.';
    } elseif (is_singular('post')) {
        $desc = wp_trim_words(get_the_excerpt() ?: strip_tags($post->post_content), 25, '…');
    } elseif (is_home()) {
        $desc = 'Tips, guides, and stories about meditation, yoga, and mindfulness in Florida. Updated monthly.';
    } elseif (is_front_page()) {
        $desc = 'Discover meditation centers, yoga studios, and wellness retreats across Florida. Free, searchable directory with 800+ locations.';
    }

    if ($desc) {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . PHP_EOL;
    }

    // Open Graph
    $og_title = wp_get_document_title();
    $og_url   = is_singular() ? get_permalink() : home_url(add_query_arg([]));
    $og_image = (is_singular() && has_post_thumbnail())
        ? get_the_post_thumbnail_url(null, 'large') : '';
    if (!$og_image && is_singular('listdom-listing')) {
        $og_image = mfl_listing_image_url($post->ID, 'large');
    }
    $og_type  = is_singular('post') ? 'article' : 'website';

    echo '<meta property="og:type"        content="' . esc_attr($og_type)  . '">' . PHP_EOL;
    echo '<meta property="og:title"       content="' . esc_attr($og_title) . '">' . PHP_EOL;
    if ($desc) echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . PHP_EOL;
    echo '<meta property="og:url"         content="' . esc_url($og_url)    . '">' . PHP_EOL;
    echo '<meta property="og:site_name"   content="Meditate Florida">'            . PHP_EOL;
    if ($og_image) echo '<meta property="og:image"       content="' . esc_url($og_image) . '">' . PHP_EOL;
}

// ─── Listings XML Sitemap ─────────────────────────────────────────────────────

add_action('init', function () {
    add_rewrite_rule('^sitemap-listings\.xml$', 'index.php?mfl_sitemap=listings', 'top');
});
add_filter('query_vars', function (array $vars): array { $vars[] = 'mfl_sitemap'; return $vars; });

add_action('template_redirect', 'mfl_serve_sitemap');
function mfl_serve_sitemap(): void {
    if (get_query_var('mfl_sitemap') !== 'listings') return;

    $base = get_post_type_archive_link('listdom-listing') ?: home_url('/listings/');

    header('Content-Type: application/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
       . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Archive root
    echo "<url><loc>" . esc_url($base) . "</loc><changefreq>daily</changefreq><priority>0.9</priority></url>\n";

    // City landing pages
    foreach (MFL_Search_Handler::CITIES as $city) {
        echo "<url><loc>" . esc_url(add_query_arg('city', urlencode($city), $base))
           . "</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>\n";
    }

    // Category landing pages
    $cats = get_terms(['taxonomy' => 'listdom-category', 'hide_empty' => true]);
    if (!is_wp_error($cats)) {
        foreach ($cats as $cat) {
            echo "<url><loc>" . esc_url(add_query_arg('category', $cat->term_id, $base))
               . "</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>\n";
        }
    }

    // Individual listings — paginated to avoid memory issues on large sets
    $paged = 1;
    do {
        $q = new WP_Query([
            'post_type'      => 'listdom-listing',
            'posts_per_page' => 200,
            'paged'          => $paged,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'no_found_rows'  => false,
        ]);
        foreach ($q->posts as $id) {
            echo "<url><loc>" . esc_url(get_permalink($id)) . "</loc>"
               . "<lastmod>" . esc_html(get_post_modified_time('c', true, $id)) . "</lastmod>"
               . "<changefreq>monthly</changefreq><priority>0.6</priority></url>\n";
        }
        $paged++;
    } while ($paged <= $q->max_num_pages);

    echo '</urlset>';
    exit;
}

// ─── BreadcrumbList JSON-LD Schema ───────────────────────────────────────────

add_action('wp_head', 'mfl_output_breadcrumb_schema');
function mfl_output_breadcrumb_schema(): void {
    $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url('/')]];
    $pos   = 2;

    if (is_singular('listdom-listing')) {
        $terms = get_the_terms(get_the_ID(), 'listdom-category');
        if (!is_wp_error($terms) && !empty($terms)) {
            $term    = $terms[0];
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $term->name,
                'item'     => add_query_arg('category', $term->term_id,
                                 get_post_type_archive_link('listdom-listing') ?: home_url('/listings/')),
            ];
        }
        $items[] = ['@type' => 'ListItem', 'position' => $pos, 'name' => get_the_title()];

    } elseif (is_post_type_archive('listdom-listing') || is_page('listings')) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos,
            'name'     => 'Browse Locations',
            'item'     => get_post_type_archive_link('listdom-listing') ?: home_url('/listings/'),
        ];

    } elseif (is_singular('post')) {
        $cats = get_the_category();
        if (!empty($cats)) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $cats[0]->name,
                'item'     => get_category_link($cats[0]->term_id),
            ];
        }
        $items[] = ['@type' => 'ListItem', 'position' => $pos, 'name' => get_the_title()];

    } else {
        return;
    }

    echo '<script type="application/ld+json">'
       . wp_json_encode(
             ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items],
             JSON_UNESCAPED_SLASHES
         )
       . '</script>' . PHP_EOL;
}

// ─── LocalBusiness JSON-LD Schema ────────────────────────────────────────────

/**
 * Output JSON-LD LocalBusiness schema on single listing pages.
 */
add_action('wp_head', 'mfl_output_listing_schema');
function mfl_output_listing_schema(): void {
    if (!is_singular('listdom-listing')) return;

    $post_id = get_the_ID();
    $title   = get_the_title($post_id);
    $url     = get_permalink($post_id);

    $phone   = sanitize_text_field(get_post_meta($post_id, 'lsd_phone',   true));
    $website = esc_url(get_post_meta($post_id,             'lsd_website', true));
    $email   = sanitize_email(get_post_meta($post_id,      'lsd_email',   true));
    $address = sanitize_text_field(get_post_meta($post_id, 'lsd_address', true));

    // Locally hosted image only (featured image → lsd_ava attachment)
    $image = mfl_listing_image_url($post_id, 'large');

    // Ratings stored by importer
    $rating_value = (float) get_post_meta($post_id, '_mfl_rating',       true);
    $rating_count = (int)   get_post_meta($post_id, '_mfl_review_count', true);

    // Hours — stored as JSON array of Google weekday_text strings:
    // ["Monday: 9:00 AM – 5:00 PM", "Tuesday: Closed", …] (index 0 = Monday)
    $hours_spec = [];
    $hours_json = get_post_meta($post_id, '_mfl_hours_json', true);
    if ($hours_json) {
        $hours_raw = json_decode($hours_json, true);
        $day_names = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        if (is_array($hours_raw)) {
            foreach ($hours_raw as $i => $line) {
                if (!isset($day_names[$i])) continue;
                $parts = explode(': ', $line, 2);
                if (count($parts) < 2) continue;
                $time_str = trim($parts[1]);
                if (stripos($time_str, 'Closed') !== false) continue;
                if (stripos($time_str, 'Open 24') !== false) {
                    $hours_spec[] = [
                        '@type'     => 'OpeningHoursSpecification',
                        'dayOfWeek' => 'https://schema.org/' . $day_names[$i],
                        'opens'     => '00:00',
                        'closes'    => '23:59',
                    ];
                    continue;
                }
                // Parse "9:00 AM – 5:00 PM"
                if (!preg_match('/(\d{1,2}:\d{2}\s*[AP]M)\s*[–\-]\s*(\d{1,2}:\d{2}\s*[AP]M)/i', $time_str, $m)) {
                    continue;
                }
                $hours_spec[] = [
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => 'https://schema.org/' . $day_names[$i],
                    'opens'     => date('H:i', strtotime($m[1])),
                    'closes'    => date('H:i', strtotime($m[2])),
                ];
            }
        }
    }

    // Category → schema @type
    $terms = get_the_terms($post_id, 'listdom-category');
    $term_name = (!is_wp_error($terms) && !empty($terms)) ? strtolower($terms[0]->name) : '';
    $type_map = [
        'yoga studio'        => 'ExerciseGym',
        'meditation retreat' => 'LodgingBusiness',
        'buddhist center'    => 'PlaceOfWorship',
        'spa & wellness'     => 'HealthAndBeautyBusiness',
        'wellness center'    => 'MedicalBusiness',
    ];
    $schema_type = $type_map[$term_name] ?? 'LocalBusiness';

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => $schema_type,
        'name'     => $title,
        'url'      => $url,
    ];
    if ($image)            $schema['image']     = $image;
    if ($phone)            $schema['telephone'] = $phone;
    if ($website)          $schema['sameAs']    = $website;
    if ($email)            $schema['email']     = $email;
    if ($address)          $schema['address']   = ['@type' => 'PostalAddress', 'streetAddress' => $address];
    if (!empty($hours_spec)) $schema['openingHoursSpecification'] = $hours_spec;
    if ($rating_value > 0 && $rating_count > 0) {
        $schema['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => round($rating_value, 1),
            'reviewCount' => $rating_count,
            'bestRating'  => 5,
            'worstRating' => 1,
        ];
    }

    echo '<script type="application/ld+json">'
       . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
       . '</script>' . PHP_EOL;
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * Returns the Unix timestamp of the next Sunday at 03:00 in the site timezone.
 * If today is Sunday and it's before 3am, returns today at 3am.
 */
function mfl_next_sunday_3am(): int
{
    $tz  = new DateTimeZone(wp_timezone_string() ?: 'America/New_York');
    $now = new DateTime('now', $tz);

    $days_until_sunday = (7 - (int) $now->format('w')) % 7;

    // If it's already Sunday but 3am hasn't passed, run today.
    if ($days_until_sunday === 0 && (int) $now->format('H') < 3) {
        $days_until_sunday = 0;
    } elseif ($days_until_sunday === 0) {
        // It's Sunday but past 3am — schedule for next Sunday.
        $days_until_sunday = 7;
    }

    $target = clone $now;
    if ($days_until_sunday > 0) {
        $target->modify("+{$days_until_sunday} days");
    }
    $target->setTime(3, 0, 0);

    return $target->getTimestamp();
}
