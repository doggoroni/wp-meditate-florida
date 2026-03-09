<?php
/**
 * Child-theme override: template part for displaying blog post cards on archive pages.
 *
 * @package Listdomer Child
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class((post_password_required() || is_attachment() || !has_post_thumbnail()) ? 'lsdr-no-image' : ''); ?>>
    <?php if (has_post_thumbnail() && !is_singular()) : ?>
        <a href="<?php the_permalink(); ?>" class="mfl-blog-card__thumb" tabindex="-1" aria-hidden="true">
            <?php the_post_thumbnail([600, 340], ['class' => 'mfl-blog-card__img', 'alt' => get_the_title()]); ?>
        </a>
    <?php elseif (is_singular()) : ?>
        <?php LSDR_Post::thumbnail([300, 300]); ?>
    <?php endif; ?>

    <div class="entry-content <?php echo is_singular() ? '' : 'mfl-blog-card__body'; ?>">
        <header role="banner" class="entry-header">
            <?php if (!is_singular()) : ?>
                <div class="mfl-blog-card__meta">
                    <time datetime="<?php echo get_the_date('c'); ?>" class="mfl-blog-card__date">
                        <?php echo get_the_date('F j, Y'); ?>
                    </time>
                </div>
            <?php endif; ?>

            <?php
            if (is_singular()) :
                the_title('<h1 class="entry-title">', '</h1>');
            else :
                the_title(
                    '<h2 class="entry-title mfl-blog-card__title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">',
                    '</a></h2>'
                );
            endif;
            ?>

            <?php if ('post' === get_post_type() && is_singular()) : ?>
                <div class="entry-meta">
                    <?php
                    LSDR_Settings::should_display_element('listdomer_display_author', 'listdomer_archive_display_author') ? LSDR_Post::posted_by() : '';
                    LSDR_Settings::should_display_element('listdomer_display_date', 'listdomer_archive_display_date') ? LSDR_Post::posted_on() : '';
                    ?>
                </div>
            <?php endif; ?>
        </header>

        <?php if (is_singular()) : ?>
            <?php
            the_content(sprintf(
                wp_kses(__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'listdomer'), ['span' => ['class' => []]]),
                wp_kses_post(get_the_title())
            ));
            wp_link_pages(['before' => '<div class="page-links">' . esc_html__('Pages:', 'listdomer'), 'after' => '</div>']);
            ?>
        <?php else : ?>
            <?php if (has_excerpt() || get_the_content()) : ?>
                <div class="mfl-blog-card__excerpt">
                    <?php
                    $excerpt = get_the_excerpt();
                    echo '<p>' . wp_trim_words($excerpt, 25, '&hellip;') . '</p>';
                    ?>
                </div>
            <?php endif; ?>
            <a href="<?php the_permalink(); ?>" class="mfl-blog-card__read-more" aria-label="<?php echo esc_attr('Read more: ' . get_the_title()); ?>">
                Read more <span aria-hidden="true">&rarr;</span>
            </a>
        <?php endif; ?>
    </div>

    <?php if (is_singular()) : ?>
        <footer role="contentinfo" class="entry-footer">
            <?php LSDR_Post::footer(); ?>
        </footer>
    <?php endif; ?>
</article>
