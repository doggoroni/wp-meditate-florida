<?php

namespace AFEB\Widgets;

use AFEB\Controls\CHelper;
use Elementor\Plugin;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Template Widget Class
 * 
 * @class Template
 * @version 1.3.0
 */
class Template extends Widget_Base
{
    /**
     * @var ControlsHelper
     */
    private $CHelper;

    /**
     * Template Constructor
     * 
     * @since 1.3.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->CHelper = new CHelper();
    }

    /**
     * Get widget name
     *
     * @since 1.3.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_template';
    }

    /**
     * Get widget title
     *
     * @since 1.3.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Template', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.3.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-template';
    }

    /**
     * Get widget categories
     *
     * @since 1.3.0
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['afeb_basic'];
    }

    /**
     * Get widget keywords
     *
     * @since 1.3.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['template', 'library', 'block', 'page', esc_html__('Template', 'addons-for-elementor-builder')];
    }

    /**
     * Register Template widget controls
     *
     * @since 1.3.0
     */
    public function register_controls()
    {
        /**
         *
         * Template
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Template', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->dslct2($obj, 'tmp_slct', esc_html__('Choose a template', 'addons-for-elementor-builder'), 'get_posts_by_type', 'elementor_library');
        });
    }

    /**
     * Render Template widget output on the frontend
     *
     * @since 1.3.0
     */
    protected function render()
    {
        $tmp_id = $this->get_settings('tmp_slct');

        if (empty($tmp_id))
            return;

        if (get_post_status($tmp_id) != 'publish')
            return;
?>
        <div class="afeb-template">
            <?php
            // PHPCS - should not be escaped.
            echo Plugin::instance()->frontend->get_builder_content_for_display($tmp_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </div>
<?php
    }
}
