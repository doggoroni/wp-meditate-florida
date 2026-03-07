<?php
/**
 * Template Name: Listdomer Canvas
 *
 * @package Listdomer
 */
get_header('blank');
?>
	<main role="main" class="site-main lsdr-canvas">
		<?php
			while(have_posts())
			{
				the_post();

				get_template_part('partials/content', 'page-no-header');

				// If comments are open, or we have at least one comment, load up the comment template.
				if(comments_open() || get_comments_number())
				{
					comments_template();
				}
			}
		?>
	</main>			
<?php
get_footer('blank');