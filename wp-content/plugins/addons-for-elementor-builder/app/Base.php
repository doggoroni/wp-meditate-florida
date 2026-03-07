<?php

namespace AFEB;

use DOMDocument;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Base Class
 * 
 * @class Base
 * @version 1.0.0
 */
class Base
{
    /**
     * Webilia URL
     */
    const WEBILIA_URL = 'https://webilia.com';

    /**
     * Webilia API URL
     */
    const WEBILIA_API_URL = 'https://api.webilia.com';

    /**
     * "Vertex Addons for Elementor" URL
     */
    const AFEB_URL = 'https://vertexaddons.com';

    /**
     * "Vertex Addons for Elementor" Contact URL
     */
    const AFEB_CONTACT_URL = 'https://vertexaddons.com/contact';

    /**
     * "Vertex Addons for Elementor" Demo URL
     */
    const AFEB_DEMO_URL = 'https://demo.vertexaddons.com';

    /**
     * Return WP Plugin Dir Path
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_path()
    {
        return AFEB_ABSPATH;
    }

    /**
     * Return "Vertex Addons for Elementor" Plugin Dir URL
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function plugin_url()
    {
        return plugins_url() . '/' . AFEB_DIRNAME;
    }

    /**
     * Return "Vertex Addons for Elementor" App Dir Path
     * 
     * @since 1.0.0
     * 
     * @param string $path
     * 
     * @return string
     */
    public function app_path($path = '')
    {
        return AFEB_ABSPATH . '/app/' . trim($path, '/ ');
    }

    /**
     * Return "Vertex Addons for Elementor" Assets Dir URL
     * 
     * @since 1.0.0
     * 
     * @param string $assets
     * 
     * @return string
     */
    public function assets_url($assets)
    {
        return $this->plugin_url() . '/assets/' . trim($assets, '/ ');
    }

    /**
     * Return "Vertex Addons for Elementor" Assets Dir Path
     * 
     * @since 1.0.0
     * 
     * @param string $assets
     * 
     * @return string
     */
    public function assets_path($assets)
    {
        return AFEB_ABSPATH . '/assets/' . trim($assets, '/ ');
    }

    /**
     * Get an HTML file using slug and name
     * 
     * @since 1.0.0
     * 
     * @param string $slug
     * @param string $name
     * @param bool $include_once
     */
    public function get_template_part($slug = '', $name = 'tpl.php', $include_once = true)
    {
        $path = wp_normalize_path("{$slug}/{$name}");

        if ($include_once) $get_part = include_once($path);
        else $get_part = @include($path);
    }

    /**
     * Redirect to dashboard page after activation
     * 
     * @since 1.0.0
     */
    public function redirect_after_activation()
    {
        // No need to redirect
        if (!get_option('_afeb_activation_redirect', false) || wp_doing_ajax()) return;

        // Delete the option to don't do it again
        delete_option('_afeb_activation_redirect');

        // Redirect to "Vertex Addons for Elementor" Dashboard
        wp_redirect(admin_url('admin.php?page=' . Menus::MENUS_SLUG));
        exit;
    }

    /**
     * Parsing a feed address
     * 
     * @since 1.0.0
     * 
     * @param string $url
     * @param int $expiration
     * 
     * @return array
     */
    public function dom_doc($url = '', $expiration = 0)
    {
        $feed = get_transient($url);
        if (!is_array($feed)) {
            $feed = [];
            $dom_doc_object = new DOMDocument();
            $previous = libxml_use_internal_errors(true);
            $loaded = $dom_doc_object->load($url);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            if (!$loaded) {
                return $feed;
            }

            foreach ($dom_doc_object->getElementsByTagName('item') as $node) {
                $item = [
                    'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                    'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                    'pubDate' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
                    'guid' => $node->getElementsByTagName('guid')->item(0)->nodeValue,
                    'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                    'content' => $node->getElementsByTagName('encoded')->item(0)->nodeValue
                ];

                array_push($feed, $item);
            }

            if ($expiration > 0) {
                delete_transient($url);
                set_transient($url, $feed, $expiration);
            }
        }
        return $feed;
    }
}
