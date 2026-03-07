<?php

namespace AFEB;

use AFEB\System\Server;
use AFEB\System\WordPress;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" System Class
 * 
 * @class System
 * @version 1.0.0
 */
class System extends Base
{
    /**
     * @var Server
     */
    public $server;

    /**
     * @var WordPress
     */
    public $wp;

    /**
     * Info Constructor
     * 
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->server = new Server();
        $this->wp = new WordPress();
    }
}
