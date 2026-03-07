<?php

namespace AFEB\Controls;

use AFEB\Helper as AFEBHelper;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

/**
 * "Vertex Addons for Elementor" Controls Helper Class
 *
 * @version 1.0.0
 */
class Helper
{
    /**
     * @var Widget_Base
     */
    private $widget;
    private $responsive = false;

    public function __construct($widget)
    {
        $this->widget = $widget;
    }

    public function responsive(): Helper
    {
        $this->responsive = true;
        return $this;
    }

    public static function get($widget): Helper
    {
        return (new Helper($widget));
    }

    private function merge(array $default, array $args): array
    {
        return array_merge($default, $args);
    }

    private function add(string $id, array $args)
    {
        // Sanitize selectors globally to prevent G.match crashes
        if (isset($args['selectors'])) {
            $args['selectors'] = $this->sanitize_selectors($args['selectors']);
        }

        // Add Responsive Control
        if ($this->responsive) {
            $this->widget->add_responsive_control($id, $args);
            $this->responsive = false;
            return;
        }

        // Regular Control
        $this->widget->add_control($id, $args);
    }


    private function get_dummy_endpoint_html($endpoint)
    {
        switch ($endpoint) {
            case 'dashboard':
                return '<h2>Dashboard</h2><p>Welcome to your account preview.</p>';
            case 'orders':
                return '<h2>My Orders</h2><table><tr><th>Order</th><th>Date</th><th>Status</th></tr><tr><td>#1234</td><td>Jan 1, 2025</td><td>Completed</td></tr></table>';
            case 'view-order':
                return '<h2>Order #1234</h2><p>Status: Completed</p>';
            case 'edit-address':
                return '<h2>Edit Address</h2><p>Address form preview...</p>';
            case 'edit-account':
                return '<h2>Edit Account</h2><p>Account details form preview...</p>';
            case 'downloads':
                return '<h2>Downloads</h2><p>No downloads available in preview.</p>';
            case 'payment-methods':
                return '<h2>Payment Methods</h2><p>No payment methods saved in preview.</p>';
            default:
                return '<p>Preview for ' . esc_html($endpoint) . '</p>';
        }
    }

    public function typography_important(array $args) {
        if (!isset($args['name'])) {
            throw new \InvalidArgumentException('Name is required for typography_important');
        }
        if (!isset($args['selector'])) {
            throw new \InvalidArgumentException('Selector is required for typography_important');
        }

        $default_options = [
            'typography' => ['selector' => $args['selector']],
            'font_family' => [
                'selectors' => [
                    $args['selector'] => 'font-family: {{VALUE}} !important;'
                ]
            ],
            'font_size' => [
                'selectors' => [
                    $args['selector'] => 'font-size: {{SIZE}}{{UNIT}} !important;'
                ]
            ],
            'font_weight' => [
                'selectors' => [
                    $args['selector'] => 'font-weight: {{VALUE}} !important;'
                ]
            ],
            'text_transform' => [
                'selectors' => [
                    $args['selector'] => 'text-transform: {{VALUE}} !important;'
                ]
            ],
            'font_style' => [
                'selectors' => [
                    $args['selector'] => 'font-style: {{VALUE}} !important;'
                ]
            ],
            'text_decoration' => [
                'selectors' => [
                    $args['selector'] => 'text-decoration: {{VALUE}} !important;'
                ]
            ],
            'line_height' => [
                'selectors' => [
                    $args['selector'] => 'line-height: {{SIZE}}{{UNIT}} !important;'
                ]
            ],
            'letter_spacing' => [
                'selectors' => [
                    $args['selector'] => 'letter-spacing: {{SIZE}}{{UNIT}} !important;'
                ]
            ],
            'word_spacing' => [
                'selectors' => [
                    $args['selector'] => 'word-spacing: {{SIZE}}{{UNIT}} !important;'
                ]
            ]
        ];

        // Merge with any existing fields_options
        if (isset($args['fields_options'])) {
            $args['fields_options'] = array_merge($default_options, $args['fields_options']);
        } else {
            $args['fields_options'] = $default_options;
        }

        // Ensure the base selector is set
        if (!isset($args['selector'])) {
            $args['selector'] = '{{WRAPPER}}';
        }

        return $this->typography($args);
    }

