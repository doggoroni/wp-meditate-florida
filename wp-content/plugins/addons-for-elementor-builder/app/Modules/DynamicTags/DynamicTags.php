<?php

namespace AFEB\Modules\DynamicTags;

use AFEB\Modules\DynamicTags\Tags\Module;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" DynamicTags Class
 * 
 * @class DynamicTags
 * @version 1.3.0
 */
class DynamicTags
{
    /**
     * Initialize "Vertex Addons for Elementor" DynamicTags
     * 
     * @since 1.3.0
     */
    public function init()
    {
        (new Module())->init();
    }
}
