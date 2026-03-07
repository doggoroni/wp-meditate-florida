<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostNavigation Widget Class
 * 
 * @class PostNavigation
 * @version 1.5.0
 */
class PostNavigation extends Widget_Base
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
     * PostNavigation Constructor
     * 
     * @since 1.5.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->post_navigation_style();
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
        return 'afeb_post_navigation';
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
        return esc_html('Post Navigation', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-post-navigation';
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
        return ['theme-elements-single'];
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
        return ['post', 'navigation', 'menu', 'links'];
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
        return ['afeb-post-navigation-style'];
    }

    /**
     * Register PostNavigation widget controls
     *
     * @since 1.5.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('post_navigation_content_section', [
            'label' => esc_html__('Post Navigation', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->sh_switcher('show_label', [
                'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->text('prev_label', [
                'label' => esc_html__('Previous Label', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'default' => esc_html__('Previous', 'addons-for-elementor-builder'),
                'condition' => ['show_label' => 'yes',],
            ]);

            $this->controls->text('next_label', [
                'label' => esc_html__('Next Label', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'default' => esc_html__('Next', 'addons-for-elementor-builder'),
                'condition' => ['show_label' => 'yes',],
            ]);

            $this->controls->sh_switcher('show_arrow', [
                'label' => esc_html__('Arrows', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->select('arrow', [
                'label' => esc_html__('Arrows Type', 'addons-for-elementor-builder'),
                'options' => [
                    'fa fa-angle-left' => esc_html__('Angle', 'addons-for-elementor-builder'),
                    'fa fa-angle-double-left' => esc_html__('Double Angle', 'addons-for-elementor-builder'),
                    'fa fa-chevron-left' => esc_html__('Chevron', 'addons-for-elementor-builder'),
                    'fa fa-chevron-circle-left' => esc_html__('Chevron Circle', 'addons-for-elementor-builder'),
                    'fa fa-caret-left' => esc_html__('Caret', 'addons-for-elementor-builder'),
                    'fa fa-arrow-left' => esc_html__('Arrow', 'addons-for-elementor-builder'),
                    'fa fa-arrow-circle-left' => esc_html__('Arrow Circle', 'addons-for-elementor-builder'),
                ],
                'default' => 'fa fa-angle-left',
                'condition' => [
                    'show_arrow' => 'yes',
                ],
            ]);

            $this->controls->sh_switcher('show_title', [
                'label' => esc_html__('Post Title', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->sh_switcher('show_borders', [
                'label' => esc_html__('Borders', 'addons-for-elementor-builder'),
                'default' => 'yes',
                'prefix_class' => 'afeb-post-navigation-borders-',
            ]);

            $this->controls->hidden('in_same_term', [
                'default' => '',
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Label
         *
         */
        $this->controls->tab_style_section('label_style_section', [
            'label' => esc_html__('Label', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->tabs('label_style_tab', [
                'label_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('label_color', [
                            'global' => ['default' => Global_Colors::COLOR_TEXT,],
                            'selectors' => [
                                '{{WRAPPER}} span.afeb-post-navigation-prev-label' => 'color: {{VALUE}}',
                                '{{WRAPPER}} span.afeb-post-navigation-next-label' => 'color: {{VALUE}}',
                            ],
                        ]);
                    },
                ],
                'label_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('label_color_hover', [
                            'selectors' => [
                                '{{WRAPPER}} span.afeb-post-navigation-prev-label:hover' => 'color: {{VALUE}}',
                                '{{WRAPPER}} span.afeb-post-navigation-next-label:hover' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->slider('label_transition_duration', [
                            'label' => esc_html__('Transition Duration', 'addons-for-elementor-builder'),
                            'size_units' => ['s', 'ms', 'custom'],
                            'default' => ['unit' => 'ms',],
                            'selectors' => [
                                '{{WRAPPER}} span.afeb-post-navigation-prev-label' => 'transition-duration: {{SIZE}}{{UNIT}}',
                                '{{WRAPPER}} span.afeb-post-navigation-next-label' => 'transition-duration: {{SIZE}}{{UNIT}}',
                            ],
                            'separator' => 'before',
                        ]);
                    },
                ],
            ]);

            $this->controls->typography([
                'name' => 'label_typography',
                'global' => ['default' => Global_Typography::TYPOGRAPHY_SECONDARY,],
                'selector' => '{{WRAPPER}} span.afeb-post-navigation-prev-label, {{WRAPPER}} span.post-navigation-next-label',
                'exclude' => ['line_height'],
            ]);
        });
        /**
         *
         * Title
         *
         */
        $this->controls->tab_style_section('title_style_section', [
            'label' => esc_html__('Title', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->tabs('title_style_tab', [
                'title_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('title_color', [
                            'global' => ['default' => Global_Colors::COLOR_SECONDARY,],
                            'selectors' => ['{{WRAPPER}} span.afeb-post-navigation-prev-title, {{WRAPPER}} span.afeb-post-navigation-next-title' => 'color: {{VALUE}}',],
                        ]);
                    },
                ],
                'title_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('title_color_hover', [
                            'selectors' => ['{{WRAPPER}} span.afeb-post-navigation-prev-title:hover, {{WRAPPER}} span.afeb-post-navigation-next-title:hover' => 'color: {{VALUE}}',],
                        ]);

                        $this->controls->slider('title_transition_duration', [
                            'label' => esc_html__('Transition Duration', 'addons-for-elementor-builder'),
                            'size_units' => ['s', 'ms', 'custom'],
                            'default' => ['unit' => 'ms',],
                            'selectors' => [
                                '{{WRAPPER}} span.afeb-post-navigation-prev-title' => 'transition-duration: {{SIZE}}{{UNIT}}',
                                '{{WRAPPER}} span.afeb-post-navigation-next-title' => 'transition-duration: {{SIZE}}{{UNIT}}',
                            ],
                            'separator' => 'before',
                        ]);
                    },
                ],
            ]);

            $this->controls->typography([
                'name' => 'title_typography',
                'global' => ['default' => Global_Typography::TYPOGRAPHY_SECONDARY,],
                'selector' => '{{WRAPPER}} span.afeb-post-navigation-prev-title, {{WRAPPER}} span.afeb-post-navigation-next-title',
                'exclude' => ['line_height'],
            ]);
        });
        /**
         *
         * Arrow
         *
         */
        $this->controls->tab_style_section('arrow_style_section', [
            'label' => esc_html__('Arrow', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->tabs('arrow_style_tab', [
                'arrow_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('arrow_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-post-navigation-arrow-wrapper' => 'color: {{VALUE}}',],
                        ]);
                    },
                ],
                'arrow_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('arrow_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-post-navigation-arrow-wrapper:hover' => 'color: {{VALUE}}',],
                        ]);

                        $this->controls->slider('arrow_transition_duration', [
                            'label' => esc_html__('Transition Duration', 'addons-for-elementor-builder'),
                            'size_units' => ['s', 'ms', 'custom'],
                            'default' => ['unit' => 'ms',],
                            'selectors' => ['{{WRAPPER}} .afeb-post-navigation-arrow-wrapper' => 'transition-duration: {{SIZE}}{{UNIT}}',],
                            'separator' => 'before',
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->slider('arrow_size', [
                'label' => esc_html__('Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['min' => 1, 'max' => 300,],
                    'em' => ['max' => 30,],
                    'rem' => ['max' => 30,],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-post-navigation-arrow-wrapper' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]);

            $this->controls->responsive()->slider('arrow_padding', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['max' => 50,],
                    'em' => ['max' => 5,],
                    'rem' => ['max' => 5,],
                ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} .afeb-post-navigation-arrow-prev' => 'padding-right: {{SIZE}}{{UNIT}}',
                    'body:not(.rtl) {{WRAPPER}} .afeb-post-navigation-arrow-next' => 'padding-left: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}} .afeb-post-navigation-arrow-prev' => 'padding-left: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}} .afeb-post-navigation-arrow-next' => 'padding-right: {{SIZE}}{{UNIT}}',
                ],
            ]);
        });
        /**
         *
         * Border
         *
         */
        $this->controls->tab_style_section('border_style_section', [
            'label' => esc_html__('Border', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->text_color('sep_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-post-navigation-separator' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .afeb-post-navigation' => 'color: {{VALUE}}',
                ],
            ]);

            $this->controls->responsive()->slider('borders_width', [
                'label' => esc_html__('Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['min' => 1, 'max' => 100,],
                    'em' => ['max' => 10,],
                    'rem' => ['max' => 10,],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-post-navigation-separator' => 'width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .afeb-post-navigation' => 'border-top-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .afeb-post-navigation-next.afeb-post-navigation-link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
                    '{{WRAPPER}} .afeb-post-navigation-prev.afeb-post-navigation-link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
                ],
            ]);

            $this->controls->responsive()->slider('borders_spacing', [
                'label' => esc_html__('Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['max' => 100,],
                    'em' => ['max' => 10,],
                    'rem' => ['max' => 10,],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-post-navigation' => 'padding: {{SIZE}}{{UNIT}} 0;',
                ],
            ]);
        });
    }

    /**
     * Render PostNavigation widget output on the frontend
     *
     * @since 1.5.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $prev_label = '';
        $next_label = '';
        $prev_arrow = '';
        $next_arrow = '';

        if ($settings['show_label'] == 'yes') {
            $prev_label = '<span class="afeb-post-navigation-prev-label">' . $settings['prev_label'] . '</span>';
            $next_label = '<span class="afeb-post-navigation-next-label">' . $settings['next_label'] . '</span>';
        }

        if ($settings['show_arrow'] == 'yes') {
            if (is_rtl()) {
                $prev_icon_class = str_replace('left', 'right', $settings['arrow']);
                $next_icon_class = $settings['arrow'];
            } else {
                $prev_icon_class = $settings['arrow'];
                $next_icon_class = str_replace('left', 'right', $settings['arrow']);
            }

            $prev_arrow = '<span class="afeb-post-navigation-arrow-wrapper afeb-post-navigation-arrow-prev"><i class="' .
                esc_attr($prev_icon_class) . '" aria-hidden="true"></i></span>';

            $next_arrow = '<span class="afeb-post-navigation-arrow-wrapper afeb-post-navigation-arrow-next"><i class="' .
                esc_attr($next_icon_class) . '" aria-hidden="true"></i></span>';
        }

        $prev_title = '';
        $next_title = '';

        if ($settings['show_title'] == 'yes') {
            $prev_title = '<span class="afeb-post-navigation-prev-title">%title</span>';
            $next_title = '<span class="afeb-post-navigation-next-title">%title</span>';
        }

        $in_same_term = false;
        $taxonomy = 'category';
        $post_type = get_post_type(get_queried_object_id());

        if (
            !empty($settings['in_same_term']) &&
            is_array($settings['in_same_term']) &&
            in_array($post_type, $settings['in_same_term'])
        ) {
            if (isset($settings[$post_type . '_taxonomy'])) {
                $in_same_term = true;
                $taxonomy = $settings[$post_type . '_taxonomy'];
            }
        }
?>
        <div class="afeb-post-navigation">
            <div class="afeb-post-navigation-prev afeb-post-navigation-link">
                <?php previous_post_link('%link', $prev_arrow . '<span class="afeb-post-navigation-link-prev">' . $prev_label . $prev_title . '</span>', $in_same_term, '', $taxonomy); ?>
            </div>
            <?php if ($settings['show_borders'] == 'yes'): ?>
                <div class="afeb-post-navigation-separator-wrapper">
                    <div class="afeb-post-navigation-separator"></div>
                </div>
            <?php endif; ?>
            <div class="afeb-post-navigation-next afeb-post-navigation-link">
                <?php next_post_link('%link', '<span class="afeb-post-navigation-link-next">' . $next_label . $next_title . '</span>' . $next_arrow, $in_same_term, '', $taxonomy); ?>
            </div>
        </div>
<?php
    }
}
