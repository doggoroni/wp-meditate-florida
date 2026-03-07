<?php
namespace AFEB\Widgets\Woo;

use Elementor\Widget_Base;
use AFEB\Controls\Helper;

class ProductContent extends Widget_Base
{
    /** @var Helper */
    protected $controls;

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $this->controls = new Helper($this);
    }

    public function get_name(): string
    {
        return 'afeb_product_content';
    }

    public function get_title(): string
    {
        return esc_html__('Product Content', 'addons-for-elementor-builder');
    }

    public function get_icon(): string
    {
        return 'afeb-iconsvg-product-content';
    }

    public function get_categories(): array
    {
        return ['afeb_basic', 'theme-elements-single'];
    }

    public function get_keywords(): array
    {
        return ['product', 'content', 'woo', 'description', 'full'];
    }

    protected function register_controls()
    {
        $control = $this->controls;

        if (!class_exists('WooCommerce'))
        {
            $control->tab_content_section('afeb_wc_notice', ['label' => esc_html__('Notice', 'addons-for-elementor-builder')], function () use ($control)
            {
                $control->raw_html('no_wc_msg', [
                    'raw' => esc_html__('WooCommerce must be installed and active to use this widget.', 'addons-for-elementor-builder'),
                ]);
            });
            return;
        }

        /** Content Controls */
        $control->tab_content_section('section_content', ['label' => esc_html__('Content', 'addons-for-elementor-builder')], function () use ($control)
        {
            $control->select('product_source', [
                'label' => esc_html__('Product Source', 'addons-for-elementor-builder'),
                'options' => [
                    'current' => esc_html__('Current Product', 'addons-for-elementor-builder'),
                    'specific' => esc_html__('Specific Product', 'addons-for-elementor-builder'),
                ],
                'default' => 'current',
            ]);

            $control->dynamic_select('product_id', [
                'label' => esc_html__('Select Product', 'addons-for-elementor-builder'),
                'query_slug' => 'product',
                'options' => 'get_posts_by_type',
                'condition' => ['product_source' => 'specific'],
            ]);

            $control->alignment('alignment', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                'responsive' => true,
                'justify' => true,
            ]);
        });

        do_action('afeb/widget/content/after_render_content_section', $this);

        /** Style Controls - Content */
        $control->tab_style_section('style_content', ['label' => esc_html__('Content', 'addons-for-elementor-builder')], function () use ($control)
        {
            $control->typography(['selector' => '{{WRAPPER}} .afeb-product-content']);
            $control->text_color('content_color', ['selectors' => ['{{WRAPPER}} .afeb-product-content' => 'color: {{VALUE}};']]);
            $control->background(['selector' => '{{WRAPPER}} .afeb-product-content']);
            $control->padding('content_padding', ['selectors' => ['{{WRAPPER}} .afeb-product-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $control->border(['selector' => '{{WRAPPER}} .afeb-product-content']);
            $control->border_radius('content_radius', ['selectors' => ['{{WRAPPER}} .afeb-product-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $control->box_shadow([
                'name' => 'product_content_box_shadow',
                'selector' => '{{WRAPPER}} .afeb-product-content',
            ]);
        });
    }

    protected function render()
    {
        if (!class_exists('WooCommerce'))
        {
            echo '<div class="afeb-warning">' . esc_html__('WooCommerce is not active.', 'addons-for-elementor-builder') . '</div>';
            return;
        }

        $settings = $this->get_settings_for_display();
        $product = null;

        if ($settings['product_source'] === 'specific' && !empty($settings['product_id']))
        {
            $product = wc_get_product((int) $settings['product_id']);
        }
        else if (function_exists('wc_get_product') && get_the_ID())
        {
            $product = wc_get_product(get_the_ID());
        }

        if (!$product) return;

        $content = $product->get_description();

        echo '<div class="afeb-product-content">' . wp_kses_post($content) . '</div>';
    }
}
