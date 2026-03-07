<?php

namespace AFEB;

use AFEB\Handler\Widgets\FormBuilderHandler;
use AFEB\Handler\Widgets\LoginRegisterHandler;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" General Handler Class
 * 
 * @class Handler
 * @version 1.0.3
 */
class Handler extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" Handler
     * 
     * @since 1.0.3
     */
    public function init()
    {
        (new LoginRegisterHandler())->init();
        (new FormBuilderHandler())->init();
    }
}
