<?php
/**
 * MFL_Search_Handler
 *
 * Handles the AJAX-powered listing search/filter endpoint.
 * Action: mfl_listings_search (auth + nopriv)
 *
 * URL params consumed (flat, SEO-friendly):
 *   city       — city name string e.g. "Miami"
 *   category   — listdom-category term_id (int)
 *   type       — listdom-tag slug: classes|retreats|drop-in|teacher-training
 *   rating     — minimum _mfl_rating (float 1–5)
 *   open_now   — 1 to filter to currently-open listings
 *   sort       — newest|rating|distance|relevance
 *   view       — grid|list  (CSS class only; does not affect query)
 *   user_lat   — float, user geolocation (for distance sort)
 *   user_lng   — float, user geolocation (for distance sort)
 *   paged      — int page number
 */

defined('ABSPATH') || exit;

class MFL_Search_Handler
{
    const PER_PAGE = 12;
    const MAX_PINS = 300; // max map pins returned

    /** Florida cities matching the importer list */
    const CITIES = [
        'Miami', 'Orlando', 'Tampa', 'Jacksonville', 'Fort Lauderdale',
        'St. Petersburg', 'Tallahassee', 'Gainesville', 'Sarasota', 'Naples',
        'Boca Raton', 'West Palm Beach', 'Fort Myers', 'Clearwater', 'Pensacola',
        'Daytona Beach', 'Bradenton', 'Ocala', 'Palm Beach Gardens', 'Delray Beach',
    ];

    /** Service types as tag slugs => labels */
    const SERVICE_TYPES = [
        'classes'          => 'Classes',
        'retreats'         => 'Retreats',
        'drop-in'          => 'Drop-in',
        'teacher-training' => 'Teacher Training',
    ];

    public function register(): void
    {
        add_action('wp_ajax_mfl_listings_search',        [$this, 'handle_ajax']);
        add_action('wp_ajax_nopriv_mfl_listings_search', [$this, 'handle_ajax']);
    }

    // ── AJAX entry point ─────────────────────────────────────────────────────

    public function handle_ajax(): void
    {
        check_ajax_referer('mfl_search_nonce', 'nonce');

        $filters = $this->sanitize_filters($_POST);
        $result  = $this->run_search($filters);

        wp_send_json_success($result);
    }

    // ── Main search logic (shared by AJAX + SSR) ─────────────────────────────

    public function run_search(array $filters): array
    {
        $needs_php_filter = !empty($filters['open_now']) || $filters['sort'] === 'distance';

        $args = $this->build_query_args($filters, $needs_php_filter);
        $query = new WP_Query($args);
        $posts = $query->posts;

        // --- PHP-side filtering ---

        if (!empty($filters['open_now'])) {
            $posts = array_filter($posts, fn($p) => $this->is_open_now((int) $p->ID));
            $posts = array_values($posts);
        }

        // --- PHP-side distance sort ---

        if ($filters['sort'] === 'distance' && !empty($filters['user_lat']) && !empty($filters['user_lng'])) {
            $posts = $this->sort_by_distance($posts, (float) $filters['user_lat'], (float) $filters['user_lng']);
        }

        // --- Manual pagination (needed when PHP-filtering or distance-sorting) ---

        $total = $needs_php_filter ? count($posts) : $query->found_posts;
        $paged = max(1, (int) ($filters['paged'] ?? 1));

        if ($needs_php_filter) {
            $offset = ($paged - 1) * self::PER_PAGE;
            $posts  = array_slice($posts, $offset, self::PER_PAGE);
        }

        $pages = (int) ceil($total / self::PER_PAGE);

        // --- Map pins (all matching, capped) ---

        $map_pins = $this->get_map_pins($needs_php_filter ? $posts : $query->posts, $filters);

        // --- Render HTML ---

        $view = in_array($filters['view'] ?? 'grid', ['grid', 'list'], true) ? $filters['view'] : 'grid';
        $html = $this->render_cards($posts, $view);

        return [
            'html'     => $html,
            'total'    => $total,
            'pages'    => $pages,
            'paged'    => $paged,
            'map_pins' => $map_pins,
        ];
    }

    // ── Query argument builder ────────────────────────────────────────────────

