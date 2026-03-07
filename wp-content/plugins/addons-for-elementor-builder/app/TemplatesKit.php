<?php

namespace AFEB;

use AFEB\PostTypes\Builder;
use AFEB\PostTypes\Popup;
use Elementor\Plugin;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" General TemplatesKit Class
 * 
 * @class TemplatesKit
 * @version 1.4.0
 */
class TemplatesKit extends Base
{
    /**
     * TemplatesKit Class Filters
     * 
     * @since 1.4.0
     */
    public function filters()
    {
        if (version_compare(get_bloginfo('version'), '5.1.0', '>='))
            add_filter('http_request_timeout', [$this, 'set_image_request_timeout'], 10, 2);

        if (version_compare(get_bloginfo('version'), '5.1.0', '>=')) {
            add_filter('wp_check_filetype_and_ext', function (
                $defaults,
                $file,
                $filename,
                $mimes,
                $real_mime
            ) {
                return $this->wp_check_filetype_and_ext($defaults, $filename);
            }, 10, 5, 99);
        } else {
            add_filter('wp_check_filetype_and_ext', function (
                $defaults,
                $file,
                $filename,
                $mimes
            ) {
                return $this->wp_check_filetype_and_ext($defaults, $filename);
            }, 10, 4);
        }
    }

    /**
     * All kits
     *
     * @since 1.4.0
     *
     * @return array
     */
    public static function kits()
    {
        return [
            'nonprofit-organization' => [
                'v1' => [
                    'name' => __('Nonprofit Organization', 'addons-for-elementor-builder'),
                    'description' => __('A demo to present your beautiful content on your site. Import & enjoy', 'addons-for-elementor-builder'),
                    'pages_title' => [
                        'home' => esc_html__('Home', 'addons-for-elementor-builder'),
                        'about-us' => esc_html__('About Us', 'addons-for-elementor-builder'),
                        'voices-of-change' => esc_html__('Voices of Change', 'addons-for-elementor-builder'),
                        'mission-in-action' => esc_html__('Mission in Action', 'addons-for-elementor-builder'),
                        'contact' => esc_html__('Contact', 'addons-for-elementor-builder'),
                        'blog-archive' => esc_html__('Blog Archive', 'addons-for-elementor-builder'),
                        'single-blog' => esc_html__('Single Blog', 'addons-for-elementor-builder'),
                    ],
                    'pages_meta' => [
                        'blog-archive' => 'category/foundation-posts',
                        'single-blog' => 'making-a-difference-one-step-at-a-time',
                    ],
                    'plugins' => [],
                    'tags' => '',
                    'theme_builder' => true,
                    'priority' => 1,
                ],
            ],
        ];
    }

    /**
     * Set Timeout for Image Request
     * 
     * @param int $timeout_value
     * @param string $url
     * 
     * @since 1.4.0
     * 
     * @return int
     */
    public function set_image_request_timeout($timeout_value, $url)
    {
        if (strpos($url, Base::AFEB_URL . '/') === false)
            return $timeout_value;

        $valid_ext = preg_match('/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-\@]+\.(jpg|png|gif|jpeg|svg)\/?$/i', $url);

        if ($valid_ext)
            $timeout_value = 300;

        return $timeout_value;
    }

    /**
     * Enable xml for demo importer
     * 
     * @param array $defaults
     * @param string $filename
     * 
     * @since 1.4.0
     * 
     * @return array
     */
    public function wp_check_filetype_and_ext($defaults, $filename)
    {
        if (strpos($filename, 'main') !== false) {
            $defaults['ext']  = 'xml';
            $defaults['type'] = 'text/xml';
        }

        return $defaults;
    }

