<?php

namespace AFEB\Widgets\Breadcrumb;

use AFEB\Helper as AFEBHelper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Breadcrumb Widget Helper Class
 * 
 * @class Helper
 * @version 1.0.0
 */
class Helper
{
    /**
     * Returning the breadcrumb structure
     * 
     * @since 1.0.0
     * 
     * @param string $separator
     * @param array $options
     */
    public function breadcrumb($separator = '', $options = [])
    {
        $default_options = ['disable_item_link' => 'no'];
        $options = wp_parse_args($options, $default_options);

        $home = esc_html('Home', 'addons-for-elementor-builder');
        $before = '<span class="afeb-current-crumb">';
        $after = '</span>';
        $output = '';
        $homeLink = home_url('/');
        $delimiter = trim($separator) ? $separator : '<span class="afeb-separator"></span>';

        if (!is_home() && !is_front_page() || is_paged()) {
            global $post;

            $href = $options['disable_item_link'] == 'no' ? ' href="' . esc_url($homeLink) . '"' : '';
            $output = '<a' . $href . '>' . esc_html($home) . '</a> ' . $delimiter . ' ';

            if (is_category()) {
                global $wp_query;

                $cat_obj = $wp_query->get_queried_object();
                $thisCat = $cat_obj->term_id;
                $thisCat = get_category($thisCat);
                $parentCat = get_category($thisCat->parent);

                if ($thisCat->parent != 0) {
                    $output .= get_category_parents($parentCat, true, ' ' . $delimiter . ' ');
                }

                $output .= $before . esc_html('Archive by category', 'addons-for-elementor-builder') . ' "' . single_cat_title('', false) . '"' . $after;
            } elseif (is_day()) {
                $href = $options['disable_item_link'] == 'no' ? ' href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '"' : '';
                $output .= '<a' . $href . '>' . esc_html(get_the_time('Y')) . '</a> ' . $delimiter . ' ';

                $href = $options['disable_item_link'] == 'no' ? ' href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '"' : '';
                $output .= '<a' . $href . '>' . esc_html(get_the_time('F')) . '</a> ' . $delimiter . ' ';
                $output .= $before . esc_html(get_the_time('d')) . $after;
            } elseif (is_month()) {
                $href = $options['disable_item_link'] == 'no' ? ' href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '"' : '';
                $output .= '<a' . $href . '>' . esc_html(get_the_time('Y')) . '</a> ' . $delimiter . ' ';
                $output .= $before . esc_html(get_the_time('F')) . $after;
            } elseif (is_year()) {
                $output .= $before . esc_html(get_the_time('Y')) . $after;
            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {

                    // woocommerce
                    $category = get_the_terms(get_the_ID(), 'product_cat');
                    $label = '';
                    $link = '';

                    if (is_array($category) && count($category) > 0) {
                        $label = (isset($category[0]->name) ?  esc_html($category[0]->name) : '');

                        if ($options['disable_item_link'] == 'no')
                            $link = 'href="' . esc_url($homeLink . '?product_cat=' . $category[0]->slug) . '"';
                    } else {
                        $post_type = get_post_type_object(get_post_type());
                        $slug = $post_type->rewrite;
                        $label = (isset($post_type->labels) ?  esc_html($post_type->labels->singular_name) : '');

                        if ($options['disable_item_link'] == 'no')
                            $link = 'href="' . esc_url($homeLink . '?post_type=' . $slug['slug']) . '"';
                    }

                    $output .= '<a ' . $link . '>' . $label . '</a> ' . $delimiter . ' ';
                    $output .= $before . esc_html(get_the_title()) . $after;
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    $output .= esc_html(get_category_parents($cat, true, ' ' . $delimiter . ' '));
                    $output .= $before . esc_html(get_the_title()) . $after;
                }
            } elseif (!is_single() && !is_page() && get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $output .= $before . (isset($post_type->labels) ? esc_html($post_type->labels->singular_name) : '') . $after;
            } elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                $cat = $cat[0];
                $output .= esc_html(get_category_parents($cat, true, ' ' . $delimiter . ' '));
                $href = $options['disable_item_link'] == 'no' ? ' href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '"' : '';
                $output .= '<a' . $href . '>' . esc_html($parent->post_title) . '</a> ' . $delimiter . ' ';
                $output .= $before . esc_html(get_the_title()) . $after;
            } elseif (is_page() && !$post->post_parent) {
                $output .= $before . esc_html(get_the_title()) . $after;
            } elseif (is_page() && $post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs = [];
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $href = $options['disable_item_link'] == 'no' ? ' href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '"' : '';
                    $breadcrumbs[] = '<a' . $href . '>' . esc_html(get_the_title($page->ID)) . '</a>';
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb) {
                    $output .= $crumb . ' ' . $delimiter . ' ';
                }
                $output .= $before . esc_html(get_the_title()) . $after;
            } elseif (is_search()) {
                $output .= $before . esc_html('Search results for', 'addons-for-elementor-builder') . ' "' . esc_html(get_search_query()) . '"' . $after;
            } elseif (is_tag()) {
                $output .= $before . esc_html('Post tags', 'addons-for-elementor-builder') . ' "' . esc_html(single_tag_title('', false)) . '"' . $after;
            } elseif (is_author()) {
                $author = get_queried_object();
                $author_id = $author->ID;
                $output .= $before . esc_html('Content written by', 'addons-for-elementor-builder') . ' ' . esc_html(get_the_author_meta('display_name', $author_id)) . $after;
            } elseif (is_404()) {
                $output .= $before . esc_html('404 Error', 'addons-for-elementor-builder') . $after;
            }
            if (get_query_var('paged')) {
                $isset = is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author();
                if ($isset) $output .= ' (';
                $output .= esc_html('Page', 'addons-for-elementor-builder') . ' ' . ' ' . intval(get_query_var('paged'));
                if ($isset) $output .= ')';
            }
        } else {
            $href = $options['disable_item_link'] == 'no' ? ' href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '"' : '';
            $output .= '<a' . $href . '>' . esc_html($home) . '</a> ' . $delimiter . ' ';
        }

        echo wp_kses($output, AFEBHelper::allowed_tags(
            ['div', 'a', 'span'],
            ['div' => ['aria-label' => []]]
        ));
    }
}
