<?php

class LSDR_Post extends LSDR_Base
{
    /**
     * Displays an optional post thumbnail.
     *
     * Wraps the post thumbnail in an anchor element on index views, or a div
     * element when on single views.
     */
    public static function thumbnail()
    {
        if (post_password_required() || is_attachment() || !has_post_thumbnail()) return;

        if (is_singular()):
            ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail(); ?>
            </div>
        <?php else: ?>
            <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
                <?php
                the_post_thumbnail('post-thumbnail', [
                    'alt' => the_title_attribute([
                        'echo' => false,
                    ]),
                ]);
                ?>
            </a>
        <?php
        endif;
    }

    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    public static function posted_on()
    {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
        /* translators: %s: post date. */
            esc_html_x('Posted on %s', 'post date', 'listdomer'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function posted_by()
    {
        $byline = sprintf(
        /* translators: %s: post author. */
            esc_html_x('by %s', 'post author', 'listdomer'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function categories()
    {
        if ('post' === get_post_type())
        {
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list(esc_html__(', ', 'listdomer'));
            if ($categories_list)
            {
                /* translators: 1: list of categories. */
                printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'listdomer') . '</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }
    }

    public static function footer()
    {
        // Hide category and tag text for pages.
        if ('post' === get_post_type() && is_single())
        {
            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html_x(' ', 'list item separator', 'listdomer')); // phpcs:ignore WordPress.WP.I18n.NoEmptyStrings
            if ($tags_list)
            {
                /* translators: 1: list of tags. */
                printf('<span class="tags-links"><span class="tags-links-title">' . esc_html__('Tags:', 'listdomer') . '</span>%1$s</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }
    }
}
