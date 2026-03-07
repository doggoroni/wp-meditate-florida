<?php

class LSDR_Theme extends LSDR_Base
{
    public function init()
    {
        add_action('after_setup_theme', [$this, 'setup']);
        add_action('after_setup_theme', [$this, 'content_width'], 0);

        add_filter('body_class', [$this, 'body_classes']);
        add_filter('template_include', [$this, 'template']);
        add_action('wp_head', [$this, 'pingback_header']);

        add_action('pre_get_posts', [$this, 'posts_number_per_page']);
        add_filter('get_the_archive_title', [$this, 'filter_archive_title']);
    }

    public function setup()
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         */
        add_theme_support('post-thumbnails');

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        ]);

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        /**
         * Add support for core custom logo.
         */
        add_theme_support('custom-logo', [
            'height' => 250,
            'width' => 250,
            'flex-width' => true,
            'flex-height' => true,
        ]);

        /**
         * Add support for editors style.
         */
        add_theme_support('editor-styles');

        /**
         * Add support for block style.
         */
        add_theme_support('wp-block-styles');

        /**
         * Add support for responsive embeds.
         */
        add_theme_support('responsive-embeds');

        /**
         * Add support for align wide.
         */
        add_theme_support('align-wide');
    }

    public function content_width()
    {
        $GLOBALS['content_width'] = apply_filters('lsdr_content_width', 640); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedVariableFound
    }

    public function body_classes($classes)
    {
        // Adds a class of hfeed to non-singular pages.
        if (!is_singular()) $classes[] = 'hfeed';

        $header = LSDR_Settings::get('listdomer_header_type', 'type1');
        $footer = LSDR_Settings::get('listdomer_footer_type', 'type1');

        // Single Page
        if (is_singular('page'))
        {
            global $post;

            $h = get_post_meta($post->ID, 'lsdr_header', true);
            $f = get_post_meta($post->ID, 'lsdr_footer', true);

            if ($post && isset($post->ID) && $h && $h !== 'inherit') $header = $h;
            if ($post && isset($post->ID) && $f && $f !== 'inherit') $footer = $f;
        }

        // Header & Footer Type
        $classes[] = 'listdomer-header-' . $header;
        $classes[] = 'listdomer-footer-' . $footer;

        return $classes;
    }

    /**
     * Add a pingback url auto-discovery header for single posts, pages, or attachments.
     */
    public function pingback_header()
    {
        if (is_singular() && pings_open())
        {
            printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
        }
    }

    public static function logo($dark = false): string
    {
        $listdomer_logo = LSDR_Settings::get('site_logo');

        if ($dark) $logo = LSDR_Theme::get_dark_logo();
        else if (isset($listdomer_logo['url']) && trim($listdomer_logo['url'])) $logo = '<img class="listdomer-custom-logo" src="' . esc_url_raw($listdomer_logo['url']) . '">';
        else $logo = get_custom_logo();

        if ((is_front_page() || is_home()) && !is_page()) return '<h1 class="site-logo">' . $logo . '</h1>';
        else return '<div class="site-logo">' . $logo . '</div>';
    }

    public static function has_dark_logo(): bool
    {
        return (boolean) get_theme_mod('listdomer_dark_logo');
    }

    public static function get_dark_logo($blog_id = 0): string
    {
        $switched_blog = false;
        if (is_multisite() && !empty($blog_id) && get_current_blog_id() !== (int) $blog_id)
        {
            switch_to_blog($blog_id);
            $switched_blog = true;
        }

        $html = '';
        $dark_logo = get_theme_mod('listdomer_dark_logo');

        // We have a logo. Logo is go.
        if ($dark_logo)
        {
            $dark_logo_id = attachment_url_to_postid($dark_logo);

            $dark_logo_attr = [
                'class' => 'custom-logo',
                'loading' => false,
            ];

            if (is_front_page() && !is_paged())
            {
                /*
                 * If on the home page, set the logo alt attribute to an empty string,
                 * as the image is decorative and doesn't need its purpose to be described.
                 */
                $dark_logo_attr['alt'] = '';
            }
            else
            {
                /*
                 * If the logo alt attribute is empty, get the site title and explicitly pass it
                 * to the attributes used by wp_get_attachment_image().
                 */
                $image_alt = get_post_meta($dark_logo_id, '_wp_attachment_image_alt', true);
                if (empty($image_alt)) $dark_logo_attr['alt'] = get_bloginfo('name', 'display');
            }

            /**
             * Filters the list of custom logo image attributes.
             *
             * @param array $custom_logo_attr Custom logo image attributes.
             * @param int $custom_logo_id Custom logo attachment ID.
             * @param int $blog_id ID of the blog to get the custom logo for.
             * @since 5.5.0
             *
             */
            $dark_logo_attr = apply_filters('get_custom_logo_image_attributes', $dark_logo_attr, $dark_logo_id, $blog_id);

            /*
             * If the alt attribute is not empty, there's no need to explicitly pass it
             * because wp_get_attachment_image() already adds the alt attribute.
             */
            $image = wp_get_attachment_image($dark_logo_id, 'full', false, $dark_logo_attr);

            if (is_front_page() && !is_paged())
            {
                // If on the home page, don't link the logo to home.
                $html = sprintf('<span class="custom-logo-link">%1$s</span>', $image);
            }
            else
            {
                $aria_current = is_front_page() && !is_paged() ? ' aria-current="page"' : '';
                $html = sprintf('<a href="%1$s" class="custom-logo-link" rel="home"%2$s>%3$s</a>', esc_url(home_url('/')), $aria_current, $image);
            }
        }
        else if (is_customize_preview())
        {
            // If no logo is set, but we're in the Customizer, leave a placeholder (needed for the live preview).
            $html = sprintf('<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo" alt="" /></a>', esc_url(home_url('/')));
        }

        // Switch Back
        if ($switched_blog) restore_current_blog();

        return $html;
    }

    public function template($template)
    {
        // Elementor Editor
        if (
            isset($_GET['elementor-preview']) &&
            is_numeric($_GET['elementor-preview'])
        )
        {
            $post = get_post($_GET['elementor-preview']);

            // Listdom Elementor Addon Style
            if (class_exists(\LSDPACELM\Base::class) && $post->post_type === \LSDPACELM\Base::PTYPE_DETAILS)
            {
                $template = locate_template('tpl-canvas.php');
            }

            // Listdomer Header or Footer
            if ($post->post_type === 'elementor_library' && class_exists('LSDRC_Documents_Header') && class_exists('LSDRC_Documents_Footer'))
            {
                $template_type = get_post_meta($post->ID, '_elementor_template_type', true);
                if (in_array($template_type, [
                    LSDRC_Documents_Header::get_type(),
                    LSDRC_Documents_Footer::get_type(),
                ])) $template = locate_template('tpl-canvas.php');
            }
        }

        return $template;
    }

    public static function numeric_pagination(WP_Query $query, string $type = 'list')
    {
        $big = 999999999;
        $args = [
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages,
            'type' => $type,
            'prev_next' => true,
            'prev_text' => '<span>' . __('Previous', 'listdomer') . '</span>',
            'next_text' => '<span>' . __('Next', 'listdomer') . '</span>',
        ];

        return paginate_links($args);
    }

    public function filter_archive_title($title)
    {
        if (!LSDR_Settings::get('listdomer_page_title_include_context', true))
        {
            switch (true)
            {
                case is_category():
                    return single_cat_title('', false);
                case is_tag():
                    return single_tag_title('', false);
                case is_author():
                    return get_the_author();
                case is_post_type_archive():
                    return post_type_archive_title('', false);
                case is_tax():
                    return single_term_title('', false);
            }
        }

        return $title;
    }

    public static function pagination()
    {
        if (LSDR_Settings::get('listdomer_archive_pagination_style') === 'numeric')
        {
            global $wp_query;
            echo LSDR_Theme::numeric_pagination($wp_query);
        }
        else the_posts_navigation();
    }

    public function posts_number_per_page($query)
    {
        if (!$query->is_main_query() || is_admin()) return;

        if (is_archive() || is_home())
        {
            $posts_per_page = LSDR_Settings::get('listdomer_blog_posts_per_page', 6);
            $query->set('posts_per_page', $posts_per_page);
        }
    }
}
