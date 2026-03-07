<?php

namespace AFEB\Plugin;

use AFEB\Db;
use AFEB\RewriteRules;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Plugin Hooks Class
 *
 * @class Hooks
 * @version	1.0.0
 */
class Hooks
{
    /**
     * The single instance of the class
     *
     * @var Hooks
     * @since 1.0.0
     */
    protected static $instance = null;
    public $db;

    /**
     * Plugin Hooks Instance
     *
     * @since 1.0.0
     * @return Hooks
     */
    public static function instance(): Hooks
    {
        // Get an instance of Class
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        // Return the instance
        return self::$instance;
    }

    /**
     * Cloning is forbidden
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, esc_html__('Forbidden', 'addons-for-elementor-builder'), '1.0.0');
    }

    /**
     * Un-serializing instances of this class is forbidden
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, esc_html__('Forbidden', 'addons-for-elementor-builder'), '1.0.0');
    }

    /**
     * Constructor method
     */
    protected function __construct()
    {
        register_activation_hook(AFEB_BASENAME, [$this, 'activate']);
        register_deactivation_hook(AFEB_BASENAME, [$this, 'deactivate']);
        register_uninstall_hook(AFEB_BASENAME, [__CLASS__, 'uninstall']);
    }

    /**
     * Runs on plugin activation
     * 
     * @param boolean $network
     *@since 1.0.0
     *
     */
    public function activate(bool $network = false)
    {
        $current_blog_id = get_current_blog_id();

        // Plugin activated only for one blog
        if (!function_exists('is_multisite') || (function_exists('is_multisite') & !is_multisite())) {
            $network = false;
        }
        if (!$network) {
            $this->install($current_blog_id);

            // Add WordPress flush rewrite rules in to do list
            RewriteRules::todo();

            // Don't run rest of the function
            return;
        }

        // DB Class
        $this->db = new DB();

        // Plugin activated for all blogs
        $blogs = $this->db->slct('SELECT `blog_id` FROM `#__blogs`', 'loadColumn');
        foreach ($blogs as $blog_id) {
            switch_to_blog($blog_id);
            $this->install($blog_id);
        }

        switch_to_blog($current_blog_id);

        // Add WordPress flush rewrite rules in to do list
        RewriteRules::todo();
    }

    /**
     * Install the plugin on a certain blog
     * 
     * @param int $blog_id
     *@since 1.0.0
     *
     */
    public function install(int $blog_id = 1)
    {
        // Redirect user to "Vertex Addons for Elementor" Dashboard
        add_option('_afeb_activation_redirect', true);

        // Save Installation Time
        update_option('_afeb_installation_time', time());
    }

    /**
     * Runs on plugin deactivation
     * 
     * @param boolean $network
     *@since 1.0.0
     *
     */
    public function deactivate(bool $network = false)
    {
        /**
         * Refresh WordPress rewrite rules
         */
        flush_rewrite_rules();
    }

    /**
     * Runs on plugin uninstallation
     * 
     * @since 1.0.0
     */
    public static function uninstall()
    {
        // DB Class
        $db = new DB();

        // Getting current blog
        $current_blog_id = get_current_blog_id();

        // Single WordPress Installation
        if (!function_exists('is_multisite') || (function_exists('is_multisite') & !is_multisite())) {
            self::purge($current_blog_id);

            /**
             * Refresh WordPress rewrite rules
             */
            flush_rewrite_rules();

            // Don't run rest of the function
            return;
        }

        // WordPress is multisite, so we should purge the plugin from al blogs
        $blogs = $db->slct('SELECT `blog_id` FROM `#__blogs`', 'loadColumn');
        foreach ($blogs as $blog_id) {
            switch_to_blog($blog_id);
            self::purge($blog_id);
        }

        // Switch back to current blog
        switch_to_blog($current_blog_id);

        /**
         * Refresh WordPress rewrite rules
         */
        flush_rewrite_rules();
    }

    /**
     * Remove plugin from a blog
     * 
     * @param int $blog_id
     *@since 1.0.0
     *
     */
    public static function purge(int $blog_id = 1)
    {
        // Delete the data or not!
        $delete = apply_filters('afeb/purge', true);

        // Plugin Deleted
        if ($delete) {
            delete_option('_afeb_todo_flush');
            delete_option('_afeb_installation_time');
        }
    }
}
