<?php
namespace AFEB\Widgets\Woo;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use AFEB\Controls\Helper;

if (!defined('ABSPATH'))
{
    exit;
}

class ProductExcerpt extends Widget_Base
{
    /** @var Helper */
    protected $controls;

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $this->controls = new Helper($this);
    }

    public function get_name()
    {
        return 'afeb_product_excerpt';
    }

    public function get_title()
    {
        return esc_html__('Product Excerpt', 'addons-for-elementor-builder');
    }

    public function get_icon()
    {
        return 'afeb-iconsvg-product-excerpt';
    }

    public function get_categories()
    {
        return ['afeb_basic', 'theme-elements-single'];
    }

    public function get_keywords()
    {
        return ['product', 'excerpt', 'woo', 'description'];
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

            $control->select('excerpt_source', [
                'label' => esc_html__('Excerpt Source', 'addons-for-elementor-builder'),
                'options' => [
                    'short' => esc_html__('Short Description', 'addons-for-elementor-builder'),
                    'long' => esc_html__('Auto from Long Description', 'addons-for-elementor-builder'),
                ],
                'default' => 'short',
            ]);

            $control->control('words_limit', [
                'label' => esc_html__('Words Limit', 'addons-for-elementor-builder'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'default' => 25,
            ]);

            $control->switcher('read_more', [
                'label' => esc_html__('Read More', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
            ]);

            $control->text('read_more_text', [
                'label' => esc_html__('Read More Text', 'addons-for-elementor-builder'),
                'default' => esc_html__('Read more', 'addons-for-elementor-builder'),
                'condition' => ['read_more' => 'yes'],
            ]);

            $control->select('read_more_link', [
                'label' => esc_html__('Read More Link', 'addons-for-elementor-builder'),
                'options' => [
                    'product' => esc_html__('Product Page', 'addons-for-elementor-builder'),
                    'custom' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                ],
                'default' => 'product',
                'condition' => ['read_more' => 'yes'],
            ]);

            $control->url('custom_read_more_url', [
                'label' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                'condition' => ['read_more' => 'yes', 'read_more_link' => 'custom'],
            ]);

            $control->alignment('alignment', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                'responsive' => true,
                'justify' => true,
            ]);
        });

        /** Style Controls - Excerpt */
        $control->tab_style_section('style_excerpt', ['label' => esc_html__('Excerpt', 'addons-for-elementor-builder')], function () use ($control)
        {
            $control->typography(['selector' => '{{WRAPPER}} .afeb-product-excerpt']);
            $control->text_color('excerpt_color', ['selectors' => ['{{WRAPPER}} .afeb-product-excerpt' => 'color: {{VALUE}};']]);
            $control->background(['selector' => '{{WRAPPER}} .afeb-product-excerpt']);
            $control->padding('excerpt_padding', ['selectors' => ['{{WRAPPER}} .afeb-product-excerpt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $control->border(['selector' => '{{WRAPPER}} .afeb-product-excerpt']);
            $control->border_radius('excerpt_radius', ['selectors' => ['{{WRAPPER}} .afeb-product-excerpt' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $control->box_shadow([
                'name' => 'product_excerpt_box_shadow',
                'selector' => '{{WRAPPER}} .afeb-product-excerpt',
            ]);
        });

        /** Style Controls - Read More */
        $control->tab_style_section('style_read_more', [
            'label' => esc_html__('Read More', 'addons-for-elementor-builder'),
            'condition' => ['read_more' => 'yes'],
        ], function () use ($control)
        {
            // Always visible typography controls
            $control->typography([
                'name' => 'read_more_typography_normal',
                'label' => esc_html__('Read More Typography (Normal)', 'addons-for-elementor-builder'),
                'selector' => '{{WRAPPER}} .afeb-read-more',
            ]);

            // Other styling in tabs
            $control->tabs('read_more_tabs', [
                'normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () use ($control)
                    {
                        $control->text_color('read_more_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-read-more' => 'color: {{VALUE}} !important;'],
                        ]);
                        $control->background_color('read_more_bg', [
                            'selectors' => ['{{WRAPPER}} .afeb-read-more' => 'background-color: {{VALUE}} !important;'],
                        ]);
                        $control->border(['selector' => '{{WRAPPER}} .afeb-read-more']);
                        $control->box_shadow([
                            'name' => 'read_more_box_shadow',
                            'selector' => '{{WRAPPER}} .afeb-read-more',
                        ]);
                    },
                ],
                'hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () use ($control)
                    {
                        $control->text_color('read_more_hover_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-read-more:hover' => 'color: {{VALUE}} !important;'],
                        ]);
                        $control->background_color('read_more_hover_bg', [
                            'selectors' => ['{{WRAPPER}} .afeb-read-more:hover' => 'background-color: {{VALUE}} !important;'],
                        ]);
                        $control->border(['selector' => '{{WRAPPER}} .afeb-read-more:hover']);
                        $control->box_shadow([
                            'name' => 'read_more_hover_box_shadow',
                            'selector' => '{{WRAPPER}} .afeb-read-more:hover',
                        ]);
                    },
                ],
            ]);

            $control->padding('read_more_padding', [
                'selectors' => ['{{WRAPPER}} .afeb-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
            ]);
            $control->border_radius('read_more_radius', [
                'selectors' => ['{{WRAPPER}} .afeb-read-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
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

        if (!$product)
        {
            return;
        }

        $excerpt = '';
        if ($settings['excerpt_source'] === 'short')
        {
            $excerpt = $product->get_short_description();
        }
        if (empty($excerpt) && $settings['excerpt_source'] === 'long')
        {
            $excerpt = wp_strip_all_tags(strip_shortcodes($product->get_description()));
        }

        if (!empty($settings['words_limit']) && $settings['words_limit'] > 0)
        {
            $excerpt = wp_trim_words($excerpt, absint($settings['words_limit']));
        }

        echo '<div class="afeb-product-excerpt">' . wp_kses_post($excerpt);

        if (!empty($settings['read_more']) && $settings['read_more'] === 'yes')
        {
            $link = '#';
            if ($settings['read_more_link'] === 'product')
            {
                $link = get_permalink($product->get_id());
            }
            else if (!empty($settings['custom_read_more_url']['url']))
            {
                $link = esc_url($settings['custom_read_more_url']['url']);
            }
            echo ' <a class="afeb-read-more" href="' . esc_url($link) . '">' . esc_html($settings['read_more_text']) . '</a>';
        }

        echo '</div>';
    }
}
