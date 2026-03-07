<?php
/**
 * Google Places importer for meditation-related businesses in Florida.
 *
 * Usage:
 *   php import-places.php "Miami"
 *   php import-places.php "Orlando"
 *
 * Reads GOOGLE_PLACES_API_KEY from .env at the project root (wp-content/../../../).
 * Outputs a CSV to: wp-content/tools/output/[city]-places.csv
 */

declare(strict_types=1);

// ─── Bootstrap ───────────────────────────────────────────────────────────────

define('TOOLS_DIR',  __DIR__);
define('OUTPUT_DIR', __DIR__ . '/output');
define('ENV_FILE',   __DIR__ . '/../../../.env'); // app/public/.env

const RATE_LIMIT_MS = 200; // milliseconds between API requests

const SEARCH_TERMS = [
    'meditation center',
    'meditation retreat',
    'yoga meditation studio',
    'mindfulness center',
    'Buddhist center',
    'wellness retreat',
];

const PLACE_FIELDS = [
    'place_id',
    'name',
    'formatted_address',
    'formatted_phone_number',
    'website',
    'rating',
    'user_ratings_total',
    'geometry',
    'photos',
    'opening_hours',
    'types',
];

// ─── Entry point ─────────────────────────────────────────────────────────────

main($argv);

function main(array $argv): void
{
    $city = parse_city_arg($argv);
    $apiKey = load_api_key();

    log_info("Starting import for city: {$city}");
    log_info('Search terms: ' . count(SEARCH_TERMS));

    $places = collect_places($city, $apiKey);

    if (empty($places)) {
        log_warning('No places found. Check your API key and city name.');
        exit(1);
    }

    log_info(count($places) . ' unique places collected after deduplication.');

    $outputPath = write_csv($places, $city);

    log_info("Done! CSV saved to: {$outputPath}");
}

// ─── CLI argument parsing ─────────────────────────────────────────────────────

function parse_city_arg(array $argv): string
{
    if (empty($argv[1])) {
        log_error('Usage: php import-places.php "City Name"');
        exit(1);
    }

    $city = trim($argv[1]);

    if (strlen($city) < 2) {
        log_error('City name is too short.');
        exit(1);
    }

    return $city;
}

// ─── .env loader ─────────────────────────────────────────────────────────────

function load_api_key(): string
{
    if (!file_exists(ENV_FILE)) {
        log_error('.env file not found at: ' . ENV_FILE);
        log_error('Create a .env file in app/public/ with: GOOGLE_PLACES_API_KEY=your_key_here');
        exit(1);
    }

    $lines = file(ENV_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments
        if (str_starts_with($line, '#')) {
            continue;
        }

        if (str_starts_with($line, 'GOOGLE_PLACES_API_KEY=')) {
            $key = trim(substr($line, strlen('GOOGLE_PLACES_API_KEY=')));
            $key = trim($key, '"\''); // strip optional quotes

            if (empty($key)) {
                log_error('GOOGLE_PLACES_API_KEY is empty in .env');
                exit(1);
            }

            return $key;
        }
    }

    log_error('GOOGLE_PLACES_API_KEY not found in .env file.');
    exit(1);
}

// ─── Place collection ─────────────────────────────────────────────────────────

/**
 * Runs all search terms for the city, fetches details, deduplicates.
 *
 * @return array<string, array> Keyed by place_id
 */
