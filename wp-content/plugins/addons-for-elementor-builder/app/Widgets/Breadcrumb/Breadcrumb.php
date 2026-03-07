<?php

namespace AFEB\Widgets\Breadcrumb;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Breadcrumb Widget Class
 * 
 * @class Breadcrumb
 * @version 1.0.0
 */
class Breadcrumb extends Widget_Base
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
     * Breadcrumb Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->breadcrumb_style();
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
        return 'afeb_breadcrumb';
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
        return esc_html__('Breadcrumb', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-breadcrumb';
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
        return ['breadcrumb', 'bread_crumb', esc_html__('Breadcrumb', 'addons-for-elementor-builder')];
    }

    /**
     * Register Breadcrumb widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Breadcrumb', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'itm_lnk_state', __('Disable Item Link', 'addons-for-elementor-builder'));
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Items Styles
         *
         */
        $brc_selecor = '{{WRAPPER}} .afeb-breadcrumb';
        $this->CHelper->add_stl_sctn($this, 'ss1', __('Content', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('itms_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't1', __('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->clr($obj, 'itms_clr', $opt[0] . ' span', esc_html__('Text Color', 'addons-for-elementor-builder'));
                $this->CHelper->clr($obj, 'itms_lnk_clr', $opt[0] . ' a', __('Link Color', 'addons-for-elementor-builder'));
                $this->CHelper->cstm_clr($obj, 'itms_sep_clr', $opt[0] . ' a::before', 'border-left-color: {{VALUE}}', __('Separator Color', 'addons-for-elementor-builder'));
                $this->CHelper->typo($obj, 'itms_typo', $opt[0] . ', ' . $opt[0] . ' span, ' . $opt[0] . ' a');
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't2', __('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->clr($obj, 'itms_clr_hvr', $opt[0] . ' span:hover', esc_html__('Text Color', 'addons-for-elementor-builder'));
                $this->CHelper->clr($obj, 'itms_lnk_clr_hvr', $opt[0] . ' a[href*="http"]:hover', __('Link Color', 'addons-for-elementor-builder'));
            }, [$opt[0]]);
            $obj->end_controls_tabs();
        }, [$brc_selecor]);
        /**
         *
         * Box Items Styles
         *
         */
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr_pckr($obj, 'bx_bg', __('Background Color', 'addons-for-elementor-builder'), '', [
                $opt[0] => 'background-color: {{VALUE}}',
                $opt[0] . ' a::after' => 'border-left-color: {{VALUE}}',
            ]);
            $this->CHelper->talmnt($obj, 'bx_almnt', $opt[0]);
            $this->CHelper->brdr($obj, 'bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'bx_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS]);
            $this->CHelper->bx_shdo($obj, 'bx_shdo', $opt[0]);
        }, [$brc_selecor]);
    }

    /**
     * Render Breadcrumb widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $helper = new Helper();
?>
        <div class="afeb-breadcrumb-wrap">
            <div class="afeb-breadcrumb" aria-label="breadcrumbs">
                <?php $helper->breadcrumb('', array('disable_item_link' => !trim($settings['itm_lnk_state']) ? 'no' : $settings['itm_lnk_state'])); ?>
            </div>
        </div>
<?php
    }
}
