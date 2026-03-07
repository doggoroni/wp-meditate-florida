<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Helper;
use Elementor\Widget_Base;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" NewsTicker Widget Class
 * 
 * @class NewsTicker
 * @version 1.0.0
 */
class NewsTicker extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $CHelper;

    /**
     * @var array
     */
    private $HTML_TG;

    /**
     * NewsTicker Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->slick_pkg();
        $this->assets->newsticker_style();
        $this->assets->newsticker_script();
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
        return 'afeb_news_ticker';
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
        return esc_html__('News Ticker', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-news-ticker';
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
        return ['news ticker', 'news', esc_html__('News ticker', 'addons-for-elementor-builder')];
    }

    /**
     * Register NewsTicker widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        /**
         *
         * News Ticker
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('News Ticker', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'nt_lbl', esc_html__('Label', 'addons-for-elementor-builder'), 1);
            $this->CHelper->num($obj, 'nt_num_of_sldr', esc_html__(' Number of Items', 'addons-for-elementor-builder'), 1, null, null, 1);
            $this->HTML_TG = [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'span' => 'SPAN',
                'div' => 'DIV',
                'p' => 'P'
            ];
            $this->CHelper->slct($obj, 'nt_html_tg', esc_html__('HTML Tag', 'addons-for-elementor-builder'), $this->HTML_TG, 'h4');
        });
        /**
         *
         * Label
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Label', 'addons-for-elementor-builder'), function ($obj) {
            $def = esc_html__('Trending', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'nt_lbl_ttl', esc_html__('Title', 'addons-for-elementor-builder'), $def, '', '', ['nt_lbl' => 'yes']);
            $this->CHelper->res_chse($obj, 'nt_lbl_pos', 'Position', [
                'column' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon'  => 'eicon-v-align-top'],
                'row-reverse' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon'  => 'eicon-h-align-right'],
                'column-reverse' => ['title' => esc_html__('Bottom', 'addons-for-elementor-builder'), 'icon'  => 'eicon-v-align-bottom'],
                'row' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon'  => 'eicon-h-align-left']
            ], ['{{WRAPPER}} .afeb-news-ticker' => 'flex-direction: {{VALUE}}'], 1, 'row');
            $slctr = ['{{WRAPPER}} .afeb-news-ticker-label' => 'width: {{SIZE}}%', '{{WRAPPER}} .afeb-news-ticker-items' => 'width: calc(100% - {{SIZE}}%)'];
            $range = ['px' => ['min' => 8, 'max'  => 50, 'step' => 1]];
            $cndtn = ['nt_lbl_pos' => ['row', 'row-reverse']];
            $def = ['unit' => '%', 'size' => 10];
            $this->CHelper->res_sldr($obj, 'nt_lbl_vwdt', esc_html__('Width (%)', 'addons-for-elementor-builder'), $slctr, $range, ['px'], $cndtn, $def);
            $slctr = ['{{WRAPPER}} .afeb-news-ticker-label' => 'width: {{SIZE}}%', '{{WRAPPER}} .afeb-news-ticker-items' => 'width: 100%'];
            $range = ['px' => ['min' => 8, 'max'  => 100, 'step' => 1]];
            $cndtn = ['nt_lbl_pos' => ['column', 'column-reverse']];
            $def = ['unit' => '%', 'size' => 100];
            $this->CHelper->res_sldr($obj, 'nt_lbl_hwdt', esc_html__('Width (%)', 'addons-for-elementor-builder'), $slctr, $range, ['px'], $cndtn, $def);
        }, [], ['nt_lbl' => 'yes']);
        /**
         *
         * Query
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs3', esc_html__('Query', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->slct($obj, 'nt_src', esc_html__('Source', 'addons-for-elementor-builder'), [
                'posts' => esc_html__('Posts', 'addons-for-elementor-builder'),
                'pages' => esc_html__('Pages', 'addons-for-elementor-builder'),
            ], 'posts');
            $this->CHelper->slct($obj, 'nt_slct', esc_html__('Source', 'addons-for-elementor-builder'), [
                'dynamic' => esc_html__('Dynamic', 'addons-for-elementor-builder'),
                'manual' => esc_html__('Manual', 'addons-for-elementor-builder'),
            ], 'dynamic', ['nt_src' => ['posts', 'pages']]);
            $this->CHelper->dslct2($obj, 'nt_slct_pst', esc_html__('Select Posts', 'addons-for-elementor-builder'), 'get_posts_by_type', 'post', 'lbc', ['nt_src' => 'posts', 'nt_slct' => 'manual'], 1);
            $this->CHelper->dslct2($obj, 'nt_slct_pge', esc_html__('Select Pages', 'addons-for-elementor-builder'), 'get_posts_by_type', 'page', 'lbc', ['nt_src' => 'pages', 'nt_slct' => 'manual'], 1);
            $obj->start_controls_tabs('nt_tax_tbs');
            /**
             * Categories
             */
            $this->CHelper->add_tb($obj, 'nt_cat_tb', esc_html__('Categories', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct2($obj, 'nt_cat_include', esc_html__('Include', 'addons-for-elementor-builder'), Helper::get_terms_by_tax('category'), [], 1, 1, ['nt_src' => 'posts']);
                $this->CHelper->slct2($obj, 'nt_cat_exclude', esc_html__('Exclude', 'addons-for-elementor-builder'), Helper::get_terms_by_tax('category'), [], 1, 1, ['nt_src' => 'posts']);
            }, [], ['nt_src' => 'posts']);
            /**
             * Tags
             */
            $this->CHelper->add_tb($obj, 'nt_tag_tb', esc_html__('Tags', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct2($obj, 'nt_tag_include', esc_html__('Include', 'addons-for-elementor-builder'), Helper::get_terms_by_tax('post_tag'), [], 1, 1, ['nt_src' => 'posts']);
                $this->CHelper->slct2($obj, 'nt_tag_exclude', esc_html__('Exclude', 'addons-for-elementor-builder'), Helper::get_terms_by_tax('post_tag'), [], 1, 1, ['nt_src' => 'posts']);
            }, [], ['nt_src' => 'posts']);
            $obj->end_controls_tabs();
            $this->CHelper->slct($obj, 'nt_ordrby', esc_html__('Sort By', 'addons-for-elementor-builder'), [
                'title' => esc_html__('Title', 'addons-for-elementor-builder'),
                'ID' => esc_html__('ID', 'addons-for-elementor-builder'),
                'date' => esc_html__('Date', 'addons-for-elementor-builder'),
                'author' => esc_html__('Author', 'addons-for-elementor-builder'),
                'comment_count' => esc_html__('Comment Count', 'addons-for-elementor-builder'),
                'rand' => esc_html__('Random', 'addons-for-elementor-builder'),
                'modified' => esc_html__('Modified', 'addons-for-elementor-builder'),
                'name' => esc_html__('Name', 'addons-for-elementor-builder'),
                'parent' => esc_html__('Parent ID', 'addons-for-elementor-builder'),
                'type' => esc_html__('Type', 'addons-for-elementor-builder')
            ], 'date');
            $this->CHelper->chse($obj, 'nt_ordr', esc_html__('Order', 'addons-for-elementor-builder'), [
                'ASC' => ['title' => esc_html__('Ascending', 'addons-for-elementor-builder'), 'icon' => 'eicon-arrow-up'],
                'DESC' => ['title' => esc_html__('Descending', 'addons-for-elementor-builder'), 'icon' => 'eicon-arrow-down']
            ], [], 1, 'DESC');
        });
        /**
         *
         * Settings
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs4', esc_html__('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'nt_vrtcl', esc_html__('Vertical', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'nt_vrtcl_swpng', esc_html__('Vertical Swiping', 'addons-for-elementor-builder'), 1, ['nt_vrtcl' => 'yes']);
            // $this->CHelper->yn_swtchr($obj, 'nt_vrbl_wdt', esc_html__('Variable Width', 'addons-for-elementor-builder'));
            // $this->CHelper->yn_swtchr($obj, 'nt_cntr_mde', esc_html__('Center Mode', 'addons-for-elementor-builder'), 0);
            $this->CHelper->yn_swtchr($obj, 'nt_ato_ply', esc_html__('Autoplay', 'addons-for-elementor-builder'), 1);
            $cndtn = ['nt_ato_ply' => 'yes'];
            $this->CHelper->num($obj, 'nt_ato_ply_spd', esc_html__('Autoplay Speed', 'addons-for-elementor-builder'), 1, null, null, 2000, '', '', $cndtn);
            $this->CHelper->yn_swtchr($obj, 'nt_puse_on_hvr', esc_html__('Pause on Hover', 'addons-for-elementor-builder'), 1);
            $this->CHelper->yn_swtchr($obj, 'nt_infnt', esc_html__('Infinite Scroll', 'addons-for-elementor-builder'), 1);
        });
        /**
         *
         * Navigation
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs5', esc_html__('Navigation', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'nt_arrows', esc_html__('Arrows', 'addons-for-elementor-builder'), 1);
            $obj->start_controls_tabs('nt_nav_stl_tbs');
            /**
             * Previous Tab
             */
            $this->CHelper->add_tb($obj, 'nt_nav_t1', esc_html__('Previous', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->icn($obj, 'nt_arr_prv_ic', '', '', '', 1, 0, ['nt_arrows' => 'yes']);
                $lbl = esc_html__('Offset', 'addons-for-elementor-builder');
                $lbl_on = esc_html__('Custom', 'addons-for-elementor-builder');
                $lbl_off = esc_html__('None', 'addons-for-elementor-builder');
                $this->CHelper->pr_tgl($obj, 'nt_arr_prv_ofst_tgl', $lbl, $lbl_on, $lbl_off, 1, ['nt_arrows' => 'yes']);
                $obj->start_popover();
                $selector = '{{WRAPPER}} .slick-prev';
                $range = ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]];
                $unit = CHelper::BDSU;
                $condition = ['nt_arrows' => 'yes', 'nt_arr_prv_ofst_tgl' => 'yes'];
                $default = ['unit' => 'px', 'size' => ''];
                $this->CHelper->res_sldr($obj, 'nt_arr_prv_ofst_x', esc_html__('Offset Left', 'addons-for-elementor-builder'), [$selector => 'left: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $this->CHelper->res_sldr($obj, 'nt_arr_prv_ofst_y', esc_html__('Offset Top', 'addons-for-elementor-builder'), [$selector => 'top: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $obj->end_popover();
            }, [], ['nt_arrows' => 'yes']);
            /**
             * Next Tab
             */
            $this->CHelper->add_tb($obj, 'nt_nav_t2', esc_html__('Next', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->icn($obj, 'nt_arr_nxt_ic', '', '', '', 1, 0, ['nt_arrows' => 'yes']);
                $this->CHelper->pr_tgl($obj, 'nt_arr_nxt_ofst_tgl', esc_html__('Offset', 'addons-for-elementor-builder'), esc_html__('Custom', 'addons-for-elementor-builder'), esc_html__('None', 'addons-for-elementor-builder'), 1, ['nt_arrows' => 'yes']);
                $obj->start_popover();
                $selector = '{{WRAPPER}} .slick-next';
                $range = ['px' => ['min' => -2000, 'max' => 2000], '%' => ['min' => 0, 'max' => 100]];
                $unit = CHelper::BDSU;
                $condition = ['nt_arrows' => 'yes', 'nt_arr_nxt_ofst_tgl' => 'yes'];
                $default = ['unit' => 'px', 'size' => ''];
                $this->CHelper->res_sldr($obj, 'nt_arr_nxt_ofst_x', esc_html__('Offset Left', 'addons-for-elementor-builder'), [$selector => 'left: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $this->CHelper->res_sldr($obj, 'nt_arr_nxt_ofst_y', esc_html__('Offset Top', 'addons-for-elementor-builder'), [$selector => 'top: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
                $obj->end_popover();
            }, [], ['nt_arrows' => 'yes']);
            $obj->end_controls_tabs();
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Box Styles
         *
         */
        $slctr = '{{WRAPPER}} .afeb-news-ticker';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'bx_bg', $opt[0]);
            $slctr = ['{{WRAPPER}} .afeb-news-ticker>*:not(.afeb-alert)' => 'min-height: {{SIZE}}{{UNIT}} !important'];
            $range = ['px' => ['min' => 55, 'max'  => 1000, 'step' => 1]];
            $this->CHelper->res_sldr($obj, 'bx_hght', esc_html__('Height', 'addons-for-elementor-builder'), $slctr, $range, ['px', '%']);
            $this->CHelper->res_pad($obj, 'bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'bx_brdr', $opt[0], '');
            $this->CHelper->brdr_rdus($obj, 'bx_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS]);
        }, [$slctr]);
        /**
         *
         * Label Styles
         *
         */
        $slctr = '{{WRAPPER}} .afeb-news-ticker-label';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Label', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'lbl_bg', $opt[0]);
            $this->CHelper->clr($obj, 'lbl_clr', $opt[0], esc_html__('Text Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'lbl_typo', $opt[0], esc_html__('Typography', 'addons-for-elementor-builder'));
            $this->CHelper->res_pad($obj, 'lbl_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'lbl_brdr', $opt[0], '');
            $this->CHelper->brdr_rdus($obj, 'lbl_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS]);
        }, [$slctr]);
        /**
         *
         * Content Styles
         *
         */
        $slctr = '{{WRAPPER}} .afeb-news-ticker-content';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Content', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'cntnt_bg', $opt[0]);
            $this->CHelper->clr($obj, 'cntnt_clr', $opt[0], esc_html__('Text Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'cntnt_typo', $opt[0], esc_html__('Typography', 'addons-for-elementor-builder'));
            $slctr = [$opt[0] => 'width: {{SIZE}}{{UNIT}} !important'];
            $range = ['px' => ['min' => 1, 'max' => 1000, 'step' => 1]];
            $this->CHelper->res_sldr($obj, 'cntnt_wdt', esc_html__('Width', 'addons-for-elementor-builder'), $slctr, $range, ['px', '%']);
            $this->CHelper->res_mar($obj, 'cntnt_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'cntnt_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'cntnt_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'cntnt_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS]);
        }, [$slctr]);
        /**
         *
         * Navigation
         *
         */
        $nav_slctr = '{{WRAPPER}} .slick-arrow';
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Navigation', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $slctr = [$opt[1] => 'font-size: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 5, 'max' => 200]];
            $this->CHelper->res_sldr($obj, 'nt_nav_sze', esc_html__('Size', 'addons-for-elementor-builder'), $slctr, $range, ['px']);
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
        }, [$nav_slctr, $nav_slctr . ':before']);
    }

    /**
     * Render attributes
     *
     * @since 1.0.4
     * 
     * @param array $settings
     * @param string $wid
     */
    protected function render_attrs($settings = [], $wid = '')
    {
        $vr_cls = $settings['nt_vrtcl'] ? ' afeb-is-v ' : ' ';

        $this->add_render_attribute(
            [
                'news_ticker' => [
                    'class' => "afeb-news-ticker afeb-slick{$vr_cls}afeb-clr",
                    'data-settings' => [wp_json_encode(
                        Helper::get_array(['id' => esc_attr($wid)], 'nt_attr', $settings)
                    )]
                ]
            ]
        );

        echo $this->get_render_attribute_string('news_ticker');
    }

    /**
     * Render NewsTicker widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wid = $this->get_id();
?>
        <div <?php $this->render_attrs($settings, $wid); ?>>
            <?php $this->render_items($settings); ?>
        </div>
        <?php
    }

    /**
     * Render NewsTicker widget output on the frontend
     *
     * @since 1.0.0
     */
    public function render_items($settings = [])
    {
        $query = $this->get_query($settings);
        $is_content = $this->get_all_items($query->posts, $settings);

        if ($is_content === false) {
            $msg = esc_html__('Unfortunately, there is no item to display', 'addons-for-elementor-builder');
            echo wp_kses(Helper::front_notice($msg, 'error'), Helper::allowed_tags(['div']));
            return;
        }

        if (
            !empty($settings['nt_lbl']) &&
            !empty($settings['nt_lbl_ttl'])
        ): ?>
            <div class="afeb-news-ticker-label afeb-clr"><?php echo wp_kses_post($settings['nt_lbl_ttl']); ?></div>
<?php endif;
        $vertical = $settings['nt_vrtcl'] ? true : false;
        $next_arrow = !empty($settings['nt_arr_nxt_ic']['value']) ?
            esc_attr($settings['nt_arr_nxt_ic']['value']) : 'fas fa-arrow-circle-' . (!$vertical ? 'right' : 'down');
        $prev_arrow = !empty($settings['nt_arr_prv_ic']['value']) ?
            esc_attr($settings['nt_arr_prv_ic']['value']) : 'fas fa-arrow-circle-' . (!$vertical ? 'left' : 'up');
        $slick = [
            'adaptiveHeight' => false,
            'arrows' => $settings['nt_arrows'] ? true : false,
            'autoplay' => $settings['nt_ato_ply'] ? true : false,
            'autoplaySpeed' => intval($settings['nt_ato_ply_spd']) ?
                intval($settings['nt_ato_ply_spd']) : 2000,
            // 'centerMode' => $settings['nt_cntr_mde'] ? true : false,
            // 'dots' => false,
            'infinite' => $settings['nt_infnt'] ? true : false,
            'nextArrow' => '<i class="slick-next '
                . $next_arrow . '"></i>',
            'pauseOnHover' => $settings['nt_puse_on_hvr'] ? true : false,
            'prevArrow' => '<i class="slick-prev '
                . $prev_arrow . '"></i>',
            'slidesToShow' => $settings['nt_num_of_sldr'] ? intval($settings['nt_num_of_sldr']) : 1,
            'slidesToScroll' => 1,
            'vertical' => $vertical,
            'verticalSwiping' => $settings['nt_vrtcl_swpng'] ? true : false,
            // 'variableWidth' => $settings['nt_vrbl_wdt'] ? true : false
        ];

        $this->add_render_attribute([
            'news_ticker_carousel' => [
                'data-slick' => [wp_json_encode($slick)]
            ]
        ]);

        $carousel_data = $this->get_render_attribute_string('news_ticker_carousel');
        $wdt_cls = !$settings['nt_lbl'] ? ' afeb-w-100' : '';

        echo '<div class="afeb-news-ticker-items' . $wdt_cls . '"' . $carousel_data . '>' . $is_content . '</div>';
    }

    /**
     * Get NewsTicker Query
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_query($settings = [])
    {
        $args = [
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => -1,
            'orderby' => !empty($settings['nt_ordrby']) ?
                sanitize_text_field($settings['nt_ordrby']) : 'date',
            'order' => !empty($settings['nt_ordr']) ?
                sanitize_text_field($settings['nt_ordr']) : 'DESC'
        ];

        switch ($settings['nt_src']) {
            case 'posts':
                $args['post_type'] = 'post';

                if (isset($settings['nt_slct']) && $settings['nt_slct'] == 'manual')
                    $args['post__in'] = !empty($settings['nt_slct_pst']) ? $settings['nt_slct_pst'] : [''];

                $tax_query = [];

                if (!empty($settings['nt_cat_include'])) {

                    $term = is_array($settings['nt_cat_include']) ?
                        $settings['nt_cat_include'] : explode(',', str_replace(', ', ',', $settings['nt_cat_include']));
                    $term = map_deep($term, 'intval');
                    $tax_query[] = [
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $term
                    ];
                }

                if (!empty($settings['nt_cat_exclude'])) {

                    $term = is_array($settings['nt_cat_exclude']) ?
                        $settings['nt_cat_exclude'] : explode(',', str_replace(', ', ',', $settings['nt_cat_exclude']));
                    $term = map_deep($term, 'intval');
                    $tax_query[] = [
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $term,
                        'operator' => 'NOT IN',
                    ];
                }

                if (!empty($settings['nt_tag_include'])) {

                    $term = is_array($settings['nt_tag_include']) ?
                        $settings['nt_tag_include'] : explode(',', str_replace(', ', ',', $settings['nt_tag_include']));
                    $term = map_deep($term, 'intval');
                    $tax_query[] = [
                        'taxonomy' => 'post_tag',
                        'field' => 'term_id',
                        'terms' => $term,
                    ];
                }

                if (!empty($settings['nt_tag_exclude'])) {

                    $term = is_array($settings['nt_tag_exclude']) ?
                        $settings['nt_tag_exclude'] : explode(',', str_replace(', ', ',', $settings['nt_tag_exclude']));
                    $term = map_deep($term, 'intval');
                    $tax_query[] = [
                        'taxonomy' => 'post_tag',
                        'field' => 'term_id',
                        'terms' => $term,
                        'operator' => 'NOT IN',
                    ];
                }

                if (count($tax_query) > 0)
                    $args['tax_query'] = $tax_query;
                break;
            case 'pages':
                $args['post_type'] = 'page';

                if (isset($settings['nt_slct']) && $settings['nt_slct'] == 'manual')
                    $args['post__in'] = !empty($settings['nt_slct_pge']) ? $settings['nt_slct_pge'] : [''];
                break;
        };

        $query = new WP_Query($args);
        wp_reset_query();

        return $query;
    }

    /**
     * Get NewsTicker All Item
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_all_items($items = [], $settings = [])
    {
        global $wp_query;
        $default_object = $wp_query->queried_object;
        $content = false;

        if (count($items)) {
            $index = 0;
            foreach ($items as $item_object) {
                if (empty($item_object))
                    continue;

                $wp_query->queried_object = $item_object;
                $content .= $this->get_item($item_object, $settings);

                $index++;
            }

            $wp_query->queried_object = $default_object;
        }

        return $content;
    }

    /**
     * Get NewsTicker Item
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_item($item_object = null, $settings = [])
    {
        $item = '';

        if ($item_object && get_class($item_object) === 'WP_Post') {
            if (isset($item_object->ID)) {
                $html_tags = is_array($this->HTML_TG) ? array_keys($this->HTML_TG) : [];
                $html_tag = (!empty($settings['nt_html_tg']) &&
                    in_array($settings['nt_html_tg'], $html_tags)) ?
                    $settings['nt_html_tg'] : 'h4';
                $item = '<' . esc_attr($html_tag) . ' class="afeb-news-ticker-content"><a href="' . get_the_permalink($item_object->ID) . '">' .
                    get_the_title($item_object->ID) .
                    '</a></' . esc_attr($html_tag) . '>';
            }
        }

        return $item;
    }
}
