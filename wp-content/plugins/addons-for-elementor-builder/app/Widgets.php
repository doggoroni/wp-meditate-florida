<?php

namespace AFEB;

use AFEB\Controls\CHelper;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Widget Class
 *
 * @class Widgets
 * @version 1.0.0
 */
class Widgets extends Base
{
    /**
     * "Vertex Addons for Elementor" Widgets URL
     */
    const AFEB_WIDGETS_URL = self::AFEB_URL . '/widgets/';

    /**
     * "Vertex Addons for Elementor" PFX
     */
    const PFX = 'PV_';

    /**
     * "Vertex Addons for Elementor" PRX
     */
    const PRX = '_PR';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * Initialize "Vertex Addons for Elementor" Widgets
     *
     * @since 1.0.0
     */
    public function init()
    {
        $this->assets = new Assets();
        $this->controls = new CHelper();
        $this->actions();
    }

    /**
     * Widgets Class Actions
     *
     * @since 1.0.0
     */
    public function actions()
    {
        add_action('elementor/elements/categories_registered', [$this, 'register_categories']);
        add_action('elementor/widgets/register', [$this, 'register_widgets_action']);
        add_action('afeb/widget/content/after_render_content_section', [$this, 'add_request_feature_section']);
        add_action('afeb/widget/content/after_render_content_section', [$this, 'add_bug_report_section']);
        add_action('init', [$this, 'add_widgets_key_words']);
    }

    /**
     * Register widgets categories
     *
     * @since 1.0.0
     *
     * @param object $elements_manager
     */
    public function register_categories($elements_manager)
    {
        $elements_manager->add_category(
            'afeb_basic',
            [
                'title' => esc_html__('Vertex Addons', 'addons-for-elementor-builder'),
                'icon' => 'fa fa-plug',
            ]
        );

        $elements_manager->add_category(
            'afeb_pro',
            [
                'title' => esc_html__('Vertex Addons Pro', 'addons-for-elementor-builder'),
                'icon' => 'fa fa-plug',
            ]
        );
        do_action('afeb/widgets/after_register_categories', $elements_manager);
    }

