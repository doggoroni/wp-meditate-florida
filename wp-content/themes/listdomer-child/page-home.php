<?php
/**
 * Template Name: Meditate Florida — Homepage
 *
 * Sections:
 *  1. Hero — search form with Google Places Autocomplete
 *  2. Browse by City — top 10 Florida cities with live listing counts
 *  3. Featured Listings — 6 highest-rated places (ordered by _mfl_rating)
 *  4. Explore by Category — icon grid from listdom-category taxonomy
 *
 * @package listdomer-child
 */

defined('ABSPATH') || exit;

// ─── Configuration ────────────────────────────────────────────────────────────

/** URL of the page containing your [listdom] shortcode (search results). */
$listings_url = get_theme_mod('mfl_listings_page_url', home_url('/listings/'));

/** Hero copy — editable in Appearance → Customize → "Homepage Hero". */
$hero_title    = get_theme_mod('mfl_hero_title',    'Find Meditation & Wellness in Florida');
$hero_subtitle = get_theme_mod('mfl_hero_subtitle', 'Discover studios, retreats, classes, and teachers near you');
$hero_bg_url   = get_theme_mod('mfl_hero_bg_image', '');

/** Top 10 Florida cities with their lat/lng for circle-proximity search. */
$cities = [
    ['name' => 'Miami',             'lat' => 25.7617,  'lng' => -80.1918, 'icon' => '🌴'],
    ['name' => 'Orlando',           'lat' => 28.5383,  'lng' => -81.3792, 'icon' => '☀️'],
    ['name' => 'Tampa',             'lat' => 27.9477,  'lng' => -82.4584, 'icon' => '🌊'],
    ['name' => 'Sarasota',          'lat' => 27.3364,  'lng' => -82.5307, 'icon' => '🧘'],
    ['name' => 'Naples',            'lat' => 26.1420,  'lng' => -81.7948, 'icon' => '🌺'],
    ['name' => 'St. Petersburg',    'lat' => 27.7676,  'lng' => -82.6403, 'icon' => '🌅'],
    ['name' => 'Fort Lauderdale',   'lat' => 26.1224,  'lng' => -80.1373, 'icon' => '🏖️'],
    ['name' => 'Gainesville',       'lat' => 29.6516,  'lng' => -82.3248, 'icon' => '🌿'],
    ['name' => 'Key West',          'lat' => 24.5551,  'lng' => -81.7800, 'icon' => '🐚'],
    ['name' => 'Jacksonville',      'lat' => 30.3322,  'lng' => -81.6557, 'icon' => '🕊️'],
];

/**
 * Returns the number of published listdom-listing posts where lsd_address
 * contains "{city}, FL". Results are cached for 6 hours via transients.
 */
function mfl_city_count(string $city): int
{
    $key   = 'mfl_city_ct_' . md5($city);
    $count = get_transient($key);

    if ($count === false) {
        global $wpdb;
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT p.ID)
             FROM   {$wpdb->posts} p
             JOIN   {$wpdb->postmeta} pm ON pm.post_id = p.ID
             WHERE  p.post_type   = 'listdom-listing'
               AND  p.post_status = 'publish'
               AND  pm.meta_key   = 'lsd_address'
               AND  pm.meta_value LIKE %s",
            '%' . $wpdb->esc_like($city . ', FL') . '%'
        ));
        set_transient($key, $count, 6 * HOUR_IN_SECONDS);
    }

    return (int) $count;
}

/**
 * Build a star string like "★★★★☆" from a numeric rating 0–5.
 */
function mfl_stars(float $rating): string
{
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= round($rating) ? '★' : '☆';
    }
    return $out;
}

/**
 * Map a listdom-category term name to a FontAwesome 5 icon class.
 */
