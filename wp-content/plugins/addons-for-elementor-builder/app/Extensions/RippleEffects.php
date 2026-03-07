<?php

namespace AFEB\Extensions;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Core\Base\Module;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" RippleEffects Extension Class
 * 
 * @class RippleEffects
 * @version 1.5.0
 */
class RippleEffects extends Module
{
    /**
     * @var CHelper
     */
    private $controls;

    /**
     * RippleEffects Constructor
     * 
     * @since 1.5.0
     */
    public function __construct()
    {
        parent::__construct();
        $this->actions();
    }

    /**
     * RippleEffects Class Actions
     * 
     * @since 1.5.0
     */
    protected function actions()
    {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_controls']);
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_controls']);

        // render scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        // add_action('elementor/frontend/widget/before_render', [$this, 'enqueue_scripts']);
        // add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Add required scripts and styles
     *
     * @since 1.5.0
     */
    public function enqueue_scripts()
    {
        $assets = new Assets();
        $assets->ripple_effects_pkg_script();
        $assets->ripple_effects_style();
        $assets->ripple_effects_script();
    }

    public function get_name()
    {
        return 'afeb-ripple-effects';
    }

    /**
     * Register RippleEffects extension controls
     *
     * @since 1.5.0
     * 
     * @param object $obj
     */
    public function register_controls($widget)
    {
        $this->controls = new CHelper($widget);
        $this->controls->tab_advanced_section(
            'afeb-ext-ripple-effects',
            ['label' => esc_html__('Ripple Effects', 'addons-for-elementor-builder'),],
            function () {

                $this->controls->yn_switcher('afeb_ripple_enable', [
                    'label' => esc_html__('Enable Ripple Effects', 'addons-for-elementor-builder'),
                    'return_value' => 'yes',
                    'frontend_available' => true,
                    'render_type' => 'template',
                ]);

                $this->controls->select('afeb_ripple_selector', [
                    'label' => esc_html__('Ripple Effects For', 'addons-for-elementor-builder'),
                    'options' => [
                        'container' => esc_html__('Container', 'addons-for-elementor-builder'),
                        'widgets' => esc_html__('Container -> Widgets', 'addons-for-elementor-builder'),
                        'buttons' => esc_html__('Container -> Buttons', 'addons-for-elementor-builder'),
                        'images' => esc_html__('Container -> Images', 'addons-for-elementor-builder'),
                        'both' => esc_html__('Container -> Images and Buttons', 'addons-for-elementor-builder'),
                    ],
                    'default' => 'widgets',
                    'label_block' => true,
                    'frontend_available' => true,
                    'render_type' => 'template',
                    'condition' => ['afeb_ripple_enable' => 'yes',],
                ]);

                $this->controls->hidden('afeb_ripple_custom_selector', [
                    'default' => '',
                    'frontend_available' => true,
                    'render_type' => 'template',
                    'condition' => ['afeb_ripple_enable' => 'yes', 'afeb_ripple_selector' => 'custom',],
                ]);

                $this->controls->hidden('afeb_ripple_on', [
                    'default' => 'click',
                    'frontend_available' => true,
                    'render_type' => 'template',
                    'condition' => ['afeb_ripple_enable' => 'yes',],
                ]);

                $this->controls->hidden('afeb_ripple_easing', [
                    'default' => 'linear',
                    'frontend_available' => true,
                    'render_type' => 'template',
                    'condition' => ['afeb_ripple_enable' => 'yes',],
                ]);

                $this->controls->slider('afeb_ripple_duration', [
                    'label' => esc_html__('Duration', 'addons-for-elementor-builder'),
                    'default' => ['size' => 0.7,],
                    'size_units' => ['px'],
                    'range' => ['px' => ['max' => 10, 'min' => 0.1, 'step' => 0.1,],],
                    'condition' => ['afeb_ripple_enable' => 'yes',],
                    'frontend_available' => true,
                    'render_type' => 'template',
                ]);

                $this->controls->slider('afeb_ripple_opacity', [
                    'label' => esc_html__('Opacity', 'addons-for-elementor-builder'),
                    'default' => ['size' => 0.4,],
                    'size_units' => ['px'],
                    'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.1,],],
                    'condition' => ['afeb_ripple_enable' => 'yes',],
                    'frontend_available' => true,
                    'render_type' => 'template',
                ]);

                $this->controls->color('afeb_ripple_color', [
                    'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                    'default' => '#c5c5c5',
                    'frontend_available' => true,
                    'render_type' => 'template',
                    'condition' => ['afeb_ripple_enable' => 'yes',],
                ]);
            }
        );
    }
}
