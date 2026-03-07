<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Controls\CHelper;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Data_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" FeaturedImage Tag Class
 * 
 * @class FeaturedImage
 * @version 1.3.0
 */
class FeaturedImage extends Data_Tag
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
        return 'afeb-featured-image';
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
        return esc_html__('Featured Image', 'addons-for-elementor-builder');
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
        return [Module::IMAGE_CATEGORY];
    }

    /**
     * Register SiteTitle tag controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        $chelper = new CHelper();
        $chelper->mda($this, 'fi_flbc', [], '', esc_html__('Fallback', 'addons-for-elementor-builder'));
    }

    /**
     * Get tag value
     *
     * @since 1.3.0
     * 
     * @param array $options
     *
     * @return array Tag value
     */
    public function get_value(array $options = [])
    {
        $thumbnail_id = get_post_thumbnail_id();

        if ($thumbnail_id) {
            $image_src = wp_get_attachment_image_src($thumbnail_id, 'full');

            if ($image_src) {
                $data = [
                    'id' => $thumbnail_id,
                    'url' => $image_src[0],
                ];
            } else {
                $data = $this->get_settings('fi_flbc');
            }
        } else {
            $data = $this->get_settings('fi_flbc');
        }

        return $data;
    }
}
