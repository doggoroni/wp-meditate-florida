<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
?>
<?php if (is_singular()): ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
            the_content(sprintf(
                /* translators: %s: Post Title. */
                wp_kses(__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'listdomer'), [
                    'span' => [
                        'class' => [],
                    ],
                ]),
                wp_kses_post(get_the_title())
            ));
        ?>
    </div>
</article>
<?php else: ?>
<article id="post-<?php the_ID(); ?>" <?php post_class((post_password_required() || !has_post_thumbnail()) ? 'lsdr-no-image' : ''); ?>>
    <?php LSDR_Post::thumbnail(); ?>

    <div class="entry-content">
        <header role="banner" class="entry-header">
            <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>') ?>
        </header>
        <?php
            the_content(sprintf(
                /* translators: %s: Post Title. */
                wp_kses(__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'listdomer'), [
                    'span' => [
                        'class' => [],
                    ],
                ]),
                wp_kses_post(get_the_title())
            ));
        ?>
    </div>
</article>
<?php endif;
