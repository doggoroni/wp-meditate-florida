<?php
// No Direct Access
defined('ABSPATH') || die();

// Absolute Path
if (!defined('LSDRC_ABSPATH')) define('LSDRC_ABSPATH', dirname(__FILE__));

// Plugin Base Name
if (!defined('LSDRC_BASENAME')) define('LSDRC_BASENAME', plugin_basename(LSDRC_ABSPATH . '/listdomer-core.php'));

// Include
require_once LSDRC_ABSPATH . '/app/includes/plugin/hooks.php';

// Plugin Activation / Deactivation / Uninstall
LSDRC_Plugin_Hooks::instance();
