<?php

use AFEB\Base;

if (!defined('ABSPATH')) exit; ?>
<div id="afeb-template-import-dialog" class="afeb-component-dialog" data-height="auto" data-width="600">

    <div class="afeb-row afeb-template-import-confirmation afeb-mr-0 afeb-d-none">
        <div class="afeb-col-4">
            <img class="afeb-template-import-image" src="" alt="">
        </div>
        <div class="afeb-col-8 afeb-pd-top-20 afeb-pd-bottom-20">
            <div class="afeb-template-import-name"></div>
            <h2 class="afeb-template-import-question"><?php esc_html_e('Do you want to import this demo?', 'addons-for-elementor-builder'); ?></h2>
            <div>
                <?php esc_html_e('This will overwrite your site settings and add new content. You might want to backup your site before proceeding.', 'addons-for-elementor-builder'); ?>
            </div>
            <div class="afeb-template-kit-btns">
                <button type="button" class="afeb-template-kit-import-btn">
                    <?php esc_html_e('Import', 'addons-for-elementor-builder'); ?>
                    <span class="fa fa-download"></span>
                </button>
                <button type="button" class="afeb-template-kit-cancel-btn">
                    <?php esc_html_e('Cancel', 'addons-for-elementor-builder'); ?>
                    <span class="fa fa-times"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="afeb-col-12 afeb-template-import afeb-d-none">
        <div class="afeb-template-import-please-wait"
            data-message="<?php esc_html_e('Please Wait ...', 'addons-for-elementor-builder'); ?>">
        </div>
        <h2 class="afeb-template-import-heading"
            data-message="<?php esc_html_e('The demo is being imported.', 'addons-for-elementor-builder'); ?>"
            data-error-message="<?php esc_html_e('An error occurred during the import process.', 'addons-for-elementor-builder'); ?>">
        </h2>
        <div class="afeb-template-import-description"
            data-message="<?php esc_html_e('The import process can take a few seconds depending on the size of the kit you are importing and speed of the connection.', 'addons-for-elementor-builder'); ?>"
            data-error-message="<?php esc_html_e('Please check the error text below and try again after fixing it or contact support.', 'addons-for-elementor-builder'); ?>">
        </div>
        <div class="afeb-template-import-tip afeb-alert afeb-info"
            data-message="<?php esc_html_e('Please do NOT close this browser window until import is completed.', 'addons-for-elementor-builder'); ?>">
        </div>
        <div class="afeb-template-import-progress-wrap">
            <div class="afeb-template-import-progress-markers">
                <div class="afeb-template-import-progress-markers-plugins">
                    <div>
                        <span class="fa fa-braille"></span>
                    </div>
                </div>
                <div class="afeb-template-import-progress-markers-settings">
                    <div>
                        <span class="fa fa-cogs"></span>
                    </div>
                </div>
                <div class="afeb-template-import-progress-markers-content">
                    <div>
                        <span class="fa fa-cubes"></span>
                    </div>
                </div>
            </div>
            <div class="afeb-template-import-progress"></div>
        </div>
        <?php
        $status = [
            'plugins' => esc_html__('Installing/Activating Plugins', 'addons-for-elementor-builder'),
            'content' => esc_html__('Importing Demo Content', 'addons-for-elementor-builder'),
            'settings' => esc_html__('Importing Settings', 'addons-for-elementor-builder'),
        ];
        ?>
        <div class="afeb-template-import-status" data-status='<?php echo wp_json_encode($status); ?>'></div>
        <div class="afeb-template-import-get-help">
            <?php echo sprintf(
                '%s %s',
                esc_html__('Having trouble with template import?', 'addons-for-elementor-builder'),
                '<a href="' . esc_url(Base::AFEB_URL . '/support?hs_ticket_category=GENERAL_INQUIRY#form') . '" target="_blank">' .
                    esc_html__('Get help', 'addons-for-elementor-builder') .
                    '</a>'
            ); ?>
            <i class="fa fa-life-ring"></i>
        </div>
    </div>

    <div class="afeb-template-import-successful afeb-d-none">
        <div class="afeb-template-import-successful-icon fa fa-check"></div>
        <h2 class="afeb-template-import-successful-heading"><?php esc_html_e('Import Was Successful!', 'addons-for-elementor-builder'); ?> 🎉 🥳</h2>
        <div class="afeb-template-import-successful-description">
            <?php esc_html_e('Your website is ready. Enter the dashboard and start customizing the demo.', 'addons-for-elementor-builder'); ?>
        </div>
        <div class="afeb-template-import-successful-get-help">
            <?php echo sprintf(
                '%s %s',
                esc_html__('Having trouble with template import?', 'addons-for-elementor-builder'),
                '<a href="' . esc_url(Base::AFEB_URL . '/support?hs_ticket_category=GENERAL_INQUIRY#form') . '" target="_blank">' .
                    esc_html__('Get help', 'addons-for-elementor-builder') .
                    '</a>'
            ); ?>
            <i class="fa fa-life-ring"></i>
        </div>

        <a class="afeb-template-kit-import-btn"
            href="<?php echo home_url(); ?>"
            target="_blank">
            <?php esc_html_e('Visit Website', 'addons-for-elementor-builder'); ?>
            <span class="fa fa-eye"></span>
        </a>
    </div>

</div>
