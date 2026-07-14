<?php

class LSDRC_OCDI extends LSDRC_Base
{
    protected $cdn = 'https://cdn.webilia.com/u/c/listdomer';

    public function init()
    {
        // Demo Importer
        add_filter('ocdi/import_files', [$this, 'demos']);

        // Plugins
        add_filter('ocdi/register_plugins', [$this, 'plugins']);

        // Before Import
        add_action('ocdi/before_widgets_import', [$this, 'prepare']);
        add_action('ocdi/before_content_import', [$this, 'listdom']);

        // After Import
        add_action('ocdi/after_import', [$this, 'config'], 10);

        // Menu Setup
        add_filter('ocdi/plugin_page_setup', [$this, 'menu']);

        // Page
        add_filter('ocdi/plugin_intro_text', [$this, 'intro']);
        add_filter('ocdi/plugin_page_title', [$this, 'title']);
        add_filter('ocdi/import_successful_buttons', function(array $buttons = [])
        {
            if (!is_array($buttons) || !count($buttons)) return [];

            // Update Theme Settings URL
            if (isset($buttons[0]['href']))
            {
                $buttons[0]['href'] = admin_url('admin.php?page=listdomer-settings');
                $buttons[0]['target'] = '';
            }

            return $buttons;
        });
    }