    /**
     * Download Template
     * 
     * @param string $kit_id
     * 
     * @since 1.4.0
     * 
     * @return string
     */
    public function download_template($kit_id)
    {
        $rand_number = substr(str_shuffle('0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ'), 0, 7);
        $remote_file_url = 'https://cdn.webilia.com/u/c/vertex/' . $kit_id . '/main.xml?=' . $rand_number;

        if (!function_exists('download_url'))
            require_once ABSPATH . 'wp-admin/includes/file.php';

        $tmp_file = download_url($remote_file_url);

        if (is_wp_error($tmp_file)) {
            wp_send_json_error(['message' => esc_html__('Error: Import File download failed.', 'addons-for-elementor-builder'),]);
        }

        $args = [
            'name' => 'main.xml',
            'tmp_name' => $tmp_file,
            'error' => 0,
            'size' => filesize($tmp_file),
        ];

        $defaults = [
            'test_form' => false,
            'test_size' => true,
            'test_upload' => true,
            'mimes' => [
                'xml' => 'text/xml',
            ],
            'wp_handle_sideload' => 'upload',
        ];

        $local_file = wp_handle_sideload($args, $defaults);

        if (isset($local_file['error']))
            wp_send_json_error(['message' => esc_html__('Import File upload failed.', 'addons-for-elementor-builder'),]);

        return $local_file['file'];
    }

    /**
     * Setup Templates
     * 
     * @param string $kit_id
     * 
     * @since 1.4.0
     */
    public function setup_templates($kit_id)
    {
        $kit_id = isset($kit_id) ? sanitize_text_field(wp_unslash($kit_id)) : '';

        $home_page = get_page_by_path('home-' . $kit_id);
        $blog_page = get_page_by_path('blog-' . $kit_id);

        if ($home_page) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home_page->ID);

            if ($blog_page)
                update_option('page_for_posts', $blog_page->ID);
        }
    }

    /**
     * Fix Elementor image URLs by replacing demo URLs with current site URL
     * and removing unwanted path segments from Elementor metadata.
     *
     * This function iterates over all Elementor-related posts (pages, builders, popups)
     * and updates their `_elementor_data` and `_elementor_page_settings` meta fields
     * to remove `/sites/{number}` paths and replace demo site URLs with the live site URL.
     *
     * @param string $kit_id The demo kit ID used to build the demo URL.
     *
     * @since 1.4.0
     */
    public function fix_elementor_images($kit_id)
    {
        $args = [
            'post_type'      => [Builder::BUILDER_POST_TYPE, Popup::POPUP_POST_TYPE, 'page'],
            'posts_per_page' => -1,
            'meta_key'       => '_elementor_version',
        ];

        $elementor_pages = new WP_Query($args);

        if (!$elementor_pages->have_posts()) {
            return;
        }

        $site_url      = preg_quote(get_site_url(), '/');
        $demo_site_url = preg_quote(Base::AFEB_DEMO_URL . '/' . $kit_id, '/');

        while ($elementor_pages->have_posts()) {
            $elementor_pages->the_post();
            $post_id = get_the_ID();

            $this->replace_elementor_data($post_id, $site_url, $demo_site_url, '_elementor_data');
            $this->replace_elementor_data($post_id, $site_url, $demo_site_url, '_elementor_page_settings');
        }

        wp_reset_postdata();
        Plugin::instance()->files_manager->clear_cache();
    }

    /**
     * Replace demo site URLs and unwanted path segments in Elementor meta data.
     *
     * @param int    $post_id       The post ID.
     * @param string $site_url      The live site URL (escaped for regex).
     * @param string $demo_site_url The demo site URL (escaped for regex).
     * @param string $meta_key      The meta key to update (e.g., '_elementor_data').
     *
     * @since 1.4.0
     */
    private function replace_elementor_data($post_id, $site_url, $demo_site_url, $meta_key)
    {
        $data = get_post_meta($post_id, $meta_key, true);

        if (empty($data)) {
            return;
        }

        if (is_array($data)) {
            $data = json_encode($data);
        }

        $data = preg_replace('/\/sites\/\d+/', '', $data);
        $data = str_replace($demo_site_url, $site_url, $data);
        $data = json_decode($data, true);

        update_metadata('post', $post_id, $meta_key, $data);
    }
}
