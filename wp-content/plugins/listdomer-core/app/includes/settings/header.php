<?php

class LSDRC_Settings_Header extends LSDRC_Settings
{
    /**
     * Set header settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Header Settings', 'listdomer-core'),
                'id' => 'header_settings',
                'desc' => esc_html__('Options for configuring the header.', 'listdomer-core'),
                'customizer_width' => '400px',
                'parent_id' => 'listdomer',
                'icon' => 'el el-arrow-up',
                'fields' => $this->get_header_fields(),
            ]
        );
    }

    /**
     * Ensures a default search form exists and returns its ID.
     */
    private function init_default_search_form(): string
    {
        if (!class_exists(LSD_Base::class)) return '';

        // Get current Redux option value
        $current_value = LSDRC_Settings::get('listdomer_search_shortcode');
        if (!empty($current_value)) return $current_value;

        $menu_form_id = $this->upsert_search_form(
            esc_html__('Menu Search Form', 'listdomer-core'),
            $this->menu_search_fields()
        );
        if (!$menu_form_id) return '';

        $sidebar_form_id = $this->upsert_search_form(
            esc_html__('Menu Search Sidebar', 'listdomer-core'),
            $this->available_search_fields()
        );

        $shortcode_id = $this->upsert_results_shortcode($sidebar_form_id);
        $page_id = $this->upsert_results_page($shortcode_id);

        if ($page_id && $shortcode_id)
        {
            $form_meta = get_post_meta($menu_form_id, 'lsd_form', true);
            if (!is_array($form_meta)) $form_meta = ['style' => 'default'];

            $form_meta['page'] = $page_id;
            $form_meta['shortcode'] = (string) $shortcode_id;
            update_post_meta($menu_form_id, 'lsd_form', $form_meta);
        }

        $options = get_option($this->opt_name, []);
        $options['listdomer_search_shortcode'] = $menu_form_id;

        update_option($this->opt_name, $options);
        return (string) $menu_form_id;
    }

    /**
     * Creates or retrieves a search form and ensures default fields.
     */
    private function upsert_search_form(string $title, array $fields): int
    {
        if (!class_exists(LSD_Base::class)) return 0;

        $existing = LSD_Base::get_post_by_title($title, LSD_Base::PTYPE_SEARCH);

        if ($existing && !is_wp_error($existing)) $form_id = $existing->ID;
        else $form_id = wp_insert_post(['post_title' => $title, 'post_type' => LSD_Base::PTYPE_SEARCH, 'post_status' => 'publish']);

        if (is_wp_error($form_id) || !$form_id) return 0;

        $current_fields = get_post_meta($form_id, 'lsd_fields', true);
        if (!is_array($current_fields) || !count($current_fields))
        {
            update_post_meta($form_id, 'lsd_fields', $fields);
            update_post_meta($form_id, 'lsd_devices', ['desktop' => ['box' => 0], 'tablet' => ['inherit' => 1], 'mobile' => ['inherit' => 1]]);
        }

        return $form_id;
    }

    /**
     * Default fields for the menu search form.
     */
    private function menu_search_fields(): array
    {
        return [
            1 => [
                'type' => 'row',
                'filters' => [
                    's' => [
                        'key' => 's',
                        'title' => esc_html__('Text Search', 'listdomer-core'),
                        'method' => 'text-input',
                    ],
                ],
                'buttons' => ['status' => 1],
                'clear' => ['status' => 0],
            ],
        ];
    }

    /**
     * Generates all available search fields.
     */
    private function available_search_fields(): array
    {
        if (!class_exists('LSD_Search_Builder')) return [];

        $builder = new LSD_Search_Builder();
        $available = $builder->getAvailableFields();

        $filters = [];
        foreach ($available as $field)
        {
            $methods = $field['methods'] ?? [];
            $method = is_array($methods) ? key($methods) : 'text-input';

            $filters[$field['key']] = [
                'key' => $field['key'],
                'title' => $field['title'],
                'method' => $method,
            ];
        }

        return [
            1 => [
                'type' => 'row',
                'filters' => $filters,
                'buttons' => ['status' => 1],
                'clear' => ['status' => 0],
            ],
        ];
    }

