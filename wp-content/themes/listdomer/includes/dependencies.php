<?php

class LSDR_Dependencies extends LSDR_Base
{
    public function init()
    {
        // Include TGMPA
        include_once get_template_directory() . '/includes/tgmpa/core.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

        add_action('tgmpa_register', [$this, 'dependencies']);
    }

    public function dependencies()
    {
        // Plugins
        $plugins = [
            [
                'name' => 'Listdomer Core',
                'slug' => 'listdomer-core',
                'required' => true,
                'force_activation' => false,
                'force_deactivation' => false,
            ],
            [
                'name' => 'Listdom',
                'slug' => 'listdom',
                'is_callable' => 'listdom',
                'required' => false,
            ],
            [
                'name' => 'One Click Demo Import',
                'slug' => 'one-click-demo-import',
                'required' => false,
            ],
            [
                'name' => 'Redux Framework',
                'slug' => 'redux-framework',
                'required' => true,
                'force_activation' => false,
                'force_deactivation' => false,
            ],
        ];

        // TGMPA Configuration Options
        $config = [
            'id' => 'listdomer',
            'default_path' => '',
            'menu' => 'listdomer-install-plugins',
            'has_notices' => true,
            'dismissable' => true,
            'dismiss_msg' => '',
            'is_automatic' => true,
            /* translators: %s: Theme Name. */
            'message' => '<p>' . sprintf(__("To use all features of %s theme. You need to install and activate following plugins.", 'listdomer'), '<strong>Listdomer</strong>') . '</p>',
            'strings' => [
                'page_title' => __('Listdomer Plugins', 'listdomer'),
                'menu_title' => __('Listdomer Plugins', 'listdomer'),
            ],
        ];

        tgmpa($plugins, $config);
    }
}
