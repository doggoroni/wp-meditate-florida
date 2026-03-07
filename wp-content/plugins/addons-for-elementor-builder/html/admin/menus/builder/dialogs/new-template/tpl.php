<?php if (!defined('ABSPATH')) exit; ?>
<div id="afeb-new-template-dialog" class="afeb-component-dialog" title="<?php echo esc_html__('Template Builder', 'addons-for-elementor-builder'); ?>" data-height="450">
    <h2 class="afeb-description">
        <?php echo esc_html__('Create a new template for your site and make your changes on it and enjoy', 'addons-for-elementor-builder'); ?>
    </h2>
    <div class="afeb-new-template-type">
        <label for="afeb-new-template-type"><?php echo esc_html__('Type', 'addons-for-elementor-builder'); ?></label>
        <select name="afeb_new_template_type">
            <option value="archive"><?php echo esc_html__('Archive', 'addons-for-elementor-builder'); ?></option>
            <option value="dynamic-loop-item"><?php echo esc_html__('Dynamic Loop Item', 'addons-for-elementor-builder'); ?></option>
            <option value="footer"><?php echo esc_html__('Footer', 'addons-for-elementor-builder'); ?></option>
            <option value="header"><?php echo esc_html__('Header', 'addons-for-elementor-builder'); ?></option>
            <option value="single"><?php echo esc_html__('Single', 'addons-for-elementor-builder'); ?></option>
        </select>
    </div>
    <div class="afeb-new-template-name">
        <label for="afeb-new-template-name"><?php echo esc_html__('Name (optional)', 'addons-for-elementor-builder'); ?></label>
        <input id="afeb-new-template-name" type="text" name="afeb_new_template_name" placeholder="<?php echo esc_html__('Enter template name', 'addons-for-elementor-builder'); ?>">
    </div>
    <button class="afeb-box-btn afeb-primary-btn afeb-w-100"
        data-enter-name-msg="<?php echo esc_html__('Please enter a valid name', 'addons-for-elementor-builder'); ?>"
        data-err-msg="<?php echo esc_html__('Something went wrong, Please try again', 'addons-for-elementor-builder'); ?>">
        <span class="fa fa-spinner fa-spin"></span>
        <?php echo esc_html__('Create Template', 'addons-for-elementor-builder'); ?>
    </button>
</div>