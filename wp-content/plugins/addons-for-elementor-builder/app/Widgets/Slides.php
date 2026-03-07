<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Slides Widget Class
 * 
 * @class Slides
 * @version 1.5.0
 */
class Slides extends Widget_Base
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
     * Slides Constructor
     * 
     * @since 1.5.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->slick_pkg();
        $this->assets->slides_style();
        $this->assets->slides_script();
    }

    /**
     * Get widget name
     *
     * @since 1.5.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_slides';
    }

    /**
     * Get widget title
     *
     * @since 1.5.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html('Slides', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.5.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-slides';
    }

    /**
     * Get widget categories
     *
     * @since 1.5.0
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['afeb_basic'];
    }

    /**
     * Get widget keywords
     *
     * @since 1.5.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return [];
    }

    /**
     * Retrieve the list of style dependencies the widget requires
     *
     * @since 1.5.0
     *
     * @return array Widget style dependencies
     */
    public function get_style_depends(): array
    {
        return ['afeb-slides-style'];
    }

    /**
     * Retrieve the list of script dependencies the widget requires
     *
     * @since 1.5.0
     *
     * @return array Widget script dependencies
     */
    public function get_script_depends()
    {
        return ['afeb-slides-script'];
    }

    /**
     * Register Slides widget controls
     *
     * @since 1.5.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('slides_content_section', [
            'label' => esc_html__('Slides', 'addons-for-elementor-builder'),
        ], function () {
            $repeater = new Repeater();
            $repeater_controls = new CHelper($repeater);
            $GLOBALS['afeb_repeater_controls'] = $repeater_controls;

            $repeater_controls->tabs('slides_content_tab', [
                'slides_content_background' => [
                    'label' => esc_html__('Background', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $repeater_controls = $GLOBALS['afeb_repeater_controls'];

                        $repeater_controls->color('slides_background_color', [
                            'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
                            'default' => '#bbbbbb',
                            'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-item-bg' => 'background-color: {{VALUE}}',],
                        ]);

                        $repeater_controls->media('slides_background_image', [
                            'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-item-bg' => 'background-image: url({{URL}})',],
                        ]);
                    },
                ],
                'slides_content_content' => [
                    'label' => esc_html__('Content', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $repeater_controls = $GLOBALS['afeb_repeater_controls'];
                        $repeater_controls->text('heading', [
                            'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                            'label_block' => false,
                            'ai' => ['active' => false,],
                        ]);

                        $repeater_controls->text_area('description', [
                            'label' => esc_html__('Description', 'addons-for-elementor-builder'),
                            'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'addons-for-elementor-builder'),
                            'ai' => ['active' => false,],
                            'rows' => 5,
                        ]);

                        $repeater_controls->text('button_text', [
                            'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                            'ai' => ['active' => false,],
                        ]);

                        $repeater_controls->url('link', ['label_block' => false,]);

                        $repeater_controls->select('link_click', [
                            'label' => esc_html__('Apply Link On', 'addons-for-elementor-builder'),
                            'options' => [
                                'slide' => esc_html__('Whole Slide', 'addons-for-elementor-builder'),
                                'button' => esc_html__('Button Only', 'addons-for-elementor-builder'),
                            ],
                            'default' => 'slide',
                            'condition' => ['link[url]!' => '']
                        ]);
                    },
                ],
                'slides_content_style' => [
                    'label' => esc_html__('Style', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $repeater_controls = $GLOBALS['afeb_repeater_controls'];
                        $repeater_controls->sh_switcher('custom_style', [
                            'label' => esc_html__('Custom', 'addons-for-elementor-builder'),
                            'description' => esc_html__('Set custom style that will only affect this specific slide.', 'addons-for-elementor-builder'),
                        ]);

                        $repeater_controls->choose('horizontal_position', [
                            'label' => esc_html__('Horizontal Position', 'addons-for-elementor-builder'),
                            'options' => [
                                'left' => [
                                    'title' => esc_html__('Left', 'addons-for-elementor-builder'),
                                    'icon' => 'eicon-h-align-left',
                                ],
                                'center' => [
                                    'title' => esc_html__('Center', 'addons-for-elementor-builder'),
                                    'icon' => 'eicon-h-align-center',
                                ],
                                'right' => [
                                    'title' => esc_html__('Right', 'addons-for-elementor-builder'),
                                    'icon' => 'eicon-h-align-right',
                                ],
                            ],
                            'selectors' => [
                                '{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-contents' => '{{VALUE}}',
                            ],
                            'selectors_dictionary' => [
                                'left' => 'margin-right: auto',
                                'center' => 'margin: 0 auto',
                                'right' => 'margin-left: auto',
                            ],
                            'condition' => ['custom_style' => 'yes'],
                        ]);

                        $repeater_controls->choose('vertical_position', [
                            'label' => esc_html__('Vertical Position', 'addons-for-elementor-builder'),
                            'options' => [
                                'top' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-top',],
                                'middle' => ['title' => esc_html__('Middle', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-middle',],
                                'bottom' => ['title' => esc_html__('Bottom', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-bottom',],
                            ],
                            'selectors' => [
                                '{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-item-inner' => 'align-items: {{VALUE}}',
                            ],
                            'selectors_dictionary' => [
                                'top' => 'flex-start',
                                'middle' => 'center',
                                'bottom' => 'flex-end',
                            ],
                            'condition' => ['custom_style' => 'yes'],
                        ]);

                        $repeater_controls->choose('text_align', [
                            'label' => esc_html__('Text Align', 'addons-for-elementor-builder'),
                            'options' => [
                                'left' => [
                                    'title' => esc_html__('Left', 'addons-for-elementor-builder'),
                                    'icon' => 'eicon-text-align-left',
                                ],
                                'center' => [
                                    'title' => esc_html__('Center', 'addons-for-elementor-builder'),
                                    'icon' => 'eicon-text-align-center',
                                ],
                                'right' => [
                                    'title' => esc_html__('Right', 'addons-for-elementor-builder'),
                                    'icon' => 'eicon-text-align-right',
                                ],
                            ],
                            'selectors' => [
                                '{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-item-inner' => 'text-align: {{VALUE}}',
                            ],
                            'condition' => ['custom_style' => 'yes'],
                        ]);

                        $repeater_controls->color('slides_content_color', [
                            'selectors' => [
                                '{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-item-inner .afeb-slides-item-heading' => 'color: {{VALUE}}',
                                '{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-item-inner .afeb-slides-item-description' => 'color: {{VALUE}}',
                                '{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-item-inner .afeb-slides-item-button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                            ],
                            'condition' => ['custom_style' => 'yes'],
                        ]);

                        $repeater_controls->text_shadow([
                            'name' => 'slides_content_color',
                            'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .afeb-slides-contents',
                            'condition' => ['custom_style' => 'yes'],
                        ]);
                    },
                ],
            ]);

            $this->controls->repeater('slides_content_repeater', [
                'label' => esc_html__('Slides', 'addons-for-elementor-builder'),
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'heading' => esc_html__('Slide 1 Heading', 'addons-for-elementor-builder'),
                        'description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'addons-for-elementor-builder'),
                        'button_text' => esc_html__('Click Here', 'addons-for-elementor-builder'),
                    ],
                    [
                        'heading' => esc_html__('Slide 2 Heading', 'addons-for-elementor-builder'),
                        'description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'addons-for-elementor-builder'),
                        'button_text' => esc_html__('Click Here', 'addons-for-elementor-builder'),
                    ],
                    [
                        'heading' => esc_html__('Slide 3 Heading', 'addons-for-elementor-builder'),
                        'description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'addons-for-elementor-builder'),
                        'button_text' => esc_html__('Click Here', 'addons-for-elementor-builder'),
                    ],
                ],
                'title_field' => '{{{ heading }}}',
            ]);

            $this->controls->responsive()->slider('slides_height', [
                'label' => esc_html__('Height', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'vh', 'custom'],
                'range' => [
                    'px' => ['min' => 100, 'max' => 1000,],
                    'em' => ['min' => 10, 'max' => 100,],
                    'rem' => ['min' => 10, 'max' => 100,],
                    'vh' => ['min' => 10, 'max' => 100,],
                ],
                'default' => ['size' => 400,],
                'selectors' => ['{{WRAPPER}} .afeb-slides-item' => 'height: {{SIZE}}{{UNIT}}',],
                'separator' => 'before',
            ]);

            $this->controls->select('slides_title_tag', [
                'label' => esc_html__('Title HTML Tag', 'addons-for-elementor-builder'),
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'div',
            ]);

            $this->controls->select('slides_description_tag', [
                'label' => esc_html__('Description HTML Tag', 'addons-for-elementor-builder'),
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'div',
            ]);
        });
        /**
         *
         * Settings
         *
         */
        $this->controls->tab_content_section('settings_content_section', [
            'label' => esc_html__('Settings', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->select('pagination', [
                'label' => esc_html__('Pagination', 'addons-for-elementor-builder'),
                'options' => [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    'dots' => esc_html__('Dots', 'addons-for-elementor-builder'),
                ],
                'default' => 'dots',
            ]);

            $this->controls->yn_switcher('autoplay', [
                'label' => esc_html__('Autoplay', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);

            $this->controls->number('autoplay_speed', [
                'label' => esc_html__('Autoplay Speed', 'addons-for-elementor-builder'),
                'default' => 2000,
                'min' => 1,
                'condition' => ['autoplay!' => ['',],],
            ]);

            $this->controls->yn_switcher('pause_on_hover', [
                'label' => esc_html__('Pause on Hover', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);

            $this->controls->yn_switcher('infinite_scroll', [
                'label' => esc_html__('Infinite Scroll', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);
        });
        /**
         *
         * Navigation
         *
         */
        $this->controls->tab_content_section('navigation_content_section', [
            'label' => esc_html__('Navigation', 'addons-for-elementor-builder')
        ], function () {
            $this->controls->yn_switcher('slides_arrows', [
                'label' => esc_html__('Arrows', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->tabs('navigation_content_tab', [
                'navigation_previous' => [
                    'label' => esc_html__('Previous', 'addons-for-elementor-builder'),
                    'condition' => ['slides_arrows' => 'yes',],
                    'callback' => function () {

                        $this->controls->icons('navigation_previous_icon', [
                            'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                            'skin' => 'inline',
                            'label_block' => false,
                            'default' => ['value' => 'fas fa-arrow-circle-left', 'library' => 'fa-solid'],
                            'exclude_inline_options' => ['svg'],
                            'condition' => ['slides_arrows' => 'yes',],
                        ]);

                        $this->controls->popover_toggle('navigation_previous_offset_toggle', [
                            'label' => esc_html__('Offset', 'addons-for-elementor-builder'),
                            'label_on' => esc_html__('Custom', 'addons-for-elementor-builder'),
                            'label_off' => esc_html__('None', 'addons-for-elementor-builder'),
                            'condition' => ['slides_arrows' => 'yes',],
                        ], function () {
                            $this->controls->responsive()->slider('navigation_previous_offset_left', [
                                'label' => esc_html__('Offset Left', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-prev' => 'left: {{SIZE}}{{UNIT}};',],
                                'condition' => ['slides_arrows' => 'yes', 'navigation_previous_offset_toggle' => 'yes',],
                            ]);

                            $this->controls->responsive()->slider('navigation_previous_offset_top', [
                                'label' => esc_html__('Offset Top', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-prev' => 'top: {{SIZE}}{{UNIT}};',],
                                'condition' => ['slides_arrows' => 'yes', 'navigation_previous_offset_toggle' => 'yes',],
                            ]);
                        });
                    },
                ],
                'navigation_next' => [
                    'label' => esc_html__('Next', 'addons-for-elementor-builder'),
                    'condition' => ['slides_arrows' => 'yes',],
                    'callback' => function () {

                        $this->controls->icons('navigation_next_icon', [
                            'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                            'skin' => 'inline',
                            'label_block' => false,
                            'default' => ['value' => 'fas fa-arrow-circle-right', 'library' => 'fa-solid'],
                            'exclude_inline_options' => ['svg'],
                            'condition' => ['slides_arrows' => 'yes',],
                        ]);

                        $this->controls->popover_toggle('navigation_next_offset_toggle', [
                            'label' => esc_html__('Offset', 'addons-for-elementor-builder'),
                            'label_on' => esc_html__('Custom', 'addons-for-elementor-builder'),
                            'label_off' => esc_html__('None', 'addons-for-elementor-builder'),
                            'condition' => ['slides_arrows' => 'yes',],
                        ], function () {

                            $this->controls->responsive()->slider('navigation_next_offset_left', [
                                'label' => esc_html__('Offset Left', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-next' => 'left: {{SIZE}}{{UNIT}}; right: unset !important;',],
                                'condition' => ['slides_arrows' => 'yes', 'navigation_next_offset_toggle' => 'yes',],
                            ]);

                            $this->controls->responsive()->slider('navigation_next_offset_top', [
                                'label' => esc_html__('Offset Top', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};',],
                                'condition' => ['slides_arrows' => 'yes', 'navigation_next_offset_toggle' => 'yes',],
                            ]);
                        });
                    },
                ],
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Slides
         *
         */
        $this->controls->tab_style_section('slides_style_section', [
            'label' => esc_html__('Slides', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->responsive()->slider('slides_content_max_width', [
                'label' => esc_html__('Content Width', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'range' => ['px' => ['max' => 1000,], 'em' => ['max' => 100,], 'rem' => ['max' => 100,],],
                'default' => ['size' => 66, 'unit' => '%',],
                'tablet_default' => ['unit' => '%',],
                'mobile_default' => ['unit' => '%',],
                'selectors' => ['{{WRAPPER}} .afeb-slides-contents' => 'max-width: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->padding('slides_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-slides-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->alignment('slides_horizontal_position', [
                'label' => esc_html__('Horizontal Position', 'addons-for-elementor-builder'),
                'options' => [
                    'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-left',],
                    'center' => ['title' => esc_html__('Center', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-center',],
                    'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-right',],
                ],
                'default' => 'center',
                'prefix_class' => 'afeb-slides-h-position-',
            ]);

            $this->controls->responsive()->alignment('slides_vertical_position', [
                'label' => esc_html__('Vertical Position', 'addons-for-elementor-builder'),
                'options' => [
                    'top' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-top',],
                    'middle' => ['title' => esc_html__('Middle', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-middle',],
                    'bottom' => ['title' => esc_html__('Bottom', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-bottom',],
                ],
                'default' => 'bottom',
                'prefix_class' => 'afeb-slides-v-position-',
            ]);

            $this->controls->responsive()->alignment('slides_text_align', [
                'label' => esc_html__('Text Align', 'addons-for-elementor-builder'),
                'default' => 'center',
                'selectors' => ['{{WRAPPER}} .afeb-slides-item-inner' => 'text-align: {{VALUE}}',],
            ]);

            $this->controls->text_shadow([
                'name' => 'slides_text_shadow',
                'selector' => '{{WRAPPER}} .afeb-slides-contents',
            ]);
        });
        /**
         *
         * Title
         *
         */
        $this->controls->tab_style_section('title_style_section', [
            'label' => esc_html__('Title', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->slider('heading_spacing', [
                'label' => esc_html__('Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => ['px' => ['max' => 100,], 'em' => ['max' => 10,], 'rem' => ['max' => 10,],],
                'selectors' => ['{{WRAPPER}} .afeb-slides-item-inner .afeb-slides-item-heading:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->text_color('heading_color', [
                'selectors' => ['{{WRAPPER}} .afeb-slides-item-heading' => 'color: {{VALUE}}',],
            ]);

            $this->controls->typography([
                'name' => 'heading_typography',
                'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY,],
                'selector' => '{{WRAPPER}} .afeb-slides-item-heading',
            ]);
        });
        /**
         *
         * Description
         *
         */
        $this->controls->tab_style_section('description_style_section', [
            'label' => esc_html__('Description', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->slider('description_spacing', [
                'label' => esc_html__('Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => ['px' => ['max' => 100,], 'em' => ['max' => 10,], 'rem' => ['max' => 10,],],
                'selectors' => ['{{WRAPPER}} .afeb-slides-item-inner .afeb-slides-item-description:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->text_color('description_color', [
                'selectors' => ['{{WRAPPER}} .afeb-slides-item-description' => 'color: {{VALUE}}',],
            ]);

            $this->controls->typography([
                'name' => 'description_typography',
                'global' => ['default' => Global_Typography::TYPOGRAPHY_SECONDARY,],
                'selector' => '{{WRAPPER}} .afeb-slides-item-description',
            ]);
        });
        /**
         *
         * Button
         *
         */
        $this->controls->tab_style_section('button_style_section', [
            'label' => esc_html__('Button', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->typography([
                'name' => 'button_typography',
                // 'global' => ['default' => Global_Typography::TYPOGRAPHY_ACCENT,],
                'selector' => '{{WRAPPER}} .afeb-slides-item-button',
            ]);

            $this->controls->tabs('button_style_tab', [
                'button_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'button_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-slides-item-button',
                        ]);

                        $this->controls->text_color('button_color', [
                            // 'global' => ['default' => Global_Colors::COLOR_TEXT,],
                            'selectors' => ['{{WRAPPER}} .afeb-slides-item-button' => 'color: {{VALUE}};',],
                        ]);

                        $this->controls->color('button_border_color', [
                            'label' => esc_html__('Border Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .afeb-slides-item-button' => 'border-color: {{VALUE}};',],
                        ]);
                    },
                ],
                'button_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'button_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-slides-item-button:hover',
                        ]);

                        $this->controls->text_color('button_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-slides-item-button:hover' => 'color: {{VALUE}};',],
                        ]);

                        $this->controls->color('button_border_color_hover', [
                            'label' => esc_html__('Border Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .afeb-slides-item-button:hover' => 'border-color: {{VALUE}};',],
                        ]);

                        $this->controls->responsive()->slider('button_transition_duration_hover', [
                            'label' => esc_html__('Transition Duration', 'addons-for-elementor-builder'),
                            'size_units' => ['s', 'ms', 'custom'],
                            'default' => ['unit' => 'ms',],
                            'selectors' => ['{{WRAPPER}} .afeb-slides-item-button' => 'transition-duration: {{SIZE}}{{UNIT}};',],
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->slider('button_border_width', [
                'label' => esc_html__('Border Width', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'range' => ['px' => ['max' => 20,], 'em' => ['max' => 2,], 'rem' => ['max' => 2,],],
                'selectors' => ['{{WRAPPER}} .afeb-slides-item-button' => 'border-width: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->slider('button_border_radius', [
                'label' => esc_html__('Border Radius', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'range' => ['px' => ['max' => 100,], 'em' => ['max' => 10,], 'rem' => ['max' => 10,],],
                'selectors' => ['{{WRAPPER}} .afeb-slides-item-button' => 'border-radius: {{SIZE}}{{UNIT}};',],
            ]);
        });
        /**
         *
         * Navigation
         *
         */
        $this->controls->tab_style_section('navigation_style_section', [
            'label' => esc_html__('Navigation', 'addons-for-elementor-builder'),
            'condition' => ['slides_arrows!' => '',],
        ], function () {
            $this->controls->responsive()->slider('navigation_size', [
                'label' => esc_html__('Size', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['range' => ['px' => ['min' => 5, 'max' => 200]],],
                'selectors' => ['{{WRAPPER}} .slick-arrow:before' => 'font-size: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->tabs('navigation_style_tab', [
                'navigation_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'navigation_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .slick-arrow',
                        ]);

                        $this->controls->color('navigation_color', [
                            'label' => esc_html__('Icon Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .slick-arrow:before' => 'color: {{VALUE}}!important;',],
                        ]);

                        $this->controls->responsive()->padding('navigation_padding', [
                            'size_units' => ['px', 'em', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .slick-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'navigation_border',
                            'selector' => '{{WRAPPER}} .slick-arrow',
                        ]);

                        $this->controls->responsive()->border_radius('navigation_border_radius', [
                            'size_units' => ['px', '%'],
                            'selectors' => ['{{WRAPPER}} .slick-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'navigation_box_shadow',
                            'selector' => '{{WRAPPER}} .slick-arrow',
                        ]);
                    },
                ],
                'navigation_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'navigation_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .slick-arrow:hover',
                        ]);

                        $this->controls->color('navigation_color_hover', [
                            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .slick-arrow:hover:before' => 'color: {{VALUE}} !important;',],
                        ]);

                        $this->controls->responsive()->padding('navigation_padding_hover', [
                            'size_units' => ['px', 'em', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .slick-arrow:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'navigation_border_hover',
                            'selector' => '{{WRAPPER}} .slick-arrow:hover',
                        ]);

                        $this->controls->responsive()->border_radius('navigation_border_radius_hover', [
                            'size_units' => ['px', '%'],
                            'selectors' => ['{{WRAPPER}} .slick-arrow:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'navigation_box_shadow_hover',
                            'selector' => '{{WRAPPER}} .slick-arrow:hover',
                        ]);
                    },
                ],
            ]);
        });
        /**
         *
         * Pagination
         *
         */
        $this->controls->tab_style_section('pagination_style_section', [
            'label' => esc_html__('Pagination', 'addons-for-elementor-builder'),
            'condition' => ['pagination' => 'dots',],
        ], function () {

            $this->controls->responsive()->slider('pagination_size', [
                'label' => esc_html__('Size', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['range' => ['px' => ['min' => 5, 'max' => 80]],],
                'selectors' => ['{{WRAPPER}} .slick-dots span' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->tabs('pagination_style_tab', [
                'pagination_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->color('pagination_color', [
                            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .slick-dots span' => 'background-color: {{VALUE}}',],
                        ]);
                    },
                ],
                'pagination_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->color('pagination_color_hover', [
                            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .slick-dots>li:hover span' => 'background-color: {{VALUE}}',],
                        ]);
                    },
                ],
                'pagination_style_active' => [
                    'label' => esc_html__('Active', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->color('pagination_color_active', [
                            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .slick-dots .slick-active span' => 'background-color: {{VALUE}}',],
                        ]);
                    },
                ],
            ]);
        });
    }

    /**
     * Render Slides widget output on the frontend
     *
     * @since 1.5.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['slides_content_repeater'])) {
            return;
        }

        $title_tag = tag_escape(Utils::validate_html_tag($settings['slides_title_tag']));
        $description_tag = tag_escape(Utils::validate_html_tag($settings['slides_description_tag']));

        $slide_count = 0;

        $slick = [
            'adaptiveHeight' => false,
            'arrows' => $settings['slides_arrows'] ? true : false,
            'autoplay' => $settings['autoplay'] ? true : false,
            'autoplaySpeed' => $settings['autoplay_speed'] ?
                intval($settings['autoplay_speed']) : 2000,
            'centerMode' => false,
            'dots' => $settings['pagination'] == 'dots' ? true : false,
            'infinite' => $settings['infinite_scroll'] ? true : false,
            'nextArrow' => '<i class="slick-next '
                . (isset($settings['navigation_next_icon']['value']) ? esc_attr($settings['navigation_next_icon']['value']) : '') . '"></i>',
            'pauseOnHover' => $settings['pause_on_hover'] ? true : false,
            'prevArrow' => '<i class="slick-prev '
                . (isset($settings['navigation_previous_icon']['value']) ? esc_attr($settings['navigation_previous_icon']['value']) : '') . '"></i>',
        ];

        $this->add_render_attribute([
            'slides' => [
                'class' => 'afeb-slides-carousel afeb-slick',
                'data-slick' => [wp_json_encode($slick)]
            ]
        ]);

?>
        <div <?php $this->print_render_attribute_string('slides'); ?>>
            <?php foreach ($settings['slides_content_repeater'] as $slide):
                $slide_element = 'div';
                $slide_wrapper_key = 'slide-wrapper-' . $slide_count;
                $button_key = 'slide-button-' . $slide_count;
                $slide_item_key = 'slide-item-' . $slide_count;

                $this->add_render_attribute($slide_wrapper_key, 'class', 'afeb-slides-item-inner');
                $this->add_render_attribute($button_key, 'class', ['elementor-button', 'afeb-slides-item-button']);

                if (!empty($slide['link']['url'])) {
                    if ($slide['link_click'] === 'button') {
                        $this->add_link_attributes($button_key, $slide['link']);
                    } else {
                        $slide_element = 'a';
                        $this->add_link_attributes($slide_wrapper_key, $slide['link']);
                    }
                }

                $this->add_render_attribute($slide_item_key, 'class', [
                    'elementor-repeater-item-' . sanitize_html_class($slide['_id']),
                    'afeb-slides-item'
                ]);
                $this->add_render_attribute($slide_item_key, 'role', 'group');
                $this->add_render_attribute($slide_item_key, 'aria-roledescription', 'slide');

                $slide_tag = tag_escape($slide_element);
                $button_element = (!empty($slide['link']['url']) && $slide['link_click'] === 'button') ? 'a' : 'span';
                $button_tag = tag_escape($button_element);
            ?>
                <div <?php $this->print_render_attribute_string($slide_item_key); ?>>
                    <div class="afeb-slides-item-bg" role="img"></div>
                    <<?php echo $slide_tag; ?> <?php $this->print_render_attribute_string($slide_wrapper_key); ?>>
                        <div class="afeb-slides-contents">
                            <?php if (!empty($slide['heading'])): ?>
                                <<?php echo $title_tag; ?> class="afeb-slides-item-heading">
                                    <?php echo wp_kses_post($slide['heading']); ?>
                                </<?php echo $title_tag; ?>>
                            <?php endif; ?>

                            <?php if (!empty($slide['description'])): ?>
                                <<?php echo $description_tag; ?> class="afeb-slides-item-description">
                                    <?php echo wp_kses_post($slide['description']); ?>
                                </<?php echo $description_tag; ?>>
                            <?php endif; ?>

                            <?php if (!empty($slide['button_text'])): ?>
                                <<?php echo $button_tag; ?> <?php $this->print_render_attribute_string($button_key); ?>>
                                    <?php echo esc_html($slide['button_text']); ?>
                                </<?php echo $button_tag; ?>>
                            <?php endif; ?>
                        </div>
                    </<?php echo $slide_tag; ?>>
                </div>
            <?php
                $slide_count++;
            endforeach; ?>
        </div>
<?php
    }
}
