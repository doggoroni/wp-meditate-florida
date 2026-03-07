<?php

namespace AFEB\Extensions\VisibilityControls;

use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use Elementor\Core\Base\Module;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" VisibilityControls Extension Class
 * 
 * @class VisibilityControls
 * @version 1.5.0
 */
class VisibilityControls extends Module
{
    public function __construct()
    {
        parent::__construct();
        $this->add_actions();
        $this->register_conditions();
    }

    protected function add_actions()
    {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_section']);

        add_action('elementor/element/common/afeb-ext-visibility-controls/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/element/section/afeb-ext-visibility-controls/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/element/container/afeb-ext-visibility-controls/before_section_end', [$this, 'register_controls'], 10, 2);

        add_action('elementor/frontend/section/should_render', [$this, 'schedule_before_render'], 10, 2);
        add_filter('elementor/frontend/widget/should_render', [$this, 'schedule_before_render'], 10, 2);
        add_action('elementor/frontend/container/should_render', [$this, 'schedule_before_render'], 10, 2);
        add_action('elementor/document/save/data', [$this, 'disable_elementor_cache_for_widgets'], 10, 2);
    }

    public function get_name()
    {
        return 'afeb-visibility-controls';
    }

    public function register_section($element)
    {
        $controls = new CHelper($element);
        $controls->tab_advanced_section('afeb-ext-visibility-controls', [
            'label' => esc_html__('Visibility Controls', 'addons-for-elementor-builder'),
        ]);
    }

    protected $conditions = [];
    protected $repeater_controls;

    const USER_GROUP = 'user';
    const POST_GROUP = 'post';

    public function get_groups()
    {
        return [
            self::USER_GROUP => ['label' => esc_html__('User', 'addons-for-elementor-builder'),],
            self::POST_GROUP => ['label' => esc_html__('Post', 'addons-for-elementor-builder'),],
        ];
    }

    public function register_conditions()
    {
        $included_conditions = [
            'authentication',
            'user',
            'role',
            'post',
            'page',
        ];

        foreach ($included_conditions as $condition_name) {

            $class_name = ucwords($condition_name);
            $class_name = __NAMESPACE__ . '\\Conditions\\' . $class_name;

            if (class_exists($class_name)) {
                if ($class_name::is_supported()) {
                    $this->conditions[$condition_name] = $class_name::instance();
                }
            }
        }
    }

    public function get_conditions($condition_name = null)
    {
        if ($condition_name) {
            if (isset($this->conditions[$condition_name])) {
                return $this->conditions[$condition_name];
            }

            return null;
        }

        return $this->conditions;
    }

    protected function set_conditions($id, $conditions = [])
    {
        if (!$conditions) {
            return;
        }

        foreach ($conditions as $index => $condition) {

            $key = '';
            $key =  $condition['afeb_condition_key'];
            $relation = $condition['afeb_condition_operator'];
            $val = $condition['afeb_condition_' . $key . '_value'];
            $custom_page_id = $condition['afeb_condition_custom_page_id'];

            $get_conditions = $this->get_conditions($key);

            if (!$get_conditions) {
                continue;
            }

            $get_conditions->set_element_id($id);

            $check = $get_conditions->check($relation, $val, $custom_page_id);

            $this->conditions[$id][$key . '_' . $condition['_id']] = $check;
        }
    }

