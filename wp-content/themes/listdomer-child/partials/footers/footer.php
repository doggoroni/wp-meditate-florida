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

                <form class="mfl-newsletter-form"
                      action="#"
                      method="post"
                      aria-label="<?php esc_attr_e('Newsletter signup', 'listdomer-child'); ?>">
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
    <?php get_template_part('partials/sub-footer', 'none'); ?>

</footer>
