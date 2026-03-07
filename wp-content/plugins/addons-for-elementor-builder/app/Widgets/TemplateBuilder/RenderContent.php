<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Controls\Helper;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" RenderContent Widget Class
 * 
 * @class RenderContent
 * @version 1.4.0
 */
class RenderContent extends Widget_Base
{
    /**
     * @var CHelper
     */
    private $controls;

    /**
     * RenderContent Constructor
     * 
     * @since 1.4.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->controls = new Helper($this);
    }

    /**
     * Get widget name
     *
     * @since 1.4.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_text_render';
    }

    /**
     * Get widget categories
     *
     * @since 1.4.0
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return [];
    }

    /**
     * Get widget keywords
     *
     * @since 1.4.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return [];
    }

    protected function is_dynamic_content(): bool
    {
        return false;
    }

    protected function is_url(): bool
    {
        return true;
    }

    protected function is_html_tag(): bool
    {
        return true;
    }

    protected function html_tag(): string
    {
        return 'div';
    }

    protected function is_hover_controls(): bool
    {
        return true;
    }

    protected function is_text_for_global_settings(): bool
    {
        return false;
    }

    public function controls($context)
    {
        $this->controls->tab_content_section('text_render_content_section', [
            'label' => $context->get_title(),
        ], function () {

            $this->controls->raw_html('text_render_notice', [
                'raw' => esc_html__('Click on the control for more options.', 'addons-for-elementor-builder'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]);

            $this->controls->text('text_render', [
                'label_block' => true,
            ]);

            if ($this->is_url())
                $this->controls->url('text_render_url', []);

            if ($this->is_html_tag()) {
                $this->controls->select('text_render_html_tag', [
                    'label' => esc_html__('HTML Tag', 'addons-for-elementor-builder'),
                    'options' => [
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                        'div' => 'div',
                        'span' => 'span',
                        'p' => 'p',
                    ],
                    'default' => 'h2',
                ]);
            } else {
                $this->controls->hidden('text_render_html_tag', [
                    'default' => $this->html_tag(),
                ]);
            }
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Styles
         *
         */
        $this->controls->tab_style_section('text_render_style_section', [
            'label' => esc_html__('Content', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->alignment('align', [
                'justify' => true,
                'separator' => 'after',
            ]);

            $global = ['default' => !$this->is_text_for_global_settings() ? Global_Colors::COLOR_PRIMARY : Global_Colors::COLOR_TEXT,];

            $this->controls->text_color('color', [
                'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                'global' => $global,
                'selectors' => [
                    '{{WRAPPER}} .afeb-text-render' => 'color: {{VALUE}}',
                ],
            ]);

            if ($this->is_hover_controls()) {
                $this->controls->text_color('color_hover', [
                    'label' => esc_html__('Hover Color', 'addons-for-elementor-builder'),
                    'global' => $global,
                    'selectors' => [
                        '{{WRAPPER}} .afeb-text-render:hover' => 'color: {{VALUE}}',
                    ],
                ]);

                $this->controls->slider('transition_duration', [
                    'label' => esc_html__('Transition Duration', 'addons-for-elementor-builder'),
                    'size_units' => ['s', 'ms', 'custom'],
                    'default' => [
                        'unit' => 's',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .afeb-text-render' => 'transition-duration: {{SIZE}}{{UNIT}}',
                    ],
                ]);
            }

            $args = [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .afeb-text-render',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
            ];

            if ($this->is_text_for_global_settings())
                unset($args['global']);

            $this->controls->typography($args);

            $this->controls->text_shadow([
                'selector' => '{{WRAPPER}} .afeb-text-render',
            ]);

            $this->controls->text_stroke([
                'selector' => '{{WRAPPER}} .afeb-text-render',
            ]);

            $this->controls->select('blend_mode', [
                'label' => esc_html__('Blend Mode', 'addons-for-elementor-builder'),
                'options' => [
                    '' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'multiply' => esc_html__('Multiply', 'addons-for-elementor-builder'),
                    'screen' => esc_html__('Screen', 'addons-for-elementor-builder'),
                    'overlay' => esc_html__('Overlay', 'addons-for-elementor-builder'),
                    'darken' => esc_html__('Darken', 'addons-for-elementor-builder'),
                    'lighten' => esc_html__('Lighten', 'addons-for-elementor-builder'),
                    'color-dodge' => esc_html__('Color Dodge', 'addons-for-elementor-builder'),
                    'saturation' => esc_html__('Saturation', 'addons-for-elementor-builder'),
                    'color' => esc_html__('Color', 'addons-for-elementor-builder'),
                    'difference' => esc_html__('Difference', 'addons-for-elementor-builder'),
                    'exclusion' => esc_html__('Exclusion', 'addons-for-elementor-builder'),
                    'hue' => esc_html__('Hue', 'addons-for-elementor-builder'),
                    'luminosity' => esc_html__('Luminosity', 'addons-for-elementor-builder'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-text-render' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]);
        });
    }

    /**
     * Render RenderContent widget output on the frontend
     *
     * @since 1.4.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if ($settings['text_render'] === '')
            return;

        $this->add_render_attribute('text-render', 'class', 'afeb-text-render');

        $text_render = wp_kses_post($settings['text_render']);

        if (!empty($settings['text_render_url']['url'])) {
            $this->add_link_attributes('url', $settings['text_render_url']);
            $text_render = sprintf('<a %1$s>%2$s</a>', $this->get_render_attribute_string('url'), $text_render);
        }

        $text_render_html = sprintf(
            '<%1$s %2$s>%3$s</%1$s>',
            Utils::validate_html_tag($settings['text_render_html_tag']),
            $this->get_render_attribute_string('text-render'),
            $text_render
        );

        // PHPCS - the variable $text_render_html holds safe data
        echo $text_render_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Render RenderContent widget output in the editor
     *
     * @since 1.4.0
     */
    protected function content_template()
    {
?>
        <#
            let text_render=elementor.helpers.sanitize( settings.text_render, { ALLOW_DATA_ATTR: false } );

            if ( typeof settings.text_render_url !=='undefined' && settings.text_render_url.url !=='' ) {
            text_render='<a href="' + elementor.helpers.sanitizeUrl( settings.text_render_url.url ) + '">' + text_render + '</a>' ;
            }

            view.addRenderAttribute( 'text-render' , 'class' , [ 'afeb-text-render' ] );

            let text_render_html_tag=elementor.helpers.validateHTMLTag( settings.text_render_html_tag ),
            text_render_ouput='<' + text_render_html_tag + ' ' + view.getRenderAttributeString( 'text-render' ) + '>' + text_render + '</' + text_render_html_tag + '>' ;

            print( text_render_ouput );
            #>
    <?php
    }
}
