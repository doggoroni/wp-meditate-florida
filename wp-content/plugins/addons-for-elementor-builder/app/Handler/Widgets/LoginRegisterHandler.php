<?php

namespace AFEB\Handler\Widgets;

use AFEB\Handler;
use AFEB\Widgets;
use Exception;

/**
 * "Vertex Addons for Elementor" LoginRegisterHandler Class
 *
 * @class LoginRegisterHandler
 * @version 1.0.3
 */
class LoginRegisterHandler extends Handler
{
    /**
     * @var int
     */
    private $page_id;

    /**
     * @var bool|int
     */
    private $widget_id;

    /**
     * @var string
     */
    private $err_msg;

    /**
     * @var string
     */
    private $sucs_msg;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var bool
     */
    private $user_can_register;

    /**
     * @var bool
     */
    private $reg_send_custom_email_user = false;

    /**
     * @var bool
     */
    private $reg_send_custom_email_admin = false;

    /**
     * @var array
     */
    private $reg_user_email_options = [];

    /**
     * @var array
     */
    private $reg_admin_email_options = [];

    /**
     * @var bool
     */
    private $lp_send_custom_email = false;

    /**
     * @var array
     */
    private $lp_email_options = [];

    /**
     * Initialize "Vertex Addons for Elementor" LoginRegisterHandler
     *
     * @since 1.0.3
     */
    public function init()
    {
        $this->user_can_register = get_option('users_can_register');
        $this->actions();
        $this->filters();
    }

    /**
     * LoginRegisterHandler Class Actions
     *
     * @since 1.0.3
     */
    public function actions()
    {
        add_action('init', [$this, 'login_or_register_user']);
        add_action('login_init', [$this, 'redirect_default_login_page']);
        add_action('login_init', [$this, 'redirect_to_reset_password_page']);
    }

    /**
     * LoginRegisterHandler Class Filters
     *
     * @since 1.3.0
     */
    public function filters()
    {
        add_filter('login_url', [$this, 'default_login_page'], 999, 2);
        add_filter('register_url', [$this, 'default_register_page'], 999, 2);
        add_filter('lostpassword_url', [$this, 'default_lost_password_page'], 999, 2);
    }

    /**
     * Login or register user handler
     *
     * @since 1.0.3
     */
    public function login_or_register_user()
    {
        do_action('afeb/login_register/before_user_handler', $_POST);

        if (isset($_POST['afeb-login-submit']))
        {
            $this->user_login();
        }
        else if (isset($_POST['afeb-register-submit']))
        {
            $this->user_register();
        }
        else if (isset($_POST['afeb-lostpassword-submit']))
        {
            $this->send_password_reset();
        }
        else if (isset($_POST['afeb-resetpassword-submit']))
        {
            $this->password_reset();
        }

        do_action('afeb/login_register/after_user_handler', $_POST);
    }

