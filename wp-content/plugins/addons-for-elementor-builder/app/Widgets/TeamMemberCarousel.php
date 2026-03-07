<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" TeamMemberCarousel Widget Class
 * 
 * @class TeamMemberCarousel
 * @version 1.5.0
 */
class TeamMemberCarousel extends Widget_Base
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
     * TeamMemberCarousel Constructor
     * 
     * @since 1.5.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->team_member_carousel_style();
        $this->assets->team_member_carousel_script();

        $this->assets->slick_pkg();
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
        return 'afeb_team_member_carousel';
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
        return esc_html('Team Member Carousel', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-team-member-carousel';
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
        return [
            'teammembercarousel',
            'teammember_carousel',
            esc_html__('Team Member Carousel', 'addons-for-elementor-builder'),
            esc_html__('Member', 'addons-for-elementor-builder'),
            esc_html__('Team', 'addons-for-elementor-builder'),
            esc_html__('Group', 'addons-for-elementor-builder'),
            esc_html__('About', 'addons-for-elementor-builder'),
        ];
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
        return ['afeb-team-member-carousel-style'];
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
        return ['afeb-team-member-carousel-script'];
    }

    /**
     * Register TeamMemberCarousel widget controls
     *
     * @since 1.5.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('team_member_carousel_content_section', [
            'label' => esc_html__('Team Member Carousel', 'addons-for-elementor-builder')
        ], function () {
            $repeater = new Repeater();
            $repeater_controls = new CHelper($repeater);


            $repeater_controls->media('image', []);

            $repeater_controls->text('title', [
                'label' => esc_html__('Title', 'addons-for-elementor-builder'),
                'default' => esc_html__('John Doe', 'addons-for-elementor-builder'),
            ]);

            $repeater_controls->text_area('text', [
                'label' => esc_html__('Description', 'addons-for-elementor-builder'),
                'default' => esc_html__('Developer', 'addons-for-elementor-builder'),
            ]);

            $repeater_controls->divider('separator_social_icons');

            $repeater_controls->sh_switcher('social_icons_status', [
                'label' => esc_html__('Social Icons', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);

            $recommended = [
                'fa-brands' => [
                    'android',
                    'apple',
                    'behance',
                    'bitbucket',
                    'codepen',
                    'delicious',
                    'deviantart',
                    'digg',
                    'dribbble',
                    'elementor',
                    'facebook',
                    'flickr',
                    'foursquare',
                    'free-code-camp',
                    'github',
                    'gitlab',
                    'globe',
                    'houzz',
                    'instagram',
                    'jsfiddle',
                    'linkedin',
                    'medium',
                    'meetup',
                    'mix',
                    'mixcloud',
                    'odnoklassniki',
                    'pinterest',
                    'product-hunt',
                    'reddit',
                    'shopping-cart',
                    'skype',
                    'slideshare',
                    'snapchat',
                    'soundcloud',
                    'spotify',
                    'stack-overflow',
                    'steam',
                    'telegram',
                    'thumb-tack',
                    'threads',
                    'tripadvisor',
                    'tumblr',
                    'twitch',
                    'twitter',
                    'viber',
                    'vimeo',
                    'vk',
                    'weibo',
                    'weixin',
                    'whatsapp',
                    'wordpress',
                    'xing',
                    'x-twitter',
                    'yelp',
                    'youtube',
                    '500px',
                ],
                'fa-solid' => ['envelope', 'link', 'rss',],
            ];

            $repeater_controls->heading('heading_social_icon_1', [
                'label' => esc_html__('Icon 1', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->icons('social_icon_1', [
                'label' => esc_html__('Select Icon', 'addons-for-elementor-builder'),
                'fa4compatibility' => 'socials',
                'recommended' => $recommended,
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->url('social_icon_link_1', [
                'label_block' => false,
                'condition' => ['social_icons_status!' => '', 'social_icon_1[value]!' => ''],
            ]);

            $repeater_controls->heading('heading_social_icon_2', [
                'label' => esc_html__('Icon 2', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->icons('social_icon_2', [
                'label' => esc_html__('Select Icon', 'addons-for-elementor-builder'),
                'fa4compatibility' => 'socials',
                'recommended' => $recommended,
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->url('social_icon_link_2', [
                'label_block' => false,
                'condition' => ['social_icons_status!' => '', 'social_icon_2[value]!' => ''],
            ]);

            $repeater_controls->heading('heading_social_icon_3', [
                'label' => esc_html__('Icon 3', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->icons('social_icon_3', [
                'label' => esc_html__('Select Icon', 'addons-for-elementor-builder'),
                'fa4compatibility' => 'socials',
                'recommended' => $recommended,
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->url('social_icon_link_3', [
                'label_block' => false,
                'condition' => ['social_icons_status!' => '', 'social_icon_3[value]!' => ''],
            ]);

            $repeater_controls->heading('heading_social_icon_4', [
                'label' => esc_html__('Icon 4', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->icons('social_icon_4', [
                'label' => esc_html__('Select Icon', 'addons-for-elementor-builder'),
                'fa4compatibility' => 'socials',
                'recommended' => $recommended,
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->url('social_icon_link_4', [
                'label_block' => false,
                'condition' => ['social_icons_status!' => '', 'social_icon_4[value]!' => ''],
            ]);

            $repeater_controls->heading('heading_social_icon_5', [
                'label' => esc_html__('Icon 5', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->icons('social_icon_5', [
                'label' => esc_html__('Select Icon', 'addons-for-elementor-builder'),
                'fa4compatibility' => 'socials',
                'recommended' => $recommended,
                'condition' => ['social_icons_status!' => '',],
            ]);

            $repeater_controls->url('social_icon_link_5', [
                'label_block' => false,
                'condition' => ['social_icons_status!' => '', 'social_icon_5[value]!' => ''],
            ]);

            $repeater_controls->divider('separator_social_icon_5');

            $repeater_controls->sh_switcher('buttons_status', [
                'label' => esc_html__('Buttons', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
            ]);

            $repeater_controls->heading('heading_button_1', [
                'label' => esc_html__('Button 1', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'condition' => ['buttons_status!' => ''],
            ]);

            $repeater_controls->text('buttons_label_1', [
                'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'condition' => ['buttons_status!' => ''],
            ]);

            $repeater_controls->url('buttons_link_1', [
                'label_block' => false,
                'condition' => ['buttons_status!' => '', 'buttons_label_1[value]!' => ''],
            ]);

            $repeater_controls->heading('heading_button_2', [
                'label' => esc_html__('Button 2', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'condition' => ['buttons_status!' => ''],
            ]);

            $repeater_controls->text('buttons_label_2', [
                'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'condition' => ['buttons_status!' => ''],
            ]);

            $repeater_controls->url('buttons_link_2', [
                'label_block' => false,
                'condition' => ['buttons_status!' => '', 'buttons_label_2[value]!' => ''],
            ]);

            $options = [];
            for ($i = 0; $i < 4; $i++) {
                $options[$i] = ['text',];
            }

            $this->controls->repeater('team_member_carousel_repeater', [
                'label' => esc_html__('Items', 'addons-for-elementor-builder'),
                'fields' => $repeater->get_controls(),
                'default' => $options,
                'title_field' => '{{{ title }}}',
            ]);
        });
        /**
         *
         * Settings
         *
         */
        $this->controls->tab_content_section('team_member_carousel_settings_content_section', [
            'label' => esc_html__('Settings', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->responsive()->slider('slides_on_display', [
                'label' => esc_html__('Slides on Display', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['px' => ['max' => 8,],],
                'default' => ['unit' => 'px', 'size' => 3,],
            ]);

            $this->controls->responsive()->slider('slides_on_scroll', [
                'label' => esc_html__('Slides on Scroll', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['px' => ['max' => 8,],],
                'default' => ['unit' => 'px', 'size' => 1,],
            ]);

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
        $this->controls->tab_content_section('team_member_carousel_navigation_content_section', [
            'label' => esc_html__('Navigation', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->yn_switcher('slides_arrows', [
                'label' => esc_html__('Arrows', 'addons-for-elementor-builder'),
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
         * Box
         *
         */
        $this->controls->tab_style_section('box_style_section', [
            'label' => esc_html__('Box', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->background([
                'name' => 'box_background',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-item',
            ]);

            $this->controls->responsive()->margin('box_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-slick .slick-slide' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('box_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-team-member-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'box_border',
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-item',
            ]);

            $this->controls->responsive()->border_radius('box_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-team-member-carousel-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'box_shadow',
                'exclude' => ['box_shadow_position',],
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-item',
            ]);
        });
        /**
         *
         * Image
         *
         */
        $this->controls->tab_style_section('image_style_section', [
            'label' => esc_html__('Image', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->alignment('image_alignment', [
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-image' => 'text-align: {{VALUE}};',],
            ]);

            $this->controls->responsive()->slider('image_width', [
                'label' => esc_html__('Width (%)', 'addons-for-elementor-builder'),
                'size_units' => ['%'],
                'range' => ['%' => ['min' => 10, 'max' => 100,],],
                'default' => ['unit' => '%', 'size' => 100,],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-image img' => 'width: {{SIZE}}%;',],
            ]);

            /*$this->controls->image_size([
                'name' => 'image_size',
                'default' => 'full',
                'responsive' => true,
            ]);*/

            $this->controls->responsive()->margin('image_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->border([
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-item-image img',
            ]);

            $this->controls->responsive()->border_radius('image_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->box_shadow([
                'name' => 'image_box_shadow',
                'exclude' => ['box_shadow_position',],
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-item-image img',
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

            $this->controls->text_color('title_color', [
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-title' => 'color: {{VALUE}} !important',],
            ]);

            $this->controls->responsive()->alignment('title_alignment', [
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-title' => 'text-align: {{VALUE}};',],
            ]);

            $this->controls->typography([
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-item-title',
            ]);

            $this->controls->responsive()->margin('title_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->padding('title_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
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

            $this->controls->text_color('description_color', [
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-text' => 'color: {{VALUE}} !important',],
            ]);

            $this->controls->responsive()->alignment('description_alignment', [
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-text' => 'text-align: {{VALUE}};',],
            ]);

            $this->controls->typography([
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-item-text',
            ]);

            $this->controls->responsive()->margin('description_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->padding('description_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-item-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);
        });
        /**
         *
         * Icons
         *
         */
        $this->controls->tab_style_section('icons_style_section', [
            'label' => esc_html__('Social Icons', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->tabs('icons_style_tab', [
                'icons_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'icons_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-social-icon-link',
                        ]);

                        $this->controls->text_color('icons_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link .afeb-team-member-carousel-social-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',],
                        ]);

                        $this->controls->border([
                            'name' => 'icons_border',
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-social-icon-link',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('icons_border_radius', [
                            'size_units' => ['px', '%'],
                            'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
                        ]);
                    },
                ],
                'icons_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'icons_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-social-icon-link:hover',
                        ]);

                        $this->controls->text_color('icons_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link .afeb-team-member-carousel-social-icon:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',],
                        ]);

                        $this->controls->number('icons_transition_duration_hover', [
                            'label' => esc_html__('Transition Duration (s)', 'addons-for-elementor-builder'),
                            'default' => 0.1,
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                            'separator' => 'before',
                            'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link' => 'transition-duration: {{VALUE}}s',],
                        ]);

                        $this->controls->border([
                            'name' => 'icons_border_hover',
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-social-icon-link:hover',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('icons_border_radius_hover', [
                            'size_units' => ['px', '%'],
                            'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->slider('icons_box_size', [
                'label' => esc_html__('Box Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'custom'],
                'range' => ['range' => ['px' => ['min' => 0, 'max' => 200,],],],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->slider('icons_size', [
                'label' => esc_html__('Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'custom'],
                'range' => ['range' => ['px' => ['min' => 0, 'max' => 200,],],],
                'selectors' => [
                    '{{WRAPPER}} .afeb-team-member-carousel-social-icon-link' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .afeb-team-member-carousel-social-icon-link svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
                ],
            ]);

            $this->controls->responsive()->slider('icons_gutter', [
                'label' => esc_html__('Gutter', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'custom'],
                'range' => ['range' => ['px' => ['min' => 0, 'max' => 25,],],],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->slider('icons_distance', [
                'label' => esc_html__('Distance', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'custom'],
                'range' => ['range' => ['px' => ['min' => 0, 'max' => 50,],],],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-social-icon-link' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->box_shadow([
                'name' => 'icons_box_shadow',
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-social-icon-link',
            ]);
        });
        /**
         *
         * Buttons
         *
         */
        $this->controls->tab_style_section('buttons_style_section', [
            'label' => esc_html__('Buttons', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->tabs('buttons_style_tab', [
                'buttons_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'buttons_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-button-link',
                        ]);

                        $this->controls->text_color('buttons_color', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-team-member-carousel-button-link' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'buttons_border',
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-button-link',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('buttons_border_radius', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-team-member-carousel-button-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
                'buttons_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'buttons_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-button-link:hover',
                        ]);

                        $this->controls->text_color('buttons_color_hover', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-team-member-carousel-button-link:hover' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->number('buttons_transition_duration_hover', [
                            'label' => esc_html__('Transition Duration (s)', 'addons-for-elementor-builder'),
                            'default' => 0.1,
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                            'separator' => 'before',
                            'selectors' => [
                                '{{WRAPPER}} .afeb-team-member-carousel-button-link' => 'transition-duration: {{VALUE}}s',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'buttons_border_hover',
                            'selector' => '{{WRAPPER}} .afeb-team-member-carousel-button-link:hover',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('buttons_border_radius_hover', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-team-member-carousel-button-link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->slider('buttons_gutter', [
                'label' => esc_html__('Gutter', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'custom'],
                'range' => ['range' => ['px' => ['min' => 0, 'max' => 25,],],],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-button-link' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->slider('buttons_distance', [
                'label' => esc_html__('Distance', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'custom'],
                'range' => [
                    'range' => ['px' => ['min' => 0, 'max' => 50,],],
                ],
                'selectors' => ['{{WRAPPER}} .afeb-team-member-carousel-button-link' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->box_shadow([
                'name' => 'buttons_box_shadow',
                'selector' => '{{WRAPPER}} .afeb-team-member-carousel-button-link',
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
     * Render TeamMemberCarousel widget output on the frontend
     *
     * @since 1.5.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

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
            'responsive' => [
                [
                    'breakpoint' => 769,
                    'settings' => [
                        'slidesToShow' => !empty($settings['slides_on_display_tablet']['size']) ? intval($settings['slides_on_display_tablet']['size']) : 2,
                        'slidesToScroll' => !empty($settings['slides_on_scroll_tablet']['size']) ? intval($settings['slides_on_scroll_tablet']['size']) : 1,
                        'touchMove' => true
                    ]
                ],
                [
                    'breakpoint' => 481,
                    'settings' => [
                        'slidesToShow' => !empty($settings['slides_on_display_mobile']['size']) ? intval($settings['slides_on_display_mobile']['size']) : 1,
                        'slidesToScroll' => !empty($settings['slides_on_scroll_mobile']['size']) ? intval($settings['slides_on_scroll_mobile']['size']) : 1,
                        'touchMove' => true
                    ]
                ]
            ],
            'slidesToShow' => !empty($settings['slides_on_display']['size']) ? intval($settings['slides_on_display']['size']) : 3,
            'slidesToScroll' => !empty($settings['slides_on_scroll']['size']) ? intval($settings['slides_on_scroll']['size']) : 1,
        ];

        $this->add_render_attribute([
            'team_member_carousel' => [
                'class' => 'afeb-team-member-carousel afeb-slick',
                'data-slick' => [wp_json_encode($slick)]
            ]
        ]);
?>
        <div <?php echo $this->get_render_attribute_string('team_member_carousel'); ?>>
            <?php $index = 1;
            foreach ($settings['team_member_carousel_repeater'] as $repeater_item): ?>
                <div class="afeb-team-member-carousel-item">

                    <?php if (!empty($repeater_item['image']['url'])): ?>
                        <div class="afeb-team-member-carousel-item-image">
                            <img src="<?php echo esc_url($repeater_item['image']['url']); ?>"
                                alt="<?php echo esc_html__('Team Member', 'addons-for-elementor-builder') . $index; ?>">
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($repeater_item['title'])): ?>
                        <div class="afeb-team-member-carousel-item-title">
                            <?php echo wp_kses_post($repeater_item['title']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($repeater_item['text'])): ?>
                        <div class="afeb-team-member-carousel-item-text">
                            <?php echo wp_kses_post($repeater_item['text']); ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    if (!empty($repeater_item['social_icons_status'])) {

                        echo '<div class="afeb-team-member-carousel-item-social-icons elementor-repeater-item-' . esc_attr($repeater_item['_id']) . '">';

                        for ($i = 0; $i <= 5; $i++) {
                            if (!empty($repeater_item['social_icon_' . $i]['value'])) {
                                $this->add_render_attribute('social_attribute_' . $i, 'class', 'afeb-team-member-carousel-social-icon-link');
                                $this->add_render_attribute('social_attribute_' . $i, 'href', esc_url($repeater_item['social_icon_link_' . $i]['url']));

                                if ($repeater_item['social_icon_link_' . $i]['is_external']) {
                                    $this->add_render_attribute('social_attribute_' . $i, 'target', '_blank');
                                }
                                if ($repeater_item['social_icon_link_' . $i]['nofollow']) {
                                    $this->add_render_attribute('social_attribute_' . $i, 'nofollow', '');
                                }

                    ?>
                                <a <?php echo $this->get_render_attribute_string('social_attribute_' . $i); ?>>
                                    <?php Icons_Manager::render_icon($repeater_item['social_icon_' . $i], [
                                        'class' => 'afeb-team-member-carousel-social-icon',
                                        'aria-hidden' => 'true',
                                    ]); ?>
                                </a>
                    <?php
                            }
                        }

                        echo '</div>';
                    }
                    ?>

                    <?php
                    if (!empty($repeater_item['buttons_status'])) {

                        echo '<div class="afeb-team-member-carousel-item-buttons">';

                        for ($i = 0; $i <= 2; $i++) {
                            if (!empty($repeater_item['buttons_label_' . $i])) {
                                $this->add_render_attribute('button_attribute_' . $i, 'class', 'afeb-team-member-carousel-button-link elementor-button');
                                $this->add_render_attribute('button_attribute_' . $i, 'href', esc_url($repeater_item['buttons_link_' . $i]['url']));

                                if ($repeater_item['buttons_link_' . $i]['is_external']) {
                                    $this->add_render_attribute('button_attribute_' . $i, 'target', '_blank');
                                }
                                if ($repeater_item['buttons_link_' . $i]['nofollow']) {
                                    $this->add_render_attribute('button_attribute_' . $i, 'nofollow', '');
                                }
                    ?>
                                <a <?php $this->print_render_attribute_string('button_attribute_' . $i); ?>>
                                    <?php echo esc_html($repeater_item['buttons_label_' . $i]); ?>
                                </a>
                    <?php
                            }
                        }

                        echo '</div>';
                    }
                    ?>
                </div>
            <?php $index++;
            endforeach; ?>
        </div>
<?php
    }
}
