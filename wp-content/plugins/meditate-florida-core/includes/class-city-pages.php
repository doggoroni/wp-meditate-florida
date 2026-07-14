<?php
/**
 * MFL_City_Pages — virtual SEO landing pages at /meditation/{slug}/.
 *
 * Registry-driven: the 20 importer target cities live in code (no DB
 * entities), each with coordinates and a hand-written intro. Listings are
 * matched by radius so suburbs roll up into their metro's page.
 *
 * Design: docs/superpowers/specs/2026-07-14-city-landing-pages-design.md
 */

defined('ABSPATH') || exit;

class MFL_City_Pages
{
    const QUERY_VAR    = 'mfl_city_page';
    const RADIUS_MILES = 25;
    const RULES_VER    = 1; // bump when rewrite rules change

    const CITIES = [
        'miami' => [
            'name' => 'Miami', 'lat' => 25.7617, 'lng' => -80.1918,
            'intro' => 'Miami wears its energy on its sleeve, which makes the city\'s quieter corners feel like genuine discoveries. From Buddhist meditation centers in Coral Gables to beachfront sunrise sits on South Beach and yoga collectives tucked between Wynwood\'s murals, Greater Miami offers more places to slow down than any other metro in Florida. Many studios teach in both English and Spanish, and several centers run free introductory nights — an easy first step if you\'ve never meditated before.',
        ],
        'orlando' => [
            'name' => 'Orlando', 'lat' => 28.5384, 'lng' => -81.3789,
            'intro' => 'Beyond the theme parks, Orlando has grown a surprisingly deep mindfulness community. Meditation centers cluster around Winter Park and College Park, lakeside yoga studios make use of the city\'s hundred-plus lakes, and several Buddhist temples in the suburbs welcome first-time visitors to weekly open sits. For locals and visitors alike, it\'s one of the easiest places in Central Florida to build a steady practice between everything else the city demands.',
        ],
        'tampa' => [
            'name' => 'Tampa', 'lat' => 27.9506, 'lng' => -82.4572,
            'intro' => 'Tampa\'s meditation scene stretches from restored bungalows in Seminole Heights to waterfront studios along Bayshore Boulevard, where the sidewalk itself doubles as a walking-meditation route at sunset. The city mixes traditional lineages — Zen and Thai Buddhist centers among them — with modern mindfulness studios and breathwork spaces in Hyde Park and Ybor City. Cross the bay and the options double, but Tampa proper has plenty to anchor a daily practice.',
        ],
        'jacksonville' => [
            'name' => 'Jacksonville', 'lat' => 30.3322, 'lng' => -81.6557,
            'intro' => 'As Florida\'s largest city by area, Jacksonville spreads its contemplative spaces wide: riverside meditation groups downtown, yoga studios in Riverside and San Marco\'s historic blocks, and retreat-style centers out toward the beaches. The Atlantic coastline at Jax Beach and Neptune Beach gives early risers a natural setting for silent sits, while established centers in town run structured courses in mindfulness, Zen, and insight meditation year-round.',
        ],
        'fort-lauderdale' => [
            'name' => 'Fort Lauderdale', 'lat' => 26.1224, 'lng' => -80.1373,
            'intro' => 'Fort Lauderdale pairs canal-side calm with a wellness scene that has matured well beyond the beach clubs. Meditation and plant-medicine retreat centers operate in quiet residential pockets, Las Olas hosts boutique yoga and breathwork studios, and Wilton Manors has become a hub for community-led mindfulness groups. With Pompano and Hollywood minutes away, the greater Fort Lauderdale area supports every style of practice from silent retreat weekends to drop-in lunchtime sits.',
        ],
        'tallahassee' => [
            'name' => 'Tallahassee', 'lat' => 30.4383, 'lng' => -84.2807,
            'intro' => 'Tallahassee\'s canopy roads and university rhythm give Florida\'s capital an unhurried undercurrent that suits meditation well. Student-friendly mindfulness groups meet near FSU and FAMU, longstanding centers in Midtown teach structured courses, and the live oaks of Maclay Gardens offer a natural cathedral for walking practice. It\'s a smaller scene than the coastal metros, but a tight-knit one — teachers here tend to know each other, and newcomers get welcomed quickly.',
        ],
        'gainesville' => [
            'name' => 'Gainesville', 'lat' => 29.6516, 'lng' => -82.3248,
            'intro' => 'Home to the University of Florida, Gainesville punches far above its size for contemplative practice. The city supports Zen groups, insight meditation communities, and donation-based yoga collectives, many run by teachers who\'ve practiced for decades. Paynes Prairie and the city\'s famous springs country add a wild, green backdrop for retreat days. If you want a college town where the meditation community is genuinely rooted rather than trendy, Gainesville is it.',
        ],
        'sarasota' => [
            'name' => 'Sarasota', 'lat' => 27.3364, 'lng' => -82.5307,
            'intro' => 'Sarasota may be the most naturally meditative city on the Gulf coast — Siesta Key\'s powder-quartz sand stays cool underfoot even at noon, and sunset drum circles have been a local ritual for decades. The city anchors a serious practice community too, led by its Kadampa Buddhist center and complemented by yoga studios downtown and wellness spaces on St. Armands. Retirees, artists, and snowbirds mix freely in classes here, and beginners are the norm rather than the exception.',
        ],
        'naples' => [
            'name' => 'Naples', 'lat' => 26.1420, 'lng' => -81.7948,
            'intro' => 'Naples brings a polished, unhurried take on wellness: Gulf-front sunrise meditation, upscale spa-and-mindfulness hybrids near Fifth Avenue South, and quiet Buddhist and meditation centers serving a community with time to practice deeply. The Everglades\' western edge sits minutes away, giving retreat programs here access to genuine wilderness silence. Classes skew small and personal, and many teachers offer one-on-one instruction — a good fit if you prefer depth over drop-in variety.',
        ],
        'st-augustine' => [
            'name' => 'St. Augustine', 'lat' => 29.9012, 'lng' => -81.3124,
            'intro' => 'The oldest city in the country knows something about stillness. St. Augustine\'s historic district — all coquina walls and gas lamps — lends itself to contemplative walking, and the town\'s meditation and yoga spaces lean into that old-world pace. Practitioners here gather in converted historic homes, on Anastasia Island\'s beaches, and at retreat centers just outside town. It\'s a compact scene with real character, and an easy day trip from Jacksonville for retreat weekends.',
        ],
        'key-west' => [
            'name' => 'Key West', 'lat' => 24.5551, 'lng' => -81.7800,
            'intro' => 'Key West runs on island time, which is halfway to meditation already. The southernmost city offers sunrise sits at the Southernmost Point before the crowds arrive, yoga on Higgs Beach, and small studios tucked into Old Town\'s lanes where roosters provide the ambient soundtrack. The nightly sunset celebration at Mallory Square is its own kind of communal mindfulness. Practice here is informal, friendly, and unbothered — exactly what you\'d expect at mile zero.',
        ],
        'boca-raton' => [
            'name' => 'Boca Raton', 'lat' => 26.3683, 'lng' => -80.1289,
            'intro' => 'Boca Raton\'s manicured calm extends naturally into its wellness scene. Meditation centers and mindfulness studios cluster near Mizner Park and along Federal Highway, several with strong programs for stress reduction and mindful aging. The Gumbo Limbo nature center and Red Reef Park give practitioners ocean-and-hammock settings for outdoor sits. Between Palm Beach County\'s teachers and Broward\'s scene just south, Boca residents have unusually rich options within a short drive.',
        ],
        'west-palm-beach' => [
            'name' => 'West Palm Beach', 'lat' => 26.7153, 'lng' => -80.0534,
            'intro' => 'West Palm Beach balances its growing downtown with an established contemplative core: meditation centers in Northwood\'s arts district, waterfront yoga along the Intracoastal with Palm Beach\'s skyline across the water, and wellness studios threaded through Antique Row. The city\'s Mounts Botanical Garden hosts regular mindfulness walks. It\'s an accessible scene — less exclusive than the island across the bridge — with steady weekly sits that welcome whoever shows up.',
        ],
        'clearwater' => [
            'name' => 'Clearwater', 'lat' => 27.9659, 'lng' => -82.8001,
            'intro' => 'Clearwater\'s claim to fame is a beach routinely ranked among America\'s best, and its wellness community makes full use of it — sunrise beach meditation and paddleboard yoga are practically municipal traditions. Inland, studios in Countryside and along the Pinellas Trail teach everything from mindfulness basics to longer meditation courses. With Dunedin\'s artsy calm just north and St. Pete\'s scene to the south, Clearwater sits in the middle of one of Florida\'s densest wellness corridors.',
        ],
        'st-petersburg' => [
            'name' => 'St. Petersburg', 'lat' => 27.7676, 'lng' => -82.6403,
            'intro' => 'The Sunshine City pairs its arts renaissance with one of Florida\'s liveliest mindfulness scenes. Meditation studios and Buddhist centers dot the Grand Central and EDGE districts, donation-based community sits meet in Crescent Lake\'s shade, and the downtown waterfront parks — seven miles of them, unbroken — make walking meditation a daily option. St. Pete\'s creative, slightly bohemian energy keeps classes unpretentious, and the density of studios means there\'s a sit within reach most hours of the day.',
        ],
        'pensacola' => [
            'name' => 'Pensacola', 'lat' => 30.4213, 'lng' => -87.2169,
            'intro' => 'On Florida\'s far western edge, Pensacola blends Gulf Coast ease with Southern warmth. The emerald water and sugar-white dunes of Pensacola Beach set the scene for sunrise meditation, while downtown\'s Palafox district hosts yoga studios and mindfulness groups in restored historic storefronts. The military community here has helped grow strong trauma-informed and veteran-focused meditation programs — a distinctive strength of the local scene that\'s hard to find elsewhere in the state.',
        ],
        'daytona-beach' => [
            'name' => 'Daytona Beach', 'lat' => 29.2108, 'lng' => -81.0228,
            'intro' => 'Famous for speed, Daytona Beach rewards those who slow down. Twenty-three miles of hard-packed sand make for effortless walking meditation, and the local wellness community gathers at beachside yoga studios, riverside parks along the Halifax, and meditation groups in Ormond Beach just north. The scene here is unpretentious and affordable — drop-in classes rarely break twenty dollars — making it one of the easiest places in Florida to try a practice on for size.',
        ],
        'fort-myers' => [
            'name' => 'Fort Myers', 'lat' => 26.6406, 'lng' => -81.8723,
            'intro' => 'Fort Myers anchors Southwest Florida\'s quieter wellness corridor. The revitalized River District hosts yoga and meditation studios in century-old brick buildings, while the beaches of Estero Island and Sanibel\'s shell-strewn shores — a short causeway away — offer natural settings for contemplative mornings. Edison and Ford\'s old winter estates set a fitting tone: this has long been where people come to restore. Local centers run strong seasonal programs timed to the winter influx.',
        ],
        'ocala' => [
            'name' => 'Ocala', 'lat' => 29.1872, 'lng' => -82.1401,
            'intro' => 'Horse country slows everyone down eventually. Ocala\'s rolling pastures and the crystalline springs of the surrounding national forest make it a natural retreat setting, and the local scene leans that way — meditation gatherings at Silver Springs, yoga studios in the historic downtown square, and retreat centers on quiet acreage outside town. It\'s the smallest scene among Florida\'s meditation hubs but among the most grounded, with nature doing half the teaching.',
        ],
        'delray-beach' => [
            'name' => 'Delray Beach', 'lat' => 26.4615, 'lng' => -80.0728,
            'intro' => 'Delray Beach has quietly become one of South Florida\'s wellness capitals. Atlantic Avenue\'s buzz gives way within blocks to meditation studios, recovery-focused mindfulness programs — the town is a national hub for them — and the serene grounds of the Morikami Museum\'s Japanese gardens, arguably the most meditative public space in the state. Beach yoga at sunrise is a fixture. The scene is walkable, social, and welcoming to beginners and long-time practitioners alike.',
        ],
    ];

