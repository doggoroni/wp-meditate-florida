<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" FlipBox Widget Class
 * 
 * @class FlipBox
 * @version 1.5.0
 */
class FlipBox extends Widget_Base
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
     * FlipBox Constructor
     * 
     * @since 1.5.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->flip_box_style();
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
        return 'afeb_flip_box';
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
        return esc_html('Flip Box', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-flip-box';
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
        return ['afeb-flip-box-style'];
    }

    /**
     * Register FlipBox widget controls
     *
     * @since 1.5.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('flip_box_content_section', [
            'label' => esc_html__('Flip Box', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->select('title_tag', [
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
                'default' => 'h3',
            ]);

            $this->controls->select('description_tag', [
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

            $this->controls->responsive()->slider('height', [
                'label' => esc_html__('Height', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'vh', 'custom'],
                'range' => [
                    'px' => ['min' => 100, 'max' => 1000,],
                    'em' => ['min' => 10, 'max' => 100,],
                    'rem' => ['min' => 10, 'max' => 100,],
                    'vh' => ['min' => 10, 'max' => 100,],
                ],
                'separator' => 'before',
                'selectors' => ['{{WRAPPER}} .afeb-flip-box' => 'height: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->responsive()->slider('border_radius', [
                'label' => esc_html__('Border Radius', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['max' => 200,],
                    'em' => ['max' => 20,],
                    'rem' => ['max' => 20,],
                ],
                'separator' => 'after',
                'selectors' => ['{{WRAPPER}} .afeb-flip-box-layer, {{WRAPPER}} .afeb-flip-box-layer-overlay' => 'border-radius: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->select('flip_effect', [
                'label' => esc_html__('Flip Effect', 'addons-for-elementor-builder'),
                'default' => 'flip',
                'options' => [
                    'flip' => esc_html__('Flip', 'addons-for-elementor-builder'),
                    'fade' => esc_html__('Fade', 'addons-for-elementor-builder'),
                ],
                'prefix_class' => 'afeb-flip-box-effect-',
            ]);

            $this->controls->choose('flip_direction', [
                'label' => esc_html__('Flip Direction', 'addons-for-elementor-builder'),
                'default' => 'up',
                'options' => [
                    'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-left',],
                    'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-right',],
                    'up' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-top',],
                    'down' => ['title' => esc_html__('Bottom', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-bottom',],
                ],
                'toggle' => false,
                'condition' => ['flip_effect!' => ['fade', 'zoom-in', 'zoom-out',],],
                'prefix_class' => 'afeb-flip-box-direction-',
            ]);

            $this->controls->yn_switcher('flip_3d', [
                'label' => esc_html__('3D Depth', 'addons-for-elementor-builder'),
                'return_value' => 'afeb-flip-box-3d',
                'default' => '',
                'prefix_class' => '',
                'condition' => ['flip_effect' => 'flip',],
            ]);
        });
        /**
         *
         * Front
         *
         */
        $this->controls->tab_content_section('front_content_section', [
            'label' => esc_html__('Front', 'addons-for-elementor-builder')
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('front_tabs_content_section', [
                'front_tab_content' => [
                    'label' => esc_html__('Content', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->choose('graphic_element', [
                            'label' => esc_html__('Graphic Element', 'addons-for-elementor-builder'),
                            'options' => [
                                'none' => ['title' => esc_html__('None', 'addons-for-elementor-builder'), 'icon' => 'eicon-ban',],
                                'image' => ['title' => esc_html__('Image', 'addons-for-elementor-builder'), 'icon' => 'eicon-image-bold',],
                                'icon' => ['title' => esc_html__('Icon', 'addons-for-elementor-builder'), 'icon' => 'eicon-star',],
                            ],
                            'default' => 'icon',
                        ]);

                        $this->controls->media('image', [
                            'dynamic' => ['active' => true,],
                            'condition' => ['graphic_element' => 'image',],
                        ]);

                        $this->controls->image_size([
                            'name' => 'image',
                            'default' => 'thumbnail',
                            'condition' => ['graphic_element' => 'image',],
                        ]);

                        $icon_prefix = Icons_Manager::is_migration_allowed() ? 'fas ' : 'fa ';
                        $this->controls->icons('selected_icon', [
                            'fa4compatibility' => 'icon',
                            'default' => ['value' => $icon_prefix . 'fa-gem', 'library' => 'fa-solid',],
                            'condition' => ['graphic_element' => 'icon',],
                        ]);

                        $this->controls->text('title_text_a', [
                            'label' => esc_html__('Title', 'addons-for-elementor-builder'),
                            'default' => esc_html__('This is the heading', 'addons-for-elementor-builder'),
                            'placeholder' => esc_html__('Enter your title', 'addons-for-elementor-builder'),
                            'label_block' => true,
                            'separator' => 'before',
                        ]);

                        $this->controls->text_area('description_text_a', [
                            'label' => esc_html__('Description', 'addons-for-elementor-builder'),
                            'default' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'addons-for-elementor-builder'),
                            'placeholder' => esc_html__('Enter your description', 'addons-for-elementor-builder'),
                            'rows' => 10,
                        ]);
                    },
                ],
                'front_tab_background' => [
                    'label' => esc_html__('Background', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'background_a',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-front',
                        ]);

                        $this->controls->color('background_overlay_a', [
                            'label' => esc_html__('Background Overlay', 'addons-for-elementor-builder'),
                            'default' => '',
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-overlay' => 'background-color: {{VALUE}}',],
                            'separator' => 'before',
                            'condition' => ['background_a_image[id]!' => '',],
                        ]);

                        $this->controls->css_filters([
                            'name' => 'background_overlay_a_filters',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-overlay',
                            'condition' => ['background_overlay_a!' => '',],
                        ]);

                        $this->controls->select('background_overlay_a_blend_mode', [
                            'label' => esc_html__('Blend Mode', 'addons-for-elementor-builder'),
                            'options' => [
                                '' => esc_html__('Normal', 'addons-for-elementor-builder'),
                                'multiply' => esc_html__('Multiply', 'addons-for-elementor-builder'),
                                'screen' => esc_html__('Screen', 'addons-for-elementor-builder'),
                                'overlay' => esc_html__('Overlay', 'addons-for-elementor-builder'),
                                'darken' => esc_html__('Darken', 'addons-for-elementor-builder'),
                                'lighten' => esc_html__('Lighten', 'addons-for-elementor-builder'),
                                'color-dodge' => esc_html__('Color Dodge', 'addons-for-elementor-builder'),
                                'color-burn' => esc_html__('Color Burn', 'addons-for-elementor-builder'),
                                'hue' => esc_html__('Hue', 'addons-for-elementor-builder'),
                                'saturation' => esc_html__('Saturation', 'addons-for-elementor-builder'),
                                'color' => esc_html__('Color', 'addons-for-elementor-builder'),
                                'exclusion' => esc_html__('Exclusion', 'addons-for-elementor-builder'),
                                'luminosity' => esc_html__('Luminosity', 'addons-for-elementor-builder'),
                            ],
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-overlay' => 'mix-blend-mode: {{VALUE}}',],
                            'condition' => ['background_overlay_a!' => '',],
                        ]);
                    },
                ],
            ]);
        });
        /**
         *
         * Back
         *
         */
        $this->controls->tab_content_section('back_content_section', [
            'label' => esc_html__('Back', 'addons-for-elementor-builder')
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('back_tabs_content_section', [
                'back_tab_content' => [
                    'label' => esc_html__('Content', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text('title_text_b', [
                            'label' => esc_html__('Title', 'addons-for-elementor-builder'),
                            'default' => esc_html__('This is the heading', 'addons-for-elementor-builder'),
                            'placeholder' => esc_html__('Enter your title', 'addons-for-elementor-builder'),
                            'label_block' => true,
                        ]);

                        $this->controls->text_area('description_text_b', [
                            'label' => esc_html__('Description', 'addons-for-elementor-builder'),
                            'default' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'addons-for-elementor-builder'),
                            'placeholder' => esc_html__('Enter your description', 'addons-for-elementor-builder'),
                            'rows' => 10,
                        ]);

                        $this->controls->text('button_text', [
                            'label' => esc_html__('Button Text', 'addons-for-elementor-builder'),
                            'default' => esc_html__('Click Here', 'addons-for-elementor-builder'),
                            'label_block' => true,
                        ]);

                        $this->controls->url('link', []);

                        $this->controls->select('link_click', [
                            'label' => esc_html__('Apply Link On', 'addons-for-elementor-builder'),
                            'options' => [
                                'box' => esc_html__('Whole Box', 'addons-for-elementor-builder'),
                                'button' => esc_html__('Button Only', 'addons-for-elementor-builder'),
                            ],
                            'default' => 'button',
                            'condition' => ['link[url]!' => '',],
                        ]);
                    },
                ],
                'back_tab_background' => [
                    'label' => esc_html__('Background', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'background_b',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-back',
                        ]);

                        $this->controls->color('background_overlay_b', [
                            'label' => esc_html__('Background Overlay', 'addons-for-elementor-builder'),
                            'default' => '',
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-overlay' => 'background-color: {{VALUE}};',],
                            'separator' => 'before',
                            'condition' => ['background_b_image[id]!' => '',],
                        ]);

                        $this->controls->css_filters([
                            'name' => 'background_overlay_b_filters',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-overlay',
                            'condition' => ['background_overlay_b!' => '',],
                        ]);

                        $this->controls->select('background_overlay_b_blend_mode', [
                            'label' => esc_html__('Blend Mode', 'addons-for-elementor-builder'),
                            'options' => [
                                '' => esc_html__('Normal', 'addons-for-elementor-builder'),
                                'multiply' => esc_html__('Multiply', 'addons-for-elementor-builder'),
                                'screen' => esc_html__('Screen', 'addons-for-elementor-builder'),
                                'overlay' => esc_html__('Overlay', 'addons-for-elementor-builder'),
                                'darken' => esc_html__('Darken', 'addons-for-elementor-builder'),
                                'lighten' => esc_html__('Lighten', 'addons-for-elementor-builder'),
                                'color-dodge' => esc_html__('Color Dodge', 'addons-for-elementor-builder'),
                                'color-burn' => esc_html__('Color Burn', 'addons-for-elementor-builder'),
                                'hue' => esc_html__('Hue', 'addons-for-elementor-builder'),
                                'saturation' => esc_html__('Saturation', 'addons-for-elementor-builder'),
                                'color' => esc_html__('Color', 'addons-for-elementor-builder'),
                                'exclusion' => esc_html__('Exclusion', 'addons-for-elementor-builder'),
                                'luminosity' => esc_html__('Luminosity', 'addons-for-elementor-builder'),
                            ],
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-overlay' => 'mix-blend-mode: {{VALUE}}',],
                            'condition' => ['background_overlay_b!' => '',],
                        ]);
                    },
                ],
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Front
         *
         */
        $this->controls->tab_style_section('front_style_section', [
            'label' => esc_html__('Front', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->alignment('front_alignment', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                'selectors' => [
                    '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-overlay' => 'text-align: {{VALUE}}',
                    '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-image' => 'text-align: {{VALUE}}',
                ],
            ]);

            $this->controls->choose('front_vertical_position', [
                'label' => esc_html__('Vertical Position', 'addons-for-elementor-builder'),
                'options' => [
                    'top' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-top',],
                    'middle' => ['title' => esc_html__('Middle', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-middle',],
                    'bottom' => ['title' => esc_html__('Bottom', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-bottom',],
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-overlay' => 'justify-content: {{VALUE}}',
                ],
            ]);

            $this->controls->responsive()->padding('front_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'front_border',
                'selector' => '{{WRAPPER}} .afeb-flip-box-front',
                'separator' => '',
            ]);

            $this->controls->heading('heading_front_image', [
                'label' => esc_html__('Image', 'addons-for-elementor-builder'),
                'condition' => ['graphic_element' => 'image',],
                'separator' => 'before',
            ]);

            $this->controls->responsive()->alignment('front_image_alignment', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                'toggle' => true,
                'selectors' => ['{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-image' => 'text-align: {{VALUE}}',],
                'condition' => ['graphic_element' => 'image',],
            ]);

            $this->controls->responsive()->slider('front_image_spacing', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['max' => 100,],
                    'em' => ['max' => 10,],
                    'rem' => ['max' => 10,],
                ],
                'selectors' => ['{{WRAPPER}} .afeb-flip-box-image' => 'margin-bottom: {{SIZE}}{{UNIT}}',],
                'condition' => ['graphic_element' => 'image',],
            ]);

            $this->controls->responsive()->slider('front_image_width', [
                'label' => esc_html__('Width', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'default' => ['unit' => '%',],
                'range' => ['px' => ['min' => 10, 'max' => 1000,], '%' => ['min' => 5, 'max' => 100,],],
                'selectors' => ['{{WRAPPER}} .afeb-flip-box-image img' => 'width: {{SIZE}}{{UNIT}}',],
                'condition' => ['graphic_element' => 'image',],
            ]);

            $this->controls->responsive()->slider('front_image_opacity', [
                'label' => esc_html__('Opacity', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'default' => ['size' => 1,],
                'range' => ['px' => ['max' => 1, 'min' => 0.10, 'step' => 0.01,],],
                'selectors' => ['{{WRAPPER}} .afeb-flip-box-image' => 'opacity: {{SIZE}}',],
                'condition' => ['graphic_element' => 'image',],
            ]);

            $this->controls->border([
                'name' => 'front_image_border',
                'selector' => '{{WRAPPER}} .afeb-flip-box-front',
                'separator' => '',
                'condition' => ['graphic_element' => 'image',],
            ]);

            $this->controls->responsive()->border_radius('front_image_border_radius', [
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['max' => 200,],
                    'em' => ['max' => 20,],
                    'rem' => ['max' => 20,],
                ],
                'selectors' => ['{{WRAPPER}} .afeb-flip-box-image img' => 'border-radius: {{SIZE}}{{UNIT}}',],
                'condition' => ['graphic_element' => 'image',],
            ]);

            $this->controls->heading('heading_front_icon', [
                'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                'condition' => ['graphic_element' => 'icon',],
                'separator' => 'before',
            ]);

            $this->controls->select('icon_view', [
                'label' => esc_html__('View', 'addons-for-elementor-builder'),
                'options' => [
                    'default' => esc_html__('Default', 'addons-for-elementor-builder'),
                    'stacked' => esc_html__('Stacked', 'addons-for-elementor-builder'),
                    'framed' => esc_html__('Framed', 'addons-for-elementor-builder'),
                ],
                'default' => 'default',
                'condition' => ['graphic_element' => 'icon',],
            ]);

            $this->controls->select('icon_shape', [
                'label' => esc_html__('Shape', 'addons-for-elementor-builder'),
                'options' => [
                    'circle' => esc_html__('Circle', 'addons-for-elementor-builder'),
                    'square' => esc_html__('Square', 'addons-for-elementor-builder'),
                ],
                'default' => 'circle',
                'condition' => ['icon_view!' => 'default', 'graphic_element' => 'icon',],
            ]);

            $this->controls->responsive()->slider('front_icon_spacing', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['max' => 100,],
                    'em' => ['max' => 10,],
                    'rem' => ['max' => 10,],
                ],
                'selectors' => ['{{WRAPPER}} .afeb-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}}',],
                'condition' => ['graphic_element' => 'icon',],
            ]);

            $this->controls->color('front_icon_primary_color', [
                'label' => esc_html__('Primary Color', 'addons-for-elementor-builder'),
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .afeb-view-stacked .afeb-icon' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .afeb-view-stacked .afeb-icon svg' => 'stroke: {{VALUE}}',
                    '{{WRAPPER}} .afeb-view-framed .afeb-icon, {{WRAPPER}} .afeb-view-default .afeb-icon' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                    '{{WRAPPER}} .afeb-view-framed .afeb-icon svg, {{WRAPPER}} .afeb-view-default .afeb-icon svg' => 'fill: {{VALUE}}; border-color: {{VALUE}}',
                ],
                'condition' => ['graphic_element' => 'icon',],
            ]);

            $this->controls->color('front_icon_secondary_color', [
                'label' => esc_html__('Secondary Color', 'addons-for-elementor-builder'),
                'default' => '',
                'condition' => ['graphic_element' => 'icon', 'icon_view!' => 'default',],
                'selectors' => [
                    '{{WRAPPER}} .afeb-view-framed .afeb-icon' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .afeb-view-framed .afeb-icon svg' => 'stroke: {{VALUE}}',
                    '{{WRAPPER}} .afeb-view-stacked .afeb-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .afeb-view-stacked .afeb-icon svg' => 'fill: {{VALUE}}',
                ],
            ]);

            $this->controls->responsive()->slider('front_icon_size', [
                'label' => esc_html__('Icon Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['min' => 6, 'max' => 300,],
                    'em' => ['min' => 0.6, 'max' => 30,],
                    'rem' => ['min' => 0.6, 'max' => 30,],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .afeb-icon svg' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => ['graphic_element' => 'icon',],
            ]);

            $this->controls->responsive()->slider('front_icon_padding', [
                'label' => esc_html__('Icon Padding', 'addons-for-elementor-builder'),
                'selectors' => ['{{WRAPPER}} .afeb-icon' => 'padding: {{SIZE}}{{UNIT}}',],
                'range' => ['em' => ['min' => 0, 'max' => 5,],],
                'condition' => ['graphic_element' => 'icon', 'icon_view!' => 'default',],
            ]);

            $this->controls->responsive()->slider('front_icon_rotate', [
                'label' => esc_html__('Icon Rotate', 'addons-for-elementor-builder'),
                'size_units' => ['deg', 'grad', 'rad', 'turn'],
                'default' => ['size' => 0, 'unit' => 'deg',],
                'selectors' => [
                    '{{WRAPPER}} .afeb-icon i' => 'transform: rotate({{SIZE}}{{UNIT}})',
                    '{{WRAPPER}} .afeb-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}})',
                ],
                'condition' => ['graphic_element' => 'icon',],
            ]);

            $this->controls->responsive()->slider('front_icon_border_width', [
                'label' => esc_html__('Border Width', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'range' => [
                    'px' => ['max' => 20,],
                    'em' => ['max' => 2,],
                    'rem' => ['max' => 2,],
                ],
                'selectors' => ['{{WRAPPER}} .afeb-icon' => 'border-width: {{SIZE}}{{UNIT}}',],
                'condition' => ['graphic_element' => 'icon', 'icon_view' => 'framed',],
            ]);

            $this->controls->responsive()->border_radius('front_icon_border_radius', [
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => ['{{WRAPPER}} .afeb-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',],
                'condition' => ['graphic_element' => 'icon', 'icon_view!' => 'default',],
            ]);
            /**
             * Tabs
             */
            $this->controls->tabs('front_texts_tabs_content_section', [
                'front_texts_tab_title' => [
                    'label' => esc_html__('Title', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->responsive()->slider('front_title_spacing', [
                            'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                            'size_units' => ['px', 'em', 'rem', 'custom'],
                            'range' => [
                                'px' => ['max' => 100,],
                                'em' => ['max' => 10,],
                                'rem' => ['max' => 10,],
                            ],
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',],
                            'condition' => ['description_text_a!' => '', 'title_text_a!' => '',],
                        ]);

                        $this->controls->text_color('front_background_overlay_a', [
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-title' => 'color: {{VALUE}}',],
                            'condition' => ['title_text_a!' => '',],
                        ]);

                        $this->controls->typography([
                            'name' => 'front_typography',
                            'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY,],
                            'selector' => '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-title',
                            'condition' => ['title_text_a!' => '',],
                        ]);

                        $this->controls->text_stroke([
                            'name' => 'front_text_stroke',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-title',
                        ]);
                    },
                ],
                'front_texts_tab_description' => [
                    'label' => esc_html__('Description', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('front_description_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-description' => 'color: {{VALUE}}',],
                            'condition' => ['description_text_a!' => '',],
                        ]);

                        $this->controls->typography([
                            'name' => 'front_description_typography',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-front .afeb-flip-box-layer-description',
                            'condition' => ['description_text_a!' => '',],
                        ]);
                    },
                ],
            ]);
        });
        /**
         *
         * Back
         *
         */
        $this->controls->tab_style_section('back_style_section', [
            'label' => esc_html__('Back', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->alignment('back_alignment', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                'default' => 'center',
                'selectors' => ['{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-overlay' => 'text-align: {{VALUE}}',],
            ]);

            $this->controls->responsive()->alignment('back_vertical_position', [
                'label' => esc_html__('Vertical Position', 'addons-for-elementor-builder'),
                'options' => [
                    'top' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-top',],
                    'middle' => ['title' => esc_html__('Middle', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-middle',],
                    'bottom' => ['title' => esc_html__('Bottom', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-bottom',],
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-overlay' => 'justify-content: {{VALUE}}',
                ],
            ]);

            $this->controls->responsive()->padding('back_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'back_border',
                'selector' => '{{WRAPPER}} .afeb-flip-box-back',
                'separator' => '',
            ]);
            /**
             * Tabs
             */
            $this->controls->tabs('back_texts_tabs_content_section', [
                'back_texts_tab_title' => [
                    'label' => esc_html__('Title', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->responsive()->slider('back_title_spacing', [
                            'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                            'size_units' => ['px', 'em', 'rem', 'custom'],
                            'range' => [
                                'px' => ['max' => 100,],
                                'em' => ['max' => 10,],
                                'rem' => ['max' => 10,],
                            ],
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',],
                            'condition' => ['description_text_a!' => '', 'title_text_a!' => '',],
                        ]);

                        $this->controls->text_color('back_background_overlay_a', [
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-title' => 'color: {{VALUE}}',],
                            'condition' => ['title_text_a!' => '',],
                        ]);

                        $this->controls->typography([
                            'name' => 'back_typography',
                            'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY,],
                            'selector' => '{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-title',
                            'condition' => ['title_text_a!' => '',],
                        ]);

                        $this->controls->text_stroke([
                            'name' => 'back_text_stroke',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-title',
                        ]);
                    },
                ],
                'back_texts_tab_description' => [
                    'label' => esc_html__('Description', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('back_description_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-description' => 'color: {{VALUE}}',],
                            'condition' => ['description_text_a!' => '',],
                        ]);

                        $this->controls->typography([
                            'name' => 'back_description_typography',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-back .afeb-flip-box-layer-description',
                            'condition' => ['description_text_a!' => '',],
                        ]);
                    },
                ],
                'front_texts_tab_button' => [
                    'label' => esc_html__('Button', 'addons-for-elementor-builder'),
                    'condition' => ['button_text!' => '',],
                    'callback' => function () {

                        $this->controls->responsive()->alignment('back_button_alignment', [
                            'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                            'toggle' => true,
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-button-wrapper' => 'text-align: {{VALUE}}',],
                        ]);

                        $this->controls->typography([
                            'name' => 'back_button_typography',
                            'selector' => '{{WRAPPER}} .afeb-flip-box-button',
                            'global' => ['default' => Global_Typography::TYPOGRAPHY_ACCENT,],
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->heading('heading_back_normal', [
                            'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->text_color('back_button_text_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-button' => 'color: {{VALUE}}',],
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->background([
                            'name' => 'back_button_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-flip-box-button',
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->color('back_button_border_color', [
                            'label' => esc_html__('Border Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-button' => 'border-color: {{VALUE}}',],
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->heading('heading_back_hover', [
                            'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->text_color('back_button_text_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-button:hover' => 'color: {{VALUE}}',],
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->background([
                            'name' => 'back_button_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-flip-box-button:hover',
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->color('back_button_border_color_hover', [
                            'label' => esc_html__('Border Color', 'addons-for-elementor-builder'),
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-button:hover' => 'border-color: {{VALUE}}',],
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->slider('back_button_border_width', [
                            'label' => esc_html__('Border Width', 'addons-for-elementor-builder'),
                            'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                            'range' => [
                                'px' => ['max' => 20,],
                                'em' => ['max' => 2,],
                                'rem' => ['max' => 2,],
                            ],
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-button' => 'border-width: {{SIZE}}{{UNIT}}',],
                            'separator' => 'before',
                            'condition' => ['button_text!' => '',],
                        ]);

                        $this->controls->responsive()->slider('back_button_border_radius', [
                            'label' => esc_html__('Border Radius', 'addons-for-elementor-builder'),
                            'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                            'range' => [
                                'px' => ['max' => 100,],
                                'em' => ['max' => 10,],
                                'rem' => ['max' => 10,],
                            ],
                            'selectors' => ['{{WRAPPER}} .afeb-flip-box-button' => 'border-radius: {{SIZE}}{{UNIT}}',],
                            'condition' => ['button_text!' => '',],
                        ]);
                    },
                ],
            ]);
        });
    }

    /**
     * Render FlipBox widget output on the frontend
     *
     * @since 1.5.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wrapper_tag = 'div';
        $button_tag = 'a';
        $title_tag = Utils::validate_html_tag($settings['title_tag']);
        $description_tag = Utils::validate_html_tag($settings['description_tag']);
        $migration_allowed = Icons_Manager::is_migration_allowed();
        $this->add_render_attribute('button', 'class', [
            'afeb-flip-box-button',
            'afeb-button',
        ]);

        $this->add_render_attribute('wrapper', 'class', 'afeb-flip-box-layer afeb-flip-box-back');

        if (!empty($settings['link']['url'])) {
            $link_element = 'button';

            if ($settings['link_click'] === 'box') {
                $wrapper_tag = 'a';
                $button_tag = 'span';
                $link_element = 'wrapper';
            }

            $this->add_link_attributes($link_element, $settings['link']);
        }

        if ($settings['graphic_element'] === 'icon') {
            $this->add_render_attribute('icon-wrapper', 'class', 'afeb-icon-wrapper');
            $this->add_render_attribute('icon-wrapper', 'class', 'afeb-view-' . $settings['icon_view']);
            if ($settings['icon_view'] != 'default') {
                $this->add_render_attribute('icon-wrapper', 'class', 'afeb-shape-' . $settings['icon_shape']);
            }

            if (!isset($settings['icon']) && !$migration_allowed) {
                $settings['icon'] = 'fa fa-gem';
            }

            if (!empty($settings['icon'])) {
                $this->add_render_attribute('icon', 'class', $settings['icon']);
            }
        }

        $has_icon = !empty($settings['icon']) || !empty($settings['selected_icon']);
        $migrated = isset($settings['__fa4_migrated']['selected_icon']);
        $is_new = empty($settings['icon']) && $migration_allowed;
?>
        <div class="afeb-flip-box" tabindex="0">
            <div class="afeb-flip-box-layer afeb-flip-box-front">
                <div class="afeb-flip-box-layer-overlay">
                    <div class="afeb-flip-box-layer-inner">
                        <?php if ($settings['graphic_element'] === 'image' && !empty($settings['image']['url'])): ?>
                            <div class="afeb-flip-box-image">
                                <?php Group_Control_Image_Size::print_attachment_image_html($settings); ?>
                            </div>
                        <?php elseif ($settings['graphic_element'] === 'icon' && $has_icon): ?>
                            <div <?php $this->print_render_attribute_string('icon-wrapper'); ?>>
                                <div class="afeb-icon">
                                    <?php if ($is_new || $migrated): Icons_Manager::render_icon($settings['selected_icon']);
                                    else : ?>
                                        <i <?php $this->print_render_attribute_string('icon'); ?>></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($settings['title_text_a'])): ?>
                            <<?php Utils::print_validated_html_tag($title_tag); ?> class="afeb-flip-box-layer-title">
                                <?php $this->print_unescaped_setting('title_text_a'); ?>
                            </<?php Utils::print_validated_html_tag($title_tag); ?>>
                        <?php endif; ?>

                        <?php if (!empty($settings['description_text_a'])) : ?>
                            <<?php Utils::print_validated_html_tag($description_tag); ?> class="afeb-flip-box-layer-description">
                                <?php $this->print_unescaped_setting('description_text_a'); ?>
                            </<?php Utils::print_validated_html_tag($description_tag); ?>>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <<?php Utils::print_validated_html_tag($wrapper_tag); ?> <?php $this->print_render_attribute_string('wrapper'); ?>>
                <div class="afeb-flip-box-layer-overlay">
                    <div class="afeb-flip-box-layer-inner">
                        <?php if (!empty($settings['title_text_b'])) : ?>
                            <<?php Utils::print_validated_html_tag($title_tag); ?> class="afeb-flip-box-layer-title">
                                <?php $this->print_unescaped_setting('title_text_b'); ?>
                            </<?php Utils::print_validated_html_tag($title_tag); ?>>
                        <?php endif; ?>

                        <?php if (!empty($settings['description_text_b'])) : ?>
                            <<?php Utils::print_validated_html_tag($description_tag); ?> class="afeb-flip-box-layer-description">
                                <?php $this->print_unescaped_setting('description_text_b'); ?>
                            </<?php Utils::print_validated_html_tag($description_tag); ?>>
                        <?php endif; ?>

                        <?php if (!empty($settings['button_text'])): ?>
                            <div class="afeb-flip-box-button-wrapper">
                                <<?php Utils::print_validated_html_tag($button_tag); ?> <?php $this->print_render_attribute_string('button'); ?>>
                                    <?php $this->print_unescaped_setting('button_text'); ?>
                                </<?php Utils::print_validated_html_tag($button_tag); ?>>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </<?php Utils::print_validated_html_tag($wrapper_tag); ?>>
        </div>
<?php
    }
}
