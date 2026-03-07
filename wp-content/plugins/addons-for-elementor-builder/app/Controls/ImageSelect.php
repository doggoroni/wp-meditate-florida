<?php

namespace AFEB\Controls;

use Elementor\Base_Data_Control;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * "Vertex Addons for Elementor" ImageSelect Controls Class
 * 
 * @class ImageSelect
 * @version 1.0.0
 */
class ImageSelect extends Base_Data_Control
{
	/**
	 * Retrieve ImageSelect control type
	 *
	 * @since 1.0.0
	 *
	 * @return string Control type
	 */
	public function get_type()
	{
		return 'afeb_img_slct';
	}

	/**
	 * Retrieve ImageSelect control content template
	 *
	 * @since 1.0.0
	 */
	public function content_template()
	{
		$control_uid = '{{value}}'; ?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-image-selector-wrapper afeb-{{ data.name }}">
				<# _.each( data.options, function( options, value ) { #>
					<input id="<?php $this->print_control_uid($control_uid); ?>" type="radio" name="elementor-image-selector-{{ data.name }}-{{ data._cid }}" value="{{ value }}" data-setting="{{ data.name }}">
					<label class="elementor-image-selector-label tooltip-target afeb-image-selector" for="<?php $this->print_control_uid($control_uid); ?>" data-tooltip="{{ options.title }}" title="{{ options.title }}">
						<img src="{{ options.url }}" alt="{{ options.title }}">
						<span class="elementor-screen-only">{{{ options.title }}}</span>
					</label>
					<# } ); #>
			</div>
		</div>

		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