    /**
     * Ensures the search results shortcode exists and is configured.
     */
    private function upsert_results_shortcode(int $search_id): int
    {
        if (!class_exists(LSD_Base::class)) return 0;

        $shortcode = LSD_Base::get_post_by_title('Menu Search Grid', LSD_Base::PTYPE_SHORTCODE);
        if ($shortcode && !is_wp_error($shortcode))
        {
            $shortcode_id = $shortcode->ID;
        }
        else
        {
            $shortcode_id = wp_insert_post([
                'post_title' => esc_html__('Menu Search Grid', 'listdomer-core'),
                'post_type' => LSD_Base::PTYPE_SHORTCODE,
                'post_status' => 'publish',
            ]);

            if (!is_wp_error($shortcode_id) && $shortcode_id)
            {
                update_post_meta($shortcode_id, 'lsd_skin', 'grid');
                update_post_meta($shortcode_id, 'lsd_display', [
                    'skin' => 'grid',
                    'grid' => [
                        'style' => 'style1',
                        'columns' => 2,
                        'limit' => 12,
                        'load_more' => 1,
                        'display_labels' => 1,
                        'display_share_buttons' => 1,
                        'map_provider' => 'googlemap',
                        'map_position' => 'left',
                    ],
                ]);
            }
            else
            {
                $shortcode_id = 0;
            }
        }

        if ($shortcode_id) update_post_meta($shortcode_id, 'lsd_search', ['searchable' => 1, 'shortcode' => $search_id, 'position' => 'left',]);

        return $shortcode_id;
    }

    /**
     * Ensures the search results page exists.
     */
    private function upsert_results_page(int $shortcode_id): int
    {
        if (!$shortcode_id) return 0;

        if (!class_exists(LSD_Base::class)) return 0;

        $page = LSD_Base::get_post_by_title('Menu Search Results', 'page');
        if ($page && !is_wp_error($page)) return $page->ID;

        $page_id = wp_insert_post([
            'post_title' => esc_html__('Menu Search Results', 'listdomer-core'),
            'post_content' => '[listdom id="' . $shortcode_id . '"]',
            'post_type' => 'page',
            'post_status' => 'publish',
        ]);

        return is_wp_error($page_id) ? 0 : $page_id;
    }