    /**
     * Register Demos for Demo Importer
     * @return array
     */
    public function demos(): array
    {
        $content = 'content.xml';

        $pro = class_exists(LSD_Base::class) && LSD_Base::isPro();
        if ($pro) $content = 'content-pro.xml';

        return [
            [
                'key' => 'demo1',
                'import_file_name' => esc_html__('General Directory', 'listdomer-core'),
                'categories' => [
                    esc_html__('General', 'listdomer-core'),
                ],
                'import_file_url' => $this->cdn . '/d1/v3/' . $content,
                'import_widget_file_url' => $this->cdn . '/d1/v3/widgets.wie',
                'import_customizer_file_url' => $this->cdn . '/d1/v3/customizer.dat',
                'import_redux' => [
                    [
                        'file_url' => $this->cdn . '/d1/v3/redux.json',
                        'option_name' => 'listdomer_theme_settings',
                    ],
                ],
                'import_preview_image_url' => $this->cdn . '/d1/v3/preview.png',
                'preview_url' => 'https://api.webilia.com/go/listdomer-demo',
                'logo' => $this->cdn . '/d1/v3/logo.png',
                'site_icon' => $this->cdn . '/d1/v3/site-icon.webp',
                'listdom' => $this->cdn . '/d1/v3/listdom.json',
                'elementor' => $this->cdn . '/d1/v3/elementor-kit.zip',
            ],
            [
                'key' => 'demo2',
                'import_file_name' => esc_html__('Real Estate Directory', 'listdomer-core'),
                'categories' => [
                    esc_html__('Real Estate', 'listdomer-core'),
                ],
                'import_file_url' => $this->cdn . '/d2/v3/' . $content,
                'import_widget_file_url' => $this->cdn . '/d2/v3/widgets.wie',
                'import_customizer_file_url' => $this->cdn . '/d2/v3/customizer.dat',
                'import_redux' => [
                    [
                        'file_url' => $this->cdn . '/d2/v3/redux.json',
                        'option_name' => 'listdomer_theme_settings',
                    ],
                ],
                'import_preview_image_url' => $this->cdn . '/d2/v3/preview.png',
                'preview_url' => 'https://api.webilia.com/go/real-estate-demo',
                'logo' => $this->cdn . '/d2/v3/logo.png',
                'site_icon' => $this->cdn . '/d2/v3/site-icon.webp',
                'listdom' => $this->cdn . '/d2/v3/listdom.json',
                'elementor' => $this->cdn . '/d2/v3/elementor-kit.zip',
            ],
            [
                'key' => 'demo3',
                'import_file_name' => esc_html__('Business Directory', 'listdomer-core'),
                'categories' => [
                    esc_html__('General', 'listdomer-core'),
                ],
                'import_file_url' => $this->cdn . '/d3/v3/' . $content,
                'import_widget_file_url' => $this->cdn . '/d3/v3/widgets.wie',
                'import_customizer_file_url' => $this->cdn . '/d3/v3/customizer.dat',
                'import_redux' => [
                    [
                        'file_url' => $this->cdn . '/d3/v3/redux.json',
                        'option_name' => 'listdomer_theme_settings',
                    ],
                ],
                'import_preview_image_url' => $this->cdn . '/d3/v3/preview.png',
                'preview_url' => 'https://api.webilia.com/go/business-directory-demo',
                'logo' => $this->cdn . '/d3/v3/logo.png',
                'site_icon' => $this->cdn . '/d3/v3/site-icon.webp',
                'listdom' => $this->cdn . '/d3/v3/listdom.json',
                'elementor' => $this->cdn . '/d3/v3/elementor-kit.zip',
            ],
            [
                'key' => 'demo4',
                'import_file_name' => esc_html__('Health Directory', 'listdomer-core'),
                'categories' => [
                    esc_html__('Health', 'listdomer-core'),
                ],
                'import_file_url' => $this->cdn . '/d4/v2/' . $content,
                'import_widget_file_url' => $this->cdn . '/d4/v2/widgets.wie',
                'import_customizer_file_url' => $this->cdn . '/d4/v2/customizer.dat',
                'import_redux' => [
                    [
                        'file_url' => $this->cdn . '/d4/v2/redux.json',
                        'option_name' => 'listdomer_theme_settings',
                    ],
                ],
                'import_preview_image_url' => $this->cdn . '/d4/v2/preview.png',
                'preview_url' => 'https://api.webilia.com/go/healthdomer',
                'logo' => $this->cdn . '/d4/v2/logo.png',
                'site_icon' => $this->cdn . '/d4/v2/site-icon.webp',
                'listdom' => $this->cdn . '/d4/v2/listdom.json',
                'elementor' => $this->cdn . '/d4/v2/elementor-kit.zip',
            ],
            [
                'key' => 'demo5',
                'import_file_name' => esc_html__('Travel Directory', 'listdomer-core'),
                'categories' => [
                    esc_html__('Tourism', 'listdomer-core'),
                ],
                'import_file_url' => $this->cdn . '/d5/v2/' . $content,
                'import_widget_file_url' => $this->cdn . '/d5/v2/widgets.wie',
                'import_customizer_file_url' => $this->cdn . '/d5/v2/customizer.dat',
                'import_redux' => [
                    [
                        'file_url' => $this->cdn . '/d5/v2/redux.json',
                        'option_name' => 'listdomer_theme_settings',
                    ],
                ],
                'import_preview_image_url' => $this->cdn . '/d5/v2/preview.png',
                'preview_url' => 'https://api.webilia.com/go/traveldomer',
                'logo' => $this->cdn . '/d5/v2/logo.png',
                'site_icon' => $this->cdn . '/d5/v2/site-icon.webp',
                'listdom' => $this->cdn . '/d5/v2/listdom.json',
                'elementor' => $this->cdn . '/d5/v2/elementor-kit.zip',
            ],
            [
                'key' => 'demo6',
                'import_file_name' => esc_html__('Service Directory', 'listdomer-core'),
                'categories' => [
                    esc_html__('Service', 'listdomer-core'),
                ],
                'import_file_url' => $this->cdn . '/d6/v2/' . $content,
                'import_widget_file_url' => $this->cdn . '/d6/v2/widgets.wie',
                'import_customizer_file_url' => $this->cdn . '/d6/v2/customizer.dat',
                'import_redux' => [
                    [
                        'file_url' => $this->cdn . '/d6/v2/redux.json',
                        'option_name' => 'listdomer_theme_settings',
                    ],
                ],
                'import_preview_image_url' => $this->cdn . '/d6/v2/preview.png',
                'preview_url' => 'https://api.webilia.com/go/servidomer',
                'logo' => $this->cdn . '/d6/v2/logo.png',
                'site_icon' => $this->cdn . '/d6/v2/site-icon.webp',
                'listdom' => $this->cdn . '/d6/v2/listdom.json',
                'elementor' => $this->cdn . '/d6/v2/elementor-kit.zip',
            ],
            [
                'key' => 'demo7',
                'import_file_name' => esc_html__('City Portal', 'listdomer-core'),
                'categories' => [
                    esc_html__('General', 'listdomer-core'),
                    esc_html__('Tourism', 'listdomer-core'),
                ],
                'import_file_url' => $this->cdn . '/d7/v1/' . $content,
                'import_widget_file_url' => $this->cdn . '/d7/v1/widgets.wie',
                'import_customizer_file_url' => $this->cdn . '/d7/v1/customizer.dat',
                'import_redux' => [
                    [
                        'file_url' => $this->cdn . '/d7/v1/redux.json',
                        'option_name' => 'listdomer_theme_settings',
                    ],
                ],
                'import_preview_image_url' => $this->cdn . '/d7/v1/preview.png',
                'preview_url' => 'https://api.webilia.com/go/city-portal',
                'logo' => $this->cdn . '/d7/v1/logo.png',
                'site_icon' => $this->cdn . '/d7/v1/site-icon.webp',
                'listdom' => $this->cdn . '/d7/v1/listdom.json',
                'elementor' => $this->cdn . '/d7/v1/elementor-kit.zip',
            ],
        ];
    }

