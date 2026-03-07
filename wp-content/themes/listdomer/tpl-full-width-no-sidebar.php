<?php
/**
 * Template Name: Full Width - No Sidebar
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
get_header();
?>
	<div class="listdomer-header listdomer-header-no-semi-circle">
		<h1><?php the_title(); ?></h1>
	</div>
	<main role="main" class="site-main">
		<?php
			while(have_posts())
			{
				the_post();

				get_template_part('partials/content', 'page');

				// If comments are open, or we have at least one comment, load up the comment template.
				if(comments_open() || get_comments_number())
				{
					comments_template();
				}
			}
		?>
	</main>			
<?php
get_footer();
