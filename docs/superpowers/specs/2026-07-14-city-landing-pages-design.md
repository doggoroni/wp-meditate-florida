# City Landing Pages — Design

**Date:** 2026-07-14
**Status:** Approved (design), pending implementation
**Goal:** Rankable, server-rendered landing pages for the 20 target cities at
`/meditation/{slug}/`, replacing query-string archive URLs as the SEO entry
point for "{city} meditation" queries.

## Decisions (from brainstorming)

- **Content:** hand-written ~100-word intro per city baked into code, plus
  data-driven blocks. Per-city admin override deferred (design leaves room:
  intros resolve through a filterable getter).
- **URL:** `/meditation/{slug}/` (e.g. `/meditation/miami/`).
- **Scope:** exactly the 20 importer target cities. Suburb listings roll up
  via a radius query, so no listing is orphaned.
- **Architecture:** virtual pages (rewrite rule + template), no DB entities.

## Components

### 1. `MFL_City_Pages` (new class, meditate-florida-core)

- `CITIES` registry: `slug => [name, lat, lng, intro]` for all 20 cities.
  Lat/lng seeded from the homepage city list (extend from 10 to 20).
- Rewrite rule `^meditation/([a-z-]+)/?$` → query var `mfl_city_page`.
  Rules flushed on plugin activation (bump an option-stored rules version so
  deploys that change routes self-flush).
- `template_include` filter: when `mfl_city_page` resolves to a known slug,
  load `city-landing.php` from the child theme. Unknown slug → normal 404.
- Data access for templates:
  - `get_city(string $slug): ?array`
  - `get_city_listing_ids(string $slug): int[]` — radius query against
    `{prefix}lsd_data` (haversine ≤ 25 miles from city center), published
    `listdom-listing` only, ordered by rating desc. Cached in a transient
    (12 h, keyed per city) since listings change weekly at most.
  - `get_nearby_cities(string $slug, int $n = 5): array` — closest other
    registry cities by haversine.
  - `get_city_category_counts(string $slug): array` — term => count for the
    city's listing IDs.
- Bare-city redirect: on `template_redirect`, if the request is the listing
  archive and the query string contains `city` as its **only** key (ignoring
  empty values) and the value maps to a registry city, 301 to the city page.
  Archive filter submissions always carry other keys → unaffected.
- SEO output for city pages (reusing the plugin's existing head hooks):
  - `<title>`: `Meditation in {City}, FL — Studios, Retreats & Centers | Meditate Florida`
  - Meta description: unique pattern including live count.
  - Canonical: the city page URL.
  - OG tags: type website, title, description, image = top listing's image.
  - JSON-LD: `BreadcrumbList` (Home → Listings → City), `ItemList` of the
    top listings, `FAQPage` matching the rendered FAQ block.
- Sitemap: `mfl_serve_sitemap()` lists the 20 city URLs instead of
  `?city=` query URLs.

### 2. `city-landing.php` (new child-theme template)

Blocks, in order (reusing existing card markup/CSS where possible):

1. Hero: breadcrumb, H1 `Meditation & Wellness in {City}, FL`, count line.
2. Intro: hand-written paragraph + one generated sentence (count + category
   mix, e.g. "The directory currently lists 52 places around Miami —
   yoga studios, Buddhist centers, wellness retreats and more.").
3. Category chips with counts → `/listings/?city={City}&category={id}`.
4. "Top Rated in {City}": top 6 by rating, card grid.
5. All listings grid: up to 24, then a "View all {count} in the directory"
   link → `/listings/?city={City}&sort=rating` (extra param avoids the 301).
6. FAQ block (3–4 Q&As, data-driven answers), matches FAQPage schema.
7. Nearby cities: link chips to the 4–5 closest city pages.

New CSS lives in `assets/css/city-landing.css`, enqueued only for city pages,
versioned via `mfl_asset_ver()`.

### 3. Internal link updates

- Homepage city cards (`page-home.php`) link to `/meditation/{slug}/`.
- Cities nav menu (DB-managed) keeps working via the 301; user may repoint
  menu items in wp-admin later.

## Error handling

- Unknown city slug → 404 (no soft-200s).
- City with zero listings in radius → page still renders (intro + nearby
  cities + link to directory); count line says "coming soon". No fatal on
  empty data.
- Transient failures degrade to live queries.

## Testing / verification

- PHP lint all touched files.
- If LocalWP is running: verify locally first.
- Post-deploy production checks: all 20 URLs return 200 with correct H1;
  junk slug 404s; bare `?city=Miami` 301s to `/meditation/miami/`; archive
  filtering with a city still works (no redirect); sitemap lists the new
  URLs; schema validates (well-formed JSON-LD); cache purged.

## Out of scope (later)

- Per-city admin override UI for intros (getter is filterable now).
- Category × city combo pages (`/meditation/miami/yoga-studios/`).
- Map embed on city pages.
- Repointing the wp-admin Cities menu items.
