<?php

namespace AFEB\Widgets\Woo;

use AFEB\Assets;
use AFEB\Controls\Helper as ControlsHelper;
use AFEB\Helper as LiteHelper;
use Elementor\Widget_Base;
use function WC;

if (!defined('ABSPATH')) {
    exit;
}

class Checkout extends Widget_Base
{
    private $assets;
    private $controls;
    private $lite;

    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        add_filter('elementor/widget/print_template', function($template, $widget)
        {
            if ($widget->get_name() === 'afeb_woo_checkout')
            {
                $template = preg_replace('/<div class="elementor-widget-container">(.*?)<\/div>/s', '', $template);
            }

            return $template;
        }, 10, 2);

        $this->assets   = new Assets();
        $this->controls = new ControlsHelper($this);
        $this->lite     = new LiteHelper();
        $this->assets->woo_checkout();

        // Force load in Elementor editor preview
        if ( $this->lite->is_edit_mode() ) wp_enqueue_style('afeb-woo-checkout-style');
    }

    public function get_name(): string
    {
        return 'afeb_woo_checkout';
    }

    public function get_title(): string
    {
        return esc_html__('Woo Checkout', 'addons-for-elementor-builder');
    }

    public function get_icon(): string
    {
        return 'afeb-iconsvg-checkout';
    }

    public function get_categories(): array
    {
        return ['afeb_basic'];
    }

    public function get_keywords(): array
    {
        return [];
    }

    public function get_script_depends(): array
    {
        $scripts = [];

        // Enqueue live-edit JS only in Elementor editor
        if ( $this->lite->is_edit_mode() )
        {
            wp_register_script(
                'afeb-woo-checkout-live',
                $this->assets->assets_url( 'js/woo-checkout-live.js'),
                ['jquery', 'elementor-frontend'],
                '1.0.2',
                true
            );
            $scripts[] = 'afeb-woo-checkout-live';
        }

        return $scripts;
    }

    public function get_style_depends(): array
    {
        return ['afeb-woo-checkout-style', 'afeb-woo-checkout-layout'];
    }

    public function register_controls()
    {
        // General Section
        $this->controls->tab_content_section('afeb_general_section', [
            'label' => esc_html__('General', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->select('afeb_general_layout', [
                'label' => esc_html__('Layout', 'addons-for-elementor-builder'),
                'options' => [
                    'one-column' => esc_html__('One Column', 'addons-for-elementor-builder'),
                    'two-column' => esc_html__('Two Column', 'addons-for-elementor-builder'),
                ],
                'default' => 'two-column',
                'frontend_available' => true,
            ]);
        });
            $this->controls->tab_content_section('afeb_login_form_section', [
                'label' => esc_html__('Login Form', 'addons-for-elementor-builder'),
            ], function () {
                $this->controls->switcher('afeb_general_show_login', [
                    'label' => esc_html__('Show Login Form', 'addons-for-elementor-builder'),
                    'return_value' => 'yes',
                    'default' => 'no',
                    'frontend_available' => true,
                ]);
            });

        // Two Column Section
        $this->controls->tab_content_section('afeb_two_column_section', [
            'label' => esc_html__('Two Column', 'addons-for-elementor-builder'),
            'condition' => [
                'afeb_general_layout' => 'two-column',
            ],
        ], function () {
            $this->controls->slider('afeb_two_column_left_width', [
                'label' => esc_html__('Left Column Width (%)', 'addons-for-elementor-builder'),
                'size_units' => ['%'],
                'range' => ['%' => ['min' => 10, 'max' => 90]],
                'default' => ['size' => 60, 'unit' => '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-wc-checkout-layout-two-column' => 'position: relative;',
                    '{{WRAPPER}} .elementor-wc-checkout-left' => 'width: {{SIZE}}%; float: left;',
                    '{{WRAPPER}} .elementor-wc-checkout-right' => 'width: calc(100% - {{SIZE}}%); margin-left: -10px; float: right;',
                ],
            ]);
        });

        // Billing Details Section
        $this->controls->tab_content_section('afeb_billing_details_section', [
            'label' => esc_html__('Billing Details', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->switcher('afeb_billing_show', [
                'label' => esc_html__('Show Billing Details', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);
        });

        //Shipping Section
        $this->controls->tab_content_section('afeb_shipping_details_section', [
            'label' => esc_html__('Shipping Details', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->switcher('afeb_shipping_show', [
                'label' => esc_html__('Show Shipping Details', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'no',
            ]);
        });

        // Coupon Section
        $this->controls->tab_content_section('afeb_coupon_section', [
            'label' => esc_html__('Coupon', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->switcher('afeb_coupon_enable', [
                'label' => esc_html__('Enable Coupon', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);
        });

        // Additional Notes Section
        $this->controls->tab_content_section('afeb_additional_notes_section', [
            'label' => esc_html__('Additional Notes', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->switcher('afeb_notes_show', [
                'label' => esc_html__('Show Additional Notes Field', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'no',
            ]);
        });

        // Payments Section
        $this->controls->tab_content_section('afeb_payments_section', [
            'label' => esc_html__('Payments', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->switcher('afeb_payments_show', [
                'label' => esc_html__('Show Payment Methods', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);
        });

        // Your Order Section
        $this->controls->tab_content_section('afeb_your_order_section', [
            'label' => esc_html__('Your Order', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->switcher('afeb_order_show', [
                'label' => esc_html__('Show Order Review', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]);
        });

        do_action('afeb/widget/content/after_render_content_section', $this);

        // STYLE TAB - Forms Section
        $this->controls->tab_style_section('section_forms_style', [
            'label' => esc_html__('Forms', 'elementor-pro'),
        ], function () {
            $this->controls->heading('forms_label_title', [
                'label' => esc_html__('Labels', 'elementor-pro'),
            ]);

            $this->controls->color('forms_label_color', [
                'label' => esc_html__('Color', 'elementor-pro'),
                'selectors' => [
                    '{{WRAPPER}}' => '--forms-labels-color: {{VALUE}};',
                ],
            ]);

            $this->controls->typography_important([
                'name' => 'forms_label_typography',
                'label' => esc_html__('Form Label Typography', 'addons-for-elementor-builder'),
                'selector' => '{{WRAPPER}} .woocommerce-billing-fields .form-row label, {{WRAPPER}} .woocommerce-shipping-fields .form-row label, {{WRAPPER}} .woocommerce-additional-fields .form-row label',
            ]);

            $this->controls->responsive()->slider('forms_label_spacing', [
                'label' => esc_html__('Spacing', 'elementor-pro'),
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => ['max' => 100],
                    'em' => ['max' => 10],
                    'rem' => ['max' => 10],
                ],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-checkout .form-row label' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                ],
            ]);

            $this->controls->heading('forms_field_title', [
                'label' => esc_html__('Fields', 'elementor-pro'),
            ]);

            $this->controls->typography_important([
                'name' => 'forms_field_typography',
                'label' => esc_html__('Field Typography', 'addons-for-elementor-builder'),
                'selector' => '{{WRAPPER}} .woocommerce form .form-row input.input-text, 
                   {{WRAPPER}} .woocommerce form .form-row textarea, 
                   {{WRAPPER}} .woocommerce form .form-row select,
                   {{WRAPPER}}  .woocommerce-checkout input,
                   {{WRAPPER}} .woocommerce-checkout select,
                   {{WRAPPER}} .select2-container--default .select2-selection--single,
                   {{WRAPPER}} .select2-container--default .select2-selection--multiple,
                   {{WRAPPER}} .select2-container',

            ]);

            $this->controls->tabs('forms_fields_style_tab', [
                'forms_fields_normal_tab' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->color('forms_fields_normal_color', [
                            'label' => esc_html__('Text Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}}' => '--forms-fields-normal-color: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'forms_fields_normal_box_shadow',
                            'selector' => '{{WRAPPER}} .woocommerce form .form-row input.input-text, {{WRAPPER}} .woocommerce form .form-row textarea, {{WRAPPER}} .woocommerce form .form-row select',
                        ]);

                        $this->controls->color('forms_fields_border_color', [
                            'label' => esc_html__('Border Color', 'elementor-pro'),
                            'selectors' => [
                                '{{WRAPPER}}' => '--forms-fields-border-color: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->responsive()->border_radius('forms_fields_border_radius', [
                            'label' => esc_html__('Border Radius', 'elementor-pro'),
                            'size_units' => ['px', '%', 'em'],
                            'selectors' => [
                                '{{WRAPPER}}' => '--forms-fields-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->responsive()->dimensions('forms_fields_padding', [
                            'label' => esc_html__('Padding', 'elementor-pro'),
                            'size_units' => ['px', 'em', 'rem'],
                            'selectors' => [
                                '{{WRAPPER}}' => '--forms-fields-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
                'forms_fields_focus_tab' => [
                    'label' => esc_html__('Focus', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->color('forms_fields_focus_color', [
                                'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                                'selectors' => [
                                        '{{WRAPPER}}' => '--forms-fields-focus-color: {{VALUE}};',
                                ],
                        ]);

                        $this->controls->box_shadow([
                                'name' => 'forms_fields_focus_box_shadow',
                                'selectors' =>
                                        '{{WRAPPER}} .woocommerce form .form-row input.input-text, 
                                          {{WRAPPER}} .woocommerce form .form-row textarea, 
                                         {{WRAPPER}} .woocommerce form .form-row select => padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',

                        ]);
                    },
                ],
            ]);
        });

        // STYLE TAB - Order Table Section
        $this->controls->tab_style_section('section_order_table_style', [
                'label' => esc_html__('Order Table', 'elementor-pro'),
        ], function () {
            $this->controls->responsive()->dimensions('order_table_cell_padding', [
                'label' => esc_html__('Cell Padding', 'elementor-pro'),
                'size_units' => ['px', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}}' => '--order-table-cell-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->heading('order_table_header_title', [
                'label' => esc_html__('Table Header', 'elementor-pro'),
            ]);

            $this->controls->color('order_table_header_bg', [
                'label' => esc_html__('Background Color', 'elementor-pro'),
                'selectors' => [
                    '{{WRAPPER}}' => '--order-table-header-bg: {{VALUE}};',
                ],
            ]);

            $this->controls->color('order_table_header_color', [
                'label' => esc_html__('Text Color', 'elementor-pro'),
                'selectors' => [
                    '{{WRAPPER}}' => '--order-table-header-color: {{VALUE}};',
                ],
            ]);

            $this->controls->heading('order_table_footer_title', [
                'label' => esc_html__('Table Footer', 'elementor-pro'),
            ]);

            $this->controls->color('order_table_footer_bg', [
                'label' => esc_html__('Background Color', 'elementor-pro'),
                'selectors' => [
                    '{{WRAPPER}}' => '--order-table-footer-bg: {{VALUE}};',
                ],
            ]);

            $this->controls->color('order_table_footer_color', [
                'label' => esc_html__('Text Color', 'elementor-pro'),
                'selectors' => [
                    '{{WRAPPER}}' => '--order-table-footer-color: {{VALUE}};',
                ],
            ]);
        });

        // STYLE TAB - Payment Methods Section
        $this->controls->tab_style_section('section_payment_methods_style', [
                'label' => esc_html__('Payment Methods', 'elementor-pro'),
        ], function () {
            $this->controls->color('payment_background_color', [
                'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
                'selectors' => [
                    '{{WRAPPER}}' => '--payment-background-color: {{VALUE}};',
                ],
            ]);
            $this->controls->select('payment_border_type', [
                'label' => esc_html__('Border Type', 'addons-for-elementor-builder'),
                'options' => [
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                    'solid' => esc_html__('Solid', 'addons-for-elementor-builder'),
                    'double' => esc_html__('Double', 'addons-for-elementor-builder'),
                    'dotted' => esc_html__('Dotted', 'addons-for-elementor-builder'),
                    'dashed' => esc_html__('Dashed', 'addons-for-elementor-builder'),
                    'groove' => esc_html__('Groove', 'addons-for-elementor-builder'),
                ],
                'default' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-checkout #payment .payment_methods li' => 'border-style: {{VALUE}} !important;',
                ],
            ]);

            $this->controls->color('payment_border_color', [
                'label' => esc_html__('Border Color', 'addons-for-elementor-builder'),
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-checkout #payment .payment_methods li' => 'border-color: {{VALUE}} !important;',
                ],
            ]);

            $this->controls->responsive()->dimensions('payment_border_width', [
                'label' => esc_html__('Border Width', 'elementor-pro'),
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-checkout #payment .payment_methods li' => 'border-style: solid; border-width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]);

            $this->controls->responsive()->border_radius('payment_border_radius', [
                'label' => esc_html__('Border Radius', 'elementor-pro'),
                'size_units' => ['px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}}' => '--payment-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        });

        // STYLE TAB - Buttons Section
        $this->controls->tab_style_section('section_buttons_style', [
                'label' => esc_html__('Buttons', 'elementor-pro'),
        ], function () {
            $this->controls->tabs('buttons_style_tab', [
                'buttons_normal_tab' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->color('button_bg_color', [
                            'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}}' => '--button-bg-color: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->color('button_text_color', [
                            'label' => esc_html__('Text Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}}' => '--button-text-color: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->border([
                                'name' => 'button_border',
                                'selector' => '{{WRAPPER}} .woocommerce #place_order',
                                'fields_options' => [
                                        'border' => [
                                                'default' => 'solid',
                                        ],
                                        'width' => [
                                                'default' => [
                                                        'top' => '1',
                                                        'right' => '1',
                                                        'bottom' => '1',
                                                        'left' => '1',
                                                        'unit' => 'px',
                                                        'isLinked' => true,
                                                ],
                                        ],
                                        'color' => [
                                                'default' => '#cccccc',
                                        ],
                                ],
                        ]);

                        $this->controls->box_shadow([
                                'name' => 'button_box_shadow',
                                'selector' => '{{WRAPPER}} #place_order, {{WRAPPER}} .checkout_coupon button',
                        ]);

                        $this->controls->typography_important([
                                'name' => 'button_typography',
                                'label' => esc_html__('Button Typography', 'addons-for-elementor-builder'),
                                'selector' => '{{WRAPPER}} .woocommerce #place_order',
                            // Optional additional fields_options
                        ]);


                        $this->controls->responsive()->border_radius('button_border_radius', [
                                'label' => esc_html__('Border Radius', 'elementor-pro'),
                                'size_units' => ['px', '%', 'em', 'rem'],
                                'selectors' => [
                                        '{{WRAPPER}}' => '--button-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                                ],
                        ]);

                        $this->controls->responsive()->dimensions('button_padding', [
                                'label' => esc_html__('Padding', 'elementor-pro'),
                                'size_units' => ['px', 'em', 'rem'],
                                'selectors' => [
                                        '{{WRAPPER}} .woocommerce #place_order' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                                ],
                        ]);
                    },
                ],
                'buttons_hover_tab' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->color('button_bg_hover_color', [
                            'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}}' => '--button-bg-hover-color: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->color('button_text_hover_color', [
                            'label' => esc_html__('Text Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}}' => '--button-text-hover-color: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'button_border_hover',
                            'selector' => '{{WRAPPER}} #place_order:hover, {{WRAPPER}} .checkout_coupon button:hover',
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'button_box_shadow_hover',
                            'selector' => '{{WRAPPER}} #place_order:hover, {{WRAPPER}} .checkout_coupon button:hover',
                        ]);

                        $this->controls->slider('button_transition_duration', [
                            'label' => esc_html__('Transition Duration', 'elementor-pro') . ' (ms)',
                            'size_units' => ['px'],
                            'range' => [
                                'px' => [
                                        'min' => 0,
                                        'max' => 3000,
                                        'step' => 100,
                                ],
                            ],
                            'selectors' => [
                                '{{WRAPPER}}' => '--button-transition-duration: {{SIZE}}ms',
                            ],
                        ]);
                    },
                ],
            ]);
        });
    }
    /**
     * Renders login and coupon forms
     */
    protected function render_login_and_coupon_forms($settings)
    {
        // Login form
        if ($settings['afeb_general_show_login'] === 'yes' && !is_user_logged_in()) woocommerce_checkout_login_form();

        // Coupon form
        if ($settings['afeb_coupon_enable'] === 'yes') woocommerce_checkout_coupon_form();
    }

    /**
     * Renders billing and shipping fields
     */
    protected function render_billing_shipping_fields($settings)
    {
        $checkout = WC()->checkout();

        // Billing details
        if ($settings['afeb_billing_show'] === 'yes')
        {
            echo '<div class="woocommerce-billing-fields">';
            echo '<h3>' . esc_html__('Billing details', 'woocommerce') . '</h3>';

            $billing_fields = $checkout->get_checkout_fields('billing');

            foreach ($billing_fields as $key => $field) woocommerce_form_field($key, $field, $checkout->get_value($key));

            echo '</div>';
        }

        // Shipping details
        if ($settings['afeb_shipping_show'] === 'yes')
        {
            echo '<div class="woocommerce-shipping-fields">';
            echo '<h3>' . esc_html__('Shipping details', 'woocommerce') . '</h3>';

            $shipping_fields = $checkout->get_checkout_fields('shipping');

            foreach ($shipping_fields as $key => $field) woocommerce_form_field($key, $field, $checkout->get_value($key));

            echo '</div>';
        }

        // Additional information
        if ($settings['afeb_notes_show'] === 'yes')
        {
            echo '<div class="woocommerce-additional-fields">';
            echo '<h3>' . esc_html__('Additional information', 'woocommerce') . '</h3>';

            $order_fields = $checkout->get_checkout_fields('order');

            foreach ($order_fields as $key => $field) woocommerce_form_field($key, $field, $checkout->get_value($key));

            echo '</div>';
        }
    }

    public function render()
    {
        // Basic checks and initialization
        if (!class_exists('WooCommerce'))
        {
            echo '<div class="elementor-alert elementor-alert-warning">';
            echo esc_html__('WooCommerce is required for this widget.', 'addons-for-elementor-builder');
            echo '</div>';
            return;
        }

        if (!function_exists('WC') || !WC()->cart)
        {
            if ($this->lite->is_edit_mode())
            {
                echo '<div class="elementor-alert elementor-alert-info">';
                echo esc_html__('Checkout preview requires WooCommerce cart.', 'addons-for-elementor-builder');
                echo '</div>';
            }
            return;
        }

        $settings = $this->get_settings_for_display();
        $layout = $settings['afeb_general_layout'] ?? 'two-column';

        // Initialize checkout if not already done
        if (!WC()->checkout())
        {
            WC()->frontend_includes();
            WC()->initialize_cart();
        }

        // Remove default WooCommerce hooks
        remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
        remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

        // Configure checkout form wrapper
        $this->add_render_attribute('checkout-form', 'name', 'checkout');
        $this->add_render_attribute('checkout-form', 'method', 'post');
        $this->add_render_attribute('checkout-form', 'action', esc_url(wc_get_checkout_url()));
        $this->add_render_attribute('checkout-form', 'enctype', 'multipart/form-data');
        $this->add_render_attribute('checkout-form', 'class', [
            'checkout',
            'woocommerce',
            'woocommerce-checkout',
            'elementor-wc-checkout',
            'elementor-wc-checkout-layout-' . $layout,
        ]);

        // Editor-specific adjustments
        if ($this->lite->is_edit_mode())
        {
            echo '<style>
            .elementor-widget-afeb_woo_checkout .elementor-widget-container:before,
            .elementor-widget-empty-icon {
                display: none !important;
            }
        </style>';
            wp_enqueue_script('wc-checkout');
        }
        ?>
        <form <?php echo $this->get_render_attribute_string('checkout-form'); ?>>
            <?php
            // Login and coupon forms
            $this->render_login_and_coupon_forms($settings);

            if ($layout === 'two-column') : ?>
                <div class="elementor-wc-checkout-columns">
                    <div class="elementor-wc-checkout-left">
                        <?php $this->render_billing_shipping_fields($settings); ?>
                    </div>

                    <?php
                    // Right column - only show if either section is enabled
                    $show_right_column = $settings['afeb_order_show'] === 'yes' || $settings['afeb_payments_show'] === 'yes';
                    if ($show_right_column) : ?>
                        <div class="elementor-wc-checkout-right">
                            <?php
                            // Order review (independent)
                            if ($settings['afeb_order_show'] === 'yes')
                            {
                                echo '<div class="elementor-order-review-section">';
                                echo '<h3>' . esc_html__('Your order', 'woocommerce') . '</h3>';
                                woocommerce_order_review();
                                echo '</div>';
                            }

                            // Payment methods (independent)
                            if ($settings['afeb_payments_show'] === 'yes')
                            {
                                echo '<div id="payment" class="elementor-payment-section">';
                                woocommerce_checkout_payment();
                                echo '</div>';
                            }
                            else
                            {
                                echo '<style>.elementor-payment-section { display: none !important; }</style>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="elementor-wc-checkout-single-column">
                    <?php
                    // One-column layout
                    $this->render_billing_shipping_fields($settings);

                    // Order review
                    if ($settings['afeb_order_show'] === 'yes')
                    {
                        echo '<div class="elementor-order-review-section">';
                        echo '<h3>' . esc_html__('Your order', 'woocommerce') . '</h3>';
                        woocommerce_order_review();
                        echo '</div>';
                    }

                    // Payment methods
                    if ($settings['afeb_payments_show'] === 'yes')
                    {
                        echo '<div id="payment" class="elementor-payment-section">';
                        woocommerce_checkout_payment();
                        echo '</div>';
                    } else {
                        echo '<style>.elementor-payment-section { display: none !important; }</style>';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
        </form>
        <?php
    }
}
