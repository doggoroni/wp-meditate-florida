<?php

namespace AFEB\Documents;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use AFEB\PostTypes\Builder as Builder_PostType;
use AFEB\Widgets;
use Elementor\Core\Base\Document;
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
class Builder extends Document
{
    /**
     * Builder Document Key
     */
    const BUILDER_DOCUMENT = 'afeb-builder';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * @var Widgets
     */
    private $widgets;

    /**
     * Builder Constructor
     * 
     * @since 1.3.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->widgets = new Widgets();

        if (Helper::is_edit_mode()) {
            $this->assets->editor_template_builder_style();
        }

        add_action('elementor/widgets/register', function ($widgets_manager) {
            $options = array_merge(
                (array) get_option('afeb-widgets-status', []),
                (array) get_option('afeb-3rdpt-widgets-status', [])
            );
            $widgets = $this->widgets->get_template_builder_widgets();
            $widgets = array_replace_recursive($widgets, $options);

            $this->widgets->register_widgets($widgets, $widgets_manager, 'TemplateBuilder');

            $post_id = !empty($_REQUEST['post']) ? intval($_REQUEST['post']) : -1;
            $document_type = str_replace('-', '_', get_post_meta($post_id, '_afeb_document_type', true));
            do_action("afeb/widgets/after_register_{$document_type}_widgets", $widgets_manager);
        });
    }

    /**
     * Get document name
     *
     * @since 1.3.0
     *
     * @return string Document name
     */
    public function get_name()
    {
        return self::BUILDER_DOCUMENT;
    }

    /**
     * Get document type
     *
     * @since 1.3.0
     *
     * @return string Document type
     */
    public static function get_type()
    {
        return self::BUILDER_DOCUMENT;
    }

    /**
     * Get document title
     *
     * @since 1.3.0
     *
     * @return string Document title
     */
    public static function get_title()
    {
        return esc_html__('Builder', 'addons-for-elementor-builder');
    }

    /**
     * Get document properties
     *
     * @since 1.3.0
     *
     * @return array Document properties
     */
    public static function get_properties()
    {
        $properties = parent::get_properties();

        $properties['admin_tab_group'] = '';
        $properties['support_kit'] = true;

        return $properties;
    }

    /**
     * Get editor panel categories
     *
     * @since 1.3.0
     *
     * @return array Panel Categories
     */
    protected static function get_editor_panel_categories()
    {
        $document_type = get_post_meta(get_the_ID(), '_afeb_document_type', true);
        $categories = [];

        switch ($document_type) {
            case 'archive':
                $categories['theme-elements-archive'] = ['title' => esc_html__('Archive', 'addons-for-elementor-builder')];
                break;
            case 'dynamic-loop-item':
                $categories['recommended'] = ['title' => esc_html__('Recommended', 'addons-for-elementor-builder')];
                break;
            case 'footer':
            case 'header':
                $categories['theme-elements'] = ['title' => esc_html__('Site', 'addons-for-elementor-builder')];
                break;
            case 'single':
                $categories['theme-elements-single'] = ['title' => esc_html__('Single', 'addons-for-elementor-builder')];
                break;
        }

        return $categories + parent::get_editor_panel_categories();
    }

