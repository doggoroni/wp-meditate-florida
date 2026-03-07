<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Controls\Helper;
use AFEB\Modules\DynamicTags\Tags\Post\Module;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostComments Tag Class
 * 
 * @class PostComments
 * @version 1.3.0
 */
class PostComments extends Base_Tag
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
        return 'afeb-post-comments';
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
        return esc_html__('Post Comments', 'addons-for-elementor-builder');
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
        return PostModule::AFEB_GROUP;
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

    /**
     * Register PostComments tag controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        $controls = new Helper($this);
        $controls->text('pc_no_cmnts_frmt', [
            'label' => esc_html__('No Comments Format', 'addons-for-elementor-builder'),
            'label_block' => true,
            'placeholder' => esc_html__('No Comments', 'addons-for-elementor-builder'),
            'ai' => ['active' => false,],
            'dynamic' => ['active' => false,],
        ]);

        $controls->text('pc_one_cmnts_frmt', [
            'label' => esc_html__('One Comment Format', 'addons-for-elementor-builder'),
            'label_block' => true,
            'placeholder' => esc_html__('Comment', 'addons-for-elementor-builder'),
            'ai' => ['active' => false,],
            'dynamic' => ['active' => false,],
        ]);

        $controls->text('pc_mny_cmnts_frmt', [
            'label' => esc_html__('Many Comment Format', 'addons-for-elementor-builder'),
            'placeholder' => sprintf('{number} %s', esc_html__('Comments', 'addons-for-elementor-builder')),
            'label_block' => true,
            'ai' => ['active' => false,],
            'dynamic' => ['active' => false,],
        ]);

        $controls->raw_html('dropdown_description', [
            'raw' => sprintf('{number} %s', esc_html__('placeholder is used to represent the number of comments in the text', 'addons-for-elementor-builder')),
            'content_classes' => 'elementor-descriptor',
        ]);

        $controls->yn_switcher('pc_cmnt_lnk', [
            'label' => esc_html__('Link', 'addons-for-elementor-builder'),
            'default' => 'yes',
        ]);
    }

    /**
     * Render PostTitle tag output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        $comments_number = get_comments_number();

        if (!$comments_number) {
            $value = $this->get_settings('pc_no_cmnts_frmt');
            $value = !trim($value) ? esc_html__('No Comments', 'addons-for-elementor-builder') : $value;
        } elseif ($comments_number == 1) {
            $value = $this->get_settings('pc_one_cmnts_frmt');
            $value = !trim($value) ? esc_html__('Comment', 'addons-for-elementor-builder') : $value;
        } else {
            $value = $this->get_settings('pc_mny_cmnts_frmt');
            $value = !trim($value) ? '{number} ' . esc_html__('Comments', 'addons-for-elementor-builder') : $value;
            $value = strtr($value, ['{number}' => number_format_i18n($comments_number),]);
        }

        if (!empty($this->get_settings('pc_cmnt_lnk')))
            $value = sprintf('<a href="%s">%s</a>', get_comments_link(), $value);

        echo wp_kses_post($value);
    }
}