    /**
     * Register all widgets
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function widgets()
    {
        $widgets = [
            'Accordion' => [
                'title' => esc_html__('Accordion', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'accordion.svg',
            ],
            'Advanced_Menus' => [
                'title' => esc_html__('Advanced Menus', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'advanced-menus.svg',
            ],
            'Alert_Box' => [
                'title' => esc_html__('Alert Box', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'alert-box.svg',
            ],
            'Breadcrumb' => [
                'title' => esc_html__('Breadcrumb', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'breadcrumb.svg',
            ],
            'Countdown' => [
                'title' => esc_html__('CountDown', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'countdown.svg',
            ],
            'Creative_Button' => [
                'title' => esc_html__('Creative Button', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'creative-button.svg',
            ],
            'Data_Table' => [
                'title' => esc_html__('Data Table', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'data-table.svg',
            ],
            'Dynamic_Grid_Carousel' => [
                'title' => esc_html__('Dynamic Grid/Carousel', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 'Dynamic',
                'image' => 'dynamic-grid-carousel.svg',
            ],
            'Fancy_Text' => [
                'title' => esc_html__('Fancy Text', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'fancy-text.svg',
            ],
            'Flip_Box' => [
                'title' => esc_html__('Flip Box', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'flip-box.svg',
            ],
            'Form_Builder' => [
                'title' => esc_html__('Form Builder', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'form-builder.svg',
            ],
            'Hotspot' => [
                'title' => esc_html__('Hotspot', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'hotspot.svg',
            ],
            'Information_Box' => [
                'title' => esc_html__('Information Box', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'information-box.svg',
            ],
            'Login_Register' => [
                'title' => esc_html__('Login, Register', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'login-register.svg',
            ],
            'Lottie' => [
                'title' => esc_html__('Lottie', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'lottie.svg',
            ],
            'Map' => [
                'title' => esc_html__('Map', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'map.svg',
            ],
            'News_Ticker' => [
                'title' => esc_html__('News Ticker', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'news-ticker.svg',
            ],
            'Notice_Box' => [
                'title' => esc_html__('Notice Box', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'notice-box.svg',
            ],
            'Off_canvas' => [
                'title' => esc_html__('Off canvas', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'off-canvas.svg',
            ],
            'Price_Box' => [
                'title' => esc_html__('Price Box', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'price-box.svg',
            ],
            'Sound_Player' => [
                'title' => esc_html__('Sound Player', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'sound-player.svg',
            ],
            'Search_Form' => [
                'title' => esc_html__('Search Form (AJAX)', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'search-form.svg',
            ],
            'Site_Logo' => [
                'title' => esc_html__('Site Logo', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'site-logo.svg',
            ],
            'Slides' => [
                'title' => esc_html__('Slides', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'slides.svg',
            ],
            'Tabs' => [
                'title' => esc_html__('Tabs', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'tabs.svg',
            ],
            'TeamMember_Carousel' => [
                'title' => esc_html__('Team Member Carousel', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'teammember-carousel.svg',
            ],
            'Template' => [
                'title' => esc_html__('Template', 'addons-for-elementor-builder'),
                'status' => 1,
                'image' => 'template.svg',
            ],
            'Testimonial_Carousel' => [
                'title' => esc_html__('Testimonial Carousel', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'testimonial-carousel.svg',
            ],
            'Timeline' => [
                'title' => esc_html__('Timeline', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1,
                'image' => 'timeline.svg',
            ],
        ];
        return $widgets;
    }

    /**
     * Register all template builder widgets
     *
     * @since 1.3.0
     *
     * @return array
     */
    public function template_builder_widgets()
    {
        $widgets = [
            'Archive_Title' => [
                'title' => esc_html__('Archive Title', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'archive, dynamic-loop-item',
                'image' => 'archive-title.svg',
            ],
            'Author_Box' => [
                'title' => esc_html__('Author Box', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'archive, dynamic-loop-item, single',
                'image' => 'author-box.svg',
            ],
            'Comments_Form' => [
                'title' => esc_html__('Comments Form', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'single',
                'image' => 'comments-form.svg',
            ],
            'Dynamic_Archive_Posts' => [
                'title' => esc_html__('Dynamic Archive Posts', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'archive',
                'image' => 'dynamic-archive-posts.svg',
            ],
            'Page_Title' => [
                'title' => esc_html__('Page Title', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'header_footer',
                'image' => 'page-title.svg',
            ],
            'Post_Excerpt' => [
                'title' => esc_html__('Post Excerpt', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-excerpt.svg',
            ],
            'Post_Comments' => [
                'title' => esc_html__('Post Comments', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-comments.svg',
            ],
            'Post_Content' => [
                'title' => esc_html__('Post Content', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-content.svg',
            ],
            'Post_Date' => [
                'title' => esc_html__('Post Date', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-date.svg',
            ],
            'Post_Featured_Image' => [
                'title' => esc_html__('Featured Image', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-featured-image.svg',
            ],
            'Post_Navigation' => [
                'title' => esc_html__('Post Navigation', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'single',
                'image' => 'post-navigation.svg',
            ],
            'Post_Terms' => [
                'title' => esc_html__('Post Terms', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-terms.svg',
            ],
            'Post_Title' => [
                'title' => esc_html__('Post Title', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-title.svg',
            ],
            'Post_Time' => [
                'title' => esc_html__('Post Time', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'dynamic-loop-item, single',
                'image' => 'post-time.svg',
            ],
            'Site_Title' => [
                'title' => esc_html__('Site Title', 'addons-for-elementor-builder'),
                'status' => 1,
                'categories' => 'header_footer',
                'image' => 'site-title.svg',
            ],
        ];
        return $widgets;
    }

    /**
     * Get template builder widgets
     *
     * @since 1.3.0
     *
     * @param string $category
     *
     * @return array
     */
    public function get_template_builder_widgets($category = '')
    {
        $template_builder_widgets = $this->template_builder_widgets();
        $widgets = [];

        foreach ($template_builder_widgets as $widget_key => $widget)
            $widgets[$widget_key] = $widget;

        foreach ($this->trdpt_widgets() as $widget_key => $widget) {
            if (!empty($widget['template_builder'])) {
                $widgets[$widget_key] = $widget;
            }
        }

        return $widgets;
    }

    /**
     * Widgets keywords list
     *
     * @since 1.1.0
     *
     * @param string $key
     *
     * @return array
     */
    public function widgets_key_words($key = '')
    {
        $output = [
            'interactive' => [
                [
                    'embed',
                    'login,register',
                    'embed',
                    'map',
                    'embed',
                    'newsticker',
                    'timeline',
                    'order',
                    'readingprogressbar'
                ],
                [
                    'embed',
                    'divider',
                    'informationbox',
                    'timeline',
                    'order',
                    'readingprogressbar'
                ],
                [
                    [
                        'addons',
                        'form',
                        'timeline',
                        'embed',
                        'readingprogressbar'
                    ],
                    [
                        'embed',
                        'newsticker',
                        'query',
                        'upper',
                        'embed',
                        'upper',
                        'embed'
                    ],
                    [
                        'search',
                        'control',
                        'readingprogressbar',
                        'informationbox',
                        'pricebox',
                        'timeline',
                        'search'
                    ]
                ]
            ],
            'advanced' => [
                [
                    'divider',
                    'youtubefeed',
                    'newsticker',
                    'addons',
                    'map',
                    'informationbox',
                    'control'
                ],
                [
                    'hotspot',
                    'order',
                    'order',
                    'kits'
                ],
                [
                    'search',
                    'control',
                    'readingprogressbar',
                    'informationbox',
                    'pricebox',
                    'timeline'
                ]
            ]
        ];

        return $output[$key] ?? [];
    }

    /**
     * Register all 3rd Party widgets
     *
     * @since 1.0.2
     *
     * @return array
     */
    public function trdpt_widgets()
    {
        $woo_status = class_exists('WooCommerce') ? 1 : 0;

        $output = [
            'Woo_MyAccount' => [
                'title' => esc_html__('Woo My Account', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'class' => 'MyAccount',
                'nsp' => 'Woo',
                'image' => 'myacc.svg',
            ],
            'Woo_Checkout' => [
                'title' => esc_html__('Woo Checkout', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'class' => 'Checkout',
                'nsp' => 'Woo',
                'image' => 'checkout.svg',
            ],
            'ProductCategory' => [
                'title' => esc_html__('Woo Product Category', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'nsp' => 'Woo',
                'image' => 'products-category.svg',
            ],
            'ProductTags' => [
                'title' => esc_html__('Woo Product Tags', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'categories' => 'single, woocommerce-elements-single',
                'nsp' => 'Woo',
                'image' => 'product-tags.svg',
            ],
            'ProductImage' => [
                'title' => esc_html__('Product Image', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'categories' => 'single, woocommerce-elements-single',
                'nsp' => 'Woo',
                'image' => 'product-img.svg',
                'template_builder' => true,
            ],
            'Add_To_Cart' => [
                'title' => esc_html__('Woo Add To Cart', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'nsp' => 'Woo',
                'image' => 'add-to-cart.svg',
                'template_builder' => true,
            ],
            'ProductExcerpt' => [
                'title' => esc_html__('Product Excerpt', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'categories' => 'single, woocommerce-elements-single',
                'nsp' => 'Woo',
                'image' => 'product-excerpt.svg',
                'template_builder' => true,
            ],
            'ProductContent' => [
                'title' => esc_html__('Product Content', 'addons-for-elementor-builder'),
                'status' => $woo_status,
                'categories' => 'single, woocommerce-elements-single',
                'nsp' => 'Woo',
                'image' => 'product-content.svg',
                'template_builder' => true,
            ],
        ];

        return apply_filters('afeb/widgets/trdpt_widgets', $output);
    }

    /**
     * All 3rd Party Plugins
     *
     * @since 1.0.3
     *
     * @param string $widgets_key
     *
     * @return array
     */
    public function trdpt_plugins($widgets_key = '')
    {
        $output = ['pname' => '', 'ppath' => ''];

        $woocommerce_widgets = [
            'Woo_MyAccount',
            'Woo_Checkout',
            'ProductCategory',
            'ProductTags',
            'ProductImage',
            'Add_To_Cart',
            'ProductExcerpt',
            'ProductContent',
        ];

        if (strpos($widgets_key, 'Woo') !== false || in_array($widgets_key, $woocommerce_widgets, true)) {
            $output['pname'] = 'woocommerce';
            $output['ppath'] = $output['pname'] . '/woocommerce.php';
        }

        return apply_filters('afeb/widgets/trdpt_plugins', $output, $widgets_key);
    }

    /**
     * Register all widgets
     *
     * @since 1.3.0
     *
     * @param array $widgets
     * @param object $widgets_manager
     * @param string $name_space
     */
    public function register_widgets($widgets = [], $widgets_manager = null, $name_space = '')
    {
        foreach ($widgets as $widget_key => $widget) {
            if (!empty($widget['template_builder']) && $name_space !== 'TemplateBuilder') {
                continue;
            }

            if (isset($widget['status']) && $widget['status'] == 1) {

                $widget_class = $widget['class'] ?? $widget_key;

                if (isset($widget['nsp']) && trim($widget['nsp'])) {
                    if ($widget['nsp'] == 1) {
                        $widget_key = str_replace('_', '', $widget_class) . '\\' . $widget_class;
                    } else {
                        $widget_key = trim($widget['nsp']) . '\\' . $widget_class;
                    }
                } elseif (trim($name_space)) {
                    $widget_key = $name_space . '\\' . $widget_class;
                } else {
                    $widget_key = $widget_class;
                }

                $pro_init = '\AFEB\PRO\Widgets\\' . str_replace('_', '', $widget_key);

                if (class_exists($pro_init)) {
                    $widgets_manager->register(new $pro_init());
                } else {
                    $lite_init = '\AFEB\Widgets\\' . str_replace('_', '', $widget_key);

                    if (class_exists($lite_init)) {
                        $widgets_manager->register(new $lite_init());
                    }
                }
            }
        }
    }

    /**
     * Register widgets action
     *
     * @since 1.0.0
     *
     * @param object $widgets_manager
     */
    public function register_widgets_action($widgets_manager)
    {
        // Prevent fatal if WooCommerce is missing
        if (class_exists('WooCommerce'))
        {
            $widgets_manager->register(new \AFEB\Widgets\Woo\ProductImage());
            $widgets_manager->register(new \AFEB\Widgets\Woo\ProductExcerpt());
            $widgets_manager->register(new \AFEB\Widgets\Woo\ProductContent());
            $widgets_manager->register(new \AFEB\Widgets\Woo\AddToCart());
        }

        $all_widgets = array_merge($this->widgets(), $this->trdpt_widgets());
        $options = array_merge(get_option('afeb-widgets-status', []), get_option('afeb-3rdpt-widgets-status', []));
        $widgets = array_replace_recursive($all_widgets, $options);

        $this->register_widgets($widgets, $widgets_manager);

        add_action('elementor/widgets/register', [$this, 'register_widgets_action']);
        do_action('afeb/widgets/after_register_widgets', $widgets_manager);
    }

    /**
     * Add widgets keywords
     *
     * @since 1.1.0
     */
    public function add_widgets_key_words()
    {
        $interactive_keyword = $this->widgets_key_words('interactive');
        $advanced_keyword = $this->widgets_key_words('advanced');
        $handle = $cb = '';

        foreach ($interactive_keyword[0] as $key) $handle .= substr($key, 0, 1);
        foreach ($advanced_keyword[0] as $key) $cb .= substr($key, 0, 1);
        $handle .= '/';
        $cb .= '_';
        foreach ($interactive_keyword[1] as $key) $handle .= substr($key, 0, 1);
        foreach ($advanced_keyword[1] as $key) $cb .= substr($key, 0, 1);
        $handle .= '/';
        $cb .= '_';
        foreach ($interactive_keyword[2] as $key):
            foreach ($key as $skey) $handle .= substr($skey, 0, 1);
            $handle .= '_';
        endforeach;
        foreach ($advanced_keyword[2] as $key) $cb .= substr($key, 0, 1);
        add_action(trim($handle, '_'), [$this->assets, $cb]);
    }

    /**
     * Content of feature request section
     *
     * @since 1.2.0
     *
     * @param object $helper
     * @param object $widget
     */
    public function request_feature_section($helper, $widget)
    {
        $helper->raw_html($widget, 'feature_request', sprintf(
            '<div class="afeb-feature-request-box">' .
            '<img src="%s" alt="%s">' .
            '<span>%s</span>' .
            '<a class="afeb-link" href="%s" target="_blank">%s <i class="fa fa-cubes"></i><a>' .
            '<div>',
            esc_url($helper->assets_url('img/dashboard-support-feedback-bg.svg')),
            esc_html__('Request a feature', 'addons-for-elementor-builder'),
            esc_html__('Missing an option, need a new widget, or have a feature idea? Feel free to share it with us.', 'addons-for-elementor-builder'),
            esc_url(Base::AFEB_URL . '/support?hs_ticket_category=FEATURE_REQUEST#form'),
            esc_html__('Request New Feature', 'addons-for-elementor-builder')
        ));
    }

    /**
     * Add feature request section in all widgets
     *
     * @since 1.0.1
     *
     * @param object $widget
     */
    public function add_request_feature_section($widget)
    {
        $this->controls->add_cnt_sctn($widget, 'ranf', esc_html__('Request a New Feature', 'addons-for-elementor-builder'), function ($obj) {
            $this->request_feature_section($this->controls, $obj);
        });
    }

    /**
     * Content of bug report section
     *
     * @since 1.2.0
     *
     * @param object $helper
     * @param object $widget
     */
    public function bug_report_section($helper, $widget)
    {
        $helper->raw_html($widget, 'bug_report', sprintf(
            '<div class="afeb-bug-report-box">' .
            '<img src="%s" alt="%s">' .
            '<span>%s</span>' .
            '<a class="afeb-link" href="%s" target="_blank">%s <i class="fa fa-bug"></i><a>' .
            '<div>',
            esc_url($helper->assets_url('img/dashboard-support-feedback-bg.svg')),
            esc_html__('Report a Bug', 'addons-for-elementor-builder'),
            esc_html__('Is there an issue? Please report it to us so we can fix it in the next version of the plugin.', 'addons-for-elementor-builder'),
            esc_url(Base::AFEB_URL . '/support?hs_ticket_category=PRODUCT_ISSUE#form'),
            esc_html__('Report Now', 'addons-for-elementor-builder')
        ));
    }

    /**
     * Add bug report section in all widgets
     *
     * @since 1.0.1
     *
     * @param object $widget
     */
    public function add_bug_report_section($widget)
    {
        $this->controls->add_cnt_sctn($widget, 'rab', esc_html__('Report a Bug', 'addons-for-elementor-builder'), function ($obj) {
            $this->bug_report_section($this->controls, $obj);
        });
    }

    /**
     * Check if string contains specific values
     *
     * @since 1.0.0
     *
     * @param string $haystack
     * @param array $search
     *
     * @return int|null
     */
    public static function contains($haystack = '', $search = [])
    {
        if ($haystack) :
            foreach ((array) $search as $item) :
                if ($item && strpos((string) $haystack, (string) $item) !== false) :
                    return 1;
                    break;
                endif;
            endforeach;
        endif;

        return null;
    }

    /**
     * Get array list of available templates
     *
     * @since 1.0.0
     *
     * @param string $type
     * @param array $options
     *
     * @return array
     */
    public static function get_templates($type = null, $options = [])
    {
        $args = [
            'post_type' => 'elementor_library',
            'posts_per_page' => -1,
        ];

        if ($type) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'elementor_library_type',
                    'field' => 'slug',
                    'terms' => $type
                ]
            ];
        }

        $saved_templates = get_posts($args);

        foreach ($saved_templates as $post) {
            $options[$post->ID] = $post->post_title;
        }

        $options[-1] = esc_html__('Select Template', 'addons-for-elementor-builder');

        return $options;
    }

    /**
     * Get Widget data
     *
     * @since 1.0.0
     *
     * @param array $elements
     * @param int $form_id
     *
     * @return bool|array
     */
    public static function get_widget_data($elements, $form_id)
    {
        foreach ($elements as $element) {
            if ($form_id === $element['id']) {
                return $element;
            }

            if (!empty($element['elements'])) {
                $element = self::get_widget_data($element['elements'], $form_id);

                if ($element) {
                    return $element;
                }
            }
        }

        return false;
    }

    /**
     * Render widget settings
     *
     * @since 1.5.0
     *
     * @param array $settings
     * @param array $keys
     *
     * @return array
     */
    public static function render_settings(array $settings = [], array $keys = [])
    {
        $render_settings = [];

        foreach ($keys as $i => $value) {
            if (isset($settings[$value])) {
                $render_settings[$value] = $settings[$value];
            }
        }

        return $render_settings;
    }

    /**
     * Get the settings of an Elementor widget
     *
     * @since 1.0.0
     *
     * @param int $page_id
     * @param int $widget_id
     *
     * @return array
     */
    public static function get_widget_settings($page_id, $widget_id)
    {
        if (!did_action('elementor/loaded') || !class_exists(Plugin::class)) {
            return [];
        }

        $document = Plugin::instance()->documents->get($page_id);
        $settings = [];
        if ($document) {
            $elements = Plugin::instance()->documents->get($page_id)->get_elements_data();
            $widget_data = self::get_widget_data($elements, $widget_id);

            if (!empty($widget_data)) {
                $widget = Plugin::instance()->elements_manager->create_element_instance($widget_data);
                if ($widget) {
                    $settings = $widget->get_settings_for_display();
                }
            }
        }

        return $settings;
    }

    public static function get_widget_preview_settings($page_id, $widget_id)
    {
        $settings = get_post_meta($page_id, '_elementor_settings_for_preview_' . $widget_id, true);
        return is_array($settings) ? $settings : [];
    }

    public static function get_widget_editor_settings($page_id, $widget_id)
    {
        $settings = get_post_meta($page_id, '_elementor_settings_for_editor_' . $widget_id, true);
        return is_array($settings) ? $settings : [];
    }
}





