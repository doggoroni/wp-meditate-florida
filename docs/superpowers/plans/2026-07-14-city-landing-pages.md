# City Landing Pages Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Server-rendered SEO landing pages at `/meditation/{slug}/` for the 20 target cities, with 301s from bare `?city=` archive URLs.

**Architecture:** Virtual pages — a rewrite rule in `meditate-florida-core` maps `/meditation/{slug}/` to a query var; a new child-theme template renders data-driven blocks from a code-based city registry. No DB entities.

**Tech Stack:** WordPress (PHP 8.2), Listdom geo table (`{prefix}lsd_data`), child theme `listdomer-child`, plugin `meditate-florida-core`.

## Global Constraints

- Never render `_mfl_image_url`; images resolve via `mfl_listing_image_url($id, $size)` only.
- All child-theme assets versioned via `mfl_asset_ver('/relative/path')`.
- Lint every touched PHP file with LocalWP PHP: `& "$env:LOCALAPPDATA\Programs\Local\resources\extraResources\lightning-services\php-8.2.27+1\bin\win64\php.exe" -l <file>`.
- Deploy = push to `master` (GitHub Action rsyncs + purges caches).
- Production checks via `ssh -p 18765 -i ~/.ssh/mfl_deploy u1673-newoibm6yff4@ssh.meditateflorida.com` and `wp` CLI at `~/www/meditateflorida.com/public_html`.
- Radius for "listings in a city" = 25 miles from registry lat/lng.
- The 20 cities (slug → name): miami→Miami, orlando→Orlando, tampa→Tampa, jacksonville→Jacksonville, fort-lauderdale→Fort Lauderdale, tallahassee→Tallahassee, gainesville→Gainesville, sarasota→Sarasota, naples→Naples, st-augustine→St. Augustine, key-west→Key West, boca-raton→Boca Raton, west-palm-beach→West Palm Beach, clearwater→Clearwater, st-petersburg→St. Petersburg, pensacola→Pensacola, daytona-beach→Daytona Beach, fort-myers→Fort Myers, ocala→Ocala, delray-beach→Delray Beach.

---

### Task 1: `MFL_City_Pages` registry + pure geo methods

**Files:**
- Create: `wp-content/plugins/meditate-florida-core/includes/class-city-pages.php`
- Modify: `wp-content/plugins/meditate-florida-core/meditate-florida-core.php` (require + init)
- Test: scratchpad harness (see Step 3)

**Interfaces:**
- Produces: `MFL_City_Pages::CITIES` (const array `slug => ['name','lat','lng','intro']`), `MFL_City_Pages::get_city(string $slug): ?array` (returns registry row + `'slug'` key), `MFL_City_Pages::get_nearby_cities(string $slug, int $n = 5): array` (list of `['slug','name','distance_miles']`, ascending), `MFL_City_Pages::haversine_miles(float,float,float,float): float`.

- [ ] **Step 1: Create the class file** with the registry (all 20 cities, coordinates below, hand-written ~90-word intros — write final copy at implementation, unique per city, mentioning real local flavor: e.g. Miami/Coral Gables & Wynwood studios; Sarasota/its Kadampa center & Siesta Key calm; Key West/island pace & sunset meditation; St. Augustine/historic district; etc. No two intros may share sentences.)

