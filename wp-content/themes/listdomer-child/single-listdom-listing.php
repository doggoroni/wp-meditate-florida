<?php
/**
 * Listdomer Child — Single Listing Template
 * Bypasses Listdom's filter_content hook for full layout control.
 *
 * Template: single-listdom-listing.php (WordPress template hierarchy)
 *
 * Meta keys used:
 *   Listdom:  lsd_address, lsd_latitude, lsd_longitude, lsd_phone,
 *             lsd_website, lsd_email, lsd_gallery (array of attach IDs),
 *             lsd_ava (featured image attach ID)
 *   Custom:   _mfl_rating, _mfl_review_count, _mfl_hours_json,
 *             _mfl_google_place_id, _mfl_image_url
 */

defined('ABSPATH') || exit;

get_header();

// ── 1. Gather all meta ───────────────────────────────────────────────────────

$post_id     = get_the_ID();
$title       = get_the_title();
$content     = get_the_content(); // "About" description stored in post_content
$permalink   = get_permalink();

// Listdom meta
$address     = get_post_meta($post_id, 'lsd_address',   true);
$lat         = (float) get_post_meta($post_id, 'lsd_latitude',  true);
$lng         = (float) get_post_meta($post_id, 'lsd_longitude', true);
$phone       = get_post_meta($post_id, 'lsd_phone',     true);
$website     = get_post_meta($post_id, 'lsd_website',   true);
$email       = get_post_meta($post_id, 'lsd_email',     true);
$gallery_ids = (array) get_post_meta($post_id, 'lsd_gallery', true);
$ava_id      = get_post_meta($post_id, 'lsd_ava',       true);
$classes_txt = get_post_meta($post_id, 'lsd_classes',   true); // freeform schedule text

// Custom import meta
$rating      = (float) get_post_meta($post_id, '_mfl_rating',       true);
$review_cnt  = (int)   get_post_meta($post_id, '_mfl_review_count', true);
$hours_json  = get_post_meta($post_id, '_mfl_hours_json', true);
// Hero image: locally hosted attachments only (featured image → lsd_ava).
// The old _mfl_image_url fallback hotlinked the Google Places Photo API,
// which bills per view, expires, and exposes the API key in the HTML.
$hero_src = function_exists('mfl_listing_image_url')
    ? mfl_listing_image_url($post_id, 'full') : '';

// Gallery: filter out empty / non-numeric IDs
$gallery_ids = array_filter($gallery_ids, 'is_numeric');

// Opening hours array: stored as JSON string
$hours = [];
if ($hours_json) {
    $decoded = json_decode($hours_json, true);
    if (is_array($decoded)) {
        $hours = $decoded;
    }
}

// Primary category
$categories = get_the_terms($post_id, 'listdom-category');
$primary_cat = (!empty($categories) && !is_wp_error($categories)) ? $categories[0] : null;

// Category archive link
$listings_url  = get_theme_mod('mfl_listings_page_url', home_url('/listings/'));
$cat_filter_url = $primary_cat
    ? add_query_arg(['sf' => ['listdom-category' => $primary_cat->term_id]], $listings_url)
    : $listings_url;

// ── 2. Star helper ───────────────────────────────────────────────────────────

function mfl_sl_stars(float $r): string
{
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= round($r) ? '★' : '☆';
    }
    return $out;
}

// ── 3. Similar listings (same category, 50-mile radius via Haversine) ────────

function mfl_sl_similar(int $post_id, float $lat, float $lng, ?object $cat, int $limit = 4): array
{
    global $wpdb;

    if (!$cat || (!$lat && !$lng)) {
        // Fallback: same city extracted from address
        return [];
    }

    $radius_miles = 50;

    // Haversine in MySQL; lsd_data stores lat/lng as numeric columns
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT p.ID, p.post_title,
                    pm_r.meta_value    AS rating,
                    pm_a.meta_value    AS address,
                    ( 3959 * ACOS( LEAST(1, COS(RADIANS(%f)) * COS(RADIANS(d.latitude))
                        * COS(RADIANS(d.longitude) - RADIANS(%f))
                        + SIN(RADIANS(%f)) * SIN(RADIANS(d.latitude)) ) ) ) AS distance_miles
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->prefix}lsd_data d    ON d.id = p.ID
             INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
             LEFT  JOIN {$wpdb->postmeta} pm_r    ON pm_r.post_id    = p.ID AND pm_r.meta_key    = '_mfl_rating'
             LEFT  JOIN {$wpdb->postmeta} pm_a    ON pm_a.post_id    = p.ID AND pm_a.meta_key    = 'lsd_address'
             WHERE p.post_type   = 'listdom-listing'
               AND p.post_status = 'publish'
               AND p.ID         != %d
               AND tr.term_taxonomy_id = %d
             HAVING distance_miles <= %f
             ORDER BY distance_miles ASC
             LIMIT %d",
            $lat, $lng, $lat, $post_id, $cat->term_taxonomy_id, $radius_miles, $limit
        )
    );

    return $results ?: [];
}

