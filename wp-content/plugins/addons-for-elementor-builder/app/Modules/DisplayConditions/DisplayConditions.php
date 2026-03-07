<?php

namespace AFEB\Modules\DisplayConditions;

use AFEB\Controls\Helper as CHelper;
use AFEB\Db;
use AFEB\Helper;
use AFEB\PostTypes\Builder;
use Elementor\Repeater;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * "Vertex Addons for Elementor" DisplayConditions Class
 * 
 * @class DisplayConditions
 * @version 1.2.0
 */
class DisplayConditions
{
	/**
	 * @var CHelper
	 */
	private $controls;

	/**
	 * Initialize "Vertex Addons for Elementor" DynamicTags
	 * 
	 * @since 1.2.0
	 */
	public function init()
	{
		$this->actions();
	}

	/**
	 * CustomCssjs Class Actions
	 *
	 * @since 1.2.0
	 */
	public function actions()
	{
		add_action('elementor/documents/register_controls', [$this, 'register_controls']);
	}

	public function register_controls($context)
	{
		global $post;
        if (!($post instanceof \WP_Post)) return;

		$template_type = get_post_meta($post->ID, '_elementor_template_type', true);
		$document_type = get_post_meta($post->ID, '_afeb_document_type', true);

		if (
			($template_type != 'afeb-builder' &&
				$template_type != 'afeb-popup') ||
			$document_type === 'dynamic-loop-item'
		) {
			return;
		}

		$slug = '';

		if ($document_type == 'archive') $slug = '_archive';
		elseif ($document_type == 'single') $slug = '_single';

		$this->controls = new CHelper($context);
		$this->controls->tab_advanced_section("display_condition{$slug}_advanced_section", [
			'label' => esc_html__('Display Conditions', 'addons-for-elementor-builder')
		], function () {
			$this->controls->yn_switcher('enable_display_conditons', [
				'label' => esc_html__('Enable Display Conditons', 'addons-for-elementor-builder'),
			]);

			global $post;
			$document_type = get_post_meta($post->ID, '_afeb_document_type', true);

			if ($document_type == 'header' || $document_type == 'footer') {
				$this->controls->yn_switcher('include_in_canvas_page', [
					'label' => esc_html__('Include in Canvas pages', 'addons-for-elementor-builder'),
				]);
			}

			$repeater = new Repeater();
			$repeater_controls = new CHelper($repeater);

			$repeater_controls->hidden('include_conditon', [
				'default' => 'yes',
			]);

			$conditions = $this->conditions();

			if ($document_type == 'archive' || $document_type == 'single')
				unset($conditions['group']['entire']);

			$args = [
				'label' => esc_html__('Group', 'addons-for-elementor-builder'),
				'options' => $conditions['group'],
				'default' => '',
				'label_block' => true,
			];

			if ($document_type == 'archive' || $document_type == 'single')
				$args['condition'] = ['condition' => 'no'];

			$repeater_controls->select('conditon_group', $args);

			if ($document_type != 'archive') {
				$singular_sub_group = Helper::new_array_items($this->get_sub_group_by_slug('_sng'), 'all');

				unset($singular_sub_group['_sngpst_post_cats']);
				unset($singular_sub_group['_sngpst_post_tags']);
				unset($singular_sub_group['_sngpst_post_type']);

				$repeater_controls->select('conditon_sub_group_singular', [
					'label' => $document_type == 'single' ?
						esc_html__('Group', 'addons-for-elementor-builder') :
						esc_html__('Singular Sub Group', 'addons-for-elementor-builder'),
					'options' => $singular_sub_group,
					'default' => '',
					'label_block' => true,
					'condition' => ['conditon_group' => 'singular'],
				]);

				$repeater_controls->dynamic_select('conditon_sub_group_value_sngpge_page', [
					'label' => esc_html__('Select Page', 'addons-for-elementor-builder'),
					'options' => 'get_posts_by_type',
					'query_slug' => 'page',
					'default' => '',
					'condition' => [
						'conditon_group' => 'singular',
						'conditon_sub_group_singular' => '_sngpge_page'
					],
				]);

				$repeater_controls->dynamic_select('conditon_sub_group_value_sngpst_post', [
					'label' => esc_html__('Select Post', 'addons-for-elementor-builder'),
					'options' => 'get_posts_by_type',
					'query_slug' => 'post',
					'default' => '',
					'condition' => [
						'conditon_group' => 'singular',
						'conditon_sub_group_singular' => '_sngpst_post'
					],
				]);
			}

			if ($document_type != 'single') {
				$archive_sub_group = Helper::new_array_items($this->get_sub_group_by_slug('_archv'), 'all');

				$repeater_controls->select('conditon_sub_group_archive', [
					'label' => $document_type == 'archive' ?
						esc_html__('Group', 'addons-for-elementor-builder') :
						esc_html__('Archive Sub Group', 'addons-for-elementor-builder'),
					'options' => $archive_sub_group,
					'default' => '',
					'label_block' => true,
					'condition' => ['conditon_group' => 'archive'],
				]);

				$repeater_controls->dynamic_select('conditon_sub_group_value_archvpst_cats', [
					'label' => esc_html__('Select Category', 'addons-for-elementor-builder'),
					'options' => 'get_terms_by_tax',
					'query_slug' => 'category',
					'default' => '',
					'condition' => [
						'conditon_group' => 'archive',
						'conditon_sub_group_archive' => '_archvpst_cats'
					],
				]);

				$repeater_controls->dynamic_select('conditon_sub_group_value_archvpst_tags', [
					'label' => esc_html__('Select Tag', 'addons-for-elementor-builder'),
					'options' => 'get_terms_by_tax',
					'query_slug' => 'post_tag',
					'default' => '',
					'condition' => [
						'conditon_group' => 'archive',
						'conditon_sub_group_archive' => '_archvpst_tags'
					],
				]);
			}

			$this->controls->repeater('display_condition', [
				'label' => esc_html__('Conditions', 'addons-for-elementor-builder'),
				'fields' => $repeater->get_controls(),
				'default' => [],
				'condition' => [
					'enable_display_conditons' => 'yes',
				],
			]);

			$repeater = new Repeater();
			$this->controls->repeater('display_condition_temp', [
				'fields' => $repeater->get_controls(),
				'default' => [],
				'condition' => [
					'condition' => 'no',
				],
			]);
		});
	}

