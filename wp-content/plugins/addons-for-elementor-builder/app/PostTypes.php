<?php

namespace AFEB;

use AFEB\PostTypes\Builder;
use AFEB\PostTypes\Popup;
use AFEB\PostTypes\Submissions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostTypes Class
 * 
 * @class PostTypes
 * @version 1.2.0
 */
class PostTypes extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" PostTypes
     * 
     * @since 1.2.0
     */
    public function init()
    {
        (new Popup())->init();
        (new Builder())->init();
        (new Submissions())->init();
    }
}
