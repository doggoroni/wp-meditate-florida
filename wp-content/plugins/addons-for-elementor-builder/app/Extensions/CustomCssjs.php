<?php

namespace AFEB\Extensions;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use AFEB\Security;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" CustomCssjs Extension Class
 *
 * @class CustomCssjs
 * @version 1.0.3
 */
class CustomCssjs
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * @var Security
     */
    private $security;

    /**
     * CustomCssjs Constructor
     *
     * @since 1.0.3
     */
    public function __construct()
    {
        $this->assets = new Assets();
        $this->security = new Security();
        $this->actions();
    }

    /**
     * CustomCssjs Class Actions
     *
     * @since 1.0.3
     */
    public function actions()
    {
        add_action('elementor/element/after_section_end', [$this, 'register_controls'], 10, 2);

        add_action('elementor/element/parse_css', [$this, 'add_post_css'], 10, 2);
        add_action('elementor/css-file/post/parse', [$this, 'add_page_settings_css']);

        add_action('wp_enqueue_scripts', [$this, 'header_script'], 999);
        add_action('wp_enqueue_scripts', [$this, 'footer_script'], 999);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Add required scripts and styles
     *
     * @since 1.5.0
     */
    public function enqueue_scripts()
    {
        $this->assets->custom_css_js();
    }

    /**
     * Register CustomCssjs extension controls
     *
     * @param object $widget
     * @param string $section_id
     * @since 1.0.3
     */
    public function register_controls($widget, $section_id)
    {
        // Remove Custom CSS Banner (From elementor free version)
        if ('section_custom_css_pro' !== $section_id) {
            return;
        }

        $this->controls = new CHelper($widget);
        if ($widget->get_type() == 'wp-page') {
            $this->controls->tab_advanced_section(
                'afeb-ext-custom-css-js',
                ['label' => esc_html__('Custom CSS/JS', 'addons-for-elementor-builder'),],
                function () {
                    $this->controls->tabs('afeb_icn_tb_cntrl', [
                        'cstm_cssjs_css_tab' => [
                            'label' => esc_html__('CSS', 'addons-for-elementor-builder'),
                            'callback' => function () {
                                $this->controls->code('afeb_cstm_hdr_css', [
                                    'label' => esc_html__('CSS', 'addons-for-elementor-builder'),
                                ]);
                            },
                        ],
                        'cstm_cssjs_js_tab' => [
                            'label' => esc_html__('JS', 'addons-for-elementor-builder'),
                            'callback' => function () {
                                $this->controls->code('afeb_cstm_hdr_js', [
                                    'label' => esc_html__('Header JS', 'addons-for-elementor-builder'),
                                    'language' => 'javascript',
                                    'render_type' => 'ui',
                                ]);

                                $this->controls->code('afeb_cstm_ftr_js', [
                                    'label' => esc_html__('Footer JS', 'addons-for-elementor-builder'),
                                    'language' => 'javascript',
                                    'render_type' => 'ui',
                                ]);
                            },
                        ],
                    ]);
                }
            );
        } else {
            $this->controls->tab_advanced_section(
                'afeb-ext-custom-css',
                ['label' => esc_html__('Custom CSS', 'addons-for-elementor-builder'),],
                function () {
                    $this->controls->code('afeb_cstm_hdr_css', [
                        'label' => esc_html__('CSS', 'addons-for-elementor-builder'),
                    ]);
                }
            );
        }
    }

    /**
     * Add custom style
     *
     * @return void
     * @since 1.0.3
     *
     */
    public function add_post_css($post_css, $element)
    {
        if ($post_css instanceof Dynamic_CSS) {
            return;
        }

        $element_settings = $element->get_settings();

        if (empty($element_settings['afeb_cstm_hdr_css'])) {
            return;
        }

        $css = trim($element_settings['afeb_cstm_hdr_css']);

        if (empty($css)) {
            return;
        }

        $css = str_replace('selector', $post_css->get_element_unique_selector($element), $css);
        $css = sprintf('/* Start custom CSS for %s, class: %s */', $element->get_name(), $element->get_unique_selector()) . $css . '/* End custom CSS */';
        $post_css->get_stylesheet()->add_raw_css($css);
    }

    public function add_page_settings_css($post_css)
    {
        $document = Plugin::instance()->documents->get($post_css->get_post_id());
        $custom_css = $document->get_settings('afeb_cstm_hdr_css') ?? '';
        $custom_css = trim($custom_css);

        if (empty($custom_css)) {
            return;
        }

        $custom_css = str_replace('selector', $document->get_css_wrapper_selector(), $custom_css);
        $custom_css = '/* Start custom CSS */' . $custom_css . '/* End custom CSS */';
        $post_css->get_stylesheet()->add_raw_css($custom_css);
    }

    /**
     * Add custom script to header
     *
     * @return void
     * @since 1.0.3
     *
     */
    public function header_script()
    {
        // Elementor Not Installed
        if (!class_exists(Plugin::class)) return;

        $document = Plugin::instance()->documents->get(get_the_ID());
        if (!$document) return;
        $custom_js = $document->get_settings('afeb_cstm_hdr_js');
        if (empty($custom_js)) return;

        $this->assets->custom_header_script();
        wp_add_inline_script('afeb-custom-header-script', $custom_js);
    }

    /**
     * Add custom script to footer
     *
     * @return void
     * @since 1.0.3
     *
     */
    public function footer_script()
    {
        // Elementor Not Installed
        if (!class_exists(Plugin::class)) return;

        $document = Plugin::instance()->documents->get(get_the_ID());
        if (!$document) return;
        $custom_js = $document->get_settings('afeb_cstm_ftr_js');
        if (empty($custom_js)) return;

        $this->assets->custom_footer_script();
        wp_add_inline_script('afeb-custom-footer-script', $custom_js);
    }
}
