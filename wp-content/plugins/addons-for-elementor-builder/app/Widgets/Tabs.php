<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Helper;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Tabs Widget Class
 * 
 * @class Tabs
 * @version 1.1.1
 */
class Tabs extends Widget_Base
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
     * Tabs Constructor
     * 
     * @since 1.1.1
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->tabs_style();
        $this->assets->tabs_script();
        do_action('afeb/widget/tabs/construct', $this);
    }

    /**
     * Get widget name
     *
     * @since 1.1.1
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_tabs';
    }

    /**
     * Get widget title
     *
     * @since 1.1.1
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Tabs', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.1.1
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-tabs';
    }

    /**
     * Get widget categories
     *
     * @since 1.1.1
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
     * @since 1.1.1
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['tabs', 'toggle', esc_html__('Tabs', 'addons-for-elementor-builder')];
    }

    /**
     * Register Tabs widget controls
     *
     * @since 1.1.1
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Tabs', 'addons-for-elementor-builder'), function ($obj) {
            $items = new Repeater();
            $this->CHelper->hddn($items, 'tb_ic_typ', '', 'none');
            $this->CHelper->hddn($items, 'tb_ic', '', '');
            $txt = esc_html__('Tab Title', 'addons-for-elementor-builder');
            $this->CHelper->txt($items, 'tb_ttl', esc_html__('Title', 'addons-for-elementor-builder'), $txt, $txt, 'lblk');
            $this->CHelper->hddn($items, 'tb_cnt_typ');
            $this->CHelper->wysiwyg($items, 'tb_cnt', esc_html__('Content', 'addons-for-elementor-builder'), CHelper::$LIM, '', '', ['tb_cnt_typ' => '']);
            $this->CHelper->rptr($obj, 'tbil', $items->get_controls(), [
                'tb_ttl' => esc_html__('Tab1', 'addons-for-elementor-builder')
            ], 'tb_ttl');
            $this->CHelper->hddn($obj, 'tb_itm_efct_lbc', '', 'flash');
        });
        /**
         *
         * Tab Items
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Tab Items', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->hddn($obj, 'def_itm_num', '', 1);
            $this->CHelper->hddn($obj, 'tb_itms_onhvr');
            $this->CHelper->chse($obj, 'tb_itms_pos', 'Position', [
                'top' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon'  => 'eicon-v-align-top'],
                'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon'  => 'eicon-h-align-right']
            ], [], 0);
            $slctr = '{{WRAPPER}} .afeb-tabs-nav';
            $this->CHelper->res_falmnt($obj, 'tb_itms_almnt', $slctr, '', '', ['tb_itms_pos' => ['', 'top', 'bottom']]);
            $this->CHelper->hddn($obj, 'tb_itm_ic_dsply', '', '');
            $this->CHelper->hddn($obj, 'tb_itm_ic_pos', '', '');
            $slctr = '{{WRAPPER}} .afeb-tab-a';
            $this->CHelper->res_talmnt($obj, 'tb_itm_almnt', $slctr, '', '', ['tb_itms_pos' => ['left', 'right']]);
            $slctr = ['.afeb-tabs-is-v .afeb-tabs-nav' => 'width: {{SIZE}}%', '.afeb-tabs-is-v .afeb-tabs-content' => 'width: calc(100% - {{SIZE}}%)'];
            $range = ['px' => ['min' => 15, 'max'  => 40, 'step' => 1]];
            $this->CHelper->sldr($obj, 'tb_itms_wdt', esc_html__('Nav Width', 'addons-for-elementor-builder'), $slctr, $range, ['px'], ['tb_itms_pos' => ['right', 'left']]);
            $slctr = ['.afeb-tab-a' => 'margin: 0 {{SIZE}}px', '.afeb-tabs-is-v .afeb-tab-a' => 'margin: {{SIZE}}px 0'];
            $range = ['px' => ['min' => 0, 'max' => 30, 'step' => 1]];
            $this->CHelper->res_sldr($obj, 'tb_itms_spcng', esc_html__('Spacing', 'addons-for-elementor-builder'), $slctr, $range, ['px']);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Box Styles
         *
         */
        $tb_bx_slctr = '{{WRAPPER}} .afeb-tabs';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'tb_bx_bg', $opt[0], [], [0]);
            $this->CHelper->res_mar($obj, 'tb_bx_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'tb_bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tb_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tb_bx_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'tb_bx_shdo', $opt[0]);
        }, [$tb_bx_slctr]);
        /**
         *
         * Tab Bar Styles
         *
         */
        $tb_br_slctr = '{{WRAPPER}} .afeb-tabs .afeb-tabs-nav';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Tab Bar', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'tb_br_bg', $opt[0], [], [0]);
            $this->CHelper->res_mar($obj, 'tb_br_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'tb_br_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tb_br_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tb_br_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'tb_br_shdo', $opt[0]);
        }, [$tb_br_slctr]);
        /**
         *
         * Tab Styles
         *
         */
        $tb_slctr = '{{WRAPPER}} .afeb-tabs .afeb-tabs-nav .afeb-tab-a';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Tab', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('itms_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'tb_bg', $opt[0], [], [0]);
                $this->CHelper->res_pad($obj, 'tb_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'tb_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'tb_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'tb_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'tb_bg_hvr', $opt[0], [], [0]);
                $this->CHelper->res_pad($obj, 'tb_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'tb_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'tb_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'tb_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            /**
             * Active Tab
             */
            $this->CHelper->add_tb($obj, 't3', esc_html__('Active', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'tb_bg_actv', $opt[0], [], [0]);
                $this->CHelper->res_pad($obj, 'tb_pad_actv', $opt[0]);
                $this->CHelper->brdr($obj, 'tb_brdr_actv', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'tb_rdus_actv', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'tb_shdo_actv', $opt[0]);
            }, [$opt[0] . '.afeb-active']);
            $obj->end_controls_tabs();
        }, [$tb_slctr]);
        /**
         *
         * Tab Text Styles
         *
         */
        $this->CHelper->add_stl_sctn($this, 'ss5', esc_html__('Tab Text', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $opt[1] = $opt[0];
            $opt[0] = $opt[0] . ' .afeb-tab-title';
            $obj->start_controls_tabs('itms_stl_tbs2');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't21', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'tb_txt_bg', $opt[0], []);
                $this->CHelper->clr($obj, 'tb_txt_clr', $opt[0]);
                $this->CHelper->typo($obj, 'tb_txt_typo', $opt[0]);
                $this->CHelper->res_mar($obj, 'tb_txt_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'tb_txt_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'tb_txt_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'tb_txt_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'tb_txt_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't22', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'tb_txt_bg_hvr', $opt[0], []);
                $this->CHelper->clr($obj, 'tb_txt_clr_hvr', $opt[0]);
                $this->CHelper->res_mar($obj, 'tb_txt_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'tb_txt_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'tb_txt_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'tb_txt_rdus_hvr', $opt[0]);
            }, [$opt[1] . ':hover .afeb-tab-title']);
            /**
             * Active Tab
             */
            $this->CHelper->add_tb($obj, 't23', esc_html__('Active', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'tb_txt_bg_actv', $opt[0], []);
                $this->CHelper->clr($obj, 'tb_txt_clr_actv', $opt[0]);
                $this->CHelper->res_mar($obj, 'tb_txt_mar_actv', $opt[0]);
                $this->CHelper->res_pad($obj, 'tb_txt_pad_actv', $opt[0]);
                $this->CHelper->brdr($obj, 'tb_txt_brdr_actv', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'tb_txt_rdus_actv', $opt[0]);
            }, [$opt[1] . '.afeb-active .afeb-tab-title']);
            $obj->end_controls_tabs();
        }, [$tb_slctr]);
        /**
         *
         * Content Styles
         *
         */
        $tb_cnt_slctr = '{{WRAPPER}} .afeb-tabs .afeb-tabs-content';
        $this->CHelper->add_stl_sctn($this, 'ss6', esc_html__('Content', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'tb_cnt_bg', $opt[0], []);
            $this->CHelper->clr($obj, 'tb_cnt_clr', $opt[0]);
            $this->CHelper->clr($obj, 'tb_cnt_lnk_clr', $opt[0] . ' a', esc_html__('Link Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'tb_cnt_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'tb_cnt_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'tb_cnt_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tb_cnt_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tb_cnt_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'tb_cnt_shdo', $opt[0]);
        }, [$tb_cnt_slctr]);
    }

    /**
     * Render attributes
     *
     * @since 1.1.1
     */
    protected function render_attrs($settings = [])
    {
        $classes = [];
        $classes[] = 'afeb-tabs afeb-clr';
        $classes[] = $settings['tb_itms_pos'];
        $classes[] = 'afeb-tabs-' . $settings['tb_itm_efct_lbc'];
        $classes[] = $settings['tb_itms_onhvr'] ? 'afeb-tabs-on-hover' : '';
        $classes[] = $settings['tb_itms_pos'] == 'right' ? 'afeb-tabs-is-v afeb-tabs-vr' : '';
        $classes[] = $settings['tb_itms_pos'] == 'bottom' ? 'afeb-tabs-nav-after' : '';
        $classes[] = $settings['tb_itms_pos'] == 'left' ? 'afeb-tabs-is-v afeb-tabs-vl' : '';
        $def_itm_num = $settings['def_itm_num'];
        $this->add_render_attribute(
            [
                'tabs' => [
                    'class' => implode(' ', array_filter($classes, 'esc_attr')),
                    'data-settings' => [
                        wp_json_encode(['activeTab' => $def_itm_num > 0 && $def_itm_num <= count($settings['tbil']) ? intval($def_itm_num) : 1])
                    ]
                ]
            ]
        );

        echo $this->get_render_attribute_string('tabs');
    }

    /**
     * Render Tabs widget output on the frontend
     *
     * @since 1.1.1
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $children = '';
        foreach (array_reverse($settings['tbil']) as $i => $item) {
            $icon = '';
            if ($item['tb_ic_typ'] === 'img') {
                $img = Group_Control_Image_Size::get_attachment_image_html($item, 'tb_img');
                $icon = '<div class="afeb-tab-image">' . $img . '</div>';
            } else if ($item['tb_ic']) {
                ob_start();
                Icons_Manager::render_icon($item['tb_ic']);
                $icon = '<span class="afeb-tab-icon">' . ob_get_clean() . '</span>';
            }
            $item['tb_ttl'] = $item['tb_ttl'] ? '<span class="afeb-tab-title">' . $item['tb_ttl'] . '</span>' : '';
            $content = Helper::hpv($item['tb_cnt']) ?  $item['tb_cnt'] :
                wp_kses(
                    Helper::front_notice(
                        sprintf(
                            /* translators: %s is replaced with "PRO" */
                            esc_html__('To use this feature, Please upgrade the plugin to the %s version', 'addons-for-elementor-builder'),
                            Helper::pro_badge()
                        ),
                        'error'
                    ),
                    Helper::allowed_tags(['div'])
                );

            $br = ($settings['tb_itm_ic_dsply'] == 'block' ? '<br>' : '');
            $children .= '<a class="afeb-tab-a afeb-d-none elementor-repeater-item-' . esc_attr($item['_id']) . '" data-tab="' . esc_attr($item['_id']) . '">' .
                ($settings['tb_itm_ic_pos'] == 'before' ? $icon . $br : '') . $item['tb_ttl'] . $br . ($settings['tb_itm_ic_pos'] != 'before' ? $icon : '') .
                '</a><div id="' . esc_attr($item['_id']) . '" class="afeb-tab"><div>' . $content . '</div></div>';
        }
?>
        <div <?php $this->render_attrs($settings); ?>>
            <div class="afeb-tabs-content">
                <?php echo wp_kses_post($children); ?>
            </div>
        </div>
<?php
    }
}