function collect_places(string $city, string $apiKey): array
{
    $seen    = []; // place_id => true
    $places  = []; // place_id => enriched place data

    foreach (SEARCH_TERMS as $term) {
        $query = "{$term} in {$city}, Florida";
        log_info("Searching: \"{$query}\"");

        $results = text_search($query, $apiKey);

        if ($results === null) {
            log_warning("Search failed for term: {$term}. Skipping.");
            continue;
        }

        log_info('  Found ' . count($results) . ' results.');

        foreach ($results as $result) {
            $placeId = $result['place_id'] ?? null;

            if (!$placeId || isset($seen[$placeId])) {
                continue;
            }

            $seen[$placeId] = true;

            rate_limit();

            log_info("  Fetching details for: {$result['name']} ({$placeId})");

            $details = place_details($placeId, $apiKey);

            if ($details === null) {
                log_warning("  Could not fetch details for place_id: {$placeId}. Skipping.");
                continue;
            }

            $places[$placeId] = normalize_place($details, $city);
        }
    }

    return $places;
}

// ─── Google Places API calls ──────────────────────────────────────────────────

/**
 * Text Search — returns array of basic place results or null on failure.
 */
function text_search(string $query, string $apiKey): ?array
{
    $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query([
        'query'  => $query,
        'region' => 'us',
        'key'    => $apiKey,
    ]);

    $response = http_get($url);

    if ($response === null) {
        return null;
    }

    $data = json_decode($response, true);

    if (($data['status'] ?? '') !== 'OK' && ($data['status'] ?? '') !== 'ZERO_RESULTS') {
        $status = $data['status'] ?? 'UNKNOWN';
        $msg    = $data['error_message'] ?? '';
        log_warning("  API error: {$status} {$msg}");
        return null;
    }

    return $data['results'] ?? [];
}

/**
 * Place Details — returns the full detail object or null on failure.
 */
function place_details(string $placeId, string $apiKey): ?array
{
    $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
        'place_id' => $placeId,
        'fields'   => implode(',', PLACE_FIELDS),
        'key'      => $apiKey,
    ]);

    $response = http_get($url);

    if ($response === null) {
        return null;
    }

    $data = json_decode($response, true);

    if (($data['status'] ?? '') !== 'OK') {
        $status = $data['status'] ?? 'UNKNOWN';
        $msg    = $data['error_message'] ?? '';
        log_warning("  Details API error: {$status} {$msg}");
        return null;
    }

    return $data['result'] ?? null;
}

// ─── Data normalization ───────────────────────────────────────────────────────

/**
 * Flatten a Place Details response into the CSV row shape.
 */
function normalize_place(array $place, string $city): array
{
    $address  = $place['formatted_address'] ?? '';
    $parsed   = parse_address($address);

    // Build a photo URL from the first photo reference (if any)
    $imageUrl = '';
    if (!empty($place['photos'][0]['photo_reference'])) {
        // This URL is valid in a browser but requires the API key.
        // For import purposes we store the reference and construct the URL.
        $ref      = $place['photos'][0]['photo_reference'];
        $imageUrl = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=800&photo_reference={$ref}";
        // Note: append &key=YOUR_KEY when using this URL.
    }

    // Opening hours as JSON string
    $hoursJson = '';
    if (!empty($place['opening_hours']['weekday_text'])) {
        $hoursJson = json_encode($place['opening_hours']['weekday_text']);
    }

    // Category: use the most relevant non-generic type
    $category = derive_category($place['types'] ?? []);

    return [
        'title'           => $place['name'] ?? '',
        'description'     => '',                         // not provided by Places API
        'address'         => $parsed['street'],
        'city'            => $parsed['city'] ?: $city,
        'state'           => $parsed['state'] ?: 'FL',
        'zip'             => $parsed['zip'],
        'phone'           => $place['formatted_phone_number'] ?? '',
        'website'         => $place['website'] ?? '',
        'email'           => '',                         // not provided by Places API
        'lat'             => $place['geometry']['location']['lat'] ?? '',
        'lng'             => $place['geometry']['location']['lng'] ?? '',
        'category'        => $category,
        'rating'          => $place['rating'] ?? '',
        'review_count'    => $place['user_ratings_total'] ?? '',
        'google_place_id' => $place['place_id'] ?? '',
        'image_url'       => $imageUrl,
        'hours_json'      => $hoursJson,
    ];
}

