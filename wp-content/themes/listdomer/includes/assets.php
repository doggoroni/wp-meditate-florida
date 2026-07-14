<?php

class LSDR_Assets extends LSDR_Base
{
    public function init()
    {
        add_action('wp_enqueue_scripts', [$this, 'frontend']);
        add_action('admin_init', [$this, 'backend']);
        add_action('customize_save_after', [LSDR_Personalize::class, 'generate']);
    }

    public function backend()
    {
        // Backend Styles
        wp_enqueue_style('listdomer-backend', get_template_directory_uri() . '/assets/css/backend.min.css', [], $this->version());

        // Editor Styles
        add_editor_style(get_template_directory_uri() . '/assets/css/editor.min.css');

        // Listdomer JavaScript
        wp_enqueue_script('listdomer', get_template_directory_uri() . '/assets/js/backend.min.js', ['jquery'], $this->version(), true);
    }

    public function frontend()
    {
        // Font Awesome
        wp_enqueue_style('fontawesome', get_template_directory_uri() . '/assets/packages/font-awesome/css/fontawesome.all.min.css', [], $this->version());

        // Bootstrap
        wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/packages/bootstrap/css/bootstrap.min.css', [], $this->version());

        // Select2
        wp_enqueue_style('select2', get_template_directory_uri() . '/assets/packages/select2/select2.min.css', [], $this->version());

        // Frontend Styles
        wp_enqueue_style('listdomer', get_template_directory_uri() . '/assets/css/styles.min.css', [], $this->version());
        wp_style_add_data('listdomer', 'rtl', get_template_directory_uri() . '/assets/css/styles-rtl.min.css');

        // Personalize Assets
        $personalize = new LSDR_Personalize();
        $personalize->assets();

        // Popper JavaScript
        wp_enqueue_script('popper', get_template_directory_uri() . '/assets/packages/popper/popper.min.js', [], $this->version(), true);

        // Bootstrap JavaScript
        wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/packages/bootstrap/js/bootstrap.min.js', [], $this->version(), true);

        // Select2 JavaScript
        wp_enqueue_script('select2', get_template_directory_uri() . '/assets/packages/select2/select2.full.min.js', ['jquery'], $this->version(), true);

        // Owl JavaScript
        wp_enqueue_script('owl', get_template_directory_uri() . '/assets/packages/owl-carousel/owl.carousel.min.js', ['jquery'], $this->version(), true);

        // Listdomer JavaScript
        wp_enqueue_script('listdomer', get_template_directory_uri() . '/assets/js/scripts.min.js', ['jquery'], $this->version(), true);

        // Comments
        if (is_singular() && comments_open() && get_option('thread_comments')) wp_enqueue_script('comment-reply');
    }

    public static function version(): string
    {
        $version = LSDR_VERSION;
        if (defined('WP_DEBUG') && WP_DEBUG) $version .= '.' . time();

        return $version;
    }
}
