<?php

namespace AFEB\Modules\DynamicTags\Tags\Site;

use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use AFEB\Modules\DynamicTags\Tags\Site\Module as SiteModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PageTitle Tag Class
 * 
 * @class PageTitle
 * @version 1.3.0
 */
class PageTitle extends Base_Tag
{
    /**
     * Get tag name
     *
     * @since 1.3.0
     *
     * @return string Tag name
     */
    public function get_name()
    {
        return 'afeb-page-title';
    }

    /**
     * Get tag title
     *
     * @since 1.3.0
     *
     * @return string Tag title
     */
    public function get_title()
    {
        return esc_html__('Page Title', 'addons-for-elementor-builder');
    }

    /**
     * Get tag group
     *
     * @since 1.3.0
     *
     * @return array Tag group
     */
    public function get_group()
    {
        return SiteModule::AFEB_GROUP;
    }

    /**
     * Get tag categories
     *
     * @since 1.3.0
     *
     * @return array Tag categories
     */
    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    protected function register_controls()
    {
        $controls = new CHelper($this);
        $controls->yn_switcher('include_context', [
            'label' => esc_html__('Include Context', 'addons-for-elementor-builder'),
            'default' => 'yes',
        ]);
        $controls->yn_switcher('show_home_title', [
            'label' => esc_html__('Show Home Title', 'addons-for-elementor-builder')
        ]);
    }

    /**
     * Render PageTitle tag output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        if (is_home() && $this->get_settings('show_home_title') !== 'yes')
            return;

        if (Plugin::instance()->common) {
            $current_action_data = Plugin::instance()->common->get_component('ajax')->get_current_action_data();

            if ($current_action_data && $current_action_data['action'] === 'render_tags')
                query_posts(['p' => get_the_ID(), 'post_type' => 'any',]);
        }

        $include_context = $this->get_settings('include_context') === 'yes';

        $title = Helper::get_page_title($include_context);

        echo wp_kses_post($title);
    }
}
