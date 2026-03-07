<?php

use AFEB\Base;

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap afeb-wrap afeb-dashboard-wrap">
    <div class="afeb-space afeb-mr-20"></div>

    <!-- Start Notice -->
    <div class="afeb-container">
        <div class="afeb-row afeb-dashboard-notice-box">
            <div class="afeb-col-lg-12">
                <h1 class="afeb-d-none"></h1>
            </div>
        </div>
    </div>
    <!-- End Notice -->

    <!-- Start Topbar -->
    <div class="afeb-top-bar">

        <!-- Start Topbar Main Section -->
        <div class="afeb-top-bar-main-section">
            <div class="afeb-top-bar-logo">
                <a href="<?php echo esc_url(admin_url('admin.php?page=' . \AFEB\Menus::MENUS_SLUG)); ?>">
                    <div class="afeb-logo"></div>
                    <div class="afeb-logo-text"></div>
                </a>
            </div>
        </div>
        <!-- End Topbar Main Section -->

        <!-- Start Topbar Secondary Section -->
        <div class="afeb-top-bar-secondary-section">
            <div class="afeb-top-bar-actions">
                <?php if (!defined('AFEBP_LITE_VS')): ?>
                    <a class="afeb-box-btn afeb-primary-btn" href="<?php echo esc_url(Base::AFEB_URL) . '/pricing'; ?>" target="_blank">
                        <?php esc_html_e("Get the Pro", 'addons-for-elementor-builder'); ?>
                    </a>
                <?php endif; ?>
                <a class="afeb-box-btn afeb-sec-primary-btn" href="<?php echo esc_url(Base::WEBILIA_API_URL) . '/go/vertex-docs'; ?>" target="_blank">
                    <?php esc_html_e('Documentation', 'addons-for-elementor-builder'); ?>
                </a>
            </div>
        </div>
        <!-- End Topbar Secondary Section -->

    </div>
    <!-- End Topbar -->

    <div class="afeb-container">

        <!-- Dashboard Tabs -->
        <?php $this->get_template_part(__DIR__ . '/tabs'); ?>

        <!-- Dashboard Content -->
        <?php $this->get_template_part(__DIR__ . '/fragments'); ?>

    </div>

    <p class="afeb-admin-footer">
        <?php esc_html_e('Made by', 'addons-for-elementor-builder'); ?>
        <a id="afeb-admin-footer-link" href="<?php echo esc_url(Base::WEBILIA_URL); ?>" target="_blank"><img src="<?php echo esc_url($this->assets_url('img/webilia.svg')); ?>" alt="<?php esc_html_e('Webilia', 'addons-for-elementor-builder'); ?>"></a>
    </p>

</div>