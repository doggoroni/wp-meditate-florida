<?php

namespace AFEB\Extensions;

use AFEB\Controls\CHelper;
use Elementor\Core\Kits\Documents\Tabs\Tab_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" GoingUpKit Extension Class
 * 
 * @class GoingUpKit
 * @version 1.0.4
 */
class GoingUpKit extends Tab_Base
{
    /**
     * @var CHelper
     */
    private $CHelper;

    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->CHelper = new CHelper();
    }

    /**
     * Get extension id
     *
     * @since 1.0.4
     *
     * @return string Extension id
     */
    public function get_id()
    {
        return 'afeb_going_up_kit_settings';
    }

    /**
     * Get extension title
     *
     * @since 1.0.4
     *
     * @return string Extension title
     */
    public function get_title()
    {
        return esc_html__('Scroll to Top', 'addons-for-elementor-builder');
    }

    /**
     * Get extension icon
     *
     * @since 1.0.4
     *
     * @return string Extension icon
     */
    public function get_icon()
    {
        return 'eicon-kit-upload';
    }

    /**
     * Register GoingUpKit extension tab controls
     *
     * @since 1.0.4
     */
    protected function register_tab_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'afeb-stng-ext-gup', esc_html__('Scroll to Top', 'addons-for-elementor-builder'), function ($obj) {
            $cndtn = ['afeb_gup' => 'yes'];
            $this->CHelper->yn_swtchr($obj, 'afeb_gup', __('Enable Scroll To Top', 'addons-for-elementor-builder'));
            $this->CHelper->chse($obj, 'afeb_gup_sh', '', ['active' => []], ['div.afeb-gotop' => 'display: flex;z-index: 999999'], 1, 'active', $cndtn);
            $this->CHelper->icn($obj, 'afeb_gup_ic', 'fas fa-angle-up', 'fa-solid', '', 1, 0, $cndtn);
            $this->CHelper->sh_swtchr($obj, 'afeb_gup_ttl_sh', __('Show Title', 'addons-for-elementor-builder'), '', $cndtn);
            $this->CHelper->txt($obj, 'afeb_gup_ttl', esc_html__('Title', 'addons-for-elementor-builder'), esc_html__('Back To Top', 'addons-for-elementor-builder'), esc_html__('e.g. Back To Top', 'addons-for-elementor-builder'), 'lblk', ['afeb_gup' => 'yes', 'afeb_gup_ttl_sh' => 'yes']);

            $slctr = '.afeb-gotop-btn';
            $obj->start_controls_tabs('itms_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't1', __('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'afeb_gup_btn_bg', $opt[0], [], [], $opt[1]);
                $this->CHelper->clr($obj, 'afeb_gup_btn_clr', $opt[0] . ' svg', '', $opt[1]);
                $this->CHelper->typo($obj, 'afeb_gup_btn_typo', $opt[0] . ' .afeb-gotop-title', '', ['afeb_gup' => 'yes', 'afeb_gup_ttl_sh' => 'yes']);
                $this->CHelper->res_falmnt($obj, 'afeb_gup_btn_almnt', 'div.afeb-gotop', '', '', $opt[1]);
                $size = ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000]];
                $unit = ['px', '%'];
                $this->CHelper->res_sldr($obj, 'afeb_gup_btn_hght', __('Height', 'addons-for-elementor-builder'), [$opt[0] => 'height: {{SIZE}}{{UNIT}}'], $size, $unit, $opt[1]);
                $this->CHelper->res_sldr($obj, 'afeb_gup_btn_wdt', __('Width', 'addons-for-elementor-builder'), [$opt[0] => 'width: {{SIZE}}{{UNIT}}'], $size, $unit, $opt[1]);
                $this->CHelper->res_mar($obj, 'afeb_gup_btn_mar', $opt[0], [], [], '', null, 0, '', $opt[1]);
                $this->CHelper->brdr($obj, 'afeb_gup_btn_brdr', $opt[0], '', $opt[1]);
                $this->CHelper->brdr_rdus($obj, 'afeb_gup_btn_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], [], [], '', null, 0, '', $opt[1]);
                $this->CHelper->bx_shdo($obj, 'afeb_gup_bx_shdo', $opt[0], '', $opt[1]);
            }, [$slctr, $cndtn], $cndtn);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't2', __('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'afeb_gup_btn_bg_hvr', $opt[0], [], [], $opt[1]);
                $this->CHelper->clr($obj, 'afeb_gup_btn_clr_hvr', $opt[0] . ' svg', '', $opt[1]);
                $this->CHelper->h_anim($obj, 'btn_anim_hvr', $opt[1]);
                $this->CHelper->brdr($obj, 'afeb_gup_btn_brdr_hvr', $opt[0], '', $opt[1]);
                $this->CHelper->brdr_rdus($obj, 'afeb_gup_btn_rdus_hvr', [$opt[0] => CHelper::FILL_BR_RADIUS], [], [], '', null, 0, '', $opt[1]);
                $this->CHelper->bx_shdo($obj, 'afeb_gup_bx_shdo_hvr', $opt[0], '', $opt[1]);
            }, [$slctr . ':hover', $cndtn], $cndtn);
            $obj->end_controls_tabs();
        }, [], [], [], $this->get_id());
    }
}
