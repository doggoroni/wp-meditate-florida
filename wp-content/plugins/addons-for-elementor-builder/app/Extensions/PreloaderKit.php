<?php

namespace AFEB\Extensions;

use AFEB\Controls\Helper as CHelper;
use Elementor\Core\Kits\Documents\Tabs\Tab_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PreloaderKit Extension Class
 * 
 * @class PreloaderKit
 * @version 1.5.0
 */
class PreloaderKit extends Tab_Base
{
    /**
     * @var CHelper
     */
    private $controls;

    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
    }

    /**
     * Get extension id
     *
     * @since 1.5.0
     *
     * @return string Extension id
     */
    public function get_id()
    {
        return 'afeb_preloader_kit_settings';
    }

    /**
     * Get extension title
     *
     * @since 1.5.0
     *
     * @return string Extension title
     */
    public function get_title()
    {
        return esc_html__('Preloader', 'addons-for-elementor-builder');
    }

    /**
     * Get extension icon
     *
     * @since 1.5.0
     *
     * @return string Extension icon
     */
    public function get_icon()
    {
        return 'eicon-page-transition';
    }

    /**
     * Register PreloaderKit extension tab controls
     *
     * @since 1.5.0
     */
    protected function register_tab_controls()
    {
        $this->controls = new CHelper($this);
        $this->controls->section('afeb-stng-ext-preloader', [
            'label' => esc_html__('Preloader', 'addons-for-elementor-builder'),
            'tab' => $this->get_id(),
        ], function () {

            $this->controls->select('afeb_preloader_loader', [
                'label' => esc_html__('Preloader', 'addons-for-elementor-builder'),
                'options' => [
                    'none' => esc_html__('Disable', 'addons-for-elementor-builder'),
                    'transition' => esc_html__('Only Page Transition', 'addons-for-elementor-builder'),
                    'animation' => esc_html__('Animation', 'addons-for-elementor-builder'),
                ],
                'default' => 'none'
            ]);

            $this->controls->select('afeb_preloader_loader_animation', [
                'label' => esc_html__('Type', 'addons-for-elementor-builder'),
                'options' => [
                    'modern' => esc_html__('Modern', 'addons-for-elementor-builder'),
                    'whirlwind' => esc_html__('Whirlwind', 'addons-for-elementor-builder'),
                    'speedster' => esc_html__('Speedster', 'addons-for-elementor-builder'),
                    'worm-crawl' => esc_html__('WormCrawl', 'addons-for-elementor-builder'),
                ],
                'default' => 'modern',
                'condition' => ['afeb_preloader_loader' => 'animation',],
            ]);

            $this->controls->background([
                'name' => 'afeb_preloader_loader_animation_background',
                'types' => ['classic'],
                'exclude' => ['image', 'video'],
                'fields_options' => [
                    'background' => [
                        'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                        'default' => 'classic',
                    ],
                    'color' => ['default' => '#5936c6',],
                ],
                'selector' => '{{WRAPPER}} .afeb-modern-loader .afeb-bar,' .
                    '{{WRAPPER}} .afeb-worm-crawl-loader .afeb-circle:before',
                'condition' => [
                    'afeb_preloader_loader' => 'animation',
                    'afeb_preloader_loader_animation' => ['modern', 'worm-crawl'],
                ],
            ]);

            $this->controls->color('afeb_preloader_loader_animation_color', [
                'default' => '#5936c6',
                'selectors' => [
                    '{{WRAPPER}} .afeb-whirlwind-loader:after,' .
                        '{{WRAPPER}} .afeb-whirlwind-loader:before,' .
                        '{{WRAPPER}} .afeb-whirlwind-loader,' .
                        '{{WRAPPER}} .afeb-speedster-loader .afeb-bar::before' => 'border-top-color: {{VALUE}}',
                ],
                'condition' => [
                    'afeb_preloader_loader' => 'animation',
                    'afeb_preloader_loader_animation' => ['whirlwind', 'speedster'],
                ],
            ]);

            $this->controls->slider('afeb_preloader_animation_scale', [
                'label' => esc_html__('Scale', 'addons-for-elementor-builder'),
                'size_units' => ['ms'],
                'default' => ['unit' => 'ms', 'size' => 1,],
                'range' => ['ms' => ['min' => 0, 'max' => 10, 'step' => 0.1],],
                'selectors' => ['{{WRAPPER}} .afeb-preloader .afeb-loader' => 'transform: scale({{SIZE}})',],
                'condition' => ['afeb_preloader_loader' => 'animation',],
            ]);

            $this->controls->select('afeb_preloader_entrance_animation', [
                'label' => esc_html__('Entrance Animation', 'addons-for-elementor-builder'),
                'options' => [
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                    'fade-in' => esc_html__('Fade In', 'addons-for-elementor-builder'),
                    'slide-down' => esc_html__('Slide Down', 'addons-for-elementor-builder'),
                ],
                'default' => 'fade-in',
                'condition' => ['afeb_preloader_loader!' => 'none',],
            ]);

            $this->controls->select('afeb_preloader_exit_animation', [
                'label' => esc_html__('Exit Animation', 'addons-for-elementor-builder'),
                'options' => [
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                    'fade-out' => esc_html__('Fade Out', 'addons-for-elementor-builder'),
                    'slide-up' => esc_html__('Slide Up', 'addons-for-elementor-builder'),
                ],
                'default' => 'fade-out',
                'condition' => ['afeb_preloader_loader!' => 'none',],
            ]);

            $this->controls->slider('afeb_preloader_animation_duration', [
                'label' => esc_html__('Slow Motion Speed', 'addons-for-elementor-builder'),
                'size_units' => ['ms'],
                'default' => ['unit' => 'ms', 'size' => 300,],
                'range' => ['ms' => ['max' => 600,],],
                'condition' => ['afeb_preloader_loader!' => 'none',],
            ]);

            $this->controls->background([
                'name' => 'afeb_preloader_background',
                'types' => ['classic'],
                'exclude' => ['image', 'video'],
                'fields_options' => [
                    'background' => [
                        'label' => esc_html__('Background', 'addons-for-elementor-builder'),
                        'default' => 'classic',
                        // 'description' => esc_html__('This is the page color behind your loading animation', 'addons-for-elementor-builder'),
                    ],
                    'color' => [
                        'label' => esc_html__('Background Color', 'elementor'),
                        'default' => '#fff',
                    ],
                ],
                'selector' => '{{WRAPPER}} .afeb-preloader',
                'condition' => ['afeb_preloader_loader!' => 'none',],
            ]);
        });
    }
}