function mfl_category_icon(string $name): string
{
    $name  = strtolower($name);
    $icons = [
        'meditation'  => 'fas fa-spa',
        'yoga'        => 'fas fa-yin-yang',
        'retreat'     => 'fas fa-mountain',
        'mindfulness' => 'fas fa-brain',
        'buddhist'    => 'fas fa-dharmachakra',
        'wellness'    => 'fas fa-heartbeat',
        'spiritual'   => 'fas fa-place-of-worship',
        'fitness'     => 'fas fa-dumbbell',
        'health'      => 'fas fa-notes-medical',
        'gym'         => 'fas fa-dumbbell',
        'spa'         => 'fas fa-spa',
    ];

    foreach ($icons as $keyword => $icon) {
        if (str_contains($name, $keyword)) return $icon;
    }

    return 'fas fa-leaf';
}

// ─── Queries ──────────────────────────────────────────────────────────────────

// Featured listings: 6 highest-rated, sorted by our Google Places rating meta.
$featured_query = new WP_Query([
    'post_type'      => 'listdom-listing',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'meta_key'       => '_mfl_rating',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
    'meta_query'     => [['key' => '_mfl_rating', 'compare' => 'EXISTS']],
    'no_found_rows'  => true,
]);

// Categories: top 8 by listing count.
$categories = get_terms([
    'taxonomy'   => 'listdom-category',
    'hide_empty' => true,
    'number'     => 8,
    'orderby'    => 'count',
    'order'      => 'DESC',
]);

