<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" CreativeButton Widget Class
 * 
 * @class CreativeButton
 * @version 1.5.0
 */
class CreativeButton extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * CreativeButton Constructor
     * 
     * @since 1.5.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->creative_button_style();
    }

    /**
     * Get widget name
     *
     * @since 1.5.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_creative_button';
    }

    /**
     * Get widget title
     *
     * @since 1.5.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html('Creative Button', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.5.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-creative-button';
    }

    /**
     * Get widget categories
     *
     * @since 1.5.0
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
     * @since 1.5.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return [];
    }

    /**
     * Retrieve the list of style dependencies the widget requires
     *
     * @since 1.5.0
     *
     * @return array Widget style dependencies
     */
    public function get_style_depends(): array
    {
        return ['afeb-creative-button-style'];
    }

    /**
     * Register CreativeButton widget controls
     *
     * @since 1.5.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('creative_button_content_section', [
            'label' => esc_html__('Creative Button', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->select('button_style', [
                'label' => esc_html__('Button Style', 'addons-for-elementor-builder'),
                'options' => [
                    '1' => esc_html__('Style1', 'addons-for-elementor-builder'),
                    '2' => esc_html__('Style2', 'addons-for-elementor-builder'),
                    '3' => esc_html__('Style3', 'addons-for-elementor-builder'),
                    '4' => esc_html__('Style4', 'addons-for-elementor-builder'),
                    '5' => esc_html__('Style5', 'addons-for-elementor-builder'),
                    '6' => esc_html__('Style6', 'addons-for-elementor-builder'),
                    '7' => esc_html__('Style7', 'addons-for-elementor-builder'),
                    '8' => esc_html__('Style8', 'addons-for-elementor-builder'),
                ],
                'default' => '1',
            ]);

            $this->controls->text('button_text', [
                'label' => esc_html__('Text', 'addons-for-elementor-builder'),
                'label_block' => true,
                'default' => esc_html__('Click Here', 'addons-for-elementor-builder'),
            ]);

            $this->controls->url('button_link', []);

            $icon_prefix = Icons_Manager::is_migration_allowed() ? 'far ' : 'fa ';
            $this->controls->icons('button_icon', [
                'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => $icon_prefix . 'fa-arrow-alt-circle-right',
                    'library' => 'fa-solid',
                ],
            ]);

            $start = is_rtl() ? 'right' : 'left';
            $end = is_rtl() ? 'left' : 'right';

            $this->controls->choose('button_icon_position', [
                'label' => esc_html__('Icon Position', 'addons-for-elementor-builder'),
                'options' => [
                    'row' => [
                        'title' => esc_html__('Start', 'elementor'),
                        'icon' => "eicon-h-align-{$start}",
                    ],
                    'row-reverse' => [
                        'title' => esc_html__('End', 'elementor'),
                        'icon' => "eicon-h-align-{$end}",
                    ],
                ],
                'default' => is_rtl() ? 'row-reverse' : 'row',
                'selectors_dictionary' => [
                    'left' => is_rtl() ? 'row-reverse' : 'row',
                    'right' => is_rtl() ? 'row' : 'row-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-creative-button-content-wrapper' => 'flex-direction: {{VALUE}}',
                ],
                'condition' => ['button_icon[value]!' => '']
            ]);

            $this->controls->slider('button_icon_spacing', [
                'label' => esc_html__('Icon Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => ['px' => ['max' => 50,], 'em' => ['max' => 5,], 'rem' => ['max' => 5,],],
                'selectors' => ['{{WRAPPER}} .afeb-creative-button .afeb-creative-button-content-wrapper' => 'gap: {{SIZE}}{{UNIT}};',],
                'condition' => ['button_text!' => '', 'button_icon[value]!' => '',],
            ]);

            $this->controls->text('button_css_id', [
                'label' => esc_html__('Button ID', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'default' => '',
                'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'addons-for-elementor-builder'),
                'description' => sprintf(
                    esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows %1$sA-z 0-9%2$s & underscore chars without spaces.', 'addons-for-elementor-builder'),
                    '<code>',
                    '</code>'
                ),
                'separator' => 'before',
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Creative Button
         *
         */
        $this->controls->tab_style_section('creative_button_style_section', [
            'label' => esc_html__('Creative Button', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->choose('button_position', [
                'label' => esc_html__('Position', 'addons-for-elementor-builder'),
                'options' => [
                    'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-left',],
                    'center' => ['title' => esc_html__('Center', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-center',],
                    'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-right',],
                    'justify' => ['title' => esc_html__('Stretch', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-stretch',],
                ],
                'selectors' => ['{{WRAPPER}} .afeb-creative-button' => '{{VALUE}}',],
                'selectors_dictionary' => [
                    'left' => 'margin-right: auto',
                    'center' => 'margin: 0 auto',
                    'right' => 'margin-left: auto',
                    'justify' => 'width: 100%'
                ],
            ]);

            $this->controls->typography([
                'name' => 'button_typography',
                'global' => ['default' => Global_Typography::TYPOGRAPHY_ACCENT,],
                'selector' => '{{WRAPPER}} .afeb-creative-button',
            ]);

            $this->controls->tabs('creative_button_style_tab', [
                'creative_button_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'button_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-creative-button-style1,' .
                                '{{WRAPPER}} .afeb-creative-button-style2,' .
                                '{{WRAPPER}} .afeb-creative-button-style3,' .
                                '{{WRAPPER}} .afeb-creative-button-style4,' .
                                '{{WRAPPER}} .afeb-creative-button-style5,' .
                                '{{WRAPPER}} .afeb-creative-button-style6,' .
                                '{{WRAPPER}} .afeb-creative-button-style7,' .
                                '{{WRAPPER}} .afeb-creative-button-style8'
                        ]);

                        $this->controls->text_color('button_color', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-creative-button, {{WRAPPER}} .afeb-creative-button .afeb-creative-button-text' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->responsive()->padding('button_padding', [
                            'size_units' => ['px', 'em', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-creative-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'button_border',
                            'selector' => '{{WRAPPER}} .afeb-creative-button',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('button_radius', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-creative-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'button_shadow',
                            'selector' => '{{WRAPPER}} .afeb-creative-button',
                        ]);

                        $this->controls->text_shadow([
                            'name' => 'button_text_shadow',
                            'selector' => '{{WRAPPER}} .afeb-creative-button',
                        ]);
                    },
                ],
                'creative_button_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'button_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-creative-button-style1::before, {{WRAPPER}} .afeb-creative-button-style1::after,' .
                                '{{WRAPPER}} .afeb-creative-button-style2::after,' .
                                '{{WRAPPER}} .afeb-creative-button-style3::before,' .
                                '{{WRAPPER}} .afeb-creative-button-style4::before, {{WRAPPER}} .afeb-creative-button-style4::after,' .
                                '{{WRAPPER}} .afeb-creative-button-style5::before,' .
                                '{{WRAPPER}} .afeb-creative-button-style6::before,' .
                                '{{WRAPPER}} .afeb-creative-button-style7::before,' .
                                '{{WRAPPER}} .afeb-creative-button-style8::before'
                        ]);

                        $this->controls->text_color('button_color_hover', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-creative-button:hover, {{WRAPPER}} .afeb-creative-button:hover .afeb-creative-button-text' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->responsive()->padding('button_padding_hover', [
                            'size_units' => ['px', 'em', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-creative-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'button_border_hover',
                            'selector' => '{{WRAPPER}} .afeb-creative-button:hover',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('button_radius_hover', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-creative-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'button_shadow_hover',
                            'selector' => '{{WRAPPER}} .afeb-creative-button:hover',
                        ]);

                        $this->controls->text_shadow([
                            'name' => 'button_text_shadow_hover',
                            'selector' => '{{WRAPPER}} .afeb-creative-button:hover',
                        ]);

                        $this->controls->duration('button_hover_transition', [
                            'label' => esc_html__('Transition Duration (s)', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-creative-button-style1::before, {{WRAPPER}} .afeb-creative-button-style1::after,' .
                                    '{{WRAPPER}} .afeb-creative-button-style2::after,' .
                                    '{{WRAPPER}} .afeb-creative-button-style3::before,' .
                                    '{{WRAPPER}} .afeb-creative-button-style4::before, {{WRAPPER}} .afeb-creative-button-style4::after,' .
                                    '{{WRAPPER}} .afeb-creative-button-style5::before,' .
                                    '{{WRAPPER}} .afeb-creative-button-style6::before,' .
                                    '{{WRAPPER}} .afeb-creative-button-style7::before,' .
                                    '{{WRAPPER}} .afeb-creative-button-style8::before' => 'transition-duration: {{SIZE}}s',
                            ],
                        ]);

                        $this->controls->hover_animation('button_hover_animation');
                    },
                ],
            ]);
        });
    }

    /**
     * Render CreativeButton widget output on the frontend
     *
     * @since 1.5.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['button_text']) && empty($settings['button_icon']['value'])) {
            return;
        }

        $optimized_markup = Plugin::instance()->experiments->is_feature_active('e_optimized_markup');

        $this->add_render_attribute('wrapper', 'class', 'afeb-creative-button-wrapper');
        $this->add_render_attribute('button', 'class', 'afeb-creative-button');
        $this->add_render_attribute('button', 'class', ' afeb-creative-button-style' . $settings['button_style']);

        if (!empty($settings['button_link']['url'])) {
            $this->add_link_attributes('button', $settings['button_link']);
            $this->add_render_attribute('button', 'class', 'afeb-creative-button-link');
        } else {
            $this->add_render_attribute('button', 'role', 'button');
        }

        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', $settings['button_css_id']);
        }

        if (!empty($settings['button_hover_animation'])) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['button_hover_animation']);
        }
?>
        <?php if (!$optimized_markup): ?>
            <div <?php $this->print_render_attribute_string('wrapper'); ?>>
            <?php endif; ?>
            <a <?php $this->print_render_attribute_string('button'); ?>>
                <?php $this->render_text($settings); ?>
            </a>
            <?php if (!$optimized_markup): ?>
            </div>
        <?php endif; ?>
    <?php
    }

    /**
     * Render button text
     * 
     * @param array $settings
     *
     * @since 1.5.0
     */
    protected function render_text($settings = [])
    {
        $migrated = isset($settings['__fa4_migrated']['button_icon']);
        $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

        $this->add_render_attribute([
            'content-wrapper' => ['class' => 'afeb-creative-button-content-wrapper',],
            'icon' => ['class' => 'afeb-creative-button-icon',],
            'text' => ['class' => 'afeb-creative-button-text',],
        ]);
    ?>
        <span <?php $this->print_render_attribute_string('content-wrapper'); ?>>
            <?php if (!empty($settings['icon']) || !empty($settings['button_icon']['value'])): ?>
                <span <?php $this->print_render_attribute_string('icon'); ?>>
                    <?php if ($is_new || $migrated):
                        Icons_Manager::render_icon($settings['button_icon'], ['aria-hidden' => 'true']);
                    else : ?>
                        <i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($settings['button_text'])): ?>
                <span <?php $this->print_render_attribute_string('text'); ?>>
                    <?php $this->print_unescaped_setting('button_text'); ?>
                </span>
            <?php endif; ?>
        </span>
<?php
    }
}
