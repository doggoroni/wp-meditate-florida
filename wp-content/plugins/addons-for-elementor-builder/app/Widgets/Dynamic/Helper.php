<?php

namespace AFEB\Widgets\Dynamic;

use AFEB\Helper as AFEBHelper;
use DOMDocument;
use Elementor\Plugin;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" DynamicGrid Widget Helper Class
 *
 * @class Helper
 * @version 1.3.0
 */
class Helper
{
    /**
     * @var Widget_Base
     */
    public $widget;

    /**
     * @var DOMDocument
     */
    private $dom;

    public function __construct($widget)
    {
        $this->widget = $widget;
    }

    /**
     * Renders the dynamic grid carousel content based on the provided settings.
     *
     * @param array $settings The widget settings array.
     */
    public function render(array $settings): void
    {
        $document_id = intval($settings['dgrd_tmp'] ?? 0);

        // Initial check and display error message
        $error_msg = $this->get_template_error_message($document_id);
        if (!empty($error_msg)) {
            echo '<div ' . $this->widget->render_attrs($settings) . '>';
            echo wp_kses(AFEBHelper::front_notice($error_msg, 'error'), AFEBHelper::allowed_tags(['div']));
            echo '</div>';
            return;
        }

        // Handle carousel settings
        $carousel_data = '';
        if ($settings['dgrd_lyt'] === 'carousel') {
            $slick_options = $this->get_carousel_slick_options($settings);
            $this->widget->add_render_attribute('dynamic_carousel', 'data-slick', wp_json_encode($slick_options));
            $carousel_data = $this->widget->get_render_attribute_string('dynamic_carousel');
        }

        // Prepare WP_Query arguments
        $args = $this->prepare_wp_query_args($settings);

        // Execute WP_Query
        $query = new WP_Query($args);
        Plugin::instance()->db->switch_to_query($args);

        echo '<div ' . $this->widget->render_attrs($settings) . '>'; // Start main container
        $this->render_grid_filters($settings);

        if ($query->have_posts()) {
            $this->dom = new DOMDocument();

            echo '<div class="afeb-dynamic-grid-carousel-items targets"' . $carousel_data . '>';

            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $content = Plugin::instance()->frontend->get_builder_content($document_id, true);

                if (!empty($content)) {
                    // Sanitize and manipulate item content DOM
                    $processed_content = $this->process_item_content($content, $post_id);

                    // Get the default allowed tags for 'post' context
                    $allowed_tags = wp_kses_allowed_html('post');

                    // **Allow the <style> tag**
                    $allowed_tags['style'] = [];

                    // Ensure final output is sanitized
                    echo wp_kses($processed_content, $allowed_tags);
                }
            }

            echo '</div>'; // End targets

            // Handle pagination
            $this->render_pagination($settings, $query);

            wp_reset_postdata();
        } else {
            // Display "No items found" message
            $no_item_msg = !empty($settings['dgrd_msg_nth_fnd']) ?
                $settings['dgrd_msg_nth_fnd'] : esc_html__('There is no item to display', 'addons-for-elementor-builder');
            echo wp_kses(AFEBHelper::front_notice($no_item_msg, 'error'), AFEBHelper::allowed_tags(['div']));
        }

        Plugin::instance()->db->restore_current_query();

