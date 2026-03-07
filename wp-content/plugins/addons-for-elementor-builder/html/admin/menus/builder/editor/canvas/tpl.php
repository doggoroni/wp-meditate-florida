<?php

use AFEB\Helper;
use AFEB\PostTypes\Builder;
use Elementor\Plugin;
use Elementor\Utils;

if (!defined('ABSPATH')) {
	exit;
}

Plugin::$instance->frontend->add_body_class('elementor-template-canvas');
$is_preview_mode = Plugin::$instance->preview->is_preview_mode();
$class[] =  $is_preview_mode && class_exists('WooCommerce') ?
	'woocommerce woocommerce-page' : '';
$class[] =  isset($GLOBALS['afeb_document_type']) && $GLOBALS['afeb_document_type'] == 'footer' ?
	'afeb-builder-footer-template' : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<?php if (!current_theme_supports('title-tag')): ?>
		<title>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
			echo wp_get_document_title(); ?>
		</title>
	<?php endif; ?>
	<?php wp_head(); ?>
	<?php Utils::print_unescaped_internal_string(Utils::get_meta_viewport('canvas')); ?>
</head>

<body <?php body_class($class); ?>>
	<?php
	wp_body_open();
	do_action('elementor/page_templates/canvas/before_content');

	if (
		is_singular(Builder::BUILDER_POST_TYPE) &&
		Helper::get_elementor_template_type(get_the_ID()) == Builder::BUILDER_POST_TYPE
	) {
		Plugin::instance()->modules_manager->get_modules('page-templates')->print_content();
	} else {
		do_action('elementor/page_templates/canvas/afeb_print_content');
	}

	do_action('elementor/page_templates/canvas/after_content');
	wp_footer();
	?>
</body>

</html>