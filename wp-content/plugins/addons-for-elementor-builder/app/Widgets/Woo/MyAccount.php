<?php

namespace AFEB\Widgets\Woo;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;

class MyAccount extends Widget_Base
{
    private $assets;
    private $controls;

    private $helper;
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->helper = new Helper();
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->woo_my_account();
        $this->assets->woo_my_account_script();

        add_filter('woocommerce_prevent_admin_access', function($prevent) {
            if (isset($_GET['elementor-preview'])) {
                return false;
            }
            return $prevent;
        });

        add_filter('woocommerce_is_account_page', function($is_account_page) {
            if (isset($_GET['elementor-preview'])) {
                return true;
            }
            return $is_account_page;
        });
        add_filter('woocommerce_get_endpoint_url', function($url, $endpoint, $value, $permalink) {
            // Make sure our custom endpoint URLs work in the editor
            if (isset($_GET['elementor-preview'])) {
                return add_query_arg($endpoint, $value, $permalink);
            }
            return $url;
        }, 10, 4);

// Force WooCommerce to load scripts in editor
        add_action('elementor/editor/before_enqueue_scripts', function() {
            if (function_exists('WC')) {
                WC()->frontend_includes();
                if (is_callable([WC()->payment_gateways(), 'init'])) {
                    WC()->payment_gateways()->init();
                }
                wp_enqueue_script('wc-checkout');
                wp_enqueue_script('selectWoo');
            }
        });

    }

    public function get_name(): string
    {
        return 'afeb_woo_my_account';
    }

    public function get_title(): string
    {
        return esc_html__('Woo My Account', 'addons-for-elementor-builder');
    }

    public function get_icon(): string
    {
        return 'afeb-iconsvg-myacc';
    }

    public function get_categories(): array
    {
        return ['afeb_basic'];
    }

    public function get_keywords(): array
    {
        return [];
    }

    public function get_style_depends(): array
    {
        return ['afeb-woo-my-account-style'];
    }


    public function get_script_depends(): array
    {
        return ['afeb-woo-my-account-script'];
    }

    public function register_controls() {
        // Content Tab
        $this->controls->tab_content_section('tabs_content_section', [
            'label' => esc_html__('Tabs', 'addons-for-elementor-builder')
        ], function () {
            $start = is_rtl() ? 'end' : 'start';
            $end = is_rtl() ? 'start' : 'end';

            // Layout Controls
            $this->controls->choose('tabs_layout', [
                'label' => esc_html__('Layout', 'addons-for-elementor-builder'),
                'options' => [
                    'vertical' => [
                        'title' => esc_html__('Vertical', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-gallery-grid'
                    ],
                    'horizontal' => [
                        'title' => esc_html__('Horizontal', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-form-vertical'
                    ],
                ],
                'default' => 'vertical',
                'render_type' => 'template',
                'prefix_class' => 'afeb-my-account-tabs-',
            ]);

            // Tab Items Repeater
            $repeater = new Repeater();
            $repeater_controls = new CHelper($repeater);
            // Add field_key control first
            $repeater_controls->hidden('field_key', [
                'default' => '',
            ]);

            $repeater_controls->text('tab_name', [
                'label' => esc_html__('Tab Name', 'addons-for-elementor-builder'),
                'dynamic' => ['active' => true],
                'frontend_available' => true
            ]);

            $repeater_controls->raw_html('order_display_description', [
                'content_classes' => 'elementor-descriptor',
                'raw' => esc_html__('Note: By default, only your last order is displayed while editing the orders section. You can see other orders on your live site or in the WooCommerce orders section', 'addons-for-elementor-builder'),
                'condition' => ['field_key' => 'orders'],
            ]);

            $repeater_controls->yn_switcher('customize_content', [
                'label' => esc_html__('Customize Content', 'addons-for-elementor-builder'),
                'label_on' => esc_html__('Yes', 'addons-for-elementor-builder'),
                'label_off' => esc_html__('No', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'render_type' => 'template',
                'frontend_available' => true,
            ]);

            $repeater_controls->dynamic_select('custom_template', [
                'label' => esc_html__('Template', 'addons-for-elementor-builder'),
                'options' => 'get_templates',
                'label_block' => true,
                'render_type' => 'template',
                'frontend_available' => true,
                'condition' => [
                    'customize_content' => 'yes',
                ],
            ]);

            $this->controls->repeater('tabs', [
                'label' => esc_html__('Tabs Items', 'addons-for-elementor-builder'),
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['field_key' => 'dashboard', 'tab_name' => esc_html__('Dashboard', 'addons-for-elementor-builder')],
                    ['field_key' => 'orders', 'tab_name' => esc_html__('Orders', 'addons-for-elementor-builder')],
                    ['field_key' => 'downloads', 'tab_name' => esc_html__('Downloads', 'addons-for-elementor-builder')],
                    ['field_key' => 'edit-address', 'tab_name' => esc_html__('Addresses', 'addons-for-elementor-builder')],
                    ['field_key' => 'edit-account', 'tab_name' => esc_html__('Account Details', 'addons-for-elementor-builder')],
                    ['field_key' => 'customer-logout', 'tab_name' => esc_html__('Logout', 'addons-for-elementor-builder')],
                ],
                'title_field' => '{{{ tab_name }}}',
                'frontend_available' => true,
                'item_actions' => [
                    'add' => false,
                    'duplicate' => false,
                    'remove' => false,
                    'sort' => false,
                ],
            ]);
        });

        // Style Tab - Navigation
        $this->controls->tab_style_section('navigation_style_section', [
            'label' => esc_html__('Tabs', 'addons-for-elementor-builder'),
        ], function () {

            // Core Navigation Structure
            $state_controls = [
                'normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'selectors' => [
                        'text' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a',
                        'background' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a',
                        'border' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a',
                        'box_shadow' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a',
                        'typography' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a',
                    ],
                    'border_fields_options' => [
                        'border' => ['default' => 'left'],
                        'width' => [
                            'default' => [
                                'top' => '0',
                                'right' => '0',
                                'bottom' => '0',
                                'left' => '3',
                                'unit' => 'px',
                                'isLinked' => false,
                            ],
                        ],
                        'color' => ['default' => 'transparent'],
                    ],
                ],
                'hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'selectors' => [
                        'text' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a:hover',
                        'background' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a:hover',
                        'border' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a:hover',
                        'box_shadow' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a:hover',
                        'typography' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a:hover',
                    ],
                    'border_fields_options' => [
                        'border' => ['default' => 'left'],
                        'width' => [
                            'default' => [
                                'top' => '0',
                                'right' => '0',
                                'bottom' => '0',
                                'left' => '3',
                                'unit' => 'px',
                                'isLinked' => false,
                            ],
                        ],
                        'color' => ['default' => '#5bc0de'],
                    ],
                ],
                'active' => [
                    'label' => esc_html__('Active', 'addons-for-elementor-builder'),
                    'selectors' => [
                        'text' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li.is-active a',
                        'background' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li.is-active a, {{WRAPPER}}.afeb-my-account-tabs-horizontal .woocommerce-MyAccount-navigation ul li.is-active a',
                        'border' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li.is-active a',
                        'box_shadow' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li.is-active a',
                        'typography' => '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li.is-active a',
                    ],
                    'border_fields_options' => [
                        'border' => ['default' => 'left'],
                        'width' => [
                            'default' => [
                                'top' => '0',
                                'right' => '0',
                                'bottom' => '0',
                                'left' => '3',
                                'unit' => 'px',
                                'isLinked' => false,
                            ],
                        ],
                        'color' => ['default' => '#5bc0de'],
                    ],
                    'typography_fields' => [
                        'font_weight' => ['default' => '600'],
                    ],
                ],
            ];

            $tabs_config = [];

            foreach ($state_controls as $state_key => $config) {
                $tabs_config['tabs_' . $state_key] = [
                    'label' => $config['label'],
                    'callback' => function () use ($config, $state_key) {
                        $this->controls->text_color('navigation_' . $state_key . '_color', [
                            'label' => esc_html__('Text Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                $config['selectors']['text'] => 'color: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'navigation_' . $state_key . '_typography',
                            'label' => esc_html__('Typography', 'addons-for-elementor-builder'),
                            'selector' => $config['selectors']['typography'],
                            'fields_options' => $config['typography_fields'] ?? [],
                        ]);

                        $this->controls->background([
                            'name' => 'navigation_' . $state_key . '_background',
                            'label' => esc_html__('Background', 'addons-for-elementor-builder'),
                            'types' => ['classic', 'gradient'],
                            'selector' => $config['selectors']['background'],
                        ]);

                        $this->controls->border([
                            'name' => 'navigation_' . $state_key . '_border',
                            'label' => esc_html__('Border', 'addons-for-elementor-builder'),
                            'selector' => $config['selectors']['border'],
                            'fields_options' => $config['border_fields_options'],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'navigation_' . $state_key . '_box_shadow',
                            'label' => esc_html__('Box Shadow', 'addons-for-elementor-builder'),
                            'selector' => $config['selectors']['box_shadow'],
                        ]);
                    },
                ];
            }

            $this->controls->tabs('navigation_items_tab', $tabs_config);

            // Item Spacing
            $this->controls->slider('navigation_item_spacing', [
                'label' => esc_html__('Item Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-MyAccount-navigation li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]);

            // Item Padding
            $this->controls->dimensions('navigation_item_padding', [
                'label' => esc_html__('Item Padding', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-MyAccount-navigation ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            // Content Area
            $this->controls->dimensions('content_padding', [
                'label' => esc_html__('Content Padding', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-MyAccount-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            // Horizontal Layout Specific
            $this->controls->heading('horizontal_layout_heading', [
                'label' => esc_html__('Horizontal Layout', 'addons-for-elementor-builder'),
                'condition' => ['tabs_layout' => 'horizontal'],
            ]);

            $this->controls->slider('horizontal_navigation_spacing', [
                'label' => esc_html__('Item Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}}.afeb-my-account-tabs-horizontal .woocommerce-MyAccount-navigation ul' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['tabs_layout' => 'horizontal'],
            ]);

            $this->controls->slider('horizontal_navigation_bottom_margin', [
                'label' => esc_html__('Bottom Margin', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 100],
                ],
                'selectors' => [
                    '{{WRAPPER}}.afeb-my-account-tabs-horizontal .woocommerce-MyAccount-navigation' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => ['tabs_layout' => 'horizontal'],
            ]);

            $this->controls->border([
                'name' => 'horizontal_active_border',
                'label' => esc_html__('Active Border', 'addons-for-elementor-builder'),
                'selector' => '{{WRAPPER}}.afeb-my-account-tabs-horizontal .woocommerce-MyAccount-navigation li.is-active a',
                'fields_options' => [
                    'border' => [
                        'default' => 'bottom',
                    ],
                    'width' => [
                        'default' => [
                            'top' => '0',
                            'right' => '0',
                            'bottom' => '3',
                            'left' => '0',
                            'unit' => 'px',
                            'isLinked' => false,
                        ],
                    ],
                ],
                'condition' => ['tabs_layout' => 'horizontal'],
            ]);
        });

        // Form Styles Section
        $this->controls->tab_style_section('forms_style_section', [
            'label' => esc_html__('Forms', 'addons-for-elementor-builder'),
        ], function() {
            // Form rows
            $this->controls->slider('form_row_spacing', [
                'name' => 'form_row_spacing',
                'label' => esc_html__('Row Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 50]],
                'selectors' => [
                    '{{WRAPPER}} .woocommerce-EditAccountForm .woocommerce-form-row,
             {{WRAPPER}} .woocommerce-address-fields .woocommerce-form-row' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]);

            // Labels
            $this->controls->typography_important([
                'name' => 'form_label_typography',
                'label' => esc_html__('Label Typography', 'addons-for-elementor-builder'),
                'selector' => '{{WRAPPER}} .woocommerce-form label,
          {{WRAPPER}} .woocommerce-address-fields label,
          {{WRAPPER}} .woocommerce-EditAccountForm label' ,
            'responsive' => true
            ]);

            // Labels
            $this->controls->typography_important([
                'name' => 'form_input_typography',
                'label' => esc_html__('Input Typography', 'addons-for-elementor-builder'),
                'selector' => '{{WRAPPER}} .woocommerce-form input.input-text,
          {{WRAPPER}} .woocommerce-form select,
          {{WRAPPER}} .woocommerce-form textarea,
          {{WRAPPER}} .woocommerce-address-fields input.input-text,
          {{WRAPPER}} .woocommerce-address-fields select,
          {{WRAPPER}} .woocommerce-address-fields textarea,
          {{WRAPPER}} .woocommerce-EditAccountForm input.input-text,
          {{WRAPPER}} .woocommerce-EditAccountForm select,
          {{WRAPPER}} .woocommerce-EditAccountForm textarea',
                'responsive' => true
            ]);
        });


        // Buttons Styles - Full Implementation
        $this->controls->tab_style_section('buttons_style_section', [
            'label' => esc_html__('Buttons', 'addons-for-elementor-builder'),
        ], function () {
            // Initialize tabs
            $this->controls->tabs('buttons_tabs', [
                'buttons_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->text_color('buttons_text_color', [
                            'label' => esc_html__('Text Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .woocommerce-Button,
                         {{WRAPPER}} .button,
                         {{WRAPPER}} .woocommerce-button,
                         {{WRAPPER}} .shop_table .button,
                         {{WRAPPER}} .order-again .button' => 'color: {{VALUE}} !important;',
                            ],
                        ]);

                        $this->controls->background([
                            'name' => 'buttons_background',
                            'label' => esc_html__('Background', 'addons-for-elementor-builder'),
                            'types' => ['classic', 'gradient'],
                            'selector' => '{{WRAPPER}} .woocommerce-Button,
                                  {{WRAPPER}} .button,
                                  {{WRAPPER}} .woocommerce-button,
                                  {{WRAPPER}} .shop_table .button,
                                  {{WRAPPER}} .order-again .button',
                        ]);

                        $this->controls->border_radius('buttons_border_radius', [
                            'label' => esc_html__('Border Radius', 'addons-for-elementor-builder'),
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .woocommerce-Button,
                         {{WRAPPER}} .button,
                         {{WRAPPER}} .woocommerce-button,
                         {{WRAPPER}} .shop_table .button,
                         {{WRAPPER}} .order-again .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                            ],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'buttons_box_shadow',
                            'label' => esc_html__('Box Shadow', 'addons-for-elementor-builder'),
                            'selector' => '{{WRAPPER}} .woocommerce-Button,
                                  {{WRAPPER}} .button,
                                  {{WRAPPER}} .woocommerce-button,
                                  {{WRAPPER}} .shop_table .button,
                                  {{WRAPPER}} .order-again .button',
                        ]);
                    },
                ],
                'buttons_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->text_color('buttons_hover_text_color', [
                            'label' => esc_html__('Text Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .woocommerce-Button:hover,
                         {{WRAPPER}} .button:hover,
                         {{WRAPPER}} .woocommerce-button:hover,
                         {{WRAPPER}} .shop_table .button:hover,
                         {{WRAPPER}} .order-again .button:hover' => 'color: {{VALUE}} !important;',
                            ],
                        ]);

                        $this->controls->background([
                            'name' => 'buttons_hover_background',
                            'label' => esc_html__('Background', 'addons-for-elementor-builder'),
                            'types' => ['classic', 'gradient'],
                            'selector' => '{{WRAPPER}} .woocommerce-Button:hover,
                                  {{WRAPPER}} .button:hover,
                                  {{WRAPPER}} .woocommerce-button:hover,
                                  {{WRAPPER}} .shop_table .button:hover,
                                  {{WRAPPER}} .order-again .button:hover',
                        ]);

                        $this->controls->border_radius('buttons_hover_border_radius', [
                            'label' => esc_html__('Border Radius', 'addons-for-elementor-builder'),
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .woocommerce-Button:hover,
                         {{WRAPPER}} .button:hover,
                         {{WRAPPER}} .woocommerce-button:hover,
                         {{WRAPPER}} .shop_table .button:hover,
                         {{WRAPPER}} .order-again .button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                            ],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'buttons_hover_box_shadow',
                            'label' => esc_html__('Box Shadow', 'addons-for-elementor-builder'),
                            'selector' => '{{WRAPPER}} .woocommerce-Button:hover,
                                  {{WRAPPER}} .button:hover,
                                  {{WRAPPER}} .woocommerce-button:hover,
                                  {{WRAPPER}} .shop_table .button:hover,
                                  {{WRAPPER}} .order-again .button:hover',
                        ]);

                        $this->controls->slider('buttons_transition_duration', [
                            'label' => esc_html__('Transition Duration', 'addons-for-elementor-builder') . ' (ms)',
                            'range' => ['px' => ['min' => 0, 'max' => 3000, 'step' => 100]],
                            'selectors' => [
                                '{{WRAPPER}} .woocommerce-Button,
                         {{WRAPPER}} .button,
                         {{WRAPPER}} .woocommerce-button,
                         {{WRAPPER}} .shop_table .button,
                         {{WRAPPER}} .order-again .button' => 'transition-duration: {{SIZE}}ms;',
                            ],
                        ]);
                    },
                ],
            ]);
        });

        do_action('afeb/widget/content/after_render_content_section', $this);
    }

    private function get_core_account_endpoints(): array
    {
        return [
            'dashboard' => esc_html__('Dashboard', 'addons-for-elementor-builder'),
            'orders' => esc_html__('Orders', 'addons-for-elementor-builder'),
            'downloads' => esc_html__('Downloads', 'addons-for-elementor-builder'),
            'edit-address' => esc_html__('Addresses', 'addons-for-elementor-builder'),
            'edit-account' => esc_html__('Account Details', 'addons-for-elementor-builder'),
            'payment-methods' => esc_html__('Payment Methods', 'addons-for-elementor-builder'),
            'customer-logout' => esc_html__('Logout', 'addons-for-elementor-builder'),
        ];
    }

    public function modify_menu_items($items, $endpoints) {
        $settings = $this->get_settings_for_display();

        if (!empty($settings['tabs'])) {
            foreach ($settings['tabs'] as $tab) {
                if (isset($tab['field_key']) && isset($tab['tab_name']) && isset($items[$tab['field_key']])) {
                    $items[$tab['field_key']] = $tab['tab_name'];
                }
            }
        }

        return $items;
    }


    public function woocommerce_get_myaccount_page_permalink($bool)
    {
        return get_permalink();
    }

    public function woocommerce_logout_default_redirect_url($redirect)
    {
        return $redirect . '?elementor_wc_logout=true&elementor_my_account_redirect=' . esc_url(get_permalink());
    }

    protected function render()
    {
        // Force WooCommerce account page context
        add_filter('woocommerce_prevent_admin_access', '__return_false');
        add_filter('woocommerce_is_account_page', '__return_true');

        $is_editor = $this->helper->is_edit_mode();

        // In editor & logged out: pretend to be an admin for preview data
        if ($is_editor && !is_user_logged_in()) {
            $admins = get_users(['role' => 'administrator', 'number' => 1]);
            if (!empty($admins)) {
                wp_set_current_user($admins[0]->ID);
            }
        }

        // Customize Woo menu items
        add_filter('woocommerce_account_menu_items', [$this, 'modify_menu_items'], 20, 2);

        echo '<div class="afeb-my-account-tab" data-editor-mode="'.($is_editor ? 'true' : 'false').'">';

        /**
         * Mirror WooCommerce shortcode hooks so notices/extensions still run.
         */
        do_action('woocommerce_before_my_account');

        if ($is_editor) {
            /**
             * EDITOR MODE: simulate full WooCommerce wrapper
             */
            echo '<div class="woocommerce">';
            // Navigation
            do_action('woocommerce_account_navigation');

            // Content wrapper
            echo '<div class="woocommerce-MyAccount-content" data-endpoint="dashboard">';

            // Load WC + Elementor assets
            if (did_action('elementor/loaded') && class_exists(Plugin::class)) {
                Plugin::$instance->frontend->enqueue_styles();
                Plugin::$instance->frontend->enqueue_scripts();
            }
            if (function_exists('wc_enqueue_js')) {
                wc_enqueue_js('jQuery(document.body).trigger("wc_fragment_refresh");');
            }

            // Dashboard content only
            $this->render_html_editor();

            echo '</div></div>'; // close content + wc wrap
            echo '</div>';       // close main wrap

            do_action('woocommerce_after_my_account');

            // Cleanup
            remove_filter('woocommerce_account_menu_items', [$this, 'modify_menu_items'], 20);
            remove_filter('woocommerce_is_account_page', '__return_true');
            return;
        }

        /**
         * FRONTEND MODE: normal WooCommerce handling
         */
        $current_endpoint = $this->get_current_endpoint();

        echo '<div class="woocommerce">';
        do_action('woocommerce_account_navigation');
        echo '<div class="woocommerce-MyAccount-content" data-endpoint="' . esc_attr($current_endpoint) . '">';

        $template_id = $this->get_tab_template_id($current_endpoint);

        if ($template_id > 0) {
            if (did_action('elementor/loaded') && class_exists(Plugin::class)) {
                Plugin::$instance->frontend->enqueue_styles();
                Plugin::$instance->frontend->enqueue_scripts();
            }

            $this->display_custom_template($current_endpoint);
        } elseif ($current_endpoint === 'dashboard') {
            wc_get_template('myaccount/dashboard.php', [
                'current_user' => wp_get_current_user(),
            ]);
        } else {
            do_action('woocommerce_account_content');
        }

        echo '</div></div></div>';

        do_action('woocommerce_after_my_account');

        if (function_exists('wc_enqueue_js')) {
            wc_enqueue_js('
            jQuery(document.body).trigger("wc_fragment_refresh");
            if (typeof jQuery.fn.select2 === "function") jQuery("select").select2();
            if (typeof jQuery.fn.validate === "function") jQuery(".woocommerce-EditAccountForm").validate();
        ');
        }

        // Cleanup filters
        remove_filter('woocommerce_account_menu_items', [$this, 'modify_menu_items'], 20);
        remove_filter('woocommerce_is_account_page', '__return_true');
    }





    private function add_hooks()
    {
        add_action('woocommerce_account_navigation', [$this, 'woocommerce_account_navigation'], 1);
        add_filter('woocommerce_account_menu_items', [$this, 'modify_menu_items'], 10, 2);
        add_action('woocommerce_account_content', [$this, 'before_account_content'], 2);
        add_action('woocommerce_account_content', [$this, 'after_account_content'], 95);
        add_filter('woocommerce_get_myaccount_page_permalink', [$this, 'woocommerce_get_myaccount_page_permalink'], 10, 1);
        add_filter('woocommerce_logout_default_redirect_url', [$this, 'woocommerce_logout_default_redirect_url'], 10, 1);
        add_filter('woocommerce_is_account_page', '__return_true');
    }

    private function remove_hooks()
    {
        remove_action('woocommerce_account_navigation', [$this, 'woocommerce_account_navigation'], 2);
        remove_action('woocommerce_account_menu_items', [$this, 'modify_menu_items'], 10);
        remove_action('woocommerce_account_content', [$this, 'before_account_content'], 5);
        remove_action('woocommerce_account_content', [$this, 'after_account_content'], 99);
        remove_filter('woocommerce_get_myaccount_page_permalink', [$this, 'woocommerce_get_myaccount_page_permalink'], 10, 1);
        remove_filter('woocommerce_logout_default_redirect_url', [$this, 'woocommerce_logout_default_redirect_url'], 10, 1);
        remove_filter('woocommerce_is_account_page', '__return_true');
    }

    private function get_account_pages()
    {
        $pages = [
            'dashboard'   => '',
            'orders'      => '',
            'downloads'   => '',
            'edit-address' => '',
        ];

        $has_payment_methods = array_filter(
            WC()->payment_gateways->get_available_payment_gateways(),
            function ($gateway) {
                return $gateway->supports('add_payment_method') || $gateway->supports('tokenization');
            }
        );

        if (!empty($has_payment_methods)) {
            $pages['payment-methods'] = '';
            $pages['add-payment-method'] = '';
        }

        $pages['edit-account'] = '';

        $recent_order = wc_get_orders([
            'limit'   => 1,
            'orderby' => 'date',
            'order'   => 'DESC',
        ]);

        if (!empty($recent_order)) {
            $pages['view-order'] = $recent_order[0]->get_id();
        }

        return $pages;
    }

    private function get_current_endpoint()
    {
        if (function_exists('WC') && isset(WC()->query)) {
            $endpoint = WC()->query->get_current_endpoint();

            if (!empty($endpoint)) {
                return sanitize_title($endpoint);
            }
        }

        global $wp_query;

        if ($wp_query instanceof \WP_Query) {
            foreach (array_keys($this->get_account_pages()) as $page) {
                if (!empty($wp_query->query[$page])) {
                    return $page;
                }
            }
        }

        return 'dashboard';
    }

    private function render_html_front_end()
    {
        $current_endpoint = $this->get_current_endpoint();
        $custom_dashboard_class = '';

        if ('dashboard' === $current_endpoint && $this->has_custom_template() && is_user_logged_in()) {
            $custom_dashboard_class = 'afeb-my-account-tab-dashboard-custom';
        }

        $classes = 'afeb-my-account-tab afeb-my-account-tab-' . sanitize_html_class($current_endpoint) . ' ' . $custom_dashboard_class;

        echo '<div class="' . esc_attr($classes) . '">';
        echo do_shortcode('[woocommerce_my_account]');
        echo '</div>';
    }

    private function render_html_editor()
    {
        $settings = $this->get_settings_for_display();

        // Decide whether to show custom Elementor dashboard or default WC dashboard
        if ($this->has_custom_template() && is_user_logged_in()) {
            ob_start();
            $this->display_custom_template();
            echo ob_get_clean();
        } else {
            ob_start();
            wc_get_template('myaccount/dashboard.php', [
                'current_user' => wp_get_current_user(),
            ]);
            echo ob_get_clean();
        }
    }


    /**
     * Return dummy HTML markup for editor preview of each endpoint
     */
    private function get_dummy_endpoint_html($endpoint): string
    {
        switch ($endpoint) {
            case 'orders':
                return '<h2>' . esc_html__('My Orders', 'addons-for-elementor-builder') . '</h2>
                <table class="shop_table order_list">
                    <thead><tr><th>Order</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>
                    <tbody>
                        <tr><td>#1234</td><td>Jan 1, 2025</td><td>Completed</td><td>$99.00</td></tr>
                        <tr><td>#1235</td><td>Jan 5, 2025</td><td>Processing</td><td>$149.00</td></tr>
                    </tbody>
                </table>';

            case 'view-order':
                return '<h2>' . esc_html__('Order #1234', 'addons-for-elementor-builder') . '</h2>
                <p>Status: Completed</p>
                <table class="shop_table order_details">
                    <thead><tr><th>Product</th><th>Total</th></tr></thead>
                    <tbody><tr><td>Sample Product</td><td>$99.00</td></tr></tbody>
                </table>';

            case 'edit-address':
                return '<h2>' . esc_html__('Edit Address', 'addons-for-elementor-builder') . '</h2>
                <form>
                    <p><label>Street Address</label><input type="text" placeholder="123 Preview St" /></p>
                    <p><label>City</label><input type="text" placeholder="Preview City" /></p>
                    <button type="button" class="button">' . esc_html__('Save', 'addons-for-elementor-builder') . '</button>
                </form>';

            case 'edit-account':
                return '<h2>' . esc_html__('Edit Account', 'addons-for-elementor-builder') . '</h2>
                <form>
                    <p><label>Name</label><input type="text" value="John Doe" /></p>
                    <p><label>Email</label><input type="email" value="john@example.com" /></p>
                    <button type="button" class="button">' . esc_html__('Save', 'addons-for-elementor-builder') . '</button>
                </form>';

            case 'downloads':
                return '<h2>' . esc_html__('My Downloads', 'addons-for-elementor-builder') . '</h2>
                <p>No downloads available yet.</p>';

            case 'payment-methods':
                return '<h2>' . esc_html__('Payment Methods', 'addons-for-elementor-builder') . '</h2>
                <p>No payment methods saved.</p>';

            default:
                return '<p class="afeb-placeholder">' . esc_html__('Preview placeholder for:', 'addons-for-elementor-builder') . ' ' . esc_html($endpoint) . '</p>';
        }
    }

    public function woocommerce_account_navigation()
    {
        $settings = $this->get_settings_for_display();

        if ($settings['tabs_layout'] === 'horizontal') {
            ob_start();
            wc_get_template('myaccount/navigation.php');
            $nav_html = ob_get_clean();

            echo '<div class="afeb-wc-account-tabs-nav">' . $nav_html . '</div>';
        }
    }

    private function get_tab_template_id(string $endpoint): int
    {
        $settings = $this->get_settings_for_display();
        $endpoint_slug = sanitize_title($endpoint);

        if (!empty($settings['tabs']) && is_array($settings['tabs'])) {
            foreach ($settings['tabs'] as $tab) {
                if (empty($tab['field_key'])) {
                    continue;
                }

                $tab_endpoint = sanitize_title($tab['field_key']);

                if ($endpoint_slug !== $tab_endpoint) {
                    continue;
                }

                if (isset($tab['customize_content']) && 'yes' === $tab['customize_content'] && !empty($tab['custom_template'])) {
                    return max(0, intval($tab['custom_template']));
                }
            }
        }

        return $this->get_legacy_template_id($endpoint_slug, $settings);
    }

    private function get_legacy_template_id(string $endpoint_slug, array $settings): int
    {
        $template_id = 0;

        $core_endpoints = $this->get_core_account_endpoints();
        if (isset($core_endpoints[$endpoint_slug]))
        {
            $setting_key = 'custom_template_' . $endpoint_slug;

            if (!empty($settings[$setting_key])) $template_id = intval($settings[$setting_key]);
        }

        if (0 === $template_id && !empty($settings['custom_endpoint_templates']) && is_array($settings['custom_endpoint_templates']))
        {
            foreach ($settings['custom_endpoint_templates'] as $custom_endpoint)
            {
                if (empty($custom_endpoint['endpoint'])) continue;

                $custom_endpoint_slug = sanitize_title($custom_endpoint['endpoint']);

                if ($endpoint_slug !== $custom_endpoint_slug) continue;

                if (!empty($custom_endpoint['template_id'])) {
                    $template_id = intval($custom_endpoint['template_id']);
                    break;
                }
            }
        }

        if (0 === $template_id && 'dashboard' === $endpoint_slug && !empty($settings['customize_dashboard_select']))
        {
            $template_id = intval($settings['customize_dashboard_select']);
        }

        return max(0, $template_id);
    }

    public function has_custom_template(string $endpoint = 'dashboard'): bool
    {
        return 0 < $this->get_tab_template_id($endpoint);
    }

    private function get_account_content_wrapper($args)
    {
        $user_id = get_current_user_id();
        $num_orders = wc_get_customer_order_count($user_id);
        $num_downloads = count(wc_get_customer_available_downloads($user_id));
        $class = 'woocommerce-MyAccount-content-wrapper';

        $no_orders = ($num_orders === 0);
        $no_downloads = ($num_downloads === 0);

        if ($args['context'] === 'frontend') {
            global $wp_query;
            $is_orders_endpoint = isset($wp_query->query_vars['orders']);
            $is_downloads_endpoint = isset($wp_query->query_vars['downloads']);

            if (($no_orders && $is_orders_endpoint) || ($no_downloads && $is_downloads_endpoint)) {
                $class .= '-no-data';
            }
        } else {
            $is_orders_page = ($args['page'] === 'orders');
            $is_downloads_page = ($args['page'] === 'downloads');

            if (($no_orders && $is_orders_page) || ($no_downloads && $is_downloads_page)) {
                $class .= '-no-data';
            }
        }

        return $class;
    }

    public function before_account_content()
    {
        $wrapper_class = $this->get_account_content_wrapper(['context' => 'frontend']);
        echo '<div class="' . esc_attr(sanitize_html_class($wrapper_class)) . '">';
    }

    public function get_dashboard_template_id()
    {
        return $this->get_tab_template_id('dashboard');
    }


    public function display_custom_template(string $endpoint = 'dashboard')
    {
        $template_id = $this->get_tab_template_id($endpoint);

        if ($template_id > 0) {
            echo do_shortcode('[elementor-template id="' . esc_attr($template_id) . '"]');

            if ('dashboard' === $endpoint) {
                do_action('woocommerce_account_dashboard');
            }

            do_action('afeb/woo_my_account/after_custom_template', $endpoint, $template_id);
        }
    }

    public function after_account_content()
    {
        echo '</div>';
    }

    public function get_group_name()
    {
        return 'woocommerce';
    }

protected function render_html_unified() {
    $settings = $this->get_settings_for_display();
    $menu_items = function_exists('wc_get_account_menu_items')
        ? wc_get_account_menu_items()
        : [];

    if (empty($menu_items) && $this->helper->is_edit_mode()) {
        // Force standard endpoints list for editor
        $menu_items = [
            'dashboard'       => __('Dashboard', 'woocommerce'),
            'orders'          => __('Orders', 'woocommerce'),
            'downloads'       => __('Downloads', 'woocommerce'),
            'edit-address'    => __('Addresses', 'woocommerce'),
            'edit-account'    => __('Account details', 'woocommerce'),
            'payment-methods' => __('Payment methods', 'woocommerce'),
            'customer-logout' => __('Logout', 'woocommerce'),
        ];
    }

    echo '<div class="afeb-my-account-tab">';
    echo '<div class="woocommerce">';
    echo '<nav class="woocommerce-MyAccount-navigation">';
    echo '<ul>';
    foreach ($menu_items as $endpoint => $label) {
        $url = wc_get_account_endpoint_url($endpoint);
        printf(
            '<li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--%1$s"><a href="%2$s">%3$s</a></li>',
            esc_attr($endpoint),
            esc_url($url),
            esc_html($label)
        );
    }
    echo '</ul>';
    echo '</nav>';

    echo '<div class="woocommerce-MyAccount-content-wrapper">';
    foreach ($menu_items as $endpoint => $label) {
        $classes = [
            'woocommerce-MyAccount-content',
            'afeb-my-account-content-' . $endpoint
        ];
        echo '<div class="' . esc_attr(implode(' ', $classes)) . '">';

        if ($this->helper->is_edit_mode()) {
            // In editor: show dummy content unless it's safe GET endpoint
            echo $this->get_dummy_endpoint_html($endpoint);
        } else {
            // Live WooCommerce rendering
            wc_get_template( 'myaccount/' . $endpoint . '.php' );
        }

        echo '</div>';
    }
    echo '</div>'; // .woocommerce-MyAccount-content-wrapper
    echo '</div>'; // .woocommerce
    echo '</div>'; // .afeb-my-account-tab
}
}
add_filter( 'woocommerce_account_endpoint_url', function( $url, $endpoint, $value, $permalink ) {
    if ( 'customer-logout' === $endpoint ) {
        return $url;
    }

    $base_permalink = $permalink ? $permalink : wc_get_page_permalink( 'myaccount' );

    return wc_get_endpoint_url( $endpoint, $value, $base_permalink );
}, 10, 4 );
