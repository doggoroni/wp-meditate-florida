<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Listdomer
 */

$after_footer = LSDR_Settings::get('listdomer_custom_js', '');
$footer_tracking_codes = LSDR_Settings::get('listdomer_footer_tracking_codes', '');
?>
	</div>
    <?php LSDR_Footers::display(); ?>
</div>

<?php wp_footer(); ?>
<?php if (trim($after_footer)) echo '<script>'.$after_footer.'</script>'; ?>
<?php if (trim($footer_tracking_codes)) echo $footer_tracking_codes; ?>
</body>
</html>