```php
<?php
/**
 * MFL_City_Pages — virtual SEO landing pages at /meditation/{slug}/.
 * Registry-driven: no DB entities. See docs/superpowers/specs/2026-07-14-*.md
 */
defined('ABSPATH') || exit;

class MFL_City_Pages
{
    const QUERY_VAR    = 'mfl_city_page';
    const RADIUS_MILES = 25;
    const RULES_VER    = 1; // bump when rewrite rules change

    const CITIES = [
        'miami'           => ['name' => 'Miami',           'lat' => 25.7617, 'lng' => -80.1918, 'intro' => '…'],
        'orlando'         => ['name' => 'Orlando',         'lat' => 28.5384, 'lng' => -81.3789, 'intro' => '…'],
        'tampa'           => ['name' => 'Tampa',           'lat' => 27.9506, 'lng' => -82.4572, 'intro' => '…'],
        'jacksonville'    => ['name' => 'Jacksonville',    'lat' => 30.3322, 'lng' => -81.6557, 'intro' => '…'],
        'fort-lauderdale' => ['name' => 'Fort Lauderdale', 'lat' => 26.1224, 'lng' => -80.1373, 'intro' => '…'],
        'tallahassee'     => ['name' => 'Tallahassee',     'lat' => 30.4383, 'lng' => -84.2807, 'intro' => '…'],
        'gainesville'     => ['name' => 'Gainesville',     'lat' => 29.6516, 'lng' => -82.3248, 'intro' => '…'],
        'sarasota'        => ['name' => 'Sarasota',        'lat' => 27.3364, 'lng' => -82.5307, 'intro' => '…'],
        'naples'          => ['name' => 'Naples',          'lat' => 26.1420, 'lng' => -81.7948, 'intro' => '…'],
        'st-augustine'    => ['name' => 'St. Augustine',   'lat' => 29.9012, 'lng' => -81.3124, 'intro' => '…'],
        'key-west'        => ['name' => 'Key West',        'lat' => 24.5551, 'lng' => -81.7800, 'intro' => '…'],
        'boca-raton'      => ['name' => 'Boca Raton',      'lat' => 26.3683, 'lng' => -80.1289, 'intro' => '…'],
        'west-palm-beach' => ['name' => 'West Palm Beach', 'lat' => 26.7153, 'lng' => -80.0534, 'intro' => '…'],
        'clearwater'      => ['name' => 'Clearwater',      'lat' => 27.9659, 'lng' => -82.8001, 'intro' => '…'],
        'st-petersburg'   => ['name' => 'St. Petersburg',  'lat' => 27.7676, 'lng' => -82.6403, 'intro' => '…'],
        'pensacola'       => ['name' => 'Pensacola',       'lat' => 30.4213, 'lng' => -87.2169, 'intro' => '…'],
        'daytona-beach'   => ['name' => 'Daytona Beach',   'lat' => 29.2108, 'lng' => -81.0228, 'intro' => '…'],
        'fort-myers'      => ['name' => 'Fort Myers',      'lat' => 26.6406, 'lng' => -81.8723, 'intro' => '…'],
        'ocala'           => ['name' => 'Ocala',           'lat' => 29.1872, 'lng' => -82.1401, 'intro' => '…'],
        'delray-beach'    => ['name' => 'Delray Beach',    'lat' => 26.4615, 'lng' => -80.0728, 'intro' => '…'],
    ];

    public static function get_city(string $slug): ?array
    {
        if (!isset(self::CITIES[$slug])) return null;
        $city = self::CITIES[$slug] + ['slug' => $slug];
        /** Allows later per-city admin overrides without touching this class. */
        $city['intro'] = apply_filters('mfl_city_intro', $city['intro'], $slug);
        return $city;
    }

    public static function haversine_miles(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r  = 3959;
        $dl = deg2rad($lat2 - $lat1);
        $do = deg2rad($lng2 - $lng1);
        $a  = sin($dl / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($do / 2) ** 2;
        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public static function get_nearby_cities(string $slug, int $n = 5): array
    {
        $me = self::CITIES[$slug] ?? null;
        if (!$me) return [];
        $out = [];
        foreach (self::CITIES as $other_slug => $c) {
            if ($other_slug === $slug) continue;
            $out[] = [
                'slug' => $other_slug,
                'name' => $c['name'],
                'distance_miles' => self::haversine_miles($me['lat'], $me['lng'], $c['lat'], $c['lng']),
            ];
        }
        usort($out, fn($a, $b) => $a['distance_miles'] <=> $b['distance_miles']);
        return array_slice($out, 0, $n);
    }
}
```

- [ ] **Step 2: Require the file** in `meditate-florida-core.php` next to the other requires: `require_once MFL_DIR . 'includes/class-city-pages.php';`

- [ ] **Step 3: Scratch-test the pure logic.** Write `scratchpad/test-city-pages.php` that stubs `defined`/`apply_filters` shims (define `ABSPATH`, `function apply_filters($t,$v){return $v;}`), includes the class file, and asserts: (a) `get_city('miami')['name'] === 'Miami'`, (b) `get_city('nope') === null`, (c) `get_nearby_cities('miami', 3)` returns 3 rows, first is `fort-lauderdale` (~25 mi), none is `miami`, distances ascending, (d) every registry row has non-empty intro ≥ 60 words distinct from all others (uniqueness check: no shared 8-word shingle between any two intros).

