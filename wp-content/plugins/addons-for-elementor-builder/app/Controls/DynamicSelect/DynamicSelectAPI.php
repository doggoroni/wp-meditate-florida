<?php

namespace AFEB\Controls\DynamicSelect;

use AFEB\Documents\Builder;
use AFEB\Helper;
use WP_Query;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * "Vertex Addons for Elementor" DynamicSelectAPI Class
 * 
 * @class DynamicSelectAPI
 * @version 1.0.7
 */
class DynamicSelectAPI
{
	/**
	 * Posts Per Page
	 */
	const POSTS_PER_PAGE = 15;

	/**
	 * Initialize "Vertex Addons for Elementor" DynamicSelectAPI
	 * 
	 * @since 1.0.7
	 */
	public function init()
	{
		$this->actions();
	}

	/**
	 * DynamicSelectAPI Class Actions
	 * 
	 * @since 1.0.7
	 */
	public function actions()
	{
		add_action('rest_api_init', [$this, 'rest_api_init']);
	}

	/**
	 * Registers a REST API route
	 *
	 * @since 1.0.7
	 */
	public function rest_api_init()
	{
		register_rest_route(
			'afeb-api/v1/dynamic-select',
			'/(?P<action>\w+)/',
			[
				'methods' => 'GET',
				'callback' =>  [$this, 'callback'],
				'permission_callback' => '__return_true'
			]
		);
	}

	/**
	 * REST API Callback
	 *
	 * @since 1.0.7
	 * 
	 * @param array $request
	 */
	public function callback($request)
	{
		return $this->{$request['action']}($request);
	}

	public function get_options($options = [])
	{
		$output = [];
		$get_options = $options;

		foreach ($get_options as $option_key => $option_value) {
			$output[] = [
				'id' => $option_key,
				'text' => $option_value,
			];
		}

		return $output;
	}

	public function get_authors($request): array
	{
		if (!current_user_can('edit_posts')) {
			return [];
		}

		$output = $args = [];
		if (isset($request['s'])) {
			$args['search'] = '*' . sanitize_text_field($request['s']) . '*';
		}

		$args['search_columns'] = ['display_name'];
		$args['fields'] = 'all';

		foreach (get_users($args) as $author) {
			if (!empty($author->ID)) {
				$output[] = [
					'id' => $author->ID,
					'text' => $author->display_name,
				];
			}
		}

		return ['results' => $output];
	}

	public function get_posts_by_type($request): array
	{
		if (!current_user_can('edit_posts'))
			return [];

		$slug = isset($request['query_slug']) ?
			sanitize_text_field($request['query_slug']) : 'none';
		$args = ['def_item' => 'non'];

		if (isset($request['s']))
			$args['s'] = sanitize_text_field($request['s']);

		$options = $this->get_options(Helper::get_posts_by_type($slug, $args));

		return ['results' => $options];
	}

	public function get_terms($request): array
	{
		if (!current_user_can('edit_posts')) {
			return [];
		}

		$output = $args = [];

		$slug = isset($request['query_slug']) ?
			sanitize_text_field($request['query_slug']) : 'none';

		if (strpos($slug, 'hide_empty') !== false) {
			$args['hide_empty'] = false;
		}

		if (isset($request['s'])) {
			$args['name__like'] = sanitize_text_field($request['s']);
		}

		foreach (get_terms($args) as $term) {
			if (!empty($term->term_id)) {

				$taxonomy = '';
				if (!empty($term->taxonomy)) {

					if (
						in_array($term->taxonomy, ['nav_menu', 'wp_theme', 'elementor_library_type']) ||
						(strpos($slug, 'custom_tax') !== false && in_array($term->taxonomy, ['category', 'post_tag']))
					) {
						continue;
					}

					$taxonomy = ucwords(str_replace('_', ' ', $term->taxonomy)) . ': ';
				}

				$term_name = !empty($term->name) ? ucwords(str_replace('-', ' ', $term->name)) : '';
				$output[] = [
					'id' => $term->term_id . '_____' . $term->taxonomy,
					'text' => $taxonomy . $term_name,
				];
			}
		}

		return ['results' => $output];
	}

	public function get_terms_by_tax($request): array
	{
		if (!current_user_can('edit_posts'))
			return [];

		$slug = isset($request['query_slug']) ?
			sanitize_text_field($request['query_slug']) : '';
		$args = ['def_item' => 'non'];

		if (isset($request['s'])) {
			$args['name__like'] = sanitize_text_field($request['s']);
		}

		$options = $this->get_options(Helper::get_terms_by_tax($slug, $args));

		return ['results' => $options];
	}

	public function get_post_types($request): array
	{
		if (!current_user_can('edit_posts'))
			return [];

		$exclude = !empty($request['query_slug']) ?
			explode(',', sanitize_text_field($request['query_slug'])) : [];
		$exclude = array_combine($exclude, $exclude);

		$args = ['def_item' => 'non'];
		$options = Helper::get_post_types($exclude, $args);

        if (isset($request['s'])) {
            $search_term = $request['s'];
            if (is_scalar($search_term)) {
                $search_term = sanitize_text_field(wp_unslash((string) $search_term));
                if ($search_term !== '') {
                    $pattern = sprintf('/.*?%s.*?/i', preg_quote($search_term, '/'));
                    $options = preg_grep($pattern, $options);
                }
            }
        }


		$options = $this->get_options($options);

		return ['results' => $options];
	}

	public function get_templates($request): array
	{
		if (!current_user_can('edit_posts'))
			return [];

		$meta_query_value = isset($request['query_slug']) ?
			sanitize_text_field($request['query_slug']) : '';

		$args = [
			'post_type' => Builder::BUILDER_DOCUMENT,
			'post_status' => 'publish',
			'posts_per_page' => self::POSTS_PER_PAGE,
			'meta_key' => '_afeb_document_type',
			'meta_value' => $meta_query_value,
		];

		if (isset($request['s']))
			$args['s'] = sanitize_text_field($request['s']);

		$options = [];
		$query = new WP_Query($args);

		if ($query->have_posts()) {
			$options = $this->get_options(Helper::new_array_items([], 'non'));
			while ($query->have_posts()) {
				$query->the_post();
				$options[] = [
					'id' => get_the_ID(),
					'text' => html_entity_decode(get_the_title()),
				];
			}
		}

		wp_reset_postdata();

		return ['results' => $options];
	}
}
