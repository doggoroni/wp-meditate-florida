<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper as Helper;
use AFEB\Controls\Helper as CHelper;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" FancyText Widget Class
 * 
 * @class FancyText
 * @version 1.0.0
 */
class FancyText extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * @var HTMLTag
     */
    public $HTML_TG;

    /**
     * FancyText Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->helper = new Helper();
        $this->controls = new CHelper($this);
        $this->assets->fancy_text_style();
        $this->assets->fancy_text_script();
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
        return 'afeb_fancy_text';
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
        return esc_html__('Fancy Text', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-fancy-text';
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
        return ['fancy_text', 'fancy text', esc_html__('Fancy Text', 'addons-for-elementor-builder')];
    }

    /**
     * Register FancyText widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->helper->add_cnt_sctn($this, 'cs1', esc_html__('Fancy Text', 'addons-for-elementor-builder'), function ($obj) {
            $this->helper->txt($obj, 'ft_prfx_txt', esc_html__('Prefix Text', 'addons-for-elementor-builder'), esc_html__('This is the ', 'addons-for-elementor-builder'));
            $items = new Repeater();
            $this->helper->txt($items, 'txt', esc_html__('Text', 'addons-for-elementor-builder'), esc_html__('First string', 'addons-for-elementor-builder'));
            $this->helper->rptr($obj, 'ft_fnctxt', $items->get_controls(), [['txt' => esc_html__('First string', 'addons-for-elementor-builder')]], 'txt');
            $this->helper->txt($obj, 'ft_sfx_txt', 'Suffix Text', 'of the sentence.');
        });
        $this->helper->add_cnt_sctn($this, 'cs2', esc_html__('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $this->helper->falmnt($obj, 'ft_almnt', '{{WRAPPER}} .afeb-fancy-text');
            $this->helper->slct($obj, 'ft_efct', esc_html__('Effect', 'addons-for-elementor-builder'), [
                'typing' => esc_html__('Typing', 'addons-for-elementor-builder'),
                'rotate-1' => esc_html__('Skew', 'addons-for-elementor-builder'),
                // 'rotate-2' => esc_html__('Flip VR', 'addons-for-elementor-builder'),
                // 'rotate-3' => esc_html__('Flip HR', 'addons-for-elementor-builder'),
                'slide' => esc_html__('Slide', 'addons-for-elementor-builder'),
                'clip' => esc_html__('Clip', 'addons-for-elementor-builder'),
                'zoom' => esc_html__('Zoom', 'addons-for-elementor-builder'),
                // 'scale' => esc_html__('Scale', 'addons-for-elementor-builder'),
                'push' => esc_html__('Push', 'addons-for-elementor-builder')
            ], 'typing');
            $this->helper->slct($obj, 'ft_crsr', esc_html__('Cursor', 'addons-for-elementor-builder'), [
                '_' => esc_html__('Underline', 'addons-for-elementor-builder'),
                '|' => esc_html__('Pipe', 'addons-for-elementor-builder'),
                'custom' => esc_html__('Custom', 'addons-for-elementor-builder')
            ], '_', ['ft_efct' => ['typing', 'clip']]);
            $this->helper->txt($obj, 'ft_cstm_crsr', esc_html__('Custom Cursor', 'addons-for-elementor-builder'), '', '', 'dai', ['ft_efct' => ['typing', 'clip'], 'ft_crsr' => 'custom']);
            $this->helper->num($obj, 'ft_anim_dur', esc_html__('Animation Duration', 'addons-for-elementor-builder'), 0.1, 8, 0.1, 0.2, '', '', ['ft_efct' => ['typing', 'clip']]);
            $this->helper->num($obj, 'ft_anim_dly', esc_html__('Animation Delay', 'addons-for-elementor-builder'), 0.1, 8, 0.1, 2);
            $this->HTML_TG = [
                'h1' => 'H1',
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
                'span' => 'SPAN',
                'div' => 'DIV',
                'p' => 'P'
            ];
            $this->helper->slct($obj, 'ft_html_tg', esc_html__('HTML Tag', 'addons-for-elementor-builder'), $this->HTML_TG, 'h2');
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Prefix Text Styles
         *
         */
        $slctr = '{{WRAPPER}} .afeb-fancy-prefix-text';
        $this->helper->add_stl_sctn($this, 'ss1', esc_html__('Prefix Text', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->helper->clr($obj, 'prfx_clr', $opt[0], esc_html__('Color', 'addons-for-elementor-builder'));
            $this->helper->typo($obj, 'prfx_typo', $opt[0], esc_html__('Typography', 'addons-for-elementor-builder'));
        }, [$slctr]);
        /**
         *
         * Fluid Text Styles
         *
         */
        $slctr = '{{WRAPPER}} .afeb-fancy-anim-text';
        $this->helper->add_stl_sctn($this, 'ss2', esc_html__('Fluid Text', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->helper->bg_grp_ctrl($obj, 'txt_bg', $opt[0]);
            $this->helper->clr($obj, 'txt_clr', $opt[0], esc_html__('Text Color', 'addons-for-elementor-builder'));
            $this->helper->typo($obj, 'txt_typo', $opt[0] . ' *', esc_html__('Typography', 'addons-for-elementor-builder'));
            $this->helper->res_pad($obj, 'txt_pad', $opt[0]);
            $this->controls->border([
                'name' => 'txt_brdr',
                'selector' => $opt[0],
            ]);
            $this->controls->responsive()->border_radius('txt_brdr_rdus', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    $opt[0] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        }, [$slctr]);
        /**
         *
         * Suffix Text Styles
         *
         */
        $slctr = '{{WRAPPER}} .afeb-fancy-suffix-text';
        $this->helper->add_stl_sctn($this, 'ss3', esc_html__('Suffix Text', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->helper->clr($obj, 'sfx_clr', $opt[0], esc_html__('Color', 'addons-for-elementor-builder'));
            $this->helper->typo($obj, 'sfx_typo', $opt[0], esc_html__('Typography', 'addons-for-elementor-builder'));
        }, [$slctr]);
    }

    /**
     * Render attributes
     *
     * @since 1.0.4
     * 
     * @param array $settings
     */
    protected function render_attrs($settings = [])
    {
        $classes = [];
        $classes[] = 'afeb-fancy-text';
        $classes[] = in_array($settings['ft_efct'], ['typing', 'rotate-2', 'rotate-3', 'scale']) ?
            'afeb-anim-text-letters' : '';

        $classes = array_filter($classes);
        $classes = array_map('sanitize_html_class', $classes);
        $this->add_render_attribute(
            [
                'fancy_text' => [
                    'class' => implode(' ', $classes),
                    'data-settings' => [
                        wp_json_encode([
                            'dur' => isset($settings['ft_anim_dur']) ? floatval($settings['ft_anim_dur']) : 0.2,
                            'dly' => isset($settings['ft_anim_dly']) ? floatval($settings['ft_anim_dly']) : 2
                        ])
                    ]
                ]
            ]
        );

    }

    /**
     * Render FancyText widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $animated_text = !empty($settings['ft_fnctxt']) ? $settings['ft_fnctxt'] : [];
        $animated_text = array_map(function ($value) {
            if (!empty($value['txt'])) return $value['txt'];
        }, $animated_text);
        $html_tags = is_array($this->HTML_TG) ? array_keys($this->HTML_TG) : [];
        $html_tag = (!empty($settings['ft_html_tg']) &&
            in_array($settings['ft_html_tg'], $html_tags)) ?
            $settings['ft_html_tg'] : 'h3';
        $type = !empty($settings['ft_efct']) ? $settings['ft_efct'] : 'typing';
        $this->render_attrs($settings);
        $escaped_html_tag = tag_escape($html_tag);
        ?>
        <<?php echo $escaped_html_tag; ?> <?php $this->print_render_attribute_string('fancy_text'); ?>>

            <?php if (!empty($settings['ft_prfx_txt'])): ?>
                <span class="afeb-fancy-prefix-text">
                    <?php echo wp_kses_post($settings['ft_prfx_txt']); ?>
                </span>
            <?php endif; ?>

            <span class="afeb-fancy-anim-text afeb-anim-text-type-<?php echo esc_attr($type); ?>">
                <span class="afeb-fancy-anim-text-inner">
                    <?php foreach ($animated_text as $value): ?>
                        <b><?php echo esc_html($value); ?></b>
                    <?php endforeach; ?>
                </span>
                <?php
                $cursor = $settings['ft_crsr'] != 'custom' ?
                    $settings['ft_crsr'] : $settings['ft_cstm_crsr'];
                if (!empty($cursor)):
                ?>
                    <span class="afeb-fancy-anim-text-cursor">
                        <?php echo esc_html($cursor); ?>
                    </span>
                <?php endif; ?>
            </span>

            <?php if ($settings['ft_sfx_txt'] !== ''): ?>
                <span class="afeb-fancy-suffix-text">
                    <?php echo wp_kses_post($settings['ft_sfx_txt']); ?>
                </span>
            <?php endif; ?>

        </<?php echo $escaped_html_tag; ?>>
<?php }
}
