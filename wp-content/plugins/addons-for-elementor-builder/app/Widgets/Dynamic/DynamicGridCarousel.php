<?php

namespace AFEB\Widgets\Dynamic;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Controls\Helper as New_Helper;
use AFEB\Helper as AFEBHelper;
use AFEB\Widgets;
use AFEB\PRO\Widgets\Dynamic\Helper as PRO_DGCHelper;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" DynamicGridCarousel Widget Class
 * 
 * @class DynamicGridCarousel
 * @version 1.3.0
 */
class DynamicGridCarousel extends Widget_Base
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
     * @var ClassHelper
     */
    private $DGCHelper;

    /**
     * @var string $keys
     */
    private $keys = 'dgrd_tmp,dgrd_lyt,dgrd_sldr_num_of_sldr,dgrd_sldr_num_of_sldr,' .
        'dgrd_itm_pr_pge,dgrd_itm_pr_pge,dgrd_ordrby,dgrd_ordr,dgrd_src,' .
        'dgrd_slct,dgrd_slct_pst,dgrd_slct_pst,dgrd_cat_include,' .
        'dgrd_cat_exclude,dgrd_tag_include,dgrd_tag_exclude,dgrd_slct_pge';

    /**
     * DynamicGridCarousel Constructor
     * 
     * @since 1.3.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->controls = new New_Helper($this);
        $this->DGCHelper = class_exists(PRO_DGCHelper::class) ? new PRO_DGCHelper($this) : new Helper($this);

        $this->assets->mixitup_pkg_script();
        $this->assets->slick_pkg();

        $this->assets->dynamic_grid_carousel_style();
        $this->assets->dynamic_grid_carousel_script();
    }

    /**
     * Get widget name
     *
     * @since 1.3.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_dynamic_grid_carousel';
    }

    /**
     * Get widget title
     *
     * @since 1.3.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Dynamic Grid/Carousel', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.3.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-dynamic-grid-carousel';
    }

    /**
     * Get widget categories
     *
     * @since 1.3.0
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
     * @since 1.3.0
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
     * @since 1.3.0
     *
     * @return array Widget style dependencies
     */
    public function get_style_depends(): array
    {
        return ['afeb-dynamic-grid-carousel-style'];
    }

    /**
     * Retrieve the list of script dependencies the widget requires
     *
     * @since 1.3.0
     *
     * @return array Widget script dependencies
     */
    public function get_script_depends()
    {
        return ['afeb-dynamic-grid-carousel-script'];
    }

    public function effects_duration_fields_options($controls) {}

    public function effects_in_custom_fields_options($controls) {}

    public function effects_out_custom_fields_options($controls) {}

    public function perspective_fields_options($controls) {}

    public function custom_post_type_query_fields_options() {}

    public function register_filter_content_section() {}

    public function register_filter_style_section() {}

    public function register_filter_count_style_section() {}

    public function register_lightbox_content_section() {}

    public function register_lightbox_style_section() {}

    public function pagination_fields_options() {}

    /**
     * Register DynamicGridCarousel widget controls
     *
     * @since 1.3.0
     */
    public function register_controls()
    {
        /**
         *
         * Layout
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Layout', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->img_slct($obj, 'dgrd_lyt', [
                'grid' => [
                    'title' => esc_html__('Grid', 'addons-for-elementor-builder'),
                    'url' => $this->assets->assets_url("img/widgets/dynamic-grid/grid.svg")
                ],
                'carousel' => [
                    'title' => esc_html__('Carousel', 'addons-for-elementor-builder'),
                    'url' => $this->assets->assets_url("img/widgets/dynamic-grid/carousel.svg")
                ]
            ], 'grid');
            $this->CHelper->dslct2($obj, 'dgrd_tmp', esc_html__('Choose a template', 'addons-for-elementor-builder'), 'get_templates', 'dynamic-loop-item');

            $this->controls->responsive()->select('dgrd_clmns_wdt', [
                'label' => esc_html__('Columns', 'addons-for-elementor-builder'),
                'meta_options' => [
                    $this->get_name(),
                    [
                        '1' => esc_html__('1', 'addons-for-elementor-builder'),
                        '2' => esc_html__('2', 'addons-for-elementor-builder'),
                        '3' => esc_html__('3', 'addons-for-elementor-builder'),
                        '4' => esc_html__('4', 'addons-for-elementor-builder'),
                        // 'pv_5' => esc_html__('5', 'addons-for-elementor-builder'),
                        // 'pv_6' => esc_html__('6', 'addons-for-elementor-builder'),
                        // 'pv_7' => esc_html__('7', 'addons-for-elementor-builder'),
                        // 'pv_8' => esc_html__('8', 'addons-for-elementor-builder'),
                        // 'pv_9' => esc_html__('9', 'addons-for-elementor-builder'),
                    ]
                ],
                'default' => '3',
                'frontend_available' => true,
                'selectors' => ['{{WRAPPER}}' => '--afeb-dynamic-grid-item-column: {{VALUE}}',],
                'condition' => ['dgrd_lyt' => 'grid', 'dgrd_tmp!' => ''],
            ]);

            $this->controls->select('layout_irregular_grid_two_columns', [
                'label' => esc_html__('Irregular Grid', 'addons-for-elementor-builder'),
                'options' => [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    '20-80' => esc_html__('20 × 80', 'addons-for-elementor-builder'),
                    '80-20' => esc_html__('80 × 20', 'addons-for-elementor-builder'),
                    '20-80-alternate' => esc_html__('20 × 80 (Alternate)', 'addons-for-elementor-builder'),
                    '80-20-alternate' => esc_html__('80 × 20 (Alternate)', 'addons-for-elementor-builder'),
                    '30-70' => esc_html__('30 × 70', 'addons-for-elementor-builder'),
                    '70-30' => esc_html__('70 × 30', 'addons-for-elementor-builder'),
                    '30-70-alternate' => esc_html__('30 × 70 (Alternate)', 'addons-for-elementor-builder'),
                    '70-30-alternate' => esc_html__('70 × 30 (Alternate)', 'addons-for-elementor-builder'),
                    '40-60' => esc_html__('40 × 60', 'addons-for-elementor-builder'),
                    '60-40' => esc_html__('60 × 40', 'addons-for-elementor-builder'),
                    '40-60-alternate' => esc_html__('40 × 60 (Alternate)', 'addons-for-elementor-builder'),
                    '60-40-alternate' => esc_html__('60 × 40 (Alternate)', 'addons-for-elementor-builder'),
                ],
                'prefix_class' => 'afeb-dynamic-grid-irregular-',
                'condition' => ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '', 'dgrd_clmns_wdt' => '2',],
            ]);

            $this->controls->select('layout_irregular_grid_three_columns', [
                'label' => esc_html__('Irregular Grid', 'addons-for-elementor-builder'),
                'meta_options' => [
                    $this->get_name(),
                    [
                        '' => esc_html__('None', 'addons-for-elementor-builder'),
                        'pv_20-20-60' => esc_html__('20 × 20 × 60', 'addons-for-elementor-builder'),
                        'pv_60-20-20' => esc_html__('60 × 20 × 20', 'addons-for-elementor-builder'),
                        'pv_20-20-60-alternate' => esc_html__('20 × 20 × 60 (Alternate)', 'addons-for-elementor-builder'),
                        'pv_60-20-20-alternate' => esc_html__('60 × 20 × 20 (Alternate)', 'addons-for-elementor-builder'),
                        'pv_20-30-50' => esc_html__('20 × 30 × 50', 'addons-for-elementor-builder'),
                        'pv_50-30-20' => esc_html__('50 × 30 × 20', 'addons-for-elementor-builder'),
                        'pv_20-30-50-alternate' => esc_html__('20 × 30 × 50 (Alternate)', 'addons-for-elementor-builder'),
                        'pv_50-30-20-alternate' => esc_html__('50 × 30 × 20 (Alternate)', 'addons-for-elementor-builder'),
                        'pv_30-40-30' => esc_html__('30 × 40 × 30', 'addons-for-elementor-builder'),
                        'pv_30-30-40' => esc_html__('30 × 30 × 40', 'addons-for-elementor-builder'),
                        'pv_40-30-30' => esc_html__('40 × 30 × 30', 'addons-for-elementor-builder'),
                    ]
                ],
                'prefix_class' => 'afeb-dynamic-grid-irregular-',
                'condition' => ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '', 'dgrd_clmns_wdt' => '3',],
            ]);

            $cndtn = ['dgrd_lyt' => 'grid', 'dgrd_tmp!' => ''];
            $this->CHelper->num($obj, 'dgrd_itm_pr_pge', esc_html__('Items Per Page', 'addons-for-elementor-builder'), 1, null, null, 6, '', '', $cndtn);
            $cndtn = ['dgrd_lyt' => 'carousel', 'dgrd_tmp!' => ''];
            $this->CHelper->num($obj, 'dgrd_sldr_num_of_sldr', esc_html__(' Number of Items', 'addons-for-elementor-builder'), 1, null, null, 12, '', '', $cndtn);

            $this->controls->yn_switcher('layout_animation', [
                'label' => esc_html__('Animation', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
                'condition' => ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '',],
            ]);

            $this->effects_duration_fields_options($this->controls);

            $this->controls->tabs('layout_animation_effects', [
                'layout_animation_effects_in' => [
                    'label' => esc_html__('Effects In', 'addons-for-elementor-builder'),
                    'condition' => ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '', 'layout_animation' => 'yes',],
                    'callback' => function () {
                        $this->controls->select('layout_animation_effect_in', [
                            'label' => esc_html__('Effect', 'addons-for-elementor-builder'),
                            'meta_options' => [
                                $this->get_name(),
                                [
                                    'fade' => esc_html__('Fade', 'addons-for-elementor-builder'),
                                    'scale' => esc_html__('Scale', 'addons-for-elementor-builder'),
                                    // 'pv_custom' => esc_html__('Custom', 'addons-for-elementor-builder'),
                                ]
                            ],
                            'default' => 'fade',
                            'condition' => ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '', 'layout_animation' => 'yes',],
                        ]);

                        $this->effects_in_custom_fields_options($this->controls);
                    },
                ],
                'layout_animation_effects_out' => [
                    'label' => esc_html__('Effects Out', 'addons-for-elementor-builder'),
                    'condition' => ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '', 'layout_animation' => 'yes',],
                    'callback' => function () {

                        $this->controls->select('layout_animation_effect_out', [
                            'label' => esc_html__('Effect', 'addons-for-elementor-builder'),
                            'meta_options' => [
                                $this->get_name(),
                                [
                                    'fade' => esc_html__('Fade', 'addons-for-elementor-builder'),
                                    'scale' => esc_html__('Scale', 'addons-for-elementor-builder'),
                                    // 'pv_custom' => esc_html__('Custom', 'addons-for-elementor-builder'),
                                ]
                            ],
                            'default' => 'fade',
                            'condition' => ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '', 'layout_animation' => 'yes',],
                        ]);

                        $this->effects_out_custom_fields_options($this->controls);
                    },
                ],
            ]);

            $this->perspective_fields_options($this->controls);
        });
        /**
         *
         * Query
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Query', 'addons-for-elementor-builder'), function ($obj) {
            $this->controls->select('dgrd_src', [
                'label' => esc_html__('Source', 'addons-for-elementor-builder'),
                'meta_options' => [
                    $this->get_name(),
                    [
                        'posts' => esc_html__('Posts', 'addons-for-elementor-builder'),
                        'pages' => esc_html__('Pages', 'addons-for-elementor-builder'),
                        'archive' => esc_html__('Current Query', 'addons-for-elementor-builder'),
                    ]
                ],
                'default' => 'posts',
            ]);
            $this->CHelper->slct($obj, 'dgrd_slct', esc_html__('Selection', 'addons-for-elementor-builder'), [
                'dynamic' => esc_html__('Dynamic', 'addons-for-elementor-builder'),
                'manual' => esc_html__('Manual', 'addons-for-elementor-builder'),
            ], 'dynamic', ['dgrd_src' => ['posts', 'pages']]);
            $this->CHelper->dslct2($obj, 'dgrd_slct_pst', esc_html__('Select Posts', 'addons-for-elementor-builder'), 'get_posts_by_type', 'post', 'lbc', ['dgrd_src' => 'posts', 'dgrd_slct' => 'manual'], 1);
            $this->CHelper->dslct2($obj, 'dgrd_slct_pge', esc_html__('Select Pages', 'addons-for-elementor-builder'), 'get_posts_by_type', 'page', 'lbc', ['dgrd_src' => 'pages', 'dgrd_slct' => 'manual'], 1);
            $this->custom_post_type_query_fields_options();
            $obj->start_controls_tabs('dgrd_tax_tbs');
            /**
             * Categories
             */
            $this->CHelper->add_tb($obj, 'dgrd_cat_tb', esc_html__('Categories', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct2($obj, 'dgrd_cat_include', esc_html__('Include', 'addons-for-elementor-builder'), AFEBHelper::get_terms_by_tax('category'), [], 1, 1, ['dgrd_src' => 'posts']);
                $this->CHelper->slct2($obj, 'dgrd_cat_exclude', esc_html__('Exclude', 'addons-for-elementor-builder'), AFEBHelper::get_terms_by_tax('category'), [], 1, 1, ['dgrd_src' => 'posts']);
            }, [], ['dgrd_src' => 'posts']);
            /**
             * Tags
             */
            $this->CHelper->add_tb($obj, 'dgrd_tag_tb', esc_html__('Tags', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct2($obj, 'dgrd_tag_include', esc_html__('Include', 'addons-for-elementor-builder'), AFEBHelper::get_terms_by_tax('post_tag'), [], 1, 1, ['dgrd_src' => 'posts']);
                $this->CHelper->slct2($obj, 'dgrd_tag_exclude', esc_html__('Exclude', 'addons-for-elementor-builder'), AFEBHelper::get_terms_by_tax('post_tag'), [], 1, 1, ['dgrd_src' => 'posts']);
            }, [], ['dgrd_src' => 'posts']);
            $obj->end_controls_tabs();
            $this->CHelper->slct($obj, 'dgrd_ordrby', esc_html__('Sort By', 'addons-for-elementor-builder'), [
                'title' => esc_html__('Title', 'addons-for-elementor-builder'),
                'ID' => esc_html__('ID', 'addons-for-elementor-builder'),
                'date' => esc_html__('Date', 'addons-for-elementor-builder'),
                'author' => esc_html__('Author', 'addons-for-elementor-builder'),
                'comment_count' => esc_html__('Comment Count', 'addons-for-elementor-builder'),
                'rand' => esc_html__('Random', 'addons-for-elementor-builder'),
                'modified' => esc_html__('Modified', 'addons-for-elementor-builder'),
                'parent' => esc_html__('Parent ID', 'addons-for-elementor-builder'),
            ], 'date');
            $this->CHelper->chse($obj, 'dgrd_ordr', esc_html__('Order', 'addons-for-elementor-builder'), [
                'ASC' => ['title' => esc_html__('Ascending', 'addons-for-elementor-builder'), 'icon' => 'eicon-arrow-up'],
                'DESC' => ['title' => esc_html__('Descending', 'addons-for-elementor-builder'), 'icon' => 'eicon-arrow-down']
            ], [], 1, 'DESC');
        }, [], ['dgrd_tmp!' => '']);
        /**
         *
         * Carousel
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs3', esc_html__('Slider / Carousel', 'addons-for-elementor-builder'), function ($obj) {
            $range = ['px' => ['min' => 1, 'max' => 8]];
            $cndtn = ['dgrd_lyt' => 'carousel'];
            $this->CHelper->res_sldr($obj, 'dgrd_sldr_on_dsply', esc_html__('Slides on Display', 'addons-for-elementor-builder'), [], $range, ['px'], $cndtn, 3);
            $this->CHelper->res_sldr($obj, 'dgrd_sldr_on_scrl', esc_html__('Slides on Scroll', 'addons-for-elementor-builder'), [], $range, ['px'], $cndtn, 1);
            $this->CHelper->yn_swtchr($obj, 'dgrd_sldr_ato_ply', esc_html__('Autoplay', 'addons-for-elementor-builder'), 1);
            $cndtn = ['dgrd_sldr_ato_ply' => 'yes'];
            $this->CHelper->num($obj, 'dgrd_sldr_ato_ply_spd', esc_html__('Autoplay Speed', 'addons-for-elementor-builder'), 1, null, null, 2000, '', '', $cndtn);
            $this->CHelper->yn_swtchr($obj, 'dgrd_sldr_puse_on_hvr', esc_html__('Pause on Hover', 'addons-for-elementor-builder'), 1);
            $this->CHelper->yn_swtchr($obj, 'dgrd_sldr_infnt_scrl', esc_html__('Infinite Scroll', 'addons-for-elementor-builder'), 1);
        }, [], ['dgrd_lyt' => 'carousel', 'dgrd_tmp!' => '']);
        /**
         *
         * Navigation
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs4', esc_html__('Navigation', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'dgrd_sldr_arrows', esc_html__('Arrows', 'addons-for-elementor-builder'), 1);
            $obj->start_controls_tabs('crsl_nav_stl_tbs');
            /**
             * Previous Tab
             */
            $this->CHelper->add_tb($obj, 'crsl_nav_t1', esc_html__('Previous', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->icn($obj, 'dgrd_sldr_arr_prv_ic', 'fas fa-arrow-circle-left', 'fa-solid', '', 1, 0, ['dgrd_sldr_arrows' => 'yes']);
                $this->CHelper->pr_tgl($obj, 'dgrd_sldr_arr_prv_ofst_tgl', esc_html__('Offset', 'addons-for-elementor-builder'), esc_html__('Custom', 'addons-for-elementor-builder'), esc_html__('None', 'addons-for-elementor-builder'), 1, ['dgrd_sldr_arrows' => 'yes']);
                $obj->start_popover();
                $selector = '{{WRAPPER}} .slick-prev';
                $range = ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]];
                $unit = CHelper::BDSU;
                $condition = ['dgrd_sldr_arrows' => 'yes', 'dgrd_sldr_arr_prv_ofst_tgl' => 'yes'];
                $default = ['unit' => 'px', 'size' => ''];
                $this->CHelper->res_sldr($obj, 'dgrd_sldr_arr_prv_ofst_x', esc_html__('Offset Left', 'addons-for-elementor-builder'), [$selector => 'left: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $this->CHelper->res_sldr($obj, 'dgrd_sldr_arr_prv_ofst_y', esc_html__('Offset Top', 'addons-for-elementor-builder'), [$selector => 'top: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $obj->end_popover();
            }, [], ['dgrd_sldr_arrows' => 'yes']);
            /**
             * Next Tab
             */
            $this->CHelper->add_tb($obj, 'crsl_nav_t2', esc_html__('Next', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->icn($obj, 'dgrd_sldr_arr_nxt_ic', 'fas fa-arrow-circle-right', 'fa-solid', '', 1, 0, ['dgrd_sldr_arrows' => 'yes']);
                $this->CHelper->pr_tgl($obj, 'dgrd_sldr_arr_nxt_ofst_tgl', esc_html__('Offset', 'addons-for-elementor-builder'), esc_html__('Custom', 'addons-for-elementor-builder'), esc_html__('None', 'addons-for-elementor-builder'), 1, ['dgrd_sldr_arrows' => 'yes']);
                $obj->start_popover();
                $selector = '{{WRAPPER}} .slick-next';
                $range = ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]];
                $unit = CHelper::BDSU;
                $condition = ['dgrd_sldr_arrows' => 'yes', 'dgrd_sldr_arr_nxt_ofst_tgl' => 'yes'];
                $default = ['unit' => 'px', 'size' => ''];
                $this->CHelper->res_sldr($obj, 'dgrd_sldr_arr_nxt_ofst_x', esc_html__('Offset Left', 'addons-for-elementor-builder'), [$selector => 'left: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $this->CHelper->res_sldr($obj, 'dgrd_sldr_arr_nxt_ofst_y', esc_html__('Offset Top', 'addons-for-elementor-builder'), [$selector => 'top: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $obj->end_popover();
            }, [], ['dgrd_sldr_arrows' => 'yes']);
            $obj->end_controls_tabs();
        }, [], ['dgrd_lyt' => 'carousel', 'dgrd_tmp!' => '']);
        $this->register_filter_content_section();
        $this->register_lightbox_content_section();
        /**
         *
         * Pagination
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs5', esc_html__('Pagination', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->slct($obj, 'dgrd_sldr_dots', esc_html__('Pagination', 'addons-for-elementor-builder'), [
                '' => esc_html__('None', 'addons-for-elementor-builder'),
                'dots' => esc_html__('Dots', 'addons-for-elementor-builder')
            ], 'dots');
            $selector = '{{WRAPPER}} .slick-dots';
            $range = ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]];
            $unit = CHelper::BDSU;
            $condition = ['dgrd_sldr_dots!' => ''];
            $this->CHelper->res_sldr($obj, 'dgrd_sldr_dots_ofst_x', esc_html__('Offset Left', 'addons-for-elementor-builder'), [$selector => 'left: {{SIZE}}{{UNIT}}'], $range, $unit, $condition);
            $this->CHelper->res_sldr($obj, 'dgrd_sldr_dots_ofst_y', esc_html__('Offset Bottom', 'addons-for-elementor-builder'), [$selector => 'bottom: {{SIZE}}{{UNIT}}'], $range, $unit, $condition);
        }, [], ['dgrd_lyt' => 'carousel', 'dgrd_tmp!' => '']);
        /**
         *
         * Pagination
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs6', esc_html__('Pagination', 'addons-for-elementor-builder'), function ($obj) {
            $this->controls->select('dgrd_pge', [
                'label' => esc_html__('Select Type', 'addons-for-elementor-builder'),
                'meta_options' => [
                    $this->get_name(),
                    [
                        '' => esc_html__('None', 'addons-for-elementor-builder'),
                        'numbers' => esc_html__('Numbers', 'addons-for-elementor-builder'),
                        'prev_next' => esc_html__('Previous/Next', 'addons-for-elementor-builder'),
                        'numbers_and_prev_next' => esc_html__('Numbers + Previous/Next', 'addons-for-elementor-builder'),
                        'pv_load_on_click' => esc_html__('Load on Click', 'addons-for-elementor-builder')
                    ]
                ],
                'default' => '',
            ]);
            $this->CHelper->yn_swtchr($obj, 'dgrd_pge_shrtn', esc_html__('Shorten', 'addons-for-elementor-builder'), '', ['dgrd_pge' => ['numbers', 'numbers_and_prev_next']]);
            $this->CHelper->txt($obj, 'dgrd_pge_prv_lbl', esc_html__('Previous Label', 'addons-for-elementor-builder'), esc_html__('Previous', 'addons-for-elementor-builder'), '', '', ['dgrd_pge' => ['prev_next', 'numbers_and_prev_next']]);
            $this->CHelper->txt($obj, 'dgrd_pge_nxt_lbl', esc_html__('Next Label', 'addons-for-elementor-builder'), esc_html__('Next', 'addons-for-elementor-builder'), '', '', ['dgrd_pge' => ['prev_next', 'numbers_and_prev_next']]);
            $this->pagination_fields_options();
        }, [], ['dgrd_lyt!' => 'carousel', 'dgrd_tmp!' => '']);
        /**
         *
         * Messages
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs7', esc_html__('Messages', 'addons-for-elementor-builder'), function ($obj) {
            $def = esc_html__('There is no item to display', 'addons-for-elementor-builder');
            $this->CHelper->txt_area($obj, 'dgrd_msg_nth_fnd', esc_html__('Nothing Found', 'addons-for-elementor-builder'), $def, '', 'lblk,dai');
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Layout
         *
         */
        $this->controls->tab_style_section('layout_style_section', [
            'label' => esc_html__('Layout', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->responsive()->margin('dgrd_lyt_mar', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-dynamic-grid-carousel-items .slick-slide' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
                'condition' => ['dgrd_lyt' => 'carousel', 'dgrd_tmp!' => '',],
            ]);

            $this->controls->responsive()->slider('grid_items_gap', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder') . '(px)',
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 50,],],
                'selectors' => ['{{WRAPPER}}' => '--afeb-dynamic-grid-carousel-item-gap: {{SIZE}}px',],
                'required' => true,
                'default' => ['unit' => 'px', 'size' => 2,],
                'condition' => ['dgrd_lyt!' => 'carousel',],
            ]);
        });
        /**
         *
         * Navigation
         *
         */
        $nav_slctr = '{{WRAPPER}} .slick-arrow';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Navigation', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $slctr = [$opt[1] => 'font-size: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 5, 'max' => 200]];
            $this->CHelper->res_sldr($obj, 'dgrd_nav_sze', esc_html__('Size', 'addons-for-elementor-builder'), $slctr, $range, ['px']);
            $obj->start_controls_tabs('nav_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'nav_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'nav_clr', $opt[1], 'color: {{VALUE}}!important;', $lbl);
                $this->CHelper->bx_shdo($obj, 'nav_bx_shdo', $opt[0]);
            }, [$opt[0], $opt[1]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'nav_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'nav_clr_hvr', $opt[0], 'color: {{VALUE}}!important;', $lbl);
                $this->CHelper->bx_shdo($obj, 'nav_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover:before']);
            $obj->end_controls_tabs();
        }, [$nav_slctr, $nav_slctr . ':before'], ['dgrd_lyt' => 'carousel']);
        $this->register_filter_style_section();
        $this->register_filter_count_style_section();
        $this->register_lightbox_style_section();
        /**
         *
         * Grid Pagination
         *
         */
        $pge_slctr = '';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Pagination', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->typo($obj, 'dgrd_pge_typo', '{{WRAPPER}} .afeb-dynamic-grid-pagination', esc_html__('Typography', 'addons-for-elementor-builder'));
            $this->CHelper->dvdr($obj, 'div_1');
            $this->CHelper->hed($obj, 'dgrd_pge_hed', esc_html__('Colors', 'addons-for-elementor-builder'));
            $obj->start_controls_tabs('dgrd_pge_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'dgrd_pge_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'dgrd_pge_clr', '{{WRAPPER}} .afeb-dynamic-grid-pagination a.page-numbers', 'color: {{VALUE}}', $lbl);
            }, []);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'dgrd_pge_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'dgrd_pge_clr_hvr', '{{WRAPPER}} .afeb-dynamic-grid-pagination a.page-numbers:hover', 'color: {{VALUE}}', $lbl);
            });
            /**
             * Active Tab
             */
            $this->CHelper->add_tb($obj, 'dgrd_pge_t3', esc_html__('Active', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'dgrd_pge_clr_act', '{{WRAPPER}} .afeb-dynamic-grid-pagination .page-numbers.current', 'color: {{VALUE}}', $lbl);
            }, []);
            $obj->end_controls_tabs();
            $this->CHelper->dvdr($obj, 'div_2');
            $slctr = [
                'body:not(.rtl) {{WRAPPER}} .afeb-dynamic-grid-pagination .page-numbers:not(:first-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
                'body:not(.rtl) {{WRAPPER}} .afeb-dynamic-grid-pagination .page-numbers:not(:last-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
                'body.rtl {{WRAPPER}} .afeb-dynamic-grid-pagination .page-numbers:not(:first-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
                'body.rtl {{WRAPPER}} .afeb-dynamic-grid-pagination .page-numbers:not(:last-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
            ];
            $range = ['px' => ['min' => 0, 'max' => 300]];
            $this->CHelper->res_sldr($obj, 'dgrd_pge_spc_btwn', esc_html__('Space Between', 'addons-for-elementor-builder'), $slctr, $range, ['px']);
            $slctr = ['{{WRAPPER}} .afeb-dynamic-grid-pagination' => 'margin-top: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'dgrd_pge_spc', esc_html__('Spacing', 'addons-for-elementor-builder'), $slctr, $range, ['px']);
        }, [$pge_slctr], ['dgrd_lyt' => 'grid', 'dgrd_pge!' => '']);
        /**
         *
         * Carousel Pagination
         *
         */
        $pge_slctr = '{{WRAPPER}} .slick-dots';
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Pagination', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $btn_slctr = $opt[0] . ' button';
            $slctr = [$btn_slctr => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 5, 'max' => 80]];
            $this->CHelper->res_sldr($obj, 'dgrd_pge_sze', esc_html__('Size', 'addons-for-elementor-builder'), $slctr, $range, ['px']);
            $obj->start_controls_tabs('pge_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'pge_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'pge_bg', $opt[0], 'background-color: {{VALUE}}', $lbl);
            }, [$btn_slctr]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'pge_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $hvr_slctr = $opt[0] . '>li:hover button';
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'pge_bg_hvr', $hvr_slctr, 'background-color: {{VALUE}}', $lbl);
            }, [$opt[0]]);
            /**
             * Active Tab
             */
            $this->CHelper->add_tb($obj, 'pge_t3', esc_html__('Active', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $act_slctr = $opt[0] . ' .slick-active button';
                $lbl = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->cstm_clr($obj, 'pge_bg_act', $act_slctr, 'background-color: {{VALUE}}', $lbl);
            }, [$opt[0]]);
            $obj->end_controls_tabs();
        }, [$pge_slctr], ['dgrd_lyt' => 'carousel']);
    }

    public function data_attrs($settings = [])
    {
        return [];
    }

    /**
     * Render attributes
     *
     * @since 1.3.0
     * 
     * @param array $settings
     */
    public function render_attrs($settings = [])
    {
        $id = esc_attr($this->get_id());
        $layout = $settings['layout_animation'] ?? '';
        $perspective = $settings['layout_animation_perspective'] ?? '';
        $perspective_distance = $settings['layout_animation_perspective_distance'] ?? '';
        $effect_in = $settings['layout_animation_effect_in'] ?? 'scale';
        $effect_out = $settings['layout_animation_effect_out'] ?? 'scale';

        $attrs = array_merge([
            'enable' => esc_attr($layout),
            'perspective' => esc_attr($perspective),
            'perspective_distance' => esc_attr($perspective_distance),
            'effect_in' => esc_attr($effect_in),
            'effect_out' => esc_attr($effect_out),
            'page_id' => intval(get_the_ID()),
            'widget_id' => $id,
        ], $this->data_attrs($settings));

        $classes = ['afeb-dynamic-grid-carousel'];
        if (!empty($settings['dgrd_lyt'])) {
            $classes[] = $settings['dgrd_lyt'] === 'carousel' ? 'afeb-dynamic-carousel afeb-slick' : 'afeb-dynamic-grid';
        }

        $this->add_render_attribute([
            'dynamic_grid' => [
                'id' => "afeb-dynamic-grid-carousel-$id",
                'style' => 'display:none;',
                'class' => implode(' ', $classes),
                'data-attrs' => wp_json_encode($attrs),
            ]
        ]);

        return $this->get_render_attribute_string('dynamic_grid');
    }


    public function before_render()
    {
        parent::before_render();
        $settings = $this->get_settings_for_display();
        $render_settings = Widgets::render_settings($settings, explode(',', $this->keys));

        update_post_meta(get_the_ID(), '_elementor_settings_for_preview_' . $this->get_id(), $render_settings);
    }

    /**
     * Render DynamicGridCarousel widget output on the frontend
     *
     * @since 1.3.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $render_settings = Widgets::render_settings($settings, explode(',', $this->keys));

        update_post_meta(get_the_ID(), '_elementor_settings_for_editor_' . $this->get_id(), $render_settings);
        $this->DGCHelper->render($settings);
    }
}
