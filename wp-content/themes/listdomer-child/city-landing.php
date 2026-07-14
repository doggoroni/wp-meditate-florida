<?php
/**
 * City landing page — /meditation/{slug}/
 *
 * Virtual page routed by MFL_City_Pages (meditate-florida-core plugin).
 * Blocks: hero/breadcrumb → intro → category chips → top rated →
 * city grid → FAQ → nearby cities.
 *
 * Design: docs/superpowers/specs/2026-07-14-city-landing-pages-design.md
 */

defined('ABSPATH') || exit;

$slug = MFL_City_Pages::current_slug();
$city = MFL_City_Pages::get_city($slug);

if (!$city) {
    // Routing guards against this; belt-and-braces.
    wp_safe_redirect(home_url('/listings/'));
    exit;
}

$ids    = MFL_City_Pages::get_city_listing_ids($slug);
$cats   = MFL_City_Pages::get_city_category_counts($slug);
$nearby = MFL_City_Pages::get_nearby_cities($slug, 5);
$count  = count($ids);

$listings_url = get_post_type_archive_link('listdom-listing') ?: home_url('/listings/');

$top_ids  = array_slice($ids, 0, 6);
$grid_ids = array_slice($ids, 6, 18);

// Generated data sentence appended to the hand-written intro.
$top_cat_names = array_slice(array_column($cats, 'name'), 0, 3);
$data_sentence = $count
    ? sprintf(
        __('The directory currently lists %1$d places within %2$d miles of %3$s — %4$s and more.', 'listdomer-child'),
        $count,
        MFL_City_Pages::RADIUS_MILES,
        $city['name'],
        strtolower(implode(', ', $top_cat_names) ?: __('studios and centers', 'listdomer-child'))
    )
    : sprintf(
        __('Listings for %s are on the way — browse the full directory in the meantime.', 'listdomer-child'),
        $city['name']
    );

// FAQ copy — mirrored 1:1 by the FAQPage schema in MFL_City_Pages::head_output().
$faqs = mfl_city_faqs($slug);

/** Star string helper (template-local, same convention as other templates). */
function mfl_cl_stars(float $rating): string
{
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= round($rating) ? '★' : '☆';
    }
    return $out;
}

