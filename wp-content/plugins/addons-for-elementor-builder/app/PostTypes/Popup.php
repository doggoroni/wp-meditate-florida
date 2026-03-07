<?php

namespace AFEB\PostTypes;

use AFEB\Assets;
use AFEB\Helper;
use AFEB\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Popup Class
 * 
 * @class Popup
 * @version 1.2.0
 */
class Popup extends PostTypes
{
    /**
     * Popup Post Type Key
     */
    const POPUP_POST_TYPE = 'afeb-popup';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * Initialize "Vertex Addons for Elementor" Popup
     * 
     * @since 1.2.0
     */
    public function init()
    {
        $this->assets = new Assets();
        $this->actions();
        $this->filters();
    }

    /**
     * Popup Class Actions
     * 
     * @since 1.2.0
     */
    public function actions()
    {
        add_action('init', [$this, 'register']);
        add_action('template_include', [$this, 'output'], 9999);
        add_action('wp_footer', [$this, 'render_popups']);
        add_action('current_screen', [$this, 'current_screen']);
    }

    /**
     * Popup Class Filters
     * 
     * @since 1.2.0
     */
    public function filters()
    {
        add_filter('option_elementor_cpt_support', [$this, 'set_option_support']);
        add_filter('default_option_elementor_cpt_support', [$this, 'set_option_support']);
    }

    /**
     * Register post type
     * 
     * @since 1.2.0
     */
    public function register()
    {
        $supports = apply_filters('afeb/popup/register_post_type/supports', ['title', 'editor', 'author', 'elementor', 'custom-fields']);
        $args = [
            'can_export' => true,
            'description' => 'description',
            'exclude_from_search' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'labels' => [
                'name' => esc_html__('Popups', 'addons-for-elementor-builder'),
                'singular_name' => esc_html__('Popups', 'addons-for-elementor-builder'),
                'all_items' => esc_html__('All Popups', 'addons-for-elementor-builder'),
                'add_new' => esc_html__('New Popup', 'addons-for-elementor-builder'),
                'add_new_item' => esc_html__('New Popup', 'addons-for-elementor-builder'),
                'edit_item' => esc_html__('Edit Popup', 'addons-for-elementor-builder')
            ],
            'menu_icon' => '',
            'public' => true,
            'publicly_queryable' => true,
            'query_var' => true,
            'rewrite' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'supports' => $supports,
            'taxonomies' => []
        ];

        register_post_type(self::POPUP_POST_TYPE, apply_filters('afeb/popup/register_post_type/args', $args));
    }

    /**
     * Popup settings
     * 
     * @since 1.2.0
     * 
     * @param int $id
     * 
     * @return array
     */
    public static function get_settings($id)
    {
        $page_settings = get_post_meta($id, '_elementor_page_settings', true);
        $defaults = [
            'ppup_anim' => 'fade',
            'ppup_anim_drtn' => 1,
            'ppup_esc_cls' => 'yes',
            'ppup_dsabl_pge_scrl' => '',
            'ppup_opn_dly' => 1,
            'ppup_ovrly_cls' => 'yes',
            'ppup_shonc' => 0,
            'ppup_opn_evnt' => ''
        ];

        return wp_parse_args($page_settings, $defaults);
    }

    /**
     * Popup HTML output
     * 
     * @since 1.2.0
     * 
     * @param string $template
     * 
     * @return string
     */
    public function output($template)
    {
        $is_singular = is_singular(self::POPUP_POST_TYPE);
        $elementor_template_type = Helper::get_elementor_template_type(get_the_ID());

        if (
            $is_singular &&
            $elementor_template_type == self::POPUP_POST_TYPE
        ) $template = AFEB_ABSPATH . '/html/admin/menus/popup/editor/tpl.php';

        return $template;
    }

    /**
     * Render the popup in the output
     * 
     * @since 1.2.0
     * 
     * @return string
     */
    public function render_popups()
    {
        if (!Helper::is_edit_mode())
            $this->get_template_part($this->get_path() . '/html/front/popup/');
    }

    /**
     * Execute the relevant code on the current page
     * 
     * @since 1.2.0
     * 
     * @param object $screen
     */
    public function current_screen($screen)
    {
        if (isset($screen->id)) {
            if ($screen->id == 'edit-' . self::POPUP_POST_TYPE) {
                add_action('admin_footer', [$this, 'new_popup_dialog']);
                add_action('admin_enqueue_scripts', function () {
                    $this->assets->normalize_style();
                    $this->assets->fontawesome_style();
                    $this->assets->jq_dialog_style();
                    wp_enqueue_script('jquery-effects-scale');
                    $this->assets->component_script();
                });

                add_filter('admin_body_class', [$this, 'change_body_class']);
                add_filter('post_row_actions', [$this, 'post_row_actions'], 999, 2);
            }
        }
    }

    /**
     * Add elementor support
     * 
     * @since 1.2.0
     * 
     * @param string $value
     * 
     * @return array
     */
    public function set_option_support($value)
    {
        return !empty($value) ? array_merge($value, [self::POPUP_POST_TYPE]) : [];
    }

    /**
     * Change current page body class
     * 
     * @since 1.2.0
     * 
     * @param string $classes
     * 
     * @return string
     */
    public function change_body_class($classes)
    {
        return $classes . ' afeb-body afeb-popup-builder-posts-body';
    }

    /**
     * Filters the array of row action links
     * 
     * @since 1.2.0
     * 
     * @param string[] $actions
     * @param WP_Post $post
     * 
     * @return array
     */
    public function post_row_actions($actions, $post)
    {
        $output = [];

        if (!empty($actions['inline hide-if-no-js']))
            $output['inline hide-if-no-js'] = $actions['inline hide-if-no-js'];

        if (!empty($actions['trash']))
            $output['trash'] = $actions['trash'];

        if (did_action('elementor/loaded')) {
            $id = intval($post->ID);
            $link = esc_url(admin_url('post.php') . "?post={$id}&action=elementor");
            $output['edit'] = sprintf('<a href="%s" aria-label="">%s</a>', $link, esc_html__('Edit with Elementor', 'addons-for-elementor-builder'));
        }

        return $output;
    }

    /**
     * Add a popup creation dialog to the page
     * 
     * @since 1.2.0
     */
    public function new_popup_dialog()
    {
        $this->get_template_part($this->get_path() . '/html/admin/menus/popup/dialogs/new-popup/');
    }
}
