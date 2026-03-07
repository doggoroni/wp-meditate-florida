<?php

namespace AFEB\NavMenus;

use AFEB\Assets;
use AFEB\Form;
use AFEB\NavMenus;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Edit AdvancedMenu Class
 * 
 * @class Edit
 * @version 1.3.0
 */
class Edit extends NavMenus
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * Initialize "Vertex Addons for Elementor" Dashboard
     * 
     * @since 1.3.0
     */
    public function init()
    {
        require_once $this->app_path('NavMenus/AdvancedMenuEditWalker.php');
        $this->assets = new Assets();
        $this->actions();
        $this->filters();
    }

    /**
     * Edit Class Actions
     * 
     * @since 1.3.0
     */
    public function actions()
    {
        add_action('current_screen', [$this, 'current_screen']);
        add_action('save_post', [$this, 'save'], 10, 2);
    }

    /**
     * Edit Class Filters
     * 
     * @since 1.3.0
     */
    public function filters()
    {
        add_filter('wp_edit_nav_menu_walker', function () {
            return 'AFEB\NavMenus\AdvancedMenuEditWalker';
        });
        add_filter('afeb/advanced_menu/fields', [$this, 'output'], 10, 5);
    }

    /**
     * Execute the relevant code on the current page
     * 
     * @since 1.3.0
     * 
     * @param object $screen
     */
    public function current_screen($screen)
    {
        if (isset($screen->id) && $screen->id == 'nav-menus') {
            add_action('admin_enqueue_scripts', function () {
                $this->assets->icon_picker_style();
                $this->assets->icon_picker_script();
                $this->assets->wp_color_picker_style();
                $this->assets->wp_color_picker_script();
                $this->assets->fontawesome_style();
            });
        }
    }

    /**
     * Edit AdvancedMenu HTML output
     * 
     * @since 1.3.0
     */
    public function output($item_output, $item, $depth, $args)
    {
        preg_match('/div.*class=["|\'].*submitbox.*["|\']/i', $item_output, $match);

        switch ($depth) {
            case 0:
                if (!empty($match[0])) $item_output = preg_replace("/<$match[0]/", $this->render($item, $depth) . '<' . $match[0], $item_output);
                return $item_output;
            default:
                if (!empty($match[0])) $item_output = preg_replace("/<$match[0]/", $this->render($item, $depth) . '<' . $match[0], $item_output);
                return $item_output;
        }
    }

    /**
     * Fires once a nav menu has been saved
     * 
     * @since 1.3.0
     * 
     * @param int $id
     * @param WP_Post $post
     */
    public function save($id, $post)
    {
        if (
            $post->post_type === 'nav_menu_item' &&
            !empty($_POST['update-nav-menu-nonce']) &&
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['update-nav-menu-nonce'])), 'update-nav_menu')
        ) {
            $activation = isset($_POST['afeb_advanced_menu_activation'][$id]) ? intval(wp_unslash($_POST['afeb_advanced_menu_activation'][$id])) : null;
            if ($activation !== null) update_post_meta($id, 'afeb_advanced_menu_activation', $activation);

            $iconpicker = isset($_POST['afeb_advanced_menu_icon'][$id]) ? sanitize_text_field(wp_unslash($_POST['afeb_advanced_menu_icon'][$id])) : null;
            if ($iconpicker !== null) update_post_meta($id, 'afeb_advanced_menu_icon', $iconpicker);
        }
    }

    /**
     * Edit HTML output for AdvancedMenu (depth:0)
     * 
     * @since 1.3.0
     * 
     * @param object $item
     * @param int $item
     */
    public function render($item, $depth = 0)
    {
        $activation = !empty($item->ID) ? get_post_meta($item->ID, 'afeb_advanced_menu_activation', true) : false;
        $activation_class = empty($activation) ? 'afeb-edit-advanced-menu-deactive' : '';
        $data_label = !empty($activation) ? esc_html__('Enable Advanced Mode', 'addons-for-elementor-builder') : esc_html__('Disable Advanced Mode', 'addons-for-elementor-builder');
        $label = empty($activation) ? esc_html__('Enable Advanced Mode', 'addons-for-elementor-builder') : esc_html__('Disable Advanced Mode', 'addons-for-elementor-builder');
        $display_fields = !empty($activation) ? 'style="display: block;"' : '';

        return '<fieldset class="afeb-edit-advanced-menu-wrap">
                    <div class="afeb-space afeb-mr-20"></div>
                        <div class="afeb-edit-advanced-menu-activation ' . $activation_class . '">
                            <input type="hidden" name="afeb_advanced_menu_activation[' . $item->ID . ']" value="0">
                            <input class="afeb-hidden-checkbox afeb-edit-advanced-menu-activation-checkbox" type="checkbox" name="afeb_advanced_menu_activation[' . $item->ID . ']" value="1"' . checked(1, $activation, false) . '>
                            <label data-label="' . $data_label . '">' . $label . '</label>
                        </div>
                    <div class="afeb-edit-advanced-menu-fields" ' . $display_fields . '>' .
            $this->fields_output($item, 'icon') .
            '</div><div class="afeb-space afeb-mr-20"></div>
            </fieldset>';
    }

    /**
     * Additional fields for AdvancedMenu
     * 
     * @since 1.3.0
     * 
     * @param object $item
     * @param string $fields_name
     * @param string|int $depth
     */
    public function fields_output($item, $fields_name = '', $depth = '')
    {
        $icon_value = !empty($item->ID) ? get_post_meta($item->ID, 'afeb_advanced_menu_icon', true) : '';

        $output = [
            // Icon
            'icon' => '<p><label for="afeb-edit-advanced-menu-field-icon">' . esc_html__('Icon', 'addons-for-elementor-builder') .
                Form::iconpicker(['id' => 'afeb-edit-advanced-menu-field-icon', 'class' => 'afeb-edit-advanced-menu-field-icon', 'name' => 'afeb_advanced_menu_icon[' . $item->ID . ']', 'value' => $icon_value]) . '</label></p>',
            // Depth
            'depth' => Form::input(['id' => 'afeb-edit-advanced-menu-field-depth', 'type' => 'hidden', 'name' => 'afeb_advanced_menu_depth[' . $item->ID . ']', 'value' => $depth]),
        ];

        if (!empty($output[$fields_name]))
            return $output[$fields_name];
    }
}