$similar = mfl_sl_similar($post_id, $lat, $lng, $primary_cat);

// ── 4. Today index (0=Sunday) ────────────────────────────────────────────────

$today_index = (int) current_time('w'); // 0=Sun … 6=Sat
// Hours array starts Monday in Google's format, shift accordingly
// Google: Monday(0)…Sunday(6) in hours_json
$day_map = ['Sunday' => 6, 'Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 2,
            'Thursday' => 3, 'Friday' => 4, 'Saturday' => 5];
$wp_to_google = [0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5]; // wp day => google index
$today_google_index = $wp_to_google[$today_index];

// ── 5. Contact form processing result (set by admin-post handler) ─────────────

$contact_sent    = isset($_GET['mfl_contact']) && $_GET['mfl_contact'] === 'sent';
$contact_error   = isset($_GET['mfl_contact']) && $_GET['mfl_contact'] === 'error';
$claim_sent      = isset($_GET['mfl_claim'])   && $_GET['mfl_claim']   === 'sent';
$claim_error     = isset($_GET['mfl_claim'])   && $_GET['mfl_claim']   === 'error';

?>

<!-- ═══════════════════════════════════════════════════════════════ HERO ══ -->
<section class="mfl-sl-hero" aria-label="<?php echo esc_attr($title); ?> hero photo">
    <?php if ($hero_src) : ?>
        <?php // Blurred fill layer: lets the sharp photo render `contain` so
              // portrait/odd-aspect photos aren't awkwardly crop-zoomed. ?>
        <div class="mfl-sl-hero__bg" aria-hidden="true"
             style="background-image:url('<?php echo esc_url($hero_src); ?>');"></div>
        <img src="<?php echo esc_url($hero_src); ?>"
             alt="<?php echo esc_attr($title); ?>"
             class="mfl-sl-hero__img" loading="eager">
    <?php else : ?>
        <div class="mfl-sl-hero__placeholder" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="mfl-sl-hero__overlay" aria-hidden="true"></div>

    <!-- Business info overlaid on hero -->
    <div class="mfl-sl-hero__info">
        <h1 class="mfl-sl-hero__title"><?php echo esc_html($title); ?></h1>
        <div class="mfl-sl-hero__meta">
            <?php if ($primary_cat) : ?>
            <span class="mfl-sl-hero__cat"><?php echo esc_html($primary_cat->name); ?></span>
            <?php endif; ?>
            <?php if ($rating) : ?>
            <span class="mfl-sl-hero__stars" aria-hidden="true"><?php echo mfl_sl_stars($rating); ?></span>
            <span class="mfl-sl-hero__rating-num"><?php echo number_format($rating, 1); ?></span>
            <?php if ($review_cnt) : ?>
            <span class="mfl-sl-hero__review-ct">(<?php echo number_format($review_cnt); ?> reviews)</span>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php if ($address) : ?>
        <p class="mfl-sl-hero__addr">
            <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?php echo esc_html($address); ?>
        </p>
        <?php endif; ?>
    </div>

    <?php if (!empty($gallery_ids)) : ?>
    <!-- Gallery strip at bottom of hero -->
    <div class="mfl-sl-gallery-strip" role="list" aria-label="Photo gallery">
        <?php foreach (array_slice($gallery_ids, 0, 6) as $i => $att_id) :
            $thumb = wp_get_attachment_image_url($att_id, 'thumbnail');
            $full  = wp_get_attachment_image_url($att_id, 'full');
            if (!$thumb) continue;
            $alt   = get_post_meta($att_id, '_wp_attachment_image_alt', true) ?: $title;
        ?>
        <button class="mfl-sl-gallery-thumb"
                data-lightbox-src="<?php echo esc_url($full); ?>"
                data-lightbox-index="<?php echo $i; ?>"
                aria-label="View photo <?php echo $i + 1; ?> of <?php echo count($gallery_ids); ?>"
                role="listitem">
            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
        </button>
        <?php endforeach; ?>
        <?php if (count($gallery_ids) > 6) : ?>
        <div class="mfl-sl-gallery-more" aria-hidden="true">+<?php echo count($gallery_ids) - 6; ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ════════════════════════════════════════════════════════════ LAYOUT ══ -->
