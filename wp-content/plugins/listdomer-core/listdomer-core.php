<?php
/**
 * Plugin Name: Listdomer Core
 * Plugin URI: https://api.webilia.com/go/listdomer
 * Description: Core Features of Listdomer Theme
 * Version: 4.1.0
 * Author: Webilia
 * Author URI: https://webilia.com/
 * Requires at least: 4.2
 * Tested up to: 6.9
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: listdomer-core
 * Domain Path: /i18n/languages/
 */

// No Direct Access
defined('ABSPATH') || die();

// Plugin Activation / Deactivation / Uninstall
require_once 'plugin.php';

// Init plugin only if current theme is listdomer
if (wp_get_theme()->get_template() === 'listdomer')
{
    require_once 'init.php';

    LSDRC::instance();
}
