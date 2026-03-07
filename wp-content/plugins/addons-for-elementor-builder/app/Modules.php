<?php

namespace AFEB;

use AFEB\Modules\DisplayConditions\DisplayConditions;
use AFEB\Modules\DynamicTags\DynamicTags;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Modules Class
 * 
 * @class Modules
 * @version 1.2.0
 */
class Modules extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" Modules
     * 
     * @since 1.2.0
     */
    public function init()
    {
        (new DisplayConditions())->init();
        (new DynamicTags())->init();
    }
}