<div class="mfl-sl-wrap container">
<div class="mfl-sl-layout">

<!-- ─────────────────────────────────── MAIN COLUMN ─────────────────────── -->
<main class="mfl-sl-main" id="main-content">

    <?php
    // ── Back-to-listings link ───────────────────────────────────────────────
    $back_url = get_post_type_archive_link('listdom-listing');
    ?>
    <div class="mfl-back-link-wrap">
        <a href="<?php echo esc_url($back_url ?: home_url('/listings/')); ?>" class="mfl-back-link">
            &larr; <?php esc_html_e('Back to Listings', 'listdomer-child'); ?>
        </a>
    </div>

    <!-- Breadcrumb -->
    <nav class="mfl-sl-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
        <span aria-hidden="true"> › </span>
        <a href="<?php echo esc_url($listings_url); ?>">Listings</a>
        <?php if ($primary_cat) : ?>
        <span aria-hidden="true"> › </span>
        <a href="<?php echo esc_url($cat_filter_url); ?>"><?php echo esc_html($primary_cat->name); ?></a>
        <?php endif; ?>
        <span aria-hidden="true"> › </span>
        <span aria-current="page"><?php echo esc_html($title); ?></span>
    </nav>

    <!-- ── Business header ──────────────────────────────────────────────── -->
    <div class="mfl-sl-header">

        <?php if ($primary_cat) : ?>
        <span class="mfl-sl-badge"><?php echo esc_html($primary_cat->name); ?></span>
        <?php endif; ?>

        <h1 class="mfl-sl-title"><?php echo esc_html($title); ?></h1>

        <div class="mfl-sl-meta-row">
            <?php if ($rating) : ?>
            <span class="mfl-sl-stars" aria-label="<?php echo esc_attr(number_format($rating, 1)); ?> out of 5 stars">
                <?php echo mfl_sl_stars($rating); ?>
            </span>
            <span class="mfl-sl-rating-num"><?php echo number_format($rating, 1); ?></span>
            <?php if ($review_cnt) : ?>
            <span class="mfl-sl-review-cnt">(<?php echo number_format($review_cnt); ?> reviews)</span>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($address) : ?>
            <span class="mfl-sl-meta-sep" aria-hidden="true">·</span>
            <span class="mfl-sl-address-inline">
                <svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo esc_html($address); ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- CTA buttons -->
        <div class="mfl-sl-ctas" role="group" aria-label="Contact options">
            <?php if ($phone) : ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone)); ?>"
               class="mfl-sl-cta-btn mfl-sl-cta-btn--phone">
                <svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l.91-.9a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <?php echo esc_html($phone); ?>
            </a>
            <?php endif; ?>

            <?php if ($website) : ?>
            <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer"
               class="mfl-sl-cta-btn mfl-sl-cta-btn--website">
                <svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Visit Website
            </a>
            <?php endif; ?>

            <?php if ($email) : ?>
            <a href="mailto:<?php echo esc_attr($email); ?>"
               class="mfl-sl-cta-btn mfl-sl-cta-btn--email">
                <svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Email
            </a>
            <?php endif; ?>
        </div>

        <!-- Social share -->
        <div class="mfl-sl-share" aria-label="Share this listing">
            <span class="mfl-sl-share__label">Share:</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode($permalink); ?>"
               target="_blank" rel="noopener noreferrer"
               class="mfl-sl-share__btn mfl-sl-share__btn--fb"
               aria-label="Share on Facebook">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode($permalink); ?>&text=<?php echo rawurlencode($title . ' — Florida Meditation Directory'); ?>"
               target="_blank" rel="noopener noreferrer"
               class="mfl-sl-share__btn mfl-sl-share__btn--x"
               aria-label="Share on X (Twitter)">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                X
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode($permalink); ?>"
               target="_blank" rel="noopener noreferrer"
               class="mfl-sl-share__btn mfl-sl-share__btn--li"
               aria-label="Share on LinkedIn">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                LinkedIn
            </a>
            <a href="mailto:?subject=<?php echo rawurlencode('Check out: ' . $title); ?>&body=<?php echo rawurlencode($title . "\n" . $permalink); ?>"
               class="mfl-sl-share__btn mfl-sl-share__btn--email"
               aria-label="Share via Email">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Email
            </a>
            <button class="mfl-sl-share__copy" data-copy-url="<?php echo esc_attr($permalink); ?>" aria-label="Copy link">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy Link
            </button>
        </div>
    </div><!-- /.mfl-sl-header -->

    <!-- ── About ─────────────────────────────────────────────────────────── -->
    <?php if ($content) : ?>
    <section class="mfl-sl-section" aria-labelledby="mfl-about-heading">
        <h2 class="mfl-sl-section__title" id="mfl-about-heading">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            About
        </h2>
        <div class="mfl-sl-about-content">
            <?php echo wp_kses_post($content); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Opening Hours ─────────────────────────────────────────────────── -->
    <?php if (!empty($hours)) :
        // Normalize: hours array is Google format starting at Monday (index 0)
        $day_names = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    ?>
    <section class="mfl-sl-section" aria-labelledby="mfl-hours-heading">
        <h2 class="mfl-sl-section__title" id="mfl-hours-heading">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Opening Hours
        </h2>
        <table class="mfl-hours-table" aria-label="Weekly opening hours">
            <caption class="screen-reader-text">Opening hours for <?php echo esc_html($title); ?></caption>
            <tbody>
            <?php foreach ($hours as $index => $line) :
                // Line format: "Monday: 9:00 AM – 5:00 PM" or "Monday: Closed"
                $parts   = explode(': ', $line, 2);
                $day_lbl = isset($parts[0]) ? trim($parts[0]) : '';
                $time_lbl = isset($parts[1]) ? trim($parts[1]) : '';
                $is_closed = stripos($time_lbl, 'Closed') !== false;
                $is_today  = isset($day_map[$day_lbl]) && $day_map[$day_lbl] === $today_google_index;
            ?>
            <tr class="<?php echo $is_today ? 'mfl-today' : ''; ?>">
                <th scope="row" class="mfl-hours-table__day">
                    <?php echo esc_html($day_lbl); ?>
                </th>
                <td class="mfl-hours-table__time <?php echo $is_closed ? 'mfl-hours-table__time--closed' : ''; ?>">
                    <?php
                    // Decode literal uXXXX sequences left by WP's wp_unslash stripping backslashes
                    // e.g. "10:00u202fAM" → "10:00 AM" (U+202F = narrow no-break space → regular space)
                    $time_lbl = preg_replace_callback('/u([0-9a-fA-F]{4})/', function ($m) {
                        $codepoint = hexdec($m[1]);
                        // Collapse unicode whitespace variants to a regular ASCII space for clean display
                        if (in_array($codepoint, [0x202F, 0x2009, 0x00A0], true)) {
                            return ' ';
                        }
                        // Convert en dash / em dash to a simple ASCII hyphen-minus surrounded by spaces
                        if (in_array($codepoint, [0x2013, 0x2014], true)) {
                            return ' - ';
                        }
                        // Fallback: render as actual UTF-8 character
                        return mb_chr($codepoint, 'UTF-8') ?: '';
                    }, $time_lbl);
                    // Normalise any double-spaces created by the substitutions above
                    $time_lbl = preg_replace('/  +/', ' ', trim($time_lbl));
                    echo esc_html($time_lbl);
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php endif; ?>

    <!-- ── Classes & Schedule ───────────────────────────────────────────── -->
    <?php if ($classes_txt) : ?>
    <section class="mfl-sl-section" aria-labelledby="mfl-classes-heading">
        <h2 class="mfl-sl-section__title" id="mfl-classes-heading">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            Classes &amp; Schedule
        </h2>
        <div class="mfl-sl-classes-content">
            <?php echo wp_kses_post(wpautop($classes_txt)); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Map ───────────────────────────────────────────────────────────── -->
    <?php if ($lat && $lng) : ?>
    <section class="mfl-sl-section mfl-sl-section--map" aria-labelledby="mfl-map-heading">
        <h2 class="mfl-sl-section__title" id="mfl-map-heading">
            <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
            Location
        </h2>
        <?php if ($address) : ?>
        <p class="mfl-sl-map-address">
            <svg aria-hidden="true" focusable="false" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?php echo esc_html($address); ?>
        </p>
        <?php endif; ?>
        <div class="mfl-sl-map">
            <iframe
                title="Map showing location of <?php echo esc_attr($title); ?>"
                src="https://maps.google.com/maps?q=<?php echo esc_attr($lat); ?>,<?php echo esc_attr($lng); ?>&z=16&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen>
            </iframe>
        </div>
        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr($lat); ?>,<?php echo esc_attr($lng); ?>"
           target="_blank" rel="noopener noreferrer"
           class="mfl-sl-directions-link">
            Get Directions
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </section>
    <?php endif; ?>

