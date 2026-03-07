<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Helper;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Map Widget Class
 * 
 * @class Map
 * @version 1.2.0
 */
class Map extends Widget_Base
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
     * Map Constructor
     * 
     * @since 1.2.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();

        $this->assets->map_style();

        if (Helper::is_edit_mode()) {
            $this->assets->gmaps_pkg('');
            $this->assets->osmaps_pkg();
            $this->assets->map_script();
        }
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
        return 'afeb_map';
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
        return esc_html__('Map', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-map';
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
        return ['map', 'Map', 'location', esc_html__('Map', 'addons-for-elementor-builder')];
    }

    /**
     * Register Map widget controls
     *
     * @since 1.2.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Map', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->slct($obj, 'mp_prvdr', esc_html__('Map Provider', 'addons-for-elementor-builder'), [
                'gmap' => esc_html__('Google Map', 'addons-for-elementor-builder'),
                'osmap' => esc_html__('OpenStreetMap', 'addons-for-elementor-builder')
            ], 'osmap');
            $mrkr = new Repeater();
            $this->CHelper->txt($mrkr, 'mrkr_lat', esc_html__('Latitude', 'addons-for-elementor-builder'), '', '', 'dai');
            $this->CHelper->txt($mrkr, 'mrkr_lng', esc_html__('Longitude', 'addons-for-elementor-builder'), '', '', 'dai');
            $this->CHelper->txt($mrkr, 'mrkr_ttl', esc_html__('Title', 'addons-for-elementor-builder'));
            $this->CHelper->txt($mrkr, 'mrkr_cnt', esc_html__('Content', 'addons-for-elementor-builder'));
            $this->CHelper->rptr($obj, 'mrkr_itms', $mrkr->get_controls(), [
                [
                    'mrkr_lat' => '40.741895',
                    'mrkr_lng' => '-73.989308',
                    'mrkr_ttl' => esc_html__('New York', 'addons-for-elementor-builder'),
                    'mrkr_cnt' => esc_html__('New York', 'addons-for-elementor-builder')

                ]
            ], '{{ mrkr_ttl }}', esc_html__('Marker', 'addons-for-elementor-builder'));
        });
        /**
         *
         * Settings
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $cndtn = ['mp_prvdr' => 'gmap'];
            $this->CHelper->txt($obj, 'mp_key', esc_html__('Google API Key', 'addons-for-elementor-builder'), '', '', 'lblk,dai', $cndtn);
            $this->CHelper->raw_html(
                $obj,
                'dsbl_api_ky',
                esc_html__(
                    'The API key is disabled in the editor. To view changes with the API key, use the preview mode',
                    'addons-for-elementor-builder'
                ),
                'elementor-panel-alert elementor-panel-alert-info',
                $cndtn
            );
            $this->CHelper->sh_swtchr($obj, 'zom_cntrl', esc_html__('Zoom', 'addons-for-elementor-builder'), 1);
            $this->CHelper->slct($obj, 'mp_typ', esc_html__('Map Type', 'addons-for-elementor-builder'), [
                'roadmap' => esc_html__('Road Map', 'addons-for-elementor-builder'),
                'satellite' => esc_html__('Satellite', 'addons-for-elementor-builder'),
                'hybrid' => esc_html__('Hybrid', 'addons-for-elementor-builder'),
                'terrain' => esc_html__('Terrain', 'addons-for-elementor-builder')
            ], 'roadmap', $cndtn);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Map Style
         *
         */
        $mp_slctr = '{{WRAPPER}} .afeb-map';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Map', 'addons-for-elementor-builder'), function ($obj, $opt) {
            // $this->CHelper->txt_area($obj, 'jsn_styl', esc_html__('Json Code', 'addons-for-elementor-builder'));
            // $this->CHelper->raw_html($obj, 'jsn_styl_info', sprintf(__('First, go to %1s this %2s link, and then choose one of the styles, then copy the Json code of the chosen style and put it in the above entry', 'addons-for-elementor-builder'), '<a href="https://snazzymaps.com/" target="_blank">', '</a>'), 'elementor-panel-alert elementor-panel-alert-info');
            $obj->start_controls_tabs('mp_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'mp_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->cs_fltr($obj, 'mp_cs_fltr', $opt[0]);
                $this->CHelper->brdr($obj, 'mp_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'mp_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], [], [], '', null, 0, '');
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'mp_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->cs_fltr($obj, 'mp_cs_fltr_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$mp_slctr]);
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
        $mrkr = [];
        $mrkrs = [];
        $opts = [];
        $opts['el'] = '#afeb-map-' . $wid; // . '-' . rand(10, 100);
        $cntr = 0;
        foreach ($settings['mrkr_itms'] as $mrkr_itm):
            $mrkr['lat'] = isset($mrkr_itm['mrkr_lat']) ? floatval($mrkr_itm['mrkr_lat']) : '';
            $mrkr['lng'] = isset($mrkr_itm['mrkr_lng']) ? floatval($mrkr_itm['mrkr_lng']) : '';
            $mrkr['ttl'] = isset($mrkr_itm['mrkr_ttl']) ? esc_attr($mrkr_itm['mrkr_ttl']) : '';
            $mrkr['cntnt'] = isset($mrkr_itm['mrkr_cnt']) ? esc_attr($mrkr_itm['mrkr_cnt']) : '';

            $mrkrs[] = $mrkr;
            $cntr++;

            if ($cntr == 1) {
                $opts['lat'] = isset($mrkr_itm['mrkr_lat']) ? floatval($mrkr_itm['mrkr_lat']) : '';
                $opts['lng'] = isset($mrkr_itm['mrkr_lng']) ? floatval($mrkr_itm['mrkr_lng']) : '';
            }
        endforeach;

        $opts['zoomControl'] = !empty($settings['zom_cntrl']) ? true : false;
        $opts['zoom'] = 15;
        $opts['mapType'] = !empty($settings['mp_typ']) ? $settings['mp_typ'] : 'roadmap';
        $opts['mp_prvdr'] = !empty($settings['mp_prvdr']) ? $settings['mp_prvdr'] : 'gmap';

        $this->add_render_attribute(
            [
                'map' => [
                    'id' => esc_attr(ltrim($opts['el'], '#')),
                    'class' => 'afeb-map',
                    'data-settings' => [wp_json_encode(Helper::get_array([
                        'mrkrs' => $mrkrs,
                        'opts' => $opts,
                        // 'stls' => trim(preg_replace('/\s+/', ' ', $settings['jsn_styl']))
                    ], 'mp_attr'))]
                ]
            ]
        );

        echo wp_kses_post($this->get_render_attribute_string('map'));
    }

    /**
     * Before render Map widget
     *
     * @since 1.2.0
     */
    public function before_render()
    {
        parent::before_render();
        if (!Helper::is_edit_mode()) {
            $settings = $this->get_settings_for_display();
            $key = !empty($settings['mp_key']) ? $settings['mp_key'] : '';
            $mp_prvdr = !empty($settings['mp_prvdr']) ? $settings['mp_prvdr'] : 'gmap';

            if ($mp_prvdr == 'gmap') $this->assets->gmaps_pkg($key);
            else $this->assets->osmaps_pkg();

            $this->assets->map_script();
        }
    }

    /**
     * Render Map widget output on the frontend
     *
     * @since 1.2.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wid = $this->get_id();
?>
        <div <?php $this->render_attrs($settings, $wid); ?>></div>
<?php
    }
}
