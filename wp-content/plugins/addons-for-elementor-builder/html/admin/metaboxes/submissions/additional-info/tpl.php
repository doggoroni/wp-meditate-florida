<?php if (!defined('ABSPATH')) exit; ?>
<div class="submitbox">
    <div id="minor-publishing">
        <div id="misc-publishing-actions">
            <?php global $post;
            $id = isset($post->ID) ? $post->ID : -1; ?>
            <!-- Start Page Section -->
            <div class="misc-pub-section">
                <span id="page">
                    <i class=""></i>
                    <?php
                    echo esc_html__('Page', 'addons-for-elementor-builder');
                    $page_id = get_post_meta($id, 'afeb_page_id', true);
                    ?>:
                    <a href="<?php echo get_the_permalink($page_id) ?>" title="<?php echo get_the_title($page_id); ?>">
                        <b><?php echo esc_html(get_the_title($page_id)); ?></b>
                    </a>
                </span>
            </div>
            <!-- End Page Section -->

            <!-- Start Create Date Section -->
            <div class="misc-pub-section">
                <span id="create-date">
                    <?php echo esc_html__('Create Date', 'addons-for-elementor-builder'); ?>:
                    <b>
                        <?php echo get_the_date('F j, Y', $id); ?>
                    </b>
                </span>
            </div>
            <!-- End Create Date Section -->

            <!-- Start User Name Section -->
            <div class="misc-pub-section">
                <b id="user-name">
                    <?php echo esc_html__('User Name', 'addons-for-elementor-builder'); ?>:
                    <b></b>
                </b>
            </div>
            <!-- End User Name Section -->

            <!-- Start User IP Section -->
            <div class="misc-pub-section">
                <span id="user-ip">
                    <?php
                    echo esc_html__('User IP', 'addons-for-elementor-builder');
                    $user_ip = get_post_meta($id, 'afeb_user_ip', true);
                    ?>:
                    <b>
                        <?php echo esc_html($user_ip); ?>
                    </b>
                </span>
            </div>
            <!-- End User IP Section -->

            <!-- Start User Agent Section -->
            <div class="misc-pub-section">
                <span id="user-agent">
                    <?php
                    echo esc_html__('User Agent', 'addons-for-elementor-builder');
                    $user_agent = get_post_meta($id, 'afeb_user_agent', true);
                    ?>:
                    <b>
                        <?php echo esc_html($user_agent); ?>
                    </b>
                </span>
            </div>
            <!-- End User Agent Section -->

        </div>
        <div class="clear"></div>
    </div>

    <div id="major-publishing-actions">
        <div id="delete-action">
            <a class="submitdelete deletion" href="<?php echo get_delete_post_link(); ?>">
                <?php echo esc_html__('Move to Trash', 'addons-for-elementor-builder'); ?>
            </a>
        </div>
        <div id="publishing-action">
            <span class="spinner"></span>
            <input name="original_publish" type="hidden" id="original_publish" value="Update">
            <input type="submit" name="save" id="publish" class="button button-primary button-large" value="Update">
        </div>
        <div class="clear"></div>
    </div>
</div>