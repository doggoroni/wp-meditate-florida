<?php

namespace AFEB;

use AFEB\Controls\DynamicSelect\DynamicSelect;
use AFEB\Controls\ImageSelect;
use AFEB\Controls\ProVersion;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
{
    exit;
}

/**
 * "Vertex Addons for Elementor" Controls Class
 *
 * @class Controls
 * @version 1.0.0
 */
class Controls extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" Controls
     *
     * @since 1.0.0
     */
    public function init()
    {
        $this->actions();
    }

    /**
     * Controls Class Actions
     *
     * @since 1.0.0
     */
    public function actions()
    {
        add_action('elementor/controls/register', [$this, 'register_controls']);
    }

    /**
     * Register controls
     *
     * @param Controls_Manager $controls_manager
     * @since 1.0.0
     *
     */
    public function register_controls(Controls_Manager $controls_manager)
    {
        $controls_manager->register(new ImageSelect());
        $controls_manager->register(new ProVersion());
        $controls_manager->register(new DynamicSelect());

        do_action('afeb/widgets/after_register_controls', $controls_manager);
    }
}
