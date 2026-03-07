<?php

namespace AFEB\Widgets\InformationBox;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" InfoBox Widget Class
 * 
 * @class InfoBox
 * @version 1.0.0
 */
class InformationBox extends Widget_Base
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
     * InfoBox Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->info_box_style();
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
        return 'afeb_info_box';
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
        return esc_html__('Information Box', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-information-box';
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
        return ['informationbox', 'information_box_', esc_html__('Information Box', 'addons-for-elementor-builder')];
    }

    /**
     * Register Info Box widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Information Box', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->icn($obj, 'ic', 'fas fa-gem', 'fa-solid', '', 1);
            $this->CHelper->txt($obj, 'ttl', esc_html__('Title', 'addons-for-elementor-builder'), esc_html__('Title Here', 'addons-for-elementor-builder'));
            $this->CHelper->txt_area($obj, 'dsc_txt', esc_html__('Description', 'addons-for-elementor-builder'), CHelper::$LIM);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Icon Styles
         *
         */
        $icon_slctr = '{{WRAPPER}} .afeb-icon-box .afeb-icon-wrapper';
        $this->CHelper->add_stl_sctn($this, 'ss1', 'Icon', function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'ic_bg', $opt[0]);
            $this->CHelper->clr($obj, 'ic_clr', $opt[0] . ' i,' . $opt[0] . ' svg');
            $selector = [$opt[0] . ' svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'ic_size', __('Size', 'addons-for-elementor-builder'), $selector, ['px' => ['min' => 30, 'max' => 80]], ['px']);
            $selector = [$opt[0] => 'height: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 40, 'max' => 150]];
            $this->CHelper->res_sldr($obj, 'ic_bx_hght', __('Box Height', 'addons-for-elementor-builder'), $selector, $range, ['px']);
            $selector = [$opt[0] => 'width: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'ic_bx_wdt', __('Box Width', 'addons-for-elementor-builder'), $selector, $range, ['px']);
            $this->CHelper->res_pad($obj, 'ic_bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'ic_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ic_bx_rdus', $opt[0]);
        }, [$icon_slctr]);
        /**
         *
         * Box Styles
         *
         */
        $desc_slctr = '{{WRAPPER}} .afeb-info-box';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'bx_bg', $opt[0]);
            $this->CHelper->res_pad($obj, 'bx_pad', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'bx_rdus', $opt[0]);
        }, [$desc_slctr]);
        /**
         *
         * Title Styles
         *
         */
        $title_slctr = '{{WRAPPER}} .afeb-title';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Title', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'ttl_clr', $opt[0]);
            $this->CHelper->typo($obj, 'ttl_typo', $opt[0] . ' span');
            $this->CHelper->talmnt($obj, 'ttl_almnt', $opt[0]);
            $this->CHelper->res_mar($obj, 'ttl_mar', $opt[0]);
        }, [$title_slctr]);
        /**
         *
         * Description Styles
         *
         */
        $desc_slctr = '{{WRAPPER}} .afeb-content p';
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Description', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'dsc_clr', $opt[0]);
            $this->CHelper->typo($obj, 'dsc_typo', $opt[0]);
            $this->CHelper->talmnt($obj, 'dsc_almnt', $opt[0]);
            $this->CHelper->res_mar($obj, 'dsc_mar', $opt[0]);
        }, [$desc_slctr]);
        /**
         *
         * Back Box Styles
         *
         */
        $this->CHelper->add_stl_sctn($this, 'ss5', __('Back Box', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->bg_grp_ctrl($obj, 'bc_bx_bg', '{{WRAPPER}} .afeb-info-box::before');
        });
    }

    /**
     * Render Info Box widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->info_box($settings);
    }

    /**
     * Display the Info Box
     *
     * @since 1.0.0
     * 
     * @param array $settings
     */
    private function info_box($settings = [])
    {
        $this->add_inline_editing_attributes('ttl');
        $this->add_inline_editing_attributes('dsc_txt');
?>
        <div class="afeb-info-box">
            <?php if ($settings['ic']['value']) : ?>
                <div class="afeb-icon-box">
                    <div class="afeb-icon-wrapper">
                        <?php Icons_Manager::render_icon($settings['ic']); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="afeb-title">
                <span class="elementor-inline-editing" <?php $this->print_render_attribute_string('ttl'); ?>>
                    <?php echo esc_html($this->print_unescaped_setting('ttl')); ?>
                </span>
            </div>
            <div class="afeb-content">
                <p class="elementor-inline-editing" <?php $this->print_render_attribute_string('dsc_txt'); ?>>
                    <?php echo esc_html($this->print_unescaped_setting('dsc_txt')); ?>
                </p>
            </div>
        </div>
<?php
    }
}