    public function plugins($plugins): array
    {
        $demo_plugins = [
            [
                'name' => 'Listdom',
                'slug' => 'listdom',
                'required' => true,
                'preselected' => true,
            ],
            [
                'name' => 'Elementor',
                'slug' => 'elementor',
                'required' => true,
                'preselected' => true,
            ],
            [
                'name' => 'Vertex Addons for Elementor',
                'slug' => 'addons-for-elementor-builder',
                'source' => 'https://cdn.webilia.com/u/s/addons-for-elementor-builder.zip',
                'required' => true,
                'preselected' => true,
            ],
        ];

        if (
            (isset($_GET['step']) && $_GET['step'] === 'import')
            || wp_doing_ajax()
        )
        {
            // Real Estate Directory
            if ((isset($_GET['import']) && $_GET['import'] === '1') || wp_doing_ajax())
            {
                $demo_plugins[] = [
                    'name' => 'Listdom Real Estate Toolkit',
                    'description' => esc_html__('A powerful toolkit that equips Listdom with real estate features for modern property listings.', 'listdomer-core'),
                    'slug' => 'listdom-tkre',
                    'source' => 'https://cdn.webilia.com/u/s/listdom-tkre.zip',
                    'required' => true,
                    'preselected' => true,
                ];
            }

            // Business Directory, Healthdomer, Traveldomer, Servidomer
            if ((isset($_GET['import']) && in_array($_GET['import'], ['2', '3', '4', '5', '6'])) || wp_doing_ajax())
            {
                $demo_plugins[] = [
                    'name' => 'Business Toolkit',
                    'description' => esc_html__('A feature-rich toolkit for building and customizing business and listing directories with Listdom.', 'listdomer-core'),
                    'slug' => 'listdom-tkbu',
                    'source' => 'https://cdn.webilia.com/u/s/listdom-tkbu.zip',
                    'required' => true,
                    'preselected' => true,
                ];
            }
        }

        return array_merge($plugins, $demo_plugins);
    }

    public function prepare($demo)
    {
        $sidebars_widgets = get_option('sidebars_widgets');

        $empty_widgets = [];
        foreach ($sidebars_widgets as $sidebar => $widgets) $empty_widgets[$sidebar] = [];

        update_option('sidebars_widgets', $empty_widgets);
    }

    public function listdom($demo)
    {
        /**
         * Import Listdom Settings
         */
        if (class_exists('LSD_IX_Settings') && isset($demo['listdom']) && wp_http_validate_url($demo['listdom']))
        {
            $listdom_path = download_url($demo['listdom'], 60);
            if (!is_wp_error($listdom_path)) LSD_IX_Settings::import($listdom_path);
        }
    }

