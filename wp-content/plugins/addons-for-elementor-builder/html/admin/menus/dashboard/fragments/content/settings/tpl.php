<?php

use AFEB\Form;
use AFEB\Helper;

if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('afeb-settings', []);
?>
<!-- Start Elements Section -->
<form class="afeb-row" action="" method="post">

    <!-- Start Element Section -->
    <div class="afeb-element-search-section afeb-col-lg-4 afeb-mr-top-20">
        <div class="afeb-box-section afeb-element-section">
            <div class="afeb-box-header">
                <?php $ttl = esc_html__('Login, Register', 'addons-for-elementor-builder'); ?>
                <img src="<?php echo esc_url($this->assets_url('img/widgets/login-register.svg')); ?>" alt="<?php echo esc_attr($ttl); ?>">
                <h2><?php echo $ttl; ?></h2>
            </div>
            <div class="afeb-box-body">
                <div>
                    <?php esc_html_e('You can set a page as the default login, registration, or lost password page in WordPress', 'addons-for-elementor-builder'); ?>
                </div>
                <?php
                $lr_settings = !empty($settings['widgets']['login_register']) ?
                    $settings['widgets']['login_register'] : [];
                $pages = Helper::get_posts_by_type('page', ['def_item' => 'non']);
                ?>
                <!-- Start Body Section -->
                <div class="afeb-box-body-section">
                    <label for="afeb-login">
                        <?php esc_html_e('Login', 'addons-for-elementor-builder'); ?>
                    </label>
                    <div class="afeb-box-body-section-control">
                        <?php
                        $selected = !empty($lr_settings['default_login_page']) ?
                            $lr_settings['default_login_page'] : '';
                        Form::select_e([
                            'id' => 'afeb-login',
                            'name' => 'afeb_settings[widgets][login_register][default_login_page]'
                        ], ['values' => $pages, 'selected' => $selected]);
                        ?>
                    </div>
                </div>
                <!-- End Body Section -->

                <!-- Start Body Section -->
                <div class="afeb-box-body-section">
                    <label for="afeb-register">
                        <?php esc_html_e('Registration', 'addons-for-elementor-builder'); ?>
                    </label>
                    <div class="afeb-box-body-section-control">
                        <?php
                        $selected = !empty($lr_settings['default_register_page']) ?
                            $lr_settings['default_register_page'] : '';
                        Form::select_e([
                            'id' => 'afeb-register',
                            'name' => 'afeb_settings[widgets][login_register][default_register_page]'
                        ], ['values' => $pages, 'selected' => $selected]);
                        ?>
                    </div>
                </div>
                <!-- End Body Section -->

                <!-- Start Body Section -->
                <div class="afeb-box-body-section">
                    <label for="afeb-lost-password">
                        <?php esc_html_e('Lost Password', 'addons-for-elementor-builder'); ?>
                    </label>
                    <div class="afeb-box-body-section-control">
                        <?php
                        $selected = !empty($lr_settings['default_lostpass_page']) ?
                            $lr_settings['default_lostpass_page'] : '';
                        Form::select_e([
                            'id' => 'afeb-lost-password',
                            'name' => 'afeb_settings[widgets][login_register][default_lostpass_page]'
                        ], ['values' => $pages, 'selected' => $selected]);
                        ?>
                    </div>
                </div>
                <!-- End Body Section -->

            </div>
        </div>
    </div>
    <!-- End Element Section -->

    <div class="afeb-element-form-submit-box afeb-w-100">
        <?php wp_nonce_field('afeb-settings-action', 'afeb-settings-nonce'); ?>
        <button class="afeb-box-btn afeb-primary-btn" type="submit">
            <?php esc_html_e('Save', 'addons-for-elementor-builder'); ?>
        </button>
    </div>
</form>
<!-- End Elements Section -->