    public function section(string $id, array $args, $callback = null)
    {
        $this->widget->start_controls_section($id, $args);

        $callback && $callback();

        $this->widget->end_controls_section();
    }
    public function get_default_tab_names() {
        return [
            'dashboard' => esc_html__('Dashboard', 'addons-for-elementor-builder-pro'),
            'orders' => esc_html__('Orders', 'addons-for-elementor-builder-pro'),
            'downloads' => esc_html__('Downloads', 'addons-for-elementor-builder-pro'),
            'edit-address' => esc_html__('Addresses', 'addons-for-elementor-builder-pro'),
            'edit-account' => esc_html__('Account Details', 'addons-for-elementor-builder-pro'),
            'customer-logout' => esc_html__('Logout', 'addons-for-elementor-builder-pro')
        ];
    }

    public function tab_advanced_section(string $id, array $args, $callback = null)
    {
        $args['tab'] = Controls_Manager::TAB_ADVANCED;
        $this->section($id, $args, $callback);
    }

    public function tab_content_section(string $id, array $args, $callback = null)
    {
        $args['tab'] = Controls_Manager::TAB_CONTENT;
        $this->section($id, $args, $callback);
    }

    public function tab_settings_section(string $id, array $args, $callback = null)
    {
        $args['tab'] = Controls_Manager::TAB_SETTINGS;
        $this->section($id, $args, $callback);
    }

    public function tab_style_section(string $id, array $args, $callback = null)
    {
        $args['tab'] = Controls_Manager::TAB_STYLE;
        $this->section($id, $args, $callback);
    }

    public function tabs(string $id, array $tabs)
    {
        $this->widget->start_controls_tabs($id);

        foreach ($tabs as $tab_id => $tab) {
            $this->widget->start_controls_tab($tab_id, $tab);

            isset($tab['callback']) && is_callable($tab['callback']) && $tab['callback']();

            $this->widget->end_controls_tab();
        }

        $this->widget->end_controls_tabs();
    }

    public function control(string $id, array $args)
    {
        $this->add($id, $args);
    }

    public function select(string $id, array $args)
    {
        $default = [
            'type' => Controls_Manager::SELECT,
            'label_block' => false,
        ];

        if (!empty($args['meta_options'])) {
            $name = str_replace('afeb_', '', $args['meta_options'][0]);
            $default['options'] = AFEBHelper::fhook($args['meta_options'][1], "afeb/$name/controls/options/$id");
            unset($args['meta_options']);
        }

        $this->add($id, $this->merge($default, $args));
    }

