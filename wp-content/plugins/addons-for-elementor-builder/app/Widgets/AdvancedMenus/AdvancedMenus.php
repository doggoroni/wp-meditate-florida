<?php

namespace AFEB\Widgets\AdvancedMenus;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use AFEB\NavMenus\AdvancedMenuWalker;
use AFEB\Widgets\AdvancedMenus\Helper as MenuHelper;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" AdvancedMenus Widget Class
 * 
 * @class AdvancedMenus
 * @version 1.3.0
 */
class AdvancedMenus extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var Helper
     */
    private $Helper;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * @var MenuHelper
     */
    public $menu_helper;

    /**
     * AdvancedMenus Constructor
     * 
     * @since 1.3.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->Helper = new Helper();
        $this->controls = new CHelper($this);
        $this->menu_helper = new MenuHelper();
        $this->assets->advanced_menus_style();
        $this->assets->smart_menus_pkg_script();
        $this->assets->advanced_menus_script();
    }

    /**
     * Get widget name
     *
     * @since 1.3.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_advanced_menus';
    }

    /**
     * Get widget title
     *
     * @since 1.3.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html('Advanced Menus', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.3.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-advanced-menus';
    }

    /**
     * Get widget categories
     *
     * @since 1.3.0
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['theme-elements'];
    }

    /**
     * Get widget keywords
     *
     * @since 1.3.0
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
     * @since 1.3.0
     *
     * @return array Widget style dependencies
     */
    public function get_style_depends(): array
    {
        return ['afeb-advanced-menus-style'];
    }

    /**
     * Retrieve the list of script dependencies the widget requires
     *
     * @since 1.3.0
     *
     * @return array Widget script dependencies
     */
    public function get_script_depends()
    {
        return ['afeb-smart-menus-pkg-script', 'afeb-advanced-menus-script'];
    }

    /**
     * Register AdvancedMenus widget controls
     *
     * @since 1.3.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('advanced_menus_content_section', [
            'label' => esc_html__('Advanced Menus', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->select('menu', [
                'label' => esc_html__('Select Menu', 'addons-for-elementor-builder'),
                'options' => $this->Helper->get_nav_menus(),
                'default' => 'default',
                'save_default' => true,
                'description' => sprintf(
                    /* translators: 1: Link opening tag, 2: Link closing tag. */
                    esc_html__('Go to the %1$sAppearance > Menus%2$s to manage your menus.', 'addons-for-elementor-builder'),
                    sprintf('<a href="%s" target="_blank">', admin_url('nav-menus.php')),
                    '</a>'
                ),
            ]);

            $this->controls->select('layout', [
                'label' => esc_html__('Mode', 'addons-for-elementor-builder'),
                'options' => [
                    'horizontal' => esc_html__('Horizontal Menu', 'addons-for-elementor-builder'),
                    'vertical' => esc_html__('Vertical Menu', 'addons-for-elementor-builder'),
                    'dropdown' => esc_html__('Toggle Menu', 'addons-for-elementor-builder'),
                ],
                'default' => 'horizontal',
                'frontend_available' => true,
                'separator' => 'before',
                'condition' => ['menu!' => 'default',]
            ]);

            $this->controls->responsive()->alignment('align_items', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-layout-vertical>ul>li>a,' .
                        '{{WRAPPER}} .afeb-advanced-menu-layout-horizontal' => 'justify-content: {{VALUE}};',
                ],
                'condition' => ['menu!' => 'default', 'layout!' => 'dropdown',]
            ]);

            $this->controls->select('pointer', [
                'label' => esc_html__('Pointer', 'addons-for-elementor-builder'),
                'options' => [
                    'none' => esc_html('None', 'addons-for-elementor-builder'),
                    'underline' => esc_html('Underline', 'addons-for-elementor-builder'),
                    'overline' => esc_html('Overline', 'addons-for-elementor-builder'),
                    'doubleline' => esc_html('Double Line', 'addons-for-elementor-builder')
                ],
                'default' => 'underline',
                'condition' => ['menu!' => 'default', 'layout!' => 'dropdown',],
            ]);

            $icon_prefix = Icons_Manager::is_migration_allowed() ? 'fas ' : 'fa ';
            $this->controls->icons('submenu_icon', [
                'label' => esc_html__('Submenu Indicator', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'default' => ['value' => $icon_prefix . 'fa-caret-down', 'library' => 'fa-solid',],
                'recommended' => ['fa-solid' => ['chevron-down', 'angle-down', 'caret-down', 'plus',],],
                'exclude_inline_options' => ['svg'],
                'frontend_available' => true,
                'condition' => ['menu!' => 'default',],
            ]);
        });
        /**
         *
         * Toggle Menu
         *
         */
        $this->controls->tab_content_section('toggle_menu_content_section', [
            'label' => esc_html__('Toggle Menu', 'addons-for-elementor-builder'),
            'condition' => ['menu!' => 'default',]
        ], function () {

            $this->controls->select('dropdown', [
                'label' => esc_html__('Breakpoint', 'addons-for-elementor-builder'),
                'options' => [
                    'mobile' => esc_html__('Mobile Portrait (> 767px)', 'addons-for-elementor-builder'),
                    'tablet' => esc_html__('Tablet Portrait (> 1024px)', 'addons-for-elementor-builder'),
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                ],
                'default' => 'tablet',
                'prefix_class' => 'afeb-advanced-menu-dropdown-',
                'condition' => ['layout!' => 'dropdown',],
            ]);

            $this->controls->select('width', [
                'label' => esc_html__('Width', 'addons-for-elementor-builder'),
                'options' => [
                    'default' => esc_html__('Default', 'addons-for-elementor-builder'),
                    'fit-to-section' => esc_html__('Fit to Section', 'addons-for-elementor-builder'),
                    'full-width' => esc_html__('Full Width', 'addons-for-elementor-builder'),
                ],
                'default' => 'full-width',
                'prefix_class' => 'afeb-advanced-menu-',
                'frontend_available' => true,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->select('toggle', [
                'label' => esc_html__('Toggle Button', 'addons-for-elementor-builder'),
                'options' => [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    'burger' => esc_html__('Hamburger', 'addons-for-elementor-builder'),
                ],
                'default' => 'burger',
                'prefix_class' => 'afeb-advanced-menu-toggle-container elementor-nav-menu--',
                'render_type' => 'template',
                'frontend_available' => true,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                            ],
                        ],
                    ],
                ],
            ]);

            /**
             * Tabs
             */
            $this->controls->tabs('toggle_icon', [
                'normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'relation' => 'and',
                                'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                            ],
                            [
                                'relation' => 'and',
                                'terms' => [
                                    ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                    ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                                ],
                            ],
                        ],
                    ],
                    'callback' => function () {

                        $this->controls->icons('toggle_icon_normal', [
                            'fa4compatibility' => 'icon',
                            'skin_settings' => [
                                'inline' => [
                                    'none' => [
                                        'label' => esc_html__('Default', 'addons-for-elementor-builder'),
                                        'icon' => 'eicon-menu-bar',
                                    ],
                                    'icon' => ['icon' => 'eicon-star',],
                                ],
                            ],
                            'recommended' => [
                                'fa-solid' => ['plus-square', 'plus', 'plus-circle', 'bars',],
                                'fa-regular' => ['plus-square',],
                            ],
                            'conditions' => [
                                'relation' => 'or',
                                'terms' => [
                                    [
                                        'relation' => 'and',
                                        'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                                    ],
                                    [
                                        'relation' => 'and',
                                        'terms' => [
                                            ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                            ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                                        ],
                                    ],
                                ],
                            ],
                        ]);
                    },
                ],
                'hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'relation' => 'and',
                                'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                            ],
                            [
                                'relation' => 'and',
                                'terms' => [
                                    ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                    ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                                ],
                            ],
                        ],
                    ],
                    'callback' => function () {

                        $this->controls->hover_animation('toggle_icon_hover_animation', [
                            'frontend_available' => true,
                            'render_type' => 'template',
                            'conditions' => [
                                'relation' => 'or',
                                'terms' => [
                                    [
                                        'relation' => 'and',
                                        'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                                    ],
                                    [
                                        'relation' => 'and',
                                        'terms' => [
                                            ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                            ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                                        ],
                                    ],
                                ],
                            ],
                        ]);
                    },
                ],
                'active' => [
                    'label' => esc_html__('Active', 'addons-for-elementor-builder'),
                    'conditions' => [
                        'relation' => 'or',
                        'terms' => [
                            [
                                'relation' => 'and',
                                'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                            ],
                            [
                                'relation' => 'and',
                                'terms' => [
                                    ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                    ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                                ],
                            ],
                        ],
                    ],
                    'callback' => function () {

                        $this->controls->icons('toggle_icon_active', [
                            'fa4compatibility' => 'icon',
                            'skin_settings' => [
                                'inline' => [
                                    'none' => ['label' => esc_html__('Default', 'addons-for-elementor-builder'), 'icon' => 'eicon-close',],
                                    'icon' => ['icon' => 'eicon-star',],
                                ],
                            ],
                            'recommended' => [
                                'fa-solid' => ['window-close', 'times-circle', 'times', 'minus-square', 'minus-circle', 'minus',],
                                'fa-regular' => ['window-close', 'times-circle', 'minus-square',],
                            ],
                            'conditions' => [
                                'relation' => 'or',
                                'terms' => [
                                    [
                                        'relation' => 'and',
                                        'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                                    ],
                                    [
                                        'relation' => 'and',
                                        'terms' => [
                                            ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                            ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                                        ],
                                    ],
                                ],
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->alignment('toggle_align', [
                'default' => 'center',
                'selectors_dictionary' => [
                    'left' => 'margin: 0px auto 0px 0px',
                    'center' => 'margin: 0px auto',
                    'right' => 'margin: 0px 0px 0px auto',
                ],
                'separator' => 'before',
                'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle' => '{{VALUE}}',],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [['name' => 'layout', 'operator' => '==', 'value' => 'dropdown',],],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                ['name' => 'layout', 'operator' => '!=', 'value' => 'dropdown',],
                                ['name' => 'dropdown', 'operator' => '!=', 'value' => 'none',],
                            ],
                        ],
                    ],
                ],
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Main Menu Styles
         *
         */
        $this->controls->tab_style_section('main_menu_style_section', [
            'label' => esc_html__('Main Menu', 'addons-for-elementor-builder'),
            'condition' => ['layout!' => 'dropdown',],
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('menu_tab_style', [
                'menu_item_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'menu_item_bg',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu>li>a',
                        ]);

                        $this->controls->text_color('menu_item_color', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu>li>a' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);
                    },
                ],
                'menu_item_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'menu_item_bg_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu>li>a:hover',
                        ]);

                        $this->controls->text_color('menu_item_color_hover', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu>li>a:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->text_color('menu_item_pointer_color_hover', [
                            'label' => esc_html__('Pointer Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-underline-pointer>ul>li>a:before,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-overline-pointer>ul>li>a::after,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-doubleline-pointer>ul>li>a:before,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-doubleline-pointer>ul>li>a::after' => 'background-color: {{VALUE}};',
                            ],
                        ]);
                    },
                ],
                'menu_item_active' => [
                    'label' => esc_html__('Active', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'menu_item_bg_active',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu>li:not(li.current-menu-parent)>a.current',
                        ]);

                        $this->controls->text_color('menu_item_color_active', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu>li:not(li.current-menu-parent)>a.current' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('separator_menu_style_controls');

            $this->controls->typography([
                'name' => 'menu_typography',
                'selector' => '{{WRAPPER}} .afeb-advanced-menu>li>a',
            ]);

            $this->controls->responsive()->slider('menu_item_icon_gap', [
                'label' => esc_html__('Menu Icon Gap (px)', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu>li>a>span>i' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->margin('menu_item_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu>li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('menu_item_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu>li>a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'menu_item_border',
                'selector' => '{{WRAPPER}} .afeb-advanced-menu>li>a',
            ]);

            $this->controls->responsive()->border_radius('menu_item_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu>li>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        });
        /**
         *
         * Dropdown Styles
         *
         */
        $this->controls->tab_style_section('dropdown_style_section', [
            'label' => esc_html__('Sub Menus', 'addons-for-elementor-builder'),
            'condition' => ['layout!' => 'dropdown',],
        ], function () {

            $this->controls->heading('heading_dropdown', [
                'label' => esc_html__('Box', 'addons-for-elementor-builder')
            ]);

            $this->controls->background([
                'name' => 'dropdown_bg',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul,' .
                    '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul,' .
                    '{{WRAPPER}} .afeb-advanced-menu-dropdown.afeb-advanced-menu-container',
            ]);

            /*$this->controls->responsive()->margin('dropdown_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul,' .
                        '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);*/

            $this->controls->responsive()->padding('dropdown_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul,' .
                        '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'dropdown_border',
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul',
            ]);

            $this->controls->responsive()->border_radius('dropdown_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul li:first-child a' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};',
                    '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul li:last-child a' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .afeb-advanced-menu-dropdown.afeb-advanced-menu-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ],
            ]);

            $this->controls->heading('heading_dropdown_item', [
                'label' => esc_html__('Items', 'addons-for-elementor-builder')
            ]);

            /**
             * Tabs
             */
            $this->controls->tabs('dropdown_tab_style', [
                'dropdown_item_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'dropdown_item_bg',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li,' .
                                '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul>li',
                        ]);

                        $this->controls->text_color('dropdown_item_color', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li>a,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul>li>a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'dropdown_item_border',
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul li,' .
                                '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul li',
                        ]);

                        $this->controls->responsive()->border_radius('dropdown_item_border_radius', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul li,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
                'dropdown_item_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'dropdown_item_bg_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li:hover>a,' .
                                '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul>li:hover>a',
                        ]);

                        $this->controls->text_color('dropdown_item_color_hover', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li:hover>a,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul>li:hover>a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'dropdown_item_border_hover',
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li:hover>a,' .
                                '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul>li:hover>a',
                        ]);

                        $this->controls->responsive()->border_radius('dropdown_item_border_radius_hover', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li:hover>a,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul>li:hover>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
                'dropdown_item_active' => [
                    'label' => esc_html__('Active', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'dropdown_item_bg_active',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li:not(li.current-menu-parent) a.current,' .
                                '{{WRAPPER}} .afeb-advanced-menu-dropdown a.current',
                        ]);

                        $this->controls->text_color('dropdown_item_color_active', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>lili:not(li.current-menu-parent) a.current' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-advanced-menu-dropdown a.current' => 'color: {{VALUE}}'
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'dropdown_item_border_active',
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li a.current,' .
                                '{{WRAPPER}} .afeb-advanced-menu-dropdown a.current',
                        ]);

                        $this->controls->responsive()->border_radius('dropdown_item_border_radius_active', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul>li a.current,' .
                                    '{{WRAPPER}} .afeb-advanced-menu-dropdown a.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('separator_dropdown_style_controls');

            $this->controls->typography([
                'name' => 'dropdown_typography',
                'exclude' => ['line_height'],
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul li,' .
                    '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul li',
            ]);

            $this->controls->responsive()->margin('dropdown_item_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul li,' .
                        '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('dropdown_item_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul li a,' .
                        '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'dropdown_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-main .afeb-advanced-menu ul,' .
                    '{{WRAPPER}} .afeb-advanced-menu-dropdown .afeb-advanced-menu ul',
            ]);
        });
        /**
         *
         * Toggle Button Styles
         *
         */
        $this->controls->tab_style_section('toggle_button_style_section', [
            'label' => esc_html__('Toggle Button', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('toggle_button_tab_style', [
                'toggle_button_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_button_bg',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle',
                        ]);

                        $this->controls->text_color('toggle_button_color', [
                            'label' => esc_html__('Content Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-toggle' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-advanced-menu-toggle svg' => 'fill: {{VALUE}}',
                            ],
                        ]);
                    },
                ],
                'toggle_button_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_button_bg_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle:hover',
                        ]);

                        $this->controls->text_color('toggle_button_color_hover', [
                            'label' => esc_html__('Content Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-advanced-menu-toggle:hover' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-advanced-menu-toggle:hover svg' => 'fill: {{VALUE}}',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('separator_toggle_button_style_controls');

            $this->controls->responsive()->padding('toggle_button_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'toggle_button_border',
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle',
            ]);

            $this->controls->responsive()->border_radius('toggle_button_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->box_shadow([
                'name' => 'toggle_button_box_shadow',
                'exclude' => ['box_shadow_position',],
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle',
            ]);
        });
        /**
         *
         * Toggle Menu Styles
         *
         */
        $this->controls->tab_style_section('toggle_menu_style_section', [
            'label' => esc_html__('Toggle Menu', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('toggle_menu_tab_style', [
                'toggle_menu_item_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_menu_item_bg',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li>a'
                        ]);

                        $this->controls->text_color('toggle_menu_item_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li>a' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',],
                        ]);
                    },
                ],
                'toggle_menu_item_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_menu_item_bg_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li>a:hover',
                        ]);

                        $this->controls->text_color('toggle_menu_item_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li>a:hover' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',],
                        ]);
                    },
                ],
                'toggle_menu_item_active' => [
                    'label' => esc_html__('Active', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_menu_item_bg_active',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li:not(li.current-menu-parent)>a.current'
                        ]);

                        $this->controls->text_color('toggle_menu_item_color_active', [
                            'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li:not(li.current-menu-parent)>a.current' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('toggle_menu_style_controls');

            $this->controls->typography([
                'name' => 'toggle_menu_typography',
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li.afeb-advanced-menu-item>a'
            ]);

            $this->controls->responsive()->padding('toggle_menu_item_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu>li>a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]);

            $this->controls->responsive()->border_radius('toggle_menu_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'
                ],
            ]);
        });
        /**
         *
         * Toggle Sub Menu Styles
         *
         */
        $this->controls->tab_style_section('toggle_sub_menu_style_section', [
            'label' => esc_html__('Toggle Sub Menus', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('toggle_sub_menu_tab_style', [
                'toggle_sub_menu_item_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_sub_menu_item_bg',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul>li',
                        ]);

                        $this->controls->text_color('toggle_sub_menu_item_color', [
                            'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul>li>a' => 'color: {{VALUE}}; fill: {{VALUE}};',],
                        ]);
                    },
                ],
                'toggle_sub_menu_item_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_sub_menu_item_bg_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul>li:hover>a',
                        ]);

                        $this->controls->text_color('toggle_sub_menu_item_color_hover', [
                            'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul>li:hover>a' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',],
                        ]);
                    },
                ],
                'toggle_sub_menu_item_active' => [
                    'label' => esc_html__('Active', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'toggle_sub_menu_item_bg_active',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul>li a.current[href^="http"]',
                        ]);

                        $this->controls->text_color('toggle_sub_menu_item_color_active', [
                            'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul>li a.current[href^="http"]' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('toggle_sub_menu_style_controls');

            $this->controls->typography([
                'name' => 'toggle_sub_menu_typography',
                'selector' => '{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul li',
                'fields_options' => [
                    'text_decoration' => ['selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul li a' => 'text-decoration: {{VALUE}};',],],
                ],
            ]);

            $this->controls->responsive()->padding('toggle_sub_menu_item_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-advanced-menu-toggle-dropdown .afeb-advanced-menu ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',],
            ]);
        });
    }

    /**
     * Render AdvancedMenus widget output on the frontend
     *
     * @since 1.3.0
     */
    protected function render()
    {
        $settings = $this->get_active_settings();
        $menu = !empty($settings['menu']) ? wp_get_nav_menu_object($settings['menu']) : false;

        if (!$menu) {
            echo wp_kses(
                Helper::front_notice(
                    esc_html__('Please select a Menu From Setting!', 'addons-for-elementor-builder'),
                    'error'
                ),
                Helper::allowed_tags(['div'])
            );
            return;
        };

        $wid = $this->get_id();
        $menu_icon = !empty($settings['submenu_icon']['value']) ?
            '_' . str_replace(' ', '_', esc_attr($settings['submenu_icon']['value'])) : '';
        $args = [
            'echo' => false,
            'menu' => $settings['menu'],
            'menu_class' => "afeb-advanced-menu {$menu_icon}",
            'menu_id' => 'menu-' . $this->menu_helper->get_nav_menu_index() . '-' . $wid,
            'fallback_cb' => '__return_empty_string',
            'container' => '',
            'walker' => new AdvancedMenuWalker,
        ];

        if ($settings['layout'] === 'vertical') {
            $args['menu_class'] .= ' sm-vertical';
        }

        $menu_html = wp_nav_menu($args);
        $args['menu_id'] = 'menu-' . $this->menu_helper->get_nav_menu_index() . '-' . $wid;
        $args['menu_type'] = 'dropdown';
        $dropdown_menu_html = wp_nav_menu($args);

        if (empty($menu_html)) {
            echo wp_kses(
                Helper::front_notice(
                    esc_html__('Unfortunately, there is no item to display', 'addons-for-elementor-builder'),
                    'error'
                ),
                Helper::allowed_tags(['div'])
            );
            return;
        }

        if ($settings['layout'] != 'dropdown'):

            $this->add_render_attribute('main-menu', 'class', [
                'afeb-advanced-menu-main',
                'afeb-advanced-menu-container',
                'afeb-advanced-menu-layout-' . $settings['layout'],
            ]);

            if (!empty($settings['pointer'])):
                $pointer = esc_attr($settings['pointer']);
                $this->add_render_attribute('main-menu', 'class', "afeb-advanced-menu-{$pointer}-pointer");
            endif;
?>
            <nav <?php $this->print_render_attribute_string('main-menu'); ?>
                style="opacity: 0;position: fixed;z-index: -99999">
                <?php echo $menu_html; ?>
            </nav>
        <?php
        endif;

        $this->menu_helper->render_toggle_menu($this);
        ?>
        <nav class="afeb-advanced-menu-dropdown afeb-advanced-menu-toggle-dropdown afeb-advanced-menu-container"
            style="opacity: 0;position: fixed;z-index: -99999" aria-hidden="true">
            <?php echo $dropdown_menu_html; ?>
        </nav>
<?php
    }
}