/**
 * Very lightweight US address parser.
 * Google returns addresses like: "123 Main St, Miami, FL 33101, USA"
 */
function parse_address(string $full): array
{
    $result = ['street' => '', 'city' => '', 'state' => '', 'zip' => ''];

    // Remove ", USA" suffix
    $full = preg_replace('/,?\s*USA\s*$/i', '', $full);
    $parts = array_map('trim', explode(',', $full));

    // Last part should be "FL 33101" or "Florida 33101"
    $last = array_pop($parts) ?? '';
    if (preg_match('/([A-Z]{2})\s+(\d{5})/', $last, $m)) {
        $result['state'] = $m[1];
        $result['zip']   = $m[2];
    }

    // Second-to-last is city
    if (!empty($parts)) {
        $result['city'] = array_pop($parts);
    }

    // Everything remaining is street
    $result['street'] = implode(', ', $parts);

    return $result;
}

/**
 * Pick a human-friendly category from the Google place types array.
 */
function derive_category(array $types): string
{
    $map = [
        'spa'                   => 'Spa & Wellness',
        'gym'                   => 'Gym & Fitness',
        'health'                => 'Health & Wellness',
        'church'                => 'Spiritual Center',
        'hindu_temple'          => 'Spiritual Center',
        'place_of_worship'      => 'Spiritual Center',
        'lodging'               => 'Retreat Center',
        'tourist_attraction'    => 'Wellness Retreat',
        'point_of_interest'     => 'Wellness Center',
        'establishment'         => 'Wellness Center',
    ];

    foreach ($types as $type) {
        if (isset($map[$type])) {
            return $map[$type];
        }
    }

    return 'Wellness Center';
}

// ─── CSV output ───────────────────────────────────────────────────────────────

/**
 * Writes places to a CSV and returns the file path.
 */
function write_csv(array $places, string $city): string
{
    if (!is_dir(OUTPUT_DIR) && !mkdir(OUTPUT_DIR, 0755, true)) {
        log_error('Could not create output directory: ' . OUTPUT_DIR);
        exit(1);
    }

    $slug     = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $city));
    $filename = OUTPUT_DIR . "/{$slug}-places.csv";

    $fh = fopen($filename, 'w');
    if ($fh === false) {
        log_error("Could not open file for writing: {$filename}");
        exit(1);
    }

    // BOM for Excel UTF-8 compatibility
    fwrite($fh, "\xEF\xBB\xBF");

    $headers = [
        'title', 'description', 'address', 'city', 'state', 'zip',
        'phone', 'website', 'email', 'lat', 'lng', 'category',
        'rating', 'review_count', 'google_place_id', 'image_url', 'hours_json',
    ];

    fputcsv($fh, $headers);

    foreach ($places as $place) {
        fputcsv($fh, array_values($place));
    }

    fclose($fh);

    return $filename;
}

// ─── HTTP helper ──────────────────────────────────────────────────────────────

/**
 * Simple cURL GET with a 10-second timeout. Returns body string or null on failure.
 */
function http_get(string $url): ?string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'MeditateFL-Importer/1.0',
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $body  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($body === false || $error) {
        log_warning("HTTP request failed: {$error}");
        return null;
    }

    return $body;
}

// ─── Rate limiter ─────────────────────────────────────────────────────────────

function rate_limit(): void
{
    usleep(RATE_LIMIT_MS * 1000);
}

// ─── Logging helpers ──────────────────────────────────────────────────────────

function log_info(string $msg): void
{
    echo '[' . date('H:i:s') . '] ' . $msg . PHP_EOL;
}

function log_warning(string $msg): void
{
    echo '[' . date('H:i:s') . '] WARNING: ' . $msg . PHP_EOL;
}

function log_error(string $msg): void
{
    fwrite(STDERR, '[' . date('H:i:s') . '] ERROR: ' . $msg . PHP_EOL);
}
