<?php

namespace AFEB\Modules\DynamicTags\Tags\Woo;

use Elementor\Core\DynamicTags\Manager;
use \Elementor\Modules\DynamicTags\Module as ElModule;

/**
 * "Vertex Addons for Elementor" Module Class
 *
 * @class Module
 * @version 1.3.0
 */
class Module extends ElModule
{
    /**
     * TAGS GROUP
     */
    const AFEB_GROUP = 'afeb_dynamic_tags_woo';

    /**
     * Retrieve the dynamic tag classes names
     *
     * @return array
     * @since 1.3.0
     *
     */
    public function get_tag_classes_names()
    {
        return apply_filters('afeb/tags/woo_tags', [
            'ProductContent',
            'ProductRegularPrice',
            'ProductSalePrice',
            'ProductSale',
            'ProductShortDescription',
            'ProductSKU',
            'ProductStock',
            'ProductStockStatus',
            'ProductRating',
            'ProductTerms',
            'ProductTitle',
            'ProductURL',
        ]);
    }

    /**
     * Retrieve the dynamic tag groups
     *
     * @return array
     * @since 1.3.0
     *
     */
    public function get_groups()
    {
        return [
            self::AFEB_GROUP => ['title' => esc_html__('Woocommerce (Vertex Addons)', 'addons-for-elementor-builder')],
        ];
    }

    /**
     * Add all the available dynamic tags
     *
     * @param Manager $dynamic_tags
     * @since 1.3.0
     *
     */
    public function register_tags($dynamic_tags)
    {
        foreach ($this->get_tag_classes_names() as $tag_class)
        {
            $tag_class = '\AFEB\Modules\DynamicTags\Tags\Woo\\' . $tag_class;
            if (class_exists($tag_class))
            {
                if (method_exists($dynamic_tags, 'register')) $dynamic_tags->register(new $tag_class());
                else $dynamic_tags->register_tag(new $tag_class());
            }
        }
    }
}
