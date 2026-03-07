<?php

use AFEB\Helper;
use AFEB\PostTypes\Builder;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
	exit;
}

Plugin::instance()->frontend->add_body_class('elementor-template-full-width');

get_header();
do_action('elementor/page_templates/header-footer/before_content');

if (
	is_singular(Builder::BUILDER_POST_TYPE) &&
	Helper::get_elementor_template_type(get_the_ID()) == Builder::BUILDER_POST_TYPE
) {
	Plugin::instance()->modules_manager->get_modules('page-templates')->print_content();
} else {
	do_action('elementor/page_templates/canvas/afeb_print_content');
}

do_action('elementor/page_templates/header-footer/after_content');
get_footer();