</main><!-- /.mfl-sl-main -->

<!-- ─────────────────────────────────── SIDEBAR ──────────────────────────── -->
<aside class="mfl-sl-sidebar" aria-label="Listing sidebar">

    <!-- ── Contact info card ─────────────────────────────────────────────── -->
    <?php if ($phone || $website || $email || $address) : ?>
    <div class="mfl-sl-sidebar-card">
        <h3 class="mfl-sl-sidebar-card__title">Contact Info</h3>
        <ul class="mfl-sl-contact-list">
            <?php if ($phone) : ?>
            <li>
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l.91-.9a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
            </li>
            <?php endif; ?>
            <?php if ($website) : ?>
            <li>
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo esc_html(preg_replace('#^https?://(www\.)?#', '', rtrim($website, '/'))); ?>
                </a>
            </li>
            <?php endif; ?>
            <?php if ($email) : ?>
            <li>
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            </li>
            <?php endif; ?>
            <?php if ($address) : ?>
            <li>
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span><?php echo esc_html($address); ?></span>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- ── Similar listings ──────────────────────────────────────────────── -->
    <?php if (!empty($similar)) : ?>
    <div class="mfl-sl-sidebar-card">
        <h3 class="mfl-sl-sidebar-card__title">Similar Nearby</h3>
        <ul class="mfl-sl-similar-list">
            <?php foreach ($similar as $s) :
                $s_url   = get_permalink($s->ID);
                $s_img   = function_exists('mfl_listing_image_url')
                    ? mfl_listing_image_url((int) $s->ID, 'thumbnail') : '';
                $s_rating = (float) $s->rating;
            ?>
            <li class="mfl-sl-similar-item">
                <a href="<?php echo esc_url($s_url); ?>" class="mfl-sl-similar-item__link">
                    <?php if ($s_img) : ?>
                    <img src="<?php echo esc_url($s_img); ?>"
                         alt="<?php echo esc_attr($s->post_title); ?>"
                         class="mfl-sl-similar-item__thumb"
                         loading="lazy" width="56" height="56">
                    <?php else : ?>
                    <div class="mfl-sl-similar-item__thumb mfl-sl-similar-item__thumb--placeholder" aria-hidden="true"></div>
                    <?php endif; ?>
                    <div class="mfl-sl-similar-item__info">
                        <span class="mfl-sl-similar-item__name"><?php echo esc_html($s->post_title); ?></span>
                        <?php if ($s_rating) : ?>
                        <span class="mfl-sl-similar-item__stars" aria-label="<?php echo esc_attr(number_format($s_rating, 1)); ?> stars">
                            <?php echo mfl_sl_stars($s_rating); ?>
                            <span class="mfl-sl-similar-item__rating-num"><?php echo number_format($s_rating, 1); ?></span>
                        </span>
                        <?php endif; ?>
                        <?php if ($s->address) : ?>
                        <span class="mfl-sl-similar-item__addr"><?php echo esc_html(wp_trim_words($s->address, 5, '')); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- ── Contact form ──────────────────────────────────────────────────── -->
    <div class="mfl-sl-sidebar-card">
        <h3 class="mfl-sl-sidebar-card__title">Send a Message</h3>

        <?php if ($contact_sent) : ?>
        <div class="mfl-sl-form-notice mfl-sl-form-notice--success" role="alert">
            Your message was sent successfully!
        </div>
        <?php elseif ($contact_error) : ?>
        <div class="mfl-sl-form-notice mfl-sl-form-notice--error" role="alert">
            Something went wrong. Please try again.
        </div>
        <?php endif; ?>

        <form class="mfl-contact-form"
              method="post"
              action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
              novalidate>

            <input type="hidden" name="action"   value="mfl_listing_contact">
            <input type="hidden" name="listing_id" value="<?php echo esc_attr($post_id); ?>">
            <input type="hidden" name="redirect"  value="<?php echo esc_attr($permalink); ?>">
            <?php wp_nonce_field('mfl_listing_contact_' . $post_id, 'mfl_contact_nonce'); ?>

            <div class="mfl-contact-form__field">
                <label for="mfl-contact-name">Your Name <span aria-hidden="true">*</span></label>
                <input type="text" id="mfl-contact-name" name="contact_name"
                       required autocomplete="name"
                       placeholder="Jane Smith">
            </div>

            <div class="mfl-contact-form__field">
                <label for="mfl-contact-email">Email Address <span aria-hidden="true">*</span></label>
                <input type="email" id="mfl-contact-email" name="contact_email"
                       required autocomplete="email"
                       placeholder="jane@example.com">
            </div>

            <div class="mfl-contact-form__field">
                <label for="mfl-contact-phone">Phone (optional)</label>
                <input type="tel" id="mfl-contact-phone" name="contact_phone"
                       autocomplete="tel"
                       placeholder="(555) 000-0000">
            </div>

            <div class="mfl-contact-form__field">
                <label for="mfl-contact-message">Message <span aria-hidden="true">*</span></label>
                <textarea id="mfl-contact-message" name="contact_message"
                          required rows="4"
                          placeholder="I'd like to learn more about your classes…"></textarea>
            </div>

            <!-- Honeypot anti-spam (hidden from humans via CSS) -->
            <div class="mfl-contact-form__field mfl-hp" aria-hidden="true">
                <label for="mfl-contact-url">Website</label>
                <input type="url" id="mfl-contact-url" name="website" tabindex="-1" autocomplete="off">
            </div>

            <button type="submit" class="mfl-contact-form__submit">Send Message</button>
        </form>
    </div>

    <!-- ── Claim this listing ────────────────────────────────────────────── -->
    <div class="mfl-sl-claim">
        <div class="mfl-sl-claim__icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 class="mfl-sl-claim__title">Are you the owner of this location?</h3>
        <p class="mfl-sl-claim__text">Send us a quick message and we'll get you set up to manage this listing.</p>

        <?php if ($claim_sent) : ?>
            <p class="mfl-claim-notice mfl-claim-notice--success">
                Thanks! We received your request and will be in touch shortly.
            </p>
        <?php elseif ($claim_error) : ?>
            <p class="mfl-claim-notice mfl-claim-notice--error">
                Something went wrong &mdash; please check your details and try again.
            </p>
        <?php else : ?>
            <form class="mfl-claim-form"
                  method="post"
                  action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                  novalidate>
                <input type="hidden" name="action"     value="mfl_claim_listing">
                <input type="hidden" name="listing_id" value="<?php echo esc_attr($post_id); ?>">
                <input type="hidden" name="redirect"   value="<?php echo esc_attr($permalink); ?>">
                <?php wp_nonce_field('mfl_claim_listing_' . $post_id, 'mfl_claim_nonce'); ?>

                <div class="mfl-claim-form__field">
                    <label for="mfl-claim-name">Your Name <span aria-hidden="true">*</span></label>
                    <input type="text" id="mfl-claim-name" name="claim_name"
                           required autocomplete="name" placeholder="Jane Smith">
                </div>

                <div class="mfl-claim-form__field">
                    <label for="mfl-claim-email">Email Address <span aria-hidden="true">*</span></label>
                    <input type="email" id="mfl-claim-email" name="claim_email"
                           required autocomplete="email" placeholder="jane@example.com">
                </div>

                <div class="mfl-claim-form__field">
                    <label for="mfl-claim-note">Anything to add? (optional)</label>
                    <textarea id="mfl-claim-note" name="claim_note" rows="3"
                              placeholder="e.g. I'm the owner, my phone is…"></textarea>
                </div>

                <!-- Honeypot -->
                <div class="mfl-contact-form__field mfl-hp" aria-hidden="true">
                    <input type="url" name="website" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" class="mfl-sl-claim__btn">Send Claim Request</button>
            </form>
        <?php endif; ?>
    </div>

