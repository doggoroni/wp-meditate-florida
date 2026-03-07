<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Controls\Helper as New_Helper;
use AFEB\Helper;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Offcanvas Widget Class
 * 
 * @class Offcanvas
 * @version 1.2.0
 */
class Offcanvas extends Widget_Base
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
     * @var CHelper
     */
    private $controls;

    /**
     * Offcanvas Constructor
     * 
     * @since 1.2.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->controls = new New_Helper($this);
        $this->assets->offcanvas_style();
        $this->assets->offcanvas_script();
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
        return 'afeb_offcanvas';
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
        return esc_html__('Offcanvas', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-offcanvas';
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
        return ['offcanvas', 'menu', 'navigator', esc_html__('Offcanvas', 'addons-for-elementor-builder')];
    }

    public function fit_content_option() {}
    public function position_option() {}

    /**
     * Register Offcanvas widget controls
     *
     * @since 1.2.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Offcanvas', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->raw_html(
                $obj,
                'use_dsply_cndtn',
                esc_html__(
                    'Using this control, you can select a pre-built Elementor template to display in the off-canvas menu, or you can create your custom template using the Elementor template builder first and then select that template for this section',
                    'addons-for-elementor-builder'
                ),
                'elementor-panel-alert elementor-panel-alert-info'
            );
            $this->controls->dynamic_select('ofcnvs_slct_temp', [
                'label' => esc_html__('Choose a template', 'addons-for-elementor-builder'),
                'options' => 'get_posts_by_type',
                'query_slug' => 'elementor_library',
                'default' => '-1',
            ]);
        });
        /**
         *
         * Button
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Button', 'addons-for-elementor-builder'), function ($obj) {
            $txt = esc_html__('Menu', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'btn_txt', esc_html__('Button Text', 'addons-for-elementor-builder'), $txt, '', 'lblk');
            $this->CHelper->icn($obj, 'btn_ic', 'fa fa-bars', 'fa-solid', '', 1);
            $this->CHelper->res_talmnt($obj, 'btn_almnt', '{{WRAPPER}} .afeb-offcanvas-trigger');
            $this->CHelper->chse($obj, 'btn_dsply', esc_html__('Display', 'addons-for-elementor-builder'), [
                'block' => ['title' => esc_html__('Block', 'addons-for-elementor-builder'), 'icon'  => 'eicon-gallery-grid'],
                'inline-block' => ['title' => esc_html__('Inline', 'addons-for-elementor-builder'), 'icon'  => 'eicon-form-vertical']
            ]);
            $this->CHelper->chse($obj, 'btn_ic_almnt', esc_html__('Icon Alignment', 'addons-for-elementor-builder'), [
                'before' => ['title' => esc_html__('Before', 'addons-for-elementor-builder'), 'icon'  => 'eicon-h-align-left'],
                'after' => ['title' => esc_html__('After', 'addons-for-elementor-builder'), 'icon'  => 'eicon-h-align-right']
            ], [], 1, 'before');
        });
        /**
         *
         * Settings
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs3', esc_html__('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->chse($obj, 'drct', esc_html__('Direction', 'addons-for-elementor-builder'), [
                'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon'  => 'eicon-h-align-left'],
                'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon'  => 'eicon-h-align-right']
            ], [], 0, 'left');
            $this->position_option();
            $opt = '{{WRAPPER}} .afeb-offcanvas-nav';
            $slctr = [
                $opt => 'width: {{SIZE}}{{UNIT}}',
                $opt . '.afeb-offcanvas-nav-left' => '-webkit-transform: translateX(-{{SIZE}}{{UNIT}});' .
                    'transform: translateX(-{{SIZE}}{{UNIT}});' .
                    '-webkit-transition: translate3d(-{{SIZE}}{{UNIT}}, 0, 0);' .
                    'transition: translate3d(-{{SIZE}}{{UNIT}}, 0, 0);',
                $opt . '.afeb-offcanvas-nav-right' => '-webkit-transform: translateX({{SIZE}}{{UNIT}});' .
                    'transform: translateX({{SIZE}}{{UNIT}});' .
                    '-webkit-transition: translate3d({{SIZE}}{{UNIT}}, 0, 0);' .
                    'transition: translate3d({{SIZE}}{{UNIT}}, 0, 0);'
            ];
            $size = ['px' => ['min' => 180, 'max' => 1000], 'vw' => ['min' => 10, 'max' => 100]];
            $this->CHelper->res_sldr($obj, 'ofcnvs_wdt', esc_html__('Width', 'addons-for-elementor-builder'), $slctr, $size, ['px', 'vw']);
            $this->fit_content_option();
            $slctr = [$opt => 'height: {{SIZE}}{{UNIT}}'];
            $size = ['px' => ['min' => 180, 'max' => 500], 'vh' => ['min' => 10, 'max' => 100]];
            $this->CHelper->res_sldr($obj, 'ofcnvs_hght', esc_html__('Height', 'addons-for-elementor-builder'), $slctr, $size, ['px', 'vh'], ['fit_content!' => 'yes']);
            $this->CHelper->slct($obj, 'anim', esc_html__('Animations', 'addons-for-elementor-builder'), [
                'slide' => esc_html__('Slide', 'addons-for-elementor-builder'),
                'none' => esc_html__('None', 'addons-for-elementor-builder')
            ], 'slide');
            $size = ['px' => ['min' => 100, 'max' => 1000]];
            $this->CHelper->sldr($obj, 'anim_spd', esc_html__('Duration', 'addons-for-elementor-builder'), [], $size, ['px'], ['anim!' => 'none'], 500);
            if (!defined('AFEBP_LITE_VS')) $this->CHelper->hddn($obj, 'fit_content', '', '');
            $this->CHelper->yn_swtchr($obj, 'ovrly', esc_html__('Overlay', 'addons-for-elementor-builder'), 1);
            $this->CHelper->yn_swtchr($obj, 'esc_cls', esc_html__('Close on Press ESC', 'addons-for-elementor-builder'), 1);
            $this->CHelper->yn_swtchr($obj, 'any_wr_cls', esc_html__('Click anywhere to Close', 'addons-for-elementor-builder'), 1);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Button
         *
         */
        $btn_slctr = '{{WRAPPER}} .afeb-offcanvas-trigger-btn';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('btn_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'btn_stl_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_bg', $opt[0]);
                $this->CHelper->clr($obj, 'btn_clr', $opt[0], esc_html__('Content Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_mar($obj, 'btn_mar', $opt[0]);
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
                $this->CHelper->clr($obj, 'btn_clr_hvr', $opt[0], esc_html__('Content Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_mar($obj, 'btn_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'btn_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$btn_slctr]);
        /**
         *
         * Button Icon
         *
         */
        $btn_icn_slctr = $btn_slctr . '>.afeb-offcanvas-trigger-icon';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Button Icon', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('btn_ic_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'btn_ic_stl_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_ic_bg', $opt[0]);
                $this->CHelper->clr($obj, 'btn_ic_clr', $opt[0], esc_html__('Icon Color', 'addons-for-elementor-builder'));
                $slctr = [$opt[0] => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}'];
                $size = ['px' => ['min' => 0, 'max' => 100]];
                $this->CHelper->res_sldr($obj, 'btn_ic_sze', esc_html__('Size', 'addons-for-elementor-builder'), $slctr, $size, ['px']);
                $this->CHelper->res_mar($obj, 'btn_ic_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'btn_ic_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_ic_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_ic_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_ic_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'btn_ic_stl_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_ic_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'btn_ic_clr_hvr', $opt[0], esc_html__('Icon Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_mar($obj, 'btn_ic_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'btn_ic_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_ic_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_ic_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_ic_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$btn_icn_slctr], ['btn_ic[value]!' => '']);
        /**
         *
         * Button Text
         *
         */
        $btn_txt_slctr = $btn_slctr . '>.afeb-offcanvas-trigger-text';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Button Text', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('btn_txt_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'btn_txt_stl_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_txt_bg', $opt[0]);
                $this->CHelper->clr($obj, 'btn_txt_clr', $opt[0], esc_html__('Text Color', 'addons-for-elementor-builder'));
                $this->CHelper->typo($obj, 'btn_txt_typo', $opt[0]);
                $this->CHelper->res_mar($obj, 'btn_txt_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'btn_txt_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_txt_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_txt_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_txt_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'btn_txt_stl_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_txt_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'btn_txt_clr_hvr', $opt[0], esc_html__('Text Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_mar($obj, 'btn_txt_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'btn_txt_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_txt_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_txt_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_txt_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$btn_txt_slctr], ['btn_txt[value]!' => '']);
        /**
         *
         * Offcanvas Styles
         *
         */
        $ofcnvs_slctr = '{{WRAPPER}} .afeb-offcanvas-nav';
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Offcanvas', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'ofcnvs_bg', $opt[0], [], []);
            $this->CHelper->res_pad($obj, 'ofcnvs_pad', $opt[0], [], [], '', null, 0, '');
            $this->CHelper->brdr($obj, 'ofcnvs_brdr', $opt[0], '');
            $this->CHelper->brdr_rdus($obj, 'ofcnvs_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], [], [], '', null, 0, '');
            $this->CHelper->bx_shdo($obj, 'ofcnvs_shdo', $opt[0], '');
        }, [$ofcnvs_slctr]);
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
        $this->add_render_attribute(
            [
                'offcanvas' => [
                    'class' => 'afeb-offcanvas-trigger',
                    'data-settings' => [wp_json_encode(Helper::get_array([
                        'id' => esc_attr($wid),
                        'anim' => esc_attr($settings['anim']),
                        'anim_spd' => !empty($settings['anim_spd']['size']) ?  intval($settings['anim_spd']['size']) : 500,
                        'ovrly' => !empty($settings['ovrly']) ? 1 : 0,
                        'esc_cls' => !empty($settings['esc_cls']) ? 1 : 0,
                        'any_wr_cls' => !empty($settings['any_wr_cls']) ? 1 : 0
                    ], 'ocnvs_attr', $settings))]
                ]
            ]
        );

        echo wp_kses_post($this->get_render_attribute_string('offcanvas'));
    }

    /**
     * Render Offcanvas widget output on the frontend
     *
     * @since 1.2.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wid = $this->get_id();
        $classes = [];
        $classes[] = 'afeb-offcanvas-nav';
        $classes[] = 'afeb-offcanvas-nav-' .
            (!empty($settings['drct']) ? $settings['drct'] : 'left');
?>
        <div <?php $this->render_attrs($settings, $wid); ?>>
            <div class="afeb-offcanvas-trigger-btn">
                <?php
                ob_start();
                Icons_Manager::render_icon($settings['btn_ic'], ['class' => 'afeb-offcanvas-trigger-icon']);
                $icon = ob_get_clean();

                if ($settings['btn_ic_almnt'] == 'before')
                    echo $icon . ($settings['btn_dsply'] == 'block' ? '<br>' : '');

                if (!empty($settings['btn_txt'])): ?>
                    <span class="afeb-offcanvas-trigger-text"><?php echo esc_html($settings['btn_txt']); ?></span>
                <?php endif;
                if ($settings['btn_ic_almnt'] == 'after')
                    echo (($settings['btn_dsply'] == 'block') ? '<br>' : '') . $icon; ?>
            </div>
        </div>
        <div id="afeb-offcanvas-nav-toggle-<?php echo esc_attr($wid); ?>"
            class="<?php echo implode(' ', array_filter($classes, 'esc_attr')); ?>">
            <?php echo Helper::get_page_as_element($settings['ofcnvs_slct_temp']); ?>
        </div>
<?php
    }
}