	/**
	 * All conditions items
	 * 
	 * @since 1.2.0
	 * 
	 * @return array
	 */
	public function conditions()
	{
		return [
			'group' => [
				'' => esc_html__('Select Group', 'addons-for-elementor-builder'),
				'entire' => esc_html__('Entire', 'addons-for-elementor-builder'),
				'singular' => esc_html__('Singular', 'addons-for-elementor-builder'),
				'archive' => esc_html__('Archive', 'addons-for-elementor-builder')
			],
			'sub_group' => [
				'_entire' => esc_html__('Entire Site', 'addons-for-elementor-builder'),
				'_sngpge_front_page' => esc_html__('Front Page', 'addons-for-elementor-builder'),
				'_sngpge_page' => esc_html__('Pages', 'addons-for-elementor-builder'),
				'_sngpge_page_404' => esc_html__('404 Page', 'addons-for-elementor-builder'),
				'_sngpst_post' => esc_html__('Posts', 'addons-for-elementor-builder'),
				'_sngpst_post_cats' => esc_html__('Post Categories', 'addons-for-elementor-builder'),
				'_sngpst_post_tags' => esc_html__('Post Tags', 'addons-for-elementor-builder'),
				'_sngpst_post_type' => esc_html__('Single Custom Post Type', 'addons-for-elementor-builder'),
				'_archvpst_cats' => esc_html__('Post Categories', 'addons-for-elementor-builder'),
				'_archvpst_tags' => esc_html__('Post Tags', 'addons-for-elementor-builder')
			]
		];
	}

	/**
	 * Get sub group condition by slug
	 * 
	 * @since 1.2.0
	 * 
	 * @param string $slug
	 * @param boolean $none
	 * 
	 * @return array
	 */
	public function get_sub_group_by_slug($slug = '', $none = false)
	{
		$sub_group = [];
		foreach ($this->conditions()['sub_group'] as $sub_group_key => $sub_group_value)
			if (strpos($sub_group_key, $slug) !== false || ($none && $sub_group_key == '_none'))
				$sub_group[$sub_group_key] = $sub_group_value;

		return $sub_group;
	}