    public function select2(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::SELECT2,
            'label_block' => true,
        ], $args));
    }

    /**
     * Displays a dynamic select box control
     *
     * @since 1.3.0
     *
     * @param string $id
     * @param array $args
     */
    public function dynamic_select(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => 'afeb_dynamic_select',
            'label_block' => true,
        ], $args));
    }

    public function url(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'label' => esc_html__('Link', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::URL,
            'default' => [
                'nofollow' => false,
                'is_external' => true,
            ],
            'dynamic' => [
                'active' => true,
            ],
            'placeholder' => esc_html__('https://your-link.com', 'addons-for-elementor-builder'),
            'label_block' => true,
        ], $args));
    }

    public function choose(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::CHOOSE,
            'toggle' => false,
        ], $args));
    }

    /**
     * Elementor popover toggle control
     *
     * @since 1.5.0
     *
     * @param string $id
     * @param array $args
     * @param object $callback
     */
    public function popover_toggle(string $id, array $args, $callback = null)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::POPOVER_TOGGLE,
        ], $args));

        $this->widget->start_popover();

        isset($callback) && is_callable($callback) && $callback();

        $this->widget->end_popover();
    }

    public function slider(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
            'default' => [
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ],
                'px' => [
                    'min' => 1,
                    'max' => 1000,
                ],
                'vw' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
        ], $args));
    }

    public function dimensions(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem', 'custom'],
        ], $args));
    }

    public function hover_animation(string $id, array $args = [])
    {
        $this->add($id, $this->merge([
            'label' => esc_html__('Hover Animation', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::HOVER_ANIMATION,
        ], $args));
    }

    public function color(string $id, array $args = [])
    {
        $this->add($id, $this->merge([
            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
        ], $args));
    }

    public function divider(string $id, array $args = [])
    {
        $this->widget->add_control($id, $this->merge([
            'type' => Controls_Manager::DIVIDER,
            'style' => 'thick',
        ], $args));
    }

    public function media(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'label' => esc_html__('Image', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::MEDIA,
            'default' => ['url' => Utils::get_placeholder_image_src()],
        ], $args));
    }

    public function image_size(array $args)
    {
        $this->widget->add_group_control(Group_Control_Image_Size::get_type(), $this->merge([
            'default' => 'full',
            'exclude' => ['custom'],
            'separator' => '',
        ], $args));
    }

    public function background(array $args)
    {
        $this->widget->add_group_control(Group_Control_Background::get_type(), $this->merge([
            'name' => 'background',
            'selector' => '{{WRAPPER}}',
        ], $args));
    }

    public function border(array $args)
    {
        // Auto‑unique if not provided
        if (empty($args['name'])) {
            static $i = 0;
            $i++;
            $args['name'] = 'border_' . $i;
        }

        $this->widget->add_group_control(Group_Control_Border::get_type(), $this->merge([
            'selector' => '{{WRAPPER}}',
            'separator' => 'before',
        ], $args));
    }

    public function button(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::BUTTON,
            'label_block' => true,
            'show_label' => false,
        ], $args));
    }

    public function padding(string $id, array $args = [])
    {
        $args['label'] = esc_html__('Padding', 'addons-for-elementor-builder');
        $this->dimensions($id, $args);
    }

    public function margin(string $id, array $args = [])
    {
        $args['label'] = esc_html__('Margin', 'addons-for-elementor-builder');
        $this->dimensions($id, $args);
    }

    public function border_radius(string $id, array $args = [])
    {
        $args['label'] = esc_html__('Border Radius', 'addons-for-elementor-builder');
        $this->dimensions($id, $args);
    }

    public function css_filters(array $args)
    {
        $this->widget->add_group_control(Group_Control_Css_Filter::get_type(), $this->merge([
            'name' => 'css_filters',
            'selector' => '{{WRAPPER}} img',
        ], $args));
    }

    public function box_shadow(array $args)
    {
        $this->widget->add_group_control(Group_Control_Box_Shadow::get_type(), $this->merge([
            'name' => 'box_shadow',
            'selector' => '{{WRAPPER}}',
        ], $args));
    }

    public function text_shadow(array $args)
    {
        $this->widget->add_group_control(Group_Control_Text_Shadow::get_type(), $this->merge([
            'name' => 'text_shadow',
            'selector' => '{{WRAPPER}}',
        ], $args));
    }

    public function text_stroke(array $args)
    {
        $this->widget->add_group_control(Group_Control_Text_Stroke::get_type(), $this->merge([
            'name' => 'text_stroke',
            'selector' => '{{WRAPPER}}',
        ], $args));
    }

    public function typography(array $args)
    {
        if (empty($args['name'])) {
            static $i = 0;
            $i++;
            $args['name'] = 'typography_' . $i;
        }

        $this->widget->add_group_control(Group_Control_Typography::get_type(), $this->merge([
            'selector' => '{{WRAPPER}}',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT,
            ],
        ], $args));
    }

    public function alignment(string $id, array $args = [])
    {
        $options = [
            'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-left',],
            'center' => ['title' => esc_html__('Center', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-center',],
            'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-right',],
        ];

        if (!empty($args['justify'])) {
            $options['justify'] = [
                'title' => esc_html__('Justified', 'addons-for-elementor-builder'),
                'icon' => 'eicon-text-align-justify',
            ];
        }

        $this->choose($id, $this->merge([
            'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
            'options' => $options,
            'selectors' => [
                '{{WRAPPER}}' => 'text-align: {{VALUE}};',
            ],
        ], $args));
    }

    public function image_fit(string $id, array $args = [])
    {
        $this->select($id, $this->merge([
            'label' => esc_html__('Image Fit', 'addons-for-elementor-builder'),
            'options' => [
                '' => esc_html__('Default', 'addons-for-elementor-builder'),
                'fill' => esc_html__('Fill', 'addons-for-elementor-builder'),
                'cover' => esc_html__('Cover', 'addons-for-elementor-builder'),
                'contain' => esc_html__('Contain', 'addons-for-elementor-builder'),
                'scale-down' => esc_html__('Scale Down', 'addons-for-elementor-builder'),
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} img' => 'object-fit: {{VALUE}};',
            ],
        ], $args));
    }

    public function opacity(string $id, array $args = [])
    {
        $this->slider($id, $this->merge([
            'label' => esc_html__('Opacity', 'addons-for-elementor-builder'),
            'size_units' => ['px'],
            'default' => ['unit' => 'px'],
            'tablet_default' => ['unit' => 'px'],
            'mobile_default' => ['unit' => 'px'],
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}' => 'opacity: {{SIZE}};',
            ],
        ], $args));
    }

    public function duration(string $id, array $args = [])
    {
        $this->slider($id, $this->merge([
            'label' => esc_html__('Duration (s)', 'addons-for-elementor-builder'),
            'size_units' => ['px'],
            'default' => ['unit' => 'px'],
            'tablet_default' => ['unit' => 'px'],
            'mobile_default' => ['unit' => 'px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 3,
                    'step' => 0.1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}}' => 'transition-duration: {{SIZE}}s',
            ],
        ], $args));
    }

    public function text_color(string $id, array $args = [])
    {
        $this->color($id, $this->merge([
            'label' => esc_html__('Text Color', 'addons-for-elementor-builder'),
        ], $args));
    }

    public function background_color(string $id, array $args = [])
    {
        $this->color($id, $this->merge([
            'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
        ], $args));
    }

    /**
     * Displays an HTML content in the panel
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function raw_html(string $id, $raw, array $args = [])
    {
        // If $raw is an array, merge directly
        if (is_array($raw)) {
            $this->add($id, $this->merge([
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ], $raw));
            return;
        }

        // If $raw is a string, wrap it in array format
        $this->add($id, $this->merge([
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => $raw,
        ], $args));
    }

    /**
     * Allows you to build repeatable blocks of fields
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function repeater(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::REPEATER,
            'label' => esc_html__('Items', 'addons-for-elementor-builder')
        ], $args));
    }

    /**
     * Elementor switcher control displays an on/off
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function switcher(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::SWITCHER,
        ], $args));
    }

    /**
     * Elementor switcher control displays an show/hide
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function sh_switcher(string $id, array $args)
    {
        $args['label_on'] = esc_html__('Show', 'addons-for-elementor-builder');
        $args['label_off'] = esc_html__('Hide', 'addons-for-elementor-builder');

        $this->Switcher($id, $args);
    }

    /**
     * Elementor switcher control displays an yes/no
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function yn_switcher(string $id, array $args)
    {
        $args['label_on'] = esc_html__('Yes', 'addons-for-elementor-builder');
        $args['label_off'] = esc_html__('No', 'addons-for-elementor-builder');

        $this->Switcher($id, $args);
    }

    /**
     * Displays a simple text input field
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function text(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
        ], $args));
    }

    /**
     * Displays a simple text area input field
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function text_area(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 3,
            'dynamic' => [
                'active' => true,
            ],
        ], $args));
    }

    /**
     * Displays a code editor input field
     *
     * @since 1.5.0
     *
     * @param string $id
     * @param array $args
     */
    public function code(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::CODE,
            'language' => 'css',
            'render_type' => 'ui',
        ], $args));
    }

    /**
     * Displays a simple WYSIWYG input field
     *
     * @since 1.5.0
     *
     * @param string $id
     * @param array $args
     */
    public function wysiwyg(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::WYSIWYG,
            'label' => esc_html__('Content', 'addons-for-elementor-builder'),
            'dynamic' => [
                'active' => true,
            ],
        ], $args));
    }

    /**
     * Displays a simple number input field
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function number(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'step' => 1,
        ], $args));
    }

    /**
     * Displays the icons chooser
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function icons(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::ICONS,
            'skin' => 'inline',
            'label_block' => false
        ], $args));
    }

    /**
     * Adds a hidden input field to the panel
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function hidden(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::HIDDEN
        ], $args));
    }

    /**
     * Displays a text heading between controls in the panel
     *
     * @since 1.0.1
     *
     * @param string $id
     * @param array $args
     */
    public function heading(string $id, array $args)
    {
        $this->add($id, $this->merge([
            'type' => Controls_Manager::HEADING
        ], $args));
    }
    /**
     * Sanitize Elementor selectors array to prevent JS errors (e.g. G.match issue).
     *
     * @param array|string $selectors
     * @return array|string
     */
    private function sanitize_selectors($selectors) {
        if (is_array($selectors)) {
            $clean = [];
            foreach ($selectors as $key => $value) {
                if (is_string($key) && trim($key) !== '') {
                    $clean[$key] = $value;
                } else {
                    // Optional: log or debug
                    error_log('AFEBP: Removed bad selector key => ' . var_export($key, true));
                }
            }
            return $clean;
        }
        return $selectors; // May be empty string or null, that's fine
    }

    // --- START: Compatibility aliases for legacy CHelper used by FormBuilder ---
    public function add_cnt_sctn(
        $widget,
        $id = '',
        $label = '',
        $call_back = null,
        $options = [],
        $condition = [],
        $conditions = [],
        $tab = ''
    ) {
        $args = [
            'label' => $label,
            'tab' => empty($tab) ? \Elementor\Controls_Manager::TAB_CONTENT : $tab,
            'condition' => $condition,
            'conditions' => $conditions,
        ];
        $this->section($id, $args, function() use ($call_back, $widget, $options) {
            if ($call_back !== null && is_callable($call_back)) {
                $call_back($widget, $options);
            }
        });
    }

    public function add_stl_sctn(
        $widget,
        $id = '',
        $label = '',
        $call_back = null,
        $options = [],
        $condition = [],
        $conditions = []
    ) {
        $args = [
            'label' => $label,
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => $condition,
            'conditions' => $conditions,
        ];
        $this->section($id, $args, function() use ($call_back, $widget, $options) {
            if ($call_back !== null && is_callable($call_back)) {
                $call_back($widget, $options);
            }
        });
    }

    public function add_adv_sctn(
        $widget,
        $id = '',
        $label = '',
        $call_back = null,
        $options = [],
        $condition = [],
        $conditions = []
    ) {
        $args = [
            'label' => $label,
            'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
            'condition' => $condition,
            'conditions' => $conditions,
        ];
        $this->section($id, $args, function() use ($call_back, $widget, $options) {
            if ($call_back !== null && is_callable($call_back)) {
                $call_back($widget, $options);
            }
        });
    }

    public function add_set_sctn(
        $widget,
        $id = '',
        $label = '',
        $call_back = null,
        $options = [],
        $condition = [],
        $conditions = []
    ) {
        $args = [
            'label' => $label,
            'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
            'condition' => $condition,
            'conditions' => $conditions,
        ];
        $this->section($id, $args, function() use ($call_back, $widget, $options) {
            if ($call_back !== null && is_callable($call_back)) {
                $call_back($widget, $options);
            }
        });
    }

    public function dvdr($widget, $style = 'thick')
    {
        $this->divider('divider_' . wp_rand(), ['style' => $style]);
    }

    public function hed($widget, $label = '')
    {
        $this->heading('heading_' . wp_rand(), ['label' => $label]);
    }

    public function hddn($widget, $id, $args = [])
    {
        $this->hidden($id, $args);
    }


    public function dtm_pckr($widget, $id = '', $label = '', $default = '')
    {
        $this->control($id, [
            'label' => !empty($label) ? $label : __('Date / Time', 'addons-for-elementor-builder'),
            'type' => \Elementor\Controls_Manager::DATE_TIME,
            'default' => $default
        ]);
    }

    public function anim($widget, $id = 'animation', $args = [])
    {
        $this->control($id, $this->merge([
            'label' => !empty($args['label']) ? $args['label'] : esc_html__('Entrance Animation', 'addons-for-elementor-builder'),
            'type' => \Elementor\Controls_Manager::ANIMATION,
            'frontend_available' => isset($args['frontend_available']) ? (bool)$args['frontend_available'] : false,
        ], $args));
    }

    public function h_anim($widget, $id = 'hover_animation', $args = [])
    {
        $this->control($id, $this->merge([
            'label' => !empty($args['label']) ? $args['label'] : esc_html__('Hover Animation', 'addons-for-elementor-builder'),
            'type' => 'afeb_hover_animation',
            'label_block' => true,
        ], $args));
    }
// --- END: Compatibility aliases ---

}
