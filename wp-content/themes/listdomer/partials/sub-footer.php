<?php
/**
 * Template part for displaying sub-footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Listdomer
 */

$subfooter_menu = wp_nav_menu([
    'theme_location' => 'menu-2',
    'menu' => LSDR_Settings::get('listdomer_footer_menu_select', 'subfooter-menu'),
    'menu_id' => 'subfooter-menu',
    'echo' => false,
]);
$copyright_text = LSDR_Settings::get('listdomer_footer_copyright_text');
?>
<div class="container">
    <div class="site-subfooter">
        <?php if (trim($subfooter_menu)): ?>
            <nav id="site-subfooter-navigation" class="subfooter-navigation" role="navigation"
                 aria-label="<?php esc_attr_e('Sub Footer ', 'listdomer'); ?>">
                <?php echo $subfooter_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </nav>
        <?php endif; ?>

        <div class="site-info">
            <?php echo empty($copyright_text) ? sprintf(esc_html__('%1$s %2$s %3$s. All rights reserved.', 'listdomer'), '&copy;', current_time('Y'), get_bloginfo('name', 'display')) : esc_html($copyright_text); ?>
        </div>
    </div>
</div>
