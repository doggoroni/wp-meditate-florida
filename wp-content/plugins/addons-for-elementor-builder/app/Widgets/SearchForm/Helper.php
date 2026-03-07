<?php

namespace AFEB\Widgets\SearchForm;

use Elementor\Plugin;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" SearchForm Widget Helper Class
 * 
 * @class Helper
 * @version 1.5.0
 */
class Helper
{
    /**
     * Get SearchForm Query
     *
     * @since 1.5.0
     *
     * @return array
     */
    public function get_query($settings = [])
    {
        $args = [
            'post_status' => 'publish',
            'posts_per_page' => 5,
            'ignore_sticky_posts' => 1,
            's' => $settings['s'],
            'order' => 'DESC',
        ];

        if (!empty($settings['post_type'])) {
            $args['post_type'] = sanitize_text_field($settings['post_type']);
        }

        if (!empty($settings['orderby'])) {
            $args['orderby'] = sanitize_text_field($settings['orderby']);
        }

        if (!empty($settings['number_of_items'])) {
            $args['posts_per_page'] = intval($settings['number_of_items']);
        }

        if (!empty($settings['order'])) {
            $args['order'] = sanitize_text_field($settings['order']);
        }

        $query = new WP_Query($args);
        wp_reset_query();

        return $query;
    }

    /**
     * Print dynamic loop item
     *
     * @since 1.5.0
     *
     * @return string
     */
    public function dynamic_loop_item($document_id, $items = [])
    {
        global $wp_query;
        $default_object = $wp_query->queried_object;
        $is_content = false;

        if (count($items)) {
            $is_content = true;
            $index = 0;
            foreach ($items as $item_object) {
                if (empty($item_object))
                    continue;

                $wp_query->queried_object = $item_object;

                $content = $this->get_dynamic_item($document_id, $item_object);
                echo $content;

                $index++;
            }

            wp_reset_query();

            $wp_query->queried_object = $default_object;
        }

        return $is_content;
    }

    /**
     * Get dynamic item
     *
     * @since 1.5.0
     *
     * @return string
     */
    public function get_dynamic_item($document_id, $item_object)
    {
        if ($item_object && get_class($item_object) === 'WP_Post') {
            global $post;
            $post = $item_object;
            setup_postdata($post);
        }

        $content = Plugin::instance()->frontend->get_builder_content($document_id, false);
        return $content;
    }

    /**
     * Print Dynamic CSS
     *
     * @since 1.5.0
     *
     */
    public function print_dynamic_css($post_id, $template_id)
    {
        $document = Plugin::instance()->documents->get_doc_for_frontend($template_id);

        if (!$document) {
            return;
        }

        Plugin::instance()->documents->switch_to_document($document);

        $css_file = LoopDynamicCSS::create($post_id, $template_id);
        $post_css = $css_file->get_content();

        if (empty($post_css)) {
            return;
        }

        $style_id = 'afeb-dynamic-loop-' . sanitize_key($template_id);
        $sanitized_css = wp_strip_all_tags($post_css);

        printf(
            '<style id="%s">%s</style>',
            esc_attr($style_id),
            $sanitized_css
        );

        Plugin::instance()->documents->restore_document();
    }
}
