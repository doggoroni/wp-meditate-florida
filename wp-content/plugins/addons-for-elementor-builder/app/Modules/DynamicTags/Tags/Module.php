<?php

namespace AFEB\Modules\DynamicTags\Tags;

use AFEB\Modules\DynamicTags\Tags\Archive\Module as ArchiveModule;
use AFEB\Modules\DynamicTags\Tags\Author\Module as AuthorModule;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use AFEB\Modules\DynamicTags\Tags\Site\Module as SiteModule;
use AFEB\Modules\DynamicTags\Tags\Woo\Module as WooModule;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Module Class
 * 
 * @class Module
 * @version 1.3.0
 */
class Module
{
    /**
     * Initialize "Vertex Addons for Elementor" Module
     * 
     * @since 1.3.0
     */
    public function init()
    {
        $this->actions();
    }

    /**
     * Module Class Actions
     * 
     * @since 1.3.0
     */
    public function actions()
    {
        $action = 'elementor/init';
        if (is_admin() && (isset($_GET['elementor_updater']) ||
            isset($_GET['elementor_pro_updater']))) {
            $action = 'elementor/documents/register';
        }

        add_action($action, function () {
            new PostModule();
            new ArchiveModule();
            new SiteModule();
            new AuthorModule();
            new WooModule();
        });
    }
}
