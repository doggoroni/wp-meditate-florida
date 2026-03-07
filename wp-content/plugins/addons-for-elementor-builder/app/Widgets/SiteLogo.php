<?php

namespace AFEB\Widgets;

use AFEB\Controls\Helper;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Exception;

/**
 * "Vertex Addons for Elementor" Site Logo Widget Class
 *
 * @class Accordion
 * @version 1.0.0
 */
class SiteLogo extends Widget_Base
{
    /**
     * @var CHelper
     */
    private $controls;

    /**
     * Accordion Constructor
     *
     * @throws Exception
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);

        $this->controls = new Helper($this);
    }

    /**
     * Get widget name
     *
     * @return string Widget name
     * @since 1.0.0
     *
     */
    public function get_name(): string
    {
        return 'afeb_site_logo';
    }

    /**
     * Get widget title
     *
     * @return string Widget title
     * @since 1.0.0
     *
     */
    public function get_title(): string
    {
        return esc_html__('Site Logo', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @return string Widget icon
     * @since 1.0.0
     *
     */
    public function get_icon(): string
    {
        return 'afeb-iconsvg-site-logo';
    }

    /**
     * Get widget categories
     *
     * @return array Widget categories
     * @since 1.0.0
     *
     */
    public function get_categories(): array
    {
        return ['theme-elements'];
    }

    /**
     * Get widget keywords
     *
     * @return array Widget keywords
     * @since 1.0.0
     *
     */
    public function get_keywords(): array
    {
        return ['site', 'logo', 'branding', esc_html__('Site Logo', 'addons-for-elementor-builder')];
    }

    /**
     * Register Accordion widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->controls->section('content_site_logo', [
            'label' => esc_html__('Site Logo', 'addons-for-elementor-builder'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ], function () {
            $this->controls->media('image', [
                'default' => ['url' => wp_get_attachment_url(get_theme_mod('custom_logo'))],
                'label' => esc_html__('Site Logo', 'addons-for-elementor-builder'),
            ]);

            $this->controls->image_size([
                'name' => 'image',
                'condition' => ['image[url]!' => ''],
                'default' => 'full',
                'separator' => 'before',
                'responsive' => true,
            ]);

            $this->controls->select('caption_source', [
                'label' => esc_html__('Caption', 'addons-for-elementor-builder'),
                'options' => [
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                    'attachment' => esc_html__('Attachment Caption', 'addons-for-elementor-builder'),
                ],
                'default' => 'none',
                'condition' => ['image[url]!' => ''],
            ]);

            $this->controls->select('link_to', [
                'label' => esc_html__('Link', 'addons-for-elementor-builder'),
                'options' => [
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                    'site_url' => esc_html__('Site URL', 'addons-for-elementor-builder'),
                    'custom' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                    'file' => esc_html__('Media File', 'addons-for-elementor-builder'),
                ],
                'default' => 'site_url',
                'condition' => ['image[url]!' => ''],
            ]);

            $this->controls->url('link', [
                'condition' => [
                    'image[url]!' => '',
                    'link_to' => 'custom',
                ],
                'show_label' => false,
            ]);

            $this->controls->select('open_lightbox', [
                'label' => esc_html__('Lightbox', 'addons-for-elementor-builder'),
                'description' => sprintf(
                    esc_html__('Manage your site’s lightbox settings in the %1$sLightbox panel%2$s.', 'addons-for-elementor-builder'),
                    '<a href="javascript: $e.run( \'panel/global/open\' ).then( () => $e.route( \'panel/global/settings-lightbox\' ) )">',
                    '</a>'
                ),
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Default', 'addons-for-elementor-builder'),
                    'yes' => esc_html__('Yes', 'addons-for-elementor-builder'),
                    'no' => esc_html__('No', 'addons-for-elementor-builder'),
                ],
                'condition' => [
                    'image[url]!' => '',
                    'link_to' => 'file',
                ],
            ]);
        });

        $this->controls->section('style_image', [
            'label' => esc_html__('Image', 'addons-for-elementor-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
        ], function () {
            $this->controls->responsive()->alignment('align');
            $this->controls->responsive()->slider('width', [
                'label' => esc_html__('Width', 'addons-for-elementor-builder'),
                'selectors' => ['{{WRAPPER}} img' => 'width: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->slider('space', [
                'label' => esc_html__('Max Width', 'addons-for-elementor-builder'),
                'selectors' => ['{{WRAPPER}} img' => 'max-width: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->slider('height', [
                'label' => esc_html__('Height', 'addons-for-elementor-builder'),
                'range' => [
                    'px' => ['min' => 1, 'max' => 500,],
                    'vh' => ['min' => 1, 'max' => 100,],
                ],
                'selectors' => ['{{WRAPPER}} img' => 'height: {{SIZE}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->image_fit('object-fit', [
                'condition' => ['height[size]!' => '',],
            ]);

            $this->controls->responsive()->select('object-position', [
                'label' => esc_html__('Object Position', 'addons-for-elementor-builder'),
                'options' => [
                    'center center' => esc_html__('Center Center', 'addons-for-elementor-builder'),
                    'center left' => esc_html__('Center Left', 'addons-for-elementor-builder'),
                    'center right' => esc_html__('Center Right', 'addons-for-elementor-builder'),
                    'top center' => esc_html__('Top Center', 'addons-for-elementor-builder'),
                    'top left' => esc_html__('Top Left', 'addons-for-elementor-builder'),
                    'top right' => esc_html__('Top Right', 'addons-for-elementor-builder'),
                    'bottom center' => esc_html__('Bottom Center', 'addons-for-elementor-builder'),
                    'bottom left' => esc_html__('Bottom Left', 'addons-for-elementor-builder'),
                    'bottom right' => esc_html__('Bottom Right', 'addons-for-elementor-builder'),
                ],
                'default' => 'center center',
                'selectors' => ['{{WRAPPER}} img' => 'object-position: {{VALUE}};',],
                'condition' => ['height[size]!' => '', 'object-fit' => ['cover', 'contain', 'scale-down'],],
            ]);

            $this->controls->divider('separator_panel_style');

            $this->controls->tabs('image_effects', [
                'normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->opacity('opacity', [
                            'selectors' => [
                                '{{WRAPPER}} img' => 'opacity: {{SIZE}};',
                            ],
                        ]);

                        $this->controls->css_filters([
                            'name' => 'css_filters',
                            'selector' => '{{WRAPPER}} img',
                        ]);
                    },
                ],
                'hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->opacity('opacity_hover', [
                            'selectors' => [
                                '{{WRAPPER}}:hover img' => 'opacity: {{SIZE}};',
                            ],
                        ]);

                        $this->controls->css_filters([
                            'name' => 'css_filters_hover',
                            'selector' => '{{WRAPPER}}:hover img',
                        ]);

                        $this->controls->duration('background_hover_transition', [
                            'label' => esc_html__('Transition Duration (s)', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} img' => 'transition-duration: {{SIZE}}s',
                            ],
                        ]);

                        $this->controls->hover_animation('hover_animation');
                    },
                ],
            ]);

            $this->controls->border([
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} img',
            ]);

            $this->controls->responsive()->dimensions('image_border_radius', [
                'label' => esc_html__('Border Radius', 'addons-for-elementor-builder'),
                'selectors' => [
                    '{{WRAPPER}} img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} img',
            ]);
        });

        $this->controls->section('style_caption', [
            'label' => esc_html__('Caption', 'addons-for-elementor-builder'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'image[url]!' => '',
                'caption_source!' => 'none',
            ],
        ], function () {
            $this->controls->responsive()->alignment('caption_align', [
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
                ],
            ]);

            $this->controls->text_color('caption_text_color', [
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
                ],
            ]);

            $this->controls->background_color('caption_background_color', [
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
                ],
            ]);

            $this->controls->typography([
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .widget-image-caption',
            ]);

            $this->controls->text_shadow([
                'name' => 'caption_text_shadow',
                'selector' => '{{WRAPPER}} .widget-image-caption',
            ]);

            $this->controls->slider('caption_space', [
                'label' => esc_html__('Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => ['max' => 100,],
                    'em' => ['min' => 0, 'max' => 10,],
                    'rem' => ['min' => 0, 'max' => 10,],
                ],
                'selectors' => ['{{WRAPPER}} .widget-image-caption' => 'margin-block-start: {{SIZE}}{{UNIT}};',],
            ]);
        });
    }

    /**
     * Render Accordion widget output on the frontend.
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['image']['url'])) return;

        $has_caption = $this->has_caption($settings);

        $link = $this->get_link_url($settings);
        if ($link) {
            $this->add_link_attributes('link', $link);

            if (Plugin::$instance->editor->is_edit_mode()) {
                $this->add_render_attribute('link', 'class', 'elementor-clickable');
            }

            if ('file' === $settings['link_to']) {
                $this->add_lightbox_data_attributes('link', $settings['image']['id'], $settings['open_lightbox']);
            }
        }
?>
        <?php if ($has_caption): ?>
            <figure class="wp-caption">
            <?php endif; ?>

            <?php if ($link): ?>
                <a <?php $this->print_render_attribute_string('link'); ?>>
                <?php endif; ?>

                <?php Group_Control_Image_Size::print_attachment_image_html($settings); ?>

                <?php if ($link): ?>
                </a>
            <?php endif; ?>

            <?php if ($has_caption): ?>
                <figcaption class="widget-image-caption wp-caption-text"><?php echo wp_kses_post($this->get_caption($settings)); ?></figcaption>
            <?php endif; ?>

            <?php if ($has_caption): ?>
            </figure>
<?php endif;
        }

        protected function get_link_url($settings)
        {
            switch ($settings['link_to']) {
                case 'none':
                    return false;

                case 'custom':
                    return !empty($settings['link']['url']) ? $settings['link'] : false;

                case 'site_url':
                    return ['url' => get_site_url()];

                default:
                    return ['url' => $settings['image']['url']];
            }
        }

        private function has_caption($settings): bool
        {
            return !empty($settings['caption_source']) && 'none' !== $settings['caption_source'];
        }

        private function get_caption($settings)
        {
            $caption = '';
            if (!empty($settings['caption_source']) && 'attachment' === $settings['caption_source']) {
                $logo_id = $settings['image']['id'] ?? 0;
                if (!$logo_id && isset($settings['image']['url']) && trim($settings['image']['url'])) $logo_id = attachment_url_to_postid($settings['image']['url']);

                $caption = wp_get_attachment_caption($logo_id);
            }

            return $caption;
        }
    }
