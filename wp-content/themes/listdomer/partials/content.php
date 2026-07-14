<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
?>
<article
    id="post-<?php the_ID(); ?>" <?php post_class((post_password_required() || is_attachment() || !has_post_thumbnail()) ? 'lsdr-no-image' : ''); ?>>
    <?php LSDR_Post::thumbnail([300, 300]); ?>

    <div class="entry-content">
        <header role="banner" class="entry-header">
            <?php if(LSDR_Settings::should_display_element('listdomer_display_category','listdomer_archive_display_category')) :?>
                <div class="entry-categories">
                    <?php LSDR_Post::categories(); ?>
                </div>
            <?php endif; ?>

            <?php
				if (is_singular()) the_title('<h1 class="entry-title">', '</h1>');
				else the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
            ?>

            <?php if ('post' === get_post_type()): ?>
                <div class="entry-meta">
                    <?php
                    LSDR_Settings::should_display_element('listdomer_display_author','listdomer_archive_display_author') ? LSDR_Post::posted_by() : '';
                    LSDR_Settings::should_display_element('listdomer_display_date','listdomer_archive_display_date') ? LSDR_Post::posted_on() : '';
                    ?>
                </div>
            <?php endif; ?>

        </header>

        <?php
			if (is_singular())
			{
                                the_content(sprintf(
                                        /* translators: %s: Post Title. */
                                        wp_kses(__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'listdomer'), [
                                                'span' => [
                                                        'class' => [],
                                                ],
                                        ]),
                                        wp_kses_post(get_the_title())
                                ));
			}

			wp_link_pages([
				'before' => '<div class="page-links">' . esc_html__('Pages:', 'listdomer'),
				'after' => '</div>',
			]);
        ?>
    </div>

    <?php if (is_singular()): ?>
        <footer role="contentinfo" class="entry-footer">
            <?php LSDR_Post::footer(); ?>
        </footer>
    <?php endif; ?>

</article>
