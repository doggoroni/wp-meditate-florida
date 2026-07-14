<?php

use AFEB\Base;
use AFEB\Menus\Dashboard;
use AFEB\System;

if (!defined('ABSPATH')) {
    exit;
}

$system = new System();

?>
<!-- Start First Section -->
<div class="afeb-row">

    <!-- Start Welcome Section -->
    <div class="afeb-col-lg-4 afeb-m-md">
        <div class="afeb-box-section afeb-welcome-section">
            <div class="afeb-box-header">
                <img src="<?php echo esc_url($this->assets_url('img/dashboard-welcome.svg')); ?>" alt="<?php esc_html_e('Welcome', 'addons-for-elementor-builder'); ?>">
                <h2>
                    <?php esc_html_e('Hello Webilia', 'addons-for-elementor-builder'); ?>
                </h2>
            </div>
            <div class="afeb-space afeb-mr-20"></div>
            <div class="afeb-box-body">
                <p>
                    <?php esc_html_e('Webilia is your companions on the exciting journey of online creation. We understand that your website is the canvas for your digital masterpiece, and we’re here to provide the best WordPress plugins and themes you need to paint your vision.', 'addons-for-elementor-builder'); ?>
                </p>
                <div class="afeb-space afeb-mr-20"></div>
                <a class="afeb-box-btn afeb-secondary-btn" href="<?php echo esc_url(Base::WEBILIA_URL); ?>" target="_blank">
                    <?php esc_html_e('Open our site', 'addons-for-elementor-builder'); ?>
                </a>
            </div>
        </div>
    </div>
    <!-- End Welcome Section -->

    <!-- Start Change Log Section -->
    <div class="afeb-col-lg-8">
        <div class="afeb-box-section afeb-changelog-section">
            <div class="afeb-box-header">
                <img src="<?php echo esc_url($this->assets_url('img/dashboard-change-log.svg')); ?>" alt="<?php esc_html_e('Change Log', 'addons-for-elementor-builder'); ?>">
                <h2>
                    <?php esc_html_e("What's New?", 'addons-for-elementor-builder'); ?>
                </h2>
                <div class="afeb-space afeb-mr-20"></div>
                <p>
                    <?php esc_html_e('Notable additions made to The Vertex Addons for Elementor.', 'addons-for-elementor-builder'); ?>
                </p>
            </div>
            <div class="afeb-space afeb-mr-20"></div>
            <div class="afeb-box-body">

                <div class="afeb-changelog-list-box">
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('February 27, 2026', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.6.4', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Fixed : Inline script type handling in the Custom CSS/JS Extension.', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Fixed : Guarded Pro widgets usage in the dashboard.', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('January 1, 2026', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.6.3', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Fixed : Authentication Error Display Issue.', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('November 9, 2025', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.6.2', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Fixed : Redirection Issue.', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('October 28, 2025', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.6.1', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Fixed : Enhanced the security and fixed some minor issues.', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('October 22, 2025', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.6.0', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Added : Woo Checkout Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Woo Add To Cart Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Woo Product Category Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Woo Product Tags Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Product Image Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Product Content Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Product Excerpt Widget', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('August 17, 2025', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.5.3', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Fixed : Issues in the Custom Footer Scripts', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('July 9, 2025', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.5.2', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Fixed : Issues in the Dynamic Grid/Carousel Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Fixed : Issues related to the Templates Kit Importer', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('May 20, 2025', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.5.1', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Fixed : Issue in the Advanced Menus Widget', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                    <div class="afeb-changelog-date">
                        <?php esc_html_e('May 19, 2025', 'addons-for-elementor-builder'); ?>
                        <span class="afeb-changelog-version"><?php esc_html_e('Version 1.5.0', 'addons-for-elementor-builder'); ?></span>
                    </div>
                    <ul class="afeb-changelog-list">
                        <li><?php esc_html_e('Added : Creative Button Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Post Navigation Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Flip Box Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Team Member Carousel Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Slides Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Ripple Effects Extension', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Preloader Extension', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Visibility Controls Extension', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Live search and Ajax functionality in the Search Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Layout animation feature in the Dynamic Grid/Carousel Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Irregular grid feature for the 2-column layout in the Dynamic Grid/Carousel Widget', 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e("Added : 'Stay in Column' feature in the Sticky Extension", 'addons-for-elementor-builder'); ?></li>
                        <li><?php esc_html_e('Added : Feature to display pages related to each demo in the Templates Kit section', 'addons-for-elementor-builder'); ?></li>
                    </ul>
                </div>
                <div class="afeb-space afeb-mr-20"></div>
                <a class="afeb-box-btn afeb-secondary-btn" href="https://wordpress.org/plugins/addons-for-elementor-builder/#developers" target="_blank">
                    <?php esc_html_e('Lite Full Change Log', 'addons-for-elementor-builder'); ?>
                </a>
            </div>
        </div>
    </div>
    <!-- End Change Log Section -->

</div>
<!-- End First Section -->

<div class="afeb-space afeb-mr-20"></div>

<!-- Start Second Section -->
<div class="afeb-row">

    <!-- Start Sys info - Support & Feedback Section -->
    <div class="afeb-col-lg-6">

        <!-- Start System Info -->
        <div class="afeb-col-lg-12 afeb-pd-0">
            <div class="afeb-box-section afeb-system-info-section">
                <div class="afeb-box-header">
                    <img src="<?php echo esc_url($this->assets_url('img/dashboard-system-info.svg')); ?>" alt="<?php esc_html_e('Basic System Info', 'addons-for-elementor-builder'); ?>">
                    <h2>
                        <?php esc_html_e('Basic System Info', 'addons-for-elementor-builder'); ?>
                    </h2>
                    <div class="afeb-space afeb-mr-20"></div>
                    <p>
                        <?php esc_html_e('See the basic information of the system here.', 'addons-for-elementor-builder'); ?>
                    </p>
                </div>
                <div class="afeb-space afeb-mr-20"></div>
                <div class="afeb-box-body">
                    <div class="afeb-row">
                        <div class="afeb-col-lg-6 afeb-pd-0">
                            <table class="afeb-table afeb-w-100">
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php esc_html_e('WP Version', 'addons-for-elementor-builder'); ?>
                                        </td>
                                        <td>
                                            <?php echo floatval($system->wp->get_version()); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php esc_html_e('PHP Version', 'addons-for-elementor-builder'); ?>
                                        </td>
                                        <td>
                                            <?php echo floatval($system->server->get_php_version()); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Memory Limit', 'addons-for-elementor-builder'); ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($system->wp->get_memory_limit()); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="afeb-col-lg-6 afeb-pd-0">
                            <table class="afeb-table afeb-w-100">
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Max Upload Size', 'addons-for-elementor-builder'); ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($system->wp->get_max_upload_size()); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Debug Mode', 'addons-for-elementor-builder'); ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($system->wp->get_debug_mode()); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Max post size', 'addons-for-elementor-builder'); ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($system->server->get_php_max_post_size()); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End System Requirement -->

        <div class="afeb-space afeb-mr-20"></div>

        <!-- Start Support & Feedback -->
        <div class="afeb-col-lg-12 afeb-pd-0 afeb-m-md">
            <div class="afeb-box-section afeb-support-feedback-section">
                <div class="afeb-box-header">
                    <img src="<?php echo esc_url($this->assets_url('img/dashboard-support-feedback.svg')); ?>" alt="<?php esc_html_e('Support & Feedback', 'addons-for-elementor-builder'); ?>">
                    <h2>
                        <?php esc_html_e('Support & Feedback', 'addons-for-elementor-builder'); ?>
                    </h2>
                </div>
                <div class="afeb-space afeb-mr-20"></div>
                <div class="afeb-box-body">
                    <p class="afeb-col-lg-9 afeb-pd-0">
                        <?php esc_html_e('Tried everything but not found a solution? Our premium support team is always there for your backup. Just click the button below.', 'addons-for-elementor-builder'); ?>
                    </p>
                    <div class="afeb-space afeb-mr-20"></div>
                    <a class="afeb-box-btn afeb-primary-btn" href="<?php echo esc_url(Base::AFEB_URL . '/support?hs_ticket_category=GENERAL_INQUIRY#form'); ?>" target="_blank">
                        <?php esc_html_e('Go to Support Center', 'addons-for-elementor-builder'); ?>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Support & Feedback -->

    </div>
    <!-- End Sys Info - Support & Feedback Section -->

    <!-- Start Latest News Section -->
    <div class="afeb-col-lg-6">
        <div class="afeb-box-section afeb-latest-news-section">
            <div class="afeb-box-header">
                <img src="<?php echo esc_url($this->assets_url('img/dashboard-latest-news.svg')); ?>" alt="<?php esc_html_e('Latest News About Vertex Addons for Elementor', 'addons-for-elementor-builder'); ?>">
                <h2>
                    <?php esc_html_e('Latest News About Vertex Addons for Elementor', 'addons-for-elementor-builder'); ?>
                </h2>
                <div class="afeb-space afeb-mr-20"></div>
                <p>
                    <?php esc_html_e('Follow the latest content and important news related to Vertex Addons for Elementor plugin from here.', 'addons-for-elementor-builder'); ?>
                </p>
            </div>
            <div class="afeb-space afeb-mr-20"></div>
            <div class="afeb-box-body">
                <?php
                $base = new Base();
                $dom_doc = $base->dom_doc(Dashboard::DASHBOARD_LATEST_NEWS_FEED, 2 * 84600); //Two Day
                $counter = 0;
                foreach ($dom_doc as $item) {
                    if ($counter > 4) {
                        break;
                    }
                    if (isset($item['title'])) {
                ?>
                        <div class="afeb-latest-news-item-box">
                            <div class="afeb-latest-news-item">
                                <a href="<?php echo isset($item['link']) ? esc_url($item['link']) : ''; ?>" target="_blank">
                                    <?php echo esc_html($item['title']); ?>
                                </a>
                                <div class="afeb-space afeb-mr-5"></div>
                                <p>
                                    <?php echo isset($item['description']) ? esc_html(substr($item['description'], 0, 60)) . '...' : ''; ?>
                                </p>
                            </div>
                        </div>
                        <div class="afeb-space afeb-mr-20"></div>
                <?php
                        $counter++;
                    }
                }
                ?>

                <div class="afeb-space afeb-mr-20"></div>
                <a class="afeb-box-btn afeb-sec-secondary-btn" href="<?php echo esc_url(Base::AFEB_URL . '/blog'); ?>" target="_blank">
                    <?php esc_html_e('Read More', 'addons-for-elementor-builder'); ?>
                </a>
                <div class="afeb-space afeb-mr-20"></div>
            </div>
        </div>
    </div>
    <!-- End Latest News Section -->

</div>
<!-- End Second Section -->

<div class="afeb-space afeb-mr-20"></div>

<!-- Start Third Section -->
<div class="afeb-row">

    <!-- Start FAQ Section -->
    <div class="afeb-col-lg-6 afeb-m-md">
        <div class="afeb-box-section afeb-faq-section">
            <div class="afeb-box-header">
                <img src="<?php echo esc_url($this->assets_url('img/dashboard-frequently-asked-questions.svg')); ?>" alt="<?php esc_html_e('Change Log', 'addons-for-elementor-builder'); ?>">
                <h2>
                    <?php esc_html_e('Frequently Asked Questions', 'addons-for-elementor-builder'); ?>
                </h2>
            </div>
            <div class="afeb-space afeb-mr-20"></div>
            <div class="afeb-box-body">

                <!-- Accordion -->
                <div class="afeb-component-accordion" data-active="100" data-axis="y" data-handle="div.afeb-accordion-box-header" data-header="div > div.afeb-accordion-box-header" data-height-style="content">

                    <!-- AccordionBox -->
                    <div class="afeb-accordion-box">

                        <!-- AccordionBoxHeader -->
                        <div class="afeb-accordion-box-header">
                            <?php esc_html_e('What does Vertex Addons for Elementor add to my site?', 'addons-for-elementor-builder'); ?>
                            <div class="afeb-accordion-box-header-icon">
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                        <!-- End AccordionBoxHeader -->

                        <!-- AccordionBoxBody -->
                        <div class="afeb-accordion-box-body">
                            <?php esc_html_e('Currently, Vertex Addons for Elementor adds a set of practical and professional widgets to your Elementor page builder for greater convenience and speed.', 'addons-for-elementor-builder'); ?>
                        </div>
                        <!-- End AccordionBoxBody -->

                    </div>
                    <!-- End AccordionBox -->

                    <!-- AccordionBox -->
                    <div class="afeb-accordion-box">

                        <!-- AccordionBoxHeader -->
                        <div class="afeb-accordion-box-header">
                            <?php esc_html_e('Which version of Elementor should we use?', 'addons-for-elementor-builder'); ?>
                            <div class="afeb-accordion-box-header-icon">
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                        <!-- End AccordionBoxHeader -->

                        <!-- AccordionBoxBody -->
                        <div class="afeb-accordion-box-body">
                            <?php esc_html_e('It is better to always use the latest version of Elementor', 'addons-for-elementor-builder'); ?>
                        </div>
                        <!-- End AccordionBoxBody -->

                    </div>
                    <!-- End AccordionBox -->

                    <!-- AccordionBox -->
                    <div class="afeb-accordion-box">

                        <!-- AccordionBoxHeader -->
                        <div class="afeb-accordion-box-header">
                            <?php esc_html_e('Is it compatible with every theme?', 'addons-for-elementor-builder'); ?>
                            <div class="afeb-accordion-box-header-icon">
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                        <!-- End AccordionBoxHeader -->

                        <!-- AccordionBoxBody -->
                        <div class="afeb-accordion-box-body">
                            <?php esc_html_e("Yes, but there might be a few highly unlikely exceptions to be honest. If you're that unlucky to get one of those incompatible themes in your basket, let us know and we will fix it.", 'addons-for-elementor-builder'); ?>
                        </div>
                        <!-- End AccordionBoxBody -->

                    </div>
                    <!-- End AccordionBox -->

                    <!-- AccordionBox -->
                    <div class="afeb-accordion-box">

                        <!-- AccordionBoxHeader -->
                        <div class="afeb-accordion-box-header">
                            <?php esc_html_e('Is there any coding skill required to use these addons?', 'addons-for-elementor-builder'); ?>
                            <div class="afeb-accordion-box-header-icon">
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                        <!-- End AccordionBoxHeader -->

                        <!-- AccordionBoxBody -->
                        <div class="afeb-accordion-box-body">
                            <?php esc_html_e('Nope, these widgets are made to bring smiles on your face. So go ahead, grab a cup of coffee and start using Vertex Addons for Elementor from today.', 'addons-for-elementor-builder'); ?>
                        </div>
                        <!-- End AccordionBoxBody -->

                    </div>
                    <!-- End AccordionBox -->

                    <!-- AccordionBox -->
                    <div class="afeb-accordion-box">

                        <!-- AccordionBoxHeader -->
                        <div class="afeb-accordion-box-header">
                            <?php esc_html_e('Are you adding more widgets to this collection?', 'addons-for-elementor-builder'); ?>
                            <div class="afeb-accordion-box-header-icon">
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                        <!-- End AccordionBoxHeader -->

                        <!-- AccordionBoxBody -->
                        <div class="afeb-accordion-box-body">
                            <?php esc_html_e("Yes, of course! We're constantly working on Vertex Addons for Elementor and trying to make it better", 'addons-for-elementor-builder'); ?>
                        </div>
                        <!-- End AccordionBoxBody -->

                    </div>
                    <!-- End AccordionBox -->

                </div>
                <!-- End Accordion -->

            </div>
        </div>
    </div>
    <!-- End FAQ Section -->

    <!-- Start Try Our Other Products Section -->
    <div class="afeb-col-lg-6">
        <div class="afeb-box-section afeb-try-our-other-products-section">
            <div class="afeb-box-header">
                <img src="<?php echo esc_url($this->assets_url('img/dashboard-webilia-logo-panel.svg')); ?>" alt="<?php esc_html_e('Webilia', 'addons-for-elementor-builder'); ?>">
                <div class="afeb-space afeb-mr-20"></div>
                <h2>
                    <?php esc_html_e('Try Our Other Products and Services', 'addons-for-elementor-builder'); ?>
                </h2>
            </div>
            <div class="afeb-space afeb-mr-20"></div>
            <div class="afeb-box-body">
                <p>
                    <?php esc_html_e('Webilia has some brilliant solutions for your digital needs. One of best products is Listdom. A final solution for directory and classified websites.', 'addons-for-elementor-builder'); ?>
                </p>
                <div class="afeb-space afeb-mr-20"></div>
                <a class="afeb-box-btn afeb-sec-primary-btn" href="<?php echo esc_url(Base::WEBILIA_URL); ?>/listdom" target="_blank">
                    <?php esc_html_e('Get Listdom', 'addons-for-elementor-builder'); ?>
                </a>
            </div>
        </div>
    </div>
    <!-- End Try Our Other Products Section -->

</div>
<!-- End Third Section -->

<div class="afeb-space afeb-mr-20"></div>

<!-- Start Fourth Section -->
<div class="afeb-row">

    <!-- Start MAF Section -->
    <div class="afeb-col-lg-6 afeb-m-md">

        <div class="afeb-box-section afeb-maf-section">
            <div class="afeb-row">
                <div class="afeb-col-lg-4 afeb-maf-image"></div>
                <div class="afeb-col-lg-8 afeb-m-md">
                    <div class="afeb-box-header">
                        <img src="<?php echo esc_url($this->assets_url('img/dashboard-maf.svg')); ?>" alt="<?php esc_html_e('Missing Any Feature?', 'addons-for-elementor-builder'); ?>">
                        <h2>
                            <?php esc_html_e('Missing Any Feature?', 'addons-for-elementor-builder'); ?>
                        </h2>
                    </div>
                    <div class="afeb-space afeb-mr-20"></div>
                    <div class="afeb-box-body">
                        <p class="afeb-col-lg-10 afeb-pd-0">
                            <?php esc_html_e("Are you in need of a feature that's not available in our plugin? Feel free to do a feature request from here", 'addons-for-elementor-builder'); ?>
                        </p>
                        <div class="afeb-space afeb-mr-20"></div>
                        <a class="afeb-box-btn afeb-sec-secondary-btn" href="<?php echo esc_url(Base::AFEB_URL . '/road-map'); ?>" target="_blank">
                            <?php esc_html_e('Ask For The Feature', 'addons-for-elementor-builder'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- End MAF Section -->

    <!-- Start Give Us A Review Section -->
    <div class="afeb-col-lg-6">
        <div class="afeb-box-section afeb-give-us-review-section">
            <div class="afeb-box-header">
                <img src="<?php echo esc_url($this->assets_url('img/dashboard-give-us-review.svg')); ?>" alt="<?php esc_html_e('Give Us A Review', 'addons-for-elementor-builder'); ?>">
                <h2>
                    <?php esc_html_e('Happy with Our Work?', 'addons-for-elementor-builder'); ?>
                </h2>
            </div>
            <div class="afeb-space afeb-mr-20"></div>
            <div class="afeb-box-body">
                <p class="afeb-col-lg-9 afeb-pd-0">
                    <?php esc_html_e("We are really thankful to you that you have chosen our plugin. If our plugin brings a smile in your face while working, please share your happiness by giving us a 5 starts rating in WordPress Org. It will make us happy and won't take more than 2 minutes.", 'addons-for-elementor-builder'); ?>
                </p>
                <div class="afeb-space afeb-mr-20"></div>
                <a class="afeb-box-btn afeb-primary-btn" href="https://wordpress.org/support/plugin/addons-for-elementor-builder/reviews/" target="_blank">
                    <?php esc_html_e("Rate On WordPress", 'addons-for-elementor-builder'); ?>
                </a>
            </div>
        </div>
    </div>
    <!-- End Give Us A Review Section -->

</div>
<!-- End Fourth Section -->
