<?php

class LSDRC_Theme extends LSDRC_Base
{
    protected $logister_has_errors = false;

    public function ajax_logister()
    {
        $logister_errors = new WP_Error();
        $action = isset($_POST['listdomer_action']) ? sanitize_text_field(wp_unslash($_POST['listdomer_action'])) : '';

        if (!in_array($action, ['login', 'register'], true))
        {
            wp_send_json_error([
                'message' => esc_html__('Invalid request.', 'listdomer-core'),
            ]);
        }

        $nonce = isset($_POST['listdomer_logister_nonce']) ? sanitize_text_field(wp_unslash($_POST['listdomer_logister_nonce'])) : '';
        if (!$nonce || !wp_verify_nonce($nonce, 'listdomer_logister'))
        {
            wp_send_json_error([
                'message' => esc_html__('Security nonce is missing or invalid!', 'listdomer-core'),
            ]);
        }

        $current_url = home_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));
        $defaults = [];
        $args = wp_parse_args([], apply_filters('login_form_defaults', $defaults));

        if ($action === 'login')
        {
            $login_redirect_page = LSDRC_Settings::get('listdomer_login_redirect');
            if ($login_redirect_page) $login_redirect = get_permalink($login_redirect_page);
            else $login_redirect = '';

            if (!$login_redirect && isset($args['redirect']) && trim($args['redirect'])) $login_redirect = $args['redirect'];
            if (!$login_redirect) $login_redirect = $current_url;

            $user_login = isset($_POST['log']) ? wp_unslash($_POST['log']) : '';
            $user_password = isset($_POST['pwd']) ? wp_unslash($_POST['pwd']) : '';

            if ($user_login === '' || $user_password === '')
            {
                $logister_errors->add('empty_credentials', esc_html__('Username or password is empty.', 'listdomer-core'));
            }
            else
            {
                $credentials = [
                    'user_login' => sanitize_text_field($user_login),
                    'user_password' => $user_password,
                    'remember' => isset($_POST['rememberme']),
                ];

                $user = wp_signon($credentials, is_ssl());
                if (is_wp_error($user)) $logister_errors = $user;
            }

            if ($logister_errors->has_errors())
            {
                $messages = array_map('wp_kses_post', $logister_errors->get_error_messages());
                wp_send_json_error([
                    'message' => implode('<br>', $messages),
                    'action' => 'login',
                ]);
            }

            $requested_redirect_to = isset($_POST['redirect_to']) ? trim(wp_unslash($_POST['redirect_to'])) : '';
            $redirect_to = $requested_redirect_to !== ''
                ? wp_validate_redirect($requested_redirect_to, $login_redirect)
                : $login_redirect;

            // Keep compatibility with plugins/themes that customize post-login routing.
            $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $user);
            $redirect_to = wp_validate_redirect($redirect_to, $login_redirect);

            wp_send_json_success([
                'message' => esc_html__('Signed in successfully.', 'listdomer-core'),
                'redirect' => $redirect_to,
                'action' => 'login',
            ]);
        }

        if (!get_option('users_can_register'))
        {
            wp_send_json_error([
                'message' => esc_html__('Registration is disabled.', 'listdomer-core'),
                'action' => 'register',
            ]);
        }

        $register_redirect_page = LSDRC_Settings::get('listdomer_register_redirect');
        if ($register_redirect_page) $register_redirect = get_permalink($register_redirect_page);
        else $register_redirect = '';

        if (!$register_redirect && isset($args['redirect']) && trim($args['redirect'])) $register_redirect = $args['redirect'];
        if (!$register_redirect) $register_redirect = $current_url;

        $user_login = isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '';
        $user_email = isset($_POST['user_email']) ? wp_unslash($_POST['user_email']) : '';

        if (class_exists('LSD_User') && method_exists('LSD_User', 'register'))
        {
            $registered = LSD_User::register($user_login, $user_email);
        }
        else
        {
            $registered = register_new_user($user_login, $user_email);
        }

        if (is_wp_error($registered))
        {
            $messages = array_map('wp_kses_post', $registered->get_error_messages());
            wp_send_json_error([
                'message' => implode('<br>', $messages),
                'action' => 'register',
            ]);
        }

        $redirect_to = isset($_POST['redirect_to']) && trim((string) $_POST['redirect_to'])
            ? wp_validate_redirect(wp_unslash($_POST['redirect_to']), $register_redirect)
            : $register_redirect;

        wp_send_json_success([
            'message' => esc_html__('Registration completed. Please check your email.', 'listdomer-core'),
            'redirect' => $redirect_to,
            'action' => 'register',
        ]);
    }
    public function register_block_pattern()
    {
        register_block_pattern(
            'listdomer/listdomer-two-buttons-pattern',
            [
                'title' => esc_html__('Listdomer Two buttons', 'listdomer-core'),
                'description' => _x('Two horizontal buttons. The left is filled and the right is outlined.', 'Listdomer Two horizontal buttons', 'listdomer-core'),
                'content' => "<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons wp-listdomer-block-buttons aligncenter\"><!-- wp:button {\"backgroundColor\":\"very-dark-gray\",\"borderRadius\":0} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-background has-very-dark-gray-background-color no-border-radius\">" . esc_html__('Button One', 'listdomer-core') . "</a></div>\n<!-- /wp:button -->\n\n<!-- wp:button {\"textColor\":\"very-dark-gray\",\"borderRadius\":0,\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link has-text-color has-very-dark-gray-color no-border-radius\">" . esc_html__('Button Two', 'listdomer-core') . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
            ]
        );
    }

    public function register_block_style()
    {
        register_block_style('core/button', [
            'name' => 'listdomer-button',
            'label' => esc_html__('Listdomer Button', 'listdomer-core'),
        ]);

        register_block_style('core/table', [
            'name' => 'listdomer-table',
            'label' => esc_html__('Listdomer Table', 'listdomer-core'),
        ]);
    }

    public function header_buttons()
    {
        // General Settings
        $settings = class_exists('LSD_Options') ? LSD_Options::settings() : [];

        // Dashboard Page
        $dashboard_page_id = isset($settings['submission_page']) && $settings['submission_page'] ? $settings['submission_page'] : 0;

        // Language Switcher Status
        $language_switcher_status = LSDRC_Settings::get('listdomer_header_language_switcher');

        // Language Switcher
        if ($language_switcher_status)
        {
            if (function_exists('pll_the_languages'))
            {
                $output = pll_the_languages([
                    'show_flags' => 1,
                    'show_names' => 0,
                    'echo' => 0,
                ]);

                echo '<div class="listdomer-language-switcher"><div class="listdomer-polylang"><ul>' . $output . '</ul></div></div>';
            }
            else
            {
                ob_start();
                do_action('wpml_language_switcher', [
                    'flags' => 1,
                    'native' => 0,
                    'translated' => 0,
                    'show_current' => 0,
                ], '');

                $output = ob_get_clean();
                if (trim($output)) echo '<div class="listdomer-language-switcher listdomer-wpml">' . $output . '</div>';
            }
        }

        // User Buttons Status
        $user_buttons_status = LSDRC_Settings::get('listdomer_header_logister');

        // User Button
        if (is_user_logged_in() && $user_buttons_status)
        {
            if ($dashboard_page_id)
            {
                $url = add_query_arg(['mode' => 'manage'], get_permalink($dashboard_page_id));
                echo '<div class="listdomer-user-button"><a class="listdomer-dashboard-link" href="' . esc_url($url) . '"><i class="fas fa-tachometer-alt"></i></a></div>';
            }
        }
        else if ($user_buttons_status)
        {
            echo '<div class="listdomer-user">';
            echo '<button class="listdomer-user-button" id="listdomer-user-button" aria-label="' . esc_attr__('Listdomer user button', 'listdomer-core') . '"><span><i class="far fa-user"></i></span></button>';
            $logister = $this->logister();
            $logister_class = 'listdomer-user-logister-wrapper';
            if ($this->logister_has_errors) $logister_class .= ' listdomer-util-show';
            echo '<div class="' . esc_attr($logister_class) . '">' . LSDRC_Base::kses($logister) . '</div>';
            echo '</div>';
        }

        // Add Listing Status
        $add_listing_status = LSDRC_Settings::get('listdomer_header_add_listing');
        $add_listing_text = LSDRC_Settings::get('listdomer_header_add_listing_text');

        // Add Listing Button
        if ($add_listing_status && $dashboard_page_id)
        {
            $url = add_query_arg(['mode' => 'form'], get_permalink($dashboard_page_id));

            echo '<div class="listdomer-add-listing"><a href="' . esc_url($url) . '">' . $add_listing_text . '</a></div>';
        }
    }

    public function archive_description()
    {
        $description = term_description();
        if (empty($description)) return;

        if (!class_exists('LSDR_Settings') || !class_exists('LSDR_Base') || !method_exists('LSDR_Base', 'lsd_get_trimmed_description'))
        {
            echo '<p>' . wp_kses_post($description) . '</p>';
            return;
        }

        // Check if limit is enabled
        $is_limit_enabled = LSDR_Settings::get('listdomer_page_desc_listing_archive_limit_enable', true);

        if ($is_limit_enabled)
        {
            $limit = LSDR_Settings::get('listdomer_page_desc_listing_archive_limit', 300);
            $desc_data = LSDR_Base::lsd_get_trimmed_description($description, $limit);

            if (empty($desc_data)) return;

            $output = '';

            // Description content with short and full versions
            $output .= '<div class="lsd-description-content" data-short="' . esc_attr($desc_data['short'] ?? '') . '" data-full="' . esc_attr($desc_data['full'] ?? '') . '">';
            $output .= '<p>' . wp_kses_post($desc_data['short'] ?? '') . '</p>';
            $output .= '</div>';

            // Show more button
            if (!empty($desc_data['is_show_more']))
            {
                $output .= '<button class="lsd-description-toggle" type="button" data-state="less" data-show-more-label="' . esc_attr__('Show More', 'listdomer-core') . '" data-show-less-label="' . esc_attr__('Show Less', 'listdomer-core') . '">';
                $output .= esc_html__('Show More', 'listdomer-core');
                $output .= '</button>';
            }
        }
        else $output = '<p>' . wp_kses_post($description) . '</p>';

        echo $output;
    }

    public function logister()
    {
        $logister_errors = new WP_Error();
        $logister_action = '';
        $show_register = false;
        $request_method = isset($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : '';
        $current_url = home_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));

        if ($request_method === 'POST' && isset($_POST['listdomer_action']))
        {
            $logister_action = sanitize_text_field(wp_unslash($_POST['listdomer_action']));
            if (!in_array($logister_action, ['login', 'register'], true)) $logister_action = '';

            if ($logister_action)
            {
                $nonce = isset($_POST['listdomer_logister_nonce']) ? sanitize_text_field(wp_unslash($_POST['listdomer_logister_nonce'])) : '';
                if (!$nonce || !wp_verify_nonce($nonce, 'listdomer_logister'))
                {
                    $logister_errors->add('invalid_nonce', esc_html__('Security nonce is missing or invalid!', 'listdomer-core'));
                }
            }
        }

        $defaults = [];

        /**
         * Filters the default login form output arguments.
         *
         * @param array $defaults An array of default login form arguments.
         * @see wp_login_form()
         *
         * @since 3.0.0
         *
         */
        $args = wp_parse_args([], apply_filters('login_form_defaults', $defaults));

        /**
         * Filters content to display at the top of the login form.
         *
         * The filter evaluates just following the opening form tag element.
         *
         * @param string $content Content to display. Default empty.
         * @param array $args Array of login form arguments.
         * @since 3.0.0
         *
         */
        $login_form_top = apply_filters('login_form_top', '', $args);

        /**
         * Filters content to display in the middle of the login form.
         *
         * The filter evaluates just following the location where the 'login-password'
         * field is displayed.
         *
         * @param string $content Content to display. Default empty.
         * @param array $args Array of login form arguments.
         * @since 3.0.0
         *
         */
        $login_form_middle = apply_filters('login_form_middle', '', $args);

        /**
         * Filters content to display at the bottom of the login form.
         *
         * The filter evaluates just preceding the closing form tag element.
         *
         * @param string $content Content to display. Default empty.
         * @param array $args Array of login form arguments.
         * @since 3.0.0
         *
         */
        $login_form_bottom = apply_filters('login_form_bottom', '', $args);

        // Lost Password URL
        $lostpassword_url = wp_lostpassword_url();

        // Registration Status
        $registration_status = get_option('users_can_register');

        $login_redirect_page = LSDRC_Settings::get('listdomer_login_redirect');
        if ($login_redirect_page) $login_redirect = get_permalink($login_redirect_page);
        else $login_redirect = '';

        if (!$login_redirect && isset($args['redirect']) && trim($args['redirect'])) $login_redirect = $args['redirect'];

        if (!$login_redirect) $login_redirect = $current_url;

        if ($logister_action === 'login' && !$logister_errors->has_errors())
        {
            $user_login = isset($_POST['log']) ? wp_unslash($_POST['log']) : '';
            $user_password = isset($_POST['pwd']) ? wp_unslash($_POST['pwd']) : '';

            if ($user_login === '' || $user_password === '')
            {
                $logister_errors->add('empty_credentials', esc_html__('Username or password is empty.', 'listdomer-core'));
            }
            else
            {
                $credentials = [
                    'user_login' => sanitize_text_field($user_login),
                    'user_password' => $user_password,
                    'remember' => isset($_POST['rememberme']),
                ];

                $user = wp_signon($credentials, is_ssl());

                if (is_wp_error($user)) $logister_errors = $user;
                else
                {
                    $redirect_to = isset($_POST['redirect_to']) && trim((string) $_POST['redirect_to'])
                        ? wp_validate_redirect(wp_unslash($_POST['redirect_to']), $login_redirect)
                        : $login_redirect;

                    wp_safe_redirect($redirect_to);
                    exit;
                }
            }
        }

        $login_form_class = ($logister_action === 'register' && $logister_errors->has_errors()) ? ' class="listdomer-util-hide"' : '';
        $login_error = '';
        if ($logister_action === 'login' && $logister_errors->has_errors())
        {
            $messages = array_map('wp_kses_post', $logister_errors->get_error_messages());
            $login_error = '<div class="lsdr-alert lsdr-error">' . implode('<br>', $messages) . '</div>';
        }

        $login = '<form name="listdomer-loginform"' . $login_form_class . ' action="' . esc_url($current_url) . '" method="post">
			<input type="hidden" name="listdomer_action" value="login">
			<input type="hidden" name="listdomer_logister_nonce" value="' . esc_attr(wp_create_nonce('listdomer_logister')) . '">
			' . $login_form_top . '
            <div class="listdomer-logister-message"></div>
			' . $login_error . '
			
                        <label for="listdomer_header_login_username" class="screen-reader-text">' . esc_html__('Username', 'listdomer-core') . '</label>
			<input type="text" id="listdomer_header_login_username" name="log" placeholder="' . esc_attr__('Username', 'listdomer-core') . '" value="" size="20">
                        <label for="listdomer_header_login_password" class="screen-reader-text">' . esc_html__('Password', 'listdomer-core') . '</label>
			<input type="password" id="listdomer_header_login_password" name="pwd" placeholder="' . esc_attr__('Password', 'listdomer-core') . '" value="" size="20">
			
			' . $login_form_middle . '
			<div class="listdomer-login-forgot-wrapper">
			    <a class="listdomer-lostpassword" href="' . esc_url($lostpassword_url) . '">' . esc_html__('Lost your password?', 'listdomer-core') . '</a>
				<label class="listdomer-login-remember listdomer-checkbox">' . esc_html__('Remember Me', 'listdomer-core') . '<input name="rememberme" type="checkbox" value="forever"><span class="checkmark"></span></label>
            </div>
			
			<input type="submit" name="wp-submit" value="' . esc_attr__('Sign In', 'listdomer-core') . '">
			<input type="hidden" name="redirect_to" value="' . esc_url($login_redirect) . '">
			
			' . ($registration_status ? '<div class="listdomer-register-wrapper">
				<p>' . esc_html__("Don't have an account?", 'listdomer-core') . '</p>
				<span class="listdomer-login-register-toggle" data-target="register">' . esc_html__('Create Account', 'listdomer-core') . '</span>
			</div>' : '') . '
			' . $login_form_bottom . '
		</form>';

        // Filter Login Form
        $login = apply_filters('lsdrc_header_login_form', $login);

        // Registration is not allowed
        if (!$registration_status) return $login;

        ob_start();
        do_action('register_form');
        $hook = ob_get_clean();

        $register_redirect_page = LSDRC_Settings::get('listdomer_register_redirect');
        if ($register_redirect_page) $register_redirect = get_permalink($register_redirect_page);
        else $register_redirect = '';

        if (!$register_redirect && isset($args['redirect']) && trim($args['redirect'])) $register_redirect = $args['redirect'];

        if (!$register_redirect) $register_redirect = $current_url;

        if ($logister_action === 'register' && !$logister_errors->has_errors())
        {
            $show_register = true;
            $user_login = isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '';
            $user_email = isset($_POST['user_email']) ? wp_unslash($_POST['user_email']) : '';

            if (class_exists('LSD_User') && method_exists('LSD_User', 'register'))
            {
                $registered = LSD_User::register($user_login, $user_email);
            }
            else
            {
                $registered = register_new_user($user_login, $user_email);
            }

            if (is_wp_error($registered)) $logister_errors = $registered;
            else
            {
                $redirect_to = isset($_POST['redirect_to']) && trim((string) $_POST['redirect_to'])
                    ? wp_validate_redirect(wp_unslash($_POST['redirect_to']), $register_redirect)
                    : $register_redirect;

                wp_safe_redirect($redirect_to);
                exit;
            }
        }

        $register_form_class = ($show_register || ($logister_action === 'register' && $logister_errors->has_errors())) ? '' : ' listdomer-util-hide';
        $register_error = '';
        if ($logister_action === 'register' && $logister_errors->has_errors())
        {
            $messages = array_map('wp_kses_post', $logister_errors->get_error_messages());
            $register_error = '<div class="lsdr-alert lsdr-error">' . implode('<br>', $messages) . '</div>';
        }

        $register = '<form name="listdomer-registerform" class="' . esc_attr(trim($register_form_class)) . '" action="' . esc_url($current_url) . '" method="post" novalidate="novalidate">
            <input type="hidden" name="listdomer_action" value="register">
            <input type="hidden" name="listdomer_logister_nonce" value="' . esc_attr(wp_create_nonce('listdomer_logister')) . '">
            <div class="listdomer-logister-message"></div>
            
            <label for="listdomer_header_register_username" class="screen-reader-text">' . esc_html__('Username', 'listdomer-core') . '</label>
			<input type="text" id="listdomer_header_register_username" name="user_login" value="" size="20" autocapitalize="off" placeholder="' . esc_attr__('Username', 'listdomer-core') . '">
                        <label for="listdomer_header_register_email" class="screen-reader-text">' . esc_html__('Email', 'listdomer-core') . '</label>
			<input type="email" id="listdomer_header_register_email" name="user_email" value="" size="25" placeholder="' . esc_attr__('Email', 'listdomer-core') . '">
			
			' . $hook . '
			' . $register_error . '
			<div class="lsdr-alert lsdr-info">' . esc_html__('Registration confirmation will be emailed to you.', 'listdomer-core') . '</div>
			
			<input type="hidden" name="redirect_to" value="' . esc_url($register_redirect) . '">
			<input type="submit" name="wp-submit" value="' . esc_attr__('Register', 'listdomer-core') . '">
			
			<div class="listdomer-login-wrapper">
				<p>' . esc_html__('Have an account?', 'listdomer-core') . '</p>
				<span class="listdomer-login-register-toggle" data-target="login">' . esc_html__('Sign in', 'listdomer-core') . '</span>
			</div>
		</form>';

        // Filter Registration Form
        $register = apply_filters('lsdrc_header_register_form', $register);

        // Social Login
        $social_shortcode = LSDRC_Settings::get('listdomer_social_login_shortcode');

        $social = '';
        if ($social_shortcode)
        {
            $social = '<div class="listdomer-other-signup">
                <div class="listdomer-other-signup-header">
                    <h4>' . esc_html__('Or sign up with', 'listdomer-core') . '</h4>
                </div>
              
                ' . do_shortcode($social_shortcode) . '
            </div>';
        }

        // Filter Social Logins
        $social = apply_filters('lsdrc_header_social_logins', $social);

        $this->logister_has_errors = $logister_errors->has_errors();

        return trim($login . $register . $social);
    }
}
