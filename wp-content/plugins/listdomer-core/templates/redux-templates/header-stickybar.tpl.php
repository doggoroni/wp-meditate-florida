<?php
// No Direct Access
defined('ABSPATH') || die();

/**
 * The template for the header sticky bar.
 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
 *
 * @author        Redux Framework
 * @package       ReduxFramework/Templates
 * @version:      4.4.2
 */
?>
<div id="redux-sticky">
	<div id="info_bar">
                <a href="javascript:void(0);"
                   class="expand_options<?php echo esc_attr(($this->parent->args['open_expanded']) ? ' expanded' : ''); ?>"<?php echo(true === $this->parent->args['hide_expand'] ? ' style="display: none;"' : ''); ?>>
                        <?php esc_attr_e('Expand', 'listdomer-core'); ?>
		</a>
		<div class="redux-action_bar">
			<span class="spinner"></span>
			<?php
                        if (false === $this->parent->args['hide_save'])
                        {
                                submit_button(esc_attr__('Save Changes', 'listdomer-core'), 'primary', 'redux_save', false, ['id' => 'redux_top_save']);
                        }

                        if (false === $this->parent->args['hide_reset'])
                        {
                                submit_button(esc_attr__('Reset Section', 'listdomer-core'), 'secondary', $this->parent->args['opt_name'] . '[defaults-section]', false, ['id' => 'redux-defaults-section-top']);
                                submit_button(esc_attr__('Reset All', 'listdomer-core'), 'secondary', $this->parent->args['opt_name'] . '[defaults]', false, ['id' => 'redux-defaults-top']);
                        }
			?>
		</div>
		<div class="clear"></div>
	</div>

	<!-- Notification bar -->
	<div id="redux_notification_bar">
		<?php $this->notification_bar(); ?>
	</div>
</div>
