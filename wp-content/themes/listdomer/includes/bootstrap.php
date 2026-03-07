<?php

final class LSDR_Bootstrap
{
    /**
     * Version.
     *
     * @var string
     */
    public $version = '3.2.0';

    /**
     * The single instance of the class.
     *
     * @var LSDR_Bootstrap
     * @since 1.0.0
     */
    protected static $instance = null;

    /**
     * Ensures only one instance of Theme is loaded or can be loaded.
     *
     * @return LSDR_Bootstrap - Main instance.
     * @since 1.0.0
     * @static
     */
    public static function instance(): LSDR_Bootstrap
    {
        // Get an instance of Class
        if (is_null(self::$instance)) self::$instance = new self();

        // Return the instance
        return self::$instance;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        // Define Constants
        $this->define_constants();

        // Auto Loader
        spl_autoload_register([$this, 'autoload']);

        // Initialize the Theme
        $this->init();

        // Include Helper Functions
        $this->helpers();
    }

    /**
     * Define Theme Constants.
     */
    private function define_constants()
    {
        // Theme Version
        if (!defined('LSDR_VERSION')) define('LSDR_VERSION', $this->version);
    }

    /**
     * Initialize the Theme
     */
    private function init()
    {
        // I18n
        (new LSDR_I18n())->init();

        // Theme
        (new LSDR_Theme())->init();

        // Assets
        (new LSDR_Assets())->init();

        // Navs
        (new LSDR_Navs())->init();

        // Sidebars
        (new LSDR_Sidebars())->init();

        // Jetpack
        (new LSDR_Jetpack())->init();

        // Dependencies
        (new LSDR_Dependencies())->init();

        // Headers
        (new LSDR_Headers())->init();

        // Footers
        (new LSDR_Footers())->init();
    }

    /**
     * Include Helper Functions
     */
    public function helpers()
    {
        // Util Functions
        require_once get_template_directory() . '/includes/helpers/util.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
    }

    /**
     * Automatically load theme classes whenever needed.
     * @param string $class_name
     * @return void
     */
    private function autoload(string $class_name)
    {
        $class_ex = explode('_', strtolower($class_name));

        // It's not a Listdomer Class
        if ($class_ex[0] !== 'lsdr') return;

        // Drop 'LSDR'
        $class_path = array_slice($class_ex, 1);

        // Create Class File Path
        $file_path = get_template_directory() . '/includes/' . implode('/', $class_path) . '.php';

        // We found the class!
        if (file_exists($file_path)) require_once $file_path; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
    }
}

