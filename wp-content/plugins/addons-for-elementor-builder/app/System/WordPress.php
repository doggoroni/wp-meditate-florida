<?php

namespace AFEB\System;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" WordPress Class
 * 
 * @class WordPress
 * @version 1.0.0
 */
class WordPress
{
    /**
     * Get memory limit info
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_memory_limit()
    {
        return ini_get('memory_limit');
    }

    /**
     * Get WP version info
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_version()
    {
        return get_bloginfo('version');
    }

    /**
     * Get max upload size info
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_max_upload_size()
    {
        return size_format(wp_max_upload_size());
    }

    /**
     * Get debug mode info
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_debug_mode()
    {
        return WP_DEBUG ? esc_attr__('Enable', 'addons-for-elementor-builder') : esc_attr__('Disable', 'addons-for-elementor-builder');
    }
}