    public function config($demo)
    {
        // Demo Key
        $key = $demo['key'] ?? 'demo1';

        // Assign Menus
        $main = get_term_by('name', 'Top Menu', 'nav_menu');
        $footer = get_term_by('name', 'Footer', 'nav_menu');

        set_theme_mod('nav_menu_locations', [
            'menu-1' => $main->term_id,
            'menu-2' => $footer->term_id,
        ]);

        // General Demo
        if ($key === 'demo1' && class_exists('LSD_Options'))
        {
            // Assign front page and posts page (blog page)
            $this->wp_pages('Home');

            /**
             * Search Forms
             */
            $this->searches([
                'Home Search Form' => 'Search Results',
            ]);

            /**
             * General Settings
             */
            $this->pages([
                'submission_page' => 'Manage Listings',
            ]);

            /**
             * Listing Category
             */
            $this->listings([
                'Sample House' => 'Real Estate',
                'Sample Auto Repair' => 'Auto Repairs',
                'Sample Restaurant' => 'Restaurant',
                'Sample Digital Agency' => 'Business',
                'Sample Barber Shop' => 'Beauty Salon',
                'Sample Hotel' => 'Hotel',
                'Sample Clinic' => 'Bank',
                'Sample Business' => 'Business',
                'Sample Bank' => 'Bank',
            ]);
        }
        // Real Estate Demo
        else if ($key === 'demo2' && class_exists('LSD_Options'))
        {
            // Assign front page and posts page (blog page)
            $this->wp_pages('Vertical Search Form Homepage');

            /**
             * Search Forms
             */
            $this->searches([
                'Homepage Vertical Search Form' => 'Search Results',
                'Homepage "Rent" Search Form' => 'Search Results',
                'Homepage "Lease" Search Form' => 'Search Results',
                'Homepage "Buy" Search Form' => 'Search Results',
            ]);

            /**
             * General Settings
             */
            $this->pages([
                'submission_page' => 'Dashboard',
            ]);

            /**
             * Listing Category
             */
            $this->listings([
                'Newly Renovated House for Rent' => 'House',
                'Gated Community House for Sale' => 'Apartment',
                'Smart Home with Advanced Features' => 'Apartment',
                'Duplex House in Prime Neighborhood' => 'House',
                'Modern Family Home for Sale' => 'House',
                'Rooftop Restaurant with Great Ambiance' => 'Restaurant',
                'Luxury Resort for Investment' => 'Hotel',
                'Corporate Office for Lease' => 'Office Space',
                'Fine Dining Space Available' => 'Restaurant',
                'Cozy Country Bungalow' => 'Bungalow',
                'Beachfront Bungalow Retreat' => 'Bungalow',
                'Charming Bungalow for Sale' => 'Bungalow',
                'Spacious 2BHK Apartment' => 'Apartment',
                'Studio Apartment for Lease' => 'Bungalow',
                'Serviced Apartment' => 'Apartment',
                'Luxury Condo for Rent' => 'Apartment',
                'Countryside Retreat for Sale' => 'Bungalow',
                'Beachfront Property for Sale' => 'Bungalow',
                'Spacious Townhouse for Sale' => 'Bungalow',
                'Exclusive Penthouse for Lease' => 'House',
                'Luxury Villa for Sale' => 'House',
                'Budget-Friendly Room for Rent' => 'House',
                'Commercial Space for Lease' => 'Hotel',
                'Office Space for Rent' => 'Office Space',
                'Great House For Sale' => 'House',
                'Hanna Restaurant' => 'Restaurant',
                'Famous Peterson House' => 'Condominium',
                'Flat Apartment (VIP)' => 'Apartment',
                'Beautiful House' => 'House',
                'House For Rent' => 'House',
                'Great Opportunity Apply NOW!' => 'Bungalow',
                'Silent Area Apartment' => 'Bungalow',
                'Classic Apartment' => 'Apartment',
                'Marshall House' => 'Bungalow',
                'Hertok Hotel' => 'Hotel',
                'Office Space' => 'Office Space',
            ]);
        }
        // Business Directory Demo
        else if ($key === 'demo3' && class_exists('LSD_Options'))
        {
            // Assign front page and posts page (blog page)
            $this->wp_pages('Home');

            /**
             * Search Forms
             */
            $this->searches([
                'Header Search Form' => 'Search Results',
            ]);

            /**
             * General Settings
             */
            $this->pages([
                'submission_page' => 'Manage Listings',
            ]);

            /**
             * Listing Category
             */
            $this->listings([
                'Sample House' => 'Real Estate',
                'Sample Auto Repair' => 'Auto Repairs',
                'Sample Restaurant' => 'Restaurant',
                'Sample Digital Agency' => 'Business',
                'Sample Barber Shop' => 'Beauty Salon',
                'Sample Hotel' => 'Hotel',
                'Sample Clinic' => 'Bank',
                'Sample Business' => 'Business',
                'Sample Bank' => 'Bank',
            ]);
        }
        // Healthdomer
        else if ($key === 'demo4' && class_exists('LSD_Options'))
        {
            // Assign front page and posts page (blog page)
            $this->wp_pages('Horizontal Search Homepage');

            /**
             * Search Forms
             */
            $this->searches([
                'Categories Homepage - Doctors Search Form' => 'Search Results',
                'Categories Homepage - Hospitals & Clinics Search Form' => 'Search Results',
                'Menu Search Form' => 'Menu Search Results',
                'Horizontal Homepage Search Form' => 'Find Doctors',
            ]);

            /**
             * General Settings
             */
            $this->pages([
                'submission_page' => 'Dashboard',
            ]);

            /**
             * Listing Category
             */
            $this->listings([
                'Dr. Ethan Morales' => 'Orthopedic Surgeons',
                'Heart Care Institute of Los Angeles' => 'Hospitals',
                'Dr. Isabella Conti' => 'Cardiologists',
                'Dr. Farhad Tabrizi' => 'Orthopedic Surgeons',
                'Pacific Orthopedic Center' => 'Clinics',
                'La Jolla Childrens Clinic' => 'Clinics',
                'Dr. Hannah Stein' => 'Pediatricians',
                'Bayview Heart Center' => 'Hospitals',
                'Dr. Marcus Levine' => 'Cardiologists',
                'La Jolla Skin & Aesthetic Clinic' => 'Clinics',
                'Dr. Sofia Rojas' => 'Dermatologist',
                'MotionCare Orthopedic Center' => 'Clinics',
                'Dr. Rajesh Patel' => 'Orthopedic Surgeons',
                'PacificCare Medical Center' => 'Clinics',
                'Dr. Emily Hartman' => 'Cardiologists',
            ]);

            // Theme Footer
            $footer = LSDRC_Base::get_post_by_title('Listdomer Footer Style 4', 'elementor_library');
            if ($footer && isset($footer->ID)) LSDRC_Settings::set('listdomer_footer_type', $footer->ID);

            // Theme Header
            $header = LSDRC_Base::get_post_by_title('Listdomer Header Style 3', 'elementor_library');
            if ($header && isset($header->ID)) LSDRC_Settings::set('listdomer_header_type', $header->ID);
        }
        // Traveldomer
        else if ($key === 'demo5' && class_exists('LSD_Options'))
        {
            // Assign front page and posts page (blog page)
            $this->wp_pages('Vertical Search Form Homepage');

            /**
             * Search Forms
             */
            $this->searches([
                'Menu Search Form' => 'Menu Search Results',
                'Homepage 1 Search Form' => 'Search Results',
                'Categories Homepage "Tours" Search Form' => 'Search Results',
                'Categories Homepage "Attractions" Search Form' => 'Search Results',
                'Categories Homepage "Destinations" Search Form' => 'Search Results',
            ]);

            /**
             * General Settings
             */
            $this->pages([
                'submission_page' => 'Dashboard',
            ]);

            /**
             * Listing Category
             */
            $this->listings([
                'Kelingking Viewpoint' => 'Attractions',
                'Echo Beach Boardwalk' => 'Attractions',
                'Goa Gajah (Elephant Cave)' => 'Attractions',
                'Pura Tirta Empul' => 'Attractions',
                'Garuda Wisnu Kencana Park' => 'Attractions',
                'Jimbaran Fish Market' => 'Attractions',
                'Sanur Beach Promenade' => 'Attractions',
                'Crystal Bay' => 'Attractions',
                'Angel’s Billabong' => 'Attractions',
                'Tegallalang Rice Terraces' => 'Attractions',
                'Suluban Beach Cave' => 'Attractions',
                'Uluwatu Temple Cliff Walk' => 'Attractions',
                'Batu Bolong Beach' => 'Attractions',
                'Tanah Lot Viewpoint' => 'Attractions',
                'Petitenget Temple' => 'Attractions',
                'Seminyak Beach Sunset' => 'Attractions',
                'Sacred Monkey Forest Sanctuary' => 'Attractions',
                'Campuhan Ridge Walk' => 'Attractions',
                'Explore Bali’s Cultural Heart' => 'Destinations',
                'Coastal Vibes of Seminyak' => 'Destinations',
                'Clifftops of Uluwatu' => 'Destinations',
                'Island Escape to Nusa Penida' => 'Destinations',
                'Laid-back Canggu' => 'Destinations',
                'Classic Kuta' => 'Destinations',
                'Island Trails Collective' => 'Tour Companies',
                'Sunset Frames Studio' => 'Tour Companies',
                'Clifftop Walks Co.' => 'Tour Companies',
                'Penida Blue Tours' => 'Tour Companies',
                'Canggu Easy Rides' => 'Tour Companies',
                'Sanur Family Guides' => 'Tour Companies',
                'Jimbaran Local Tastes' => 'Tour Companies',
                'Kadek Suryani (Agent)' => 'Agents',
                'Nengah Adi (Agent)' => 'Agents',
                'Made Wirawan (Local Guide)' => 'Local Guides',
                'Ayu Kartika (Local Guide)' => 'Local Guides',
                'Putra Arya (Local Guide)' => 'Local Guides',
                'Seminyak Café Hopping' => 'Tours & Experiences',
                'Ubud Night Food Crawl' => 'Tours & Experiences',
                'Penida East Viewpoints Trail' => 'Tours & Experiences',
                'Jimbaran Fishing Boats Tour' => 'Tours & Experiences',
                'Sanur Sunrise Paddle' => 'Tours & Experiences',
                'Uluwatu Sunset & Kecak Show' => 'Tours & Experiences',
                'Ubud Artisan Village Hop' => 'Tours & Experiences',
                'Seminyak Family Beach Day' => 'Tours & Experiences',
                'Canggu Rice-Field Cycle' => 'Tours & Experiences',
                'Nusa Penida Manta Snorkel' => 'Tours & Experiences',
                'Ubud Temples & Springs Circuit' => 'Tours & Experiences',
                'Uluwatu Hidden Beaches Walk' => 'Tours & Experiences',
                'Ubud Rice Terrace & Temple Walk' => 'Tours & Experiences',
                'Echo Beach Surf Taster' => 'Tours & Experiences',
                'Tanah Lot Golden Hour Frames' => 'Tours & Experiences',
                'GWK Culture & Sculpture Tour' => 'Tours & Experiences',
                'Jimbaran Seafood Market Walk' => 'Tours & Experiences',
                'Sanur Promenade Family Ride' => 'Tours & Experiences',
                'Crystal Bay Snorkel Session' => 'Tours & Experiences',
                'Penida West Highlights Day Trip' => 'Tours & Experiences',
                'Uluwatu Clifftop Photo Tour' => 'Tours & Experiences',
                'Canggu Food & Murals Stroll' => 'Tours & Experiences',
                'Seminyak Sunset Bike Loop' => 'Tours & Experiences',
                'Sunrise Campuhan Ridge Hike' => 'Tours & Experiences',
            ]);

            // Theme Pages
            $footer = LSDRC_Base::get_post_by_title('Listdomer Footer Style 4', 'elementor_library');
            if ($footer && isset($footer->ID)) LSDRC_Settings::set('listdomer_footer_type', $footer->ID);

            // Header Menu
            $top_menu = get_term_by('name', 'Top Menu', 'nav_menu');
            if ($top_menu && isset($top_menu->term_id)) LSDRC_Settings::set('listdomer_menu_select', $top_menu->term_id);
        }
        // Servidomer
        else if ($key === 'demo6' && class_exists('LSD_Options'))
        {
            // Assign front page and posts page (blog page)
            $this->wp_pages('Homepage 1');

            /**
             * Search Forms
             */
            $this->searches([
                'Homepages 1 Search Form' => 'Search Results',
                'Menu Search Form' => 'Menu Search Results',
                '"Personal Care & Wellness" Search Form - Categories Homepage' => 'Search Results',
                'Event & Wedding Services" Search Form - Categories Homepage' => 'Search Results',
                '"Home Services" Search Form - Categories Homepage' => 'Search Results',
            ]);

            /**
             * General Settings
             */
            $this->pages([
                'submission_page' => 'Dashboard',
            ]);

            /**
             * Listing Category
             */
            $this->listings([
                'ParkSafe Airport Parking Shuttle' => 'Auto & Transport Services',
                'NorthernFleet Van & Truck Hire' => 'Auto & Transport Services',
                'MetroRent Car Rentals' => 'Auto & Transport Services',
                'CrystalRide Auto Detailing' => 'Auto & Transport Services',
                'SkyTow 24/7 Towing' => 'Auto & Transport Services',
                'Cityline Auto Repair' => 'Auto & Transport Services',
                'CineFrame Event Videography' => 'Event & Wedding Services',
                'Bloom & Ribbon Floral Studio' => 'Event & Wedding Services',
                'Midnight Groove DJs' => 'Event & Wedding Services',
                'HarborLights Catering Co.' => 'Event & Wedding Services',
                'Skyline Event Planners' => 'Event & Wedding Services',
                'Lumiere Wedding Photography' => 'Event & Wedding Services',
                'SparkClean Home Cleaning' => 'Home Services',
                'PrimePaint Wall & Decor' => 'Home Services',
                'HandyHive Home Maintenance' => 'Home Services',
                'ComfortAir HVAC & Cooling' => 'Home Services',
                'BrightSpark Electrical Services' => 'Home Services',
                'UrbanFix Plumbing & Repairs' => 'Home Services',
                'CityStride Personal Training' => 'Personal Care & Wellness',
                'Nordic Glow Massage Studio' => 'Personal Care & Wellness',
                'Harmony Wellness Coaching' => 'Personal Care & Wellness',
                'BalancePoint Yoga & Wellness' => 'Personal Care & Wellness',
                'FreshCut Barber Studio' => 'Personal Care & Wellness',
                'GlowSpace Urban Spa' => 'Personal Care & Wellness',
                'UrbanDesk Co-Working Lawyers Hub' => 'Professional Services',
                'Clarity Financial Planning' => 'Professional Services',
                'Summit HR & Recruitment' => 'Professional Services',
                'Skyline Creative Consultants' => 'Professional Services',
                'MapleLeaf Tax & Accounting' => 'Professional Services',
                'Northbridge Legal Advisors' => 'Professional Services',
            ]);
        }
        // City Portal
        else if ($key === 'demo7' && class_exists('LSD_Options'))
        {
            // Assign front page and posts page (blog page)
            $this->wp_pages('Homepage 1');

            /**
             * Search Forms
             */
            $this->searches([
                'Homepages 1 Search Form' => 'Search',
                'Menu Search Form' => 'Search',
                'Homepage Vertical Search Form' => 'Search',
                '"Places & Attractionss" Search Form - Categories Homepage' => 'Search',
                '"Health & Wellness" Search Form - Categories Homepage' => 'Search',
                '"Food & Drink" Search Form - Categories Homepage' => 'Search',
            ]);

            /**
             * General Settings
             */
            $this->pages([
                'submission_page' => 'Dashboard',
            ]);

            /**
             * Listing Category
             */
            $this->listings([
                'Tapas Lab El Born' => 'Food & Drink',
                'Café Brisa Gràcia' => 'Food & Drink',
                'Seafood Terrace Barceloneta' => 'Food & Drink',
                'Night Bites Poble-sec' => 'Food & Drink',
                'Eco Bakery Raval' => 'Food & Drink',
                'Wellness Clinic Eixample' => 'Health & Wellness',
                'Yoga Studio Poblenou' => 'Health & Wellness',
                '24h Pharmacy Central' => 'Health & Wellness',
                'Barcelona City Services Center' => 'Living in the City',
                'Barcelona Public Transport Info Hub' => 'Living in the City',
                'Neighborhood Community Resource Center' => 'Living in the City',
                'BCN Bike Rentals & Tours' => 'Local Services',
                'HomeFix Repairs Barcelona' => 'Local Services',
                'Eco Clean Apartments' => 'Local Services',
                'Sagrada Família Experience Center' => 'Places & Attractions',
                'Park Güell Viewpoint Walk' => 'Places & Attractions',
                'Gothic Quarter Hidden Plazas Tour' => 'Places & Attractions',
                'Montjuïc Castle Panorama' => 'Places & Attractions',
                'Picasso Museum Collection Hub' => 'Places & Attractions',
                'Local Market Finds Santa Caterina' => 'Shopping',
                'Boutique Streetwear Eixample' => 'Shopping',
                'Neighborhood Grocery Gràcia' => 'Shopping',
            ]);
        }

        // Remove Hello World! Post
        $hello = LSDRC_Base::get_post_by_title('Hello World!');
        if ($hello instanceof WP_Post) wp_trash_post($hello->ID);

        // Listings Geo-point
        if (class_exists(LSD_Main::class) && method_exists(LSD_Main::class, 'update_geopoints'))
        {
            LSD_Main::update_geopoints();
        }

        // Elementor Kit
        if (class_exists(\Elementor\Plugin::class) && isset($demo['elementor']) && wp_http_validate_url($demo['elementor']))
        {
            try
            {
                if (!function_exists('download_url')) require_once ABSPATH . 'wp-admin/includes/file.php';

                $kit_path = download_url($demo['elementor'], 60);
                if (!is_wp_error($kit_path))
                {
                    $elementor = \Elementor\Plugin::$instance->app->get_component('import-export');
                    $elementor->import_kit($kit_path, ['referrer' => 'remote']);

                    if ($kit_path && is_string($kit_path) && file_exists($kit_path)) unlink($kit_path);
                }
            }
            catch (Exception $e) {}
        }

        // Import Logo
        if (isset($demo['logo']) && trim($demo['logo']) && class_exists('LSD_IX') && class_exists('LSD_File'))
        {
            $ix = new LSD_IX();
            $logo_id = $ix->attach_by_buffer(LSD_File::download($demo['logo']), basename($demo['logo']));

            set_theme_mod('custom_logo', $logo_id);
        }

        // Import Site Icon
        if (isset($demo['site_icon']) && trim($demo['site_icon']) && class_exists('LSD_IX') && class_exists('LSD_File'))
        {
            $ix = new LSD_IX();
            $icon_id = $ix->attach_by_buffer(LSD_File::download($demo['site_icon']), basename($demo['site_icon']));

            update_option('site_icon', $icon_id, true);
        }

        // Nav Menu Widget
        $nav_menus = get_option('widget_nav_menu', []);
        if (is_array($nav_menus) && count($nav_menus))
        {
            // New Menus
            $new_menus = [];

            foreach ($nav_menus as $nvk => $nav_menu)
            {
                if (!is_numeric($nvk))
                {
                    $new_menus[$nvk] = $nav_menu;
                    continue;
                }

                // Menu
                $title = $nav_menu['title'] ?? '';
                $menu = wp_get_nav_menu_object($title);

                // Add new Menu ID
                if ($menu->term_id) $nav_menu['nav_menu'] = $menu->term_id;

                // New Menus
                $new_menus[$nvk] = $nav_menu;
            }

            // New Menus
            update_option('widget_nav_menu', $new_menus);
        }

        // Listdom Personalized
        if (class_exists('LSD_Personalize')) LSD_Personalize::generate();

        // Listdomer Personalized
        if (class_exists('LSDR_Personalize')) LSDR_Personalize::generate();

        // Permalinks
        $this->permalinks();
    }