</aside><!-- /.mfl-sl-sidebar -->
</div><!-- /.mfl-sl-layout -->
</div><!-- /.mfl-sl-wrap -->

<!-- ════════════════════════════════════════════════ LIGHTBOX (no deps) ══ -->
<div class="mfl-lightbox" id="mfl-lightbox" role="dialog" aria-modal="true" aria-label="Photo lightbox" hidden>
    <button class="mfl-lightbox__close" id="mfl-lb-close" aria-label="Close lightbox">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <button class="mfl-lightbox__prev" id="mfl-lb-prev" aria-label="Previous photo">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <div class="mfl-lightbox__img-wrap">
        <img class="mfl-lightbox__img" id="mfl-lb-img" src="" alt="">
        <div class="mfl-lightbox__counter" id="mfl-lb-counter" aria-live="polite"></div>
    </div>
    <button class="mfl-lightbox__next" id="mfl-lb-next" aria-label="Next photo">
        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
</div>

<?php
// ── JSON-LD Structured Data ───────────────────────────────────────────────────
// Output inline (not via wp_head hook) to keep it co-located with the template.

// Map Listdom/custom category to schema.org type
$schema_type_map = [
    'Yoga Studio'        => 'ExerciseGym',
    'Meditation Center'  => 'LocalBusiness',
    'Retreat Center'     => 'LodgingBusiness',
    'Wellness Center'    => 'HealthClub',
    'Buddhist Center'    => 'PlaceOfWorship',
    'Mindfulness Center' => 'LocalBusiness',
];
$schema_type = 'LocalBusiness';
if ($primary_cat) {
    foreach ($schema_type_map as $keyword => $type) {
        if (stripos($primary_cat->name, explode(' ', $keyword)[0]) !== false) {
            $schema_type = $type;
            break;
        }
    }
}

