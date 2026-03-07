<?php

use AFEB\Widgets;

if (!defined('ABSPATH')) {
    exit;
}

$widgets_instance = new Widgets();
?>
<!-- Start Search Elements Section -->
<div class="afeb-space afeb-mr-20"></div>
<div class="afeb-row">
    <div class="afeb-col-lg-4">
        <div class="afeb-elements-search-section">
            <span class="afeb-elements-search-input-icon fa fa-search"></span>
            <input class="afeb-elements-search-input" type="text" placeholder="<?php esc_html_e('Search the Widgets', 'addons-for-elementor-builder'); ?>">
        </div>
    </div>
    <div class="afeb-col-lg-4"></div>
    <div class="afeb-col-lg-4">
        <div class="afeb-elements-state-btn-section">
            <button class="afeb-box-btn afeb-sec-secondary-btn afeb-elements-activate-all-btn">
                <?php esc_html_e("Activate All", 'addons-for-elementor-builder'); ?>
            </button>
            <button class="afeb-box-btn afeb-secondary-btn afeb-elements-deactivate-all-btn">
                <?php esc_html_e('Deactivate All', 'addons-for-elementor-builder'); ?>
            </button>
        </div>
    </div>
</div>
<div class="afeb-space afeb-mr-20"></div>
<!-- End Search Elements Section -->

<!-- Start Elements Section -->
<form class="afeb-row" action="" method="post">
    <?php
    $widget_type = 'widget';
    $count = 0;
    $widgets = array_merge(
        $widgets_instance->widgets(),
        ['separate' => [
            'type' => 'template-builder',
            'title' => __('Template Builder Widgets', 'addons-for-elementor-builder')
        ]],
        $widgets_instance->template_builder_widgets()
    );
    $widgets_array = array_replace_recursive($widgets, get_option('afeb-widgets-status', []));

    foreach ($widgets_array as $widget_key => $widget) :

        if ($widget_key == 'separate') {
            $widget_type = $widget['type'];
    ?>
            <div class="afeb-element-separate-section afeb-col-lg-12 afeb-mr-top-40 afeb-mr-bottom-30">
                <h2><?php echo esc_html($widget['title']); ?></h2>
            </div>
        <?php
            continue;
        }
        ?>
        <!-- Start Element Section -->
        <div class="afeb-element-search-section afeb-col-lg-4 <?php echo $count > 2 ? 'afeb-mr-top-20' : ''; ?>" data-widget-type="<?php echo esc_attr($widget_type); ?>">
            <div class="afeb-box-section afeb-element-section">
                <div class="afeb-box-header">
                    <?php
                    $image_filename = isset($widget['image']) ? ltrim($widget['image'], '/') : '';
                    $image_url = $image_filename ? $this->assets_url('img/widgets/' . $image_filename) : '';
                    ?>
                    <img class="afeb-element-search-img" src="<?php echo esc_url($image_url); ?>" alt="<?php echo (isset($widget['title']) && trim($widget['title'])) ? esc_attr(trim($widget['title'])) : ''; ?>">
                    <h2 class="afeb-element-search-text">
                        <?php
                        $title = isset($widget['title']) && trim($widget['title']) ? trim($widget['title']) : '';
                        echo esc_html($title);
                        ?>
                    </h2>
                    <div class="afeb-element-status <?php echo intval($widget['status']) != 1 ? 'afeb-element-status-deactive' : ''; ?>"></div>
                </div>
                <div class="afeb-element-actions">
                    <a class="afeb-form-group afeb-element-checkbox-box <?php echo intval($widget['status']) != 1 ? 'afeb-deactive-checkbox' : ''; ?>">
                        <input type="hidden" name="afeb_active_widgets[<?php echo esc_attr(trim($widget_key)); ?>][status]" value="0">
                        <input id="afeb-active-elements[<?php echo esc_attr(trim($widget_key)); ?>]" class="afeb-checkbox" type="checkbox" name="afeb_active_widgets[<?php echo esc_attr(trim($widget_key)); ?>][status]" value="1" <?php checked(intval($widget['status']), 1); ?>>
                        <label for="afeb-active-elements[<?php echo esc_attr(trim($widget_key)); ?>]"></label>
                    </a>
                    <a class="afeb-tooltip-box fa fa-eye" href="<?php echo esc_url(Widgets::AFEB_WIDGETS_URL . strtolower(str_replace('_', '-', $widget_key))); ?>" target="_blank">
                        <span class="afeb-tooltip-text">
                            <?php echo esc_html__('Demo', 'addons-for-elementor-builder'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Element Section -->
    <?php
        $count++;
    endforeach;
    ?>
    <div class="afeb-element-form-submit-box afeb-w-100">
        <input type="hidden" name="afeb_active_widgets[Deactive][status]" value="0">
        <?php wp_nonce_field('afeb-widgets-action', 'afeb-widgets-nonce'); ?>
        <button class="afeb-box-btn afeb-primary-btn" type="submit">
            <?php esc_html_e('Save', 'addons-for-elementor-builder'); ?>
        </button>
    </div>
</form>
<!-- End Elements Section -->