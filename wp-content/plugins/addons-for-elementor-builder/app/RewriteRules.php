<?php

namespace AFEB;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Rewrite Rules Class
 * 
 * @class RewriteRules
 * @version 1.0.0
 */
class RewriteRules extends Base
{
    /**
     * Rewrite todo flush
     * 
     * @since 1.0.0
     */
    public static function todo()
    {
        update_option('_afeb_todo_flush', 1);
    }

    /**
     * Rewrite flush
     * 
     * @since 1.0.0
     */
    public function flush()
    {
        // if flush is not needed
        if (!get_option('_afeb_todo_flush', 0)) {
            return;
        }

        add_action('init', array($this, 'perform'));
    }

    /**
     * Rewrite flush rules
     * 
     * @since 1.0.0
     */
    public static function perform()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules(false);

        delete_option('_afeb_todo_flush');
    }
}