// Build openingHours array from hours_json
// Schema.org format: "Mo 09:00-17:00" — convert from Google's verbose format
function mfl_hours_to_schema(array $hours): array
{
    $day_abbr = [
        'Monday' => 'Mo', 'Tuesday' => 'Tu', 'Wednesday' => 'We',
        'Thursday' => 'Th', 'Friday' => 'Fr', 'Saturday' => 'Sa', 'Sunday' => 'Su',
    ];
    $out = [];
    foreach ($hours as $line) {
        $parts = explode(': ', $line, 2);
        if (count($parts) < 2) continue;
        [$day, $time] = $parts;
        if (stripos($time, 'Closed') !== false) continue;
        $abbr = $day_abbr[trim($day)] ?? null;
        if (!$abbr) continue;
        // Time format: "9:00 AM – 9:00 PM" or "Open 24 hours"
        if (stripos($time, 'Open 24') !== false) {
            $out[] = $abbr . ' 00:00-23:59';
            continue;
        }
        // Parse "9:00 AM – 5:00 PM"
        if (preg_match('/(\d{1,2}:\d{2}\s*[AP]M)\s*[–-]\s*(\d{1,2}:\d{2}\s*[AP]M)/i', $time, $m)) {
            $open  = date('H:i', strtotime($m[1]));
            $close = date('H:i', strtotime($m[2]));
            $out[] = "$abbr $open-$close";
        }
    }
    return $out;
}

$schema = [
    '@context' => 'https://schema.org',
    '@type'    => $schema_type,
    'name'     => $title,
    'url'      => $permalink,
];

if ($address)  $schema['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $address];
if ($phone)    $schema['telephone'] = $phone;
if ($website)  $schema['sameAs']    = $website;
if ($hero_src) $schema['image']     = $hero_src;
if ($lat && $lng) {
    $schema['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $lat, 'longitude' => $lng];
}
if ($rating && $review_cnt) {
    $schema['aggregateRating'] = [
        '@type'       => 'AggregateRating',
        'ratingValue' => round($rating, 1),
        'reviewCount' => $review_cnt,
        'bestRating'  => 5,
        'worstRating' => 1,
    ];
}
$oh = mfl_hours_to_schema($hours);
if ($oh) $schema['openingHours'] = $oh;

echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . PHP_EOL;
?>

<?php get_footer(); ?>