    // ─── Routing ─────────────────────────────────────────────────────────────

    public static function register(): void
    {
        add_action('init',             [self::class, 'add_rewrite']);
        add_filter('query_vars',       [self::class, 'add_query_var']);
        add_filter('template_include', [self::class, 'load_template']);
        add_action('wp',               [self::class, 'maybe_flush_rules']);
    }

    public static function add_rewrite(): void
    {
        add_rewrite_rule(
            '^meditation/([a-z0-9-]+)/?$',
            'index.php?' . self::QUERY_VAR . '=$matches[1]',
            'top'
        );
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
        if ($slug === '') {
            return $template;
        }

        if (!isset(self::CITIES[$slug])) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            return get_404_template() ?: $template;
        }

        $t = locate_template('city-landing.php');
        return $t ?: $template;
    }

    /**
     * Self-flush rewrite rules once per RULES_VER bump — deploys rsync files
     * and never fire activation hooks, so routes must flush themselves.
     */
    public static function maybe_flush_rules(): void
    {
        if ((int) get_option('mfl_city_rules_ver') !== self::RULES_VER) {
            flush_rewrite_rules(false);
            update_option('mfl_city_rules_ver', self::RULES_VER);
        }
    }

    // ─── Registry access ─────────────────────────────────────────────────────

    public static function get_city(string $slug): ?array
    {
        if (!isset(self::CITIES[$slug])) {
            return null;
        }
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

    /** Closest other registry cities, ascending by distance. */
    public static function get_nearby_cities(string $slug, int $n = 5): array
    {
        $me = self::CITIES[$slug] ?? null;
        if (!$me) {
            return [];
        }

        $out = [];
        foreach (self::CITIES as $other_slug => $c) {
            if ($other_slug === $slug) {
                continue;
            }
            $out[] = [
                'slug'           => $other_slug,
                'name'           => $c['name'],
                'distance_miles' => self::haversine_miles($me['lat'], $me['lng'], $c['lat'], $c['lng']),
            ];
        }

        usort($out, fn($a, $b) => $a['distance_miles'] <=> $b['distance_miles']);
        return array_slice($out, 0, $n);
    }
}
