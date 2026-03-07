<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Helper;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Hotspot Widget Class
 * 
 * @class Hotspot
 * @version 1.0.0
 */
class Hotspot extends Widget_Base
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
     * Hotspot Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->hotspot_style();
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
        return 'afeb_hotspot';
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
        return esc_html__('Hotspot', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-hotspot';
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
        return ['Hotspot', 'hotspot', esc_html__('Hotspot', 'addons-for-elementor-builder')];
    }

    /**
     * Register Hotspot widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', __('Image', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->mda($obj, 'image');
            $this->CHelper->img_sze($obj, 'media_thumbnail');
        });
        /**
         *
         * Content Styles
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', __('Hotspots', 'addons-for-elementor-builder'), function ($obj) {
            $items = new Repeater();
            $this->CHelper->chse($items, 'ht_mda_typ', __('Media Type', 'addons-for-elementor-builder'), [
                'none' => ['title' => __('None', 'addons-for-elementor-builder'), 'icon' => 'eicon-ban'],
                'icon' => ['title' => esc_html__('Icon', 'addons-for-elementor-builder'), 'icon' => 'eicon-star-o'],
                'image' => ['title' => __('Image', 'addons-for-elementor-builder'), 'icon' => 'eicon-image']
            ], [], 1, 'icon');
            $this->CHelper->icn($items, 'ht_ic', 'fas fa-plus', 'fa-solid', ' ', 0, 1, ['ht_mda_typ' => 'icon']);
            $this->CHelper->mda($items, 'ht_img', ['ht_mda_typ' => 'image']);
            $this->CHelper->img_sze($items, 'hts_tmbnl', ['ht_mda_typ' => 'image']);
            $this->CHelper->pr_tgl($items, 'ht_ofst_tgl', __('Offset', 'addons-for-elementor-builder'), esc_html__('Custom', 'addons-for-elementor-builder'), esc_html__('None', 'addons-for-elementor-builder'), 1);

            $items->start_popover();
            $selector = '{{WRAPPER}} .afeb-hotspot-wrapper {{CURRENT_ITEM}}';
            $range = ['px' => ['min' => -1000, 'max' => 1000], '%' => ['min' => 0, 'max' => 100]];
            $unit = CHelper::BDSU;
            $condition = ['ht_ofst_tgl' => 'yes'];
            $default = ['unit' => 'px', 'size' => 300];
            $this->CHelper->res_sldr($items, 'ht_ofst_x', __('Offset Left', 'addons-for-elementor-builder'), [$selector => 'left: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
            $this->CHelper->res_sldr($items, 'ht_ofst_y', __('Offset Top', 'addons-for-elementor-builder'), [$selector => 'top: {{SIZE}}{{UNIT}}'], $range, $unit, $condition, $default);
            $items->end_popover();

            $this->CHelper->dvdr($items, 'div_1');
            $this->CHelper->sh_swtchr($items, 'sh_toltp', __('Show Tooltip', 'addons-for-elementor-builder'), 1);
            $css = '--afeb-hotspot-tooltip-top:auto; --afeb-hotspot-tooltip-right:auto; --afeb-hotspot-tooltip-bottom:100%; --afeb-hotspot-tooltip-left:50%;' .
                '--afeb-hotspot-tooltip-transform-x: -50%; --afeb-hotspot-tooltip-transform-y: 0;' .
                '--afeb-hotspot-tooltip-margin: 0 0 10px 0; --afeb-hotspot-tooltip-before-top:auto; --afeb-hotspot-tooltip-before-right:auto;' .
                '--afeb-hotspot-tooltip-before-left: 50%; --afeb-hotspot-tooltip-before-bottom: -5px;' .
                '--afeb-hotspot-tooltip-before-transform-x: -50%; --afeb-hotspot-tooltip-before-transform-y: 0;';
            $pro = ' ' . __('(PRO)', 'addons-for-elementor-builder');
            $items->add_responsive_control(
                'psitn',
                [
                    'label' => __('Position', 'addons-for-elementor-builder'),
                    'type' => Controls_Manager::SELECT,
                    'display' => false,
                    'default' => 'top',
                    'tablet_default' => 'top',
                    'mobile_default' => 'top',
                    'options' => Helper::get_array([
                        'top' => esc_html__('Top', 'addons-for-elementor-builder'),
                        esc_html__('Right', 'addons-for-elementor-builder') . $pro,
                        esc_html__('Bottom', 'addons-for-elementor-builder') . $pro,
                        esc_html__('Left', 'addons-for-elementor-builder') . $pro
                    ], 'psitn'),
                    'selectors_dictionary' => Helper::get_array([
                        'top' => str_replace('x-top', '20%', $css),
                        str_replace('x-right', '20%', $css),
                        str_replace('x-bottom', '', $css),
                        str_replace('x-left', '', $css)
                    ], 'psitn_selectors_dictionary'),
                    'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}} .afeb-hotspot-tooltip-text,
                            {{WRAPPER}} {{CURRENT_ITEM}} .afeb-hotspot-tooltip-text:before' => '{{VALUE}}'],
                    'condition' => ['sh_toltp' => 'yes']
                ]
            );
            $this->CHelper->dvdr($items, 'div_2');
            $this->CHelper->wysiwyg($items, 'toltp_txt', __('Tooltip Text', 'addons-for-elementor-builder'), __('Tooltip Content', 'addons-for-elementor-builder'), __('Type tooltip text here', 'addons-for-elementor-builder'), 'lblk', ['sh_toltp' => 'yes']);
            $this->CHelper->dvdr($items, 'div_3');
            $this->CHelper->hddn($items, 'sh_dfalt_toltp', '', 'yes');
            $this->CHelper->rptr($obj, 'htspt', $items->get_controls(), [
                [
                    'ht_ic' => ['value' => 'fas fa-plus', 'library' => 'fa-solid'],
                    'toltp_txt' => esc_html__('Tooltip Content', 'addons-for-elementor-builder'),
                    'ht_ofst_tgl' => 'yes',
                    'ht_ofst_x' => 300,
                    'ht_ofst_y' => 300,
                ]
            ], 'toltp_txt');
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Image Style
         *
         */
        $hot_img_slctr = '{{WRAPPER}} .afeb-hotspot-wrapper .afeb-hotspot-image';
        $this->CHelper->add_stl_sctn($this, 'ss1', __('Image', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->res_talmnt($obj, 'almnt', '{{WRAPPER}} .elementor-widget-container');

            $range = ['min' => 0, 'max' => 1000];
            $this->CHelper->res_sldr($obj, 'ht_img_hght_size', __('Height', 'addons-for-elementor-builder'), [$opt[0] => 'height: {{SIZE}}{{UNIT}}'], $range, ['px', 'vh']);
            $this->CHelper->res_sldr($obj, 'ht_img_wdt_size', __('Width', 'addons-for-elementor-builder'), [$opt[0] => 'width: {{SIZE}}{{UNIT}}'], $range, ['px', '%', 'vw']);
            $this->CHelper->res_slct($obj, 'ht_obj-fit', esc_html__('Object Fit', 'addons-for-elementor-builder'), [
                '' => esc_html__('Default', 'addons-for-elementor-builder'),
                'fill' => esc_html__('Fill', 'addons-for-elementor-builder'),
                'cover' => esc_html__('Cover', 'addons-for-elementor-builder'),
                'contain' => esc_html__('Contain', 'addons-for-elementor-builder'),
            ], 'cover', [], [$opt[0] . '>img' => 'object-fit: {{VALUE}}']);
            $this->CHelper->res_pad($obj, 'ht_img_pad', $opt[0] . '>img');
            $this->CHelper->brdr($obj, 'ht_img_brdr', $opt[0] . '>img');
            $this->CHelper->res_brdr_rdus($obj, 'ht_img_brdr_rdus', $opt[0] . '>img', CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'bx_shdo_psitn', $opt[0] . '>img');
        }, [$hot_img_slctr]);
        /**
         *
         * Spot Style
         *
         */
        $hot_item_slctr = '{{WRAPPER}} .afeb-hotspot-wrapper .afeb-hotspot-item';
        $hot_item_wrap_slctr = $hot_item_slctr . ' .afeb-hotspot-item-wrap';
        $this->CHelper->add_stl_sctn($this, 'ss2', __('Spot', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->res_sldr($obj, 'spot_font_size', __('Media Size', 'addons-for-elementor-builder'), [
                $opt[1] . '>i' => 'font-size: {{SIZE}}{{UNIT}}',
                $opt[1] . '>img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
                $opt[1] . '>svg' => 'width: {{SIZE}}{{UNIT}};height: auto'
            ]);
            $selector = [$opt[0] => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'spot_width_size', __('Background Size', 'addons-for-elementor-builder'), $selector, ['min' => 0, 'max' => 500], ['px']);

            $obj->start_controls_tabs('itms_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't1', __('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_clr($obj, 'ht_bg_clr', $opt[0]);
                $this->CHelper->clr($obj, 'ht_clr', $opt[1] . '>i,' . $opt[1] . '>svg');
            }, [$opt[0], $opt[1]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't2', __('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_clr($obj, 'ht_bg_clr_hvr', $opt[0] . ':hover');
                $hover_slctr = ':hover .afeb-hotspot-item-wrap>';
                $this->CHelper->clr($obj, 'ht_clr_hvr', $opt[0] . $hover_slctr . 'i,' . $opt[0] . $hover_slctr . 'svg');
                $this->CHelper->cstm_clr($obj, 'ht_brdr_clr_hvr', $opt[0] . ':hover', 'border-color: {{VALUE}}', esc_html__('Border Color', 'addons-for-elementor-builder'));
            }, [$opt[0]]);
            $obj->end_controls_tabs();

            $this->CHelper->brdr($obj, 'ht_brdr', $opt[0]);
            $selector = [$opt[0] . ',{{WRAPPER}} .afeb-hotspot-item-wrap:after' => CHelper::FILL_BR_RADIUS];
            $this->CHelper->res_brdr_rdus($obj, 'ht_rdus', $selector, CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'ht_img_shdo', $opt[0]);
        }, [$hot_item_slctr, $hot_item_wrap_slctr]);
        /**
         *
         * Tooltip Style
         *
         */
        $tooltip_text_slctr = '{{WRAPPER}} .afeb-hotspot-tooltip-text';
        $this->CHelper->add_stl_sctn($this, 'ss3', __('Tooltip', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'toltp_bg_clr', $opt[0] . ', ' . $opt[0] . ':before');
            $this->CHelper->clr($obj, 'toltp_clr', $opt[0] . ', ' . $opt[0] . '>*', esc_html__('Text Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'toltp_typo', $opt[0] . ', ' . $opt[0] . '>*');
            $this->CHelper->res_talmnt($obj, 'toltp_almnt', $opt[0]);
            $range = ['px' => ['min' => 0, 'max' => 1000], '%' => ['min' => 0, 'max' => 100]];
            $this->CHelper->res_sldr($obj, 'toltp_wdt_size', __('Width', 'addons-for-elementor-builder'), [$opt[0] => 'width: {{SIZE}}{{UNIT}}'], $range, CHelper::BDSU);
            $this->CHelper->res_pad($obj, 'toltp_pad', $opt[0], CHelper::BDSU);
            $this->CHelper->res_brdr_rdus($obj, 'toltp_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'toltp_bx_shdo', $opt[0]);
        }, [$tooltip_text_slctr]);
    }

    /**
     * Render Hotspot widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
?>
        <div class="afeb-hotspot-wrapper">
            <figure class="afeb-hotspot-image">
                <?php
                $img = Group_Control_Image_Size::get_attachment_image_html($settings, 'media_thumbnail', 'image');
                if ($settings['image']) echo wp_kses($img, Helper::allowed_tags(['img'], ['img' => ['loading' => []]]));
                ?>
            </figure>
            <?php foreach ($settings['htspt'] as $item) : ?>
                <div class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> afeb-hotspot-item">
                    <section class="afeb-hotspot-item-wrap afeb-hotspot-type-hover">
                        <?php if ('yes' === $item['sh_toltp']) : ?>
                            <?php $active_class = !empty($item['sh_dfalt_toltp']) ? 'afeb-active' : ''; ?>
                            <span class="afeb-hotspot-tooltip-text <?php echo esc_attr($active_class) ?> afeb-hotspot-<?php echo esc_attr($item['psitn']); ?>">
                                <?php echo wp_kses_post($item['toltp_txt']); ?>
                            </span>
                        <?php endif; ?>
                        <?php
                        if ('icon' === $item['ht_mda_typ']) Icons_Manager::render_icon($item['ht_ic'], ['aria-hidden' => 'true']);
                        if ('image' === $item['ht_mda_typ']) echo wp_kses(
                            Group_Control_Image_Size::get_attachment_image_html($item, 'hts_tmbnl', 'ht_img'),
                            Helper::allowed_tags(['img'], ['img' => ['loading' => []]])
                        );
                        ?>
                    </section>
                </div>
            <?php endforeach; ?>
        </div>
<?php
    }
}