    private function build_query_args(array $filters, bool $get_all = false): array
    {
        $paged = max(1, (int) ($filters['paged'] ?? 1));

        $args = [
            'post_type'      => 'listdom-listing',
            'post_status'    => 'publish',
            'posts_per_page' => $get_all ? -1 : self::PER_PAGE,
            'paged'          => $get_all ? 1 : $paged,
            'meta_query'     => ['relation' => 'AND'],
            'tax_query'      => ['relation' => 'AND'],
        ];

        // City — LIKE '%City, FL%'
        if (!empty($filters['city'])) {
            $args['meta_query'][] = [
                'key'     => 'lsd_address',
                'value'   => sanitize_text_field($filters['city']) . ', FL',
                'compare' => 'LIKE',
            ];
        }

        // Category taxonomy
        if (!empty($filters['category'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'listdom-category',
                'field'    => 'term_id',
                'terms'    => (int) $filters['category'],
            ];
        }

        // Service type via listdom-tag
        if (!empty($filters['type'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'listdom-tag',
                'field'    => 'slug',
                'terms'    => sanitize_title($filters['type']),
            ];
        }

        // Minimum rating
        if (!empty($filters['rating']) && (float) $filters['rating'] > 0) {
            $args['meta_query'][] = [
                'key'     => '_mfl_rating',
                'value'   => (float) $filters['rating'],
                'compare' => '>=',
                'type'    => 'DECIMAL(10,1)',
            ];
        }

        // Sort
        switch ($filters['sort'] ?? 'newest') {
            case 'rating':
                $args['meta_key'] = '_mfl_rating';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'relevance':
                if (!empty($filters['s'])) {
                    $args['s']       = sanitize_text_field($filters['s']);
                    $args['orderby'] = 'relevance';
                } else {
                    $args['orderby'] = 'date';
                    $args['order']   = 'DESC';
                }
                break;
            default: // newest, distance (distance sorted in PHP later)
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
        }

        // Clean up empty tax_query / meta_query
        if (count($args['tax_query']) === 1) unset($args['tax_query']);
        if (count($args['meta_query']) === 1) unset($args['meta_query']);

        return $args;
    }

    // ── "Open Now" PHP filter ─────────────────────────────────────────────────

    private function is_open_now(int $post_id): bool
    {
        $hours_json = get_post_meta($post_id, '_mfl_hours_json', true);
        if (!$hours_json) return false;

        $hours = json_decode($hours_json, true);
        if (!is_array($hours)) return false;

        // PHP date('w'): 0=Sunday…6=Saturday
        // Google hours array: index 0=Monday…6=Sunday
        $wp_to_google = [0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5];
        $google_index = $wp_to_google[(int) current_time('w')];

        if (!isset($hours[$google_index])) return false;

        $line  = $hours[$google_index];
        $parts = explode(': ', $line, 2);
        if (count($parts) < 2) return false;

        $time_str = trim($parts[1]);

        if (stripos($time_str, 'Closed') !== false) return false;
        if (stripos($time_str, 'Open 24') !== false) return true;

        // "9:00 AM – 5:00 PM"
        if (!preg_match('/(\d{1,2}:\d{2}\s*[AP]M)\s*[–\-]\s*(\d{1,2}:\d{2}\s*[AP]M)/i', $time_str, $m)) {
            return false;
        }

        $open_ts  = strtotime($m[1]);
        $close_ts = strtotime($m[2]);
        $now_ts   = strtotime(current_time('g:i A'));

        return $now_ts >= $open_ts && $now_ts <= $close_ts;
    }

    // ── Distance sort ─────────────────────────────────────────────────────────

    private function sort_by_distance(array $posts, float $user_lat, float $user_lng): array
    {
        $distances = [];
        foreach ($posts as $p) {
            $lat = (float) get_post_meta($p->ID, 'lsd_latitude',  true);
            $lng = (float) get_post_meta($p->ID, 'lsd_longitude', true);
            $distances[$p->ID] = ($lat && $lng)
                ? $this->haversine($user_lat, $user_lng, $lat, $lng)
                : PHP_FLOAT_MAX;
        }

        usort($posts, fn($a, $b) => $distances[$a->ID] <=> $distances[$b->ID]);
        return $posts;
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R  = 3959; // miles
        $dL = deg2rad($lat2 - $lat1);
        $dO = deg2rad($lng2 - $lng1);
        $a  = sin($dL / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dO / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    // ── Map pin data ──────────────────────────────────────────────────────────

    public function get_map_pins(array $posts, array $filters = []): array
    {
        // For non-filtered queries, get a broader pin set via lightweight query
        if (empty($filters['open_now']) && ($filters['sort'] ?? '') !== 'distance' && count($posts) >= self::PER_PAGE) {
            $pin_args = $this->build_query_args($filters);
            $pin_args['posts_per_page'] = self::MAX_PINS;
            $pin_args['paged']          = 1;
            $pin_args['fields']         = 'ids';
            $pin_ids  = get_posts($pin_args);
        } else {
            $pin_ids = array_map(fn($p) => $p->ID, $posts);
        }

        $pins = [];
        foreach (array_slice((array) $pin_ids, 0, self::MAX_PINS) as $id) {
            $lat = (float) get_post_meta($id, 'lsd_latitude',  true);
            $lng = (float) get_post_meta($id, 'lsd_longitude', true);
            if (!$lat || !$lng) continue;

            $rating = (float) get_post_meta($id, '_mfl_rating', true);
            $img    = mfl_listing_image_url($id, 'thumbnail');

            $pins[] = [
                'id'      => $id,
                'lat'     => $lat,
                'lng'     => $lng,
                'title'   => get_the_title($id),
                'url'     => get_permalink($id),
                'address' => get_post_meta($id, 'lsd_address', true),
                'rating'  => $rating,
                'img'     => $img ?: '',
            ];
        }

        return $pins;
    }

    // ── Card renderer ─────────────────────────────────────────────────────────

    public function render_cards(array $posts, string $view = 'grid'): string
    {
        if (empty($posts)) {
            return '<div class="mfl-arch-empty">'
                 . '<svg aria-hidden="true" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>'
                 . '<p>No listings found matching your filters.</p>'
                 . '<p class="mfl-arch-empty__hint">Try adjusting your filters or <a href="?" class="mfl-reset-link">clear all</a>.</p>'
                 . '</div>';
        }

        $html = '';
        foreach ($posts as $post) {
            $html .= $this->render_card($post, $view);
        }
        return $html;
    }

    private function render_card(\WP_Post $post, string $view): string
    {
        $id      = $post->ID;
        $title   = get_the_title($id);
        $url     = get_permalink($id);
        $address = get_post_meta($id, 'lsd_address',       true);
        $phone   = get_post_meta($id, 'lsd_phone',         true);
        $website = get_post_meta($id, 'lsd_website',       true);
        $rating  = (float) get_post_meta($id, '_mfl_rating',       true);
        $count   = (int)   get_post_meta($id, '_mfl_review_count', true);
        $img     = mfl_listing_image_url($id, 'medium');

        $cats     = get_the_terms($id, 'listdom-category');
        $cat_name = (!empty($cats) && !is_wp_error($cats)) ? $cats[0]->name : '';

        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= round($rating) ? '★' : '☆';
        }

        if ($view === 'list') {
            return $this->render_list_card($id, $title, $url, $img, $address, $phone, $website, $rating, $count, $cat_name, $stars);
        }
        return $this->render_grid_card($id, $title, $url, $img, $address, $phone, $website, $rating, $count, $cat_name, $stars);
    }

    private function render_grid_card(
        int $id, string $title, string $url, string $img, string $address,
        string $phone, string $website, float $rating, int $count,
        string $cat_name, string $stars
    ): string {
        $img_html = $img
            ? '<img src="' . esc_url($img) . '" alt="' . esc_attr($title) . '" loading="lazy" class="mfl-card__img">'
            : '<div class="mfl-card__img-placeholder" aria-hidden="true"></div>';

        $cat_badge = $cat_name
            ? '<span class="mfl-card__cat">' . esc_html($cat_name) . '</span>'
            : '';

        $rating_html = $rating
            ? '<div class="mfl-card__rating" aria-label="' . esc_attr(number_format($rating, 1)) . ' out of 5">'
              . '<span class="mfl-card__stars">' . $stars . '</span>'
              . '<span class="mfl-card__rating-num">' . number_format($rating, 1) . '</span>'
              . ($count ? '<span class="mfl-card__count">(' . number_format($count) . ')</span>' : '')
              . '</div>'
            : '';

        $addr_html = $address
            ? '<p class="mfl-card__address">'
              . '<svg aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>'
              . esc_html($address) . '</p>'
            : '';

        $btns = '';
        if ($phone) {
            $btns .= '<a href="tel:' . esc_attr(preg_replace('/[^+\d]/', '', $phone)) . '" class="mfl-card__btn mfl-card__btn--phone" aria-label="Call ' . esc_attr($title) . '">'
                   . '<svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l.91-.9a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>Call</a>';
        }
        if ($website) {
            $btns .= '<a href="' . esc_url($website) . '" target="_blank" rel="noopener noreferrer" class="mfl-card__btn mfl-card__btn--web">Website</a>';
        }

        return '<article class="mfl-card mfl-card--grid" data-listing-id="' . esc_attr($id) . '">'
             . '<a href="' . esc_url($url) . '" class="mfl-card__img-wrap" tabindex="-1" aria-hidden="true">' . $img_html . $cat_badge . '</a>'
             . '<div class="mfl-card__body">'
             . '<h2 class="mfl-card__title"><a href="' . esc_url($url) . '">' . esc_html($title) . '</a></h2>'
             . $rating_html
             . $addr_html
             . ($btns ? '<div class="mfl-card__btns">' . $btns . '</div>' : '')
             . '</div>'
             . '</article>';
    }

    private function render_list_card(
        int $id, string $title, string $url, string $img, string $address,
        string $phone, string $website, float $rating, int $count,
        string $cat_name, string $stars
    ): string {
        $img_html = $img
            ? '<img src="' . esc_url($img) . '" alt="' . esc_attr($title) . '" loading="lazy" class="mfl-list-card__img">'
            : '<div class="mfl-list-card__img mfl-list-card__img--placeholder" aria-hidden="true"></div>';

        $cat_badge = $cat_name
            ? '<span class="mfl-card__cat">' . esc_html($cat_name) . '</span>'
            : '';

        $rating_html = $rating
            ? '<span class="mfl-card__stars" aria-label="' . esc_attr(number_format($rating, 1)) . ' stars">' . $stars . '</span>'
              . '<span class="mfl-card__rating-num">' . number_format($rating, 1) . '</span>'
              . ($count ? '<span class="mfl-card__count">(' . number_format($count) . ')</span>' : '')
            : '';

        $meta = trim(($cat_name ? '<span class="mfl-list-card__cat">' . esc_html($cat_name) . '</span>' : '')
              . ($address ? ' <span class="mfl-list-card__addr"><svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>' . esc_html($address) . '</span>' : ''));

        return '<article class="mfl-list-card" data-listing-id="' . esc_attr($id) . '">'
             . '<a href="' . esc_url($url) . '" class="mfl-list-card__img-wrap" tabindex="-1" aria-hidden="true">' . $img_html . '</a>'
             . '<div class="mfl-list-card__body">'
             . '<h2 class="mfl-list-card__title"><a href="' . esc_url($url) . '">' . esc_html($title) . '</a></h2>'
             . ($rating_html ? '<div class="mfl-list-card__rating">' . $rating_html . '</div>' : '')
             . ($meta ? '<div class="mfl-list-card__meta">' . $meta . '</div>' : '')
             . '</div>'
             . '<a href="' . esc_url($url) . '" class="mfl-list-card__cta">View Details</a>'
             . '</article>';
    }

    // ── Input sanitizer ───────────────────────────────────────────────────────

    public function sanitize_filters(array $raw): array
    {
        return [
            'city'     => sanitize_text_field($raw['city']     ?? ''),
            'category' => absint($raw['category'] ?? 0),
            'type'     => sanitize_title($raw['type']          ?? ''),
            'rating'   => min(5, max(0, (float) ($raw['rating'] ?? 0))),
            'open_now' => !empty($raw['open_now']) ? 1 : 0,
            'sort'     => in_array($raw['sort'] ?? '', ['newest', 'rating', 'distance', 'relevance'])
                          ? $raw['sort'] : 'newest',
            'view'     => in_array($raw['view'] ?? 'grid', ['grid', 'list'], true)
                          ? sanitize_key($raw['view'] ?? 'grid')
                          : 'grid',
            'user_lat' => (float) ($raw['user_lat'] ?? 0),
            'user_lng' => (float) ($raw['user_lng'] ?? 0),
            's'        => sanitize_text_field($raw['s']        ?? ''),
            'paged'    => max(1, (int) ($raw['paged']          ?? 1)),
        ];
    }
}