    private function searches(array $modules = [])
    {
        if (!class_exists(LSD_Base::class)) return;

        foreach ($modules as $f => $p)
        {
            // Form
            $form = LSDRC_Base::get_post_by_title($f, LSD_Base::PTYPE_SEARCH);

            if ($form && isset($form->ID))
            {
                // Search Results Page
                $results_page = LSDRC_Base::get_post_by_title($p, 'page');

                $options = get_post_meta($form->ID, 'lsd_form', true);

                $options['page'] = $results_page && isset($results_page->ID) ? $results_page->ID : '';
                $options['shortcode'] = '';

                update_post_meta($form->ID, 'lsd_form', $options);
            }
        }
    }

    private function listings(array $listings = [])
    {
        if (!class_exists(LSD_Base::class)) return;

        foreach ($listings as $listing_title => $category_title)
        {
            // Listing
            $listing = LSDRC_Base::get_post_by_title($listing_title, LSD_Base::PTYPE_LISTING);

            // Category
            $category = get_term_by('name', $category_title, LSD_Base::TAX_CATEGORY);

            // Set Category
            if (isset($listing->ID) && isset($category->term_id))
            {
                wp_set_post_terms($listing->ID, is_taxonomy_hierarchical(LSD_Base::TAX_CATEGORY) ? $category->term_id : $category_title, LSD_Base::TAX_CATEGORY);
                update_post_meta($listing->ID, 'lsd_primary_category', $category->term_id);
            }
        }
    }

