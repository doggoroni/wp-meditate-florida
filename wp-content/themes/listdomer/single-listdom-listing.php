<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Listdomer
 */
get_header();

// Listing Style
$listing_style = method_exists('LSD_PTypes_Listing_Single', 'get_listing_style')
	? LSD_PTypes_Listing_Single::get_listing_style(get_the_ID())
	: '';
?>
<div id="content" class="site-content <?php echo is_numeric($listing_style) ? '' : 'container'; ?>">
	<div class="row">
		<div class="col-lg-12">
			<main role="main">
				<?php
					while(have_posts())
					{
						the_post();

						get_template_part('partials/content-listdom-listing', get_post_type());

						// If comments are open, or we have at least one comment, load up the comment template.
						if(comments_open() || get_comments_number())
						{
							comments_template();
						}
					}
				?>
			</main>
		</div>
	</div>
</div>
<?php
get_footer();
