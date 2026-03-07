<?php

namespace AFEB\PostTypes;

use AFEB\Assets;
use AFEB\Db;
use AFEB\Form;
use AFEB\Helper;
use AFEB\Modules\DisplayConditions\DisplayConditions;
use AFEB\PostTypes;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Builder Class
 * 
 * @class Builder
 * @version 1.3.0
 */
class Builder extends PostTypes
{
    /**
     * Builder Post Type Key
     */
    const BUILDER_POST_TYPE = 'afeb-builder';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var int
     */
    private $post_id;

    /**
     * Initialize "Vertex Addons for Elementor" Builder
     * 
     * @since 1.3.0
     */
    public function init()
    {
        $this->assets = new Assets();
        $this->actions();
        $this->filters();
    }

    /**
     * Builder Class Actions
     * 
     * @since 1.3.0
     */
    public function actions()
    {
        add_action('init', [$this, 'register']);
        add_action('manage_' . self::BUILDER_POST_TYPE . '_posts_custom_column', array($this, 'custom_columns'), 10, 2);
        add_action('restrict_manage_posts', [$this, 'add_filters']);
        add_action('elementor/dynamic_tags/before_render', [$this, 'switch_to_preview_query']);
        add_action('elementor/dynamic_tags/after_render', [$this, 'restore_current_query']);
        add_action('wp', function () {
            add_action('get_header', [$this, 'get_header']);
            add_action('elementor/page_templates/canvas/before_content', [$this, 'get_canvas_header']);

            add_action('get_footer', [$this, 'get_footer']);
            add_action('elementor/page_templates/canvas/after_content', [$this, 'get_canvas_footer']);
        });
        add_action('template_include', [$this, 'output'], 9999);
        add_action('elementor/page_templates/canvas/afeb_print_content', [$this, 'print_content'], 9999);
        add_action('current_screen', [$this, 'current_screen']);
    }

    /**
     * Builder Class Filters
     * 
     * @since 1.3.0
     */
    public function filters()
    {
        add_filter('manage_' . self::BUILDER_POST_TYPE . '_posts_columns', [$this, 'add_custom_columns']);
        add_filter('parse_query', [$this, 'apply_filters']);
        add_filter('option_elementor_cpt_support', [$this, 'set_option_support']);
        add_filter('default_option_elementor_cpt_support', [$this, 'set_option_support']);
    }

