<?php
// no direct access
defined('ABSPATH') || die();

// Main HTML Tag
$html_tag = apply_filters(LSD_Base::TAX_FEATURE.'_html_tag', 'div'); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.DynamicHooknameFound

// Title Status
$title_status = apply_filters(LSD_Base::TAX_FEATURE.'_show_title', true); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.DynamicHooknameFound

// Tag Description 
$description = term_description();

/**
 * Listdom Feature Archive Template
 *
 * @author Webilia <hello@webilia.com>
 * @package LSD/Templates
 * @version 1.0.0
 */
get_header('lsd');

$page_title = LSDR_Settings::get('listdomer_page_title_listing_archive_display');
$description_position = LSDR_Settings::get('listdomer_page_desc_listing_archive_position');
$breadcrumb = LSDR_Settings::get('listdomer_listing_breadcrumb_display');
$page_description = $description_position !== 'disable';

$taxonomies = LSDR_Settings::get('listdomer_page_title_listing_taxonomies');
$features = is_array($taxonomies) && isset($taxonomies['features']) && $taxonomies['features'];
?>
<?php if ($features && ($title_status || $description) && ($page_title || $breadcrumb || ($page_description && $description_position === 'before'))): ?>
    <div class="listdomer-header">
        <?php if ($title_status && $page_title): ?>
            <h1><?php echo single_term_title(); ?></h1>
        <?php endif; ?>
        <?php if($breadcrumb): ?>
            <?php do_action('lsdr_breadcrumb'); ?>
        <?php endif; ?>
        <?php if ($description && $page_description && $description_position === 'before'): ?>
            <?php do_action(LSD_Base::TAX_FEATURE.'_archive_description'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
		
<<?php echo esc_attr($html_tag); ?> id="<?php echo esc_attr(apply_filters(LSD_Base::TAX_FEATURE.'_html_id', 'content')); ?>" class="<?php echo esc_attr(apply_filters(LSD_Base::TAX_FEATURE.'_html_class', 'container')); ?>"><?php // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.DynamicHooknameFound ?>

	<div class="lsd-taxonomy-content"><?php do_action(LSD_Base::TAX_FEATURE.'_archive_content'); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.DynamicHooknameFound ?></div>

    <?php if ($description && $page_description && $description_position === 'after'): ?>
        <div class="lsd-page-archive-description">
            <?php do_action(LSD_Base::TAX_FEATURE.'_archive_description'); ?>
        </div>
    <?php endif; ?>

</<?php echo esc_attr($html_tag); ?>>

<?php
get_footer();
