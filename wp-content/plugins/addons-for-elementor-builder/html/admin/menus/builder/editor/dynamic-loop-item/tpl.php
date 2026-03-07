<?php

if (!defined('ABSPATH')) {
	exit;
}
$elementor_plugin = \Elementor\Plugin::$instance;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
	<?php if (!current_theme_supports('title-tag')): ?>
		<title><?php echo esc_html(wp_get_document_title()); ?></title>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div class="afeb-template-dynamic-loop-item">
		<div class="afeb-template-dynamic-loop-item-inner">

			<!-- Start Template Container -->
			<div class="afeb-dynamic-loop-item-container">

				<!-- Start Template Inner Container -->
				<div class="afeb-dynamic-loop-item-container-inner">
					<?php $elementor_plugin->modules_manager->get_modules('page-templates')->print_content(); ?>
				</div>
				<!-- End Template Inner Container -->

			</div>
			<!-- End Template Container -->

		</div>
	</div>

	<?php wp_footer(); ?>
</body>

</html>