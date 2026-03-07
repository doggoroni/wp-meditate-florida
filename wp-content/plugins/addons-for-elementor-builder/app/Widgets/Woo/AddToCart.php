<?php

namespace AFEB\Widgets\Woo;

use AFEB\Controls\Helper;
use AFEB\Helper as GeneralHelper;
use Elementor\Widget_Base;

class AddToCart extends Widget_Base
{
    protected $generalHelper;

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $this->generalHelper = new GeneralHelper();

    }

    public function get_name(): string
    {
        return 'afebp_woo_add_to_cart';
    }

    public function get_title(): string
    {
        return esc_html__('Woo Add To Cart', 'addons-for-elementor-builder');
    }

    public function get_icon(): string
    {
        return 'afeb-iconsvg-add-to-cart';
    }

    public function get_categories(): array
    {
        return ['afeb_pro'];
    }

    public function get_keywords(): array
    {
        return ['woo', 'add to cart', 'button', 'product'];
    }

    public function get_style_depends(): array
    {
        return ['afeb-woo-add-to-cart'];
    }

    protected function register_controls()
    {
        $helper = new Helper($this);

        /**
         * Content Tab
         */
        $helper->tab_content_section('section_content', [
            'label' => esc_html__('Content', 'addons-for-elementor-builder-pro'),
        ], function () use ($helper)
        {

            $helper->select('product_source', [
                'label' => esc_html__('Product Source', 'addons-for-elementor-builder-pro'),
                'options' => [
                    'current' => esc_html__('Current Product', 'addons-for-elementor-builder-pro'),
                    'specific' => esc_html__('Specific Product', 'addons-for-elementor-builder-pro'),
                ],
                'default' => 'current',
                'render_type' => 'template',
                'label_block' => true,
            ]);

            $helper->dynamic_select('product_select', [
                'options' => 'get_posts_by_type',
                'label' => esc_html__('Select Product', 'addons-for-elementor-builder-pro'),
                'condition' => ['product_source' => 'specific'],
                'query_slug' => 'product',
                'render_type' => 'template',
                'description' => esc_html__('Search for a product by name.', 'addons-for-elementor-builder-pro'),
            ]);

            $helper->number('product_id', [
                'label' => esc_html__('Or Enter Product ID', 'addons-for-elementor-builder-pro'),
                'condition' => ['product_source' => 'specific'],
                'render_type' => 'template',
                'description' => esc_html__('Used if no product is selected above.', 'addons-for-elementor-builder-pro'),
            ]);

            $helper->switcher('show_quantity', [
                'label' => esc_html__('Show Quantity Field', 'addons-for-elementor-builder-pro'),
                'render_type' => 'template',
            ]);

            $helper->choose('input_position', [
                'label' => esc_html__('Input Position', 'addons-for-elementor-builder-pro'),
                'options' => [
                    'horizontal' => ['title' => esc_html__('Horizontal', 'addons-for-elementor-builder-pro'), 'icon' => 'eicon-h-align-left'],
                    'vertical' => ['title' => esc_html__('Vertical', 'addons-for-elementor-builder-pro'), 'icon' => 'eicon-v-align-top'],
                ],
                'default' => 'horizontal',
                'selectors_dictionary' => [
                    'horizontal' => 'flex-direction:row; align-items:center;',
                    'vertical' => 'flex-direction:column; align-items:center;',
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-atc-inner' => '{{VALUE}}',
                ],
                'frontend_available' => true,
            ]);

            $helper->text('button_text', [
                'label' => esc_html__('Button Text Override', 'addons-for-elementor-builder-pro'),
                'placeholder' => esc_html__('Leave empty for default', 'addons-for-elementor-builder-pro'),
                'render_type' => 'template',
                'default' => 'Add to cart',
                'label_block' => true,
            ]);

            $helper->select('after_add_redirect', [
                'label' => esc_html__('After Add Redirect', 'addons-for-elementor-builder-pro'),
                'default' => 'stay',
                'options' => [
                    'stay' => esc_html__('Stay on Page (AJAX)', 'addons-for-elementor-builder-pro'),
                    'cart' => esc_html__('Go to Cart Page', 'addons-for-elementor-builder-pro'),
                    'checkout' => esc_html__('Go to Checkout Page', 'addons-for-elementor-builder-pro'),
                ],
                'render_type' => 'template',
                'label_block' => true,
            ]);

            $helper->alignment('align', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder-pro'),
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-atc-inner' => 'justify-content: {{VALUE}};',
                ],
                'frontend_available' => true,
            ]);
        });
        $helper->tab_style_section('section_style_view_cart_button', [
            'label' => esc_html__('View Cart Button', 'addons-for-elementor-builder-pro'),
        ], function () use ($helper)
        {

            $selector = '.afeb-view-cart, .afeb-view-cart .button.wc-forward';

            $helper->typography([
                'selector' => '{{WRAPPER}} ' . $selector,
                'frontend_available' => true,
            ]);

            $helper->text_color('view_cart_btn_text_color', [
                'selectors' => ['{{WRAPPER}} ' . $selector => 'color: {{VALUE}};'],
                'frontend_available' => true,
            ]);

            $helper->background_color('view_cart_btn_bg_color', [
                'selectors' => ['{{WRAPPER}} ' . $selector => 'background-color: {{VALUE}};'],
                'frontend_available' => true,
            ]);

            $helper->border([
                'selector' => '{{WRAPPER}} ' . $selector,
                'frontend_available' => true,
            ]);

            $helper->border_radius('view_cart_btn_border_radius', [
                'selectors' => ['{{WRAPPER}} ' . $selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
                'frontend_available' => true,
            ]);

            $helper->padding('view_cart_btn_padding', [
                'selectors' => ['{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
                'frontend_available' => true,
            ]);

            $helper->box_shadow([
                'name' => 'view_cart_button_box_shadow',
                'selector' => '{{WRAPPER}} ' . $selector,
                'frontend_available' => true,
            ]);
        });
        /**
         * Style: Button
         */
        $helper->tab_style_section('section_style_button', [
            'label' => esc_html__('Button', 'addons-for-elementor-builder-pro'),
        ], function () use ($helper)
        {

            $helper->typography([
                'selector' => '{{WRAPPER}} .afeb-woo-atc-button',
                'frontend_available' => true,
            ]);

            $helper->slider('btn_width', [
                'label' => esc_html__('Button Width', 'addons-for-elementor-builder-pro'),
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 600],
                    '%' => ['min' => 0, 'max' => 100],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-atc-button' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
                'frontend_available' => true,
            ]);

            $helper->tabs('btn_style_tabs', [
                'normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder-pro'),
                    'callback' => function () use ($helper)
                    {
                        $helper->text_color('btn_text_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-woo-atc-button' => 'color: {{VALUE}};'],
                            'frontend_available' => true,
                        ]);
                        $helper->background_color('btn_bg_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-woo-atc-button' => 'background-color: {{VALUE}};'],
                            'frontend_available' => true,
                        ]);
                        $helper->border([
                            'selector' => '{{WRAPPER}} .afeb-woo-atc-button',
                            'frontend_available' => true,
                        ]);
                        $helper->border_radius('btn_border_radius_normal', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-woo-atc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                            'frontend_available' => true,
                        ]);
                        $helper->box_shadow([
                            'name' => 'button_box_shadow',
                            'selector' => '{{WRAPPER}} .afeb-woo-atc-button',
                            'frontend_available' => true,
                        ]);
                    },
                ],
                'hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder-pro'),
                    'callback' => function () use ($helper)
                    {
                        $helper->text_color('btn_text_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-woo-atc-button:hover' => 'color: {{VALUE}};'],
                            'frontend_available' => true,
                        ]);
                        $helper->background_color('btn_bg_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-woo-atc-button:hover' => 'background-color: {{VALUE}};'],
                            'frontend_available' => true,
                        ]);
                        // hover-only border
                        $helper->border([
                            'selector' => '{{WRAPPER}} .afeb-woo-atc-button:hover',
                            'frontend_available' => true,
                        ]);
                        $helper->border_radius('btn_border_radius_hover', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-woo-atc-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                            'frontend_available' => true,
                        ]);
                        $helper->box_shadow([
                            'name' => 'button_hover_box_shadow',
                            'selector' => '{{WRAPPER}} .afeb-woo-atc-button:hover',
                            'frontend_available' => true,
                        ]);
                    },
                ],
            ]);

            $helper->padding('btn_padding', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-atc-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'frontend_available' => true,
            ]);
        });

        /**
         * Style: Quantity
         */
        $helper->tab_style_section('section_style_qty', [
            'label' => esc_html__('Quantity Field', 'addons-for-elementor-builder-pro'),
            'condition' => ['show_quantity' => 'yes'],
        ], function () use ($helper)
        {

            // dedicated typography subheading
            $helper->heading('qty_typography_heading', [
                'label' => esc_html__('Typography', 'addons-for-elementor-builder-pro'),
                'separator' => 'before',
            ]);
            $helper->typography([
                'selector' => '{{WRAPPER}} .afeb-woo-atc-qty input.qty',
                'frontend_available' => true,
            ]);

            $helper->text_color('qty_text_color', [
                'selectors' => ['{{WRAPPER}} .afeb-woo-atc-qty input.qty' => 'color: {{VALUE}};'],
                'frontend_available' => true,
            ]);
            $helper->background_color('qty_bg_color', [
                'selectors' => ['{{WRAPPER}} .afeb-woo-atc-qty input.qty' => 'background-color: {{VALUE}};'],
                'frontend_available' => true,
            ]);
            $helper->border([
                'selector' => '{{WRAPPER}} .afeb-woo-atc-qty input.qty',
                'frontend_available' => true,
            ]);
            $helper->border_radius('qty_border_radius', [
                'selectors' => ['{{WRAPPER}} .afeb-woo-atc-qty input.qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
                'frontend_available' => true,
            ]);
            $helper->padding('qty_padding', [
                'selectors' => ['{{WRAPPER}} .afeb-woo-atc-qty input.qty' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
                'frontend_available' => true,
            ]);

            $helper->slider('qty_field_width', [
                'label' => esc_html__('Quantity Field Width', 'addons-for-elementor-builder-pro'),
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 300],
                    '%' => ['min' => 0, 'max' => 100],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-atc-qty input.qty' => 'width: {{SIZE}}{{UNIT}} !important;',
                ],
                'frontend_available' => true,
            ]);

            $helper->slider('qty_btn_spacing', [
                'label' => esc_html__('Spacing Between', 'addons-for-elementor-builder-pro'),
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-atc-inner' => '--afeb-woo-atc-gap: {{SIZE}}{{UNIT}};',
                ],
                'frontend_available' => true,
            ]);
        });
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Detect product ID
        if (($settings['product_source'] ?? '') === 'specific')
        {
            if (!empty($settings['product_select']))
            {
                $product_id = (int) $settings['product_select'];
            }
            else if (!empty($settings['product_id']))
            {
                $product_id = (int) $settings['product_id'];
            }
            else
            {
                $product_id = 0;
            }
        }
        else
        {
            $product_id = get_the_ID();
            if ($this->generalHelper->is_edit_mode() && get_post_type($product_id) !== 'product')
            {
                $preview_product = wc_get_products([
                    'status' => 'publish',
                    'limit' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'return' => 'ids',
                ]);
                if (!empty($preview_product[0]))
                {
                    $product_id = $preview_product[0];
                }
            }
        }

        // Fallback for editor preview: load first product if none selected


        // Make Woo functions happy in preview
        global $product;
        $product = wc_get_product($product_id);

        if (!$product)
        {
            echo '<p>No product found for preview.</p>';
            return;
        }

        $layout_class = ($settings['input_position'] ?? 'horizontal') === 'vertical'
            ? 'afeb-layout-vertical'
            : 'afeb-layout-horizontal';

        $redirect_mode = $settings['after_add_redirect'] ?? 'stay';

        echo '<div class="afeb-woo-atc">';
        echo '<div class="afeb-woo-atc-inner ' . esc_attr($layout_class) . '" style="display:flex!important;gap:var(--afeb-woo-atc-gap,10px)">';

        // Quantity field
        if (!empty($settings['show_quantity']) && $settings['show_quantity'] === 'yes')
        {
            echo '<span class="afeb-woo-atc-qty">';
            woocommerce_quantity_input([
                'min_value' => 1,
                'input_value' => 1,
            ], $product);
            echo '</span>';
        }

        $button_text = $settings['button_text'] ?: $product->add_to_cart_text();

        // For 'stay' (AJAX), use Woo's real URL even in editor to simulate
        $add_to_cart_url = ($redirect_mode === 'stay')
            ? $product->add_to_cart_url()
            : '#';

        // Classes: add WC's AJAX classes only for stay mode
        $classes = 'elementor-button elementor-size-md afeb-woo-atc-button';
        if ($redirect_mode === 'stay')
        {
            $classes .= ' add_to_cart_button ajax_add_to_cart';
        }

        $inline_styles = 'display:flex;align-items:center;justify-content:center;text-align:center;height:auto;white-space:nowrap;';

        printf(
            '<a href="%s" data-quantity="1" data-product_id="%d" data-redirect="%s" class="%s" style="%s">%s</a>',
            esc_url($add_to_cart_url),
            $product_id,
            esc_attr($redirect_mode),
            esc_attr($classes),
            esc_attr($inline_styles),
            esc_html($button_text)
        );

        // Show only a fake "View Cart" button in editor preview when AJAX is chosen
        if ($this->generalHelper->is_edit_mode() && $redirect_mode === 'stay')
        {
            $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
            echo '<a href="' . esc_url($cart_url) . '" class="added_to_cart wc-forward afeb-view-cart, afeb-view-cart">'
                . esc_html__('View cart', 'woocommerce') .
                '</a>';
        }
        echo '</div></div>';
    }
}
