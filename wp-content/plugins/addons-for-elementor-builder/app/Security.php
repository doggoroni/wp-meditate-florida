<?php

namespace AFEB;

use DOMDocument;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Security Class
 * 
 * @class Security
 * @version 1.0.6
 */
class Security extends Base
{
    /**
     * Ajax request security check
     * 
     * @since 1.0.6
     * 
     * @param string $nonce
     * @param string|int $nonce_action
     * @param string $capability
     * 
     * @return string
     */
    public function ajax_request_validate($nonce = '', $nonce_action = -1, $capability = 'manage-options')
    {
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($nonce)), $nonce_action) || current_user_can($capability))
            wp_send_json_error(['message' => esc_html__('Ajax request is not valid', 'addons-for-elementor-builder')]);
    }

    /**
     * Cleaning And sanitizing the CSS codes
     *
     * @since 1.0.6
     * 
     * @param string $css
     *
     * @return string
     */
    public function clean_and_sanitize_css($css)
    {
        return wp_strip_all_tags($css);
    }

    /**
     * Sanitize SVG content
     * 
     * @since 1.4.0

     * @param string $svg_content
     * 
     * @return string
     */
    public function sanitize_svg($svg_content)
    {
        $dom = new DOMDocument();
        $dom->loadXML($svg_content, LIBXML_NOENT | LIBXML_DTDLOAD);

        // Remove scripts
        $scripts = $dom->getElementsByTagName('script');
        while ($scripts->length > 0)
            $scripts->item(0)->parentNode->removeChild($scripts->item(0));

        // Remove external entities
        // Additional sanitization can be added here as needed

        return $dom->saveXML();
    }
}