    /**
     * Register Builder document controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        parent::register_controls();

        /**
         *
         * Layout
         *
         */
        $this->controls->tab_settings_section('layout_content_section', [
            'label' => esc_html__('Layout', 'addons-for-elementor-builder')
        ], function () {
            $document_type = get_post_meta(get_the_ID(), '_afeb_document_type', true);

            $select_items_groups = [];
            $select_default_item = '';

            if ($document_type != 'single') {
                $select_items_groups['archive'] = [
                    'label' => esc_html__('Archives', 'addons-for-elementor-builder'),
                    'options' => [
                        'archive_post' => esc_html__('Posts Archive', 'addons-for-elementor-builder'),
                        'archive_author' => esc_html__('Author Archive', 'addons-for-elementor-builder'),
                        'archive_date' => esc_html__('Date Archive', 'addons-for-elementor-builder'),
                        'archive_search' => esc_html__('Search Results', 'addons-for-elementor-builder'),
                    ],
                ];

                $select_default_item = 'archive_author';
            }

            if ($document_type != 'archive') {
                $select_items_groups['single'] = [
                    'label' => esc_html__('Single', 'addons-for-elementor-builder'),
                    'options' => [
                        'single_post' => esc_html__('Posts', 'addons-for-elementor-builder'),
                        'single_page' => esc_html__('Pages', 'addons-for-elementor-builder'),
                    ],
                ];

                $select_default_item = 'single_post';
            }

            $this->controls->select('preview_source', [
                'label' => esc_html__('Preview Source', 'addons-for-elementor-builder'),
                'groups' => $select_items_groups,
                'default' => $select_default_item,
            ]);

            $wp_users = Helper::get_users();
            reset($wp_users);
            $first_user_id = key($wp_users);

            if ($document_type != 'single') {
                $this->controls->select2('archive_author_source', [
                    'label' => esc_html__('Select Author', 'addons-for-elementor-builder'),
                    'options' => Helper::get_users(),
                    'default' => $first_user_id,
                    'separator' => 'before',
                    'condition' => ['preview_source' => 'archive_author']
                ]);

                $this->controls->text('archive_search_source', [
                    'label' => esc_html__('Search Keyword', 'addons-for-elementor-builder'),
                    'separator' => 'before',
                    'dynamic' => ['active' => true,],
                    'ai' => ['active' => false,],
                    'condition' => ['preview_source' => 'archive_search',],
                ]);
            }

            if ($document_type != 'archive') {
                $this->controls->dynamic_select('single_posts_source', [
                    'label' => esc_html__('Select Post', 'addons-for-elementor-builder'),
                    'options' => 'get_posts_by_type',
                    'query_slug' => 'post',
                    'default' => '',
                    'condition' => ['preview_source' => 'single_post',]
                ]);

                $this->controls->dynamic_select('single_pages_source', [
                    'label' => esc_html__('Select Page', 'addons-for-elementor-builder'),
                    'options' => 'get_posts_by_type',
                    'query_slug' => 'page',
                    'default' => '',
                    'condition' => ['preview_source' => 'single_page',]
                ]);
            }

            $this->controls->raw_html('apply_preview', [
                'raw' => sprintf(
                    '<div class="afeb-update-preview elementor-update-preview"><span>%s</span><button class="elementor-button elementor-button-success">%s</button>',
                    esc_html__('Update changes to Preview', 'addons-for-elementor-builder'),
                    esc_html__('Apply', 'addons-for-elementor-builder')
                ),
            ]);

            switch ($document_type) {
                case 'archive':
                case 'single':

                    /**
                     *
                     * Page Layout
                     *
                     */
                    $this->controls->divider('separator_layout_section');

                    $this->controls->select('template_type', [
                        'label' => esc_html__('Page Layout', 'addons-for-elementor-builder'),
                        'options' => [
                            'default' => esc_html__('Default', 'addons-for-elementor-builder'),
                            'elementor_canvas' => esc_html__('Elementor Canvas', 'addons-for-elementor-builder'),
                            'elementor_header_footer' => esc_html__('Elementor Full Width', 'addons-for-elementor-builder'),
                        ],
                        'default' => 'default',
                    ]);

                    $this->controls->hidden('page_id', [
                        'default' => get_the_ID(),
                    ]);

                    $this->controls->raw_html('canvas_description', [
                        'raw' => esc_html__('No header, no footer, just Elementor', 'addons-for-elementor-builder'),
                        'content_classes' => 'elementor-descriptor',
                        'condition' => [
                            'template_type' => 'elementor_canvas',
                        ]
                    ]);

                    $this->controls->raw_html('header_footer_description', [
                        'raw' => esc_html__('This template includes the header, full-width content and footer', 'addons-for-elementor-builder'),
                        'content_classes' => 'elementor-descriptor',
                        'condition' => [
                            'template_type' => 'elementor_header_footer',
                        ]
                    ]);

                    break;
            }

            switch ($document_type) {
                case 'dynamic-loop-item':

                    $this->controls->slider('preview_width', [
                        'label' => esc_html__('Preview Width (vw)', 'addons-for-elementor-builder'),
                        'range' => ['px' => ['min' => 25, 'max' => 100,],],
                        'selectors' => ['{{WRAPPER}} .afeb-dynamic-loop-item-container' => 'width: {{SIZE}}vw',],
                        'separator' => 'before',
                    ]);

                    break;
            }
        });
    }

    /**
     * Get document query args
     *
     * @since 1.3.0
     *
     * @return array Query ARGS
     */
    public function get_document_query_args()
    {
        $settings = $this->get_settings();
        $source = $settings['preview_source'];
        $default_args = ['post_type' => Builder_PostType::BUILDER_POST_TYPE, 'p' => get_the_ID()];
        $args = $default_args;

        $get_posts_args = [
            'post_type' => 'post',
            'numberposts' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'suppress_filters' => false,
        ];

        $posts = get_posts($get_posts_args);

        if (strpos($source, 'archive_') !== false) {
            if ($source == 'archive_post') {
                $archive_post = isset($posts[0]->ID) ? $posts[0]->ID : '';

                if (is_int($archive_post))
                    $args = ['post_type' => 'post', 'p' => $archive_post];
            } elseif ($source == 'archive_author') {
                $args = ['author' => intval($settings['archive_author_source'])];
            } elseif ($source == 'archive_date') {
                $args = ['post_type' => 'date'];
            } elseif ($source == 'archive_search') {
                $args = ['s' => sanitize_text_field($settings['archive_search_source'])];
            }
        }

        if (strpos($source, 'single_') !== false) {
            $single_post = isset($posts[0]->ID) ? $posts[0]->ID : '';

            if (is_int($single_post))
                $args = ['post_type' => 'post', 'p' => $single_post];

            switch ($source) {
                case 'single_post':
                    $single_post = isset($posts[0]->ID) ? $posts[0]->ID : '';
                    $single_post = trim($settings['single_posts_source']) ? intval($settings['single_posts_source']) : $single_post;

                    if (is_int($single_post))
                        $args = ['post_type' => 'post', 'p' => $single_post];

                    break;
                case 'single_page':
                    $pages = get_posts([
                        'post_type' => 'page',
                        'numberposts' => 1,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'suppress_filters' => false,
                    ]);

                    $single_page = isset($pages[0]->ID) ? $pages[0]->ID : '';
                    $single_page = trim($settings['single_pages_source']) ? intval($settings['single_pages_source']) : $single_page;

                    $args = is_int($single_page) ? ['post_type' => 'page', 'p' => $single_page] : $default_args;
                    break;
            }
        }

        return $args;
    }

    /**
     * Switch to preview query
     *
     * @since 1.3.0
     */
    public function switch_to_preview_query()
    {
        if (get_post_type(get_the_ID()) == Builder_PostType::BUILDER_POST_TYPE) {
            $document = Plugin::instance()->documents->get_doc_or_auto_save(get_the_ID());
            Plugin::instance()->db->switch_to_query($document->get_document_query_args());
        }
    }

    /**
     * Get Builder document content
     *
     * @since 1.3.0
     *
     * @return string document content
     */
    public function get_content($with_css = false)
    {
        $this->switch_to_preview_query();

        $content = parent::get_content($with_css);
        Plugin::instance()->db->restore_current_query();

        return $content;
    }

    /**
     * Print Content
     *
     * @since 1.3.0
     */
    public function print_content()
    {
        $plugin = Plugin::instance();

        if ($plugin->preview->is_preview_mode($this->get_main_id())) echo '' . $plugin->preview->builder_wrapper(''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        else echo '' . $this->get_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Get Builder document default preview
     *
     * @since 1.3.0
     *
     * @return string Default Preview Options
     */
    public static function get_preview_as_default()
    {
        return '';
    }

    /**
     * Get Builder document preview options
     *
     * @since 1.3.0
     *
     * @return array Preview Options
     */
    public static function get_preview_as_options()
    {
        return [];
    }

    /**
     * Get elements raw data
     *
     * @since 1.3.0
     * 
     * @param null $data
     * @param bool $with_html_content
     *
     * @return array
     */
    public function get_elements_raw_data($data = null, $with_html_content = false)
    {

        $this->switch_to_preview_query();

        $editor_data = parent::get_elements_raw_data($data, $with_html_content);
        Plugin::instance()->db->restore_current_query();

        return $editor_data;
    }

    /**
     * Get elements raw data
     *
     * @since 1.3.0
     * 
     * @param null $data
     *
     * @return string
     */
    public function render_element($data)
    {
        $this->switch_to_preview_query();

        $render_html = parent::render_element($data);
        Plugin::instance()->db->restore_current_query();

        return $render_html;
    }
}
