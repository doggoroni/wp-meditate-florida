<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Listdomer
 */
get_header();

$words_limit = LSDR_Settings::get('listdomer_navigation_title_word_limit', 5);

$prev_post = get_previous_post();
$next_post = get_next_post();

$prev = $words_limit > 0 && $prev_post ? wp_trim_words(get_the_title($prev_post->ID), $words_limit, '...') : '%title';
$next = $words_limit > 0 && $next_post ? wp_trim_words(get_the_title($next_post->ID), $words_limit, '...') : '%title';
?>
<div id="content" class="site-content container">
	<div class="row">
        <?php if (LSDR_Settings::get('listdomer_single_post_layout') === 'left') get_sidebar(); ?>

		<div class="<?php echo LSDR_Settings::get('listdomer_single_post_layout') === 'full' ? 'col-lg-12' : 'col-lg-8'; ?>">
			<main role="main">
				<?php
					while (have_posts())
					{
						the_post();

						get_template_part('partials/content', get_post_type());

                        if (LSDR_Settings::get('listdomer_display_navigation'))
						{
							the_post_navigation([
								'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'listdomer') . '</span> <span class="nav-title">' . $prev . '</span>',
								'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'listdomer') . '</span> <span class="nav-title">' . $next . '</span>',
							]);
						}

						// If comments are open, or we have at least one comment, load up the comment template.
						if (comments_open() || get_comments_number())
						{
							comments_template();
						}
					}
				?>
			</main>
		</div>
		
		<?php if (LSDR_Settings::get('listdomer_single_post_layout') === 'right') get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