- [ ] **Step 4: Run the harness + lint both files.** Expected: all PASS lines, `No syntax errors`.

- [ ] **Step 5: Commit** `feat: city registry + geo methods for city landing pages`.

---

### Task 2: Routing — rewrite rule, query var, template loading, self-flush

**Files:**
- Modify: `wp-content/plugins/meditate-florida-core/includes/class-city-pages.php`
- Modify: `wp-content/plugins/meditate-florida-core/meditate-florida-core.php`

**Interfaces:**
- Produces: `MFL_City_Pages::register(): void` (hooks everything), template loads `city-landing.php` from child theme when `get_query_var(MFL_City_Pages::QUERY_VAR)` is a registry slug. `mfl_city_url(string $slug): string` helper in the main plugin file returning `home_url('/meditation/' . $slug . '/')`.

- [ ] **Step 1: Add `register()` and hook methods** to the class:

```php
public static function register(): void
{
    add_action('init',              [self::class, 'add_rewrite']);
    add_filter('query_vars',        [self::class, 'add_query_var']);
    add_filter('template_include',  [self::class, 'load_template']);
    add_action('wp',                [self::class, 'maybe_flush_rules']);
}

public static function add_rewrite(): void
{
    add_rewrite_rule('^meditation/([a-z0-9-]+)/?$', 'index.php?' . self::QUERY_VAR . '=$matches[1]', 'top');
}

public static function add_query_var(array $vars): array
{
    $vars[] = self::QUERY_VAR;
    return $vars;
}

public static function current_slug(): string
{
    return (string) get_query_var(self::QUERY_VAR);
}

public static function is_city_page(): bool
{
    $slug = self::current_slug();
    return $slug !== '' && isset(self::CITIES[$slug]);
}

public static function load_template(string $template): string
{
    $slug = self::current_slug();
    if ($slug === '') return $template;

    if (!isset(self::CITIES[$slug])) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        return get_404_template() ?: $template;
    }

    $t = locate_template('city-landing.php');
    return $t ?: $template;
}

/** Self-flush rewrite rules once per RULES_VER bump (deploys can't call activation hooks). */
public static function maybe_flush_rules(): void
{
    if ((int) get_option('mfl_city_rules_ver') !== self::RULES_VER) {
        flush_rewrite_rules(false);
        update_option('mfl_city_rules_ver', self::RULES_VER);
    }
}
```

- [ ] **Step 2: Init in main plugin file** (next to search handler init): `MFL_City_Pages::register();` inside the existing `add_action('init', …)` won't work for `init`-hooked methods — call `MFL_City_Pages::register();` at file scope after requires. Add helper:

```php
function mfl_city_url(string $slug): string
{
    return home_url('/meditation/' . $slug . '/');
}
```

- [ ] **Step 3: Guard against 404-ing the whole page tree.** Confirm rule regex `^meditation/…` cannot shadow existing routes (no page/CPT named "meditation" — verify with `wp post list --post_type=page --field=post_name` on server; expected: no `meditation` slug).

- [ ] **Step 4: Lint both files.** Expected: `No syntax errors`.

- [ ] **Step 5: Commit** `feat: route /meditation/{slug}/ to city landing template with self-flushing rules`.

---

### Task 3: City listing data — radius query, category counts, transients

**Files:**
- Modify: `wp-content/plugins/meditate-florida-core/includes/class-city-pages.php`

**Interfaces:**
- Consumes: `{prefix}lsd_data` (columns `id`, `latitude`, `longitude` — NOTE: keyed by `id`, not `post_id`), `_mfl_rating` meta.
- Produces: `MFL_City_Pages::get_city_listing_ids(string $slug): int[]` (rating-desc, transient-cached 12h, key `mfl_city_ids_{slug}`), `MFL_City_Pages::get_city_category_counts(string $slug): array` (`term_id => ['name','count']`, count desc).

- [ ] **Step 1: Implement both methods**:

