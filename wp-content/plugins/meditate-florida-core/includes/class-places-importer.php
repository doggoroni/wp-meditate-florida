<?php
/**
 * Meditate Florida — Places Importer
 *
 * Queries the Google Places API for meditation-related businesses across 20
 * Florida cities, then creates or updates Listdom listings in WordPress.
 *
 * Listing meta keys used:
 *   lsd_address, lsd_latitude, lsd_longitude   — Listdom core geo fields
 *   lsd_phone, lsd_website                     — Listdom contact fields
 *   _mfl_google_place_id                        — our dedup key (private meta)
 *   _mfl_rating, _mfl_review_count             — Google rating data
 *   _mfl_hours_json                             — opening hours JSON
 *   _mfl_image_url                              — Google photo URL
 */

defined('ABSPATH') || exit;

class MFL_Places_Importer
{
    // ─── Configuration ───────────────────────────────────────────────────────

    const CITIES = [
        'Miami', 'Orlando', 'Tampa', 'Jacksonville', 'Fort Lauderdale',
        'Tallahassee', 'Gainesville', 'Sarasota', 'Naples', 'St. Augustine',
        'Key West', 'Boca Raton', 'West Palm Beach', 'Clearwater',
        'St. Petersburg', 'Pensacola', 'Daytona Beach', 'Fort Myers',
        'Ocala', 'Delray Beach',
    ];

    const SEARCH_TERMS = [
        'meditation center',
        'meditation retreat',
        'yoga meditation studio',
        'mindfulness center',
        'Buddhist center',
        'wellness retreat',
    ];

    const PLACE_FIELDS = [
        'place_id', 'name', 'formatted_address', 'formatted_phone_number',
        'website', 'rating', 'user_ratings_total', 'geometry',
        'photos', 'opening_hours', 'types',
    ];

    /** Milliseconds to sleep between Google API requests. */
    const RATE_LIMIT_MS = 200;

    /** Listdom post type and taxonomy (confirmed from plugin source). */
    const PTYPE   = 'listdom-listing';
    const TAX_CAT = 'listdom-category';

    /** Map Google place types → human-readable category names. */
    const TYPE_MAP = [
        'spa'                => 'Spa & Wellness',
        'gym'                => 'Gym & Fitness',
        'health'             => 'Health & Wellness',
        'church'             => 'Spiritual Center',
        'hindu_temple'       => 'Spiritual Center',
        'place_of_worship'   => 'Spiritual Center',
        'lodging'            => 'Retreat Center',
        'tourist_attraction' => 'Wellness Retreat',
        'point_of_interest'  => 'Wellness Center',
        'establishment'      => 'Wellness Center',
    ];

    // ─── Properties ──────────────────────────────────────────────────────────

    private MFL_Logger $log;
    private string     $api_key;

    /** Cache: all existing place_ids already in WP → post_id. Loaded once. */
    private array $existing_place_ids = [];

    // ─── Entry point ─────────────────────────────────────────────────────────

    public function __construct(MFL_Logger $logger)
    {
        $this->log     = $logger;
        $this->api_key = $this->load_api_key();
    }

    /**
     * Main entry point: imports all 20 cities and sends a summary email.
     */
    public function run_all_cities(): void
    {
        $this->log->separator();
        $this->log->info('=== Meditate Florida weekly import started ===');
        $this->log->info('Cities to process: ' . count(self::CITIES));

        // Pre-load all existing Google place IDs to avoid repeated DB queries.
        $this->load_existing_place_ids();

        $totals = ['new' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];
        $city_stats = [];

        foreach (self::CITIES as $city) {
            $stats = $this->import_city($city);
            $city_stats[$city] = $stats;
            $totals['new']     += $stats['new'];
            $totals['updated'] += $stats['updated'];
            $totals['skipped'] += $stats['skipped'];
            $totals['errors']  += $stats['errors'];
        }

        $this->log->separator();
        $this->log->info(sprintf(
            '=== Import complete — New: %d | Updated: %d | Skipped: %d | Errors: %d ===',
            $totals['new'], $totals['updated'], $totals['skipped'], $totals['errors']
        ));
        $this->log->separator();

        $this->send_summary_email($totals, $city_stats);
    }

    // ─── Per-city import ─────────────────────────────────────────────────────