	/**
	 * Returning all conditions from the database
	 * 
	 * @since 1.2.0
	 * 
	 * @param string $post_type
	 * 
	 * @return array
	 */
	public function get_all_conditions_from_db($post_type)
	{
		$post_type = esc_sql($post_type);
		$query = "SELECT `#__posts`.`ID`,`#__postmeta`.`meta_value` FROM `#__posts` " .
			"LEFT JOIN `#__postmeta` ON `#__posts`.`ID` = `#__postmeta`.`post_id` And `#__postmeta`.`meta_key` = '_elementor_page_settings' " .
			"WHERE `#__posts`.`post_status` = 'publish' AND `#__posts`.`post_type` = '$post_type'";

		$conditions = [];
		$db_conditions = (new Db())->slct($query);
		foreach ($db_conditions as $conditions_key => $condition_value) {
			$meta_value = $condition_value->meta_value;

			if (!empty($meta_value) && maybe_unserialize($meta_value))
				$meta_value = unserialize($condition_value->meta_value);

			$display_conditions = [['_id' => '12abcde']];

			if (!empty($meta_value['enable_display_conditons'])) {
				if (
					!empty($meta_value['display_condition']) &&
					is_array($meta_value['display_condition'])
				) {
					$meta_display_conditions = [];
					foreach ($meta_value['display_condition'] as $meta_display_condition):
						if (isset($meta_display_condition['conditon_group']))
							$meta_display_conditions[] = $meta_display_condition;
					endforeach;

					$display_conditions = $meta_display_conditions;
				}
			}

			$relation_type = $meta_value['cdnt_rltn_typ'] ?? 'or';
			$conditions[$conditions_key] = [
				'conditions' => $this->normalize_conditions($display_conditions),
				'relation_type' => $relation_type
			];
		}

		return $conditions;
	}

	/**
	 * Check if the display condition is matches
	 * 
	 * @return array
	 * @since 1.2.0
     *
     */
	public function check_condition($slug = '', $arg = '')
	{
		$slug = ltrim($slug, '_');
		switch ($slug) {
			case 'entire':
				return [
					'result' => true,
					'priority' => 100,
				];
			case 'sngpge_front_page':
				return [
					'result' => is_front_page(),
					'priority' => 70,
				];
			case 'sngpge_page':
				if (empty($arg) || $arg == -1) {
					return [
						'result' => is_page(),
						'priority' => 60,
					];
				}

				return [
					'result' => is_page($arg),
					'priority' => 59,
				];
			case 'sngpge_page_child':
				if (empty($arg) || !is_page()) {
					return [
						'result' => false,
						'priority' => -1,
					];
				}

				global $post;
				$parent = intval($arg);
				return [
					'result' => $post->post_parent === $parent,
					'priority' => 50,
				];
			case 'sngpge_page_template':
				if (empty($arg) || !is_page()) {
					return [
						'result' => false,
						'priority' => -1,
					];
				}

				global $post;
				$page_template_slug = get_page_template_slug($post->ID);
				return [
					'result' => $page_template_slug === $arg,
					'priority' => 50,
				];
			case 'sngpge_page_404':
				return [
					'result' => is_404(),
					'priority' => 0,
				];
			case 'sngpst_post':
				if (empty($arg) || $arg == -1) {
					return [
						'result' => is_singular() && get_post_type() == 'post',
						'priority' => 30,
					];
				}

				return [
					'result' => is_single($arg),
					'priority' => 29,
				];
			case 'sngpst_post_cats':
				if (empty($arg) || !is_single()) {
					return [
						'result' => false,
						'priority' => -1,
					];
				}

				global $post;
				return [
					'result' => in_category($arg, $post),
					'priority' => 30,
				];
			case 'sngpst_post_tags':
				if (empty($arg) || !is_single()) {
					return [
						'result' => false,
						'priority' => -1,
					];
				}

				global $post;
				if ($arg == -1) {
					return [
						'result' => has_tag('', $post),
						'priority' => 30,
					];
				}

				return [
					'result' => has_tag($arg, $post),
					'priority' => 30,
				];
			case 'sng_all':
			case 'sngpst_post_type':
				if (empty($arg)) {
					return [
						'result' => is_singular(),
						'priority' => 45,
					];
				}

				return [
					'result' => is_singular($arg),
					'priority' => 44,
				];
			case 'archv_all':
				return [
					'result' => is_archive(),
					'priority' => 10,
				];
			case 'archvpst_cpt_post':
				if (empty($arg)) {
					return [
						'result' => is_post_type_archive(),
						'priority' => 45,
					];
				}

				if ($arg == 'post' && get_post_type() == 'post') {
					return [
						'result' => is_archive() || is_home(),
						'priority' => 45,
					];
				}

				return [
					'result' => is_post_type_archive($arg) || (is_tax() && get_post_type() == $arg),
					'priority' => 45,
				];
			case 'archvpst_cats':
				if (empty($arg) || $arg == -1) {
					return [
						'result' => is_category(),
						'priority' => 50,
					];
				}

				return [
					'result' => is_category($arg),
					'priority' => 49,
				];
			case 'archvpst_tags':
				if (empty($arg)) {
					return [
						'result' => is_tag(),
						'priority' => 50,
					];
				}

				return [
					'result' => is_tag($arg),
					'priority' => 49,
				];
			default:
				return [
					'result' => false,
					'priority' => -1,
				];
		}
	}

