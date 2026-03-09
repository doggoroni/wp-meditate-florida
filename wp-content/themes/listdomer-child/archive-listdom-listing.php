<?php
/**
 * Listdomer Child — Listing Archive
 * Replaces Listdom's default shortcode-based archive with a fully custom
 * filterable page: sidebar filters, grid/list toggle, sort bar, split-map view.
 *
 * URL params (flat, shareable, crawlable):
 *   city, category, type, rating, open_now, sort, view, paged
 *
 * AJAX action: mfl_listings_search (defined in meditate-florida-core plugin)
 */

defined('ABSPATH') || exit;

get_header();

// ── 1. Sanitize incoming filters ──────────────────────────────────────────────

$handler = new MFL_Search_Handler();
$filters = $handler->sanitize_filters($_GET);

// ── 2. Run initial (SSR) search ───────────────────────────────────────────────

$result   = $handler->run_search($filters);
$cards_html = $result['html'];
$total    = $result['total'];
$pages    = $result['pages'];
$paged    = $result['paged'];
$map_pins = $result['map_pins'];

// ── 3. Sidebar data ───────────────────────────────────────────────────────────

$categories = get_terms([
    'taxonomy'   => 'listdom-category',
    'hide_empty' => true,
    'orderby'    => 'count',
    'order'      => 'DESC',
]);
$categories = is_wp_error($categories) ? [] : $categories;

$cities = MFL_Search_Handler::CITIES;
sort($cities);

$service_types = MFL_Search_Handler::SERVICE_TYPES;

// Current active filter values (for UI state)
$f_city     = $filters['city'];
$f_category = $filters['category'];
$f_type     = $filters['type'];
$f_rating   = $filters['rating'];
$f_open_now = $filters['open_now'];
$f_sort     = $filters['sort'];
$f_view     = $filters['view'];

// Active filter count (for mobile badge)
$active_count = (int) (!!$f_city) + (int) (!!$f_category) + (int) (!!$f_type)
              + (int) ($f_rating > 0) + (int) $f_open_now;

// Base URL for this archive (without filter params)
$archive_url = get_post_type_archive_link('listdom-listing') ?: home_url('/listdom-listing/');

?>

<!-- ═══════════════════════════════════════════════════ ARCHIVE HEADER ══ -->
<header class="mfl-arch-header">
    <div class="container">
        <h1 class="mfl-arch-header__title">Florida Meditation Directory</h1>
        <p class="mfl-arch-header__sub">
            <?php if ($total) : ?>
            <strong><?php echo number_format($total); ?></strong> location<?php echo $total !== 1 ? 's' : ''; ?> found
            <?php if ($f_city) : ?>in <strong><?php echo esc_html($f_city); ?></strong><?php endif; ?>
            <?php else : ?>
            No locations match your current filters
            <?php endif; ?>
        </p>
    </div>
</header>

<!-- ════════════════════════════════════════════════ MAIN LAYOUT ══ -->
<div class="mfl-arch-wrap container">
<div class="mfl-arch-layout" id="mfl-arch-layout">

