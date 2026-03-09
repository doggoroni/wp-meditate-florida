<?php
/**
 * Listdomer Child — Footer Type 1
 *
 * Overrides parent's partials/footers/footer.php.
 * Changes vs parent:
 *  - Adds newsletter signup band above widget areas
 *  - If the 'mfl-newsletter' sidebar has a widget, renders that instead
 *    of the built-in form (so you can drop in a MailChimp/Mailerlite widget)
 */
?>
<footer role="contentinfo" id="colophon" class="site-footer">

    <!-- ─── Newsletter band ──────────────────────────────────────────────── -->
    <div class="mfl-newsletter-band">
        <div class="container">
            <?php if (is_active_sidebar('mfl-newsletter')): ?>
                <?php dynamic_sidebar('mfl-newsletter'); ?>
            <?php else: ?>
                <h3><?php esc_html_e('Stay Mindful. Stay Connected.', 'listdomer-child'); ?></h3>
                <p><?php esc_html_e('Get the latest meditation centers, retreats, and wellness events across Florida — delivered to your inbox monthly.', 'listdomer-child'); ?></p>

                <?php
                $nl_status = sanitize_key($_GET['mfl_newsletter'] ?? '');
                if ($nl_status === 'sent') : ?>
                    <p class="mfl-newsletter-notice mfl-newsletter-notice--success">
                        <?php esc_html_e('Thanks for subscribing! We\'ll be in touch.', 'listdomer-child'); ?>
                    </p>
                <?php elseif ($nl_status === 'error') : ?>
                    <p class="mfl-newsletter-notice mfl-newsletter-notice--error">
                        <?php esc_html_e('Please enter a valid email address and try again.', 'listdomer-child'); ?>
                    </p>
                <?php endif; ?>

                <form class="mfl-newsletter-form"
                      action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                      method="post"
                      aria-label="<?php esc_attr_e('Newsletter signup', 'listdomer-child'); ?>">
                    <input type="hidden" name="action" value="mfl_newsletter_signup">
                    <?php wp_nonce_field('mfl_newsletter_signup', 'mfl_newsletter_nonce'); ?>
                    <input
                        type="email"
                        name="mfl_email"
                        placeholder="<?php esc_attr_e('Your email address', 'listdomer-child'); ?>"
                        required
                        aria-label="<?php esc_attr_e('Email address', 'listdomer-child'); ?>"
                        autocomplete="email"
                    >
                    <button type="submit">
                        <?php esc_html_e('Subscribe', 'listdomer-child'); ?>
                    </button>
                </form>

                <p class="mfl-newsletter-note">
                    <?php esc_html_e('No spam, ever. Unsubscribe at any time.', 'listdomer-child'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ─── Footer widget areas (sidebars 1–4 from parent) ─────────────── -->
    <?php get_template_part('partials/footer-widgets', 'none'); ?>

    <!-- ─── Sub-footer / copyright ──────────────────────────────────────── -->
    <?php
        $subfooter_menu = wp_nav_menu([
            'theme_location' => 'menu-2',
            'menu'           => LSDR_Settings::get('listdomer_footer_menu_select', 'subfooter-menu'),
            'menu_id'        => 'subfooter-menu',
            'echo'           => false,
        ]);
    ?>
    <div class="container">
        <div class="site-subfooter">

            <?php if (trim($subfooter_menu)): ?>
                <nav id="site-subfooter-navigation" class="subfooter-navigation" role="navigation"
                     aria-label="<?php esc_attr_e('Sub Footer', 'listdomer-child'); ?>">
                    <?php echo $subfooter_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </nav>
            <?php endif; ?>

            <p class="mfl-footer-credit">
                &copy; <?php echo esc_html(date('Y')); ?> Meditate Florida &mdash; Florida&#8217;s Mindfulness Directory
            </p>

        </div>
    </div>

</footer>
