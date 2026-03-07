<?php

namespace AFEB\System;

use AFEB\Db;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Server Class
 * 
 * @class Server
 * @version 1.0.0
 */
class Server
{
    /**
     * Get PHP version info
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_php_version()
    {
        return PHP_VERSION;
    }

    /**
     * Get PHP max post size info
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_php_max_post_size()
    {
        return ini_get('post_max_size');
    }
}
