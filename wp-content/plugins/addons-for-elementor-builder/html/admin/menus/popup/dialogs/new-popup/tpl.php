<?php if (!defined('ABSPATH')) exit; ?>
<div id="afeb-new-popup-dialog" class="afeb-component-dialog" title="<?php echo esc_html__('Popup Builder', 'addons-for-elementor-builder'); ?>">
    <h2 class="afeb-description">
        <?php echo esc_html__('Create a new popup for your site and make your changes on it and enjoy', 'addons-for-elementor-builder'); ?>
    </h2>
    <div class="afeb-new-popup-name">
        <label for="afeb-new-popup-name"><?php echo esc_html__('Name (optional)', 'addons-for-elementor-builder'); ?></label>
        <input id="afeb-new-popup-name" type="text" name="afeb_new_popup_name" placeholder="<?php echo esc_html__('Enter popup name', 'addons-for-elementor-builder'); ?>">
    </div>
    <button class="afeb-box-btn afeb-primary-btn afeb-w-100"
        data-enter-name-msg="<?php echo esc_html__('Please enter a valid name', 'addons-for-elementor-builder'); ?>"
        data-err-msg="<?php echo esc_html__('Something went wrong, Please try again', 'addons-for-elementor-builder'); ?>">
        <span class="fa fa-spinner fa-spin"></span>
        <?php echo esc_html__('Create Popup', 'addons-for-elementor-builder'); ?>
    </button>
</div>