<!-- ─────────────────────────── FILTER SIDEBAR ──────────────────────────── -->
<aside class="mfl-arch-sidebar" id="mfl-arch-sidebar" aria-label="Listing filters">

    <div class="mfl-arch-sidebar__inner">
        <div class="mfl-arch-sidebar__head">
            <h2 class="mfl-arch-sidebar__heading">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filters
                <?php if ($active_count) : ?>
                <span class="mfl-arch-sidebar__badge" aria-label="<?php echo $active_count; ?> active filters"><?php echo $active_count; ?></span>
                <?php endif; ?>
            </h2>
            <?php if ($active_count) : ?>
            <a href="<?php echo esc_url($archive_url); ?>" class="mfl-arch-sidebar__clear">Clear all</a>
            <?php endif; ?>
        </div>

        <form class="mfl-filter-form" id="mfl-filter-form" method="get" action="<?php echo esc_url($archive_url); ?>">

            <!-- City dropdown -->
            <div class="mfl-filter-group">
                <label class="mfl-filter-label" for="mfl-city">City</label>
                <select name="city" id="mfl-city" class="mfl-filter-select">
                    <option value="">All Cities</option>
                    <?php foreach ($cities as $city) : ?>
                    <option value="<?php echo esc_attr($city); ?>"<?php selected($f_city, $city); ?>><?php echo esc_html($city); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Category checkboxes -->
            <?php if (!empty($categories)) : ?>
            <div class="mfl-filter-group">
                <fieldset>
                    <legend class="mfl-filter-label">Category</legend>
                    <ul class="mfl-filter-checklist">
                        <?php foreach ($categories as $cat) : ?>
                        <li>
                            <label class="mfl-filter-check">
                                <input type="radio" name="category" value="<?php echo esc_attr($cat->term_id); ?>"
                                    <?php checked((int) $f_category, (int) $cat->term_id); ?>>
                                <span class="mfl-filter-check__mark"></span>
                                <?php echo esc_html($cat->name); ?>
                                <span class="mfl-filter-check__count"><?php echo (int) $cat->count; ?></span>
                            </label>
                        </li>
                        <?php endforeach; ?>
                        <?php if ($f_category) : ?>
                        <li>
                            <label class="mfl-filter-check mfl-filter-check--clear">
                                <input type="radio" name="category" value="" <?php checked($f_category, ''); ?>>
                                <span class="mfl-filter-check__mark"></span>
                                All Categories
                            </label>
                        </li>
                        <?php endif; ?>
                    </ul>
                </fieldset>
            </div>
            <?php endif; ?>

            <!-- Service type -->
            <div class="mfl-filter-group">
                <fieldset>
                    <legend class="mfl-filter-label">Service Type</legend>
                    <ul class="mfl-filter-checklist">
                        <?php foreach ($service_types as $slug => $label) : ?>
                        <li>
                            <label class="mfl-filter-check">
                                <input type="radio" name="type" value="<?php echo esc_attr($slug); ?>"
                                    <?php checked($f_type, $slug); ?>>
                                <span class="mfl-filter-check__mark"></span>
                                <?php echo esc_html($label); ?>
                            </label>
                        </li>
                        <?php endforeach; ?>
                        <?php if ($f_type) : ?>
                        <li>
                            <label class="mfl-filter-check mfl-filter-check--clear">
                                <input type="radio" name="type" value="" <?php checked($f_type, ''); ?>>
                                <span class="mfl-filter-check__mark"></span>
                                All Types
                            </label>
                        </li>
                        <?php endif; ?>
                    </ul>
                </fieldset>
            </div>

            <!-- Minimum rating -->
            <div class="mfl-filter-group">
                <fieldset>
                    <legend class="mfl-filter-label">Minimum Rating</legend>
                    <div class="mfl-star-picker" role="group" aria-label="Minimum star rating">
                        <?php for ($s = 1; $s <= 5; $s++) : ?>
                        <label class="mfl-star-picker__star <?php echo ($f_rating >= $s) ? 'is-active' : ''; ?>"
                               title="<?php echo $s; ?> star<?php echo $s > 1 ? 's' : ''; ?> minimum">
                            <input type="radio" name="rating" value="<?php echo $s; ?>"
                                   class="sr-only"
                                   <?php checked((int) $f_rating, $s); ?>>
                            ★
                        </label>
                        <?php endfor; ?>
                        <?php if ($f_rating) : ?>
                        <button type="button" class="mfl-star-picker__clear" data-clear-rating aria-label="Clear rating filter">×</button>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="rating" id="mfl-rating-input" value="<?php echo esc_attr($f_rating ?: ''); ?>">
                </fieldset>
            </div>

            <!-- Open Now toggle -->
            <div class="mfl-filter-group">
                <label class="mfl-toggle-wrap">
                    <input type="checkbox" name="open_now" value="1" id="mfl-open-now"
                           class="mfl-toggle__input sr-only"
                           <?php checked($f_open_now, 1); ?>>
                    <span class="mfl-toggle" aria-hidden="true"></span>
                    <span class="mfl-toggle-label">Open Now</span>
                </label>
            </div>

            <button type="submit" class="mfl-filter-form__submit">Apply Filters</button>

        </form><!-- /.mfl-filter-form -->
    </div>

</aside><!-- /.mfl-arch-sidebar -->

