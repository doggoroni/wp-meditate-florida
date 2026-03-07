<?php

use AFEB\Modules\DisplayConditions\DisplayConditions;
use AFEB\PostTypes\Popup;
use \Elementor\Plugin;

if (!defined('ABSPATH')) {
	exit;
}

$elementor = Plugin::instance();
$cdnts_match = (new DisplayConditions())->find_matched_results();
$popups = get_posts([
	'numberposts' => -1,
	'include' => !empty($cdnts_match) ? $cdnts_match : [-1],
	'post_type' => Popup::POPUP_POST_TYPE,
	'post_status' => 'publish',
]);

foreach ($popups as $popup):
	$id = $popup->ID;
	$builder_content = $elementor->frontend->get_builder_content($id, false);
	$settings = Popup::get_settings($id);
	$settings = !empty($settings) ? wp_json_encode($settings) : '[]';
?>

	<div id="afeb-popup-id-<?php echo esc_attr($id); ?>"
		class="afeb-template-popup afeb-popup-output" data-settings="<?php echo esc_attr($settings); ?>">

		<div class="afeb-template-popup-inner">

			<!-- Start Popup Overlay -->
			<div class="afeb-popup-overlay"></div>
			<!-- End Popup Overlay -->

			<!-- Start Template Container -->
			<div class="afeb-popup-container">

				<!-- Start Close Button -->
				<div class="afeb-popup-close-btn">
					<i class="eicon-close"></i>
				</div>
				<!-- End Close Button -->

				<!-- Start Template Inner Container -->
				<div class="afeb-popup-container-inner">
					<?php echo $builder_content; ?>
				</div>
				<!-- End Template Inner Container -->

			</div>
			<!-- End Template Container -->

		</div>
	</div>
<?php endforeach; ?>