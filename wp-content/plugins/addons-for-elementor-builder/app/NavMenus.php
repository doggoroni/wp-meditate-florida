<?php

namespace AFEB;

use AFEB\NavMenus\Edit;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" General NavMenus Class
 * 
 * @class NavMenus
 * @version 1.3.0
 */
class NavMenus extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" NavMenus
     * 
     * @since 1.3.0
     */
    public function init()
    {
        (new Edit())->init();
    }
}
