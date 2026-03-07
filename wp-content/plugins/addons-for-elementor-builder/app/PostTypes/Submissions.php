<?php

namespace AFEB\PostTypes;

use AFEB\Assets;
use AFEB\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Submissions Class
 * 
 * @class Submissions
 * @version 1.4.0
 */
class Submissions extends PostTypes
{
    /**
     * Submissions Post Type Key
     */
    const SUBMISSIONS_POST_TYPE = 'afeb-submissions';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * Initialize "Vertex Addons for Elementor" Submissions
     * 
     * @since 1.4.0
     */
    public function init()
    {
        $this->assets = new Assets();
        $this->actions();
    }

    /**
     * Submissions Class Actions
     * 
     * @since 1.4.0
     */
    public function actions()
    {
        add_action('init', [$this, 'register']);
        add_action('current_screen', [$this, 'current_screen']);
        add_action('save_post', [$this, 'save'], 10, 2);
    }

    /**
     * Register post type
     * 
     * @since 1.4.0
     */
    public function register()
    {
        $supports = apply_filters('afeb/submissions/register_post_type/supports', ['title']);
        $args = [
            'can_export' => true,
            'exclude_from_search' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'labels' => [
                'name' => esc_html__('Submissions', 'addons-for-elementor-builder'),
                'singular_name' => esc_html__('Submissions', 'addons-for-elementor-builder'),
                'all_items' => esc_html__('All Submissions', 'addons-for-elementor-builder'),
            ],
            'menu_icon' => '',
            'public' => true,
            'publicly_queryable' => false,
            'query_var' => true,
            'rewrite' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => false,
            'supports' => $supports,
            'taxonomies' => [],
            'capability_type' => 'post',
            'capabilities' => ['create_posts' => false,],
            'map_meta_cap' => true,
        ];

        register_post_type(self::SUBMISSIONS_POST_TYPE, apply_filters('afeb/submissions/register_post_type/args', $args));
    }

    /**
     * Execute the relevant code on the current page
     * 
     * @since 1.4.0
     * 
     * @param object $screen
     */
    public function current_screen($screen)
    {
        if (isset($screen->id)) {
            if ($screen->id == 'edit-' . self::SUBMISSIONS_POST_TYPE) {
                add_action('admin_enqueue_scripts', function () {
                    $this->assets->normalize_style();
                });

                add_filter('admin_body_class', [$this, 'change_body_class']);
                add_filter('post_row_actions', [$this, 'post_row_actions'], 999, 2);
            }

            if ($screen->id == self::SUBMISSIONS_POST_TYPE) {
                add_action('add_meta_boxes', [$this, 'add_meta_boxes']);

                add_filter('use_block_editor_for_post_type', '__return_false', 100);

                if (function_exists('gutenberg_register_scripts_and_styles')) {
                    add_filter('gutenberg_can_edit_post_type', '__return_false', 100);

                    remove_action('admin_menu', 'gutenberg_menu');
                    remove_action('admin_init', 'gutenberg_redirect_demo');

                    // Gutenberg 5.3+
                    remove_action('wp_enqueue_scripts', 'gutenberg_register_scripts_and_styles');
                    remove_action('admin_enqueue_scripts', 'gutenberg_register_scripts_and_styles');
                    remove_action('admin_notices', 'gutenberg_wordpress_version_notice');
                    remove_action('rest_api_init', 'gutenberg_register_rest_widget_updater_routes');
                    remove_action('admin_print_styles', 'gutenberg_block_editor_admin_print_styles');
                    remove_action('admin_print_scripts', 'gutenberg_block_editor_admin_print_scripts');
                    remove_action('admin_print_footer_scripts', 'gutenberg_block_editor_admin_print_footer_scripts');
                    remove_action('admin_footer', 'gutenberg_block_editor_admin_footer');
                    remove_action('admin_enqueue_scripts', 'gutenberg_widgets_init');
                    remove_action('admin_notices', 'gutenberg_build_files_notice');
                    remove_action('do_meta_boxes', 'gutenberg_meta_box_save');
                    remove_action('submitpost_box', 'gutenberg_intercept_meta_box_render');
                    remove_action('submitpage_box', 'gutenberg_intercept_meta_box_render');
                    remove_action('edit_page_form', 'gutenberg_intercept_meta_box_render');
                    remove_action('edit_form_advanced', 'gutenberg_intercept_meta_box_render');
                    remove_action('admin_enqueue_scripts', 'gutenberg_check_if_classic_needs_warning_about_blocks');

                    remove_filter('redirect_post_location', 'gutenberg_meta_box_save_redirect');
                    remove_filter('filter_gutenberg_meta_boxes', 'gutenberg_filter_meta_boxes');
                    remove_filter('body_class', 'gutenberg_add_responsive_body_class');
                    remove_filter('admin_url', 'gutenberg_modify_add_new_button_url'); // old
                    remove_filter('register_post_type_args', 'gutenberg_filter_post_type_labels');
                    remove_filter('load_script_translation_file', 'gutenberg_override_translation_file');
                    remove_filter('block_editor_settings', 'gutenberg_extend_block_editor_styles');
                    remove_filter('default_content', 'gutenberg_default_demo_content');
                    remove_filter('default_title', 'gutenberg_default_demo_title');
                    remove_filter('block_editor_settings', 'gutenberg_legacy_widget_settings');
                    remove_filter('rest_request_after_callbacks', 'gutenberg_filter_oembed_result');
                    remove_filter('wp_refresh_nonces', 'gutenberg_add_rest_nonce_to_heartbeat_response_headers');
                    remove_filter('get_edit_post_link', 'gutenberg_revisions_link_to_editor');
                    remove_filter('wp_prepare_revision_for_js', 'gutenberg_revisions_restore');
                    remove_filter('registered_post_type', 'gutenberg_register_post_prepare_functions');
                }
            }
        }
    }

