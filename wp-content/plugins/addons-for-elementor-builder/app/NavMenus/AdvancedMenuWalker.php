<?php

namespace AFEB\NavMenus;

use AFEB\Helper;
use Walker_Nav_Menu;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" AdvancedMenuWalker Class
 * 
 * @class AdvancedMenuWalker
 * @version 1.3.0
 */
require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
class AdvancedMenuWalker extends  Walker_Nav_Menu
{
    /**
     * Starts the element output
     * 
     * @since 1.3.0
     * 
     * @param string $output
     * @param WP_Post
     * @param int $depth
     * @param stdClass $args
     * @param int $id
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
    {
        $menu_item_settings = get_post_meta($item->ID);
        if (empty($menu_item_settings['afeb_advanced_menu_activation'][0])) $menu_item_settings = null;

        $title = empty($menu_item_settings['afeb_advanced_menu_hide_title'][0]) ? apply_filters('afeb/advanced_menu/walker/title', $item->title, $item->ID) : '';
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $url = trailingslashit($item->url);
        $base = basename($url);

        if (!Helper::contains($url, '/#') && is_page_template()) {
            if (
                in_array('current-menu-ancestor', $classes) ||
                in_array('current-menu-item', $classes) ||
                (in_array('current_page_parent', $classes) && get_post_type() === 'post')
            ) {
                $classes[] = 'afeb-advanced-menu-item-active';
            }


            $ptype = get_post_type_object(get_post_type(get_the_ID()));
            if (!empty($ptype)) {
                $cndtn = ($base === strtolower(urlencode(html_entity_decode($ptype->name))));
                if ($cndtn && trailingslashit(home_url()) === $url) $cndtn = false;
                if ((is_singular() && $url === trailingslashit(get_the_permalink(get_the_ID()))) ||
                    (isset($ptype->rewrite['slug']) && Helper::contains($base, $ptype->rewrite['slug']) && in_array('current_page_parent', $classes)) ||
                    $cndtn ||
                    ($base === strtolower(urlencode(html_entity_decode($ptype->label)))) ||
                    ($base === strtolower(urlencode(html_entity_decode($ptype->has_archive))))
                ) $classes[] = 'afeb-advanced-menu-item-active';
            }

            if (in_array('menu-item-object-category', $classes) && is_single()) {
                $key = array_search('current-menu-parent', $classes);
                if (isset($classes[$key])) unset($classes[$key]);
            }
        }
        $classes = join(' ', apply_filters('afeb/advanced_menu/walker/classes', array_filter($classes), $item));
        $classes .= ' ' . ($depth == 0 ? 'afeb-advanced-menu-item' : 'afeb-advanced-menu-sub-item');
        $classes = ' class="' . esc_attr($classes) . '"';

        if (!empty($menu_item_settings['afeb_advanced_menu_icon'][0])) {
            $icon = $menu_item_settings['afeb_advanced_menu_icon'][0];
            $title = $title ?
                '<i class="' . $icon . '"></i>' . $title :
                '<i class="' . $icon . ' afeb-advanced-menu-no-text-icon" title="' . $title . '"></i>';
        }

        $li_id = 'afeb-menu-' . $item->ID;
        $output .= '<li id="' . $li_id . '"' . $classes  . '>';

        $attributes = empty($item->attr_title) ? '' : ' title="' . esc_attr($item->attr_title) . '"';
        $attributes .= empty($item->target) ? '' : ' target="' . esc_attr($item->target) . '"';
        $attributes .= empty($item->xfn) ? '' : ' rel="' . esc_attr($item->xfn) . '"';
        $attributes .= empty($item->url) ? '' : ' href="' . esc_attr($item->url) . '"';
        $attributes .= ' data-title="' . esc_attr(wp_strip_all_tags($title)) . '"';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . '<span>' . $title . '</span>' . $args->link_after;
        $item_output .= '</a>';
        $item_output .= (!empty($menu_item_settings['afeb_advanced_menu'][0]) &&
            $menu_item_settings['afeb_advanced_menu'][0] == 'custom' &&
            !empty($menu_item_settings['afeb_advanced_menu_template_id'][0])) ?
            '<ul class="sub-menu">' .
            Helper::get_page_as_element($menu_item_settings['afeb_advanced_menu_template_id'][0]) .
            '</ul>' : '';

        $output .= apply_filters('afeb/advanced_menu/walker/start_el', $item_output, $item, $depth, $args, $id);
    }
}
