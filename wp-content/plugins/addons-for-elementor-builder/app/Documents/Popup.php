<?php

namespace AFEB\Documents;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Helper;
use Elementor\Core\Base\Document;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Popup Class
 * 
 * @class Popup
 * @version 1.2.0
 */
class Popup extends Document
{
    /**
     * Popup Document Key
     */
    const POPUP_DOCUMENT = 'afeb-popup';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var ControlsHelper
     */
    private $CHelper;

    /**
     * Popup Constructor
     * 
     * @since 1.2.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();

        if (Helper::is_edit_mode()) {
            $this->assets->editor_popup_style();
        }

        $this->assets->popup_style();
        $this->assets->popup_script();
    }

    /**
     * Get document name
     *
     * @since 1.2.0
     *
     * @return string Document name
     */
    public function get_name()
    {
        return self::POPUP_DOCUMENT;
    }

    /**
     * Get document type
     *
     * @since 1.2.0
     *
     * @return string Document type
     */
    public static function get_type()
    {
        return self::POPUP_DOCUMENT;
    }

    /**
     * Get document title
     *
     * @since 1.2.0
     *
     * @return string Document title
     */
    public static function get_title()
    {
        return esc_html__('Popup', 'addons-for-elementor-builder');
    }

    /**
     * Get document properties
     *
     * @since 1.2.0
     *
     * @return array Document properties
     */
    public static function get_properties()
    {
        $properties = parent::get_properties();

        $properties['admin_tab_group'] = '';
        $properties['support_kit'] = true;

        return $properties;
    }

