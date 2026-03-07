<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Listdomer
 */
get_header();

$page_title = LSDR_Settings::get('listdomer_404_title');
$page_content = LSDR_Settings::get('listdomer_404_content');
$page_image = LSDR_Settings::get('listdomer_404_image');
$button_text = LSDR_Settings::get('listdomer_404_button_text');
$button_page = LSDR_Settings::get('listdomer_404_button_page');
?>
<div id="content" class="site-content container">
	<div class="row">
		<div class="col-lg-12">
			<main role="main">

				<section class="error-404 not-found">
					
					<img src="<?php echo esc_url($page_image['url'] ?: get_template_directory_uri() . '/assets/img/404.png'); ?>" alt="<?php esc_attr_e('Not Found Page', 'listdomer'); ?>" />
					
					<header role="banner" class="page-header">.
                        <h1 class="page-title"><?php echo $page_title ?: esc_html__('Oops! Error 404.', 'listdomer'); ?> </h1>
                        <?php if (!empty($page_content)): ?><span class="page-message"><?php echo $page_content; ?></span><?php endif; ?>
					</header>

					<div class="page-content">
                        <a href="<?php echo esc_url(get_permalink($button_page ? $button_page : get_option('page_on_front'))); ?>">
							<?php echo $button_text ?: esc_html__('Go to Home', 'listdomer'); ?>
						</a>
					</div>
					
				</section>

			</main>
		</div>
	</div>
</div>
<?php
get_footer();