```php
/** Published listings within RADIUS_MILES of the city center, best-rated first. */
public static function get_city_listing_ids(string $slug): array
{
    $city = self::CITIES[$slug] ?? null;
    if (!$city) return [];

    $key = 'mfl_city_ids_' . $slug;
    $ids = get_transient($key);
    if (is_array($ids)) return array_map('intval', $ids);

    global $wpdb;
    $ids = $wpdb->get_col($wpdb->prepare(
        "SELECT p.ID
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->prefix}lsd_data d ON d.id = p.ID
         LEFT  JOIN {$wpdb->postmeta} r ON r.post_id = p.ID AND r.meta_key = '_mfl_rating'
         WHERE p.post_type = 'listdom-listing'
           AND p.post_status = 'publish'
           AND ( 3959 * ACOS( LEAST(1,
                 COS(RADIANS(%f)) * COS(RADIANS(d.latitude))
               * COS(RADIANS(d.longitude) - RADIANS(%f))
               + SIN(RADIANS(%f)) * SIN(RADIANS(d.latitude)) ) ) ) <= %d
         ORDER BY CAST(COALESCE(r.meta_value, '0') AS DECIMAL(3,1)) DESC, p.post_date DESC",
        $city['lat'], $city['lng'], $city['lat'], self::RADIUS_MILES
    ));

    $ids = array_map('intval', $ids ?: []);
    set_transient($key, $ids, 12 * HOUR_IN_SECONDS);
    return $ids;
}

/** listdom-category counts across the city's listings, biggest first. */
public static function get_city_category_counts(string $slug): array
{
    $ids = self::get_city_listing_ids($slug);
    if (!$ids) return [];

    $counts = [];
    foreach ($ids as $id) {
        foreach ((array) get_the_terms($id, 'listdom-category') ?: [] as $t) {
            if (!$t instanceof WP_Term) continue;
            $counts[$t->term_id] ??= ['name' => $t->name, 'count' => 0];
            $counts[$t->term_id]['count']++;
        }
    }
    uasort($counts, fn($a, $b) => $b['count'] <=> $a['count']);
    return $counts;
}
```

- [ ] **Step 2: Lint.** Expected: `No syntax errors`.

- [ ] **Step 3: Commit** `feat: radius listing query + category counts for city pages`.

(Behavioral verification happens post-deploy in Task 7 via `wp eval 'print_r(count(MFL_City_Pages::get_city_listing_ids("miami")));'` — expected ~50+.)

---

### Task 4: `city-landing.php` template + CSS

**Files:**
- Create: `wp-content/themes/listdomer-child/city-landing.php`
- Create: `wp-content/themes/listdomer-child/assets/css/city-landing.css`
- Modify: `wp-content/themes/listdomer-child/functions.php` (enqueue)