// Category dropdown for the search form.
$search_categories = get_terms([
    'taxonomy'   => 'listdom-category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

get_header();
?>

<!-- ════════════════════════════════════════════════════════════════════════
     1. HERO
     ════════════════════════════════════════════════════════════════════════ -->
<section class="mfl-hero" aria-label="<?php esc_attr_e('Homepage hero — search for meditation and wellness', 'listdomer-child'); ?>">

    <?php if ($hero_bg_url): ?>
        <div class="mfl-hero__bg"
             style="background-image: url('<?php echo esc_url($hero_bg_url); ?>')"
             role="img"
             aria-label="<?php esc_attr_e('Florida nature background', 'listdomer-child'); ?>">
        </div>
    <?php endif; ?>

    <div class="mfl-hero__overlay" aria-hidden="true"></div>

    <div class="mfl-hero__content">

        <span class="mfl-hero__eyebrow"><?php esc_html_e('Florida\'s Mindfulness Directory', 'listdomer-child'); ?></span>

        <h1 class="mfl-hero__title"><?php echo esc_html($hero_title); ?></h1>

        <p class="mfl-hero__subtitle"><?php echo esc_html($hero_subtitle); ?></p>

        <!-- Search form — fields map directly to Listdom's sf[] GET parameters -->
        <form id="mfl-hero-search-form"
              class="mfl-search-form"
              method="get"
              action="<?php echo esc_url($listings_url); ?>"
              role="search"
              aria-label="<?php esc_attr_e('Search listings', 'listdomer-child'); ?>">

            <!-- Keyword: maps to sf[s] (Listdom textsearch) -->
            <div class="mfl-search-form__field">
                <label class="mfl-search-form__label" for="mfl-hero-keyword">
                    <?php esc_html_e('What', 'listdomer-child'); ?>
                </label>
                <input
                    type="search"
                    id="mfl-hero-keyword"
                    name="sf[s]"
                    class="mfl-search-form__input"
                    placeholder="<?php esc_attr_e('What are you looking for?', 'listdomer-child'); ?>"
                    value="<?php echo esc_attr(sanitize_text_field($_GET['sf']['s'] ?? '')); ?>"
                    autocomplete="off"
                >
            </div>

            <!-- City / zip — Places Autocomplete. On selection, the hidden
                 sf[shape], sf[circle_latitude] etc. are populated so Listdom
                 runs a geo-radius search. Without a selection, sf[address]
                 falls back to Listdom's text-address search. -->
            <div class="mfl-search-form__field">
                <label class="mfl-search-form__label" for="mfl-hero-city">
                    <?php esc_html_e('Where', 'listdomer-child'); ?>
                </label>
                <input
                    type="text"
                    id="mfl-hero-city"
                    name="sf[address]"
                    class="mfl-search-form__input"
                    placeholder="<?php esc_attr_e('City or zip code', 'listdomer-child'); ?>"
                    value="<?php echo esc_attr(sanitize_text_field($_GET['sf']['address'] ?? '')); ?>"
                    autocomplete="off"
                >
                <!-- Geo fields populated by Places Autocomplete JS -->
                <input type="hidden" id="mfl-hero-shape"  name="sf[shape]">
                <input type="hidden" id="mfl-hero-lat"    name="sf[circle_latitude]">
                <input type="hidden" id="mfl-hero-lng"    name="sf[circle_longitude]">
                <input type="hidden" id="mfl-hero-radius" name="sf[circle_radius]">
            </div>

            <!-- Category: maps to sf[listdom-category] (Listdom taxonomy filter) -->
            <div class="mfl-search-form__field">
                <label class="mfl-search-form__label" for="mfl-hero-cat">
                    <?php esc_html_e('Category', 'listdomer-child'); ?>
                </label>
                <select
                    id="mfl-hero-cat"
                    name="sf[listdom-category]"
                    class="mfl-search-form__select"
                    aria-label="<?php esc_attr_e('Filter by category', 'listdomer-child'); ?>"
                >
                    <option value=""><?php esc_html_e('All categories', 'listdomer-child'); ?></option>
                    <?php if (!is_wp_error($search_categories)): ?>
                        <?php foreach ($search_categories as $cat): ?>
                            <option
                                value="<?php echo esc_attr($cat->term_id); ?>"
                                <?php selected(($cat->term_id == ($_GET['sf']['listdom-category'] ?? 0))); ?>
                            >
                                <?php echo esc_html($cat->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback static options if no categories exist yet -->
                        <option value=""><?php esc_html_e('Meditation', 'listdomer-child'); ?></option>
                        <option value=""><?php esc_html_e('Yoga', 'listdomer-child'); ?></option>
                        <option value=""><?php esc_html_e('Retreat', 'listdomer-child'); ?></option>
                        <option value=""><?php esc_html_e('Wellness', 'listdomer-child'); ?></option>
                        <option value=""><?php esc_html_e('Buddhist Center', 'listdomer-child'); ?></option>
                        <option value=""><?php esc_html_e('Mindfulness', 'listdomer-child'); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" class="mfl-search-form__btn">
                <i class="fas fa-search" aria-hidden="true"></i>
                <?php esc_html_e('Search', 'listdomer-child'); ?>
            </button>

        </form>

        <!-- Trust strip -->
        <div class="mfl-hero__trust" aria-hidden="true">
            <span class="mfl-hero__trust-item">
                <i class="fas fa-map-marker-alt"></i>
                <?php esc_html_e('20 Florida cities', 'listdomer-child'); ?>
            </span>
            <span class="mfl-hero__trust-item">
                <i class="fas fa-leaf"></i>
                <?php
                    $total = wp_count_posts('listdom-listing');
                    $n     = $total->publish ?? 0;
                    /* translators: %d = number of listings */
                    echo esc_html(sprintf(_n('%d listing', '%d listings', $n, 'listdomer-child'), $n));
                ?>
            </span>
            <span class="mfl-hero__trust-item">
                <i class="fas fa-star"></i>
                <?php esc_html_e('Google-verified locations', 'listdomer-child'); ?>
            </span>
        </div>

    </div>
</section>


<!-- ════════════════════════════════════════════════════════════════════════
     2. BROWSE BY CITY
     ════════════════════════════════════════════════════════════════════════ -->
<section class="mfl-section mfl-section--alt">
    <div class="container">

        <div class="mfl-section__header">
            <span class="mfl-section__eyebrow"><?php esc_html_e('Explore Florida', 'listdomer-child'); ?></span>
            <h2 class="mfl-section__title"><?php esc_html_e('Browse by City', 'listdomer-child'); ?></h2>
            <p class="mfl-section__desc"><?php esc_html_e('Find meditation and wellness centres in every corner of the Sunshine State.', 'listdomer-child'); ?></p>
        </div>

        <div class="mfl-city-grid">
            <?php foreach ($cities as $city):
                $count     = mfl_city_count($city['name']);
                $lat       = $city['lat'];
                $lng       = $city['lng'];
                $city_url  = add_query_arg([
                    'sf' => [
                        'shape'             => 'circle',
                        'circle_latitude'   => $lat,
                        'circle_longitude'  => $lng,
                        'circle_radius'     => 30,   // km
                        'address'           => $city['name'] . ', FL',
                    ],
                ], $listings_url);
            ?>
                <a href="<?php echo esc_url($city_url); ?>"
                   class="mfl-city-card"
                   aria-label="<?php echo esc_attr(sprintf(
                       /* translators: 1: city name 2: listing count */
                       _n('Browse %1$s — %2$d listing', 'Browse %1$s — %2$d listings', $count, 'listdomer-child'),
                       $city['name'], $count
                   )); ?>">
                    <div class="mfl-city-card__icon" aria-hidden="true">
                        <?php echo $city['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </div>
                    <div class="mfl-city-card__name"><?php echo esc_html($city['name']); ?></div>
                    <div class="mfl-city-card__count">
                        <?php echo esc_html(
                            $count > 0
                                ? sprintf(_n('%d listing', '%d listings', $count, 'listdomer-child'), $count)
                                : __('Coming soon', 'listdomer-child')
                        ); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>


<!-- ════════════════════════════════════════════════════════════════════════
     3. FEATURED LISTINGS
     ════════════════════════════════════════════════════════════════════════ -->
<section class="mfl-section">
    <div class="container">

        <div class="mfl-section__header">
            <span class="mfl-section__eyebrow"><?php esc_html_e('Highly Rated', 'listdomer-child'); ?></span>
            <h2 class="mfl-section__title"><?php esc_html_e('Featured Listings', 'listdomer-child'); ?></h2>
            <p class="mfl-section__desc"><?php esc_html_e('Top-rated meditation centres, retreats, and wellness studios across Florida.', 'listdomer-child'); ?></p>
        </div>

        <?php if ($featured_query->have_posts()): ?>
            <div class="mfl-listings-grid">
                <?php while ($featured_query->have_posts()): $featured_query->the_post();
                    $pid        = get_the_ID();
                    $rating     = (float) get_post_meta($pid, '_mfl_rating',       true);
                    $rev_count  = (int)   get_post_meta($pid, '_mfl_review_count', true);
                    $address    = get_post_meta($pid, 'lsd_address',               true);
                    $image_url  = get_post_meta($pid, '_mfl_image_url',            true);
                    $categories = get_the_terms($pid, 'listdom-category');
                    $cat_name   = (!is_wp_error($categories) && $categories) ? $categories[0]->name : '';
                ?>
                    <a href="<?php the_permalink(); ?>" class="mfl-listing-card">

                        <div class="mfl-listing-card__img-wrap">
                            <?php if ($image_url): ?>
                                <img
                                    src="<?php echo esc_url($image_url); ?>"
                                    alt="<?php the_title_attribute(); ?>"
                                    class="mfl-listing-card__img"
                                    loading="lazy"
                                    width="400"
                                    height="200"
                                >
                            <?php elseif (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('medium_large', ['class' => 'mfl-listing-card__img', 'loading' => 'lazy']); ?>
                            <?php else: ?>
                                <div class="mfl-listing-card__img-placeholder" aria-hidden="true">🧘</div>
                            <?php endif; ?>

                            <?php if ($cat_name): ?>
                                <span class="mfl-listing-card__badge"><?php echo esc_html($cat_name); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="mfl-listing-card__body">
                            <h3 class="mfl-listing-card__title"><?php the_title(); ?></h3>

                            <?php if ($rating): ?>
                                <div class="mfl-listing-card__stars">
                                    <span class="mfl-listing-card__star-icons"
                                          aria-label="<?php echo esc_attr(number_format($rating, 1) . ' out of 5 stars'); ?>">
                                        <?php echo mfl_stars($rating); ?>
                                    </span>
                                    <span class="mfl-listing-card__rating-num"><?php echo esc_html(number_format($rating, 1)); ?></span>
                                    <?php if ($rev_count): ?>
                                        <span class="mfl-listing-card__review-ct">
                                            <?php echo esc_html(sprintf(
                                                /* translators: %d = number of reviews */
                                                _n('(%d review)', '(%d reviews)', $rev_count, 'listdomer-child'),
                                                $rev_count
                                            )); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($address): ?>
                                <div class="mfl-listing-card__address">
                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                    <span><?php echo esc_html($address); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <div class="mfl-section__cta">
                <a href="<?php echo esc_url($listings_url); ?>" class="mfl-btn mfl-btn--primary">
                    <?php esc_html_e('View All Listings', 'listdomer-child'); ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

        <?php else: ?>
            <div class="mfl-empty-state">
                <i class="fas fa-seedling" aria-hidden="true"></i>
                <p><?php esc_html_e('Listings are being imported. Check back soon!', 'listdomer-child'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</section>


<!-- ════════════════════════════════════════════════════════════════════════
     4. EXPLORE BY CATEGORY
     ════════════════════════════════════════════════════════════════════════ -->
<section class="mfl-section mfl-section--alt">
    <div class="container">

        <div class="mfl-section__header">
            <span class="mfl-section__eyebrow"><?php esc_html_e('What are you looking for?', 'listdomer-child'); ?></span>
            <h2 class="mfl-section__title"><?php esc_html_e('Explore by Category', 'listdomer-child'); ?></h2>
        </div>

        <?php if (!is_wp_error($categories) && $categories): ?>
            <div class="mfl-cat-grid">
                <?php foreach ($categories as $cat):
                    $icon     = mfl_category_icon($cat->name);
                    $cat_link = get_term_link($cat);
                    if (is_wp_error($cat_link)) $cat_link = $listings_url;
                ?>
                    <a href="<?php echo esc_url($cat_link); ?>"
                       class="mfl-cat-card"
                       aria-label="<?php echo esc_attr(sprintf(
                           /* translators: 1: category name 2: count */
                           _n('%1$s — %2$d listing', '%1$s — %2$d listings', $cat->count, 'listdomer-child'),
                           $cat->name, $cat->count
                       )); ?>">
                        <div class="mfl-cat-card__icon-wrap" aria-hidden="true">
                            <i class="<?php echo esc_attr($icon); ?>"></i>
                        </div>
                        <div class="mfl-cat-card__name"><?php echo esc_html($cat->name); ?></div>
                        <div class="mfl-cat-card__count">
                            <?php echo esc_html(sprintf(
                                _n('%d listing', '%d listings', $cat->count, 'listdomer-child'),
                                $cat->count
                            )); ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Placeholder grid until categories exist -->
            <?php
            $placeholder_cats = [
                ['name' => 'Meditation',     'icon' => 'fas fa-spa'],
                ['name' => 'Yoga',           'icon' => 'fas fa-yin-yang'],
                ['name' => 'Retreats',       'icon' => 'fas fa-mountain'],
                ['name' => 'Wellness',       'icon' => 'fas fa-heartbeat'],
                ['name' => 'Buddhist',       'icon' => 'fas fa-dharmachakra'],
                ['name' => 'Mindfulness',    'icon' => 'fas fa-brain'],
                ['name' => 'Spiritual',      'icon' => 'fas fa-place-of-worship'],
                ['name' => 'Health',         'icon' => 'fas fa-leaf'],
            ];
            ?>
            <div class="mfl-cat-grid">
                <?php foreach ($placeholder_cats as $pc): ?>
                    <a href="<?php echo esc_url($listings_url); ?>" class="mfl-cat-card">
                        <div class="mfl-cat-card__icon-wrap" aria-hidden="true">
                            <i class="<?php echo esc_attr($pc['icon']); ?>"></i>
                        </div>
                        <div class="mfl-cat-card__name"><?php echo esc_html($pc['name']); ?></div>
                        <div class="mfl-cat-card__count"><?php esc_html_e('Coming soon', 'listdomer-child'); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
