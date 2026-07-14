<?php

namespace AFEB;

if (!defined('ABSPATH')) exit;

use AFEB\Controls\DynamicSelect\DynamicSelectAPI;
use AFEB\Plugin\Hooks;

/**
 * Main "Vertex Addons for Elementor" Class
 *
 * @class "Vertex Addons for Elementor"
 * @version 1.0.0
 */
final class Vertex_Addons_For_Elementor
{
    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.6.4';

    /**
     * The single instance of the class
     *
     * @since 1.0.0
     *
     * @var Vertex_Addons_For_Elementor
     */
    protected static $instance = null;

    /**
     * Whether Elementor dependent components are initialized.
     *
     * @var bool
     */
    private $elementor_initialized = false;

    /**
     * Main "Vertex Addons for Elementor" Instance
     *
     * Ensures only one instance of "Vertex Addons for Elementor" is loaded or can be loaded
     *
     * @return Vertex_Addons_For_Elementor
     * @since 1.0.0
     *
     */
    public static function instance(): ?Vertex_Addons_For_Elementor
    {
        // Getting an instance of class
        if (is_null(self::$instance))
        {
            self::$instance = new self();

            do_action('afeb/loaded');
        }

        return self::$instance;
    }

    /**
     * "Vertex Addons for Elementor" Constructor
     *
     * Initializing plugin
     *
     * @since 1.0.0
     */
    protected function __construct()
    {
        $this->constants();
        $this->autoload();

        Hooks::instance();

        $this->actions();

        if (did_action('elementor/loaded')) $this->init();
        else add_action('elementor/loaded', [$this, 'init']);
    }

    /**
     * "Vertex Addons for Elementor" constants
     *
     * Define plugin constants
     *
     * @since 1.0.0
     */
    private function constants()
    {
        // Plugin Absolute Path
        if (!defined('AFEB_ABSPATH'))
            define('AFEB_ABSPATH', dirname(__FILE__));

        // Plugin Directory Name
        if (!defined('AFEB_DIRNAME'))
            define('AFEB_DIRNAME', basename(AFEB_ABSPATH));

        // Plugin Base Name
        if (!defined('AFEB_BASENAME'))
            define('AFEB_BASENAME', plugin_basename(AFEB_ABSPATH . '/addons-for-elementor-builder.php'));

        // Plugin Version
        if (!defined('AFEB_VERSION'))
            define('AFEB_VERSION', $this->version);

        // Slug of Plugin Lite Version
        if (!defined('AFEBP_PRO_VS'))
            define('AFEBP_PRO_VS', 'addons-for-elementor-builder-pro');
    }

    /**
     * "Vertex Addons for Elementor" Vendor DIR
     *
     * Libraries needed for the plugin
     *
     * @since 1.0.0
     */
    public function autoload()
    {
        require_once AFEB_ABSPATH . '/vendor/autoload.php';
    }

    /**
     * Initialize Elementor dependent components once Elementor is ready.
     *
     * @since 1.5.4
     */
    public function init()
    {
        if ($this->elementor_initialized || !did_action('elementor/loaded')) {
            return;
        }

        $this->elementor_initialized = true;

        (new Ajax())->init();
        (new Assets())->init();
        (new I18n())->init();
        (new DynamicSelectAPI())->init();
        (new Controls())->init();
        (new Documents())->init();
        (new Widgets())->init();
        (new Extensions())->init();
        (new Modules())->init();
        (new Handler())->init();
        (new PostTypes())->init();

        if (is_admin())
        {
            (new Menus())->init();
            (new NavMenus())->init();
            (new TemplatesKit())->filters();
        }

        (new RewriteRules())->flush();
    }

    /**
     * AFEB Class Actions
     *
     * @since 1.0.0
     * @access public
     */
    public function actions()
    {
        $base = new Base();
        add_action('admin_init', [$base, 'redirect_after_activation']);
    }
}

/**
 * Main instance of "Vertex Addons for Elementor"
 *
 * Returns the main instance of "Vertex Addons for Elementor" to prevent the need to use globals
 *
 * @return Vertex_Addons_For_Elementor
 * @since 1.0.0
 */
function vertex_addons_for_elementor(): ?Vertex_Addons_For_Elementor
{
    return Vertex_Addons_For_Elementor::instance();
}

// Init the plugin :)
vertex_addons_for_elementor();
