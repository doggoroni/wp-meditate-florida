<?php

namespace AFEB\Widgets\Timeline;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 *"Vertex Addons for Elementor" Timeline Widget Class
 * 
 * @class Timeline
 * @version 1.0.0
 */
class Timeline extends Widget_Base
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
     * Timeline Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->timeline_style();
    }

    /**
     * Retrieve Timeline widget name
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_timeline';
    }

    /**
     * Retrieve Timeline widget title
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Timeline', 'addons-for-elementor-builder');
    }

    /**
     * Retrieve Timeline widget icon
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-timeline';
    }

    /**
     * Retrieve Timeline widget categories
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
     * Retrieve the list of keywords the widget belongs to
     *
     * @since 1.0.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['timeline', 'time_line', esc_html__('Timeline', 'addons-for-elementor-builder')];
    }

    /**
     * Adds different input fields to allow the user to change and customize the widget settings
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $time_line_slctr = '{{WRAPPER}} .afeb-timeline {{CURRENT_ITEM}} ';
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Timeline', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $items = new Repeater();
            $this->CHelper->txt($items, 'tl_date', __('Date', 'addons-for-elementor-builder'), __('15 Dec', 'addons-for-elementor-builder'), '', 'lblk', ['tl_sh_date' => 'yes']);
            $this->CHelper->icn($items, 'tl_ic', 'fa fa-bookmark', 'fa-solid', '', 1);
            $this->CHelper->txt_area($items, 'tl_ttl', esc_html__('Title', 'addons-for-elementor-builder'), esc_html__('Lorem Ipsum', 'addons-for-elementor-builder'));
            $this->CHelper->wysiwyg($items, 'tl_txt', __('Text', 'addons-for-elementor-builder'), CHelper::$LIM, __('Write description text here', 'addons-for-elementor-builder'));
            $this->CHelper->sh_swtchr($items, 'tl_sh_date', __('Show Date', 'addons-for-elementor-builder'), 1);
            $alignment = [
                'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon'  => 'eicon-text-align-left'],
                'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon'  => 'eicon-text-align-right']
            ];
            $alignment = !is_rtl() ? $alignment : array_reverse($alignment);
            $this->CHelper->chse($items, 'tl_bx_almnt', __('Box Alignment', 'addons-for-elementor-builder'), $alignment, [], 1, is_rtl() ? 'right' : 'left');
            $this->CHelper->clr($items, 'itm_tl_date_clr', $opt[0] . '.afeb-timeline-date', __('Date Color', 'addons-for-elementor-builder'));
            $this->CHelper->bg_clr($items, 'itm_tl_ic_bg_clr', $opt[0] . '.afeb-timeline-icon', __('Icon Background Color', 'addons-for-elementor-builder'));
            $this->CHelper->clr($items, 'itm_tl_ic_clr', $opt[0] . '.afeb-timeline-icon', __('Icon Color', 'addons-for-elementor-builder'));
            $this->CHelper->clr($items, 'itm_tl_ttl_clr', $opt[0] . '.afeb-timeline-content h2', __('Title Color', 'addons-for-elementor-builder'));
            $this->CHelper->bg_clr($items, 'itm_tl_txt_bg_clr', $opt[0] . '.afeb-timeline-content', __('Text Background Color', 'addons-for-elementor-builder'));
            $this->CHelper->clr($items, 'itm_tl_txt_clr', $opt[0] . '.afeb-timeline-content p', esc_html__('Text Color', 'addons-for-elementor-builder'));
            $this->CHelper->bg_clr($items, 'itm_tl_itm_bg_clr', $opt[0], __('Item Background Color', 'addons-for-elementor-builder'));
            $this->CHelper->rptr($obj, 'tllst', $items->get_controls(), [
                [
                    'tl_ic' => ['value' => 'fas fa-podcast', 'library' => 'solid'],
                    'tl_ttl' => __('Intelligence Organization', 'addons-for-elementor-builder'),
                    'tl_txt' => CHelper::$LIM
                ],
                [
                    'tl_ic' => ['value' => 'fas fa-dollar-sign', 'library' => 'solid'],
                    'tl_ttl' => __('Basic Economics', 'addons-for-elementor-builder'),
                    'tl_txt' => CHelper::$LIM,
                    'tl_bx_almnt' => 'right'
                ],
                [
                    'tl_ic' => ['value' => 'fas fa-users', 'library' => 'solid'],
                    'tl_ttl' => __('World Tourism', 'addons-for-elementor-builder'),
                    'tl_txt' => CHelper::$LIM
                ]
            ], 'tl_ttl');
        }, [$time_line_slctr]);
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Line Styles
         *
         */
        $this->CHelper->add_stl_sctn($this, 'ss1', __('Line', 'addons-for-elementor-builder'), function ($obj) {
            $line_slctr = '{{WRAPPER}} .afeb-timeline::after, {{WRAPPER}} .afeb-timeline-container::before';
            $this->CHelper->bg_clr($obj, 'tl_line_bg_clr', $line_slctr);
        });
        /**
         *
         * Marker Styles
         *
         */
        $tl_container_slctr = '{{WRAPPER}} .afeb-timeline .afeb-timeline-container::after';
        $this->CHelper->add_stl_sctn($this, 'ss2', __('Marker', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'tl_mrkr_bg_clr', $opt[0]);
            $this->CHelper->brdr($obj, 'tl_mrkr_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tl_mrkr_rdus', $opt[0], CHelper::BDSU);
        }, [$tl_container_slctr]);
        /**
         *
         * Date Styles
         *
         */
        $date_slctr = '{{WRAPPER}} .afeb-timeline .afeb-timeline-date';
        $this->CHelper->add_stl_sctn($this, 'ss3', __('Date', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'tl_date_bg_clr', $opt[0]);
            $this->CHelper->clr($obj, 'tl_date_clr', $opt[0]);
            $this->CHelper->typo($obj, 'tl_date_typo', $opt[0]);
            $this->CHelper->mar($obj, 'tl_date_mar', $opt[0]);
            $this->CHelper->pad($obj, 'tl_date_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tl_date_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tl_date_rdus', $opt[0], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'tl_date_bx_shdo', $opt[0]);
        }, [$date_slctr]);
        /**
         *
         * Items Styles
         *
         */
        $content_slctr = '{{WRAPPER}} .afeb-timeline .afeb-timeline-content';
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Items', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'tl_itms_bg_clr', $opt[0]);
            $this->CHelper->mar($obj, 'tl_itms_mar', $opt[0]);
            $this->CHelper->pad($obj, 'tl_itms_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tl_itms_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tl_itms_rdus', $opt[0], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'tl_itms_bx_shdo', $opt[0]);
        }, [$content_slctr]);
        /**
         *
         * Icon Styles
         *
         */
        $icon_slctr = '{{WRAPPER}} .afeb-timeline .afeb-timeline-icon';
        $this->CHelper->add_stl_sctn($this, 'ss5', esc_html__('Icon', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'tl_ic_bg_clr', $opt[0]);
            $this->CHelper->clr($obj, 'tl_ic_clr', $opt[0]);
            $this->CHelper->mar($obj, 'tl_ic_mrg', $opt[0]);
            $this->CHelper->pad($obj, 'tl_ic_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tl_ic_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tl_ic_rdus', $opt[0], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'tl_ic_bx_shdo', $opt[0]);
        }, [$icon_slctr]);
        /**
         *
         * Title Styles
         *
         */
        $title_slctr = '{{WRAPPER}} .afeb-timeline .afeb-timeline-content h2';
        $this->CHelper->add_stl_sctn($this, 'ss6', esc_html__('Title', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'tl_ttl_bg_clr', $opt[0]);
            $this->CHelper->clr($obj, 'tl_ttl_clr', $opt[0]);
            $this->CHelper->typo($obj, 'tl_ttl_typo', $opt[0]);
            $this->CHelper->mar($obj, 'tl_ttl_mar', $opt[0]);
            $this->CHelper->pad($obj, 'tl_ttl_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tl_ttl_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tl_ttl_rdus', $opt[0], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'tl_ttl_bx_shdo', $opt[0]);
        }, [$title_slctr]);
        /**
         *
         * Text Styles
         *
         */
        $text_slctr = '{{WRAPPER}} .afeb-timeline .afeb-timeline-content p';
        $this->CHelper->add_stl_sctn($this, 'ss7', __('Text', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'tl_txt_bg_clr', $opt[0]);
            $this->CHelper->clr($obj, 'tl_txt_clr', $opt[0]);
            $this->CHelper->typo($obj, 'tl_txt_typo', $opt[0]);
            $this->CHelper->mar($obj, 'tl_txt_mar', $opt[0]);
            $this->CHelper->pad($obj, 'timeline_text_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'tl_txt_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tl_txt_rdus', $opt[0], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'tl_txt_bx_shdo', $opt[0]);
        }, [$text_slctr]);
    }

    /**
     * Render Timeline widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $items = $settings['tllst'];
        if ($items) :
?>
            <div class="afeb-timeline">
                <?php foreach ($items as $item) : ?>
                    <div class="afeb-timeline-container elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> afeb-<?php echo esc_attr($item['tl_bx_almnt']); ?>">
                        <?php if ($item['tl_sh_date'] === 'yes') : ?>
                            <div class="afeb-timeline-date"><?php echo esc_html($item['tl_date']); ?></div>
                        <?php endif ?>
                        <i class="afeb-timeline-icon <?php echo esc_attr($item['tl_ic']['value']); ?>"></i>
                        <div class="afeb-timeline-content">
                            <?php if (trim($item['tl_ttl'])) : ?>
                                <h2><?php echo esc_html($item['tl_ttl']); ?></h2>
                            <?php endif ?>
                            <p><?php echo wp_kses_post($item['tl_txt']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
<?php
        endif;
    }
}
