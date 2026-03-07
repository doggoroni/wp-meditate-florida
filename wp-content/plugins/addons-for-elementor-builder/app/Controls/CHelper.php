<?php

namespace AFEB\Controls;

use AFEB\Helper;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * "Vertex Addons for Elementor" Controls Helper Class
 * 
 * @class CHelper
 * @version 1.0.1
 */
class CHelper extends Helper
{
	/**
	 * FILL Dimension
	 */
	const FILL_DIMENSION = ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}';

	/**
	 * FILL Margin
	 */
	const FILL_MARGIN = 'margin' . self::FILL_DIMENSION;

	/**
	 * FILL PADDING
	 */
	const FILL_PADDING = 'padding' . self::FILL_DIMENSION;

	/**
	 * FILL BORDER RADIUS
	 */
	const FILL_BR_RADIUS = 'border-radius' . self::FILL_DIMENSION;

	/**
	 * BASE DEFAULT SIZE UNIT
	 */
	const BDSU = ['px', '%'];

	/**
	 * Lorem Ipsum Message
	 */
	public static $LIM;

	/**
	 * @var array
	 */
	public $default_size_units;

	/**
	 * CHelper Constructor.
	 * 
	 * @since 1.0.1
	 */
	public function __construct()
	{
		self::$LIM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
		$this->default_size_units = array_merge(self::BDSU, ['em', 'rem', 'custom']);
	}

	/**
	 * Set a conditions for the element
	 * 
	 * @since 1.0.1
	 * 
	 * @param array $args
	 * @param array $conditions
	 */
	public function set_conditions(
		$args = [],
		$conditions = []
	) {
		if (is_array($conditions)) {
			if (isset($conditions['nstd'])) $args['conditions'] = $conditions['nstd'];
			else if (count($conditions) > 0) $args['condition'] = $conditions;
		}

		return $args;
	}

	/**
	 * Creates a new section for content tab
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param $call_back
	 * @param $options
	 * @param $condition
	 * @param $conditions
	 */
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
		$widget->start_controls_section($id, [
			'label' => $label,
			'tab' => empty($tab) ? Controls_Manager::TAB_CONTENT : $tab,
			'condition' => $condition,
			'conditions' => $conditions
		]);
		if ($call_back !== null) {
			$call_back($widget, $options);
		}
		$widget->end_controls_section();
	}

	/**
	 * Creates a new section for style tab
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param $call_back
	 * @param array $options
	 * @param array $condition
	 * @param array $conditions
	 */
	public function add_stl_sctn(
		$widget,
		$id = '',
		$label = '',
		$call_back = null,
		$options = [],
		$condition = [],
		$conditions = []
	) {
		$widget->start_controls_section(
			$id,
			[
				'label' => $label,
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
				'conditions' => $conditions
			]
		);
		if ($call_back !== null) {
			$call_back($widget, $options);
		}
		$widget->end_controls_section();
	}

	/**
	 * Creates a new section for advanced tab
	 * 
	 * @since 1.0.3
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param $call_back
	 * @param array $options
	 * @param array $condition
	 * @param array $conditions
	 */
	public function add_adv_sctn(
		$widget,
		$id = '',
		$label = '',
		$call_back = null,
		$options = [],
		$condition = [],
		$conditions = []
	) {
		$widget->start_controls_section($id, [
			'label' => $label,
			'tab' => Controls_Manager::TAB_ADVANCED,
			'condition' => $condition,
			'conditions' => $conditions
		]);
		if ($call_back !== null) {
			$call_back($widget, $options);
		}
		$widget->end_controls_section();
	}

	/**
	 * Creates a new section for elementor setting
	 * 
	 * @since 1.2.0
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param $call_back
	 * @param array $options
	 * @param array $condition
	 * @param array $conditions
	 */
	public function add_set_sctn(
		$widget,
		$id = '',
		$label = '',
		$call_back = null,
		$options = [],
		$condition = [],
		$conditions = []
	) {
		$widget->start_controls_section($id, [
			'label' => $label,
			'tab' => Controls_Manager::TAB_SETTINGS,
			'condition' => $condition,
			'conditions' => $conditions
		]);
		if ($call_back !== null) {
			$call_back($widget, $options);
		}
		$widget->end_controls_section();
	}

	/**
	 * Creates a new tab
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $label
	 * @param $call_back
	 * @param $options
	 */
	public function add_tb(
		$widget,
		$id = '',
		$label = '',
		$call_back = null,
		$options = [],
		$conditions = []
	) {
		$widget->start_controls_tab($id, [
			'label' => $label,
			'condition' => $conditions
		]);
		if ($call_back !== null) {
			$call_back($widget, $options);
		}
		$widget->end_controls_tab();
	}

	/**
	 * Displays a separator between controls
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $conditions
	 */
	public function dvdr(
		$widget,
		$id = '',
		$conditions = []
	) {
		$args = [];
		$args['type'] = Controls_Manager::DIVIDER;
		$widget->add_control($id . '_divider', $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a text heading between controls in the panel
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $conditions
	 */
	public function hed(
		$widget,
		$id = '',
		$label = '',
		$conditions = []
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::HEADING;
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Adds a hidden input field to the panel
	 * 
	 * @since 1.0.2
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 */
	public function hddn(
		$widget,
		$id = '',
		$label = '',
		$default = ''
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::HIDDEN;
		if (!empty($default)) {
			$args['default'] = $default;
		}
		$widget->add_control($id, $args);
	}

	/**
	 * Displays an HTML content in the panel
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $raw
	 * @param string $content_classes
	 * @param array $conditions
	 */
	public function raw_html(
		$widget,
		$id = '',
		$raw = '',
		$content_classes = '',
		$conditions = []
	) {
		$args = [];
		$args['type'] = Controls_Manager::RAW_HTML;
		if (!empty($raw)) {
			$args['raw'] = $raw;
		}
		if (!empty($content_classes)) {
			$args['content_classes'] = $content_classes;
		}

		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays an animation control
	 * 
	 * @since 1.2.0
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $default
	 * @param string $ad_conditions
	 * @param array $conditions
	 * @param array $more
	 */
	public function anim(
		$widget,
		$id = '',
		$default = '',
		$ad_conditions = '',
		$conditions = [],
		$more = []
	) {
		$args = [];
		$args['type'] = Controls_Manager::ANIMATION;
		if (!empty($default)) $args['default'] = $default;
		if (strpos($ad_conditions, 'lbc') !== false) {
			$args['label_block'] = true;
		}
		if (strpos($ad_conditions, 'frntnd') !== false) {
			$args['frontend_available'] = true;
		}
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a hover animation control
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $conditions
	 */
	public function h_anim(
		$widget,
		$id = '',
		$conditions = []
	) {
		$args = [];
		$args['type'] = Controls_Manager::HOVER_ANIMATION;
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Display a badget to upgrade to the professional version
	 * 
	 * @since 1.0.2
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param string $hook
	 * @param array $conditions
	 */
	public function pro(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$conditions = []
	) {
		if (!defined('AFEBP_LITE_VS')) {
			$args = [];
			$args['label'] = $label;
			$args['type'] = 'afeb_pro_v';
			if (!empty($default)) $args['default'] = ($default == 00 ? 'no' : ($default == 11 ? 'yes' : $default));
			$widget->add_control($id, $this->set_conditions($args, $conditions));
		}
		do_action_ref_array($id, [$id, $widget, $label, $conditions]);
	}

	/**
	 * Displays a simple text or textarea input field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $type
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param string $placeholder
	 * @param string $ad_conditions
	 * @param array $conditions
	 */
	public function bse_txt(
		$widget,
		$type = 'text',
		$id = '',
		$label = '',
		$default = '',
		$placeholder = '',
		$ad_conditions = '',
		$conditions = []
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = $type == 'text' ?
			Controls_Manager::TEXT : ($type == 'text_area' ? Controls_Manager::TEXTAREA : Controls_Manager::WYSIWYG);

		if (!empty($default)) $args['default'] = $default;
		if (!empty($placeholder)) $args['placeholder'] = $placeholder;
		if (strpos($ad_conditions, 'lblk') !== false) $args['label_block'] = true;
		if (strpos($ad_conditions, 'dnmc') !== false) $args['dynamic'] = ['active' => true];
		if (strpos($ad_conditions, 'dai') !== false) $args['ai'] = ['active' => false];

		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a simple text input field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param string $placeholder
	 * @param string $ad_conditions
	 * @param array $conditions
	 */
	public function txt(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$placeholder = '',
		$ad_conditions = '',
		$conditions = []
	) {
		$this->bse_txt(
			$widget,
			'text',
			$id,
			$label,
			$default,
			$placeholder,
			$ad_conditions,
			$conditions
		);
	}

	/**
	 * Displays a simple text area input field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param string $placeholder
	 * @param string $ad_conditions
	 * @param array $conditions
	 */
	public function txt_area(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$placeholder = '',
		$ad_conditions = '',
		$conditions = []
	) {
		$this->bse_txt(
			$widget,
			'text_area',
			$id,
			$label,
			$default,
			$placeholder,
			$ad_conditions,
			$conditions
		);
	}

	/**
	 * Displays a WYSIWYG input field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param string $placeholder
	 * @param string $ad_conditions
	 * @param array $conditions
	 */
	public function wysiwyg(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$placeholder = '',
		$ad_conditions = '',
		$conditions = []
	) {
		$this->bse_txt(
			$widget,
			'wysiwyg',
			$id,
			$label,
			$default,
			$placeholder,
			$ad_conditions,
			$conditions
		);
	}

	/**
	 * Displays a simple number input field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param int $min
	 * @param int $max
	 * @param float $step
	 * @param string $default
	 * @param string $placeholder
	 * @param string $ad_conditions
	 * @param array $conditions
	 */
	public function num(
		$widget,
		$id = '',
		$label = '',
		$min = 0,
		$max = null,
		$step = null,
		$default = '',
		$placeholder = '',
		$ad_conditions = '',
		$conditions = []
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::NUMBER;
		if ($min !== null) $args['min'] = $min;
		if ($max !== null) $args['max'] = $max;
		if ($step !== null) $args['step'] = $step;
		if (!empty($default)) $args['default'] = $default;
		if (!empty($placeholder)) $args['placeholder'] = $placeholder;
		if (strpos($ad_conditions, 'lblk') !== false) $args['label_block'] = true;

		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a URL input field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param int $nofollow
	 * @param string $placeholder
	 * @param array $conditions
	 * @param int $external
	 * @param int $dynamic
	 * @param string $label
	 */
	public function url(
		$widget,
		$id = '',
		$nofollow = 0,
		$placeholder = '',
		$conditions = [],
		$external = 1,
		$dynamic = 1,
		$label = ''
	) {
		$args = [];
		$args['type'] = Controls_Manager::URL;
		$args['default'] = [
			'nofollow' => $nofollow,
			'is_external' => $external
		];
		if (!empty($placeholder)) {
			$args['placeholder'] = $placeholder;
		}
		$args['dynamic'] = [
			'active' => $dynamic
		];
		$label = !empty($label) ? $label : __('Link', 'addons-for-elementor-builder');
		$args['label_block'] = true;
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a code editor textarea
	 * 
	 * @since 1.0.3
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param int $language
	 * @param array $conditions
	 */
	public function cde(
		$widget,
		$id = '',
		$label = '',
		$language = '',
		$conditions = []
	) {
		$args = [];
		$args['label'] = !empty($label) ? $label : __('Code Editor', 'addons-for-elementor-builder');
		$args['type'] = Controls_Manager::CODE;
		if (!empty($language)) {
			$args['language	'] = $language;
		}
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays the icons chooser
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $icon
	 * @param string $icon_library
	 * @param string $label
	 * @param int $inline_skin
	 * @param int $label_block
	 * @param array $conditions
	 */
	public function icn(
		$widget,
		$id = '',
		$icon = '',
		$icon_library = '',
		$label = '',
		$inline_skin = 0,
		$label_block = 0,
		$conditions = []
	) {
		$args = [];
		$args['label'] = !empty($label) ? $label : esc_html__('Icon', 'addons-for-elementor-builder');
		$args['type'] = Controls_Manager::ICONS;
		if (!empty($icon) && !empty($icon_library)) {
			$args['default'] = [
				'value' => $icon,
				'library' => $icon_library
			];
		}
		if ($inline_skin == 1) {
			$args['skin'] = 'inline';
		}
		if ($label_block == 1) $args['label_block'] = true;
		else $args['label_block'] = false;

		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Elementor switcher control displays an on/off
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $label_on
	 * @param string $label_off
	 * @param int $default
	 * @param array $conditions
	 * @param string $description
	 */
	public function swtchr(
		$widget,
		$id = '',
		$label = '',
		$label_on = '',
		$label_off = '',
		$default = 0,
		$condition = [],
		$conditions = [],
		$description = '',
		$responsive = 0
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::SWITCHER;
		$args['label_on'] = $label_on;
		$args['label_off'] = $label_off;
		if ($default === 1) {
			$args['default'] = 'yes';
		}
		if (!empty($description)) {
			$args['description'] = $description;
		}
		if ($responsive == 0) {
			$widget->add_control($id, $this->set_conditions($args, $condition, $conditions));
		} else {
			$widget->add_responsive_control($id, $this->set_conditions($args, $condition, $conditions));
		}
	}

	/**
	 * Elementor switcher control displays an yes/no
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param array $conditions
	 * @param string $description
	 */
	public function yn_swtchr(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$condition = [],
		$conditions = [],
		$description = ''
	) {
		$this->swtchr(
			$widget,
			$id,
			$label,
			__('Yes', 'addons-for-elementor-builder'),
			__('No', 'addons-for-elementor-builder'),
			$default,
			$condition,
			$conditions,
			$description
		);
	}

	/**
	 * Elementor switcher control displays an yes/no (Responsive)
	 * 
	 * @since 1.0.7
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param array $conditions
	 * @param string $description
	 */
	public function res_yn_swtchr(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$condition = [],
		$conditions = [],
		$description = ''
	) {
		$this->swtchr(
			$widget,
			$id,
			$label,
			__('Yes', 'addons-for-elementor-builder'),
			__('No', 'addons-for-elementor-builder'),
			$default,
			$condition,
			$conditions,
			$description,
			1
		);
	}

	/**
	 * Elementor switcher control displays an show/hide
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param array $conditions
	 * @param string $description
	 */
	public function sh_swtchr(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$conditions = [],
		$description = ''
	) {
		$this->swtchr(
			$widget,
			$id,
			$label,
			__('Show', 'addons-for-elementor-builder'),
			__('Hide', 'addons-for-elementor-builder'),
			$default,
			$conditions,
			$description
		);
	}

	/**
	 * Displays radio buttons styled as groups of buttons with icons for each option
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $options
	 * @param array $selectors
	 * @param string $default
	 * @param int $no_toggle
	 * @param array $conditions
	 * @param array $selectors_dictionary
	 * @param string $classes
	 * @param int $responsive
	 */
	public function bse_chse(
		$widget,
		$id = '',
		$label = '',
		$options = [],
		$selectors = [],
		$no_toggle = 1,
		$default = '',
		$conditions = [],
		$selectors_dictionary = [],
		$classes = '',
		$responsive = 0
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::CHOOSE;
		if (is_array($options) && count($options) > 0) {
			$args['options'] = $options;
		}
		if (is_array($selectors) && count($selectors) > 0) {
			$args['selectors'] = $selectors;
		}
		if ($no_toggle === 1) {
			$args['toggle'] = false;
		}
		$args['default'] = $default;
		if (is_array($selectors_dictionary) && count($selectors_dictionary) > 0) {
			$args['selectors_dictionary'] = $selectors_dictionary;
		}
		if (!empty($classes)) $args['classes'] = $classes;
		if ($responsive == 0) {
			$widget->add_control($id, $this->set_conditions($args, $conditions));
		} else {
			$widget->add_responsive_control($id, $this->set_conditions($args, $conditions));
		}
	}

	/**
	 * Displays radio buttons styled as groups of buttons with icons for each option
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $options
	 * @param array $selectors
	 * @param string $default
	 * @param int $no_toggle
	 * @param array $conditions
	 * @param array $selectors_dictionary
	 * @param string $classes
	 */
	public function chse(
		$widget,
		$id = '',
		$label = '',
		$options = [],
		$selectors = [],
		$no_toggle = 1,
		$default = '',
		$conditions = [],
		$selectors_dictionary = [],
		$classes = ''
	) {
		$this->bse_chse(
			$widget,
			$id,
			$label,
			$options,
			$selectors,
			$no_toggle,
			$default,
			$conditions,
			$selectors_dictionary,
			$classes
		);
	}

	/**
	 * Displays radio buttons styled as groups of buttons with icons for each option (Responsive)
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $options
	 * @param array $selectors
	 * @param string $default
	 * @param int $no_toggle
	 * @param array $conditions
	 * @param array $selectors_dictionary
	 * @param string $classes
	 */
	public function res_chse(
		$widget,
		$id = '',
		$label = '',
		$options = [],
		$selectors = [],
		$no_toggle = 1,
		$default = '',
		$conditions = [],
		$selectors_dictionary = [],
		$classes = ''
	) {
		$this->bse_chse(
			$widget,
			$id,
			$label,
			$options,
			$selectors,
			$no_toggle,
			$default,
			$conditions,
			$selectors_dictionary,
			$classes,
			1
		);
	}

	/**
	 * Displays a toggle button to open and close a popover
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $label_on
	 * @param string $label_off
	 * @param int $default
	 * @param array $conditions
	 */
	public function pr_tgl(
		$widget,
		$id = '',
		$label = '',
		$label_on = '',
		$label_off = '',
		$default = 0,
		$conditions = []
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::POPOVER_TOGGLE;
		$args['label_on'] = $label_on;
		$args['label_off'] = $label_off;
		if ($default === 1) {
			$args['return_value'] = 'yes';
		}
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a media chooser
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $conditions
	 * @param string $default
	 * @param string $label
	 * @param int $dynamic
	 */
	public function mda(
		$widget,
		$id = '',
		$conditions = [],
		$default = '',
		$label = '',
		$dynamic = 1,
		$media_types = []
	) {
		$args = [];
		$args['label'] = !empty($label) ? $label : esc_html__('Image', 'addons-for-elementor-builder');
		$args['type'] = Controls_Manager::MEDIA;
		$args['default'] = ['url' => !empty($default) ? $default : Utils::get_placeholder_image_src()];
		if ($dynamic == 1)
			$args['dynamic'] = ['active' => true];

		if (is_array($media_types) && count($media_types) > 0)
			$args['media_types'] = $media_types;

		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a audio chooser
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $conditions
	 * @param string $default
	 * @param string $label
	 * @param int $dynamic
	 */
	public function ado(
		$widget,
		$id = '',
		$conditions = [],
		$default = '',
		$label = '',
		$dynamic = 1
	) {
		$this->mda($widget, $id, $conditions, !empty($default) ? $default : ' ', !empty($label) ? $label : __('Audio', 'addons-for-elementor-builder'), $dynamic, ['audio']);
	}

	/**
	 * Displays input fields to define one of the default image sizes
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $conditions
	 * @param array $exclude
	 * @param string $default
	 * @param string $separator
	 */
	public function img_sze(
		$widget,
		$id = '',
		$conditions = [],
		$exclude = [],
		$default = 'full',
		$separator = ''
	) {
		$args = [];
		$args['name'] = $id;
		$args['default'] = $default;
		if (trim($separator)) $args['separator'] = $separator;

		$args['exclude'] = is_array($exclude) && count($exclude) > 0 ? $exclude : ['custom'];
		$widget->add_group_control(Group_Control_Image_Size::get_type(), $this->set_conditions($args, $conditions));
	}

	/**
	 * Adjust element text alignment
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $selectors
	 * @param int $default
	 * @param array $conditions
	 * @param int $responsive
	 */
	public function talmnt(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$default = '',
		$conditions = [],
		$responsive = 0
	) {
		$selectors = [];
		if (!empty($selector)) {
			$selectors[$selector] = 'text-align: {{VALUE}}';
		}
		$label = !empty($label) ? $label : __('Alignment', 'addons-for-elementor-builder');
		if ($responsive == 0) {
			$this->chse(
				$widget,
				$id,
				$label,
                $this->get_alignment_options(),
				$selectors,
				1,
				$default,
				$conditions
			);
		} else {
			$this->res_chse(
				$widget,
				$id,
				$label,
                $this->get_alignment_options(),
				$selectors,
				1,
				$default,
				$conditions
			);
		}
	}

	/**
	 * Adjust element text alignment (Responsive)
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $selectors
	 * @param int $default
	 * @param array $conditions
	 */
	public function res_talmnt(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$default = '',
		$conditions = []
	) {
		$this->talmnt(
			$widget,
			$id,
			$selector,
			$label,
			$default,
			$conditions,
			1
		);
	}

	/**
	 * Adjust element flex alignment
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $selectors
	 * @param int $default
	 * @param array $conditions
	 * @param int $responsive
	 */
	public function falmnt(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$default = '',
		$conditions = [],
		$responsive = 0
	) {
		$selectors = [];
		if (!empty($selector)) {
			$selectors[$selector] = 'justify-content: {{VALUE}}';
		}
		$label = !empty($label) ? $label : esc_html__('Alignment', 'addons-for-elementor-builder');
		if ($responsive == 0) {
			$this->chse(
				$widget,
				$id,
				$label,
				$this->get_alignment_options(),
				$selectors,
				1,
				$default,
				$conditions
			);
		} else {
			$this->res_chse(
				$widget,
				$id,
				$label,
                $this->get_alignment_options(),
				$selectors,
				1,
				$default,
				$conditions
			);
		}
	}

	/**
	 * Adjust element flex alignment (Responsive)
	 * 
	 * @since 1.0.2
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $selectors
	 * @param int $default
	 * @param array $conditions
	 */
	public function res_falmnt(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$default = '',
		$conditions = []
	) {
		$this->falmnt(
			$widget,
			$id,
			$selector,
			$label,
			$default,
			$conditions,
			1
		);
	}

	/**
	 * Allows you to build repeatable blocks of fields
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $fields
	 * @param array $default
	 * @param string $title_field
	 * @param string $label
	 * @param array $conditions
	 */
	public function rptr(
		$widget,
		$id = '',
		$fields = null,
		$default = [],
		$title_field = '',
		$label = '',
		$conditions = []
	) {
		$args = [];
		$args['type'] = Controls_Manager::REPEATER;
		$args['fields'] = $fields;
		$args['title_field'] = $title_field == ' ' ? '' : sprintf(substr(trim($title_field), 0, 1) == '{' ? '%s' : '{{{%s}}}', !empty($title_field) ? $title_field : 'itm_ttl');
		if (is_array($default) && count($default) > 0) {
			$args['default'] = $default;
		}
		$args['label'] = !empty($label) ? $label : esc_html__('Items', 'addons-for-elementor-builder');
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a color picker field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 * @param array $selectors
	 * @param array $conditions
	 */
	public function clr_pckr(
		$widget,
		$id = '',
		$label = '',
		$default = '',
		$selectors = [],
		$conditions = []
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::COLOR;
		$args['default'] = $default;
		if (is_array($selectors) && count($selectors) > 0) {
			$args['selectors'] = $selectors;
		}
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a color picker field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param string $selector_value
	 * @param string $label
	 * @param string $default
	 * @param array $conditions
	 */
	public function cstm_clr(
		$widget,
		$id = '',
		$selector = '',
		$selector_value = '',
		$label = '',
		$default = '',
		$conditions = []
	) {
		$selectors = [];
		if (!empty($selector)) {
			$selectors[$selector] = $selector_value;
		}
		$this->clr_pckr(
			$widget,
			$id,
			$label,
			$default,
			$selectors,
			$conditions
		);
	}

	/**
	 * Displays a color picker field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param string $label
	 * @param array $conditions
	 * @param string $default
	 * @param boolean $important
	 */
	public function clr(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$conditions = [],
		$default = '',
		$important = true
	) {
		$label = !empty($label) ? $label : __('Text Color', 'addons-for-elementor-builder');
		$selectors = [];

		if (!empty($selector))
			$selectors[$selector] = 'color: {{VALUE}}' .
				($important ? ' !important;' : ';') . 'fill: {{VALUE}}' .
				($important ? ' !important;' : '');

		$this->clr_pckr(
			$widget,
			$id,
			$label,
			$default,
			$selectors,
			$conditions
		);
	}

	/**
	 * Displays a color picker field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param string $default
	 * @param string $label
	 * @param array $conditions
	 * @param boolean $important
	 */
	public function bg_clr(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$default = '',
		$conditions = [],
		$important = true
	) {
		$label = !empty($label) ? $label : esc_html__('Background Color', 'addons-for-elementor-builder');
		$selectors = [];

		if (!empty($selector))
			$selectors[$selector] = 'background-color: {{VALUE}}' . ($important ? ' !important' : '');

		$this->clr_pckr(
			$widget,
			$id,
			$label,
			$default,
			$selectors,
			$conditions
		);
	}

	/**
	 * Displays input fields to define the background including the background color, background image, background gradient or background video
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param array $types
	 * @param array $exclude
	 * @param array $conditions
	 * @param array $more
	 */
	public function bg_grp_ctrl(
		$widget,
		$id = '',
		$selector = '',
		$types = [],
		$exclude = [],
		$conditions = [],
		$more = []
	) {
		$args = [];
		$args['name'] = $id;
		if (!empty($selector)) {
			$args['selector'] = $selector;
		}
		if (is_array($types) && count($types) > 0) {
			$args['types'] = $types;
		}
		if (is_array($exclude) && count($exclude) > 0) {
			$args['exclude'] = $exclude;
		} else {
			$args['exclude'] = ['image'];
		}
		if (is_array($more) && count($more) > 0) {
			$args = array_merge($args, $more);
		}
		$widget->add_group_control(Group_Control_Background::get_type(), $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays input fields to define the border
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param string $label
	 * @param array $conditions
	 */
	public function brdr(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$conditions = []
	) {
		$args = [];
		$args['name'] = $id;
		if (!empty($selector)) {
			$args['selector'] = $selector;
		}
		$args['label'] = !empty($label) ? $label : __('Border', 'addons-for-elementor-builder');
		$widget->add_group_control(Group_Control_Border::get_type(), $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays input fields to define the box shadow
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param string $label
	 * @param array $conditions
	 */
	public function bx_shdo(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$conditions = []
	) {
		$args = [];
		$args['name'] = $id;
		if (!empty($selector)) {
			$args['selector'] = $selector;
		}
		$args['label'] = !empty($label) ? $label : __('Box Shadow', 'addons-for-elementor-builder');
		$widget->add_group_control(Group_Control_Box_Shadow::get_type(), $this->set_conditions($args, $conditions));
	}

	/**
	 * displays sliders fields to define the values of different CSS filters
	 * 
	 * @since 1.2.0
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param array $conditions
	 */
	public function cs_fltr(
		$widget,
		$id = '',
		$selector = '',
		$conditions = []
	) {
		$args = [];
		$args['name'] = $id;
		if (!empty($selector)) {
			$args['selector'] = $selector;
		}
		$widget->add_group_control(Group_Control_Css_Filter::get_type(), $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays input fields to define the content typography
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $selector
	 * @param string $label
	 * @param array $conditions
	 */
	public function typo(
		$widget,
		$id = '',
		$selector = '',
		$label = '',
		$conditions = []

	) {
		$args = [];
		$args['name'] = $id;
		if (!empty($selector)) {
			$args['selector'] = $selector;
		}
		$args['label'] = !empty($label) ? $label : __('Typography', 'addons-for-elementor-builder');
		$widget->add_group_control(Group_Control_Typography::get_type(), $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a draggable range slider
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $selectors
	 * @param array $range
	 * @param array $size_units
	 * @param array $condition
	 * @param array $default
	 * @param array $conditions
	 * @param int $responsive
	 */
	public function sldr(
		$widget,
		$id = '',
		$label = '',
		$selectors = '',
		$range = [],
		$size_units = [],
		$condition = [],
		$default = [],
		$conditions = [],
		$responsive = 0
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::SLIDER;
		if (is_array($selectors) && count($selectors) > 0) {
			$args['selectors'] = $selectors;
		}
		if (is_array($range) && count($range) > 0) {
			$args['range'] = $range;
		}
		if (is_array($default) && count($default) > 0) {
			if (!isset($default['unit'])) {
				$default['unit'] = 'px';
			}
			$args['default'] = $default;
		}
		if (is_array($size_units) && count($size_units) > 0) {
			$args['size_units'] = $size_units;
		} else {
			$args['size_units'] = $this->default_size_units;
		}
		if ($responsive == 0) {
			$widget->add_control($id, $this->set_conditions($args, $condition, $conditions));
		} else {
			$widget->add_responsive_control($id, $this->set_conditions($args, $condition, $conditions));
		}
	}

	/**
	 * Displays a draggable range slider (Responsive)
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $selectors
	 * @param array $range
	 * @param array $size_units
	 * @param array $condition
	 * @param array $default
	 * @param array $conditions
	 */
	public function res_sldr(
		$widget,
		$id = '',
		$label = '',
		$selectors = '',
		$range = [],
		$size_units = [],
		$condition = [],
		$default = [],
		$conditions = []
	) {
		$this->sldr($widget, $id, $label, $selectors, $range, $size_units, $condition, $default, $conditions, 1);
	}

	/**
	 * Displays a image picker field
	 * 
	 * @since 1.0.2
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $options
	 * @param string $default
	 * @param string $label
	 * @param array $conditions
	 */
	public function img_slct(
		$widget,
		$id = '',
		$options = [],
		$default = '',
		$label = '',
		$conditions = []
	) {
		$args = [];
		if (!empty($label)) $args['label'] = $label;
		$args['type'] = 'afeb_img_slct';
		$args['label_block'] = true;
		if (is_array($options) && count($options) > 0) {
			$args['options'] = $options;
		}
		if (!empty($default)) {
			$args['default'] = $default;
		}

		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a simple select box field
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $options
	 * @param string $default
	 * @param array $conditions
	 * @param array $selectors
	 * @param array $selectors_dictionary
	 * @param string $prefix_class
	 * @param int $responsive
	 * @param string $ad_conditions
	 */
	public function slct(
		$widget,
		$id = '',
		$label = '',
		$options = [],
		$default = '',
		$conditions = [],
		$selectors = [],
		$selectors_dictionary = [],
		$prefix_class = '',
		$responsive = 0,
		$ad_conditions = ''
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::SELECT;
		if (is_array($options) && count($options) > 0) {
			$args['options'] = Helper::fhook($options, $id, $widget);
		}
		$args['default'] = $default;
		if (is_array($selectors) && count($selectors) > 0) {
			$args['selectors'] = $selectors;
		}
		if (is_array($selectors_dictionary) && count($selectors_dictionary) > 0) {
			$args['selectors_dictionary'] = $selectors_dictionary;
		}
		if (!empty($prefix_class)) $args['prefix_class'] = $prefix_class;
		if (strpos($id, '_lbc') !== false || strpos($ad_conditions, 'lbc') !== false) {
			$args['label_block'] = true;
		}
		if ($responsive == 0) {
			$widget->add_control($id, $this->set_conditions($args, $conditions));
		} else {
			$widget->add_responsive_control($id, $this->set_conditions($args, $conditions));
		}
	}

	/**
	 * Displays a simple select box field(Responsive)
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $options
	 * @param string $default
	 * @param array $conditions
	 * @param array $selectors
	 * @param array $selectors_dictionary
	 * @param string $prefix_class
	 */
	public function res_slct(
		$widget,
		$id = '',
		$label = '',
		$options = [],
		$default = '',
		$conditions = [],
		$selectors = [],
		$selectors_dictionary = [],
		$prefix_class = ''
	) {
		$this->slct($widget, $id, $label, $options, $default, $conditions, $selectors, $selectors_dictionary, $prefix_class, 1);
	}

	/**
	 * Displays a select box control
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $options
	 * @param array $default
	 * @param int $label_block
	 * @param int $multiple
	 * @param array $conditions
	 */
	public function slct2(
		$widget,
		$id = '',
		$label = '',
		$options = [],
		$default = [],
		$label_block = 1,
		$multiple = 1,
		$conditions = []
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = Controls_Manager::SELECT2;
		if (is_array($options) && count($options) > 0) {
			$args['options'] = $options;
		}
		if (is_array($default) && count($default) > 0) {
			$args['default'] = $default;
		}
		if ($label_block == 1) {
			$args['label_block'] = true;
		}
		if ($multiple == 1) {
			$args['multiple'] = true;
		}
		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a select box control
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param array $options
	 * @param string $query_slug
	 * @param string $ad_conditions
	 * @param int $multiple
	 * @param array $conditions
	 */
	public function dslct2(
		$widget,
		$id = '',
		$label = '',
		$options = '',
		$query_slug = '',
		$ad_conditions = 'lbc',
		$conditions = [],
		$multiple = 0
	) {
		$args = [];
		$args['label'] = $label;
		$args['type'] = 'afeb_dynamic_select';
		if (!empty($options))
			$args['options'] = $options;

		if (!empty($query_slug))
			$args['query_slug'] = esc_attr($query_slug);

		if (strpos($ad_conditions, 'lbc') !== false)
			$args['label_block'] = true;

		if ($multiple == 1)
			$args['multiple'] = true;

		$widget->add_control($id, $this->set_conditions($args, $conditions));
	}

	/**
	 * Displays a date/time picker
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param string $label
	 * @param string $default
	 */
	public function dtm_pckr(
		$widget,
		$id = '',
		$label = '',
		$default = ''
	) {
		$args = [];
		$args['label'] = !empty($label) ? $label : __('Date / Time', 'addons-for-elementor-builder');
		$args['type'] = Controls_Manager::DATE_TIME;
		if (!empty($default)) {
			$args['default'] = $default;
		}
		$widget->add_control($id, $args);
	}

	/**
	 * Displays a input fields for top, right, bottom, left and the option to link them together
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $type
	 * @param string $id
	 * @param string $label
	 * @param array $selectors
	 * @param int $global_value
	 * @param int $no_linked
	 * @param int $unit
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 * @param array $size_units
	 * @param array $allowed_dimensions
	 * @param int $responsive
	 * @param array $conditions
	 */
	public function dimensions(
		$widget,
		$type = '',
		$id = '',
		$label = '',
		$selectors = '',
		$global_value = null,
		$no_linked = 0,
		$unit = '',
		$top = null,
		$right = null,
		$bottom = null,
		$left = null,
		$size_units = [],
		$allowed_dimensions = [],
		$responsive = 0,
		$conditions = []
	) {
		$args = [];
		if (empty($label)) {
			switch ($type) {
				case 'margin':
					$args['label'] = __('Margin', 'addons-for-elementor-builder');
					break;
				case 'padding':
					$args['label'] = __('Padding', 'addons-for-elementor-builder');
					break;
				case 'border_radius':
					$args['label'] = __('Border Radius', 'addons-for-elementor-builder');
					break;
			}
		} else {
			$args['label'] = $label;
		}
		$args['type'] = Controls_Manager::DIMENSIONS;
		if (is_array($selectors) && count($selectors) > 0) {
			$args['selectors'] = $selectors;
		} else if (!empty($selectors)) {
			$args['selectors'] = [$selectors => $type == 'margin' ? self::FILL_MARGIN : ($type == 'padding' ? self::FILL_PADDING : self::FILL_BR_RADIUS)];
		}
		if ($global_value !== null) {
			$args['default'] = [
				'top' => $global_value,
				'right' => $global_value,
				'bottom' => $global_value,
				'left' => $global_value,
			];
		}
		if ($no_linked == 1) {
			$args['default']['isLinked'] = false;
		}
		if (!empty($unit)) {
			$args['default']['unit'] = $unit;
		}
		if ($top !== null) {
			$args['default']['top'] = $top;
		}
		if ($right !== null) {
			$args['default']['right'] = $right;
		}
		if ($bottom !== null) {
			$args['default']['bottom'] = $bottom;
		}
		if ($left !== null) {
			$args['default']['left'] = $left;
		}
		if (is_array($size_units) && count($size_units) > 0) {
			$args['size_units'] = $size_units;
		} else {
			$args['size_units'] = $this->default_size_units;
		}
		if (is_array($allowed_dimensions) && count($allowed_dimensions) > 0) {
			$args['allowed_dimensions'] = $allowed_dimensions;
		}
		if ($responsive == 0) {
			$widget->add_control($id, $this->set_conditions($args, $conditions));
		} else {
			$widget->add_responsive_control($id, $this->set_conditions($args, $conditions));
		}
	}

	/**
	 * Displays a input fields for margin
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $selectors
	 * @param array $size_units
	 * @param array $allowed_dimensions
	 * @param string $label
	 * @param int $global_value
	 * @param int $no_linked
	 * @param int $unit
	 * @param array $conditions
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 */
	public function mar(
		$widget,
		$id = '',
		$selectors = '',
		$size_units = [],
		$allowed_dimensions = [],
		$label = '',
		$global_value = null,
		$no_linked = 0,
		$unit = '',
		$conditions = [],
		$top = null,
		$right = null,
		$bottom = null,
		$left = null
	) {
		$this->dimensions(
			$widget,
			'margin',
			$id,
			$label,
			$selectors,
			$global_value,
			$no_linked,
			$unit,
			$top,
			$right,
			$bottom,
			$left,
			$size_units,
			$allowed_dimensions,
			0,
			$conditions
		);
	}

	/**
	 * Displays a input fields for margin (Responsive)
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $selectors
	 * @param array $size_units
	 * @param array $allowed_dimensions
	 * @param string $label
	 * @param int $global_value
	 * @param int $no_linked
	 * @param int $unit
	 * @param array $conditions
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 */
	public function res_mar(
		$widget,
		$id = '',
		$selectors = '',
		$size_units = [],
		$allowed_dimensions = [],
		$label = '',
		$global_value = null,
		$no_linked = 0,
		$unit = '',
		$conditions = [],
		$top = null,
		$right = null,
		$bottom = null,
		$left = null
	) {
		$this->dimensions(
			$widget,
			'margin',
			$id,
			$label,
			$selectors,
			$global_value,
			$no_linked,
			$unit,
			$top,
			$right,
			$bottom,
			$left,
			$size_units,
			$allowed_dimensions,
			1,
			$conditions
		);
	}

	/**
	 * Displays a input fields for padding
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $selectors
	 * @param array $size_units
	 * @param array $allowed_dimensions
	 * @param string $label
	 * @param int $global_value
	 * @param int $no_linked
	 * @param int $unit
	 * @param array $conditions
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 */
	public function pad(
		$widget,
		$id = '',
		$selectors = '',
		$size_units = [],
		$allowed_dimensions = [],
		$label = '',
		$global_value = null,
		$no_linked = 0,
		$unit = '',
		$conditions = [],
		$top = null,
		$right = null,
		$bottom = null,
		$left = null
	) {
		$this->dimensions(
			$widget,
			'padding',
			$id,
			$label,
			$selectors,
			$global_value,
			$no_linked,
			$unit,
			$top,
			$right,
			$bottom,
			$left,
			$size_units,
			$allowed_dimensions,
			0,
			$conditions
		);
	}

	/**
	 * Displays a input fields for padding (Responsive)
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $selectors
	 * @param array $size_units
	 * @param array $allowed_dimensions
	 * @param string $label
	 * @param int $global_value
	 * @param int $no_linked
	 * @param int $unit
	 * @param array $conditions
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 */
	public function res_pad(
		$widget,
		$id = '',
		$selectors = '',
		$size_units = [],
		$allowed_dimensions = [],
		$label = '',
		$global_value = null,
		$no_linked = 0,
		$unit = '',
		$conditions = [],
		$top = null,
		$right = null,
		$bottom = null,
		$left = null
	) {
		$this->dimensions(
			$widget,
			'padding',
			$id,
			$label,
			$selectors,
			$global_value,
			$no_linked,
			$unit,
			$top,
			$right,
			$bottom,
			$left,
			$size_units,
			$allowed_dimensions,
			1,
			$conditions
		);
	}

	/**
	 * Displays a input fields for border radius
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $selectors
	 * @param array $size_units
	 * @param array $allowed_dimensions
	 * @param string $label
	 * @param int $global_value
	 * @param int $no_linked
	 * @param int $unit
	 * @param array $conditions
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 */
	public function brdr_rdus(
		$widget,
		$id = '',
		$selectors = '',
		$size_units = [],
		$allowed_dimensions = [],
		$label = '',
		$global_value = null,
		$no_linked = 0,
		$unit = '',
		$conditions = [],
		$top = null,
		$right = null,
		$bottom = null,
		$left = null
	) {
		$this->dimensions(
			$widget,
			'border_radius',
			$id,
			$label,
			$selectors,
			$global_value,
			$no_linked,
			$unit,
			$top,
			$right,
			$bottom,
			$left,
			$size_units,
			$allowed_dimensions,
			0,
			$conditions
		);
	}

	/**
	 * Displays a input fields for border radius (Responsive)
	 * 
	 * @since 1.0.1
	 * 
	 * @param object $widget
	 * @param string $id
	 * @param array $selectors
	 * @param array $size_units
	 * @param array $allowed_dimensions
	 * @param string $label
	 * @param int $global_value
	 * @param int $no_linked
	 * @param int $unit
	 * @param array $conditions
	 * @param int $top
	 * @param int $right
	 * @param int $bottom
	 * @param int $left
	 */
	public function res_brdr_rdus(
		$widget,
		$id = '',
		$selectors = '',
		$size_units = [],
		$allowed_dimensions = [],
		$label = '',
		$global_value = null,
		$no_linked = 0,
		$unit = '',
		$conditions = [],
		$top = null,
		$right = null,
		$bottom = null,
		$left = null
	) {
		$this->dimensions(
			$widget,
			'border_radius',
			$id,
			$label,
			$selectors,
			$global_value,
			$no_linked,
			$unit,
			$top,
			$right,
			$bottom,
			$left,
			$size_units,
			$allowed_dimensions,
			1,
			$conditions
		);
	}
}
