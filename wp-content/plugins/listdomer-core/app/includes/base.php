<?php

class LSDRC_Base
{
    public static function kses($html): string
    {
        if (class_exists('LSD_Kses')) return LSD_Kses::page($html);
        else
        {
            $allowed = wp_kses_allowed_html('post');
            return wp_kses($html, $allowed);
        }
    }

    /**
     * @param string $title
     * @param string $post_type
     * @return WP_Post|null
     */
    public static function get_post_by_title(string $title, string $post_type = 'post'): ?WP_Post
    {
        // Query
        $query = new WP_Query([
            'title' => $title,
            'post_type' => $post_type,
            'posts_per_page' => 1,
            'post_status' => ['publish', 'pending', 'draft', 'auto-draft', 'future', 'inherit', 'trash'],
        ]);

        $post = null;
        if ($query->have_posts())
        {
            while ($query->have_posts())
            {
                $query->the_post();
                $post = get_post();
            }
        }

        // Reset Data
        wp_reset_postdata();

        // Return Post
        return $post;
    }

    /**
     * @return WP_Post[]
     */
    public static function get_elementor_headers(): array
    {
        return get_posts([
            'post_type' => 'elementor_library',
            'meta_key' => '_elementor_template_type',
            'meta_value' => LSDRC_Documents_Header::get_type(),
            'posts_per_page' => -1,
        ]);
    }

    public static function get_elementor_footers(): array
    {
        return get_posts([
            'post_type' => 'elementor_library',
            'meta_key' => '_elementor_template_type',
            'meta_value' => LSDRC_Documents_Footer::get_type(),
            'posts_per_page' => -1,
        ]);
    }
}
