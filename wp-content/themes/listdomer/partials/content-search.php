<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class((post_password_required() || !has_post_thumbnail()) ? 'lsdr-no-image' : ''); ?>>
    <?php LSDR_Post::thumbnail(); ?>

    <div class="entry-content">
        <header role="banner" class="entry-header">
            <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

            <?php if ('post' === get_post_type()): ?>
                <div class="entry-meta">
                    <?php
                        LSDR_Post::posted_on();
                        LSDR_Post::posted_by();
                    ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div>

        <footer role="contentinfo" class="entry-footer">
            <?php LSDR_Post::footer(); ?>
        </footer>
    </div>
</article>
