<?php

namespace AFEB\Controls;

use AFEB\Assets;
use AFEB\Helper;
use Elementor\Base_Data_Control;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * "Vertex Addons for Elementor" ProVersion Controls Class
 * 
 * @class ProVersion
 * @version 1.0.2
 */
class ProVersion extends Base_Data_Control
{
	/**
	 * @var Assets
	 */
	private $assets;

	/**
	 * ProVersion Constructor
	 * 
	 * @since 1.0.2
	 */
	public function __construct($data = [], $args = [])
	{
		parent::__construct($data, $args);
		$this->assets = new Assets();
		$this->assets->pro_version_style();
	}

	/**
	 * Retrieve ProVersion control type
	 *
	 * @since 1.0.2
	 *
	 * @return string Control type
	 */
	public function get_type()
	{
		return 'afeb_pro_v';
	}

	/**
	 * Retrieve ProVersion control content template
	 *
	 * @since 1.0.2
	 */
	public function content_template()
	{
?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-afeb-pro-wrapper">
				<?php echo wp_kses(Helper::pro_badge(), Helper::allowed_tags()); ?>
				<input type="hidden" name="{{ data.name }}" value="{{ data.value }}" data-setting="{{ data.name }}">
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>
		<?php
	}
}
