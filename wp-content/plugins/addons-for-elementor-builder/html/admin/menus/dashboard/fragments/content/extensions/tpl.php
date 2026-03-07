<?php

use AFEB\Extensions;

if (!defined('ABSPATH')) {
    exit;
}

$extensions = new Extensions();
?>
<!-- Start Search Elements Section -->
<div class="afeb-space afeb-mr-20"></div>
<div class="afeb-row">
    <div class="afeb-col-lg-4">
        <div class="afeb-elements-search-section">
            <span class="afeb-elements-search-input-icon fa fa-search"></span>
            <input class="afeb-elements-search-input" type="text" placeholder="<?php esc_html_e('Search the Extensions', 'addons-for-elementor-builder'); ?>">
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
    $extensions_array = array_replace_recursive($extensions->extensions(), get_option('afeb-extensions-status', []));

    foreach ($extensions_array as $extension_key => $extension) :
        $has_pro = isset($extension['pro']) ? 1 : 0;
    ?>
        <!-- Start Element Section -->
        <div class="afeb-element-search-section afeb-col-lg-4 <?php echo $count > 2 ? 'afeb-mr-top-20' : ''; ?>">
            <div class="afeb-box-section afeb-element-section">
                <div class="afeb-box-header">
                    <img class="afeb-element-search-img" src="<?php echo esc_url($this->assets_url('img/extensions/' . strtolower(str_replace('_', '-', $extension_key)) . '.svg')); ?>" alt="<?php echo (isset($extension['title']) && trim($extension['title'])) ? esc_attr(trim($extension['title'])) : ''; ?>">
                    <h2 class="afeb-element-search-text">
                        <?php echo isset($extension['title']) && trim($extension['title']) ? esc_html(trim($extension['title'])) : ''; ?>
                    </h2>
                    <?php if (!$has_pro || ($has_pro && defined('AFEBP_LITE_VS'))) : ?>
                        <div class="afeb-element-status <?php echo intval($extension['status']) != 1 ? 'afeb-element-status-deactive' : ''; ?>"></div>
                    <?php else : ?>
                        <i class="afeb-element-status-alert-ic afeb-pro-version-ic fa fa-lock">
                            <span>
                                <?php echo esc_html__('PRO', 'addons-for-elementor-builder'); ?>
                            </span>
                        </i>
                    <?php endif; ?>
                </div>
                <div class="afeb-element-actions">
                    <?php if (!$has_pro || ($has_pro && defined('AFEBP_LITE_VS'))) : ?>
                        <a class="afeb-form-group afeb-element-checkbox-box <?php echo intval($extension['status']) != 1 ? 'afeb-deactive-checkbox' : ''; ?>">
                            <input type="hidden" name="afeb_active_extensions[<?php echo esc_attr(trim($extension_key)); ?>][status]" value="0">
                            <input id="afeb-active-elements[<?php echo esc_attr(trim($extension_key)); ?>]" class="afeb-checkbox" type="checkbox" name="afeb_active_extensions[<?php echo esc_attr(trim($extension_key)); ?>][status]" value="1" <?php checked(intval($extension['status']), 1); ?>>
                            <label for="afeb-active-elements[<?php echo esc_attr(trim($extension_key)); ?>]"></label>
                        </a>
                    <?php else : ?>
                        <a class="afeb-go-to-pro-box afeb-tooltip-box fa fa-unlock-alt" href="">
                            <span class="afeb-tooltip-text">
                                <?php echo esc_html__('Get PRO Version', 'addons-for-elementor-builder'); ?>
                            </span>
                        </a>
                    <?php endif; ?>
                    <a class="afeb-tooltip-box fa fa-eye" href="<?php echo esc_url(Extensions::AFEB_EXTENSIONS_URL . strtolower(str_replace('_', '-', $extension_key))); ?>" target="_blank">
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
        <input type="hidden" name="afeb_active_extensions[Deactive][status]" value="0">
        <?php wp_nonce_field('afeb-extensions-action', 'afeb-extensions-nonce'); ?>
        <button class="afeb-box-btn afeb-primary-btn" type="submit">
            <?php esc_html_e('Save', 'addons-for-elementor-builder'); ?>
        </button>
    </div>
</form>
<!-- End Elements Section -->