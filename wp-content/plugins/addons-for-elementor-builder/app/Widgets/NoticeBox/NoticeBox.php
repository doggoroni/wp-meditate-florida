<?php

namespace AFEB\Widgets\NoticeBox;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" NoticeBox Widget Class
 * 
 * @class NoticeBox
 * @version 1.0.0
 */
class NoticeBox extends Widget_Base
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
     * NoticeBox Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->notice_box_style();
    }

    /**
     * Retrieve NoticeBox widget name
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_notice_box';
    }

    /**
     * Retrieve NoticeBox widget title
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Notice Box', 'addons-for-elementor-builder');
    }

    /**
     * Retrieve NoticeBox widget icon
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-notice-box';
    }

    /**
     * Retrieve NoticeBox widget categories
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
        return ['notice', 'notice_box', esc_html__('Notice Box', 'addons-for-elementor-builder')];
    }

    /**
     * Register NoticeBox widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Icon', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->icn($obj, 'ic', 'fa fa-exclamation', 'fa-solid');
        });
        /**
         *
         * Main Content
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', __('Content', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->txt($obj, 'ttl_txt', esc_html__('Title', 'addons-for-elementor-builder'), esc_html__('Notice text', 'addons-for-elementor-builder'), esc_html__('Type title text here', 'addons-for-elementor-builder'));
            $this->CHelper->txt_area($obj, 'dsc_txt', 'Text', CHelper::$LIM, esc_html__('Type description here', 'addons-for-elementor-builder'));
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Box Styles
         *
         */
        $notice_box_slctr = '{{WRAPPER}} .afeb-notice-box';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'bx_bg', $opt[0]);
            $this->CHelper->bg_clr($obj, 'bx_brdr_ln_clr', $opt[0] . '::before', __('Border line color', 'addons-for-elementor-builder'));
            $this->CHelper->sldr($obj, 'bx_itms_gp', __('Items Gap', 'addons-for-elementor-builder'), [$opt[0] => 'gap: {{SIZE}}{{UNIT}}'], [], CHelper::BDSU);
            $this->CHelper->res_pad($obj, 'bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'bx_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'ic_shdo', $opt[0]);
        }, [$notice_box_slctr]);
        /**
         *
         * Icon Styles
         *
         */
        $notice_box_ic_slctr = '{{WRAPPER}} .afeb-notice-box .afeb-icon';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Icon', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'ic_bg_clr', $opt[0]);
            $this->CHelper->clr($obj, 'ic_clr', $opt[0] . ',' . $opt[0] . '>svg');
            $range = ['px' => ['min' => 0, 'max' => 500, 'step' => 1]];
            $selector = [$opt[0] => 'min-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}'];
            $this->CHelper->sldr($obj, 'ic_wdt', __('Box Width', 'addons-for-elementor-builder'), $selector, $range, CHelper::BDSU);
            $range = ['px' => ['min' => 5, 'max' => 100, 'step' => 1]];
            $this->CHelper->sldr($obj, 'ic_size', __('Size', 'addons-for-elementor-builder'), [$opt[0] . ' svg' => 'width: {{SIZE}}{{UNIT}}'], $range, CHelper::BDSU);
            $this->CHelper->slct($obj, 'ic_bg_bx_typ', __('Box Type', 'addons-for-elementor-builder'), [
                'shield' => esc_html__('Shield', 'addons-for-elementor-builder'),
                'circle' => esc_html__('Circle', 'addons-for-elementor-builder'),
                'square' => esc_html__('Square', 'addons-for-elementor-builder')
            ], 'shield');
            $this->CHelper->brdr($obj, 'ic_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ic_bx_rdus', $opt[0]);
        }, [$notice_box_ic_slctr]);
        /**
         *
         * Title Style
         *
         */
        $notice_title_slctr = '{{WRAPPER}} .afeb-notice-box .afeb-title';
        $this->CHelper->add_stl_sctn($this, 'ss3', __('Title Style', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'ttl_txt_clr', $opt[0]);
            $this->CHelper->typo($obj, 'ttl_typo', $opt[0]);
        }, [$notice_title_slctr]);
        /**
         *
         * Description Styles
         *
         */
        $notice_title_desc = '{{WRAPPER}} .afeb-notice-box .afeb-description';
        $this->CHelper->add_stl_sctn($this, 'ss4', __('Description Style', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'dsc_txt_clr', $opt[0]);
            $this->CHelper->typo($obj, 'dsc_typo', $opt[0]);
        }, [$notice_title_desc]);
    }

    /**
     * Render Notice Box widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->notice_box($settings);
    }

    /**
     * Display the Notice Box
     *
     * @since 1.0.0
     * 
     * @param array $settings
     */
    private function notice_box($settings = [])
    {
        $this->add_inline_editing_attributes('ttl_txt');
        $this->add_inline_editing_attributes('dsc_txt');
?>
        <div class="afeb-notice-box">
            <div class="afeb-icon <?php printf("afeb-%s", esc_attr($settings['ic_bg_bx_typ'])); ?>">
                <?php Icons_Manager::render_icon($settings['ic']); ?>
            </div>
            <div class="afeb-content">
                <div class="afeb-title elementor-inline-editing" <?php $this->print_render_attribute_string('ttl_txt'); ?>>
                    <?php echo esc_html($this->print_unescaped_setting('ttl_txt')); ?>
                </div>
                <div class="afeb-description elementor-inline-editing" <?php $this->print_render_attribute_string('dsc_txt'); ?>>
                    <?php echo esc_html($this->print_unescaped_setting('dsc_txt')); ?>
                </div>
            </div>
        </div>
<?php
    }
}
