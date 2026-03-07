<?php

namespace AFEB\Widgets\AdvancedMenus;

use Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" AdvancedMenus Widget Helper Class
 * 
 * @class Helper
 * @version 1.3.0
 */
class Helper
{
    /**
     * @var int
     */
    public $menu_index = 1;

    public function render_toggle_menu($context)
    {
        $settings = $context->get_active_settings();

        if (
            !isset($settings['toggle']) ||
            $settings['toggle'] != 'burger'
        ) {
            return;
        }

        $context->add_render_attribute('menu-toggle', [
            'aria-label' => esc_attr__('Advanced Menu Toggle', 'addons-for-elementor-builder'),
            'aria-expanded' => 'false',
            'class' => 'afeb-advanced-menu-toggle',
            'role' => 'button',
            'style' => 'opacity: 0;position: fixed;z-index: -99999',
            'tabindex' => '0',
        ]);
?>
        <div <?php $context->print_render_attribute_string('menu-toggle'); ?>>
            <?php
            $toggle_icon_hover_animation = !empty($settings['toggle_icon_hover_animation'])
                ? ' elementor-animation-' . $settings['toggle_icon_hover_animation'] : '';

            $open_class = 'afeb-advanced-menu-toggle-open-icon' . $toggle_icon_hover_animation;
            $close_class = 'afeb-advanced-menu-toggle-close-icon' . $toggle_icon_hover_animation;
            $normal_icon = ! empty($settings['toggle_icon_normal']['value'])
                ? $settings['toggle_icon_normal']
                : [
                    'library' => 'eicons',
                    'value' => 'eicon-menu-bar',
                ];

            $is_normal_icon_svg = 'svg' === $normal_icon['library'];
            if ($is_normal_icon_svg) echo '<span class="' . esc_attr($open_class) . '">';

            Icons_Manager::render_icon(
                $normal_icon,
                [
                    'aria-hidden' => 'true',
                    'role' => 'presentation',
                    'class' => $open_class,
                ]
            );

            if ($is_normal_icon_svg) echo '</span>';

            $active_icon = !empty($settings['toggle_icon_active']['value'])
                ? $settings['toggle_icon_active']
                : [
                    'library' => 'eicons',
                    'value' => 'eicon-close',
                ];

            $is_active_icon_svg = 'svg' === $active_icon['library'];
            if ($is_active_icon_svg) echo '<span class="' . esc_attr($close_class) . '">';

            Icons_Manager::render_icon(
                $active_icon,
                [
                    'aria-hidden' => 'true',
                    'role' => 'presentation',
                    'class' => $close_class,
                ]
            );

            if ($is_active_icon_svg) echo '</span>';
            ?>
        </div>
<?php
    }

    public function get_nav_menu_index()
    {
        return $this->menu_index++;
    }
}
