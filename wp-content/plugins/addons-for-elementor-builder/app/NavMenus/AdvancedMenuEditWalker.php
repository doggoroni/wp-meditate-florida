<?php

namespace AFEB\NavMenus;

use Walker_Nav_Menu_Edit;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" AdvancedMenuEditWalker Class
 * 
 * @class AdvancedMenuEditWalker
 * @version 1.3.0
 */
require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
class AdvancedMenuEditWalker extends Walker_Nav_Menu_Edit
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
        parent::start_el($item_output, $item, $depth, $args);
        $output .= apply_filters('afeb/advanced_menu/fields', $item_output, $item, $depth, $args);
    }
}