	/**
	 * Finding matching results
	 * 
	 * @since 1.2.0
	 * 
	 * @param string $post_type
	 * 
	 * @return array
	 */
	public function find_matched_results($post_type = 'afeb-popup')
	{
		$conditions = $this->get_all_conditions_from_db($post_type);
		$results_id_list = [];

		foreach ($conditions as $result_id => $result_data) {
			$result_conditions = $result_data['conditions'];

			if (empty($result_conditions)) {
				continue;
			}

			$result_conditions = array_map(function ($condition) {
				$conditon_sub_group = $condition['sub_group'] ?? '';
				$conditon_sub_group_value = $condition['sub_group_value'] ?? '';
				$check_condition = $this->check_condition($conditon_sub_group, $conditon_sub_group_value);
				$condition['match'] = $check_condition['result'];
				$condition['priority'] = $check_condition['priority'];

				return $condition;
			}, $result_conditions);

			$includes_matchs = [];
			$excludes_matchs = [];

			$priority = 100;
			foreach ($result_conditions as $condition) {

				if ($condition['match'] && $condition['priority'] < $priority) {
					$priority = $condition['priority'];
				}

				$include_conditon = filter_var($condition['include'], FILTER_VALIDATE_BOOLEAN);

				if ($include_conditon) {
					$includes_matchs[] = $condition['match'];
				} else {
					$excludes_matchs[] = $condition['match'];
				}
			}

			$is_included = !empty($includes_matchs) && in_array(true, $includes_matchs);
			$is_excluded = !empty($excludes_matchs) && in_array(true, $excludes_matchs);

			if ($is_included && !$is_excluded) {

				if ($post_type == Builder::BUILDER_POST_TYPE) {
					$results_id_list[] = [
						'id' => $result_id,
						'priority' => $priority,
					];
				} else {
					$results_id_list[] = $result_id;
				}
			}
		}

		return $results_id_list;
	}

	/**
	 * Normalize the conditions
	 * 
	 * @since 1.2.0
	 * 
	 * @param array $conditions
	 * 
	 * @return array
	 */
	public function normalize_conditions($conditions = [])
	{
		$normal_condition = [];
		$normal_conditions = [];

		foreach ($conditions as $condition):
			$normal_condition['id'] = isset($condition['_id']) ? sanitize_text_field($condition['_id']) : '';

			$normal_condition['include'] = (isset($condition['include_conditon']) &&
				$condition['include_conditon'] != 'yes') ? false : true;

			$normal_condition['group'] = isset($condition['conditon_group']) ?
				sanitize_text_field(strtolower($condition['conditon_group'])) : 'entire';

			$sub_group_value = true;
			if (!empty($condition['conditon_sub_group_' . $normal_condition['group']]))
				$normal_condition['sub_group'] = sanitize_text_field($condition['conditon_sub_group_' . $normal_condition['group']]);
			else {
				$sub_group_value = false;
				if ($normal_condition['group'] == 'entire') $normal_condition['sub_group'] = '_entire';
				else if ($normal_condition['group'] == 'singular') $normal_condition['sub_group'] = '_sng_all';
				else if ($normal_condition['group'] == 'archive') $normal_condition['sub_group'] = '_archv_all';
			}

			$normal_condition['sub_group_value'] = ($sub_group_value &&
				!empty($condition['conditon_sub_group_value' . $normal_condition['sub_group']])) ?
				sanitize_text_field($condition['conditon_sub_group_value' . $normal_condition['sub_group']]) : '';

			$normal_conditions[] = $normal_condition;
		endforeach;

		return $normal_conditions;
	}
}
