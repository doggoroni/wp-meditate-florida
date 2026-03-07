<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
get_header();
?>

<div class="listdomer-header">
	<?php
		the_archive_title('<h1 class="page-title">', '</h1>');
		the_archive_description('<div class="archive-description">', '</div>');
	?>
</div>

<div id="content" class="site-content container">
	<div class="row">
		<div class="col-lg-12">
			<main role="main">

				<?php if(have_posts()): ?>
					<?php
					/* Start the Loop */
					while(have_posts())
					{
						the_post();

						/*
						 * Include the Post-Type-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
						 */
						get_template_part('partials/content', get_post_type());
					}

					// Print Pagination
					LSDR_Theme::pagination();

				else:

					get_template_part('partials/content', 'none');

				endif;
				?>

			</main>
		</div>
	</div>
</div>

<?php
get_footer();
