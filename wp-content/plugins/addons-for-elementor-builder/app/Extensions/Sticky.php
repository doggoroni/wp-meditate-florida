<?php

namespace AFEB\Extensions;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Core\Base\Module;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Sticky Extension Class
 * 
 * @class Sticky
 * @version 1.4.0
 */
class Sticky extends Module
{
    /**
     * @var CHelper
     */
    private $controls;

    /**
     * Sticky Constructor
     * 
     * @since 1.4.0
     */
    public function __construct()
    {
        parent::__construct();
        $this->actions();
    }

    /**
     * Sticky Class Actions
     * 
     * @since 1.4.0
     */
    protected function actions()
    {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_controls']);
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'register_controls']);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_controls']);
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_controls']);

        // render scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Add required scripts and styles
     *
     * @since 1.4.0
     */
    public function enqueue_scripts()
    {
        $assets = new Assets();
        $assets->sticky_pkg_script();
        $assets->sticky_script();
    }

    public function get_name()
    {
        return 'afeb-sticky';
    }

    public function position_options($context) {}

    public function sticky_on_options($context) {}

    public function effects_offset_options($context) {}

    /**
     * Register Sticky extension controls
     *
     * @since 1.4.0
     * 
     * @param object $obj
     */
    public function register_controls($widget)
    {
        $this->controls = new CHelper($widget);
        $this->controls->tab_advanced_section(
            'afeb-ext-sticky',
            ['label' => esc_html__('Sticky', 'addons-for-elementor-builder'),],
            function () {
                $this->controls->raw_html('afeb_sticky_panal_notice', [
                    'raw' => sprintf(
                        '<strong> %s </strong> %s',
                        esc_html__('Sticky', 'addons-for-elementor-builder'),
                        esc_html__('Does not work in Editor page', 'addons-for-elementor-builder')
                    ),
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                    'condition' => ['afeb_sticky_enable' => 'yes',],
                ]);

                $this->controls->yn_switcher('afeb_sticky_enable', [
                    'label' => esc_html__('Enable Sticky', 'addons-for-elementor-builder'),
                    'return_value' => 'yes',
                    'frontend_available' => true,
                    'render_type' => 'template',
                ]);

                $this->position_options($this->controls);

                $this->controls->yn_switcher('afeb_sticky_stay_in_column', [
                    'label' => esc_html__('Stay In Column', 'addons-for-elementor-builder'),
                    'return_value' => 'yes',
                    'frontend_available' => true,
                    'render_type' => 'template',
                    'condition' => ['afeb_sticky_enable' => 'yes',],
                ]);

                $this->sticky_on_options($this->controls);

                $this->controls->responsive()->number('afeb_sticky_top_spacing', [
                    'label' => esc_html__('Top Spacing', 'addons-for-elementor-builder') . ' (px)',
                    'max' => 500,
                    'default' => 0,
                    'required' => true,
                    'frontend_available' => true,
                    'render_type' => 'templat',
                    'condition' => ['afeb_sticky_enable' => 'yes',],
                ]);

                $this->effects_offset_options($this->controls);
            }
        );
    }
}