    /**
     * Change current page body class
     * 
     * @since 1.4.0
     * 
     * @param string $classes
     * 
     * @return string
     */
    public function change_body_class($classes)
    {
        return $classes . ' afeb-body afeb-submissions-posts-body';
    }

    /**
     * Filters the array of row action links
     * 
     * @since 1.4.0
     * 
     * @param string[] $actions
     * @param WP_Post $post
     * 
     * @return array
     */
    public function post_row_actions($actions, $post)
    {
        $output = [];

        if (!empty($actions['edit']))
            $output['edit'] = $actions['edit'];

        if (!empty($actions['trash']))
            $output['trash'] = $actions['trash'];

        return $output;
    }

    public function add_meta_boxes()
    {
        add_meta_box('form-fields', esc_html__('Submission', 'addons-for-elementor-builder'), function ($post) {
            $this->get_template_part($this->get_path() . '/html/admin/metaboxes/submissions/submission/');
        }, self::SUBMISSIONS_POST_TYPE, 'advanced', 'low');

        remove_meta_box('submitdiv', self::SUBMISSIONS_POST_TYPE, 'side');
        add_meta_box('submitdiv', esc_html__('Additional Info', 'addons-for-elementor-builder'), function ($post) {
            $this->get_template_part($this->get_path() . '/html/admin/metaboxes/submissions/additional-info/');
        }, self::SUBMISSIONS_POST_TYPE, 'side', 'high');
    }

    /**
     * Save value of Submissions post type in DB
     * 
     * @since 1.0.0
     * @access public
     * 
     * @param int $post_id
     * @param object $post
     * 
     * @return void
     */
    public function save($post_id, $post)
    {
        // It's not a Submission
        if ($post->post_type !== self::SUBMISSIONS_POST_TYPE) {
            return;
        }

        // Nonce is not set!
        if (!isset($_POST['_afeb_submissions_nonce'])) {
            return;
        }

        // Nonce is not valid!
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_afeb_submissions_nonce'])), 'afeb_submissions_cpt')) {
            return;
        }

        // We don't need to do anything on post auto save
        if (defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) {
            return;
        }

        $form_fields = isset($_POST['afeb-form-fields']) ? wp_unslash($_POST['afeb-form-fields']) : [];
        if (!is_array($form_fields)) $form_fields = [];

        $db_form_fields = get_post_meta($post_id, 'afeb_form_fields', true);
        if (!is_array($db_form_fields)) $db_form_fields = [];

        foreach ($form_fields as $key => $value) {
            if (isset($db_form_fields[$key]['value'])) {

                if (isset($db_form_fields[$key]['type'])) {

                    switch ($db_form_fields[$key]['type']) {
                        case 'tel':
                        case 'number':
                            $value = preg_replace('/[^\d+]/', '', $value);
                            break;
                        case 'email':
                            $value = sanitize_email($value);
                            break;
                        case 'url':
                            $value = sanitize_url($value);
                            break;
                        case 'textarea':
                            $value = sanitize_textarea_field($value);
                            break;
                        case 'checkbox':
                            $value = is_array($value) ? map_deep($value, 'sanitize_text_field') : sanitize_textarea_field($value);
                            break;
                        default:
                            $value = sanitize_text_field($value);
                            break;
                    }
                }

                $db_form_fields[$key]['value'] = $value;
            }
        }

        update_post_meta($post_id, 'afeb_form_fields', $db_form_fields);
    }
}
