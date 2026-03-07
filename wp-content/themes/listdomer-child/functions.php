<?php
/**
 * Listdomer Child — Meditate Florida
 * functions.php: enqueue fonts + styles, card enhancements, sticky header JS.
 */

defined('ABSPATH') || exit;

// ─── Enqueue Google Fonts + parent + child stylesheets ────────────────────────

add_action('wp_enqueue_scripts', 'mfl_child_enqueue_styles');

function mfl_child_enqueue_styles(): void
{
    // 1. Google Fonts: Playfair Display (headings) + Lato (body)
    wp_enqueue_style(
        'mfl-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap',
        [],
        null
    );

    // 2. Parent stylesheet
    wp_enqueue_style(
        'listdomer-parent',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme('listdomer')->get('Version')
    );

    // 3. Parent assets stylesheet (where actual CSS lives in the parent)
    wp_enqueue_style(
        'listdomer-parent-assets',
        get_template_directory_uri() . '/assets/css/styles.css',
        ['listdomer-parent'],
        wp_get_theme('listdomer')->get('Version')
    );

    // 4. Child stylesheet (our overrides — must load LAST)
    wp_enqueue_style(
        'mfl-child',
        get_stylesheet_uri(),
        ['listdomer-parent-assets'],
        wp_get_theme()->get('Version')
    );
}

// ─── Sticky header + mobile search toggle (inline JS, < 1 KB) ────────────────

add_action('wp_footer', 'mfl_child_footer_scripts');

function mfl_child_footer_scripts(): void
{
    ?>
    <script>
    (function () {
        'use strict';

        // ── Sticky header scroll shadow ──────────────────────────────────────
        var header = document.getElementById('masthead');
        if (header) {
            var onScroll = function () {
                header.classList.toggle('is-scrolled', window.scrollY > 10);
            };
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll(); // run once on load
        }

        // ── Mobile search toggle ─────────────────────────────────────────────
        var searchToggle = document.getElementById('mfl-search-toggle');
        var searchBar    = document.getElementById('mfl-header-search');
        if (searchToggle && searchBar) {
            searchToggle.addEventListener('click', function () {
                var open = searchBar.classList.toggle('is-open');
                searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) {
                    var input = searchBar.querySelector('input');
                    if (input) input.focus();
                }
            });
        }

        // ── Listing cards: inject rating stars from meta ─────────────────────
        // Listdom renders cards; we look for data-mfl-rating on card wrappers
        // (set by child theme's card template hook) and inject star HTML.
        document.querySelectorAll('.lsd-listing-item[data-mfl-rating]').forEach(function (card) {
            var rating = parseFloat(card.dataset.mflRating || '0');
            var count  = card.dataset.mflCount || '';
            if (!rating) return;

            var body = card.querySelector('.lsd-listing-body');
            if (!body) return;

            // Build star string (filled ★ vs empty ☆)
            var stars = '';
            for (var i = 1; i <= 5; i++) {
                stars += i <= Math.round(rating) ? '★' : '☆';
            }

            var row = document.createElement('div');
            row.className = 'mfl-rating-row';
            row.innerHTML =
                '<span class="mfl-stars" aria-label="' + rating.toFixed(1) + ' out of 5 stars">' + stars + '</span>' +
                (count ? '<span class="mfl-rating-count">(' + count + ' reviews)</span>' : '');

            // Insert after first child (title) of body
            var title = body.querySelector('.lsd-listing-title, .lsd-item-title, .entry-title');
            if (title && title.nextSibling) {
                body.insertBefore(row, title.nextSibling);
            } else {
                body.appendChild(row);
            }
        });
    }());
    </script>
    <?php
}

// ─── Inject rating data attributes onto listing card wrappers ─────────────────
// Listdom renders cards via its shortcode system. We hook into `the_posts` filter
// to prime a lookup table, then use `post_class` to add data attributes.

add_filter('post_class', 'mfl_listing_card_data_attrs', 20, 3);

function mfl_listing_card_data_attrs(array $classes, $class, $post_id): array
{
    if (get_post_type($post_id) !== 'listdom-listing') {
        return $classes;
    }

    // Data attributes can't be added via post_class, so we use a footer hook
    // to output a small JS object that the inline script above can consume.
    // Instead, we output data directly on the wrapper via a later Listdom hook.
    return $classes;
}

// Output a JS lookup table of rating data for all listing posts on the page.
add_action('wp_footer', 'mfl_output_rating_data', 5);

function mfl_output_rating_data(): void
{
    global $wp_query;

    // Only on pages that have listings
    if (empty($wp_query->posts)) return;

    $data = [];

    foreach ($wp_query->posts as $post) {
        if ($post->post_type !== 'listdom-listing') continue;

        $rating = get_post_meta($post->ID, '_mfl_rating', true);
        $count  = get_post_meta($post->ID, '_mfl_review_count', true);

        if ($rating) {
            $data[$post->ID] = [
                'rating' => (float) $rating,
                'count'  => (int) $count,
            ];
        }
    }

    if (empty($data)) return;

    echo '<script>window.mflListingRatings = ' . wp_json_encode($data) . ';</script>' . PHP_EOL;
}

// ─── Register newsletter widget area ─────────────────────────────────────────

add_action('widgets_init', 'mfl_register_sidebars');

function mfl_register_sidebars(): void
{
    register_sidebar([
        'name'          => __('Newsletter Band', 'listdomer-child'),
        'id'            => 'mfl-newsletter',
        'description'   => __('Replaces the default newsletter signup form in the footer. Leave empty to show the built-in placeholder form.', 'listdomer-child'),
        'before_widget' => '<div class="mfl-newsletter-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ]);
}
