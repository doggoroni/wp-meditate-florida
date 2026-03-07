<?php
namespace AFEB\Widgets\Woo;

use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use Elementor\Widget_Base;

// Lite helper

if (!defined('ABSPATH')) exit;

class ProductCategory extends Widget_Base {

    private $controls;
    private $helper;

    public function __construct($data = [], $args = []) {
        parent::__construct($data, $args);
        $this->controls = new CHelper($this);
        $this->helper   = new Helper();
    }

    public function get_name() { return 'afeb_woo_product_category'; }
    public function get_title() { return esc_html__('Woo Product Category', 'addons-for-elementor-builder'); }
    public function get_icon(): string
    { return 'afeb-iconsvg-products-category'; }
    public function get_categories() { return ['afeb_basic']; }
    public function get_keywords() { return ['woocommerce', 'product', 'category', 'afeb']; }


    public function get_script_depends() {
        if (!wp_script_is('swiper', 'registered')) {
            wp_register_script(
                'swiper',
                $this->helper->assets_url('packages/swiper/swiper-bundle.min.js'),
                [],
                '9.3.2',
                true
            );
            wp_register_style(
                'swiper',
                $this->helper->assets_url('packages/swiper/swiper-bundle.min.css'),
                [],
                '9.3.2'
            );
        }

        wp_deregister_script('lite-cat-carousel-grid');
        wp_register_script(
            'lite-cat-carousel-grid',
            $this->helper->assets_url('js/lite-cat-carousel-grid.js'),
            ['jquery', 'swiper'],
            '1.0.2',
            true
        );

        return ['swiper', 'lite-cat-carousel-grid'];
    }

    public function get_style_depends(): array
    {
        wp_deregister_style('afeb-woo-cat');
        wp_register_style(
            'afeb-woo-cat',
            $this->helper->assets_url('css/widgets/woo-product-category.css'),
            [],
            '1.0.5'
        );

        return ['swiper', 'afeb-woo-cat'];
    }