    public function register_controls($widget, $args)
    {
        $controls = new CHelper($widget);

        $controls->yn_switcher('afeb_display_conditions_enable', [
            'label' => esc_html__('Display Conditions', 'addons-for-elementor-builder'),
            'return_value' => 'yes',
            'frontend_available' => true,
        ]);

        $controls->select('afeb_display_conditions_action', [
            'label' => esc_html__('Action', 'addons-for-elementor-builder'),
            'options' => [
                'show' => esc_html__('Show', 'addons-for-elementor-builder'),
                'hide' => esc_html__('Hide', 'addons-for-elementor-builder'),
            ],
            'default' => 'show',
            'condition' => ['afeb_display_conditions_enable' => 'yes',],
        ]);

        $controls->select('afeb_display_conditions_relation', [
            'label' => esc_html__('When', 'addons-for-elementor-builder'),
            'options' => [
                'all' => esc_html__('All conditions met', 'addons-for-elementor-builder'),
                'any' => esc_html__('Any condition met', 'addons-for-elementor-builder'),
            ],
            'default' => 'all',
            'condition' => ['afeb_display_conditions_enable' => 'yes',],
        ]);

        $repeater = new Repeater();
        $this->repeater_controls = new CHelper($repeater);

        $this->repeater_controls->select('afeb_condition_key', [
            'label' => esc_html__('Condition', 'addons-for-elementor-builder'),
            'groups' => $this->get_conditions_options(),
            'default' => 'authentication',
        ]);

        $this->repeater_controls->select('afeb_condition_operator', [
            'label' => esc_html__('Operator', 'addons-for-elementor-builder'),
            'options' => [
                'is' => esc_html__('Is', 'addons-for-elementor-builder'),
                'not' => esc_html__('Is not', 'addons-for-elementor-builder'),
            ],
            'default' => 'is',
        ]);

        $this->add_value_controls();

        $this->repeater_controls->number('afeb_condition_custom_page_id', [
            'label' => esc_html__('Custom Page ID', 'addons-for-elementor-builder'),
            'default' => '',
            'condition' => ['afeb_condition_key' => 'page', 'afeb_condition_page_value' => 'custom',],
        ]);

        $controls->repeater('afeb_display_conditions', [
            'label' => esc_html__('Conditions', 'addons-for-elementor-builder'),
            'fields' => $repeater->get_controls(),
            'title_field' => 'Condition - <# print(afeb_condition_key.replace(/_/i, " ").split(" ").map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(" ")) #>',
            'prevent_empty' => false,
            'condition' => ['afeb_display_conditions_enable' => 'yes',],
        ]);
    }

    public function disable_elementor_cache_for_widgets($data)
    {
        if (is_array($data)) {
            $this->modify_elementor_settings($data,  function ($settings) {
                if (!empty($settings['afeb_display_conditions_enable'])) {
                    $settings['_element_cache'] = 'yes';
                }

                return $settings;
            });
        }

        return $data;
    }

    private function modify_elementor_settings(array &$data, ?callable $callback)
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                if (isset($value['settings']) || isset($value['elType'], $value['id'])) {
                    if (isset($value['settings'])) {
                        if (is_callable($callback)) {
                            $modified_settings = call_user_func($callback, $value['settings']);
                            if (is_array($modified_settings)) {
                                $value['settings'] = $modified_settings;
                            }
                        }
                    }
                }
            }
            if (is_array($value)) {

                // Recursively traverse nested arrays
                $this->modify_elementor_settings($value, $callback);
            }
        }
    }

    private function get_conditions_options()
    {

        $groups = $this->get_groups();

        foreach ($this->conditions as $condition) {
            $groups[$condition->get_group()]['options'][$condition->get_name()] = $condition->get_title();
        }

        return $groups;
    }

    private function add_value_controls()
    {
        if (!$this->conditions) {
            return;
        }

        foreach ($this->conditions as $condition) {

            $condition_name = $condition->get_name();
            $ctrl_key = 'afeb_condition_' . $condition_name . '_value';
            $ctrl_settings = $condition->get_control_settings();

            // Show this only if the user select this specific condition
            $ctrl_settings['condition'] = [
                'afeb_condition_key' => $condition_name,
            ];

            $condition->get_control($this->repeater_controls, $ctrl_key, $ctrl_settings);
        }
    }

    protected function is_visible($id, $relation)
    {
        if (!array_key_exists($id, $this->conditions)) {
            return false;
        }

        if (!Helper::is_edit_mode()) {
            if ($relation === 'any') {
                if (!in_array(true, $this->conditions[$id])) {
                    return false;
                }
            } else {
                if (in_array(false, $this->conditions[$id])) {
                    return false;
                }
            }
        }

        return true;
    }

    public function schedule_before_render($should_render, $widget)
    {
        $settings = $widget->get_settings();

        if (!empty($settings['afeb_display_conditions_enable'])) {
            $this->set_conditions($widget->get_id(), $settings['afeb_display_conditions']);

            $check_conditions = $this->is_visible($widget->get_id(), $settings['afeb_display_conditions_relation']);
            $action = $settings['afeb_display_conditions_action'];

            if (($action === 'show' && $check_conditions === true) || ($action === 'hide' && $check_conditions === false)) {
                $should_render = true;
            } else if (($action === 'show' && $check_conditions === false) || ($action === 'hide' && $check_conditions === true)) {
                $should_render = false;
            }
        }

        return $should_render;
    }
}
