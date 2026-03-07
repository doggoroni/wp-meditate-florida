<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Helper;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Lottie Widget Class
 * 
 * @class Lottie
 * @version 1.2.0
 */
class Lottie extends Widget_Base
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
     * Lottie Constructor
     * 
     * @since 1.2.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();

        $this->assets->lotties_pkg();
    }

    /**
     * Get widget name
     *
     * @since 1.2.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_lottie';
    }

    /**
     * Get widget title
     *
     * @since 1.2.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Lottie', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.2.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-lottie';
    }

    /**
     * Get widget categories
     *
     * @since 1.2.0
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
     * @since 1.2.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['lottie', 'animation', 'svg', esc_html__('Lottie', 'addons-for-elementor-builder')];
    }

    /**
     * Register Lottie widget controls
     *
     * @since 1.2.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Lottie', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->slct($obj, 'lti_src', esc_html__('Source', 'addons-for-elementor-builder'), [
                'mda' => esc_html__('Media File', 'addons-for-elementor-builder'),
                'url' => esc_html__('External URL', 'addons-for-elementor-builder')
            ], 'mda');
            $this->CHelper->mda($obj, 'lti_mda', ['lti_src' => 'mda'], ' ', esc_html__('Upload JSON File', 'addons-for-elementor-builder'), 1, ['application/json']);
            $this->CHelper->url($obj, 'lti_url', 0, 'URL', ['lti_src' => 'url'], '', [], 0, 1, esc_html__('External URL', 'addons-for-elementor-builder'));
        });
        /**
         *
         * Settings
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'lti_lop', esc_html__('Loop', 'addons-for-elementor-builder'), 1);
            $range = ['px' => ['min' => 0.1, 'max'  => 5, 'step' => 0.1]];
            $this->CHelper->sldr($obj, 'lti_ply_spd', esc_html__('Play Speed', 'addons-for-elementor-builder'), '', $range, ['px']);
            $this->CHelper->sldr($obj, 'lti_strt_pnt', esc_html__('Start Point', 'addons-for-elementor-builder'), '', [], ['%']);
            $this->CHelper->sldr($obj, 'lti_end_pnt', esc_html__('End Point', 'addons-for-elementor-builder'), '', [], ['%']);
            $this->CHelper->slct($obj, 'lti_rndr', esc_html__('Renderer', 'addons-for-elementor-builder'), [
                'svg' => esc_html__('SVG', 'addons-for-elementor-builder'),
                'canvas' => esc_html__('Canvas', 'addons-for-elementor-builder')
            ], 'svg');
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Map Style
         *
         */
        $lti_slctr = '{{WRAPPER}} .afeb-lottie-wrapper';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Lottie', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $opt[1] = $opt[0] . ' .afeb-lottie';
            $range = ['px' => ['min' => 10, 'max' => 1000], 'vw' => ['min' => 10, 'max' => 100]];
            $this->CHelper->res_sldr($obj, 'lti_wdt', esc_html__('Width', 'addons-for-elementor-builder'), [$opt[1] => 'width: {{SIZE}}{{UNIT}}'], $range, ['%', 'px', 'vw']);
            $this->CHelper->res_sldr($obj, 'lti_mxwdt', esc_html__('Max Width', 'addons-for-elementor-builder'), [$opt[1] => 'max-width: {{SIZE}}{{UNIT}}'], $range, ['%', 'px', 'vw']);
            $this->CHelper->res_talmnt($obj, 'lti_almnt', $opt[0]);
            $this->CHelper->dvdr($obj, 'div_1');
            $obj->start_controls_tabs('lti_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'lti_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $range = ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]];
                $this->CHelper->sldr($obj, 'lti_opcty', esc_html__('Opacity', 'addons-for-elementor-builder'), [$opt[0] => 'opacity: {{SIZE}}'], $range, ['px']);
                $this->CHelper->brdr($obj, 'lti_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'lti_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'lti_bx_shdo', $opt[0]);
            }, [$opt[1]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'lti_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $range = ['px' => ['min' => 0, 'max' => 1, 'step' => 0.1]];
                $this->CHelper->sldr($obj, 'lti_opcty_hvr', esc_html__('Opacity', 'addons-for-elementor-builder'), [$opt[0] => 'opacity: {{SIZE}}'], $range, ['px']);
                $this->CHelper->brdr($obj, 'lti_brdr_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'lti_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ' .afeb-lottie:hover']);
            $obj->end_controls_tabs();
        }, [$lti_slctr]);
    }

    /**
     * Render attributes
     *
     * @since 1.2.0
     * 
     * @param array $settings
     * @param string $wid
     */
    protected function render_attrs($settings = [], $wid = '')
    {
        if (isset($settings['lti_src'])) {
            $default_json_url = $this->assets->assets_url('docs/lottie.json');

            if ($settings['lti_src'] == 'mda') {
                $mda_url = !empty($settings['lti_mda']['url']) ? trim($settings['lti_mda']['url']) : '';
                $settings['lti_mda']['url'] = trim($mda_url) ? $mda_url : $default_json_url;
            } elseif ($settings['lti_src'] == 'url') {
                $url = !empty($settings['lti_url']['url']) ? trim($settings['lti_url']['url']) : '';
                $settings['lti_mda']['url'] = trim($url) ? $url : $default_json_url;
            }
        }

        $this->add_render_attribute(
            [
                'lottie' => [
                    'id' => "afeb-lottie-{$wid}",
                    'class' => 'afeb-lottie afeb-d-inline-block',
                    'data-settings' => [wp_json_encode(Helper::get_array($settings, 'lti_attr'))]
                ]
            ]
        );

        echo wp_kses_post($this->get_render_attribute_string('lottie'));
    }

    /**
     * Render Lottie widget output on the frontend
     *
     * @since 1.2.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wid = $this->get_id();
?>
        <div class="afeb-lottie-wrapper">
            <div <?php $this->render_attrs($settings, $wid); ?>></div>
        </div>
<?php
    }
}
