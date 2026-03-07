<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductRating extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_rating';
    }

    public function get_title()
    {
        return esc_html__('Product Rating', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $rating = $product->get_average_rating();
        if ('' === $rating || null === $rating) return;

        if (function_exists('wc_get_rating_html')) $this->echo_text(wc_get_rating_html((float) $rating));
        else $this->echo_text((string) $rating);
    }
}