    protected function register_controls() {

        /**
         * -------------------
         * Content Tab
         * -------------------
         */
        // Section: Query
        $this->controls->tab_content_section('section_query', [
            'label' => esc_html__('Query', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->select('cats_source', [
                'label'   => esc_html__('Source', 'addons-for-elementor-builder'),
                'options' => [
                    'all'           => esc_html__('All Categories', 'addons-for-elementor-builder'),
                    'parent'        => esc_html__('By Parent', 'addons-for-elementor-builder'),
                    'subcategories' => esc_html__('By Subcategories of Current', 'addons-for-elementor-builder'),
                    'manual'        => esc_html__('Manual Selection', 'addons-for-elementor-builder'),
                ],
                'default' => 'all',
            ]);

            $this->controls->number('cats_parent', [
                'label'     => esc_html__('Parent Category ID', 'addons-for-elementor-builder'),
                'condition' => ['cats_source' => 'parent'],
                'default'   => 0,
            ]);

                    // Manual WooCommerce product categories only
            $this->controls->select2('include_cats_manual', [
                    'label'    => __('Include Categories', 'addons-for-elementor-builder'),
                    'options'  => Helper::get_terms_safe('product_cat'),
                    'multiple' => true,
                    'condition'=> ['cats_source' => 'manual'],
            ]);


            $this->controls->yn_switcher('hide_empty', [
                'label'        => esc_html__('Hide Empty Categories', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default'      => 'no',
            ]);

            $this->controls->yn_switcher('show_count', [
                'label'        => esc_html__('Show Product Count', 'addons-for-elementor-builder'),
                'return_value' => 'yes',
                'default'      => 'no',
            ]);

            $this->controls->select('display_mode', [
                'label'   => esc_html__('Layout Mode', 'addons-for-elementor-builder'),
                'options' => [
                    'grid'     => esc_html__('Grid', 'addons-for-elementor-builder'),
                    'carousel' => esc_html__('Carousel', 'addons-for-elementor-builder'),
                ],
                'default' => 'grid',
            ]);

            $this->controls->responsive()->number('grid_columns', [
                'label'     => esc_html__('Columns', 'addons-for-elementor-builder'),
                'min'       => 1,
                'max'       => 12,
                'default'   => 3,
                'render_type' => 'template', // KEY for live AJAX update
                'frontend_available' => true, // Pass value to frontend JS if needed

                'selectors' => [
                    '{{WRAPPER}} .afebp-woo-cat-grid' => '--afebp-woo-cat-columns: {{VALUE}};'
                ],
                'condition' => ['display_mode' => 'grid']

            ]);
        });

        // Section: Carousel Settings
        $this->controls->tab_content_section('section_carousel', [
            'label'     => esc_html__('Carousel Settings', 'addons-for-elementor-builder'),
            'condition' => ['display_mode' => 'carousel']
        ], function () {
            $this->controls->responsive()->number('carousel_items', [
                'label'   => esc_html__('Items Per View', 'addons-for-elementor-builder'),
                'min'     => 1,
                'max'     => 6,
                'default' => 3,
            ]);
            $this->controls->number('carousel_speed', [
                'label'   => esc_html__('Autoplay Speed (ms)', 'addons-for-elementor-builder'),
                'default' => 3000,
            ]);
            $this->controls->yn_switcher('carousel_autoplay', [
                'label'   => esc_html__('Autoplay', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
            $this->controls->yn_switcher('carousel_loop', [
                'label'   => esc_html__('Loop', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
            $this->controls->yn_switcher('carousel_arrows', [
                'label'   => esc_html__('Navigation Arrows', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
            $this->controls->yn_switcher('carousel_dots', [
                'label'   => esc_html__('Pagination Dots', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
        });

        // Section: Elements
        $this->controls->tab_content_section('section_elements', [
            'label' => esc_html__('Elements', 'addons-for-elementor-builder')
        ], function () {
            $this->controls->switcher('show_image', [
                'label'   => esc_html__('Show Image', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
            $this->controls->switcher('show_title', [
                'label'   => esc_html__('Show Title', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
            $this->controls->switcher('show_desc', [
                'label'   => esc_html__('Show Description', 'addons-for-elementor-builder'),
                'default' => 'no',
            ]);
        });

        /**
         * -------------------
         * Style Tab
         * -------------------
         */
        /**
         * -------------------
         * Style Tab
         * -------------------
         */

// Shared Item Box styles (Grid + Carousel)
        $this->controls->tab_style_section('style_item', [
                'label' => esc_html__('Item Box', 'addons-for-elementor-builder')
        ], function () {
            $selector_item = '{{WRAPPER}} .afebp-woo-cat-grid .afebp-woo-cat-item, {{WRAPPER}} .afebp-woo-cat-swiper .afebp-woo-cat-item';

            $this->controls->responsive()->dimensions('item_padding', [
                    'label'     => esc_html__('Padding', 'addons-for-elementor-builder'),
                    'selectors' => [$selector_item => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
            ]);
            // Defensive Grid Gap Slider
            $grid_gap_selectors = [];
            $grid_selector_key  = '{{WRAPPER}} .afebp-woo-cat-grid';
            if (is_string($grid_selector_key) && trim($grid_selector_key) !== '') {
                $grid_gap_selectors[$grid_selector_key] = '--afebp-woo-cat-gap: {{SIZE}}{{UNIT}} !important;';
            }

            $grid_gap_selectors = [];
            $grid_selector_key = '{{WRAPPER}} .afebp-woo-cat-grid, {{WRAPPER}} .afebp-woo-cat-grid .elementor-grid';
            if (is_string($grid_selector_key) && trim($grid_selector_key) !== '') {
                $grid_gap_selectors[$grid_selector_key] = '--afebp-woo-cat-gap: {{SIZE}}{{UNIT}} !important;';
            }

            $this->controls->responsive()->slider('item_gap', [
                    'label' => esc_html__('Gap Between Items (Grid)', 'addons-for-elementor-builder'),
                    'default' => [
                            'size' => 20,
                            'unit' => 'px',
                    ],
                    'range' => [
                            'px' => ['min' => 0, 'max' => 200],
                            '%'  => ['min' => 0, 'max' => 50],
                    ],
                    'selectors' => $grid_gap_selectors,
                    'condition' => [
                            'display_mode' => 'grid',
                    ],
            ]);


// Defensive Carousel Gap Slider
            $carousel_gap_selectors = [];
            $carousel_selector_key  = '{{WRAPPER}} .swiper-slide';
            if (is_string($carousel_selector_key) && trim($carousel_selector_key) !== '') {
                $carousel_gap_selectors[$carousel_selector_key] = 'margin-right: {{SIZE}}{{UNIT}} !important;';
            }

            $this->controls->responsive()->slider('carousel_gap', [
                    'label' => esc_html__('Gap Between Items (Carousel)', 'addons-for-elementor-builder'),
                    'default' => [
                            'size' => 20,
                            'unit' => 'px',
                    ],
                    'range' => [
                            'px' => ['min' => 0, 'max' => 200],
                            '%'  => ['min' => 0, 'max' => 50],
                    ],
                    'selectors' => $carousel_gap_selectors,
                    'condition' => [
                            'display_mode' => 'carousel',
                    ],
            ]);

            $this->controls->border(['name' => 'item_border','selector' => $selector_item]);
            $this->controls->responsive()->border_radius('item_border_radius', [
                    'selectors'  => [$selector_item =>
                            'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
            ]);
            $this->controls->box_shadow(['name' => 'item_shadow', 'selector' => $selector_item]);
            $this->controls->background(['name' => 'item_bg', 'selector' => $selector_item]);
        });

// Image styles
        $this->controls->tab_style_section('style_image', [
                'label' => esc_html__('Image', 'addons-for-elementor-builder')
        ], function () {
            $selector_img = '{{WRAPPER}} .afebp-woo-cat-image img';

            $this->controls->responsive()->slider('image_width', [
                    'label' => esc_html__('Width', 'addons-for-elementor-builder'),
                    'selectors' => [$selector_img => 'width: {{SIZE}}{{UNIT}};']
            ]);
            $this->controls->responsive()->slider('image_height', [
                    'label' => esc_html__('Height', 'addons-for-elementor-builder'),
                    'selectors' => [$selector_img => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;']
            ]);
            $this->controls->responsive()->margin('image_margin', [
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-image' =>
                            'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
            ]);
            $this->controls->border(['name' => 'image_border','selector' => $selector_img]);
            $this->controls->responsive()->border_radius('image_radius', [
                    'selectors' => [$selector_img =>
                            'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
            ]);
            $this->controls->box_shadow(['name' => 'image_shadow','selector' => $selector_img]);
        });

// Title styles
        $this->controls->tab_style_section('style_title', [
                'label' => esc_html__('Title', 'addons-for-elementor-builder')
        ], function () {
            $this->controls->typography(['name' => 'title_typography','selector' => '{{WRAPPER}} .afebp-woo-cat-title']);
            $this->controls->color('title_color', [
                    'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-title' => 'color: {{VALUE}};']
            ]);
            $this->controls->color('title_hover_color', [
                    'label' => esc_html__('Hover Color', 'addons-for-elementor-builder'),
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-title:hover' => 'color: {{VALUE}};']
            ]);
            $this->controls->responsive()->margin('title_margin', [
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-title' =>
                            'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
            ]);
            $this->controls->alignment('title_align', [
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-title' => 'text-align: {{VALUE}};']
            ]);
        });

// Description styles
        $this->controls->tab_style_section('style_desc', [
                'label' => esc_html__('Description', 'addons-for-elementor-builder')
        ], function () {
            $this->controls->typography(['name' => 'desc_typography','selector' => '{{WRAPPER}} .afebp-woo-cat-desc']);
            $this->controls->color('desc_color', [
                    'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-desc' => 'color: {{VALUE}};']
            ]);
            $this->controls->responsive()->margin('desc_margin', [
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-desc' =>
                            'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
            ]);
            $this->controls->alignment('desc_align', [
                    'selectors' => ['{{WRAPPER}} .afebp-woo-cat-desc' => 'text-align: {{VALUE}};']
            ]);
        });

// Carousel‑specific controls
        $this->controls->tab_style_section('style_carousel_nav', [
                'label'     => esc_html__('Carousel Navigation', 'addons-for-elementor-builder'),
                'condition' => ['display_mode' => 'carousel']
        ], function () {
            // Arrows
            $this->controls->color('carousel_arrow_color', [
                    'label' => esc_html__('Arrow Color', 'addons-for-elementor-builder'),
                    'selectors' => [
                            '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}};'
                    ]
            ]);
            $this->controls->slider('carousel_arrow_size', [
                    'label' => esc_html__('Arrow Size', 'addons-for-elementor-builder'),
                    'selectors' => [
                            '{{WRAPPER}} .swiper-button-next:after,{{WRAPPER}} .swiper-rtl .swiper-button-prev:after,{{WRAPPER}} .swiper-button-prev:after,{{WRAPPER}} .swiper-rtl .swiper-button-next:after' => 'font-size: {{SIZE}}{{UNIT}};'
                    ]
            ]);
            // Dots
            $this->controls->color('carousel_dots_color', [
                    'label' => esc_html__('Dots Color', 'addons-for-elementor-builder'),
                    'selectors' => ['{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};']
            ]);
            $this->controls->color('carousel_dots_active_color', [
                    'label' => esc_html__('Active Dot Color', 'addons-for-elementor-builder'),
                    'selectors' => ['{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};']
            ]);
        });
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // --- Category Query ---
        $args = [
                'taxonomy'   => 'product_cat',
                'hide_empty' => (!empty($settings['hide_empty']) && $settings['hide_empty'] === 'yes'),
                'orderby'    => 'name',
                'order'      => 'ASC',
        ];

        switch ($settings['cats_source'] ?? 'all') {
            case 'parent':
                if (!empty($settings['cats_parent'])) {
                    $args['parent'] = intval($settings['cats_parent']);
                }
                break;

            case 'manual':
                $ids = array_filter(array_map('intval', (array) $settings['include_cats_manual']));
                if (empty($ids)) {
                    echo '<div class="afeb-lite-woo-cat-empty">' . esc_html__('No categories selected.', 'addons-for-elementor-builder') . '</div>';
                    return;
                }
                $args = [
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                        'include'    => $ids,
                ];
                break;

            case 'subcategories':
                if (is_tax('product_cat')) {
                    $args['parent'] = get_queried_object_id();
                }
                break;
        }

        $terms = taxonomy_exists('product_cat') ? get_terms($args) : [];

        if (is_wp_error($terms) || empty($terms)) {
            echo '<div class="afeb-lite-woo-cat-empty">' . esc_html__('No categories found.', 'addons-for-elementor-builder') . '</div>';
            return;
        }

        // Get responsive values with fallbacks
        $grid_columns = [
                'desktop' => $settings['grid_columns'] ?? 3,
                'tablet'  => $settings['grid_columns_tablet'] ?? ($settings['grid_columns'] ?? 2),
                'mobile'  => $settings['grid_columns_mobile'] ?? 1
        ];

        $carousel_items = [
                'desktop' => $settings['carousel_items'] ?? 3,
                'tablet'  => $settings['carousel_items_tablet'] ?? ($settings['carousel_items'] ?? 2),
                'mobile'  => $settings['carousel_items_mobile'] ?? 1
        ];

        // --- Pass settings to JS ---
        $data_settings = [
                'display_mode'      => $settings['display_mode'] ?? 'grid',
                'carousel_items'    => $carousel_items,
                'carousel_spacing'  => intval($settings['carousel_gap']['size'] ?? 20),
                'carousel_loop'     => $settings['carousel_loop'] ?? 'no',
                'carousel_autoplay' => $settings['carousel_autoplay'] ?? 'no',
                'carousel_speed'    => intval($settings['carousel_speed'] ?? 3000),
                'carousel_arrows'   => $settings['carousel_arrows'] ?? 'no',
                'carousel_dots'     => $settings['carousel_dots'] ?? 'no',
                'grid_columns'      => $grid_columns,
                'min_width'         => intval($settings['grid_min_width'] ?? 200),
                'gap'               => intval($settings['item_gap']['size'] ?? 20),
                'grid_type'         => $settings['grid_type'] ?? 'default',
        ];
        ?>
        <div class="afeb-lite-woo-cat" data-settings='<?php echo wp_json_encode($data_settings); ?>'>
            <?php if ($data_settings['display_mode'] === 'carousel') : ?>
                <div class="afebp-woo-cat-swiper swiper"
                     data-swiper-settings='<?php echo wp_json_encode([
                             'items' => [
                                     'desktop' => $settings['carousel_items'] ?? 3,
                                     'tablet' => $settings['carousel_items_tablet'] ?? ($settings['carousel_items'] ?? 2),
                                     'mobile' => $settings['carousel_items_mobile'] ?? 1
                             ],
                             'spaceBetween' => !empty($settings['carousel_gap']['size']) ? $settings['carousel_gap']['size'] : 20,
                             'loop' => $settings['carousel_loop'] === 'yes',
                             'autoplay' => $settings['carousel_autoplay'] === 'yes' ? [
                                     'delay' => $settings['carousel_speed'] ?? 3000,
                                     'disableOnInteraction' => false
                             ] : false,
                             'navigation' => $settings['carousel_arrows'] === 'yes',
                             'pagination' => $settings['carousel_dots'] === 'yes',
                     ]); ?>'>
                    <div class="swiper-wrapper">
                        <?php foreach ($terms as $term) : ?>
                            <div class="swiper-slide afebp-woo-cat-item">
                                <?php $this->print_term_item($term, $settings); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($settings['carousel_arrows'] === 'yes') : ?>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    <?php endif; ?>

                    <?php if ($settings['carousel_dots'] === 'yes') : ?>
                        <div class="swiper-pagination"></div>
                    <?php endif; ?>
                </div>

            <?php else : ?>

                <div class="afebp-woo-cat-grid elementor-grid"
                     style="--afebp-woo-cat-columns: <?php echo esc_attr($grid_columns['desktop']); ?>;
                             --afebp-woo-cat-columns-tablet: <?php echo esc_attr($grid_columns['tablet']); ?>;
                             --afebp-woo-cat-columns-mobile: <?php echo esc_attr($grid_columns['mobile']); ?>;
                             --afebp-woo-cat-min-width: <?php echo esc_attr($data_settings['min_width']); ?>px;
                             --afebp-woo-cat-gap: <?php echo esc_attr($data_settings['gap']); ?>px;"
                     data-grid-type="<?php echo esc_attr($data_settings['grid_type']); ?>">
                    <?php foreach ($terms as $term) : ?>
                        <div class="afebp-woo-cat-item elementor-grid-item">
                            <?php $this->print_term_item($term, $settings); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Output single category box
     */
    private function print_term_item($term, $settings) {
        $show_image = !empty($settings['show_image']) && $settings['show_image'] === 'yes';
        $show_title = !empty($settings['show_title']) && $settings['show_title'] === 'yes';
        $show_desc  = !empty($settings['show_desc']) && $settings['show_desc'] === 'yes';
        $show_count = !empty($settings['show_count']) && $settings['show_count'] === 'yes';

        echo '<a href="' . esc_url(get_term_link($term)) . '">';
        if ($show_image) {
            $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            echo '<div class="afebp-woo-cat-image">';
            if ($thumb_id) {
                echo wp_get_attachment_image($thumb_id, 'medium');
            } else {
                $alt_text = esc_attr($term->name);

                if (function_exists('wc_placeholder_img')) {
                    echo wc_placeholder_img('woocommerce_thumbnail', [
                            'class' => 'afebp-woo-cat-placeholder',
                            'alt'   => $alt_text,
                    ]);
                } else {
                    $placeholder_src = '';

                    if (function_exists('wc_placeholder_img_src')) {
                        $placeholder_src = wc_placeholder_img_src('woocommerce_thumbnail');

                        if (empty($placeholder_src)) {
                            $placeholder_src = wc_placeholder_img_src();
                        }
                    }

                    if (empty($placeholder_src) && function_exists('includes_url')) {
                        $placeholder_src = includes_url('images/media/default.png');
                    }

                    if (!empty($placeholder_src)) {
                        echo '<img src="' . esc_url($placeholder_src) . '" alt="' . $alt_text . '" class="afebp-woo-cat-placeholder" />';
                    }
                }
            }
            echo '</div>';
        }
        if ($show_title) {
            echo '<h3 class="afebp-woo-cat-title">' . esc_html($term->name);
            if ($show_count) {
                echo ' <span class="afebp-woo-cat-count">(' . intval($term->count) . ')</span>';
            }
            echo '</h3>';
        }
        $term_description = term_description($term);

        if ($show_desc && $term_description) {
            echo '<div class="afebp-woo-cat-desc">' . wp_kses_post($term_description) . '</div>';
        }
        echo '</a>';
    }

}