    /**
     * Returns the fields for the header settings.
     * @return array Fields array.
     */
    private function get_header_fields(): array
    {
        $default_search = '';
        if (class_exists(LSD_Base::class)) $default_search = $this->init_default_search_form();

        $header_options = class_exists('LSDR_Headers') ? LSDR_Headers::all() : [];

        return [
            // Header Type
            [
                'id' => 'listdomer_header_type',
                'type' => 'select',
                'title' => esc_html__('Header Type', 'listdomer-core'),
                'options' => $header_options,
                'default' => get_theme_mod('listdomer_header_type', 'type1'),
                'subtitle' => esc_html__('You need listdomer pro version to see all header types.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_search_shortcode',
                'type' => 'select',
                'title' => esc_html__('Search Shortcode', 'listdomer-core'),
                'default' => $default_search,
                'subtitle' => esc_html__('Select the search shortcode that will show up in the header', 'listdomer-core'),
                'data' => $this->search_shortcodes(),
                'required' => ['listdomer_header_type', 'equals', 'type6'],
            ],
            [
                'id' => 'listdomer_menu_select',
                'type' => 'select',
                'title' => esc_html__('Select Menu', 'listdomer-core'),
                'options' => wp_list_pluck(wp_get_nav_menus(), 'name', 'term_id'),
                'default' => get_theme_mod('listdomer_menu_select'),
                'desc' => esc_html__('Choose a header menu. It overrides the menu assigned in Appearance > Menus. If left empty, the default menu is used.', 'listdomer-core'),
            ],
            // Dark Logo
            [
                'id' => 'listdomer_dark_logo',
                'type' => 'media',
                'title' => esc_html__('Dark Logo', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_dark_logo', ''),
                'subtitle' => esc_html__('Used in some special header types.', 'listdomer-core'),
            ],
            // Disable Logo Background
            [
                'id' => 'listdomer_logo_bg',
                'type' => 'switch',
                'title' => esc_html__('Logo Background', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_logo_bg', 1),
                'desc' => esc_html__("If your logo doesn't stand out, disable its background color.", 'listdomer-core'),
                'on' => esc_html__('Enable', 'listdomer-core'),
                'off' => esc_html__('Disable', 'listdomer-core'),
            ],
            // Logo Width
            [
                'id' => 'listdomer_logo_width',
                'type' => 'slider',
                'title' => esc_html__('Logo Width', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_logo_width', 160),
                'subtitle' => esc_html__('Set the logo width in pixels.', 'listdomer-core'),
                'min' => 40,
                'step' => 1,
                'max' => 400,
            ],
            // Header Description
            [
                'id' => 'listdomer_header_description',
                'type' => 'switch',
                'title' => esc_html__('Header Tagline', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_description', true),
                'desc' => esc_html__('Show or hide the site description in the header.', 'listdomer-core'),
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_header_description_color',
                'type' => 'color',
                'title' => esc_html__('Header Tagline Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_description_color', '#fff'),
                'subtitle' => esc_html__('Change the color of the header tagline text.', 'listdomer-core'),
                'required' => ['listdomer_header_description', 'equals', true],
            ],
            // Header Background Color
            [
                'id' => 'listdomer_header_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_bg_color', '#ffffff'),
                'subtitle' => esc_html__('Easily change header background color.', 'listdomer-core'),
            ],
            // Header Text Color
            [
                'id' => 'listdomer_header_text_color',
                'type' => 'color',
                'title' => esc_html__('Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_text_color', '#000000'),
                'subtitle' => esc_html__('Easily change header text color.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_header_hover_text_color',
                'type' => 'color',
                'title' => esc_html__('Hover Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_text_color', '#0ab0fe'),
                'subtitle' => esc_html__('Easily change header text color.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_header_active_text_color',
                'type' => 'color',
                'title' => esc_html__('Active Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_text_color', '#0ab0fe'),
                'subtitle' => esc_html__('Easily change header text color.', 'listdomer-core'),
            ],
            // Language Switcher
            [
                'id' => 'listdomer_header_language_switcher',
                'type' => 'switch',
                'title' => esc_html__('Language Switcher', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_language_switcher', true),
                'desc' => esc_html__('Show the WPML or Polylang language switcher in the header. It works only if those plugins are installed and configured.', 'listdomer-core'),
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            // Add Listing Button
            [
                'id' => 'listdomer_header_add_listing',
                'type' => 'switch',
                'title' => esc_html__('Add Listing Button', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_add_listing', true),
                'subtitle' => esc_html__('Links to the add listing form.', 'listdomer-core'),
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_header_add_listing_text',
                'type' => 'text',
                'title' => esc_html__('Add Listing Button Text', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_add_listing_text', esc_html__('Add Listing', 'listdomer-core')),
                'subtitle' => esc_html__('Text shown on the add listing button.', 'listdomer-core'),
                'required' => ['listdomer_header_add_listing', 'equals', true],
            ],
            // User Login/Register
            [
                'id' => 'listdomer_header_logister',
                'type' => 'switch',
                'title' => esc_html__('Login & Register', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_header_logister', true),
                'subtitle' => esc_html__('Show the login and register form in the header.', 'listdomer-core'),
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            // Login Redirect Page
            [
                'id' => 'listdomer_login_redirect',
                'type' => 'select',
                'title' => esc_html__('Login Redirection Page', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_login_redirect', true),
                'subtitle' => esc_html__('Page to redirect users after login.', 'listdomer-core'),
                'data' => 'pages',
            ],
            // Registration Redirect Page
            [
                'id' => 'listdomer_register_redirect',
                'type' => 'select',
                'title' => esc_html__('Register Redirection Page', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_register_redirect', true),
                'desc' => esc_html__('Page to redirect users after registration. Use it to thank them and guide them.', 'listdomer-core'),
                'data' => 'pages',
            ],
            [
                'id' => 'listdomer_social_login_shortcode',
                'type' => 'textarea',
                'title' => esc_html__('Social Login Shortcode', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_social_login_shortcode', ''),
                'desc' => esc_html__('Add social login shortcodes here. Configure the social plugin first or nothing will show.', 'listdomer-core'),
            ],
        ];
    }

    public function search_shortcodes(): array
    {
        if (!class_exists(LSD_Base::class)) return [];

        return wp_list_pluck(
            get_posts([
                'post_type' => LSD_Base::PTYPE_SEARCH,
                'posts_per_page' => -1,
                'post_status' => 'publish',
            ]),
            'post_title',
            'ID'
        );
    }
}