        echo '</div>'; // End main container
    }

    /**
     * Checks the template status and returns an error message if issues exist.
     *
     * @param int $document_id The ID of the template.
     * @return string The error message or an empty string if no errors are found.
     */
    protected function get_template_error_message(int $document_id): string
    {
        if ($document_id === 0) {
            return esc_html__('Please select a template to continue.', 'addons-for-elementor-builder');
        }

        $document_status = get_post_field('post_status', $document_id);
        if ($document_status !== 'publish') {
            return esc_html__('This template is not available. Please use another template to continue.', 'addons-for-elementor-builder');
        }
        return '';
    }

    /**
     * Returns the Slick Carousel options based on widget settings.
     *
     * @param array $settings The widget settings.
     * @return array An array of Slick options.
     */
    protected function get_carousel_slick_options(array $settings): array
    {
        return [
            'adaptiveHeight' => false,
            'arrows'         => !empty($settings['dgrd_sldr_arrows']),
            'autoplay'       => !empty($settings['dgrd_sldr_ato_ply']),
            'autoplaySpeed'  => intval($settings['dgrd_sldr_ato_ply_spd'] ?? 2000),
            'centerMode'     => false,
            'dots'           => ($settings['dgrd_sldr_dots'] ?? '') === 'dots',
            'infinite'       => !empty($settings['dgrd_sldr_infnt_scrl']),
            'nextArrow'      => '<i class="slick-next ' . esc_attr($settings['dgrd_sldr_arr_nxt_ic']['value'] ?? '') . '"></i>',
            'pauseOnHover'   => !empty($settings['dgrd_sldr_puse_on_hvr']),
            'prevArrow'      => '<i class="slick-prev ' . esc_attr($settings['dgrd_sldr_arr_prv_ic']['value'] ?? '') . '"></i>',
            'responsive'     => [
                [
                    'breakpoint' => 769,
                    'settings'   => [
                        'slidesToShow' => !empty($settings['dgrd_sldr_on_dsply_tablet']['size']) ? intval($settings['dgrd_sldr_on_dsply_tablet']['size']) : 2,
                        'slidesToScroll' => !empty($settings['dgrd_sldr_on_scrl_tablet']['size']) ? intval($settings['dgrd_sldr_on_scrl_tablet']['size']) : 1,
                        'touchMove'      => true
                    ]
                ],
                [
                    'breakpoint' => 481,
                    'settings'   => [
                        'slidesToShow' => !empty($settings['dgrd_sldr_on_dsply_mobile']['size']) ? intval($settings['dgrd_sldr_on_dsply_mobile']['size']) : 1,
                        'slidesToScroll' => !empty($settings['dgrd_sldr_on_scrl_mobile']['size']) ? intval($settings['dgrd_sldr_on_scrl_mobile']['size']) : 1,
                        'touchMove'      => true
                    ]
                ]
            ],
            'slidesToShow' => !empty($settings['dgrd_sldr_on_dsply']['size']) ? intval($settings['dgrd_sldr_on_dsply']['size']) : 3,
            'slidesToScroll' => !empty($settings['dgrd_sldr_on_scrl']['size']) ? intval($settings['dgrd_sldr_on_scrl']['size']) : 1,
        ];
    }

    /**
     * Prepares the WP_Query arguments based on widget settings.
     *
     * @param array $settings The widget settings.
     * @return array WP_Query arguments.
     */
    protected function prepare_wp_query_args(array $settings): array
    {
        $layout = $settings['dgrd_lyt'] ?? '';
        $orderby = in_array($settings['dgrd_ordrby'] ?? '', ['title', 'ID', 'date', 'author', 'comment_count', 'rand', 'modified', 'parent']) ? $settings['dgrd_ordrby'] : 'date';
        $order = in_array(strtoupper($settings['dgrd_ordr'] ?? 'DESC'), ['ASC', 'DESC']) ? strtoupper($settings['dgrd_ordr']) : 'DESC';

        $posts_per_page = intval($layout === 'carousel' ?
            ($settings['dgrd_sldr_num_of_sldr'] ?? -1) : ($settings['dgrd_itm_pr_pge'] ?? -1));

        $args = [
            'post_status'         => 'publish',
            'post_type'           => ($settings['dgrd_src'] === 'pages') ? 'page' : 'post',
            'posts_per_page'      => $posts_per_page,
            'orderby'             => $orderby,
            'order'               => $order,
            'paged'               => max(1, get_query_var('paged') ?: get_query_var('page')),
            'ignore_sticky_posts' => 1,
        ];

        $args['post__not_in'] = array_merge($args['post__not_in'] ?? [], [get_the_ID()]);

        if (isset($settings['dgrd_slct']) && $settings['dgrd_slct'] === 'manual') {
            // Ensure all post IDs are integers for security.
            $suffix = ['post' => 'pst', 'page' => 'pge'];
            $args['post__in'] = !empty($settings['dgrd_slct_' . $suffix[$args['post_type']]]) ? array_map('intval', $settings['dgrd_slct_' . $suffix[$args['post_type']]]) : [-1];
        }

        $tax_query = $this->build_tax_query($settings);
        if ($tax_query) {
            $args['tax_query'] = $tax_query;
        }

        // Merge with the main WP_Query on archive pages
        if ($settings['dgrd_src'] === 'archive') {
            global $wp_query;
            $args = wp_parse_args($args, $wp_query->query_vars);
        }

        // Build a custom query using the provided settings
        if (!in_array($settings['dgrd_src'], ['posts', 'pages', 'archive'], true)) {
            $args = array_merge($args, $this->build_custom_post_type_query($settings));
        }

        return $args;
    }

    private function build_tax_query($settings)
    {
        $tax_query = [];
        foreach (['cat_include' => 'category', 'cat_exclude' => 'category', 'tag_include' => 'post_tag', 'tag_exclude' => 'post_tag'] as $key => $taxonomy) {
            if (!empty($settings['dgrd_' . $key])) {
                $terms = is_array($settings['dgrd_' . $key]) ? $settings['dgrd_' . $key] : explode(',', str_replace(', ', ',', $settings['dgrd_' . $key]));
                $terms = map_deep($terms, 'intval');
                $tax_query[] = [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $terms,
                    'operator' => ($key === 'cat_exclude' || $key === 'tag_exclude') ? 'NOT IN' : 'IN',
                ];
            }
        }

        return $tax_query;
    }

    public function build_custom_post_type_query($settings = [])
    {
        return [];
    }

    /**
     * Processes the item content (e.g., adding CSS classes).
     *
     * @param string $content The HTML content of the item.
     * @param int $post_id The ID of the current post.
     * @return string The processed and sanitized HTML content.
     */
    protected function process_item_content(string $content, int $post_id): string
    {
        // Use DOMDocument only for structural changes and always sanitize the output.
        // While Elementor's get_builder_content is generally safe, any DOM manipulation
        // necessitates re-sanitizing the final output for maximum security.
        $new_class = implode(' ', get_post_class('afeb-dynamic-grid-carousel-item mix', $post_id));

        // Suppress XML errors to prevent breaking on malformed HTML, but be aware of potential hidden issues.
        libxml_use_internal_errors(true);
        // Use LIBXML_HTML_NOIMPLIED and LIBXML_HTML_NODEFDTD to prevent DOMDocument from adding <html>, <body>, <!DOCTYPE> tags.
        $this->dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $divs = $this->dom->getElementsByTagName('div');
        if ($divs->length > 0) {
            $first_div = $divs->item(0);
            $current_class = $first_div->getAttribute('class');
            $updated_class = trim($current_class . ' ' . $new_class);
            $first_div->setAttribute('class', esc_attr($updated_class));
        }
        $output = $this->dom->saveHTML();

        // Remove any remaining HTML/BODY/DOCTYPE tags that DOMDocument might have added,
        // although LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD should minimize this.
        // The final wp_kses_post will handle allowed tags anyway.
        $output = preg_replace('/<!DOCTYPE.+?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $output));

        return $output;
    }

    /**
     * Renders the pagination for the query.
     *
     * @param array $settings The widget settings.
     * @param WP_Query $query The WP_Query object.
     */
    protected function render_pagination(array $settings, WP_Query $query): void
    {
        if (empty($settings['dgrd_pge'])) {
            return;
        }

        global $wp_rewrite;

        // Sanitize GET parameters for security
        $current_query_args = map_deep($_GET, 'sanitize_text_field');

        // Remove 'paged' from current query arguments if it exists
        unset($current_query_args['paged'], $current_query_args['page']);

        if ($wp_rewrite instanceof \WP_Rewrite && $wp_rewrite->using_permalinks())
        {
            $first_page_link = get_pagenum_link(1);

            $parsed_url = wp_parse_url(remove_query_arg('paged', $first_page_link));

            $base = $parsed_url['scheme'] . '://' . $parsed_url['host'];

            if (!empty($parsed_url['port'])) {
                $base .= ':' . $parsed_url['port'];
            }

            if (!empty($parsed_url['path'])) {
                $base .= trailingslashit($parsed_url['path']);
            } else {
                $base .= '/';
            }

            $base = esc_url($base . '%_%');

            $format = 'page/%#%/';
        }
        else
        {
            $base   = str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999)));
            $format = '?paged=%#%';
        }

        $current_page = max(1, get_query_var('paged') ?: get_query_var('page') ?: 1);

        $pagination_args = [
            'base'      => $base,
            'format'    => $format,
            'total'     => intval($query->max_num_pages),
            'current'   => $current_page,
            'prev_text' => $settings['dgrd_pge_prv_lbl'] ?? esc_html__('Previous', 'addons-for-elementor-builder'),
            'next_text' => $settings['dgrd_pge_nxt_lbl'] ?? esc_html__('Next', 'addons-for-elementor-builder'),
            'add_args'  => $current_query_args,
        ];

        $is_ajax = $this->is_ajax_pagination_enabled($settings);
        $pagination_attributes = $is_ajax ? ' data-pagination-type="' . esc_attr($settings['dgrd_pge']) . '"' : '';

        switch ($settings['dgrd_pge']) {
            case 'numbers':
                $pagination_args['prev_next'] = false;
            case 'numbers_and_prev_next':
                $pagination_args['mid_size'] = empty($settings['dgrd_pge_shrtn']) ? 99999 : 2;
                $paginate_links = paginate_links($pagination_args);
                if ($is_ajax) {
                    $paginate_links = $this->add_ajax_attributes_to_links($paginate_links);
                }
                $classes = ['afeb-dynamic-grid-pagination'];
                if ($is_ajax) {
                    $classes[] = 'afeb-dynamic-grid-pagination--ajax';
                }
                // Sanitize pagination links as they contain HTML
                echo $paginate_links ? '<div class="' . esc_attr(implode(' ', $classes)) . '"' . $pagination_attributes . '>' . wp_kses_post($paginate_links) . '</div>' : '';
                break;

            case 'prev_next':
                $pagination_args['type'] = 'array';
                $paginate_links = paginate_links($pagination_args);

                if ($paginate_links && is_array($paginate_links)) {
                    $classes = ['afeb-dynamic-grid-pagination', 'afeb-prev-next'];
                    if ($is_ajax) {
                        $classes[] = 'afeb-dynamic-grid-pagination--ajax';
                    }
                    echo '<div class="' . esc_attr(implode(' ', $classes)) . '"' . $pagination_attributes . '>';
                    foreach ($paginate_links as $link) {
                        if ($is_ajax) {
                            $link = $this->add_ajax_attributes_to_links($link);
                        }
                        if (strpos($link, 'prev page-numbers') !== false || strpos($link, 'next page-numbers') !== false) {
                            // Sanitize individual pagination links
                            echo wp_kses_post($link);
                        }
                    }
                    echo '</div>';
                }
                break;
        }

        $this->render_ajax_pagination($settings, $query);
    }

    public function render_grid_filters($settings) {}

    public function render_ajax_pagination($settings, WP_Query $query)
    {
        if (is_object($this->widget) && method_exists($this->widget, 'render_ajax_pagination')) {
            $this->widget->render_ajax_pagination($settings, $query);
        }
    }

    protected function is_ajax_pagination_enabled(array $settings): bool
    {
        if (empty($settings['dgrd_pge'])) {
            return false;
        }

        if ($settings['dgrd_pge'] === 'load_on_click') {
            return true;
        }

        return !empty($settings['ajax_pagination']) && $settings['ajax_pagination'] === 'yes';
    }

    protected function add_ajax_attributes_to_links($links)
    {
        if (empty($links)) {
            return $links;
        }

        $callback = function ($matches) {
            $attributes = $matches[1];
            $content = $matches[2];

            if (!preg_match('/href=("|\')(.*?)\1/i', $attributes, $href_match)) {
                return $matches[0];
            }

            $href = $href_match[2];
            $page = $this->get_page_number_from_url($href);

            $attributes = preg_replace('/\sdata-page=("|\').*?\1/i', '', $attributes);
            $attributes = rtrim($attributes);
            $attributes .= ' data-page="' . intval($page) . '"';

            return '<a' . $attributes . '>' . $content . '</a>';
        };

        return preg_replace_callback('/<a([^>]*)>(.*?)<\/a>/i', $callback, $links);
    }

    protected function get_page_number_from_url($url)
    {
        if (empty($url)) {
            return 1;
        }

        if (preg_match('/[?&]paged=(\d+)/', $url, $matches)) {
            return intval($matches[1]);
        }

        if (preg_match('#/page/(\d+)/?#', $url, $matches)) {
            return intval($matches[1]);
        }

        return 1;
    }
}