<!-- ─────────────────────────── RESULTS COLUMN ──────────────────────────── -->
<div class="mfl-arch-results" id="mfl-arch-results">

    <!-- Sort/view toolbar -->
    <div class="mfl-arch-toolbar" role="toolbar" aria-label="Results display options">

        <!-- Sort -->
        <div class="mfl-arch-toolbar__sort">
            <label for="mfl-sort" class="sr-only">Sort by</label>
            <select id="mfl-sort" name="sort" class="mfl-sort-select" aria-label="Sort listings">
                <option value="newest"    <?php selected($f_sort, 'newest'); ?>>Newest Added</option>
                <option value="rating"    <?php selected($f_sort, 'rating'); ?>>Highest Rated</option>
                <option value="distance"  <?php selected($f_sort, 'distance'); ?>>Distance (nearest)</option>
                <option value="relevance" <?php selected($f_sort, 'relevance'); ?>>Relevance</option>
            </select>
        </div>

        <!-- View toggles -->
        <div class="mfl-arch-toolbar__views" role="group" aria-label="View mode">
            <button class="mfl-view-btn <?php echo $f_view === 'grid' ? 'is-active' : ''; ?>"
                    data-view="grid" aria-pressed="<?php echo $f_view === 'grid' ? 'true' : 'false'; ?>"
                    title="Grid view">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span class="sr-only">Grid view</span>
            </button>
            <button class="mfl-view-btn <?php echo $f_view === 'list' ? 'is-active' : ''; ?>"
                    data-view="list" aria-pressed="<?php echo $f_view === 'list' ? 'true' : 'false'; ?>"
                    title="List view">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                <span class="sr-only">List view</span>
            </button>
            <button class="mfl-view-btn <?php echo $f_view === 'map' ? 'is-active' : ''; ?>"
                    data-view="map" aria-pressed="<?php echo $f_view === 'map' ? 'true' : 'false'; ?>"
                    title="Map view">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
                <span class="sr-only">Map view</span>
            </button>
        </div>

        <!-- Mobile filter toggle -->
        <button class="mfl-mobile-filter-btn" id="mfl-mobile-filter-btn" aria-expanded="false" aria-controls="mfl-arch-sidebar">
            <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Filters
            <?php if ($active_count) : ?>
            <span class="mfl-mobile-filter-btn__badge"><?php echo $active_count; ?></span>
            <?php endif; ?>
        </button>

    </div><!-- /.mfl-arch-toolbar -->

    <!-- Split map pane (shown when view=map) -->
    <div class="mfl-arch-map" id="mfl-arch-map" aria-label="Listings map" hidden>
        <div id="mfl-leaflet-map"></div>
    </div>

    <!-- Loading overlay -->
    <div class="mfl-arch-loading" id="mfl-arch-loading" aria-live="polite" aria-label="Loading results" hidden>
        <span class="mfl-arch-loading__spinner" aria-hidden="true"></span>
        <span>Finding locations…</span>
    </div>

    <!-- Cards container -->
    <div class="mfl-cards-wrap mfl-cards-wrap--<?php echo esc_attr($f_view); ?>" id="mfl-cards-wrap" aria-live="polite" aria-label="Listing results">
        <?php echo $cards_html; // pre-rendered, safe HTML from render_cards() ?>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1) :

        /**
         * Build a windowed set of page numbers with ellipsis gaps.
         * Always shows: first, ellipsis, up to 2 neighbours each side of $current, ellipsis, last.
         * Negative values in the returned array represent ellipsis sentinels.
         */
        function mfl_pagination_window(int $current, int $total): array {
            $show = [1, $total];
            for ($i = max(1, $current - 2); $i <= min($total, $current + 2); $i++) {
                $show[] = $i;
            }
            $show = array_unique($show);
            sort($show);

            $result = [];
            $prev   = null;
            foreach ($show as $p) {
                if ($prev !== null && $p - $prev > 1) {
                    $result[] = -1; // ellipsis sentinel
                }
                $result[] = $p;
                $prev = $p;
            }
            return $result;
        }

        $window   = mfl_pagination_window($paged, $pages);
        $prev_url = $paged > 1
            ? add_query_arg(array_merge(array_filter($_GET), ['paged' => $paged - 1]), $archive_url)
            : null;
        $next_url = $paged < $pages
            ? add_query_arg(array_merge(array_filter($_GET), ['paged' => $paged + 1]), $archive_url)
            : null;
    ?>
    <nav class="mfl-arch-pagination" id="mfl-arch-pagination" aria-label="Listings pagination">

        <?php if ($prev_url) : ?>
        <a href="<?php echo esc_url($prev_url); ?>" class="mfl-page-btn mfl-page-btn--prev" data-page="<?php echo $paged - 1; ?>" aria-label="Previous page">&lsaquo; Prev</a>
        <?php else : ?>
        <span class="mfl-page-btn mfl-page-btn--disabled" aria-disabled="true">&lsaquo; Prev</span>
        <?php endif; ?>

        <?php foreach ($window as $pg) :
            if ($pg === -1) : ?>
            <span class="mfl-page-ellipsis" aria-hidden="true">&hellip;</span>
            <?php elseif ($pg === $paged) : ?>
            <span class="mfl-page-btn mfl-page-btn--current" aria-current="page"><?php echo $pg; ?></span>
            <?php else :
                $page_url = add_query_arg(array_merge(array_filter($_GET), ['paged' => $pg]), $archive_url);
            ?>
            <a href="<?php echo esc_url($page_url); ?>" class="mfl-page-btn" data-page="<?php echo $pg; ?>"><?php echo $pg; ?></a>
            <?php endif;
        endforeach; ?>

        <?php if ($next_url) : ?>
        <a href="<?php echo esc_url($next_url); ?>" class="mfl-page-btn mfl-page-btn--next" data-page="<?php echo $paged + 1; ?>" aria-label="Next page">Next &rsaquo;</a>
        <?php else : ?>
        <span class="mfl-page-btn mfl-page-btn--disabled" aria-disabled="true">Next &rsaquo;</span>
        <?php endif; ?>

    </nav>
    <?php endif; ?>

</div><!-- /.mfl-arch-results -->
</div><!-- /.mfl-arch-layout -->
</div><!-- /.mfl-arch-wrap -->

<?php
// ── Inline data for JS ────────────────────────────────────────────────────────
$js_config = [
    'ajaxUrl'     => admin_url('admin-ajax.php'),
    'nonce'       => wp_create_nonce('mfl_search_nonce'),
    'archiveUrl'  => $archive_url,
    'filters'     => $filters,
    'mapPins'     => $map_pins,
    'totalPages'  => $pages,
    'currentPage' => $paged,
    'leafletCss'  => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'leafletJs'   => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
];
echo '<script>window.mflArchive = ' . wp_json_encode($js_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';</script>' . PHP_EOL;
?>

<?php get_footer(); ?>