**Interfaces:**
- Consumes: `MFL_City_Pages::{current_slug,get_city,get_city_listing_ids,get_city_category_counts,get_nearby_cities}`, `mfl_listing_image_url()`, `mfl_city_url()`, `MFL_Search_Handler::render_cards()` is **not** reused (it's row-object based); cards are rendered inline with the same classes as `page-home.php` (`mfl-listing-card…`) so existing home.css card styles apply — enqueue `home.css` too, or copy the card CSS into `city-landing.css` (decision: copy the needed card styles into `city-landing.css`; do not couple to the homepage bundle).
- Produces: page blocks in spec order — hero/breadcrumb, intro, category chips, Top Rated (6 cards), city grid (next 18, i.e. ids 7–24), FAQ, nearby cities.

- [ ] **Step 1: Write the template.** Structure (full markup written at implementation, following `page-home.php` card patterns exactly — image via `mfl_listing_image_url($pid,'medium_large')` with 🧘 placeholder fallback, star rating via a local `mfl_cl_stars()` copy of `mfl_stars()` if that helper is homepage-scoped — check first; hoist to functions.php if page-home defines it inside the template file):
  - Data prep at top: `$slug = MFL_City_Pages::current_slug(); $city = MFL_City_Pages::get_city($slug); $ids = MFL_City_Pages::get_city_listing_ids($slug); $cats = …; $nearby = …; $count = count($ids);`
  - Generated sentence: `sprintf('The directory currently lists %d places within %d miles of %s — %s and more.', $count, MFL_City_Pages::RADIUS_MILES, $city['name'], <top 3 category names joined>)`. Zero-listing state: intro + "Listings for {city} are on the way — browse the full directory meanwhile." + nearby cities (spec: no fatal, no soft-404 — page still 200s).
  - FAQ block: 3 Q&As — "How many meditation centers are there in {city}?" (count + categories), "What are the top-rated meditation spots in {city}?" (top 3 names + ratings), "What types of wellness practices can I find in {city}?" (category list). Rendered as `<details>` accordions.
  - Category chips link: `add_query_arg(['city' => $city['name'], 'category' => $term_id], $listings_url)`.
  - "View all" link: `add_query_arg(['city' => $city['name'], 'sort' => 'rating'], $listings_url)` (extra param dodges the Task 6 redirect).
  - Nearby chips: `mfl_city_url($n['slug'])` + rounded miles.
- [ ] **Step 2: Write `city-landing.css`** — hero band reusing the archive header treatment (`--mfl-forest` background, Playfair H1), chip styles, card grid (copied card styles), FAQ `<details>` styling, nearby-chip row. Mobile: single-column grid ≤ 640px.
- [ ] **Step 3: Enqueue in functions.php** (pattern-match the other conditional enqueues):

```php
add_action('wp_enqueue_scripts', function () {
    if (!get_query_var('mfl_city_page')) return;
    wp_enqueue_style(
        'mfl-city-landing',
        get_stylesheet_directory_uri() . '/assets/css/city-landing.css',
        ['mfl-child'],
        mfl_asset_ver('/assets/css/city-landing.css')
    );
});
```

- [ ] **Step 4: Lint all three files.** Expected: `No syntax errors`.
- [ ] **Step 5: Commit** `feat: city landing page template and styles`.

---

### Task 5: SEO head — title, meta, canonical, OG, JSON-LD, sitemap

**Files:**
- Modify: `wp-content/plugins/meditate-florida-core/includes/class-city-pages.php` (head output)
- Modify: `wp-content/plugins/meditate-florida-core/meditate-florida-core.php` (sitemap swap; guard existing meta fn)

**Interfaces:**
- Consumes: existing `mfl_output_meta_description()`-style `wp_head` hook (`meditate-florida-core.php` ~line 330) and `mfl_serve_sitemap()`.
- Produces: on city pages — `<title>` via `pre_get_document_title`, one meta description, canonical, OG set, one `<script type="application/ld+json">` with `@graph` of BreadcrumbList + ItemList (top 10) + FAQPage (3 Q&As mirroring template copy). Sitemap lists `mfl_city_url()` for all 20 cities instead of `?city=` URLs.

- [ ] **Step 1: Add to `register()`:** `add_filter('pre_get_document_title', [self::class, 'document_title']);` and `add_action('wp_head', [self::class, 'head_output'], 4);` (priority 4: before the existing meta fn at default 10).
- [ ] **Step 2: Implement `document_title()`** — return `'Meditation in {name}, FL — Studios, Retreats & Centers | Meditate Florida'` when `is_city_page()`, else pass through.
- [ ] **Step 3: Implement `head_output()`** — when `is_city_page()`: echo meta description (`'Find {count} meditation centers, yoga studios and retreats in {name}, FL. Ratings, hours, directions — Meditate Florida's free local directory.'`), canonical `mfl_city_url($slug)`, `og:type website`, `og:title` (same as title), `og:description`, `og:url`, `og:image` (first listing's `mfl_listing_image_url($id,'large')` if any), and the JSON-LD `@graph` (BreadcrumbList: Home → Florida Directory (`/listings/`) → {City}; ItemList of top-10 listing permalinks; FAQPage with the same three Q&As as the template, answers plain-text).
- [ ] **Step 4: Suppress the generic meta fn on city pages** — in the existing `wp_head` description function, early-return when `get_query_var('mfl_city_page')` is non-empty (prevents duplicate descriptions/OG).
- [ ] **Step 5: Swap sitemap city URLs** in `mfl_serve_sitemap()`: replace the `?city=` loop with `foreach (array_keys(MFL_City_Pages::CITIES) as $slug) { echo "<url><loc>" . esc_url(mfl_city_url($slug)) . "</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>\n"; }`.
- [ ] **Step 6: Lint both files. Commit** `feat: SEO head + sitemap for city landing pages`.

---

### Task 6: Bare `?city=` 301 + homepage city card links

**Files:**
- Modify: `wp-content/plugins/meditate-florida-core/includes/class-city-pages.php`
- Modify: `wp-content/themes/listdomer-child/page-home.php` (city card hrefs)

**Interfaces:**
- Consumes: homepage `$cities` array (name/lat/lng) — extend each row with `'slug'`; `mfl_city_url()`.
- Produces: 301 from `/listings/?city={Name}` (city the only non-empty query key) → `/meditation/{slug}/`; homepage city cards link to city pages.

- [ ] **Step 1: Add redirect to `register()`:** `add_action('template_redirect', [self::class, 'redirect_bare_city_archive']);`

```php
public static function redirect_bare_city_archive(): void
{
    if (!is_post_type_archive('listdom-listing')) return;

    $params = array_filter($_GET, fn($v) => $v !== '' && $v !== null);
    if (count($params) !== 1 || !isset($params['city'])) return;

    $name = sanitize_text_field(wp_unslash($params['city']));
    foreach (self::CITIES as $slug => $c) {
        if (strcasecmp($c['name'], $name) === 0) {
            wp_safe_redirect(mfl_city_url($slug), 301);
            exit;
        }
    }
}
```

- [ ] **Step 2: Point homepage city cards** at `mfl_city_url($city['slug'])` (add `'slug'` to each row of the `$cities` array in `page-home.php`; keep count badge query as-is). Guard with `function_exists('mfl_city_url')`.
- [ ] **Step 3: Lint both files. Commit** `feat: 301 bare city archive URLs to landing pages; homepage links`.

---

### Task 7: Deploy + production verification

**Files:** none (verification only)

- [ ] **Step 1: Push to master**; wait for the Action (watch for the CSS/route to appear).
- [ ] **Step 2: Force rules flush check** — first request may self-flush; verify `curl -s -o /dev/null -w "%{http_code}" https://meditateflorida.com/meditation/miami/` → `200` (retry once if first hit 404s before flush).
- [ ] **Step 3: All 20 cities 200:** loop the slugs with curl; expected 20 × `200`.
- [ ] **Step 4: Junk slug 404:** `/meditation/not-a-city/` → `404`.
- [ ] **Step 5: Redirect matrix:** bare `/listings/?city=Miami` → `301 → /meditation/miami/`; `/listings/?city=Miami&sort=rating` → `200` (no redirect); `/listings/?city=Miami&category=` (empty value) → `301` (empty params ignored).
- [ ] **Step 6: Content checks (Miami):** H1 contains "Miami", intro text present, ≥ 1 `mfl-listing-card`, FAQ `<details>` present, nearby chips link to `/meditation/fort-lauderdale/` etc., exactly one meta description, one canonical = city URL, JSON-LD parses (`python -c "import json,sys; json.loads(sys.stdin.read())"` on the extracted block or equivalent).
- [ ] **Step 7: Data sanity:** `wp eval 'echo count(MFL_City_Pages::get_city_listing_ids("miami"));'` on server — expected ≥ 40. `wp eval` the same for `key-west` — expected ≥ 10.
- [ ] **Step 8: Sitemap:** `curl -sL https://meditateflorida.com/sitemap-listings.xml | grep -c '/meditation/'` → `20`, and `grep -c 'city='` → `0`.
- [ ] **Step 9: Purge cache** (`wp sg purge`) and spot-check one city page in the Playwright browser at desktop + 390px widths.
- [ ] **Step 10: Update SKILL.md** (deploy section unchanged; add city pages under SEO Setup) and memory. Commit docs.

---

## Self-Review

- **Spec coverage:** routing ✓ (T2), registry+intros ✓ (T1), radius roll-up ✓ (T3), all 7 content blocks ✓ (T4), SEO head + sitemap ✓ (T5), 301s + homepage links ✓ (T6), verification incl. zero-listing/404/redirect cases ✓ (T7). Cities nav menu repointing = out of scope per spec ✓.
- **Placeholder scan:** intros are marked for authorship at implementation with explicit uniqueness requirements and a mechanical shingle check in T1 Step 3 — deliberate, testable, not a TBD. Template markup deferred to implementation but fully specified by block list + existing card pattern reference.
- **Type consistency:** `get_city_listing_ids` returns `int[]` everywhere; `CITIES` rows carry name/lat/lng/intro consistently; `mfl_city_url()` used in T4/T5/T6 matches T2's definition.
