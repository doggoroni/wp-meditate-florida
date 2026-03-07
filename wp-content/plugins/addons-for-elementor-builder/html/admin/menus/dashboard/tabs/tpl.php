<?php

use AFEB\Widgets;

if (!defined('ABSPATH')) {
    exit;
}

$widgets = new Widgets();
?>

<!-- Start Tab -->
<div class="afeb-row">
    <div class="afeb-col-lg-12">
        <div class="afeb-tab-bar">
            <ul>
                <li><a class="<?php echo $this->tab == 'dashboard' ? 'afeb-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=afeb')); ?>"><span class="fa fa-home"></span><?php esc_html_e('Dashboard', 'addons-for-elementor-builder'); ?></a></li>
                <li><a class="<?php echo $this->tab == 'widgets' ? 'afeb-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=afeb&tab=widgets')); ?>"><span class="fa fa-th-large"></span><?php esc_html_e('Widgets', 'addons-for-elementor-builder'); ?></a></li>
                <?php if (count($widgets->trdpt_widgets()) > 0): ?><li><a class="<?php echo $this->tab == '3rdpt-widgets' ? 'afeb-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=afeb&tab=3rdpt-widgets')); ?>"><span class="fa fa-th"></span><?php esc_html_e('3rd Party Widgets', 'addons-for-elementor-builder'); ?></a></li><?php endif; ?>
                <li><a class="<?php echo $this->tab == 'extensions' ? 'afeb-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=afeb&tab=extensions')); ?>"><span class="fa fa-plus"></span><?php esc_html_e('Extensions', 'addons-for-elementor-builder'); ?></a></li>
                <li><a class="<?php echo $this->tab == 'templates-kit' ? 'afeb-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=afeb&tab=templates-kit')); ?>"><span class="fa fa-th-list"></span><?php esc_html_e('Templates Kit', 'addons-for-elementor-builder'); ?></a></li>
                <li><a class="<?php echo $this->tab == 'settings' ? 'afeb-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=afeb&tab=settings')); ?>"><span class="fa fa-cogs"></span><?php esc_html_e('Settings', 'addons-for-elementor-builder'); ?></a></li>
                <!-- <?php if (!defined('AFEBP_LITE_VS')) : ?><li><a class="<?php echo $this->tab == 'go-pro' ? 'afeb-tab-active' : ''; ?>" href="<?php echo esc_url(admin_url('admin.php?page=afeb&tab=go-pro')); ?>"><span class="fa fa-gem"></span><?php esc_html_e('Go Pro', 'addons-for-elementor-builder'); ?></a></li><?php endif; ?> -->
                <?php do_action('afeb/html/dashboard/tab', $this->tab); ?>
            </ul>
        </div>
    </div>
</div>
<!-- End Tab -->

<div class="afeb-space afeb-mr-20"></div>