    /**
     * Runs all search terms for one city, then syncs each result to WP.
     *
     * @return array{new: int, updated: int, skipped: int, errors: int}
     */
    private function import_city(string $city): array
    {
        $this->log->separator();
        $this->log->info("Starting city: {$city}");

        $stats  = ['new' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];
        $seen   = []; // place_ids we've processed this run (avoid double-processing)

        foreach (self::SEARCH_TERMS as $term) {
            $query   = "{$term} in {$city}, Florida";
            $results = $this->text_search($query);

            if ($results === null) {
                $this->log->warning("Search failed for: \"{$query}\"");
                $stats['errors']++;
                continue;
            }

            $this->log->info("  \"{$query}\" → {$results['count']} results");

            foreach ($results['places'] as $basic) {
                $place_id = $basic['place_id'] ?? null;

                if (!$place_id || isset($seen[$place_id])) {
                    $stats['skipped']++;
                    continue;
                }

                $seen[$place_id] = true;

                $this->rate_limit();

                $details = $this->place_details($place_id);

                if ($details === null) {
                    $this->log->warning("  Couldn't fetch details for place_id: {$place_id}");
                    $stats['errors']++;
                    continue;
                }

                $place = $this->normalize_place($details, $city, $term);

                if (isset($this->existing_place_ids[$place_id])) {
                    // Existing listing — update mutable fields only.
                    $post_id = $this->existing_place_ids[$place_id];
                    $updated = $this->update_listing($post_id, $place);

                    if ($updated) {
                        $this->log->info("  UPDATED: {$place['title']} (post #{$post_id})");
                        $stats['updated']++;
                    } else {
                        $this->log->info("  unchanged: {$place['title']} (post #{$post_id})");
                        $stats['skipped']++;
                    }
                } else {
                    // New listing.
                    $post_id = $this->create_listing($place);

                    if ($post_id > 0) {
                        // Add to our in-memory cache so we don't create duplicates
                        // if the same place appears in another search term later.
                        $this->existing_place_ids[$place_id] = $post_id;
                        $this->log->info("  CREATED: {$place['title']} (post #{$post_id})");
                        $stats['new']++;
                    } else {
                        $this->log->error("  FAILED to create listing: {$place['title']}");
                        $stats['errors']++;
                    }
                }
            }
        }

        $this->log->info(sprintf(
            "Finished %s — New: %d | Updated: %d | Skipped: %d | Errors: %d",
            $city, $stats['new'], $stats['updated'], $stats['skipped'], $stats['errors']
        ));

        return $stats;
    }

    // ─── Google Places API ───────────────────────────────────────────────────

    /**
     * Text Search endpoint.
     * Returns ['count' => int, 'places' => array] or null on failure.
     */
    private function text_search(string $query): ?array
    {
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query([
            'query'  => $query,
            'region' => 'us',
            'key'    => $this->api_key,
        ]);

        $body = $this->http_get($url);
        if ($body === null) return null;

        $data   = json_decode($body, true);
        $status = $data['status'] ?? 'UNKNOWN';

        if ($status === 'ZERO_RESULTS') {
            return ['count' => 0, 'places' => []];
        }

        if ($status !== 'OK') {
            $msg = $data['error_message'] ?? '';
            $this->log->warning("Places API error [{$status}]: {$msg}");
            return null;
        }

        $places = $data['results'] ?? [];

        return ['count' => count($places), 'places' => $places];
    }

