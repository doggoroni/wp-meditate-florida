<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php LSDR_Post::thumbnail([300, 300]); ?>

    <div class="entry-content">
        <?php
			the_content();

			wp_link_pages([
				'before' => '<div class="page-links">' . esc_html__('Pages:', 'listdomer'),
				'after' => '</div>',
			]);
        ?>
    </div>
</article>
