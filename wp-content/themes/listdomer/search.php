<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Listdomer
 */
get_header();
?>
<div id="content" class="site-content container">
	<div class="row">
		<div class="col-lg-8">
			<main role="main">
				<?php
				if(have_posts()):

					if(is_home() && !is_front_page()):
						?>
						<header role="banner">
							<h1 class="page-title screen-reader-text">
							<?php
								/* translators: %s: search query. */
								printf(esc_html__('Search Results for: %s', 'listdomer'), '<span>' . get_search_query() . '</span>');
							?>
							</h1>
						</header>
						<?php
					endif;

					/* Start the Loop */
					while(have_posts())
					{
						the_post();

						/*
						 * Include the Post-Type-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
						 */
						get_template_part('partials/content', 'search');
					}

					// Print Pagination
					LSDR_Theme::pagination();

                else:

					get_template_part('partials/content', 'none');

				endif;
				?>

			</main>
		</div>
		<?php
			get_sidebar();
		?>
	</div>
</div>
<?php
get_footer();