    /**
     * Place Details endpoint.
     * Returns the full details array or null on failure.
     */
    private function place_details(string $place_id): ?array
    {
        $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
            'place_id' => $place_id,
            'fields'   => implode(',', self::PLACE_FIELDS),
            'key'      => $this->api_key,
        ]);

        $body = $this->http_get($url);
        if ($body === null) return null;

        $data   = json_decode($body, true);
        $status = $data['status'] ?? 'UNKNOWN';

        if ($status !== 'OK') {
            $msg = $data['error_message'] ?? '';
            $this->log->warning("Details API error [{$status}]: {$msg}");
            return null;
        }

        return $data['result'] ?? null;
    }

    // ─── Data normalization ──────────────────────────────────────────────────

    /**
     * Flatten a Place Details API response into a flat array ready for WP.
     */
    private function normalize_place(array $place, string $city, string $search_term = ''): array
    {
        $full_address = $place['formatted_address'] ?? '';
        $parsed       = $this->parse_address($full_address);

        $image_url = '';
        if (!empty($place['photos'][0]['photo_reference'])) {
            $ref       = $place['photos'][0]['photo_reference'];
            $image_url = add_query_arg([
                'maxwidth'        => 800,
                'photo_reference' => $ref,
                'key'             => $this->api_key,
            ], 'https://maps.googleapis.com/maps/api/place/photo');
        }

        $hours_json = '';
        if (!empty($place['opening_hours']['weekday_text'])) {
            $hours_json = wp_json_encode($place['opening_hours']['weekday_text'], JSON_UNESCAPED_UNICODE);
        }

        return [
            'title'        => $place['name'] ?? '',
            'address'      => trim(implode(', ', array_filter([
                $parsed['street'],
                $parsed['city'] ?: $city,
                ($parsed['state'] ?: 'FL') . ' ' . $parsed['zip'],
            ]))),
            'street'       => $parsed['street'],
            'city'         => $parsed['city'] ?: $city,
            'state'        => $parsed['state'] ?: 'FL',
            'zip'          => $parsed['zip'],
            'phone'        => $place['formatted_phone_number'] ?? '',
            'website'      => $place['website'] ?? '',
            'lat'          => (string) ($place['geometry']['location']['lat'] ?? ''),
            'lng'          => (string) ($place['geometry']['location']['lng'] ?? ''),
            'category'     => $this->derive_category($place['types'] ?? [], $search_term),
            'rating'       => (string) ($place['rating'] ?? ''),
            'review_count' => (string) ($place['user_ratings_total'] ?? ''),
            'place_id'     => $place['place_id'] ?? '',
            'image_url'    => $image_url,
            'hours_json'   => $hours_json,
        ];
    }

    /**
     * Parse a Google-formatted address into components.
     * Input format: "123 Main St, Miami, FL 33101, USA"
     */
    private function parse_address(string $full): array
    {
        $result = ['street' => '', 'city' => '', 'state' => '', 'zip' => ''];

        $full  = preg_replace('/,?\s*USA\s*$/i', '', $full);
        $parts = array_map('trim', explode(',', $full));

        $last = array_pop($parts) ?? '';
        if (preg_match('/([A-Z]{2})\s+(\d{5})/', $last, $m)) {
            $result['state'] = $m[1];
            $result['zip']   = $m[2];
        }

        if (!empty($parts)) {
            $result['city'] = array_pop($parts);
        }

        $result['street'] = implode(', ', $parts);

        return $result;
    }

    /**
     * Pick the most meaningful category label, preferring the search term used
     * to discover the place, falling back to Google's types array.
     */
    private function derive_category(array $types, string $search_term = ''): string
    {
        // ── 1. Search-term based (most accurate) ────────────────────────────────
        $term_lower = strtolower($search_term);
        if (str_contains($term_lower, 'buddhist'))          return 'Buddhist Center';
        if (str_contains($term_lower, 'mindfulness'))        return 'Mindfulness Center';
        if (str_contains($term_lower, 'yoga'))               return 'Yoga Studio';
        if (str_contains($term_lower, 'meditation retreat')) return 'Meditation Retreat';
        if (str_contains($term_lower, 'wellness retreat'))   return 'Wellness Retreat';
        if (str_contains($term_lower, 'meditation center'))  return 'Meditation Center';
        if (str_contains($term_lower, 'meditation'))         return 'Meditation Center';

        // ── 2. Google types fallback (secondary signal) ──────────────────────────
        foreach ($types as $type) {
            if (isset(self::TYPE_MAP[$type])) {
                return self::TYPE_MAP[$type];
            }
        }

        return 'Wellness Center';
    }

    // ─── WordPress listing CRUD ──────────────────────────────────────────────

    /**
     * Pre-load all existing _mfl_google_place_id values into memory.
     * This avoids a DB query for every single place during the import.
     */
    private function load_existing_place_ids(): void
    {
        global $wpdb;

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT pm.post_id, pm.meta_value
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = %s
               AND p.post_type = %s
               AND p.post_status != 'trash'",
            '_mfl_google_place_id',
            self::PTYPE
        ));

        $this->existing_place_ids = [];
        foreach ($rows as $row) {
            $this->existing_place_ids[$row->meta_value] = (int) $row->post_id;
        }

        $this->log->info(sprintf(
            'Loaded %d existing listings from database.',
            count($this->existing_place_ids)
        ));
    }

    /**
     * Create a brand-new Listdom listing.
     * Returns the new post ID, or 0 on failure.
     */
    private function create_listing(array $place): int
    {
        // 1. Insert the WP post.
        $post_id = wp_insert_post([
            'post_title'   => sanitize_text_field($place['title']),
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => self::PTYPE,
        ], true);

        if (is_wp_error($post_id)) {
            $this->log->error('wp_insert_post failed: ' . $post_id->get_error_message());
            return 0;
        }

        // 2. Resolve / create the category term.
        $term_id = $this->get_or_create_category($place['category']);

        if ($term_id > 0) {
            wp_set_object_terms($post_id, [$term_id], self::TAX_CAT, false);
            update_post_meta($post_id, 'lsd_primary_category', $term_id);
        }

        // 3. Write all Listdom core meta fields.
        $this->write_listdom_meta($post_id, $place);

        // 4. Write our custom meta fields.
        $this->write_mfl_meta($post_id, $place);

        // 5. Insert the geo-point row that Listdom uses for map proximity search.
        $this->upsert_geopoint($post_id, $place['lat'], $place['lng']);

        return $post_id;
    }

    /**
     * Update only the mutable fields (phone, website, hours) if they have changed.
     * Returns true if any field was actually changed, false if nothing changed.
     */
    private function update_listing(int $post_id, array $place): bool
    {
        $changed = false;

        $fields = [
            'lsd_phone'       => sanitize_text_field($place['phone']),
            'lsd_website'     => esc_url_raw($place['website']),
            '_mfl_hours_json' => $place['hours_json'],
        ];

        foreach ($fields as $key => $new_value) {
            $old_value = get_post_meta($post_id, $key, true);
            if ($old_value !== $new_value) {
                update_post_meta($post_id, $key, $new_value);
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * Write the Listdom-standard meta keys for a listing.
     */
    private function write_listdom_meta(int $post_id, array $place): void
    {
        update_post_meta($post_id, 'lsd_address',   sanitize_text_field($place['address']));
        update_post_meta($post_id, 'lsd_latitude',  sanitize_text_field($place['lat']));
        update_post_meta($post_id, 'lsd_longitude', sanitize_text_field($place['lng']));
        update_post_meta($post_id, 'lsd_phone',     sanitize_text_field($place['phone']));
        update_post_meta($post_id, 'lsd_website',   esc_url_raw($place['website']));
        update_post_meta($post_id, 'lsd_object_type', 'marker');
    }

    /**
     * Write our custom _mfl_* meta keys for a listing.
     */
    private function write_mfl_meta(int $post_id, array $place): void
    {
        update_post_meta($post_id, '_mfl_google_place_id', sanitize_text_field($place['place_id']));
        update_post_meta($post_id, '_mfl_rating',          sanitize_text_field($place['rating']));
        update_post_meta($post_id, '_mfl_review_count',    sanitize_text_field($place['review_count']));
        update_post_meta($post_id, '_mfl_hours_json',      $place['hours_json']);
        update_post_meta($post_id, '_mfl_image_url',       esc_url_raw($place['image_url']));
    }

    /**
     * Upsert a row in Listdom's spatial geo-point table.
     * This is required for the map search functionality to work.
     */
    private function upsert_geopoint(int $post_id, string $lat, string $lng): void
    {
        global $wpdb;

        if (!is_numeric($lat) || !is_numeric($lng)) return;

        $table = $wpdb->prefix . 'lsd_data';

        // Use REPLACE INTO so it works for both insert and update.
        $wpdb->query($wpdb->prepare(
            "REPLACE INTO `{$table}` (`id`, `latitude`, `longitude`) VALUES (%d, %s, %s)",
            $post_id, $lat, $lng
        ));

        // Update the MySQL Point spatial column (used for radius searches).
        $wpdb->query($wpdb->prepare(
            "UPDATE `{$table}` SET `point` = Point(`latitude`, `longitude`) WHERE `id` = %d",
            $post_id
        ));
    }

    /**
     * Find a listdom-category term by name, or create it if it doesn't exist.
     * Returns the term ID, or 0 on failure.
     */
    private function get_or_create_category(string $name): int
    {
        $term = get_term_by('name', $name, self::TAX_CAT);

        if ($term instanceof WP_Term) {
            return $term->term_id;
        }

        $result = wp_insert_term($name, self::TAX_CAT);

        if (is_wp_error($result)) {
            $this->log->warning("Could not create category \"{$name}\": " . $result->get_error_message());
            return 0;
        }

        $this->log->info("Created new category: \"{$name}\"");
        return (int) $result['term_id'];
    }

    // ─── Summary email ───────────────────────────────────────────────────────

    /**
     * Send an HTML summary email to the site admin when the import finishes.
     */
    private function send_summary_email(array $totals, array $city_stats): void
    {
        $admin_email = get_option('admin_email');
        $site_name   = get_option('blogname');
        $date        = date('l, F j, Y \a\t g:ia');

        // Build city-by-city breakdown table rows.
        $rows = '';
        foreach ($city_stats as $city => $s) {
            $rows .= sprintf(
                '<tr><td>%s</td><td>%d</td><td>%d</td><td>%d</td><td>%d</td></tr>',
                esc_html($city), $s['new'], $s['updated'], $s['skipped'], $s['errors']
            );
        }

        $body = "
        <html><body style='font-family: Arial, sans-serif; color: #333;'>
        <h2>Meditate Florida — Weekly Import Report</h2>
        <p>Run completed on <strong>{$date}</strong></p>

        <h3>Totals</h3>
        <table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
            <tr style='background:#f5f5f5;'>
                <th>New listings</th><th>Updated</th><th>Unchanged</th><th>Errors</th>
            </tr>
            <tr>
                <td>{$totals['new']}</td>
                <td>{$totals['updated']}</td>
                <td>{$totals['skipped']}</td>
                <td>{$totals['errors']}</td>
            </tr>
        </table>

        <h3>By City</h3>
        <table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
            <tr style='background:#f5f5f5;'>
                <th>City</th><th>New</th><th>Updated</th><th>Unchanged</th><th>Errors</th>
            </tr>
            {$rows}
        </table>

        <p style='color:#888; font-size:12px;'>
            Full log: wp-content/logs/places-import.log<br>
            Next run: next Sunday at 3:00 AM site time.
        </p>
        </body></html>";

        add_filter('wp_mail_content_type', fn() => 'text/html');

        $sent = wp_mail(
            $admin_email,
            "[{$site_name}] Weekly Places Import Complete",
            $body,
            ['Content-Type: text/html; charset=UTF-8']
        );

        remove_filter('wp_mail_content_type', fn() => 'text/html');

        if ($sent) {
            $this->log->info("Summary email sent to {$admin_email}");
        } else {
            $this->log->warning("Failed to send summary email to {$admin_email}");
        }
    }

    // ─── HTTP & utilities ────────────────────────────────────────────────────

    /**
     * Simple cURL GET. Returns the response body or null on failure.
     */
    private function http_get(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'MeditateFL-WP/' . MFL_VERSION,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body  = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno || $body === false) {
            $this->log->error("HTTP GET failed ({$url}): {$error}");
            return null;
        }

        return $body;
    }

    private function rate_limit(): void
    {
        usleep(self::RATE_LIMIT_MS * 1000);
    }

    // ─── .env loader ────────────────────────────────────────────────────────

    /**
     * Read GOOGLE_PLACES_API_KEY from the project-root .env file.
     * Throws a RuntimeException if the key is not found.
     */
    private function load_api_key(): string
    {
        if (!file_exists(MFL_ENV_FILE)) {
            throw new RuntimeException(
                'MFL: .env file not found at ' . MFL_ENV_FILE .
                '. Add GOOGLE_PLACES_API_KEY=your_key to that file.'
            );
        }

        foreach (file(MFL_ENV_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);

            if (trim($key) === 'GOOGLE_PLACES_API_KEY') {
                $value = trim(trim($value), '"\'');
                if ($value === '') break;
                return $value;
            }
        }

        throw new RuntimeException('MFL: GOOGLE_PLACES_API_KEY not set in .env');
    }
}
