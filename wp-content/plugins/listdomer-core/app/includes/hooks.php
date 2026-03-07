<?php

class LSDRC_Hooks extends LSDRC_Base
{
    /**
     * @var LSDRC_Theme
     */
    private $theme;

    public function __construct()
    {
        // Theme
        $this->theme = new LSDRC_Theme();
    }

    public function init()
    {
        // Register Actions
        $this->actions();

        // Register Filters
        $this->filters();

        // Register Widgets
        (new LSDRC_Widgets())->init();

        // Theme Settings
        (new LSDRC_Settings())->init();

        // Elementor
        (new LSDRC_Elementor())->init();

        // OCDI
        (new LSDRC_OCDI())->init();

        // Menus
        (new LSDRC_Menus())->init();
    }

    public function actions()
    {
        add_action('after_setup_theme', [$this->theme, 'register_block_style']);
        add_action('init', [$this->theme, 'register_block_pattern']);

        // Header Button
        add_action('listdomer_header_buttons', [$this->theme, 'header_buttons']);

        // Archive Description
        if (class_exists(LSD_Base::class))
        {
            foreach (LSD_Base::taxonomies() as $tax) add_action($tax . '_archive_description', [$this->theme, 'archive_description']);
        }

        // Include needed assets (CSS, JavaScript etc.) in the WordPress backend
        add_action('admin_enqueue_scripts', function ()
        {
            // Include Listdomer Core backend CSS file for WordPress
            wp_enqueue_style(
                'lsdrc-wp-backend',
                plugins_url() . '/' . LSDRC_DIRNAME . '/assets/css/wp-backend.css',
                [],
                LSDRC_VERSION
            );
        });

        // Redirect to Demo Import
        add_action('admin_init', function ()
        {
            // No need to redirect
            if (!get_option('lsdrc_activation_redirect', false) || wp_doing_ajax()) return;

            // OCDI is Required
            if (!is_plugin_active('one-click-demo-import/one-click-demo-import.php')) return;

            // Delete the option to don't do it again
            delete_option('lsdrc_activation_redirect');

            // Redirect to Listdomer Demo Import
            wp_redirect(admin_url('/admin.php?page=listdomer'));
            exit;
        });

        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post', [$this, 'save'], 10, 2);
    }

    public function filters()
    {
        add_filter('lsd_template', [$this, 'template'], 10, 3);
        add_filter('lsdaddclm_display_verified', '__return_false');
        add_filter('lsdaddclm_claim_text', function ()
        {
            return esc_html__('Claim It Now!', 'listdomer-core');
        });
    }

    public function register()
    {
        // Theme is not activated
        if (!class_exists('LSDR_Headers') || !class_exists('LSDR_Footers')) return;

        add_meta_box('lsdrp-metabox', esc_html__('Listdomer', 'listdomer-core'), [$this, 'metabox'], 'page', 'side', 'high');
    }

    public function metabox($post)
    {
        $headers = LSDR_Headers::all();
        $footers = LSDR_Footers::all();

        $header = get_post_meta($post->ID, 'lsdr_header', true);
        $footer = get_post_meta($post->ID, 'lsdr_footer', true);

        // Nonce
        wp_nonce_field('lsdrp_page', '_lsdrpnonce');
        ?>
        <div class="lsd-metabox lsdrp-metabox">
            <div class="lsd-mb-3">
                <p class="post-attributes-label-wrapper">
                    <label for="lsdr_header">
                        <strong><?php esc_html_e('Header', 'listdomer-core'); ?></strong>
                    </label>
                </p>
                <div>
                    <select name="lsdr[header]" id="lsdr_header" class="widefat">
                        <option value="inherit"><?php esc_html_e('Inherit from global options', 'listdomer-core'); ?></option>
                        <?php foreach ($headers as $key => $name): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php echo $header == $key ? 'selected="selected"' : ''; ?>><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <p class="post-attributes-label-wrapper">
                    <label for="lsdr_footer">
                        <strong><?php esc_html_e('Footer', 'listdomer-core'); ?></strong>
                    </label>
                </p>
                <div>
                    <select name="lsdr[footer]" id="lsdr_footer" class="widefat">
                        <option value="inherit"><?php esc_html_e('Inherit from global options', 'listdomer-core'); ?></option>
                        <?php foreach ($footers as $key => $name): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php echo $footer == $key ? 'selected="selected"' : ''; ?>><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }

    public function save($post_id, $post)
    {
        // It's not a page
        if ($post->post_type !== 'page') return;

        // Nonce is not set!
        if (!isset($_POST['_lsdrpnonce'])) return;

        // Nonce is not valid!
        if (!wp_verify_nonce(sanitize_text_field($_POST['_lsdrpnonce']), 'lsdrp_page')) return;

        // We don't need to do anything on post auto save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        // Get Listdomer Core Data
        $lsdr = $_POST['lsdr'] ?? [];

        $header = isset($lsdr['header']) ? sanitize_text_field($lsdr['header']) : 'inherit';
        update_post_meta($post_id, 'lsdr_header', $header);

        $footer = isset($lsdr['footer']) ? sanitize_text_field($lsdr['footer']) : 'inherit';
        update_post_meta($post_id, 'lsdr_footer', $footer);
    }

    public function template($path, $tpl, $override): string
    {
        $OPath = LSDRC_ABSPATH . '/templates/' . ltrim($tpl, '/');
        if (!is_file($OPath)) return $path;

        if ($override)
        {
            // Main Theme
            $theme = get_template_directory();
            $theme_path = $theme . '/listdom/' . ltrim($tpl, '/');

            if (is_file($theme_path)) $OPath = $theme_path;

            // Child Theme
            if (is_child_theme())
            {
                $child_theme = get_stylesheet_directory();
                $child_theme_path = $child_theme . '/listdom/' . ltrim($tpl, '/');

                if (is_file($child_theme_path)) $OPath = $child_theme_path;
            }
        }

        return $OPath;
    }
}
