<?php
/**
 * The header for our theme
 *
 * This is the template that displays all the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Listdomer
 */

$after_header = LSDR_Settings::get('listdomer_after_header', '');
$header_tracking_codes = LSDR_Settings::get('listdomer_header_tracking_codes', '');
$preloader = LSDR_Settings::get('listdomer_activate_preloader');
$preloader_logo = LSDR_Settings::get('listdomer_preloader_icon', []);
$loading_style = LSDR_Settings::get('listdomer_preloader_loading_style');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
    <?php if (trim($after_header)) echo '<style>'.$after_header.'</style>'; ?>
    <?php if (trim($header_tracking_codes)) echo $header_tracking_codes; ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'listdomer'); ?></a>
<?php LSDR_Headers::display(); ?>

<?php if ($preloader): ?>
    <div id="listdomer-preloader-box">
        <div id="listdomer-preloader">
            <?php if (is_array($preloader_logo) && isset($preloader_logo['url']) && trim($preloader_logo['url'])): ?>
                <img src="<?php echo esc_url_raw($preloader_logo['url']); ?>" alt="<?php esc_attr_e('Preloader Icon', 'listdomer'); ?>">
            <?php endif; ?>

            <?php if ($loading_style === 'spinner'): ?>
                <div class="loading-spinner">
                    <div></div>
                </div>
            <?php elseif ($loading_style === 'bars'): ?>
                <div class="loading-bars">
                    <span></span><span></span><span></span><span></span>
                </div>
            <?php elseif ($loading_style === 'circle'): ?>
                <div class="loading-circle"></div>
            <?php elseif ($loading_style === 'pulse'): ?>
                <div class="loading-pulse">
                    <div class="pulse"></div>
                    <div class="pulse"></div>
                    <div class="pulse"></div>
                </div>
            <?php elseif ($loading_style === 'dots'): ?>
                <div class="loading-dots">
                    <span></span><span></span><span></span>
                </div>
            <?php elseif ($loading_style === 'multi-spinner'): ?>
                <div class="parent">
                    <div class="loading-multi-spinner"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<div id="page" class="site">
	<div class="listdomer-primary" id="primary">