/** Render one listing card (matches the homepage card markup/classes). */
function mfl_cl_card(int $pid): void
{
    $rating    = (float) get_post_meta($pid, '_mfl_rating',       true);
    $rev_count = (int)   get_post_meta($pid, '_mfl_review_count', true);
    $address   = get_post_meta($pid, 'lsd_address', true);
    $image_url = function_exists('mfl_listing_image_url') ? mfl_listing_image_url($pid, 'medium_large') : '';
    $terms     = get_the_terms($pid, 'listdom-category');
    $cat_name  = (!is_wp_error($terms) && $terms) ? $terms[0]->name : '';
    ?>
    <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="mfl-listing-card">
        <div class="mfl-listing-card__img-wrap">
            <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>"
                     alt="<?php echo esc_attr(get_the_title($pid)); ?>"
                     class="mfl-listing-card__img" loading="lazy" width="400" height="200">
            <?php else: ?>
                <div class="mfl-listing-card__img-placeholder" aria-hidden="true">🧘</div>
            <?php endif; ?>
            <?php if ($cat_name): ?>
                <span class="mfl-listing-card__badge"><?php echo esc_html($cat_name); ?></span>
            <?php endif; ?>
        </div>
        <div class="mfl-listing-card__body">
            <h3 class="mfl-listing-card__title"><?php echo esc_html(get_the_title($pid)); ?></h3>
            <?php if ($rating): ?>
                <div class="mfl-listing-card__stars">
                    <span class="mfl-listing-card__star-icons"
                          aria-label="<?php echo esc_attr(number_format($rating, 1) . ' out of 5 stars'); ?>">
                        <?php echo mfl_cl_stars($rating); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </span>
                    <span class="mfl-listing-card__rating-num"><?php echo esc_html(number_format($rating, 1)); ?></span>
                    <?php if ($rev_count): ?>
                        <span class="mfl-listing-card__review-ct">(<?php echo esc_html(number_format($rev_count)); ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($address): ?>
                <p class="mfl-listing-card__address">
                    <svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?php echo esc_html($address); ?>
                </p>
            <?php endif; ?>
        </div>
    </a>
    <?php
}

get_header();
?>

<!-- ── Hero ─────────────────────────────────────────────────────────────── -->
<header class="mfl-city-hero">
    <div class="container">
        <nav class="mfl-city-hero__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'listdomer-child'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'listdomer-child'); ?></a>
            <span aria-hidden="true">›</span>
            <a href="<?php echo esc_url($listings_url); ?>"><?php esc_html_e('Florida Directory', 'listdomer-child'); ?></a>
            <span aria-hidden="true">›</span>
            <span aria-current="page"><?php echo esc_html($city['name']); ?></span>
        </nav>

        <h1 class="mfl-city-hero__title">
            <?php echo esc_html(sprintf(__('Meditation & Wellness in %s, FL', 'listdomer-child'), $city['name'])); ?>
        </h1>

        <?php if ($count): ?>
        <p class="mfl-city-hero__count">
            <?php echo esc_html(sprintf(
                _n('%d place to practice, rated by the people who go there', '%d places to practice, rated by the people who go there', $count, 'listdomer-child'),
                $count
            )); ?>
        </p>
        <?php endif; ?>
    </div>
</header>

<div class="mfl-city-body">
<div class="container">

    <!-- ── Intro ────────────────────────────────────────────────────────── -->
    <section class="mfl-city-intro">
        <p><?php echo esc_html($city['intro']); ?></p>
        <p class="mfl-city-intro__data"><?php echo esc_html($data_sentence); ?></p>
    </section>

    <?php if ($cats): ?>
    <!-- ── Category chips ───────────────────────────────────────────────── -->
    <section class="mfl-city-cats" aria-label="<?php esc_attr_e('Browse by category', 'listdomer-child'); ?>">
        <?php foreach (array_slice($cats, 0, 8, true) as $term_id => $c): ?>
            <a class="mfl-city-chip"
               href="<?php echo esc_url(add_query_arg(['city' => $city['name'], 'category' => $term_id], $listings_url)); ?>">
                <?php echo esc_html($c['name']); ?>
                <span class="mfl-city-chip__count"><?php echo (int) $c['count']; ?></span>
            </a>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if ($top_ids): ?>
    <!-- ── Top rated ────────────────────────────────────────────────────── -->
    <section class="mfl-city-section">
        <h2 class="mfl-city-section__title">
            <?php echo esc_html(sprintf(__('Top Rated in %s', 'listdomer-child'), $city['name'])); ?>
        </h2>
        <div class="mfl-city-grid">
            <?php foreach ($top_ids as $pid) mfl_cl_card($pid); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($grid_ids): ?>
    <!-- ── More places ──────────────────────────────────────────────────── -->
    <section class="mfl-city-section">
        <h2 class="mfl-city-section__title">
            <?php echo esc_html(sprintf(__('More Places Around %s', 'listdomer-child'), $city['name'])); ?>
        </h2>
        <div class="mfl-city-grid">
            <?php foreach ($grid_ids as $pid) mfl_cl_card($pid); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($count): ?>
    <div class="mfl-city-viewall">
        <a class="mfl-btn mfl-btn--primary"
           href="<?php echo esc_url(add_query_arg(['city' => $city['name'], 'sort' => 'rating'], $listings_url)); ?>">
            <?php echo esc_html(sprintf(
                _n('View all %d in the directory', 'View all %d in the directory', $count, 'listdomer-child'),
                $count
            )); ?>
        </a>
    </div>
    <?php else: ?>
    <div class="mfl-city-viewall">
        <a class="mfl-btn mfl-btn--primary" href="<?php echo esc_url($listings_url); ?>">
            <?php esc_html_e('Browse the full Florida directory', 'listdomer-child'); ?>
        </a>
    </div>
    <?php endif; ?>

    <?php if ($faqs): ?>
    <!-- ── FAQ ──────────────────────────────────────────────────────────── -->
    <section class="mfl-city-section mfl-city-faq">
        <h2 class="mfl-city-section__title">
            <?php echo esc_html(sprintf(__('%s Meditation FAQ', 'listdomer-child'), $city['name'])); ?>
        </h2>
        <?php foreach ($faqs as $faq): ?>
        <details class="mfl-city-faq__item">
            <summary><?php echo esc_html($faq['q']); ?></summary>
            <p><?php echo esc_html($faq['a']); ?></p>
        </details>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if ($nearby): ?>
    <!-- ── Nearby cities ────────────────────────────────────────────────── -->
    <section class="mfl-city-section mfl-city-nearby" aria-label="<?php esc_attr_e('Nearby cities', 'listdomer-child'); ?>">
        <h2 class="mfl-city-section__title"><?php esc_html_e('Explore Nearby', 'listdomer-child'); ?></h2>
        <div class="mfl-city-nearby__row">
            <?php foreach ($nearby as $n): ?>
                <a class="mfl-city-chip mfl-city-chip--nearby" href="<?php echo esc_url(mfl_city_url($n['slug'])); ?>">
                    <?php echo esc_html($n['name']); ?>
                    <span class="mfl-city-chip__count"><?php echo esc_html(sprintf(__('%d mi', 'listdomer-child'), (int) round($n['distance_miles']))); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div>
</div>

<?php get_footer(); ?>