    /**
     * Register post type
     * 
     * @since 1.3.0
     */
    public function register()
    {
        $supports = apply_filters('afeb/builder/register_post_type/supports', ['title', 'editor', 'author', 'elementor', 'custom-fields']);
        $args = [
            'can_export' => true,
            'description' => 'description',
            'exclude_from_search' => true,
            'has_archive' => false,
            'hierarchical' => false,
            'labels' => [
                'name' => esc_html__('Template Builder', 'addons-for-elementor-builder'),
                'singular_name' => esc_html__('Templates', 'addons-for-elementor-builder'),
                'add_new' => esc_html__('Add Template', 'addons-for-elementor-builder'),
                'add_new_item' => esc_html__('Add New Template', 'addons-for-elementor-builder'),
                'new_item' => esc_html__('New Template', 'addons-for-elementor-builder'),
                'edit_item' => esc_html__('Edit Template', 'addons-for-elementor-builder'),
                'view_item' => esc_html__('View Template', 'addons-for-elementor-builder'),
                'view_items' => esc_html__('View Templates', 'addons-for-elementor-builder'),
                'all_items' => esc_html__('All Templates', 'addons-for-elementor-builder'),
                'search_items' => esc_html__('Search Templates', 'addons-for-elementor-builder'),
                'not_found' => esc_html__('No templates found!', 'addons-for-elementor-builder'),
                'not_found_in_trash' => esc_html__('No templates found in Trash!', 'addons-for-elementor-builder'),
                'archives' => esc_html__('Template Archives', 'addons-for-elementor-builder'),
                'insert_into_item' => esc_html__('Insert into template', 'addons-for-elementor-builder'),
                'uploaded_to_this_item' => esc_html__('Uploaded to this template', 'addons-for-elementor-builder'),
                'filter_items_list' => esc_html__('Filter templates list', 'addons-for-elementor-builder')
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

        register_post_type(self::BUILDER_POST_TYPE, apply_filters('afeb/builder/register_post_type/args', $args));
    }

    public function get_header($name, $canvas = false)
    {
        $matched_conditions = (new DisplayConditions())->find_matched_results(self::BUILDER_POST_TYPE);

        // Purification of headers other pages are removed.
        foreach ($matched_conditions as $key => $value) {
            $document_type = get_post_meta(intval($value['id']), '_afeb_document_type', true);

            if ($document_type !== 'header') {
                unset($matched_conditions[$key]);
            }
        }

        $sorted_matched_conditions_by_priority = $matched_conditions;
        usort($sorted_matched_conditions_by_priority, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        if (isset($sorted_matched_conditions_by_priority[0])) {

            $matched_conditions = $sorted_matched_conditions_by_priority[0];
            foreach ($sorted_matched_conditions_by_priority as $key => $value) {
                if (($value['priority'] == $matched_conditions['priority']) &&
                    ($value['id'] > $matched_conditions['id'])
                ) {
                    $matched_conditions = $value;
                }
            }
        }

        $headers = get_posts([
            'include' => !empty($matched_conditions) ? [$matched_conditions['id']] : [-1],
            'meta_key' => '_afeb_document_type',
            'meta_value' => 'header',
            'post_type' => self::BUILDER_POST_TYPE,
            'suppress_filters' => false,
        ]);

        $id = $headers[0]->ID ?? '';

        $page_settings = get_post_meta($id, '_elementor_page_settings', true);
        if (! is_array($page_settings)) $page_settings = [];

        if ($canvas && empty($page_settings['include_in_canvas_page'])) {
            return;
        }

        if (is_int($id) && !empty($page_settings['enable_display_conditons'])) {
            require AFEB_ABSPATH . '/html/admin/menus/builder/editor/header/tpl.php';
            echo Helper::get_page_as_element($id);

            $templates = [];
            $name = (string) $name;

            if ($name != '') {
                $templates[] = "header-{$name}.php";
            }

            $templates[] = 'header.php';

            remove_all_actions('wp_head');

            if (!$canvas) {
                ob_start();
                locate_template($templates, true);
                ob_get_clean();
            }
        }
    }

    public function get_canvas_header()
    {
        $this->get_header('', true);
    }

    public function get_footer($name, $canvas = false)
    {
        $matched_conditions = (new DisplayConditions())->find_matched_results(self::BUILDER_POST_TYPE);

        // Purification of footers other pages are removed.
        foreach ($matched_conditions as $key => $value) {
            $document_type = get_post_meta(intval($value['id']), '_afeb_document_type', true);

            if ($document_type !== 'footer') {
                unset($matched_conditions[$key]);
            }
        }

        $sorted_matched_conditions_by_priority = $matched_conditions;
        usort($sorted_matched_conditions_by_priority, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        if (isset($sorted_matched_conditions_by_priority[0])) {

            $matched_conditions = $sorted_matched_conditions_by_priority[0];
            foreach ($sorted_matched_conditions_by_priority as $key => $value) {
                if (($value['priority'] == $matched_conditions['priority']) &&
                    ($value['id'] > $matched_conditions['id'])
                ) {
                    $matched_conditions = $value;
                }
            }
        }

        $footers = get_posts([
            'include' => !empty($matched_conditions) ? [$matched_conditions['id']] : [-1],
            'meta_key' => '_afeb_document_type',
            'meta_value' => 'footer',
            'post_type' => self::BUILDER_POST_TYPE,
            'suppress_filters' => false,
        ]);

        $id = $footers[0]->ID ?? '';

        $page_settings = get_post_meta($id, '_elementor_page_settings', true);
        if (!is_array($page_settings)) $page_settings = [];

        if ($canvas && empty($page_settings['include_in_canvas_page'])) {
            return;
        }

        if (is_int($id) && !empty($page_settings['enable_display_conditons'])) {
            echo Helper::get_page_as_element($id);
            require AFEB_ABSPATH . '/html/admin/menus/builder/editor/footer/tpl.php';

            $templates = [];
            $name = (string) $name;

            if ($name != '') {
                $templates[] = "footer-{$name}.php";
            }

            $templates[] = 'footer.php';

            if (!$canvas) {
                ob_start();
                locate_template($templates, true);
                ob_get_clean();
            }
        }
    }

    public function get_canvas_footer()
    {
        $this->get_footer('', true);
    }

    /**
     * Builder HTML output
     * 
     * @since 1.3.0
     * 
     * @param string $template
     * 
     * @return string
     */
    public function output($template)
    {
        $is_singular = is_singular(self::BUILDER_POST_TYPE);
        $elementor_template_type = Helper::get_elementor_template_type(get_the_ID());
        $document_type = get_post_meta(get_the_ID(), '_afeb_document_type', true);

        if (
            $is_singular &&
            $elementor_template_type == self::BUILDER_POST_TYPE
        ) {
            $document = Plugin::instance()->documents->get_doc_or_auto_save(get_the_ID());
            $document_settings = $document->get_settings_for_display();
            $page_template = $document_settings['template_type'] ?? 'default';
            $GLOBALS['afeb_document_type'] = $document_type;

            switch ($document_type) {
                case 'archive':
                case 'single':
                    if (!trim($page_template) || ($page_template == 'default' || $page_template == 'elementor_header_footer')) {
                        $template = AFEB_ABSPATH . '/html/admin/menus/builder/editor/header-footer/tpl.php';
                    } else {
                        $template = AFEB_ABSPATH . '/html/admin/menus/builder/editor/canvas/tpl.php';
                    }
                    break;
                case 'dynamic-loop-item':
                    $this->assets->dynamic_loop_item_style();
                    $template = AFEB_ABSPATH . '/html/admin/menus/builder/editor/dynamic-loop-item/tpl.php';
                    break;
                case 'footer':
                case 'header':
                    $template = AFEB_ABSPATH . '/html/admin/menus/builder/editor/canvas/tpl.php';
                    break;
            }
        } else {
            $matched_conditions = (new DisplayConditions())->find_matched_results(self::BUILDER_POST_TYPE);
            $matched_conditions = array_map(function ($item) {
                return $item['id'];
            }, $matched_conditions);

            $pages = get_posts([
                'orderby' => 'date',
                'order' => 'DESC',
                'include' => !empty($matched_conditions) ? $matched_conditions : [-1],
                'meta_key' => '_afeb_document_type',
                'meta_value' => ['archive', 'single'],
                'post_type' => self::BUILDER_POST_TYPE,
                'suppress_filters' => false,

            ]);

            $id = $pages[0]->ID ?? '';
            $page_settings = get_post_meta($id, '_elementor_page_settings', true);

            if (is_int($id) && !empty($page_settings['enable_display_conditons'])) {

                $this->post_id = $id;

                $document_settings = get_post_meta($id, '_elementor_page_settings');
                $page_template = $document_settings[0]['template_type'] ?? 'default';

                if (!trim($page_template) || ($page_template == 'default' || $page_template == 'elementor_header_footer')) {
                    $template = AFEB_ABSPATH . '/html/admin/menus/builder/editor/header-footer/tpl.php';
                } else {
                    $template = AFEB_ABSPATH . '/html/admin/menus/builder/editor/canvas/tpl.php';
                }
            }
        }

        return $template;
    }

    public function print_content()
    {
        $id = $this->post_id;
        echo Helper::get_page_as_element($id);
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
        if (isset($screen->id)) {
            if ($screen->id == 'edit-' . self::BUILDER_POST_TYPE) {
                add_action('admin_footer', [$this, 'new_builder_dialog']);
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

    public function switch_to_preview_query()
    {
        if (get_post_type(get_the_ID()) == self::BUILDER_POST_TYPE) {
            $document = Plugin::instance()->documents->get_doc_or_auto_save(get_the_ID());
            Plugin::instance()->db->switch_to_query($document->get_document_query_args());
        }
    }

    public function restore_current_query()
    {
        Plugin::instance()->db->restore_current_query();
    }

    /**
     * Get template type label
     * 
     * @since 1.3.0
     * 
     * @param string $type
     * 
     * @return string
     */
    public function get_template_type_label($type = '')
    {
        switch ($type) {
            case 'archive':
                $type = esc_html__('Archive', 'addons-for-elementor-builder');
                break;
            case 'dynamic-loop-item':
                $type = esc_html__('Dynamic Loop Item', 'addons-for-elementor-builder');
                break;
            case 'footer':
                $type = esc_html__('Footer', 'addons-for-elementor-builder');
                break;
            case 'header':
                $type = esc_html__('Header', 'addons-for-elementor-builder');
                break;
            case 'single':
                $type = esc_html__('Single', 'addons-for-elementor-builder');
                break;
            default:
                $type = esc_html__('Unknown', 'addons-for-elementor-builder');
                break;
        }

        return $type;
    }

    /**
     * Add custom columns
     * 
     * @since 1.3.0
     * 
     * @param array $columns
     * 
     * @return array
     */
    public function add_custom_columns($columns)
    {
        $title = $columns['title'];
        $author = $columns['author'];
        $date = $columns['date'];

        unset($columns['title']);
        unset($columns['author']);
        unset($columns['date']);

        $columns['title'] = $title;
        $columns = array_merge($columns, [
            'type' => esc_html__('Type', 'addons-for-elementor-builder'),
        ]);
        $columns['author'] = $author;
        $columns['date'] = $date;
        return $columns;
    }

    /**
     * Add custom columns
     * 
     * @since 1.3.0
     * 
     * @param array $columns
     * @param int $post_id
     */
    public function custom_columns($columns, $post_id)
    {
        if ($columns == 'type') {
            $type = get_post_meta($post_id, '_afeb_document_type', true);
            printf('<strong><code>%s</code></strong>', $this->get_template_type_label($type));
        }
    }

    /**
     * Add filter options in manage builder page
     * 
     * @since 1.3.0
     * 
     * @param string $post_type
     */
    public function add_filters($post_type)
    {
        if ($post_type !== self::BUILDER_POST_TYPE) return;

        $post_status = !empty($_GET['post_status']) ? esc_sql(sanitize_text_field(wp_unslash($_GET['post_status']))) : '';
        $post_status_sql = (!trim($post_status) || $post_status == 'all') ?
            "`#__posts` . `post_status` != 'trash'" :
            "`#__posts` . `post_status` = '{$post_status}'";

        $query = "SELECT `#__posts`.`ID`,`#__postmeta`.`meta_value` FROM `#__posts` " .
            "LEFT JOIN `#__postmeta` ON `#__posts`.`ID` = `#__postmeta`.`post_id` And `#__postmeta`.`meta_key` = '_afeb_document_type' " .
            "AND `#__postmeta`.`meta_value` != '' WHERE {$post_status_sql} AND `#__posts`.`post_type` = 'afeb-builder' " .
            "GROUP BY `#__postmeta`.`meta_value`";
        $db_options = (new Db())->slct($query, 'ASSOCLIST');
        $options = [];

        foreach ($db_options as $db_option)
            $options[$db_option['meta_value']] = $this->get_template_type_label($db_option['meta_value']);

        if (count($options) > 1) {
            $options = array_merge(['' => esc_html__('All types', 'addons-for-elementor-builder')], $options);
            $selected = !empty($_GET['afeb_template_type_filter']) ?
                sanitize_text_field(wp_unslash($_GET['afeb_template_type_filter'])) : '';

            Form::select_e([
                'id' => 'afeb_template_type_filter',
                'name' => 'afeb_template_type_filter'
            ], ['values' => $options, 'selected' => $selected]);
        }
    }

    public function apply_filters($query)
    {
        global $pagenow, $typenow;

        if ($typenow == self::BUILDER_POST_TYPE && $pagenow == 'edit.php') {
            if (!empty($_GET['afeb_template_type_filter'])) {
                $query->query_vars['meta_query'] = [
                    [
                        'key' => '_afeb_document_type',
                        'value' => sanitize_text_field(wp_unslash($_GET['afeb_template_type_filter'])),
                        'compare' => '=',
                    ],
                ];
            }
        }
    }

    /**
     * Add elementor support
     * 
     * @since 1.3.0
     * 
     * @param string $value
     * 
     * @return array
     */
    public function set_option_support($value)
    {
        return !empty($value) ? array_merge($value, [self::BUILDER_POST_TYPE]) : [];
    }

    /**
     * Change current page body class
     * 
     * @since 1.3.0
     * 
     * @param string $classes
     * 
     * @return string
     */
    public function change_body_class($classes)
    {
        return $classes . ' afeb-body afeb-builder-posts-body';
    }

    /**
     * Filters the array of row action links
     * 
     * @since 1.3.0
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
     * Add a builder creation dialog to the page
     * 
     * @since 1.3.0
     */
    public function new_builder_dialog()
    {
        $this->get_template_part($this->get_path() . '/html/admin/menus/builder/dialogs/new-template/');
    }
}