    /**
     * Register Popup document controls
     *
     * @since 1.2.0
     */
    protected function register_controls()
    {
        $this->CHelper->add_set_sctn($this, 'sts1', '', function () {}, [], ['show' => 'no']);
        /**
         *
         * Overlay
         *
         */
        $slctr = '{{WRAPPER}} .afeb-popup-overlay';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Overlay', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'ovrly_bg', $opt[0]);
        }, [$slctr]);
        /**
         *
         * Popup
         *
         */
        $slctr = '{{WRAPPER}} .afeb-popup-container';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Popup', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'ppup_bg', $opt[0]);
            $this->CHelper->res_pad($obj, 'ppup_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'ppup_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ppup_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'ppup_bx_shdo', $opt[0]);
        }, [$slctr]);
        /**
         *
         * Close Button
         *
         */
        $slctr = '{{WRAPPER}} .afeb-popup-close-btn';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Close Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('btn_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'btn_stl_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_bg', $opt[0]);
                $this->CHelper->clr($obj, 'btn_clr', $opt[0], esc_html__('Icon Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_pad($obj, 'btn_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'btn_stl_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'btn_clr_hvr', $opt[0], esc_html__('Icon Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_pad($obj, 'btn_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$slctr]);
        parent::register_controls();
        /**
         *
         * Settings
         *
         */
        $this->CHelper->add_set_sctn($this, 'sts2', esc_html__('Display Settings', 'addons-for-elementor-builder'), function ($obj) {
            $cndtn = ['ppup_opn_evnt!' => ''];
            $this->CHelper->raw_html(
                $obj,
                'use_dsply_cndtn',
                esc_html__(
                    'For more advanced settings, use the «Conditional Display» option in the Advanced tab',
                    'addons-for-elementor-builder'
                ),
                'elementor-panel-alert elementor-panel-alert-info',
                $cndtn
            );
            $this->CHelper->slct($obj, 'ppup_opn_evnt', esc_html__('Open Event', 'addons-for-elementor-builder'), Helper::get_array([
                '' => esc_html__('Not Selected', 'addons-for-elementor-builder'),
                'load' => esc_html__('On Page Load', 'addons-for-elementor-builder'),
                'exit' => esc_html__('On Exit', 'addons-for-elementor-builder')
            ]));
            $this->CHelper->num($obj, 'ppup_opn_dly', esc_html__('Open Delay (Sec)', 'addons-for-elementor-builder'), 0, null, 0.1, 1, '', '', ['ppup_opn_evnt' => 'load']);
            $this->CHelper->yn_swtchr($obj, 'ppup_shonc', esc_html__('Show once', 'addons-for-elementor-builder'), '', $cndtn);
            $this->CHelper->yn_swtchr($obj, 'ppup_esc_cls', esc_html__('Close on Press ESC', 'addons-for-elementor-builder'), 1, $cndtn);
            $this->CHelper->yn_swtchr($obj, 'ppup_dsabl_pge_scrl', esc_html__('Disable Page Scrolling', 'addons-for-elementor-builder'), '', $cndtn);
        });
        /**
         *
         * Layout
         *
         */
        $this->CHelper->add_set_sctn($this, 'sts4', esc_html__('Layout', 'addons-for-elementor-builder'), function ($obj) {
            $slctr = '{{WRAPPER}} .afeb-popup-container';
            $range = ['px' => ['min' => 100, 'max' => 2000], 'vw' => ['min' => 10, 'max' => 100]];
            $this->CHelper->res_sldr($obj, 'ppup_wdt', esc_html__('Width', 'addons-for-elementor-builder'), [$slctr => 'width: {{SIZE}}{{UNIT}}'], $range, ['px', 'vw']);
            $this->CHelper->yn_swtchr($obj, 'ppup_cstm_hght', esc_html__('Custom Height', 'addons-for-elementor-builder'));
            $slctr = '{{WRAPPER}} .afeb-popup-container-inner';
            $range = ['px' => ['min' => 100, 'max' => 1000], 'vh' => ['min' => 10, 'max' => 100]];
            $this->CHelper->res_sldr($obj, 'ppup_hght', esc_html__('Height', 'addons-for-elementor-builder'), [$slctr => 'height: {{SIZE}}{{UNIT}}'], $range, ['px', 'vh'], ['ppup_cstm_hght' => 'yes']);
            $this->CHelper->dvdr($obj, 'div_1');
            $this->CHelper->hed($obj, 'ppup_almnt', esc_html__('Alignment', 'addons-for-elementor-builder'));
            $slctr = '{{WRAPPER}} .afeb-template-popup';
            $this->CHelper->res_chse($obj, 'ppup_almnt_hrzntl', esc_html__('Horizontal Align', 'addons-for-elementor-builder'), [
                'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-left'],
                'center' => ['title' => esc_html__('Center', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-center'],
                'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-right']
            ], [$slctr => 'justify-content: {{VALUE}}']);
            $this->CHelper->res_chse($obj, 'ppup_almnt_vrtcl', esc_html__('Vertical Align', 'addons-for-elementor-builder'), [
                'flex-start' => ['title' => esc_html__('Top', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-top'],
                'center' => ['title' => esc_html__('Middle', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-middle'],
                'flex-end' => ['title' => esc_html__('Bottom', 'addons-for-elementor-builder'), 'icon' => 'eicon-v-align-bottom']
            ], [$slctr => 'align-items: {{VALUE}}']);
            $this->CHelper->dvdr($obj, 'div_2');
            $this->CHelper->hed($obj, 'ppup_motn', esc_html__('Motion', 'addons-for-elementor-builder'));
            $this->CHelper->anim($obj, 'ppup_anim', esc_html__('Animation', 'addons-for-elementor-builder'), 'fadeIn', 'frntnd');
            $range = ['px' => ['min' => 0, 'max' => 9999]];
            $this->CHelper->sldr($obj, 'ppup_zindx', esc_html__('ZIndex', 'addons-for-elementor-builder'), ['{{WRAPPER}}' => 'z-index: {{SIZE}}'], $range, ['px']);
        });
        /**
         *
         * Overlay
         *
         */
        $this->CHelper->add_set_sctn($this, 'sts5', esc_html__('Overlay', 'addons-for-elementor-builder'), function ($obj) {

            $this->CHelper->chse($obj, 'ppup_ovrly', esc_html__('Show Overlay', 'addons-for-elementor-builder'), [
                'no' => ['title' => esc_html__('No', 'addons-for-elementor-builder'), 'icon' => 'eicon-close'],
                'yes' => ['title' => esc_html__('Yes', 'addons-for-elementor-builder'), 'icon' => 'eicon-preview-medium']
            ], ['{{WRAPPER}} .afeb-popup-overlay' => '{{VALUE}}'], 1, 'yes', [], [
                'no' => 'display: none !important',
                'yes' => 'display: block'
            ]);
            $cndtn = ['ppup_opn_evnt!' => '', 'ppup_ovrly' => 'yes'];
            $this->CHelper->yn_swtchr($obj, 'ppup_ovrly_cls', esc_html__('Close On Click', 'addons-for-elementor-builder'), 1, $cndtn);
        });
        /**
         *
         * Close Button
         *
         */
        $this->CHelper->add_set_sctn($this, 'sts6', esc_html__('Close Button', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->chse($obj, 'ppup_btn_cls', esc_html__('Show Close Button', 'addons-for-elementor-builder'), [
                'no' => ['title' => esc_html__('No', 'addons-for-elementor-builder'), 'icon' => 'eicon-close'],
                'yes' => ['title' => esc_html__('Yes', 'addons-for-elementor-builder'), 'icon' => 'eicon-preview-medium']
            ], ['{{WRAPPER}} .afeb-popup-close-btn' => '{{VALUE}}'], 1, 'yes', [], [
                'no' => 'display: none !important',
                'yes' => 'display: flex'
            ]);
            $slctr = '{{WRAPPER}} .afeb-popup-close-btn';
            $range = ['px' => ['min' => -100, 'max' => 100]];
            $this->CHelper->res_sldr($obj, 'ppup_vr_pos', esc_html__('Vertical Position', 'addons-for-elementor-builder'), [$slctr => 'top: {{SIZE}}{{UNIT}}'], $range, ['px'], ['ppup_btn_cls' => 'yes']);
            $this->CHelper->res_sldr($obj, 'ppup_hr_pos', esc_html__('Horizontal Position', 'addons-for-elementor-builder'), [$slctr => 'right: {{SIZE}}{{UNIT}}'], $range, ['px'], ['ppup_btn_cls' => 'yes']);
        });
        do_action('afeb/document/settings/after_render_setting_section', $this);
    }
}
