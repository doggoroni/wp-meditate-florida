<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" AlertBox Widget Class
 * 
 * @class AlertBox
 * @version 1.0.0
 */
class AlertBox extends Widget_Base
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
     * AlertBox Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->alert_box_style();
        if (is_rtl()) {
            $this->assets->rtl_alert_box_style();
        }
        $this->assets->alert_box_script();
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
        return 'afeb_alert_box';
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
        return esc_html__('Alert Box', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-alert-box';
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
        return ['alert_box', 'alertbox', esc_html__('Alert Box', 'addons-for-elementor-builder')];
    }

    /**
     * Register Alert Box widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Alert Box', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->txt_area($obj, 'txt_alrt', 'Text', __('Write the alert text...', 'addons-for-elementor-builder'));
            $this->CHelper->sh_swtchr($obj, 'btn_cls', __('Close Button', 'addons-for-elementor-builder'), 1);
            $this->CHelper->icn($obj, 'btn_cls_ic', 'fa fa-times', 'fa-solid', esc_html__('Icon', 'addons-for-elementor-builder'), 1, 1, ['btn_cls' => 'yes']);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Content Styles
         *
         */
        $alert_slctr = '{{WRAPPER}} .afeb-alert-box';
        $content_slctr = '{{WRAPPER}} .afeb-alert-box-content';
        $this->CHelper->add_stl_sctn($this, 'ss1', __('Content', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'bx_bg', $opt[0]);
            $this->CHelper->clr($obj, 'bx_clr', $opt[1]);
            $this->CHelper->typo($obj, 'bx_typo', $opt[1]);
            $this->CHelper->talmnt($obj, 'bx_almnt', $opt[1]);
            $this->CHelper->pad($obj, 'bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'bx_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'bx_shdo', $opt[0]);
        }, [$alert_slctr, $content_slctr]);
        /**
         *
         * Close BTN Styles
         *
         */
        $this->CHelper->add_stl_sctn($this, 'ss2', __('Close Button', 'addons-for-elementor-builder'), function ($obj) {
            $btn_slctr = '{{WRAPPER}} .close-alert-box';
            $obj->start_controls_tabs('itms_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't1', __('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_bg', $opt[0]);
                $this->CHelper->clr($obj, 'btn_clr', $opt[0] . '>svg');
                $size = ['px' => ['min' => 15, 'max' => 40]];
                $this->CHelper->res_sldr($obj, 'btn_bx_size', __('Box Icon Size', 'addons-for-elementor-builder'), [$opt[0] => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'], $size, ['px']);
                $size = ['px' => ['min' => 10, 'max' => 30]];
                $this->CHelper->res_sldr($obj, 'btn_ic_size', __('Icon Size', 'addons-for-elementor-builder'), [$opt[0] . '>svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'], $size, ['px']);
                $this->CHelper->mar($obj, 'btn_mar', $opt[0], ['px'], [], '', null, 1);
                $this->CHelper->pad($obj, 'btn_pad', $opt[0], ['px'], ['top', 'bottom'], '', null, 1);
                $this->CHelper->brdr($obj, 'btn_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_shdo', $opt[0]);
            }, [$btn_slctr]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't2', __('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'btn_clr_hvr', $opt[0] . '>svg');
            }, [$btn_slctr . ':hover']);
            $obj->end_controls_tabs();
        }, [], ['btn_cls' => 'yes']);
    }

    /**
     * Render AlertBox widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wid = $this->get_id();
        $btn_cls = $settings['btn_cls'] == 'yes';
        $this->add_inline_editing_attributes('txt_alrt');
?>
        <div class="afeb-alert-box afeb-alert-box-<?php echo esc_attr($wid); ?>">
            <?php if ($btn_cls) : ?>
                <span class="close-alert-box">
                    <?php Icons_Manager::render_icon($settings['btn_cls_ic']); ?>
                </span>
            <?php endif; ?>
            <div class="afeb-alert-box-content elementor-inline-editing" <?php $this->print_render_attribute_string('txt_alrt'); ?>>
                <?php echo esc_html($this->print_unescaped_setting('txt_alrt')); ?>
            </div>
        </div>
<?php
    }
}
