<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Helper;
use AFEB\Controls\CHelper;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" SoundPlayer Widgets Class
 * 
 * @class SoundPlayer
 * @version 1.0.0
 */
class SoundPlayer extends Widget_Base
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
     * SoundPlayer Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->sound_player_style();
        $this->assets->howler_script();
        $this->assets->sound_player_script();
    }

    /**
     * Get widget name
     *
     * Retrieve Sound Player widget name
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_sound_player';
    }

    /**
     * Get widget title
     *
     * Retrieve Sound Player widget title
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Sound Player', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * Retrieve Sound Player widget icon
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-sound-player';
    }

    /**
     * Get widget categories
     *
     * Retrieve Sound Player widget categories
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
     * Retrieve the list of keywords the widget belongs to
     *
     * @since 1.0.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['soundplayer', 'sound_player', esc_html__('Sound Player', 'addons-for-elementor-builder')];
    }


    /**
     * Register Sound Player widget controls
     *
     * Adds different input fields to allow the user to change and customize the widget settings
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', 'Audio', function ($obj) {
            $this->CHelper->txt($obj, 'ttl', esc_html__('Title', 'addons-for-elementor-builder'), esc_html__('Title', 'addons-for-elementor-builder'));
            $this->CHelper->txt($obj, 'sb_ttl', __('Sub Title', 'addons-for-elementor-builder'), esc_html__('Sub Title', 'addons-for-elementor-builder'));
            $this->CHelper->ado($obj, 'ado');
            $this->CHelper->mda($obj, 'cvr');
        });
        /**
         *
         * Content Styles
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', __('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->sh_swtchr($obj, 'sh_snd_volm', __('Sound Volume', 'addons-for-elementor-builder'), 1);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Box Styles
         *
         */
        $spc_bx_slctr = '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-player-content';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'spc_bx_bg', $opt[0], [], [0]);
            $this->CHelper->res_pad($obj, 'spc_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'spc_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'spc_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'spc_shdo', $opt[0]);
        }, [$spc_bx_slctr]);
        /**
         *
         * Image Styles
         *
         */
        $sp_slctr = '{{WRAPPER}} .afeb-sound-player-1 ';
        $cvr_bx_slctr = $sp_slctr . '.afeb-sound-cover-wrap';
        $cvr_slctr = $cvr_bx_slctr . ' .afeb-sound-cover';
        $this->CHelper->add_stl_sctn($this, 'ss2', __('Image', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'img_bx_bg', $opt[0]);
            $range = ['px' => ['min' => 50, 'max' => 1000]];
            $this->CHelper->res_sldr($obj, 'ic_size', __('Size', 'addons-for-elementor-builder'), [$opt[1] => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'], $range);
            $this->CHelper->res_mar($obj, 'img_mar', $opt[1]);
            $this->CHelper->brdr($obj, 'img_brdr', $opt[1]);
            $this->CHelper->res_brdr_rdus($obj, 'img_rdus', $opt[1]);
            $this->CHelper->bx_shdo($obj, 'bx_shdo', $opt[1]);
        }, [$cvr_bx_slctr, $cvr_slctr]);
        /**
         *
         * Title Styles
         *
         */
        $dtls_bx_slctr = '{{WRAPPER}} .afeb-sound-details-wrap';
        $ttl_slctr = $dtls_bx_slctr . ' .afeb-sound-title';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Title', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'ttl_clr', $opt[0]);
            $this->CHelper->typo($obj, 'ttl_typo', $opt[0]);
            $this->CHelper->res_talmnt($obj, 'ttl_almnt', $opt[0]);
            $this->CHelper->res_pad($obj, 'ttl_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'ttl_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ttl_rdus', $opt[0]);
        }, [$ttl_slctr]);
        /**
         *
         * SubTitle Styles
         *
         */
        $sb_ttl_slctr = $dtls_bx_slctr . ' .afeb-sound-subtitle';
        $this->CHelper->add_stl_sctn($this, 'ss4', __('Sub Title', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'sb_ttl_clr', $opt[0]);
            $this->CHelper->typo($obj, 'sb_ttl_typo', $opt[0]);
            $this->CHelper->res_talmnt($obj, 'sb_ttl_almnt', $opt[0]);
            $this->CHelper->res_pad($obj, 'sb_ttl_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'sb_ttl_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'sb_ttl_rdus', $opt[0]);
        }, [$sb_ttl_slctr]);
        /**
         *
         * BTNS Box Styles
         *
         */
        $btn_box_slctr = $dtls_bx_slctr . ' .afeb-sound-controls';
        $this->CHelper->add_stl_sctn($this, 'ss15', __('Buttons Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'btns_bx_bg', $opt[0]);
            $this->CHelper->res_talmnt($obj, 'btns_bx_almnt', $opt[0]);
            $this->CHelper->res_pad($obj, 'btns_bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'btns_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'btns_bx_rdus', $opt[0]);
        }, [$btn_box_slctr]);
        /**
         *
         * 30 Seconds Back BTN Styles
         *
         */
        $btn_prev_slctr = $sp_slctr . '.afeb-sound-btn-sec-prev';
        $this->CHelper->add_stl_sctn($this, 'ss5', __('30 Seconds Back Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $opt[1] = $opt[0] . ' svg';
            $opt[2] = $opt[1] . ' path';
            $opt[3] = $opt[1] . ' circle';
            $this->CHelper->clr($obj, 'prv_clr', $opt[1] . ', ' . $opt[2] . ', ' . $opt[3]);
            $range = ['px' => ['min' => 15, 'max' => 30]];
            $this->CHelper->res_sldr($obj, 'prv_size', __('Size', 'addons-for-elementor-builder'), [$opt[1] => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'], $range, ['px']);
            $this->CHelper->res_mar($obj, 'prv_mar', $opt[0]);
        }, [$btn_prev_slctr]);
        /**
         *
         * Play And Pause BTN Styles
         *
         */
        $btn_ply_slctr = $sp_slctr . '.afeb-sound-btn-playing';
        $this->CHelper->add_stl_sctn($this, 'ss6', __('Play And Pause Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'plpu_bg', $opt[0]);
            $this->CHelper->clr($obj, 'plpu_clr', $opt[0] . ' svg, ' . $opt[0] . ' svg path');
            $range = ['px' => ['min' => 40, 'max' => 65]];
            $this->CHelper->res_sldr($obj, 'plpu_size', __('Size', 'addons-for-elementor-builder'), [$opt[0] => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'], $range, ['px']);
            $this->CHelper->res_mar($obj, 'plpu_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'plpu_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'plpu_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'plpu_rdus', $opt[0]);
        }, [$btn_ply_slctr]);
        /**
         *
         * 30 Seconds Back BTN Styles
         *
         */
        $btn_next_slctr = $sp_slctr . '.afeb-sound-btn-sec-next';
        $this->CHelper->add_stl_sctn($this, 'ss7', __('30 Seconds Forward Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $opt[1] = $opt[0] . ' svg';
            $opt[2] = $opt[1] . ' path';
            $opt[3] = $opt[1] . ' circle';
            $this->CHelper->clr($obj, 'nxt_clr', $opt[1] . ', ' . $opt[2] . ', ' . $opt[3]);
            $range = ['px' => ['min' => 15, 'max' => 30]];
            $this->CHelper->res_sldr($obj, 'nxt_size', __('Size', 'addons-for-elementor-builder'), [$opt[1] => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'], $range, ['px']);
            $this->CHelper->res_mar($obj, 'nxt_mar', $opt[0]);
        }, [$btn_next_slctr]);
        /**
         *
         * Volume BTN Styles
         *
         */
        $vlum_slctr = $sp_slctr . '.afeb-sound-volume';
        $this->CHelper->add_stl_sctn($this, 'ss8', __('Volume Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'vlum_clr', $opt[0]);
            $range = ['px' => ['min' => 15, 'max' => 30]];
            $this->CHelper->res_sldr($obj, 'vlum_size', __('Size', 'addons-for-elementor-builder'), [$opt[0] => 'font-size: {{SIZE}}{{UNIT}}'], $range, ['px']);
            $this->CHelper->res_mar($obj, 'vlum_mar', $opt[0]);
        }, [$vlum_slctr]);
        /**
         *
         * Volume Box Styles
         *
         */
        $vlum_bx_slctr = $vlum_slctr . ' .afeb-sound-volume-box';
        $this->CHelper->add_stl_sctn($this, 'ss9', __('Volume Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $opt[1] = $opt[0] . ' input[type="range"]::-webkit-slider-runnable-track';
            $this->CHelper->bg_grp_ctrl($obj, 'vlum_bx_bg', $opt[0]);
            $this->CHelper->bg_clr($obj, 'vlum_bx_br_clr', $opt[1], __('Bar Color', 'addons-for-elementor-builder'));
            $this->add_control(
                'vlum_bx_act_clr',
                [
                    'label' => esc_html__('Active Color', 'addons-for-elementor-builder'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-volume input[type="range"]::-webkit-slider-thumb' => 'background-color:{{VALUE}};box-shadow: -407px 0 0 400px {{VALUE}};border-color:{{VALUE}}',
                        '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-volume input[type="range"]::-moz-range-thumb' => 'background-color:{{VALUE}};box-shadow: -407px 0 0 400px {{VALUE}};border-color:{{VALUE}}',
                    ]
                ]
            );
            $range = ['px' => ['min' => 2, 'max' => 8]];
            $this->CHelper->res_sldr($obj, 'vlum_bx_hght', __('Height', 'addons-for-elementor-builder'), [$opt[1] => 'height: {{SIZE}}{{UNIT}}'], $range, ['px']);
            $this->CHelper->res_pad($obj, 'vlum_bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'vlum_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'vlum_bx_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'vlum_bx_shdo', $opt[0]);
        }, [$vlum_bx_slctr]);
        /**
         *
         * Seek Bar Styles
         *
         */
        $sb_br_slctr = $sp_slctr . '.afeb-sound-timer-line';
        $sb_rng_slctr = $sb_br_slctr . ' input[type="range"]';
        $sb_stl_slctr = $sb_br_slctr . ' .afeb-stl';
        $this->CHelper->add_stl_sctn($this, 'ss12', __('Seek Bar', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $slctr = sprintf('%s, %s', $opt[0], $opt[1]);
            $this->CHelper->bg_grp_ctrl($obj, 'sb_bg', $opt[0]);
            $this->add_control(
                'sb_bg_act',
                [
                    'label' => esc_html__('Active Color', 'addons-for-elementor-builder'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-timer-progress span, {{WRAPPER}} .afeb-sound-player-1 .afeb-sound-timer-progress' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-timer-line .afeb-stl' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-timer-line input[type="range"]::-webkit-slider-thumb' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-timer-line input[type="range"]::-moz-range-thumb' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} .afeb-sound-player-1 .afeb-sound-timer-line input[type="range"]::-ms-thumb' => 'background-color: {{VALUE}}',
                    ]
                ]
            );
            $this->CHelper->res_mar($obj, 'sb_br_mar', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'sb_rdus', $slctr . ',' . $opt[2]);
        }, [$sb_br_slctr, $sb_rng_slctr, $sb_stl_slctr]);
        /**
         *
         * Time Counter Styles
         *
         */
        $tmr_slctr = $sp_slctr . '.afeb-sound-timer-time';
        $tmr_cntr_slctr = $tmr_slctr . ' .afeb-sound-timer-down';
        $this->CHelper->add_stl_sctn($this, 'ss13', __('Time Counter', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'tc_bg', $opt[0]);
            $this->CHelper->clr($obj, 'tc_clr', $opt[0]);
            $this->CHelper->typo($obj, 'tc_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'tc_mar', $opt[0], ['px'], [], '', null, 1);
            $this->CHelper->res_pad($obj, 'tc_pad', $opt[0], ['px'], [], '', null, 1);
            $this->CHelper->brdr($obj, 'tc_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tc_rdus', $opt[0]);
        }, [$tmr_cntr_slctr]);
        /**
         *
         * Fixed Time Styles
         *
         */
        $tmr_slctr = $sp_slctr . '.afeb-sound-timer-time';
        $tmr_fx_slctr = $tmr_slctr . ' .afeb-sound-timer-default';
        $this->CHelper->add_stl_sctn($this, 'ss14', __('Fixed Time', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'fx_bg', $opt[0]);
            $this->CHelper->clr($obj, 'fx_clr', $opt[0]);
            $this->CHelper->typo($obj, 'fx_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'fx_mar', $opt[0], ['px'], [], '', null, 1);
            $this->CHelper->res_pad($obj, 'fx_pad', $opt[0], ['px'], [], '', null, 1);
            $this->CHelper->brdr($obj, 'fx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'fx_rdus', $opt[0]);
        }, [$tmr_fx_slctr]);
    }

    /**
     * Render attributes
     *
     * @since 1.0.4
     * 
     * @param string $wid
     */
    protected function render_attrs($wid = '')
    {
        $classes = [];
        $classes[] = 'afeb-sound-player';
        $classes[] = 'afeb-sound-player-1';
        $classes[] = !is_admin() ? ' afeb-has-loader ' : '';
        $classes[] = "afeb-sound-player-$wid";
        $classes[] = 'afeb-sound-cols-none-buttons';

        $this->add_render_attribute(
            [
                'sound-player' => [
                    'class' => esc_attr(implode(' ', $classes)),
                    'data-player-id' => esc_attr($wid)
                ]
            ]
        );

        echo wp_kses_post($this->get_render_attribute_string('sound-player'));
    }

    /**
     * Written in PHP and used to generate the final HTML
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wid = $this->get_id();
?>
        <div <?php $this->render_attrs($wid); ?>>
            <?php
            $sound_audio = isset($settings['ado']) && !empty($settings['ado']['url']) ? $settings['ado']['url'] : '';
            $meta = $sound_audio ? get_post_meta($settings['ado']['id'], '_wp_attachment_metadata', true) : [];
            $length = $meta && isset($meta['length_formatted']) ? $meta['length_formatted'] : '00:00';
            $notice = false;
            $show_frame = !is_admin() ? true : trim($sound_audio) && $sound_audio !== 'undefined';
            ?>
            <div class="afeb-sound-player-content afeb-sound-player-content-<?php echo esc_attr($wid . $wid) . ($show_frame ? ' afeb-has-sound' : ''); ?>">
                <?php
                if ($show_frame) : ?>
                    <div class="afeb-sound-cols afeb-sound-cover-wrap">
                        <div class="afeb-sound-col-content">
                            <div class="afeb-sound-cover afeb-sound-pri-cover" style="background-image: url('<?php echo esc_url($settings['cvr']['url']); ?>')"></div>
                        </div>
                    </div>
                    <div class="afeb-sound-cols afeb-sound-cols-eq afeb-sound-details-wrap">
                        <div class="afeb-sound-col-content">
                            <div class="afeb-sound-title">
                                <?php echo esc_html($settings['ttl']); ?>
                            </div>
                            <div class="afeb-sound-subtitle">
                                <?php echo esc_attr($settings['sb_ttl']); ?>
                            </div>
                            <div class="afeb-sound-controls">
                                <a href="#" class="afeb-sound-control-btn afeb-sound-btn-sec-prev" data-player-id="<?php echo esc_attr($wid); ?>" data-hash="<?php echo esc_attr(md5($sound_audio)); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
                                        <g id="_30-seconds" data-name="30-seconds">
                                            <path id="Path_5" data-name="Path 5" d="M182.858,272.6c66.04-19.251,104.3-88.215,85.047-154.5a124.514,124.514,0,0,0-154.5-85.047A116.132,116.132,0,0,0,88.551,43.288l3.168.975A12.336,12.336,0,0,1,84.652,67.9L52,57.909a12.119,12.119,0,0,1-8.529-13.89,8.9,8.9,0,0,1,.731-3.655L56.384,8.44c0-.244.244-.487.244-.975a12.288,12.288,0,0,1,22.663,9.5l-3.168,8.042A146.106,146.106,0,0,1,148.254,6.247c81.148,0,146.7,65.8,146.7,146.944s-65.8,146.7-146.944,146.7a11.076,11.076,0,0,1-10.479-10.479,10.933,10.933,0,0,1,10.479-11.453A150.613,150.613,0,0,0,182.858,272.6ZM23.973,72.774A145.765,145.765,0,0,0,11.545,96.412h0c-2.681,6.092.487,13.159,6.58,15.84s13.4-.487,16.083-6.58A151.432,151.432,0,0,1,44.443,85.933l.244-.487C52.241,71.068,32.5,59.615,23.973,72.774Zm62.384,187.4a11.969,11.969,0,0,0-16.571,4.386c-3.168,5.849-1.462,13.4,4.386,16.571A180.263,180.263,0,0,0,98.3,292.338a12.3,12.3,0,0,0,8.285-23.15A124.9,124.9,0,0,1,86.357,260.171Zm-46.788-48.25h0a12.5,12.5,0,0,0-16.815-3.9,12.443,12.443,0,0,0-3.9,16.815,167.673,167.673,0,0,0,14.865,22.176,11.257,11.257,0,0,0,1.706,1.706c12.184,9.5,26.075-6.336,17.3-17.058A173.676,173.676,0,0,1,39.569,211.921Zm-13.4-40.7a154.987,154.987,0,0,1-1.462-21.2v-1.95c-1.462-15.84-23.638-14.865-24.369.244a136.523,136.523,0,0,0,1.706,26.318,6.489,6.489,0,0,0,.731,2.924,11.952,11.952,0,0,0,13.647,7.8,12.158,12.158,0,0,0,9.748-14.134Z" transform="translate(0)" />
                                            <g id="Group_26" data-name="Group 26" transform="translate(72.711 99.549)">
                                                <path id="Path_6" data-name="Path 6" d="M68.259,40.8A37.3,37.3,0,0,1,82.88,43.724a20.942,20.942,0,0,1,9.5,8.285A27.931,27.931,0,0,1,95.8,65.9v6.58a51.3,51.3,0,0,1-.487,6.092,18.537,18.537,0,0,1-1.95,5.117,18.17,18.17,0,0,1-3.412,4.143,14.763,14.763,0,0,1-5.117,2.681,14.435,14.435,0,0,1,5.6,3.168,19.082,19.082,0,0,1,3.9,4.874,28.423,28.423,0,0,1,2.437,6.336,37.835,37.835,0,0,1,.731,7.554v3.9a22.48,22.48,0,0,1-7.554,18.277,31.023,31.023,0,0,1-20.957,6.336h0a193.372,193.372,0,0,1-20.714-.975A172.344,172.344,0,0,1,30,137.3V116.587H59c2.681,0,5.361-.244,8.042-.487a7.136,7.136,0,0,0,3.9-1.706,4.367,4.367,0,0,0,.975-3.412v-2.437a5.733,5.733,0,0,0-1.218-3.9,10.222,10.222,0,0,0-3.412-2.193,38.3,38.3,0,0,0-5.6-.975L37.311,100.5V80.521L59.73,79.059a17.329,17.329,0,0,0,7.8-1.706,3.947,3.947,0,0,0,2.437-4.143V71.992a5.247,5.247,0,0,0-2.681-5.361,20.047,20.047,0,0,0-9.991-1.462H30.487V44.455c5.849-.975,11.941-1.95,18.277-2.681Z" transform="translate(-30 -40.769)" />
                                                <path id="Path_7" data-name="Path 7" d="M135.912,114.168V68.842a33.427,33.427,0,0,0-3.168-15.352,22.019,22.019,0,0,0-8.773-9.5,28.614,28.614,0,0,0-14.134-3.168H88.393a31.453,31.453,0,0,0-14.865,3.168,23.21,23.21,0,0,0-9.26,9.5A36.884,36.884,0,0,0,61.1,68.842v45.326a32.58,32.58,0,0,0,3.168,14.378,22.01,22.01,0,0,0,9.016,9.26,35.78,35.78,0,0,0,15.109,3.168h21.2a25.177,25.177,0,0,0,19.5-7.067A27.258,27.258,0,0,0,135.912,114.168Zm-25.344-1.95a6.634,6.634,0,0,1-7.554,5.6H94.973v.244a7.715,7.715,0,0,1-5.6-1.706,8.024,8.024,0,0,1-1.95-6.092V71.523a8.678,8.678,0,0,1,1.706-5.849,6.78,6.78,0,0,1,5.117-1.95h9.991a8.023,8.023,0,0,1,4.63,1.462,8.024,8.024,0,0,1,1.95,6.092v38.5A9.142,9.142,0,0,0,110.569,112.219Z" transform="translate(14.687 -40.787)" />
                                            </g>
                                        </g>
                                    </svg>
                                </a>
                                <a
                                    href="#"
                                    class="afeb-sound-control-btn afeb-sound-btn-playing afeb-sound-btn-play afeb-in-content"
                                    data-title="<?php echo isset($settings['ttl']) ? esc_attr($settings['ttl']) : ''; ?>"
                                    data-subtitle="<?php echo isset($settings['sb_ttl']) ? esc_attr($settings['sb_ttl']) : ''; ?>"
                                    data-cover="<?php echo isset($settings['cvr']['url']) ? esc_url($settings['cvr']['url']) : ''; ?>"
                                    data-duration="<?php echo isset($meta['length']) ? esc_html($meta['length']) : ''; ?>"
                                    data-length="<?php echo esc_attr($length); ?>"
                                    data-player-id="<?php echo esc_attr($wid); ?>"
                                    data-audio="<?php echo esc_url($sound_audio); ?>"
                                    data-hash="<?php echo esc_attr(md5($sound_audio)); ?>">
                                    <svg class="afeb-player-play-icon" xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
                                        <g id="Group_31" data-name="Group 31">
                                            <g id="Polygon_4" data-name="Polygon 4" transform="translate(343 -41) rotate(90)" fill="none">
                                                <path d="M 191.4999542236328 77.89984130859375 C 175.8775482177734 77.89984130859375 161.9220123291016 85.9970703125 154.1689605712891 99.55996704101562 L 66.07882690429688 253.6600189208984 C 58.3831787109375 267.1224975585938 58.43484497070312 283.167236328125 66.21707153320312 296.5798950195312 C 73.99923706054688 309.9924926757812 87.90313720703125 318 103.4099426269531 318 L 279.590087890625 318 C 295.0968933105469 318 309.0007934570312 309.9924926757812 316.782958984375 296.579833984375 C 324.5651245117188 283.167236328125 324.6167907714844 267.1224975585938 316.9210815429688 253.6600189208984 L 228.8310089111328 99.55990600585938 C 221.0778961181641 85.9970703125 207.1223754882812 77.89984130859375 191.4999542236328 77.89984130859375 M 191.4999847412109 60.89987182617188 C 211.7859344482422 60.89987182617188 232.0718688964844 70.97430419921875 243.5897827148438 91.12319946289062 L 331.6798400878906 245.2233123779297 C 354.5452575683594 285.2227172851562 325.6637268066406 335 279.590087890625 335 L 103.4099426269531 335 C 57.33624267578125 335 28.45477294921875 285.2227172851562 51.320068359375 245.2233123779297 L 139.4101867675781 91.12319946289062 C 150.9281005859375 70.97430419921875 171.2140502929688 60.89987182617188 191.4999847412109 60.89987182617188 Z" stroke="none" fill="#000" />
                                            </g>
                                        </g>
                                    </svg>
                                    <svg class="afeb-player-pause-icon" xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
                                        <g id="Group_28" data-name="Group 28">
                                            <rect id="Rectangle_5" data-name="Rectangle 5" width="70" height="292" rx="35" />
                                            <rect id="Rectangle_6" data-name="Rectangle 6" width="70" height="292" rx="35" transform="translate(182 4)" />
                                        </g>
                                    </svg>
                                </a>
                                <a
                                    href="#"
                                    class="afeb-sound-control-btn afeb-sound-btn-sec-next"
                                    data-player-id="<?php echo esc_attr($wid); ?>"
                                    data-hash="<?php echo esc_attr(md5($sound_audio)); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
                                        <g id="_30-seconds2" data-name="30-seconds2">
                                            <path id="Path_8" data-name="Path 8" d="M147.363,277.842A10.942,10.942,0,0,1,157.851,289.3a11.085,11.085,0,0,1-10.487,10.487A146.5,146.5,0,0,1,.3,153.216C.3,72,65.905,6.152,147.12,6.152a148.861,148.861,0,0,1,72.19,18.779l-3.171-8.048a12.3,12.3,0,0,1,22.681-9.512c.244.244.244.488.244.976L251.259,40.3a9.786,9.786,0,0,1,.732,3.658,12.174,12.174,0,0,1-8.536,13.658l-32.681,10A12.346,12.346,0,0,1,203.7,43.955l3.171-.976A116.226,116.226,0,0,0,182,32.736C115.9,13.469,46.638,51.759,27.371,117.852s19.023,135.357,85.116,154.624A114.3,114.3,0,0,0,147.363,277.842ZM271.746,72.733c-8.536-12.926-28.535-1.463-20.974,12.682l.244.488a126.52,126.52,0,0,1,10.243,19.755,12.3,12.3,0,1,0,22.681-9.512h0a194.718,194.718,0,0,0-12.194-23.413Zm-82.921,197.06a12.022,12.022,0,0,0-7.317,15.609,12.345,12.345,0,0,0,15.609,7.56,127.612,127.612,0,0,0,24.145-11.219,12.131,12.131,0,1,0-12.194-20.974A169.617,169.617,0,0,1,188.824,269.794Zm54.143-37.8c-9.024,10.731,5.122,26.584,17.316,17.072a11.266,11.266,0,0,0,1.707-1.707,153.21,153.21,0,0,0,14.877-22.194,12.215,12.215,0,0,0-20.73-12.926h0Q249.918,222.48,242.967,231.991Zm26.34-60.484a12.172,12.172,0,0,0,23.413,6.341c.244-.976.488-1.707.732-2.683a138.93,138.93,0,0,0,1.463-26.584c-.732-15.121-22.925-16.1-24.389-.244v1.951a88.657,88.657,0,0,1-1.219,21.218Z" transform="translate(0 0)" />
                                            <g id="Group_27" data-name="Group 27" transform="translate(72.734 99.774)">
                                                <path id="Path_9" data-name="Path 9" d="M68.29,40.8a37.328,37.328,0,0,1,14.633,2.927,20.959,20.959,0,0,1,9.512,8.292,27.954,27.954,0,0,1,3.414,13.9v6.585a51.341,51.341,0,0,1-.488,6.1,18.553,18.553,0,0,1-1.951,5.122A18.184,18.184,0,0,1,90,87.87a14.775,14.775,0,0,1-5.122,2.683,14.446,14.446,0,0,1,5.609,3.171,19.1,19.1,0,0,1,3.9,4.878,28.445,28.445,0,0,1,2.439,6.341,37.865,37.865,0,0,1,.732,7.56v3.9A22.5,22.5,0,0,1,90,134.7a31.048,31.048,0,0,1-20.974,6.341h0a193.528,193.528,0,0,1-20.73-.976A172.488,172.488,0,0,1,30,137.379v-20.73H59.022c2.683,0,5.366-.244,8.048-.488a7.142,7.142,0,0,0,3.9-1.707,4.371,4.371,0,0,0,.976-3.414V108.6a5.737,5.737,0,0,0-1.219-3.9,10.23,10.23,0,0,0-3.414-2.195,38.336,38.336,0,0,0-5.609-.976l-24.389-.976v-20L59.754,79.09a17.343,17.343,0,0,0,7.8-1.707A3.95,3.95,0,0,0,70,73.237V72.017a5.251,5.251,0,0,0-2.683-5.366,20.064,20.064,0,0,0-10-1.463H30.488V44.458c5.853-.976,11.95-1.951,18.291-2.683Z" transform="translate(-30 -40.769)" />
                                                <path id="Path_10" data-name="Path 10" d="M135.973,114.228V68.865A33.454,33.454,0,0,0,132.8,53.5a22.037,22.037,0,0,0-8.78-9.512,28.637,28.637,0,0,0-14.145-3.171H88.415a31.479,31.479,0,0,0-14.877,3.171A23.229,23.229,0,0,0,64.271,53.5,36.913,36.913,0,0,0,61.1,68.865v45.363a32.607,32.607,0,0,0,3.171,14.389,22.028,22.028,0,0,0,9.024,9.268,35.809,35.809,0,0,0,15.121,3.171h21.218a25.2,25.2,0,0,0,19.511-7.073A27.28,27.28,0,0,0,135.973,114.228Zm-25.364-1.951a6.639,6.639,0,0,1-7.56,5.609H95v.244a7.721,7.721,0,0,1-5.609-1.707,8.03,8.03,0,0,1-1.951-6.1V71.548a8.685,8.685,0,0,1,1.707-5.853,6.786,6.786,0,0,1,5.122-1.951h10a8.03,8.03,0,0,1,4.634,1.463,8.03,8.03,0,0,1,1.951,6.1v38.534A9.15,9.15,0,0,0,110.609,112.277Z" transform="translate(14.749 -40.787)" />
                                            </g>
                                        </g>
                                    </svg>
                                </a>
                                <?php if ($settings['sh_snd_volm'] === 'yes') : ?>
                                    <div class="afeb-sound-volume">
                                        <i class="fas fa-volume-up"></i>
                                        <div class="afeb-sound-volume-box">
                                            <input type="range" class="afeb-change-sound-volume" min="0" max="100" step="1" value="50" orient="horezintal">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="afeb-sound-cols afeb-sound-cols-eq afeb-sound-timer-wrap" id="afeb-timer-progress-<?php echo esc_attr($wid); ?>-<?php echo esc_attr(md5($sound_audio)); ?>">
                        <div class="afeb-sound-col-content">
                            <div class="afeb-sound-timer-line">
                                <span class="afeb-stl"></span>
                                <div class="afeb-sound-timer-progress" data-player-id="<?php echo esc_attr($wid); ?>"><span></span>
                                </div>
                                <input dir="<?php echo is_rtl() ? 'rtl' : 'ltr' ?>" type="range" min="0" max="100" value="0" class="afeb-sound-player-range" data-player-id="<?php echo esc_attr($wid); ?>" data-hash="<?php echo esc_attr(md5($sound_audio)); ?>">
                            </div>
                            <div class="afeb-sound-timer-time">
                                <span class="afeb-sound-timer-down">00:00</span>
                                <span class="afeb-sound-timer-default" title="<?php echo esc_html__('Remaining Time', 'addons-for-elementor-builder'); ?>">
                                    <?php echo esc_html($length); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php
                else :
                    $notice = true;
                endif;
                ?>
            </div>
            <?php if ($notice) echo wp_kses(
                Helper::front_notice(esc_html__('Please select a valid audio file', 'addons-for-elementor-builder'), 'error'),
                Helper::allowed_tags(['div'])
            ); ?>
        </div>
<?php
    }
}
