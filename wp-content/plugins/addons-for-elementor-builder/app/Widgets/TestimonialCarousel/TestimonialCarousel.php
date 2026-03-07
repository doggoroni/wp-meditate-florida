<?php

namespace AFEB\Widgets\TestimonialCarousel;

use AFEB\Assets;
use AFEB\Base;
use AFEB\Controls\CHelper;
use AFEB\Controls\Helper as New_Helper;
use AFEB\Helper;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" TestimonialCarousel Widget Class
 * 
 * @class TestimonialCarousel
 * @version 1.0.0
 */
class TestimonialCarousel extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var ControlsHelper
     */
    private $CHelper;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * TestimonialCarousel Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->controls = new New_Helper($this);

        $this->assets->slick_pkg();
        $this->assets->testimonial_carousel_style();
        $this->assets->testimonial_carousel_script();
    }

    /**
     * Get widget name
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_testimonial_carousel';
    }

    /**
     * Get widget title
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Testimonial Carousel', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-testimonial-carousel';
    }

    /**
     * Get widget categories
     *
     * @since 1.0.0
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
     * @since 1.0.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['testimonialcarousel', 'testimonial_carousel', esc_html__('Testimonial Carousel', 'addons-for-elementor-builder')];
    }

    /**
     * Register TestimonialCarousel widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', __('Testimonials', 'addons-for-elementor-builder'), function ($obj) {
            $items = new Repeater();

            $this->CHelper->mda($items, 'itm_img');
            $this->CHelper->img_sze($items, 'mda_tumbnl');
            $this->CHelper->wysiwyg($items, 'itm_dsc', esc_html__('Description', 'addons-for-elementor-builder'), CHelper::$LIM, esc_html__('Write description text here', 'addons-for-elementor-builder'));
            $this->CHelper->txt($items, 'itm_usr_nm', __('User Name', 'addons-for-elementor-builder'), esc_html__('John Dou', 'addons-for-elementor-builder'), esc_html__('e.g. John Dou', 'addons-for-elementor-builder'));
            $ea = str_replace('https://', '', Base::AFEB_URL);
            $this->CHelper->txt($items, 'itm_site_nm', __('Site Name', 'addons-for-elementor-builder'), $ea, sprintf("%s $ea", esc_html__('e.g.', 'addons-for-elementor-builder')));
            $this->CHelper->sh_swtchr($items, 'sh_rate', __('Show Rate', 'addons-for-elementor-builder'), 1);
            $this->CHelper->num($items, 'rate', __('Rate', 'addons-for-elementor-builder'), 1, 5, 0.1, 5, '', '', ['sh_rate' => 'yes']);
            $this->CHelper->sh_swtchr($items, 'sh_rate_num', __('Show Number', 'addons-for-elementor-builder'), 1);
            $options = [];
            for ($i = 0; $i < 4; $i++) $options[$i] = [
                'itm_dsc' => CHelper::$LIM,
                'itm_usr_nm' => esc_html__('John Dou', 'addons-for-elementor-builder'),
                'itm_site_nm' => $ea
            ];
            $this->CHelper->rptr($obj, 'tsl', $items->get_controls(), $options, 'itm_usr_nm', ' ');
        });
        /**
         *
         * Settings
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', __('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $this->controls->responsive()->slider('slds_per_view', [
                'label' => esc_html__('Slides on Display', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 1, 'max' => 2,],],
                'default' => ['unit' => 'px', 'size' => 2,],
            ]);

            $this->controls->responsive()->slider('slides_on_scroll', [
                'label' => esc_html__('Slides on Scroll', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 1, 'max' => 2,],],
                'default' => ['unit' => 'px', 'size' => 1,],
            ]);

            $this->CHelper->slct($obj, 'pgnat', __('Pagination', 'addons-for-elementor-builder'), [
                'none' => esc_html__('None', 'addons-for-elementor-builder'),
                'default' => esc_html__('Dots', 'addons-for-elementor-builder'),
            ], 'default');

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

            $this->controls->yn_switcher('sh_arws', [
                'label' => esc_html__('Arrows', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->tabs('navigation_content_tab', [
                'navigation_previous' => [
                    'label' => esc_html__('Previous', 'addons-for-elementor-builder'),
                    'condition' => ['sh_arws' => 'yes',],
                    'callback' => function () {
                        $this->controls->icons('navigation_previous_icon', [
                            'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                            'skin' => 'inline',
                            'label_block' => false,
                            'default' => ['value' => 'fas fa-arrow-circle-left', 'library' => 'fa-solid'],
                            'exclude_inline_options' => ['svg'],
                            'condition' => ['sh_arws' => 'yes',],
                        ]);

                        $this->controls->popover_toggle('navigation_previous_offset_toggle', [
                            'label' => esc_html__('Offset', 'addons-for-elementor-builder'),
                            'label_on' => esc_html__('Custom', 'addons-for-elementor-builder'),
                            'label_off' => esc_html__('None', 'addons-for-elementor-builder'),
                            'condition' => ['sh_arws' => 'yes',],
                        ], function () {
                            $this->controls->responsive()->slider('navigation_previous_offset_left', [
                                'label' => esc_html__('Offset Left', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-prev' => 'left: {{SIZE}}{{UNIT}};',],
                                'condition' => ['sh_arws' => 'yes', 'navigation_previous_offset_toggle' => 'yes',],
                            ]);

                            $this->controls->responsive()->slider('navigation_previous_offset_top', [
                                'label' => esc_html__('Offset Top', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-prev' => 'top: {{SIZE}}{{UNIT}};',],
                                'condition' => ['sh_arws' => 'yes', 'navigation_previous_offset_toggle' => 'yes',],
                            ]);
                        });
                    },
                ],
                'navigation_next' => [
                    'label' => esc_html__('Next', 'addons-for-elementor-builder'),
                    'condition' => ['sh_arws' => 'yes',],
                    'callback' => function () {

                        $this->controls->icons('navigation_next_icon', [
                            'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                            'skin' => 'inline',
                            'label_block' => false,
                            'default' => ['value' => 'fas fa-arrow-circle-right', 'library' => 'fa-solid'],
                            'exclude_inline_options' => ['svg'],
                            'condition' => ['sh_arws' => 'yes',],
                        ]);

                        $this->controls->popover_toggle('navigation_next_offset_toggle', [
                            'label' => esc_html__('Offset', 'addons-for-elementor-builder'),
                            'label_on' => esc_html__('Custom', 'addons-for-elementor-builder'),
                            'label_off' => esc_html__('None', 'addons-for-elementor-builder'),
                            'condition' => ['sh_arws' => 'yes',],
                        ], function () {

                            $this->controls->responsive()->slider('navigation_next_offset_left', [
                                'label' => esc_html__('Offset Left', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-next' => 'left: {{SIZE}}{{UNIT}}; right: unset !important;',],
                                'condition' => ['sh_arws' => 'yes', 'navigation_next_offset_toggle' => 'yes',],
                            ]);

                            $this->controls->responsive()->slider('navigation_next_offset_top', [
                                'label' => esc_html__('Offset Top', 'addons-for-elementor-builder'),
                                'size_units' => ['px', '%', 'custom'],
                                'range' => ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]],
                                'default' => ['unit' => 'px', 'size' => ''],
                                'selectors' => ['{{WRAPPER}} .slick-next' => 'top: {{SIZE}}{{UNIT}};',],
                                'condition' => ['sh_arws' => 'yes', 'navigation_next_offset_toggle' => 'yes',],
                            ]);
                        });
                    },
                ],
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /*
         *
         * Box Avatar Styles
         *
         */
        $avatar_slctr = '{{WRAPPER}} .afeb-testimonial-carousel .afeb-testimonial-carousel-item-content .afeb-head .afeb-avatar';
        $this->CHelper->add_stl_sctn($this, 'ss1', __('Avatar', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $opt[1] = $opt[0] . ' img';
            $slctr = [$opt[1] => 'width: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'bx_avtr_size', __('Size', 'addons-for-elementor-builder'), $slctr, ['px' => ['min' => 0, 'max' => 150]], CHelper::BDSU);
            $this->CHelper->res_pad($obj, 'bx_avtr_pad', $opt[0], [], ['left', 'right'], __('Margin', 'addons-for-elementor-builder'), null, 1, '%');
            $this->CHelper->brdr_rdus($obj, 'bx_avtr_rdus', $opt[1]);
            $this->CHelper->bx_shdo($obj, 'bx_avtr_shdo', $opt[1]);
        }, [$avatar_slctr]);
        /**
         *
         * Box Head Styles
         *
         */
        $item_content_slctr = '{{WRAPPER}} .afeb-testimonial-carousel .afeb-testimonial-carousel-item-content';
        $head_slctr = $item_content_slctr . ' .afeb-head';
        $meta_slctr = $item_content_slctr . ' .afeb-meta';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Title', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'bx_ttl_clr', $opt[1] . ' .afeb-name', esc_html__('Text Color', 'addons-for-elementor-builder'));
            $this->CHelper->clr($obj, 'bx_sbtte_clr', $opt[1] . ' .afeb-site-name', __('SubTitle Color', 'addons-for-elementor-builder'));
            $this->CHelper->clr($obj, 'bx_strs_clr', $opt[2] . ' .afeb-rate .afeb-stars span.checked', __('Stars Color', 'addons-for-elementor-builder'));
            $this->CHelper->clr($obj, 'bx_untracked_strs_clr', $opt[2] . ' .afeb-rate .afeb-stars span', __('Untracked Stars Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'bx_ttl_typo', $opt[1] . ' .afeb-name');
            $this->CHelper->typo($obj, 'bx_sbttl_typo', $opt[1] . ' .afeb-site-name');
            $this->CHelper->pad($obj, 'bx_ttl_pad', $opt[2], [], [], __('Title Box Padding', 'addons-for-elementor-builder'));
        }, [$item_content_slctr, $head_slctr, $meta_slctr]);
        /**
         *
         * Box Content Styles
         *
         */
        $this->CHelper->add_stl_sctn($this, 'ss3', __('Content', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'bx_cntnt_clr', $opt[0], esc_html__('Text Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'bx_cntnt_typo', $opt[0]);
            $this->CHelper->mar($obj, 'bx_cntnt_mar', $opt[0], [], ['top', 'bottom'], __('Content Margin', 'addons-for-elementor-builder'));
        }, [$item_content_slctr . ' .afeb-content']);
        /**
         *
         * Box Wrap Styles
         *
         */
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $opt[1] = $opt[0] . ' .afeb-icon';
            $this->CHelper->bg_clr($obj, 'bx_bg_clr', $opt[0]);
            $this->CHelper->dvdr($obj, 'div_1');
            $this->CHelper->clr($obj, 'bx_ic_clr', $opt[1] . ' svg', __('Icon Color', 'addons-for-elementor-builder'));
            $selector = [$opt[1] . ' svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 15, 'max' => 70]];
            $this->CHelper->res_sldr($obj, 'bx_ic_size', __('Icon Size', 'addons-for-elementor-builder'), $selector, $range);
            $this->CHelper->mar($obj, 'bx_ic_mar', $opt[1]);
            $this->CHelper->pad($obj, 'bx_wrp_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'itm_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'bx_rdus', $opt[0], CHelper::BDSU);
        }, [$item_content_slctr]);
        /**
         *
         * Navigation
         *
         */
        $this->controls->tab_style_section('navigation_style_section', [
            'label' => esc_html__('Navigation', 'addons-for-elementor-builder'),
            'condition' => ['sh_arws!' => '',],
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
            'condition' => ['pgnat' => 'default',],
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
     * Render attributes
     *
     * @since 1.0.4
     */
    protected function render_attrs($settings = [])
    {
        $slick = [
            'arrows' => !empty($settings['sh_arws']) ? true : false,
            'dots' => $settings['pgnat'] == 'default' ? true : false,
            'infinite' => $settings['infinite_scroll'] ? true : false,
            'nextArrow' => '<i class="slick-next ' . (isset($settings['navigation_next_icon']['value']) ? esc_attr($settings['navigation_next_icon']['value']) : '') . '"></i>',
            'prevArrow' => '<i class="slick-prev ' . (isset($settings['navigation_previous_icon']['value']) ? esc_attr($settings['navigation_previous_icon']['value']) : '') . '"></i>',
            'responsive' => [
                [
                    'breakpoint' => 769,
                    'settings' => [
                        'slidesToShow' => !empty($settings['slds_per_view_tablet']['size']) ? intval($settings['slds_per_view_tablet']['size']) : 2,
                        'slidesToScroll' => !empty($settings['slides_on_scroll_tablet']['size']) ? intval($settings['slides_on_scroll_tablet']['size']) : 1,
                        'touchMove' => true
                    ]
                ],
                [
                    'breakpoint' => 481,
                    'settings' => [
                        'slidesToShow' => !empty($settings['slds_per_view_mobile']['size']) ? intval($settings['slds_per_view_mobile']['size']) : 1,
                        'slidesToScroll' => !empty($settings['slides_on_scroll_mobile']['size']) ? intval($settings['slides_on_scroll_mobile']['size']) : 1,
                        'touchMove' => true
                    ]
                ]
            ],
            'slidesToShow' => !empty($settings['slds_per_view']['size']) ? intval($settings['slds_per_view']['size']) : 2,
            'slidesToScroll' => !empty($settings['slides_on_scroll']['size']) ? intval($settings['slides_on_scroll']['size']) : 1,
        ];

        $this->add_render_attribute([
            'testimonial_carousel' => [
                'class' => 'afeb-testimonial-carousel afeb-slick',
                'data-slick' => [wp_json_encode($slick)],
            ]
        ]);

        echo wp_kses_post($this->get_render_attribute_string('testimonial_carousel'));
    }

    /**
     * Render Testimonial Carousel widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
?>
        <div <?php $this->render_attrs($settings); ?>>
            <?php
            foreach ($settings['tsl'] as $item):
                $name = $item['itm_usr_nm'];
                $site_name = $item['itm_site_nm'];
                $avatar_url = $item['itm_img']['url'] ?? '';
                $content = $item['itm_dsc'];
                $star_rate = $item['rate'];
            ?>
                <div class="afeb-testimonial-carousel-item-wrap">
                    <div class="afeb-testimonial afeb-testimonial-carousel-item">
                        <div class="afeb-testimonial-carousel-item-content">
                            <div class="afeb-icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0" fill="none" width="24" height="24" />
                                    <g>
                                        <path d="M11.192 15.757c0-.88-.23-1.618-.69-2.217-.326-.412-.768-.683-1.327-.812-.55-.128-1.07-.137-1.54-.028-.16-.95.1-1.956.76-3.022.66-1.065 1.515-1.867 2.558-2.403L9.373 5c-.8.396-1.56.898-2.26 1.505-.71.607-1.34 1.305-1.9 2.094s-.98 1.68-1.25 2.69-.346 2.04-.217 3.1c.168 1.4.62 2.52 1.356 3.35.735.84 1.652 1.26 2.748 1.26.965 0 1.766-.29 2.4-.878.628-.576.94-1.365.94-2.368l.002.003zm9.124 0c0-.88-.23-1.618-.69-2.217-.326-.42-.77-.692-1.327-.817-.56-.124-1.074-.13-1.54-.022-.16-.94.09-1.95.75-3.02.66-1.06 1.514-1.86 2.557-2.4L18.49 5c-.8.396-1.555.898-2.26 1.505-.708.607-1.34 1.305-1.894 2.094-.556.79-.97 1.68-1.24 2.69-.273 1-.345 2.04-.217 3.1.165 1.4.615 2.52 1.35 3.35.732.833 1.646 1.25 2.742 1.25.967 0 1.768-.29 2.402-.876.627-.576.942-1.365.942-2.368v.01z" />
                                    </g>
                                </svg>
                            </div>
                            <div class="afeb-head">
                                <div class="afeb-avatar">
                                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($name); ?>">
                                </div>
                                <div class="afeb-meta">
                                    <div class="afeb-name">
                                        <?php echo esc_html($name); ?>
                                    </div>
                                    <div class="afeb-site-name">
                                        <?php echo esc_html($site_name); ?>
                                    </div>
                                    <?php if ($item['sh_rate'] === 'yes' && $star_rate) : ?>
                                        <div class="afeb-rate">
                                            <?php if ($item['sh_rate_num'] === 'yes') : ?>
                                                <div class="afeb-num"><?php echo esc_html($star_rate); ?></div>
                                            <?php endif; ?>
                                            <div class="afeb-stars">
                                                <?php
                                                for ($i = 0; $i <= $star_rate; $i++) {
                                                    if ($i >= 5) {
                                                        break;
                                                    }
                                                    $checked = ($i < 5 && $i < $star_rate) ? 'checked' : '';
                                                    if ($checked) {
                                                        echo wp_kses('<span class="fa fa-star ' . $checked . '"></span>', Helper::allowed_tags(['span']));
                                                    }
                                                }
                                                if ($i <= 5) {
                                                    for ($n = 1; $n <= 5 - $star_rate; $n++) {
                                                        echo '<span class="fa fa-star"></span>';
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="afeb-content"><?php echo wp_kses_post($content); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
<?php
    }
}