    private function pages(array $pages = [])
    {
        if (!class_exists('LSD_Options')) return;

        // Listdom Settings
        $settings = LSD_Options::settings();

        foreach ($pages as $k => $p)
        {
            // Page
            $page = LSDRC_Base::get_post_by_title($p, 'page');
            $settings[$k] = $page && isset($page->ID) ? $page->ID : '';
        }

        update_option('lsd_settings', $settings);
    }

    private function wp_pages(string $home)
    {
        $front_page = LSDRC_Base::get_post_by_title($home, 'page');
        $blog_page = LSDRC_Base::get_post_by_title('Blog', 'page');

        update_option('show_on_front', 'page');
        update_option('page_on_front', $front_page->ID);
        update_option('page_for_posts', $blog_page->ID);
    }

    public function permalinks()
    {
        /** @var WP_Rewrite $wp_rewrite */
        global $wp_rewrite;

        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();
    }

    public function menu($setup)
    {
        $setup['parent_slug'] = 'admin.php';
        $setup['page_title'] = esc_html__('Demo Import', 'listdomer-core');
        $setup['menu_title'] = esc_html__('Demo Import', 'listdomer-core');
        $setup['menu_slug'] = 'listdomer';

        return $setup;
    }

    public function intro($intro): string
    {
        return '<div class="ocdi__intro-text"><p class="about-description">'
            . esc_html__('Importing demo data (post, pages, images, theme settings, etc.) is the quickest and easiest way to set up your new theme. It allows you to simply edit everything instead of creating content and layouts from scratch.', 'listdomer-core') .
            '</p>
        </div>';
    }

    public function title($title): string
    {
        return '<div class="ocdi__title-container">
			<h1 class="ocdi__title-container-title">' . esc_html__('Listdomer Demo Importer', 'listdomer-core') . '</h1>
			<a href="https://ocdi.com/user-guide/" target="_blank" rel="noopener noreferrer">
				<img class="ocdi__title-container-icon" src="' . plugins_url() . '/one-click-demo-import/assets/images/icons/question-circle.svg" alt="Questionmark icon">
			</a>
		</div>';
    }
}