    /**
     * Checks if there is an initial error
     *
     * @param string $action
     * @since 1.0.3
     *
     */
    public function check_common_errors($action = '')
    {
        $this->page_id = 0;
        $this->err_msg = '';

        if (!empty($_POST['page_id']))
        {
            $this->page_id = absint(wp_unslash($_POST['page_id']));

            if (in_array(get_post_status($this->page_id), ['future', 'draft', 'pending']))
                $this->err_msg = esc_html__('Please publish the page first and then try again', 'addons-for-elementor-builder');
        }
        else
        {
            $this->err_msg = esc_html__('The page ID is not set', 'addons-for-elementor-builder');
        }

        $this->widget_id = false;
        if (empty($this->err_msg))
        {
            if (!empty($_POST['widget_id']))
            {
                $this->widget_id = sanitize_text_field(wp_unslash($_POST['widget_id']));
                setcookie('afeb_' . $action . '_error_' . $this->widget_id, '', time() + 2);
            }
            else
            {
                $this->err_msg = esc_html__('The widget ID is not set', 'addons-for-elementor-builder');
            }
        }
        if (empty($this->err_msg))
        {
            if (empty($_POST['afeb-' . $action . '-nonce']))
                $this->err_msg = esc_html__('The submitted form is not secure, Nonce is not set', 'addons-for-elementor-builder');
        }
        if (empty($this->err_msg) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afeb-' . $action . '-nonce'])), 'afeb-' . $action . '-action'))
            $this->err_msg = esc_html__('Security token did not match', 'addons-for-elementor-builder');

        $this->settings = Widgets::get_widget_settings($this->page_id, $this->widget_id);

        if (empty($this->err_msg) && is_user_logged_in())
            $this->err_msg = !empty($this->settings['err_logdin']) ? $this->settings['err_logdin'] : esc_html__('You are already logged in', 'addons-for-elementor-builder');

        if (empty($this->err_msg) && $action == 'register' && !$this->user_can_register)
            $this->err_msg = esc_html__('Unfortunately, The ability to register on this site is disabled', 'addons-for-elementor-builder');

        if (empty($this->err_msg) && $action == 'lostpassword')
        {
            if (isset($_POST['afeb-lostpassword-username-email']))
            {
                $lostpassword_identifier = sanitize_text_field(wp_unslash($_POST['afeb-lostpassword-username-email']));

                if (!is_email($lostpassword_identifier) && get_user_by('login', $lostpassword_identifier) === false)
                    $this->err_msg = esc_html__('There is no user registered with that user name', 'addons-for-elementor-builder');

                if (empty($this->err_msg) && is_email($lostpassword_identifier) && !email_exists(sanitize_email($lostpassword_identifier)))
                    $this->err_msg = esc_html__('There is no user registered with that email address', 'addons-for-elementor-builder');
            }
        }

        if (empty($this->err_msg) && $action == 'resetpassword')
        {
            if (empty($_POST['afeb-key']))
                $this->err_msg = esc_html__('The password reset key is not set', 'addons-for-elementor-builder');

            if (empty($this->err_msg) && empty($_POST['afeb-login']))
                $this->err_msg = esc_html__('The user login parameter is not set correctly', 'addons-for-elementor-builder');

            if (empty($this->err_msg) && empty($_POST['afeb-resetpassword-new-password']))
                $this->err_msg = esc_html__('Please enter a new password', 'addons-for-elementor-builder');
        }

        if (empty($this->err_msg) && !empty($this->settings['frm_sec_field']))
        {
            $sec = !empty($_POST['afeb-sec-field']) ? md5(absint(wp_unslash($_POST['afeb-sec-field']))) : false;
            $sec_ans = !empty($_POST['afeb-sec-field-ans']) ? sanitize_text_field(wp_unslash($_POST['afeb-sec-field-ans'])) : false;

            if (!$sec || !$sec_ans || $sec !== $sec_ans)
                $this->err_msg = !empty($this->settings['err_sec']) ? $this->settings['err_sec'] : esc_html__('Invalid security answer, Please try again', 'addons-for-elementor-builder');
        }

        if (!empty($this->err_msg))
        {
            if (!empty($this->widget_id))
                setcookie('afeb_' . $action . '_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);

            $referer = $this->get_referer_url();

            if (!empty($referer))
            {
                wp_safe_redirect($referer);
                exit();
            }
        }
    }

    /**
     * Sign in to site
     *
     * @since 1.0.3
     */
    public function user_login()
    {
        $this->check_common_errors('login');
        do_action('afeb/login_register/before_signon', $_POST, $this->settings, $this);

        $user_login = !empty($_POST['afeb-login-username']) ? sanitize_text_field(wp_unslash($_POST['afeb-login-username'])) : '';

        if (is_email($user_login)) $user_login = sanitize_email($user_login);

        $password = !empty($_POST['afeb-login-password']) ? sanitize_text_field(wp_unslash($_POST['afeb-login-password'])) : '';
        $rememberme = !empty($_POST['lgn_itm_rm_chkd']) ? sanitize_text_field(wp_unslash($_POST['lgn_itm_rm_chkd'])) : '';

        $login_info = [
            'user_login' => $user_login,
            'user_password' => $password,
            'remember' => ('forever' === $rememberme),
        ];

        $user_data = wp_signon($login_info);

        if (is_wp_error($user_data))
        {
            $this->err_msg = '';

            if (isset($user_data->errors['invalid_email'][0]))
            {
                $this->err_msg = !empty($this->settings['err_eml']) ? $this->settings['err_eml'] : esc_html__('Invalid Email. Please check your email or try again with your username', 'addons-for-elementor-builder');
            }
            else if (isset($user_data->errors['invalid_username'][0]))
            {
                $this->err_msg = !empty($this->settings['err_usrnm']) ? $this->settings['err_usrnm'] : esc_html__('Invalid Username. Please check your username or try again with your email', 'addons-for-elementor-builder');
            }
            else if (isset($user_data->errors['incorrect_password'][0]) || isset($user_data->errors['empty_password'][0]))
            {
                $this->err_msg = !empty($this->settings['err_pass']) ? $this->settings['err_pass'] : esc_html__('Please enter a valid Password', 'addons-for-elementor-builder');
            }
            else
            {
                if (!empty($user_data->errors))
                {
                    foreach ($user_data->errors as $error)
                    {
                        $this->err_msg = !empty($error[0]) ? $error[0] : (!empty($this->settings['err_unkn']) ? $this->settings['err_unkn'] : esc_html__('Something went wrong!', 'addons-for-elementor-builder'));
                        break;
                    }
                }
            }

            $this->err_msg = apply_filters('afeb/login_register/login_validation_error_message', $this->err_msg, $user_data);

            if (is_array($this->err_msg))
            {
                $this->err_msg = !empty($this->err_msg[0]) ? $this->err_msg[0] : '';
            }
            setcookie('afeb_login_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
        }
        else
        {
            wp_set_current_user($user_data->ID, $user_login);
            $redirect_act = !empty($this->settings['rdrct_aftr_lgn_act_lbc']) ? sanitize_text_field($this->settings['rdrct_aftr_lgn_act_lbc']) : '';
            $redirect_custom_url = !empty($this->settings['rdrct_aftr_lgn_cstm_url']) ? sanitize_url($this->settings['rdrct_aftr_lgn_cstm_url']['url']) : '';
            $redirect_to_prev_page_url = !empty($_POST['afeb-redirect-to-prev-page']) ? sanitize_url(wp_unslash($_POST['afeb-redirect-to-prev-page'])) : '';
            $redirect_url = '';

            do_action('wp_login', $user_data->user_login, $user_data);
            do_action('afeb/login_register/after_signon', $user_data->user_login, $user_data);

            if ($redirect_act == 'custom_url') $redirect_url = $redirect_custom_url;
            if ($redirect_act == 'previous_page') $redirect_url = $redirect_to_prev_page_url;
            if (!empty($redirect_url))
            {
                wp_safe_redirect($redirect_url);
                exit();
            }
        }
        $referer = $this->get_referer_url();

        if (!empty($referer))
        {
            wp_safe_redirect($referer);
            exit();
        }
    }

    /**
     * Register on the site
     *
     * @since 1.0.3
     */
    public function user_register()
    {
        $this->check_common_errors('register');

        do_action('afeb/login_register/before_user_register', $_POST, $this->settings, $this);

        $this->err_msg = '';
        $register_repeater = !empty($this->settings['reg_itms_rpt']) ? $this->settings['reg_itms_rpt'] : [];
        $register_info = [
            'username' => [
                'minlength' => 2,
                'maxlength' => 20,
                'value' => '',
            ],
            'email' => [
                'minlength' => 6,
                'maxlength' => 30,
                'value' => '',
            ],
            'password' => [
                'minlength' => 4,
                'maxlength' => 30,
                'value' => '',
                'auto' => false,
            ],
            'firstname' => [
                'minlength' => 2,
                'maxlength' => 20,
                'value' => '',
            ],
            'lastname' => [
                'minlength' => 2,
                'maxlength' => 20,
                'value' => '',
            ],
            'website' => [
                'minlength' => 4,
                'maxlength' => 30,
                'value' => '',
            ],
        ];

        $loop_break = false;
        $url = null;

        if (is_array($register_repeater) && count($register_repeater))
        {
            foreach ($register_repeater as $register_repeater_item)
            {
                if ($loop_break)
                {
                    break;
                }
                else if (!empty($register_repeater_item['reg_itms']))
                {
                    $fields = strtolower($register_repeater_item['reg_itms']);
                    switch ($fields)
                    {
                        case 'username':
                            if (!empty($_POST['afeb-register-username']))
                            {
                                if (!empty($register_repeater_item['reg_itm_min_lnt']))
                                    $register_info['username']['minlength'] = intval($register_repeater_item['reg_itm_min_lnt']);

                                if (!empty($register_repeater_item['reg_itm_max_lnt']))
                                    $register_info['username']['maxlength'] = intval($register_repeater_item['reg_itm_max_lnt']);

                                $register_info['username']['value'] = sanitize_user($_POST['afeb-register-username']);
                                if (!validate_username($register_info['username']['value']))
                                {
                                    $this->err_msg = !empty($this->settings['err_usrnm']) ? $this->settings['err_usrnm'] : esc_html__('Please enter a valid username', 'addons-for-elementor-builder');
                                    $loop_break = true;
                                }
                                else if ($register_info['username']['minlength'] !== null && mb_strlen($register_info['username']['value']) < $register_info['username']['minlength'])
                                {
                                    /* translators: %s is replaced with "The minimum length of the username" */
                                    $this->err_msg = sprintf(esc_html__('Username must be more than %s characters', 'addons-for-elementor-builder'), $register_info['username']['minlength']);
                                    $loop_break = true;
                                }
                                else if (mb_strlen($register_info['username']['value']) > $register_info['username']['maxlength'])
                                {
                                    /* translators: %s is replaced with "The maximum length of the username" */
                                    $this->err_msg = sprintf(esc_html__('Username must be less than %s characters', 'addons-for-elementor-builder'), $register_info['username']['maxlength']);
                                    $loop_break = true;
                                }
                                else if (username_exists($register_info['username']['value']))
                                {
                                    $this->err_msg = !empty($this->settings['err_usrnm_usd']) ? $this->settings['err_usrnm_usd'] : esc_html__('This username already exists on the site', 'addons-for-elementor-builder');
                                    $loop_break = true;
                                }
                            }
                            else
                            {
                                $this->err_msg = !empty($this->settings['err_usrnm']) ? $this->settings['err_usrnm'] : esc_html__('Please enter a valid username', 'addons-for-elementor-builder');
                                $loop_break = true;
                            }
                            break;
                        case 'email':
                            if (!empty($_POST['afeb-register-email']))
                            {
                                if (!empty($register_repeater_item['reg_itm_min_lnt']))
                                    $register_info['email']['minlength'] = intval($register_repeater_item['reg_itm_min_lnt']);

                                if (!empty($register_repeater_item['reg_itm_max_lnt']))
                                    $register_info['email']['maxlength'] = intval($register_repeater_item['reg_itm_max_lnt']);

                                $register_info['email']['value'] = sanitize_email(wp_unslash($_POST['afeb-register-email']));
                                if (!is_email($register_info['email']['value']))
                                {
                                    $this->err_msg = !empty($this->settings['err_eml']) ? $this->settings['err_eml'] : esc_html__('Please enter a valid email', 'addons-for-elementor-builder');
                                    $loop_break = true;
                                }
                                else if ($register_info['email']['minlength'] !== null && mb_strlen($register_info['email']['value']) < $register_info['email']['minlength'])
                                {
                                    /* translators: %s is replaced with "The minimum length of the email" */
                                    $this->err_msg = sprintf(esc_html__('Email must be more than %s characters', 'addons-for-elementor-builder'), $register_info['email']['minlength']);
                                    $loop_break = true;
                                }
                                else if (mb_strlen($register_info['email']['value']) > $register_info['email']['maxlength'])
                                {
                                    /* translators: %s is replaced with "The maximum length of the email" */
                                    $this->err_msg = sprintf(esc_html__('Email must be less than %s characters', 'addons-for-elementor-builder'), $register_info['email']['maxlength']);
                                    $loop_break = true;
                                }
                                else if (email_exists($register_info['email']['value']))
                                {
                                    $this->err_msg = !empty($this->settings['err_eml_usd']) ? $this->settings['err_eml_usd'] : esc_html__('This email already exists on the site', 'addons-for-elementor-builder');
                                    $loop_break = true;
                                }
                            }
                            else
                            {
                                $this->err_msg = !empty($this->settings['err_eml_mis']) ? $this->settings['err_eml_mis'] : esc_html__('Email is missing or Invalid', 'addons-for-elementor-builder');
                                $loop_break = true;
                            }
                            break;
                        case 'confirmemail':
                            if (!empty($_POST['afeb-register-confirmemail']))
                            {
                                $confirm_email = sanitize_email(wp_unslash($_POST['afeb-register-confirmemail']));
                                $primary_email = !empty($_POST['afeb-register-email']) ? sanitize_email(wp_unslash($_POST['afeb-register-email'])) : '';

                                if (!empty($primary_email))
                                {
                                    if ($primary_email !== $confirm_email)
                                    {
                                        $this->err_msg = !empty($this->settings['err_eml_cfrm_did_mtch']) ? $this->settings['err_eml_cfrm_did_mtch'] : esc_html__('The confirmed email did not match', 'addons-for-elementor-builder');
                                        $loop_break = true;
                                    }
                                }
                                else
                                {
                                    $this->err_msg = !empty($this->settings['err_eml_mis']) ? $this->settings['err_eml_mis'] : esc_html__('Email is missing or Invalid', 'addons-for-elementor-builder');
                                    $loop_break = true;
                                }
                            }
                            else
                            {
                                $this->err_msg = !empty($this->settings['err_eml_cfrm_did_mtch']) ? $this->settings['err_eml_cfrm_did_mtch'] : esc_html__('The confirmed email did not match', 'addons-for-elementor-builder');
                                $loop_break = true;
                            }
                            break;
                        case 'password':
                            if (!empty($_POST['afeb-register-password']))
                            {
                                if (!empty($register_repeater_item['reg_itm_min_lnt']))
                                    $register_info['password']['minlength'] = intval($register_repeater_item['reg_itm_min_lnt']);

                                if (!empty($register_repeater_item['reg_itm_max_lnt']))
                                    $register_info['password']['maxlength'] = intval($register_repeater_item['reg_itm_max_lnt']);

                                $register_info['password']['value'] = sanitize_text_field(wp_unslash($_POST['afeb-register-password']));
                                if ($register_info['password']['minlength'] !== null && mb_strlen($register_info['password']['value']) < $register_info['password']['minlength'])
                                {
                                    /* translators: %s is replaced with "The minimum length of the password" */
                                    $this->err_msg = sprintf(esc_html__('Password must be more than %s characters', 'addons-for-elementor-builder'), $register_info['password']['minlength']);
                                    $loop_break = true;
                                }
                                else if (mb_strlen($register_info['password']['value']) > $register_info['password']['maxlength'])
                                {
                                    /* translators: %s is replaced with "The maximum length of the password" */
                                    $this->err_msg = sprintf(esc_html__('Password must be less than %s characters', 'addons-for-elementor-builder'), $register_info['password']['maxlength']);
                                    $loop_break = true;
                                }
                            }
                            else
                            {
                                if (!empty($register_repeater_item['reg_itm_rqurd']))
                                {
                                    $this->err_msg = !empty($this->settings['err_pass']) ? $this->settings['err_pass'] : esc_html__('Please enter a valid Password', 'addons-for-elementor-builder');
                                    $loop_break = true;
                                }
                                else
                                {
                                    $register_info['password']['value'] = wp_generate_password();
                                    $register_info['password']['auto'] = 'yes';
                                }
                            }
                            break;
                        case 'confirmpassword':
                            if (!empty($_POST['afeb-register-confirmpassword']))
                            {
                                $confirm_password = sanitize_text_field(wp_unslash($_POST['afeb-register-confirmpassword']));
                                $primary_password = !empty($_POST['afeb-register-password']) ? sanitize_text_field(wp_unslash($_POST['afeb-register-password'])) : '';

                                if (!empty($primary_password))
                                {
                                    if ($primary_password !== $confirm_password)
                                    {
                                        $this->err_msg = !empty($this->settings['err_conf_pass']) ? $this->settings['err_conf_pass'] : esc_html__('The confirmed password did not match', 'addons-for-elementor-builder');
                                        $loop_break = true;
                                    }
                                }
                                else
                                {
                                    $this->err_msg = !empty($this->settings['err_pass']) ? $this->settings['err_pass'] : esc_html__('Please enter a valid Password', 'addons-for-elementor-builder');
                                    $loop_break = true;
                                }
                            }
                            else
                            {
                                $this->err_msg = !empty($this->settings['err_conf_pass']) ? $this->settings['err_conf_pass'] : esc_html__('The confirmed password did not match', 'addons-for-elementor-builder');
                                $loop_break = true;
                            }
                            break;
                        case 'firstname':
                        case 'lastname':
                        case 'website':
                            if (!empty($_POST["afeb-register-$fields"]))
                            {
                                if (!empty($register_repeater_item['reg_itm_min_lnt']))
                                    $register_info["$fields"]['minlength'] = intval($register_repeater_item['reg_itm_min_lnt']);

                                if (!empty($register_repeater_item['reg_itm_max_lnt']))
                                    $register_info["$fields"]['maxlength'] = intval($register_repeater_item['reg_itm_max_lnt']);

                                $raw_value = wp_unslash($_POST["afeb-register-$fields"]);
                                $register_info["$fields"]['value'] = $fields == 'website' ? sanitize_url($raw_value) : sanitize_text_field($raw_value);
                                if ($register_info["$fields"]['minlength'] !== null && mb_strlen($register_info["$fields"]['value']) < $register_info["$fields"]['minlength'])
                                {
                                    /* translators: %1$s is replaced with "Field Name" And %2$s replaced with "The minimum length of the additional fields" */
                                    $this->err_msg = sprintf(esc_html__('%1$s must be more than %1$s characters', 'addons-for-elementor-builder'), $fields, $register_info["$fields"]['minlength']);
                                    $loop_break = true;
                                }
                                else if (mb_strlen($register_info["$fields"]['value']) > $register_info["$fields"]['maxlength'])
                                {
                                    /* translators: %1$s is replaced with "Field Name" And %2$s replaced with "The maximum length of the additional fields" */
                                    $this->err_msg = sprintf(esc_html__('%1$s must be less than %1$s characters', 'addons-for-elementor-builder'), $fields, $register_info["$fields"]['maxlength']);
                                    $loop_break = true;
                                }
                            }
                            else if (!empty($register_repeater_item['reg_itm_rqurd']))
                            {
                                /* translators: %s is replaced with "Field Name" */
                                $this->err_msg = sprintf(esc_html__('Please enter a valid %s', 'addons-for-elementor-builder'), $fields);
                                $loop_break = true;
                            }
                            break;
                    }

                    $protocol = is_ssl() ? 'https://' : 'http://';
                    $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
                    $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
                    $url = sanitize_url($protocol . $host . $request_uri);

                    if (!empty($this->err_msg) && !empty($url))
                    {
                        setcookie('afeb_register_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
                        wp_safe_redirect($url);
                        exit();
                    }
                }
            }

            $user_data = [
                'user_login' => $register_info['username']['value'],
                'user_pass' => $register_info['password']['value'],
                'user_email' => $register_info['email']['value'],
            ];

            if (!empty($this->settings['reg_usr_rol']) && strtolower($this->settings['reg_usr_rol']) != 'administrator')
                $user_data['role'] = sanitize_text_field($this->settings['reg_usr_rol']);

            if (!empty($register_info['firstname']['value'])) $user_data['first_name'] = $register_info['firstname']['value'];
            if (!empty($register_info['lastname']['value'])) $user_data['last_name'] = $register_info['lastname']['value'];
            if (!empty($register_info['website']['value'])) $user_data['user_url'] = esc_url_raw($register_info['website']['value']);

            $user_id = wp_insert_user($user_data);

            do_action('afeb/login_register/after_insert_user', $user_id, $user_data);

            if (is_wp_error($user_id) && !empty($url))
            {
                $this->err_msg = !empty($this->settings['err_unkn']) ? $this->settings['err_unkn'] : esc_html__('Sorry, something went wrong!, User could not be registered', 'addons-for-elementor-builder');
                setcookie('afeb_register_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
                wp_safe_redirect($url);
                exit();
            }
            else
            {
                $notify = !empty($register_info['password']['auto']) ? 'user' : '';

                // User Email
                if (!empty($this->settings['reg_usr_eml_typ']) && $this->settings['reg_usr_eml_typ'] != 'none')
                {
                    $notify = empty($notify) ? 'user' : $notify;

                    add_filter('wp_new_user_notification_email', [$this, 'new_user_notification_email'], 10, 3);

                    if (!empty($register_info['password']['auto']) || $this->settings['reg_usr_eml_typ'] == 'custom')
                        $this->reg_send_custom_email_user = true;

                    if ($this->reg_send_custom_email_user) :

                        $this->reg_user_email_options['username'] = $register_info['username']['value'];
                        $this->reg_user_email_options['email'] = $register_info['email']['value'];
                        $this->reg_user_email_options['password'] = $register_info['password']['value'];
                        $this->reg_user_email_options['firstname'] = $register_info['firstname']['value'];
                        $this->reg_user_email_options['lastname'] = $register_info['lastname']['value'];
                        $this->reg_user_email_options['website'] = $register_info['website']['value'];

                        if (isset($this->settings['reg_usr_eml_sbjct']))
                            $this->reg_user_email_options['subject'] = wp_kses_post($this->settings['reg_usr_eml_sbjct']);

                        if (isset($this->settings['reg_usr_eml_msg']))
                            $this->reg_user_email_options['message'] = wp_kses_post($this->settings['reg_usr_eml_msg']);

                        if (isset($this->settings['reg_usr_eml_cnt_typ']))
                            $this->reg_user_email_options['headers'] = 'Content-Type: text/' . wp_strip_all_tags($this->settings['reg_usr_eml_cnt_typ']) . '; charset=UTF-8' . "\r\n";
                    endif;
                }

                if (!empty($this->settings['reg_usr_admn_eml_typ']) && $this->settings['reg_usr_admn_eml_typ'] != 'none')
                {
                    $notify = empty($notify) ? 'admin' : 'both';

                    add_filter('wp_new_user_notification_email_admin', [$this, 'new_user_notification_email_admin'], 10, 3);

                    if ($this->settings['reg_usr_admn_eml_typ'] == 'custom')
                        $this->reg_send_custom_email_admin = true;

                    if ($this->reg_send_custom_email_admin) :

                        $this->reg_admin_email_options['username'] = $register_info['username']['value'];
                        $this->reg_admin_email_options['email'] = $register_info['email']['value'];
                        $this->reg_admin_email_options['password'] = $register_info['password']['value'];
                        $this->reg_admin_email_options['firstname'] = $register_info['firstname']['value'];
                        $this->reg_admin_email_options['lastname'] = $register_info['lastname']['value'];
                        $this->reg_admin_email_options['website'] = $register_info['website']['value'];

                        if (isset($this->settings['reg_usr_admn_eml_sbjct']))
                            $this->reg_admin_email_options['subject'] = wp_kses_post($this->settings['reg_usr_admn_eml_sbjct']);

                        if (isset($this->settings['reg_usr_admn_eml_msg']))
                            $this->reg_admin_email_options['message'] = wp_kses_post($this->settings['reg_usr_admn_eml_msg']);

                        if (isset($this->settings['reg_usr_admn_eml_cnt_typ']))
                            $this->reg_admin_email_options['headers'] = 'Content-Type: text/' . wp_strip_all_tags($this->settings['reg_usr_admn_eml_cnt_typ']) . '; charset=UTF-8' . "\r\n";
                    endif;
                }

                if (!empty($register_info['password']['auto']))
                    update_user_option($user_id, 'default_password_nag', true, true);

                if ($this->reg_send_custom_email_user || $this->reg_send_custom_email_admin):
                    $user = get_user_by('id', $user_id);
                    $key = get_password_reset_key($user);
                    if (!is_wp_error($key))
                    {
                        if (!empty($this->settings['cstm_rst_pass_frm']))
                        {
                            $locale = get_user_locale($user_data);
                            $this->reg_user_email_options['password_reset_link'] = add_query_arg(
                                [
                                    'afeb-rp' => 1,
                                    'key' => $key,
                                    'login' => rawurlencode($user->user_login),
                                ],
                                esc_url_raw($this->wp_login_url())
                            );
                            $this->reg_user_email_options['password_reset_link'] = $this->reg_user_email_options['password_reset_link'] . '&page_id=' . $this->page_id . '&wp_lang=' . $locale . "\r\n\r\n";
                        }
                        else
                        {
                            $this->reg_user_email_options['password_reset_link'] = add_query_arg(
                                ['action' => 'rp', 'key' => $key, 'login' => rawurlencode($user->user_login)],
                                esc_url_raw($this->wp_login_url())
                            );
                            $this->reg_user_email_options['password_reset_link'] = $this->reg_user_email_options['password_reset_link'] . "\r\n\r\n";
                        }
                    }
                endif;

                remove_action('register_new_user', 'wp_send_new_user_notifications');
                do_action('register_new_user', $user_id);

                if (!empty($notify))
                    wp_new_user_notification($user_id, null, $notify);

                $after_register_actions = !empty($this->settings['aftr_reg_act_lbc']) ? $this->settings['aftr_reg_act_lbc'] : '';
                $redirect_custom_url = !empty($this->settings['rdrct_aftr_reg_cstm_url']) ? sanitize_url($this->settings['rdrct_aftr_reg_cstm_url']['url']) : '';
                if ($after_register_actions == 'auto_login' && !is_user_logged_in() && !empty($url))
                {
                    wp_signon([
                        'user_login' => $user_data['user_login'],
                        'user_password' => $user_data['user_pass'],
                        'remember' => true,
                    ]);

                    wp_safe_redirect($url);
                    exit();
                }

                if ($after_register_actions == 'redirect' && !empty($redirect_custom_url))
                {
                    wp_safe_redirect($redirect_custom_url);
                    exit();
                }

                if (!empty($register_info['password']['auto'])) $this->sucs_msg = !empty($this->settings['sucs_msg_reg_no_pass']) ? $this->settings['sucs_msg_reg_no_pass'] : esc_html__('Your registration was successfull, Please check your email inbox for the password', 'addons-for-elementor-builder');
                else $this->sucs_msg = !empty($this->settings['sucs_msg_reg']) ? $this->settings['sucs_msg_reg'] : esc_html__('Your registration was successfull, Now you can login to the site', 'addons-for-elementor-builder');

                setcookie('afeb_register_success_' . $this->widget_id, esc_html($this->sucs_msg), time() + 2);
            }
        }
        $referer = $this->get_referer_url();

        if (!empty($referer))
        {
            wp_safe_redirect($referer);
            exit();
        }
    }

    /**
     * Send password reset to user email
     *
     * @since 1.0.3
     */
    public function send_password_reset()
    {
        $this->check_common_errors('lostpassword');

        do_action('afeb/login_register/before_lostpassword_email');
        $username_email = !empty($_POST['afeb-lostpassword-username-email']) ? sanitize_text_field(wp_unslash($_POST['afeb-lostpassword-username-email'])) : '';

        if (is_email($username_email))
        {
            $username_email = sanitize_email($username_email);
        }

        // Custom Reset Password
        if (!empty($this->settings['cstm_rst_pass_frm']))
            $this->lp_send_custom_email = true;

        if ($this->lp_send_custom_email) :
            if (isset($this->settings['lp_eml_sbjct']))
                $this->lp_email_options['subject'] = wp_kses_post($this->settings['lp_eml_sbjct']);

            if (isset($this->settings['lp_eml_msg_rst_lnk_txt']))
                $this->lp_email_options['reset_link_text'] = wp_kses_post($this->settings['lp_eml_msg_rst_lnk_txt']);

            if (isset($this->settings['lp_eml_msg']))
                $this->lp_email_options['message'] = $this->settings['lp_eml_msg'];

            if (isset($this->settings['lp_eml_cnt_typ']))
                $this->lp_email_options['headers'] = 'Content-Type: text/' . wp_kses_post($this->settings['lp_eml_cnt_typ']) . '; charset=UTF-8' . "\r\n";

            $referer = $this->get_referer_url();
            if (!empty($referer))
                $this->lp_email_options['http_referer'] = strtok($referer, '?');

            $this->lp_email_options['page_id'] = $this->page_id;
            $this->lp_email_options['widget_id'] = $this->widget_id;
        endif;

        add_filter('retrieve_password_notification_email', [$this, 'retrieve_password_notification_email'], 10, 4);

        $r_password = retrieve_password($username_email);
        if (is_wp_error($r_password))
        {
            $this->err_msg = esc_html__('Unfortunately, There is a problem in the email sending process', 'addons-for-elementor-builder');
            setcookie('afeb_lostpassword_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
        }
        else
        {
            $this->sucs_msg = !empty($this->settings['sucs_msg_lp']) ? $this->settings['sucs_msg_lp'] : esc_html__('Check your email for the confirmation link', 'addons-for-elementor-builder');
            setcookie('afeb_lostpassword_success_' . $this->widget_id, esc_html($this->sucs_msg), time() + 2);

            $redirect_act = !empty($this->settings['rdrct_aftr_lp_act_lbc']) ? $this->settings['rdrct_aftr_lp_act_lbc'] : '';
            $redirect_custom_url = !empty($this->settings['rdrct_aftr_lp_cstm_url']) ? sanitize_url($this->settings['rdrct_aftr_lp_cstm_url']['url']) : '';
            $redirect_to_prev_page_url = !empty($_POST['afeb-redirect-to-prev-page']) ? sanitize_url(wp_unslash($_POST['afeb-redirect-to-prev-page'])) : '';
            $redirect_url = '';

            if ($redirect_act == 'custom_url') $redirect_url = $redirect_custom_url;
            if ($redirect_act == 'previous_page') $redirect_url = $redirect_to_prev_page_url;
            if (!empty($redirect_url))
            {
                wp_safe_redirect($redirect_url);
                exit();
            }
        }
        $referer = $this->get_referer_url();

        if (!empty($referer))
        {
            wp_safe_redirect($referer);
            exit();
        }
    }

    /**
     * Handles resetting the user’s password
     *
     * @since 1.0.3
     */
    public function password_reset()
    {
        $this->check_common_errors('resetpassword');

        do_action('afeb/login_register/before_resetpassword');
        $new_password = sanitize_text_field(wp_unslash($_POST['afeb-resetpassword-new-password']));
        $confirm_new_password = !empty($_POST['afeb-resetpassword-confirm-new-password']) ? sanitize_text_field(wp_unslash($_POST['afeb-resetpassword-confirm-new-password'])) : '';
        $limit_fields_length = false;

        if (!empty($this->settings['crp_np_itm_'])) $limit_fields_length = intval($this->settings['crp_np_itm_lnt']);
        if ($new_password !== $confirm_new_password)
        {
            $this->err_msg = !empty($this->settings['err_conf_pass']) ? $this->settings['err_conf_pass'] : esc_html__('The confirmed password did not match', 'addons-for-elementor-builder');
            setcookie('afeb_resetpassword_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
        }
        else if ($limit_fields_length && mb_strlen($new_password) < $limit_fields_length)
        {
            /* translators: %s is replaced with "The minimum length of the password field" */
            $this->err_msg = sprintf(esc_html__('Password must be more than %s characters', 'addons-for-elementor-builder'), $limit_fields_length);
            setcookie('afeb_resetpassword_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
        }
        else
        {
            $key = sanitize_text_field(wp_unslash($_POST['afeb-key']));
            $user_login = sanitize_text_field(wp_unslash($_POST['afeb-login']));

            $user = check_password_reset_key($key, $user_login);
            if (is_wp_error($user))
            {
                if ($user->get_error_code() === 'expired_key')
                {
                    $this->err_msg = !empty($this->settings['err_rp_key_exprd']) ? $this->settings['err_rp_key_exprd'] : esc_html__('Your password reset link appears to be invalid. Please request a new link', 'addons-for-elementor-builder');
                }
                else
                {
                    $code = $user->get_error_code();
                    if (empty($code)) $code = '00';

                    /* translators: %s: Error Code */
                    $this->err_msg = sprintf(esc_html__('That key is no longer valid. Please reset your password again. Code: %s', 'addons-for-elementor-builder'), $code);
                }

                setcookie('afeb_resetpassword_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
            }
            else
            {
                try
                {
                    reset_password($user, $new_password);

                    $this->sucs_msg = !empty($this->settings['sucs_msg_crp']) ? $this->settings['sucs_msg_crp'] : esc_html__('Password changed successfully, Now you can login to the site', 'addons-for-elementor-builder');
                    setcookie('afeb_resetpassword_success_' . $this->widget_id, esc_html($this->sucs_msg), time() + 2);
                }
                catch (Exception $e)
                {
                    unset($e);
                    $this->err_msg = !empty($this->settings['err_unkn']) ? $this->settings['err_unkn'] : esc_html__('Sorry, something went wrong!', 'addons-for-elementor-builder');
                    setcookie('afeb_resetpassword_error_' . $this->widget_id, esc_html($this->err_msg), time() + 2);
                }
            }
        }
        $referer = $this->get_referer_url();

        if (!empty($referer))
        {
            wp_safe_redirect($referer);
            exit();
        }
    }

    /**
     * Filters the contents of the new user notification email
     *
     * @param array $email
     * @since 1.0.3
     *
     */
    public function new_user_notification_email($email)
    {
        if (!$this->reg_send_custom_email_user) return $email;
        if (!empty($this->reg_user_email_options['subject'])) $email['subject'] = $this->reg_user_email_options['subject'];
        if (!empty($this->reg_user_email_options['message'])) $email['message'] = $this->reg_user_email_options['message'];
        if (!empty($this->reg_user_email_options['headers'])) $email['headers'] = $this->reg_user_email_options['headers'];

        $email['message'] = wpautop($this->replace_placeholders($this->reg_user_email_options, $email['message']));

        return $email;
    }

    /**
     * Filters the contents of the new user notification email sent to the site admin
     *
     * @param array $email
     * @since 1.0.3
     *
     */
    public function new_user_notification_email_admin($email)
    {
        if (!$this->reg_send_custom_email_admin) return $email;
        if (!empty($this->reg_admin_email_options['subject'])) $email['subject'] = $this->reg_admin_email_options['subject'];
        if (!empty($this->reg_admin_email_options['message'])) $email['message'] = $this->reg_admin_email_options['message'];
        if (!empty($this->reg_admin_email_options['headers'])) $email['headers'] = $this->reg_admin_email_options['headers'];
        $email['message'] = wpautop($this->replace_placeholders($this->reg_admin_email_options, $email['message']));

        return $email;
    }

    /**
     * Filters the contents of the reset password notification email sent to the user
     *
     * @param array $defaults
     * @param string $key
     * @param string $user_login
     * @param object $user_data
     * @since 1.0.3
     *
     */
    public function retrieve_password_notification_email($defaults, $key, $user_login, $user_data)
    {
        if (!$this->lp_send_custom_email) return $defaults;
        if (!empty($this->lp_email_options['subject'])) $defaults['subject'] = $this->lp_email_options['subject'];

        $page_id = $this->lp_email_options['page_id'] ?: 0;

        if (!empty($this->lp_email_options['message']))
        {
            if (!empty($key))
            {
                $locale = get_user_locale($user_data);
                $this->lp_email_options['password_reset_link'] = add_query_arg(
                    [
                        'afeb-rp' => 1,
                        'key' => $key,
                        'login' => rawurlencode($user_login),
                    ],
                    esc_url_raw($this->wp_login_url())
                );
                $this->lp_email_options['password_reset_link'] = $this->lp_email_options['password_reset_link'] . '&page_id=' . $page_id . '&wp_lang=' . $locale . "\r\n\r\n";
            }

            if (is_object($user_data))
            {
                $user_meta = get_user_meta($user_data->ID);
                $this->lp_email_options['username'] = $user_login;
                $this->lp_email_options['firstname'] = !empty($user_meta['first_name'][0]) ? $user_meta['first_name'][0] : '';
                $this->lp_email_options['lastname'] = !empty($user_meta['last_name'][0]) ? $user_meta['last_name'][0] : '';
                $this->lp_email_options['email'] = $user_data->user_email;
                $this->lp_email_options['website'] = $user_data->user_url;
            }
            $defaults['message'] = $this->replace_placeholders($this->lp_email_options, $this->lp_email_options['message']);
        }

        if (!empty($this->lp_email_options['headers'])) $defaults['headers'] = $this->lp_email_options['headers'];
        $defaults['message'] = wpautop($defaults['message']);

        return $defaults;
    }

    /**
     * Redirect to reset password page
     *
     * @since 1.0.3
     */
    public function redirect_to_reset_password_page()
    {
        $settings = get_option('afeb-settings', []);

        // get custom lost password page from settings
        $lost_pass_page = !empty($settings['widgets']['login_register']['default_lostpass_page'])
            ? intval($settings['widgets']['login_register']['default_lostpass_page'])
            : 0;

        $key = isset($_GET['key']) ? sanitize_text_field(wp_unslash($_GET['key'])) : '';
        $user_login = isset($_GET['login']) ? sanitize_text_field(wp_unslash($_GET['login'])) : '';

        // If no custom page is set and no explicit redirect flag is present, bail out
        if (empty($_GET['afeb-rp']) && ($lost_pass_page <= 0 || empty($key) || empty($user_login))) return;

        $this->page_id = isset($_GET['page_id']) ? absint(wp_unslash($_GET['page_id'])) : 0;
        $page_url = ($lost_pass_page > 0)
            ? get_permalink($lost_pass_page)
            : get_permalink($this->page_id);

        wp_redirect(add_query_arg(
            [
                'afeb-reset-password' => 1,
                'afeb-key' => $key,
                'afeb-login' => rawurlencode($user_login),
            ],
            $page_url
        ));
        exit;
    }

    /**
     * Change the default Login URL
     *
     * @since 1.3.0
     */
    public function default_login_page($login_url, $redirect)
    {
        $settings = get_option('afeb-settings', []);
        $settings = !empty($settings['widgets']['login_register']) ?
            $settings['widgets']['login_register'] : [];

        $login = !empty($settings['default_login_page']) ?
            get_the_permalink(intval($settings['default_login_page'])) : false;

        if ($login !== false)
        {
            $login = add_query_arg('afeb-login', 'yes', $login);
            $login_url = !empty($redirect) ?
                add_query_arg('redirect_to', $redirect, $login) : $login;
        }

        return $login_url;
    }

    /**
     * Change the default Register URL
     *
     * @since 1.3.0
     */
    public function default_register_page($register_url)
    {
        $settings = get_option('afeb-settings', []);
        $settings = !empty($settings['widgets']['login_register']) ?
            $settings['widgets']['login_register'] : [];

        $register = !empty($settings['default_register_page']) ?
            get_the_permalink(intval($settings['default_register_page'])) : false;

        if ($register !== false)
            $register_url = add_query_arg('afeb-register', 'yes', $register);

        return $register_url;
    }

    /**
     * Change the default Lost Password URL
     *
     * @since 1.3.0
     */
    public function default_lost_password_page($lost_pass_url, $redirect)
    {
        $settings = get_option('afeb-settings', []);
        $settings = !empty($settings['widgets']['login_register']) ?
            $settings['widgets']['login_register'] : [];

        $lost_pass = !empty($settings['default_lostpass_page']) ?
            get_the_permalink(intval($settings['default_lostpass_page'])) : false;

        if ($lost_pass !== false)
        {
            $lost_pass = add_query_arg('afeb-lostpassword', 'yes', $lost_pass);
            $lost_pass_url = !empty($redirect) ?
                add_query_arg('redirect_to', $redirect, $lost_pass) : $lost_pass;
        }

        return $lost_pass_url;
    }

    /**
     * Execute the relevant code on the current page
     *
     * @since 1.2.0
     */
    public function redirect_default_login_page()
    {
        $settings = get_option('afeb-settings', []);
        $settings = !empty($settings['widgets']['login_register']) ?
            $settings['widgets']['login_register'] : [];

        $login = !empty($settings['default_login_page']) ?
            get_the_permalink(intval($settings['default_login_page'])) : false;

        $register = !empty($settings['default_register_page']) ?
            get_the_permalink(intval($settings['default_register_page'])) : false;

        $lost_pass = !empty($settings['default_lostpass_page']) ?
            get_the_permalink(intval($settings['default_lostpass_page'])) : false;

        $action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';

        // Avoid redirect loops during password reset process
        if (in_array($action, ['rp', 'resetpass'], true))
        {
            return;
        }

        if ($action == 'register')
        {
            if ($register !== false)
            {
                $register = sanitize_url(add_query_arg('afeb-register', 'yes', $register));
                if (!empty($register)) wp_safe_redirect($register);
            }
        }
        else if ($action == 'lostpassword')
        {
            if ($lost_pass !== false)
            {
                $lost_pass = sanitize_url(add_query_arg('afeb-lostpassword', 'yes', $lost_pass));
                if (!empty($lost_pass)) wp_safe_redirect($lost_pass);
            }
        }
        else
        {
            if ($login !== false)
            {
                $login = sanitize_url(add_query_arg('afeb-login', 'yes', $login));
                if (!empty($login)) wp_safe_redirect($login);
            }
        }
    }

    /**
     * Retrieves the login URL
     *
     * @since 1.0.3
     */
    public function wp_login_url()
    {
        return apply_filters('afeb/login_register/wp-login-url', wp_login_url());
    }

    /**
     * It replaces placeholders with dynamic value and returns it
     *
     * @param array $options
     * @param string $message
     * @since 1.0.3
     *
     */
    public function replace_placeholders($options = [], $message = '')
    {
        $reset_link_text = !empty($options['reset_link_text']) ? $options['reset_link_text'] : esc_html__('Click here to reset your password', 'addons-for-elementor-builder');
        $password_reset_url = !empty($options['password_reset_link']) ? sanitize_url($options['password_reset_link']) : '';
        $password_reset_link = !empty($password_reset_url) ? '<a href="' . esc_url($password_reset_url) . '">' . esc_html($reset_link_text) . '</a>' : '';
        $username = !empty($options['username']) ? $options['username'] : '';
        $email = !empty($options['email']) ? $options['email'] : '';
        $password = !empty($options['password']) ? $options['password'] : '';
        $firstname = !empty($options['firstname']) ? $options['firstname'] : '';
        $lastname = !empty($options['lastname']) ? $options['lastname'] : '';
        $website = !empty($options['website']) ? $options['website'] : '';

        $placeholders = [
            '/\[password_reset_link]/',
            '/\[username]/',
            '/\[email]/',
            '/\[password]/',
            '/\[firstname]/',
            '/\[lastname]/',
            '/\[website]/',
            '/\[loginurl]/',
            '/\[sitetitle]/',
        ];

        $replacement = [
            $password_reset_link,
            $username,
            $email,
            $password,
            $firstname,
            $lastname,
            $website,
            esc_url($this->wp_login_url()),
            get_option('blogname'),
        ];

        return preg_replace($placeholders, $replacement, $message);
    }

    private function get_referer_url(): string
    {
        $referer = wp_get_raw_referer();

        if (empty($referer) && isset($_SERVER['HTTP_REFERER']))
        {
            $referer = wp_unslash($_SERVER['HTTP_REFERER']);
        }

        $referer = !empty($referer) ? sanitize_url($referer) : '';

        return $referer ?: '';
    }
}
