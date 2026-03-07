<?php

namespace AFEB\Controls\DynamicSelect;

use AFEB\Assets;
use Elementor\Base_Data_Control;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * "Vertex Addons for Elementor" DynamicSelect Controls Class
 * 
 * @class DynamicSelect
 * @version 1.0.7
 */
class DynamicSelect extends Base_Data_Control
{
	/**
	 * Retrieve DynamicSelect control type
	 *
	 * @since 1.0.7
	 *
	 * @return string
	 */
	public function get_type()
	{
		return 'afeb_dynamic_select';
	}

	/**
	 * Enqueue DynamicSelect control scripts and styles
	 *
	 * @since 1.0.7
	 */
	public function enqueue()
	{
		$assets = new Assets();
		$assets->dynamic_select_script();
	}

	/**
	 * Get DynamicSelect control default settings
	 *
	 * @since 1.0.7
	 *
	 * @return array
	 */
	protected function get_default_settings()
	{
		return [
			'options' => [],
			'multiple' => false,
			'select2options' => [],
			'query_slug' => ''
		];
	}

	/**
	 * Retrieve DynamicSelect control content template
	 *
	 * @since 1.0.7
	 */
	public function content_template()
	{
		$control_uid = $this->get_control_uid(); ?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<# var multiple=( data.multiple ) ? 'multiple' : '' ; #>
					<select id="<?php echo esc_attr($control_uid); ?>" class="elementor-control-type-afeb-dynamic-select" {{ multiple }} data-query-slug="{{ data.query_slug }}" data-setting="{{ data.name }}" data-rest-url="<?php echo esc_attr(get_rest_url() . 'afeb-api/v1/dynamic-select' . '/{{data.options}}/'); ?>"></select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
