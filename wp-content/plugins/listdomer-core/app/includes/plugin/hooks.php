<?php

class LSDRC_Plugin_Hooks
{
    /**
     * The single instance of the class.
     *
     * @var LSDRC_Plugin_Hooks
     * @since 1.0.0
     */
    protected static $instance = null;

    /**
     * Plugin Hooks Instance.
     *
     * @return LSDRC_Plugin_Hooks
     * @since 1.0.0
     * @static
     */
    public static function instance(): LSDRC_Plugin_Hooks
    {
        // Get an instance of Class
        if (is_null(self::$instance)) self::$instance = new self();

        // Return the instance
        return self::$instance;
    }

    protected function __construct()
    {
        register_activation_hook(LSDRC_BASENAME, [$this, 'activate']);
        register_deactivation_hook(LSDRC_BASENAME, [$this, 'deactivate']);
        register_uninstall_hook(LSDRC_BASENAME, ['LSDRC_Plugin_Hooks', 'uninstall']);
    }

    /**
     * Runs on plugin activation
     * @param bool $network
     */
    public function activate(bool $network = false)
    {
        $current_blog_id = get_current_blog_id();

        // Plugin activated only for one blog
        if (!function_exists('is_multisite') || !is_multisite()) $network = false;
        if (!$network)
        {
            $this->install($current_blog_id);

            // Don't run rest of the function
            return;
        }

        // Plugin activated for all blogs
        $blogs = self::blogs();
        foreach ($blogs as $blog_id)
        {
            switch_to_blog($blog_id);
            $this->install($blog_id);
        }

        switch_to_blog($current_blog_id);
    }

    /**
     * Install the plugin on a certain blog
     * @param int $blog_id
     */
    public function install(int $blog_id = 1)
    {
        // Disable Listdom Activation Redirect
        update_option('lsd_activation_redirect', 0);

        // Disable Vertex Activation Redirect
        update_option('_afeb_activation_redirect', 0);

        // Redirect user to Listdomer Demo Import
        add_option('lsdrc_activation_redirect', true);
    }

    /**
     * Runs on plugin deactivation
     * @param bool $network
     */
    public function deactivate(bool $network = false)
    {
    }

    /**
     * Runs on plugin uninstallation
     */
    public static function uninstall()
    {
        // Getting current blog
        $current_blog_id = get_current_blog_id();

        // Single WordPress Installation
        if (!function_exists('is_multisite') || !is_multisite())
        {
            self::purge($current_blog_id);

            // Don't run rest of the function
            return;
        }

        // WordPress is multisite, so we should purge the plugin from all blogs
        $blogs = self::blogs();
        foreach ($blogs as $blog_id)
        {
            switch_to_blog($blog_id);
            self::purge($blog_id);
        }

        // Switch back to current blog
        switch_to_blog($current_blog_id);
    }

    /**
     * Remove Addon from a blog
     * @param int $blog_id
     */
    public static function purge(int $blog_id = 1)
    {
    }

    /**
     * Get All Blogs
     * @return array
     */
    public static function blogs(): array
    {
        global $wpdb;
        return $wpdb->get_col("SELECT `blog_id` FROM `" . $wpdb->base_prefix . "blogs`");
    }
}
