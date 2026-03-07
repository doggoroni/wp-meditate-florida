<?php

use AFEB\Helper;
use AFEB\Widgets;
use AFEB\PRO\Widgets as ProWidgets;

if (!defined('ABSPATH')) {
    exit;
}

$widgets = new Widgets();
$pro_widgets = class_exists(ProWidgets::class) ? new ProWidgets() : null;
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
    $count = 0;
    $trdpt_widgets_array = array_replace_recursive($widgets->trdpt_widgets(), get_option('afeb-3rdpt-widgets-status', []));

    foreach ($trdpt_widgets_array as $widget_key => $widget) :
        $trdpt_plugin = !isset($widget['pro']) || !$pro_widgets ?
            $widgets->trdpt_plugins($widget_key) :
            $pro_widgets->trdpt_plugins($widget_key);
        $status = true;

        if (!Helper::is_plugin_installed($trdpt_plugin['pname'], $trdpt_plugin['ppath'])) $status = 'pni';
        else if (!is_plugin_active($trdpt_plugin['ppath'])) $status = 'pna';
    ?>
        <!-- Start Element Section -->
        <div class="afeb-element-search-section afeb-col-lg-4 <?php echo $count > 2 ? 'afeb-mr-top-20' : ''; ?>">
            <div class="afeb-box-section afeb-element-section">
                <div class="afeb-box-header">
                    <?php
                    $image_filename = isset($widget['image']) ? ltrim($widget['image'], '/') : '';
                    $img_url = $image_filename ? $this->assets_url('img/widgets/' . $image_filename) : '';
                    if ($img_url && isset($widget['pro'])) {
                        $img_url = str_replace('addons-for-elementor-builder', 'addons-for-elementor-builder-pro', $img_url);
                    }
                    ?>
                    <img class="afeb-element-search-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo (isset($widget['title']) && trim($widget['title'])) ? esc_attr(trim($widget['title'])) : ''; ?>">
                    <h2 class="afeb-element-search-text">
                        <?php echo (isset($widget['title']) && trim($widget['title'])) ? esc_html(trim($widget['title'])) : ''; ?>
                    </h2>
                    <?php if ($status === true) : ?>
                        <div class="afeb-element-status <?php echo intval($widget['status']) != 1 ? 'afeb-element-status-deactive' : ''; ?>"></div>
                    <?php else : ?>
                        <i class="afeb-element-status-alert-ic fa fa-exclamation"></i>
                    <?php endif; ?>
                </div>
                <div class="afeb-element-actions">
                    <?php if ($status === true) : ?>
                        <a class="afeb-form-group afeb-element-checkbox-box <?php echo intval($widget['status']) != 1 ? 'afeb-deactive-checkbox' : ''; ?>">
                            <input type="hidden" name="afeb_active_3rdpt_widgets[<?php echo esc_attr(trim($widget_key)); ?>][status]" value="0">
                            <input id="afeb-active-elements[<?php echo esc_attr(trim($widget_key)); ?>]" class="afeb-checkbox" type="checkbox" name="afeb_active_3rdpt_widgets[<?php echo esc_attr(trim($widget_key)); ?>][status]" value="1" <?php checked(intval($widget['status']), 1); ?>>
                            <label for="afeb-active-elements[<?php echo esc_attr(trim($widget_key)); ?>]"></label>
                        </a>
                    <?php elseif ($status === 'pni') : $install_link = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $trdpt_plugin['pname']), 'install-plugin_' . $trdpt_plugin['pname']); ?>
                        <a class="afeb-tooltip-box afeb-iplugins-tooltip-box afeb-act-alert fa fa-download" href="<?php echo esc_url($install_link); ?>" target="_blank">
                            <input id="afeb-active-elements[<?php echo esc_attr(trim($widget_key)); ?>]" class="afeb-checkbox" type="checkbox" name="afeb_active_3rdpt_widgets[<?php echo esc_attr(trim($widget_key)); ?>][status]" value="1" <?php checked(intval($widget['status']), 1); ?>>
                            <span class="afeb-tooltip-text">
                                <?php echo esc_html__('Install the plugin', 'addons-for-elementor-builder'); ?>
                            </span>
                        </a>
                    <?php elseif ($status === 'pna') : $active_link = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $trdpt_plugin['ppath'] . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $trdpt_plugin['ppath']); ?>
                        <a class="afeb-tooltip-box afeb-iplugins-tooltip-box afeb-act-alert fa fa-magic" href="<?php echo esc_url($active_link); ?>" target="_blank">
                            <input id="afeb-active-elements[<?php echo esc_attr(trim($widget_key)); ?>]" class="afeb-checkbox" type="checkbox" name="afeb_active_3rdpt_widgets[<?php echo esc_attr(trim($widget_key)); ?>][status]" value="1" <?php checked(intval($widget['status']), 1); ?>>
                            <span class="afeb-tooltip-text">
                                <?php echo esc_html__('Plugin activation', 'addons-for-elementor-builder'); ?>
                            </span>
                        </a>
                    <?php endif; ?>
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
        <input type="hidden" name="afeb_active_3rdpt_widgets[Deactive][status]" value="0">
        <?php wp_nonce_field('afeb-widgets-action', 'afeb-widgets-nonce'); ?>
        <button class="afeb-box-btn afeb-primary-btn" type="submit">
            <?php esc_html_e('Save', 'addons-for-elementor-builder'); ?>
        </button>
    </div>
</form>
<!-- End Elements Section -->
