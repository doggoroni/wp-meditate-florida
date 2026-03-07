<?php

namespace AFEB\Handler\Widgets;

use AFEB\Handler;
use AFEB\PostTypes\Submissions;
use AFEB\PRO\Helper as AFEBPHelper;
use AFEB\Widgets;

if (!defined('ABSPATH'))
{
    exit;
}

/**
 * "Vertex Addons for Elementor" FormBuilderHandler Class
 *
 * @class FormBuilderHandler
 * @version 1.4.0
 */
class FormBuilderHandler extends Handler
{
    /**
     * @var bool
     */
    private $ajax;

    /**
     * @var int
     */
    private $page_id;

    /**
     * @var int
     */
    private $form_id;

    /**
     * @var string
     */
    private $err_msg;

    /**
     * Initialize "Vertex Addons for Elementor" FormBuilderHandler
     *
     * @since 1.4.0
     */
    public function init()
    {
        $this->actions();
    }

    /**
     * FormBuilderHandler Class Actions
     *
     * @since 1.4.0
     */
    public function actions()
    {
        add_action('init', [$this, 'form_builder_handler']);
    }

    /**
     * Login or register user handler
     *
     * @since 1.4.0
     */
    public function form_builder_handler()
    {
        do_action('afeb/form_builder/before_form_handler', $_POST);

        if (isset($_POST['afeb_form_submit']))
        {
            $this->form_submit();
        }


        do_action('afeb/form_builder/after_form_handler', $_POST);
    }

    /**
     * Checks if there is an initial error
     *
     * @since 1.4.0
     */
    public function check_common_errors()
    {
        $this->ajax = wp_doing_ajax();
        $this->page_id = 0;
        $this->err_msg = '';

        if (!empty($_POST['page_id'])) {
            $this->page_id = intval($_POST['page_id']);
            if (in_array(get_post_status($this->page_id), ['future', 'draft', 'pending'])) {
                $this->err_msg = esc_html__('Please publish the page first and then try again', 'addons-for-elementor-builder');
            }
        } else {
            $this->err_msg = esc_html__('The page ID is not set', 'addons-for-elementor-builder');
        }

        $this->form_id = false;
        if (empty($this->err_msg)) {
            if (!empty($_POST['form_id'])) {
                $this->form_id = sanitize_text_field(wp_unslash($_POST['form_id']));
                setcookie('afeb_form_submit_error_' . $this->form_id, '', time() + 2);
            } else {
                $this->err_msg = esc_html__('The form ID is not set', 'addons-for-elementor-builder');
            }
        }

        if (empty($this->err_msg)) {
            // Skip nonce for admin edit submission
            if (!(defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'afeb_update_submission')) {
                if (empty($_POST['afeb_form_submit_nonce'])) {
                    $this->err_msg = esc_html__('The submitted form is not secure, Nonce is not set', 'addons-for-elementor-builder');
                }
                if (empty($this->err_msg) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afeb_form_submit_nonce'])), 'afeb_form_submit_action')) {
                    $this->err_msg = esc_html__('Security token did not match', 'addons-for-elementor-builder');
                }
            }
        }

        if (!empty($this->err_msg)) {
            if ($this->ajax) {
                wp_send_json_error($this->err_msg);
            }
            if (!empty($this->form_id)) {
                setcookie('afeb_form_submit_error_' . $this->form_id, esc_html($this->err_msg), time() + 2);
            }
            $referer = $this->get_referer_url();

            if (!empty($referer)) {
                wp_safe_redirect($referer);
                exit();
            }
        }
    }

    /**
     * Form Submit
     *
     * @since 1.4.0
     */
    public function form_submit()
    {
        $this->check_common_errors();

        $settings = Widgets::get_widget_preview_settings($this->page_id, $this->form_id);
        $form_fields = $this->sanitize_posted_fields($_POST['form_fields'] ?? []);
        $field_order = $this->get_field_order($_POST['field_order'] ?? '', $settings);
        $settings_fields = $settings['form_fields'] ?? [];

        // Handle file uploads
        $uploaded_files = $this->process_uploaded_files($_FILES['form_fields'] ?? [], $settings_fields);
        if (isset($uploaded_files['errors'])) {
            $this->handle_error(
                __('Some fields have errors.', 'addons-for-elementor-builder'),
                $uploaded_files['errors']
            );
        }

        // Prepare normal fields
        $new_fields = $this->prepare_form_fields($field_order, $settings_fields, $form_fields, $uploaded_files['files'] ?? []);

        // Verify reCAPTCHA
        if ($this->has_recaptcha_error($settings_fields)) {
            $this->handle_error(__('Recaptcha verification failed', 'addons-for-elementor-builder'));
        }

        // Execute submission actions
        $result = $this->execute_submit_actions($settings, $new_fields);

        // Handle success / failure
        if (!$result['success']) {
            $this->handle_error($result['message']);
        }

        $this->handle_success($result['message'], $result['redirect']);
    }


    /* ============================================================
     * ===============     PRIVATE HELPER METHODS     ===============
     * ============================================================
     */

    private function sanitize_posted_fields($fields)
    {
        return is_array($fields) ? array_map('wp_unslash', $fields) : [];
    }

    private function get_field_order($posted_order, $settings)
    {
        if (!empty($posted_order)) {
            return json_decode(stripslashes($posted_order), true);
        }
        return array_column($settings['form_fields'] ?? [], '_id');
    }

    private function process_uploaded_files($files, $settings_fields)
    {
        $errors = [];
        $uploaded = [];

        foreach ($files['name'] ?? [] as $field_id => $names) {
            if (empty($names)) continue;

            $field = $this->find_field_by_id($settings_fields, $field_id);
            if (empty($field) || ($field['field_type'] ?? '') !== 'upload') continue;

            $normalized = $this->normalize_files($files, $field_id);
            $allowed_exts = $this->get_allowed_extensions($field);

            $validation = \AFEB\Widgets\FormBuilder\Helper::validate_uploaded_files(
                $normalized,
                [
                    'mime_types' => wp_get_mime_types(),
                    'exts'       => $allowed_exts,
                    'max_size'   => !empty($field['max_file_size'])
                        ? (int)$field['max_file_size'] * 1024 * 1024
                        : wp_max_upload_size(),
                ],
                !empty($field['required'])
            );

            if (!$validation['valid']) {
                $errors[$field_id] = implode(' ', (array)$validation['errors']);
            } else {
                $uploaded[$field_id] = $validation['files'];
            }
        }

        return !empty($errors) ? ['errors' => $errors] : ['files' => $uploaded];
    }

    private function normalize_files($files, $field_id)
    {
        $out = [];
        if (is_array($files['name'][$field_id])) {
            foreach ($files['name'][$field_id] as $i => $name) {
                if (empty($name)) continue;
                $out[] = [
                    'name'     => $files['name'][$field_id][$i],
                    'type'     => $files['type'][$field_id][$i],
                    'tmp_name' => $files['tmp_name'][$field_id][$i],
                    'error'    => $files['error'][$field_id][$i],
                    'size'     => $files['size'][$field_id][$i],
                ];
            }
        } else {
            $out[] = [
                'name'     => $files['name'][$field_id],
                'type'     => $files['type'][$field_id],
                'tmp_name' => $files['tmp_name'][$field_id],
                'error'    => $files['error'][$field_id],
                'size'     => $files['size'][$field_id],
            ];
        }
        return $out;
    }

    private function get_allowed_extensions($field)
    {
        return !empty($field['allowed_file_types'])
            ? array_map('strtolower', array_map('trim', explode(',', $field['allowed_file_types'])))
            : [];
    }

    private function find_field_by_id($fields, $id)
    {
        foreach ($fields as $f) {
            if (($f['_id'] ?? '') === $id) return $f;
        }
        return null;
    }

    private function prepare_form_fields($order, $settings_fields, $form_fields, $uploaded_files)
    {
        $fields = [];
        foreach ($order as $id) {
            $f = $this->find_field_by_id($settings_fields, $id);
            if (empty($f) || in_array($f['field_type'], ['step', 'html', 'recaptcha_v3'])) continue;

            $value = $this->get_field_value($f['field_type'], $form_fields[$id] ?? '', $uploaded_files[$id] ?? []);
            $fields[] = [
                '_id'     => $id,
                'label'   => $f['field_label'] ?? '',
                'type'    => $f['field_type'] ?? '',
                'options' => $f['field_options'] ?? '',
                'value'   => $value,
            ];
        }
        return $fields;
    }

    private function get_field_value($type, $value, $uploaded)
    {
        if ($type === 'upload') return $uploaded;

        switch ($type) {
            case 'email':    return sanitize_email($value);
            case 'url':      return sanitize_url($value);
            case 'textarea': return sanitize_textarea_field($value);
            case 'tel':
            case 'number':   return preg_replace('/[^\d+]/', '', $value);
            case 'checkbox': return is_array($value)
                ? map_deep($value, 'sanitize_text_field')
                : sanitize_text_field($value);
            default:         return sanitize_text_field($value);
        }
    }

    private function has_recaptcha_error($settings_fields)
    {
        $has_recaptcha = false;
        foreach ($settings_fields as $f)
        {
            if (($f['field_type'] ?? '') === 'recaptcha_v3')
            {
                $has_recaptcha = true;
                break;
            }
        }

        $ajax_verify_recaptcha = get_option('_afebp_ajax_verify_recaptcha', 'no');
        if ($has_recaptcha && !$this->ajax && $ajax_verify_recaptcha === 'no')
        {
            $recaptcha_response = $_POST['afeb_form_field_recaptcha_v3'] ?? '';
            if (class_exists('\AFEB\PRO\Helper') && method_exists('AFEBPHelper', 'verify_recaptcha'))
            {
                $is_valid = AFEBPHelper::verify_recaptcha($recaptcha_response);
                if ($is_valid !== true) return true;
            }
        }

        update_option('_afebp_ajax_verify_recaptcha', 'no');
        return false;
    }

    private function execute_submit_actions($settings, $fields)
    {
        $actions = $settings['submit_actions'] ?? [];
        $redirect = in_array('redirect', $actions) && !empty($settings['redirect_to'])
            ? sanitize_url($settings['redirect_to'])
            : '';
        $error = false;

        // Save submission
        if (in_array('submission', $actions) && !$this->save_submission($settings, $fields)) $error = true;

        // Send email
        if (in_array('email', $actions) && $this->send_email($settings, $fields) === false) $error = true;

        return [
            'success'  => !$error,
            'message'  => $error
                ? ($settings['error_message'] ?? __('The operation was unsuccessful.', 'addons-for-elementor-builder'))
                : ($settings['success_message'] ?? __('The operation was successful.', 'addons-for-elementor-builder')),
            'redirect' => $redirect,
        ];
    }

    private function save_submission($settings, $fields)
    {
        $form_title = sanitize_text_field(isset($_POST['form_name']) ? wp_unslash($_POST['form_name']) : '');
        $id = wp_insert_post([
            'post_status' => 'publish',
            'post_title'  => $form_title ?: "Vertex Submission",
            'post_type'   => Submissions::SUBMISSIONS_POST_TYPE,
        ], true);

        if (is_wp_error($id) || !$id) return false;

        if (trim($form_title)) update_post_meta($id, 'afeb_form_name', $form_title);

        update_post_meta($id, 'afeb_form_id', $this->form_id);
        update_post_meta($id, 'afeb_page_id', $this->page_id);
        update_post_meta($id, 'afeb_user_ip', $this->get_client_ip());
        update_post_meta($id, 'afeb_user_agent', sanitize_textarea_field($_SERVER['HTTP_USER_AGENT'] ?? ''));
        update_post_meta($id, 'afeb_form_fields', $fields);

        return true;
    }

    private function handle_error($message, $field_errors = [])
    {
        if ($this->ajax) wp_send_json_error(['message' => $message, 'errors' => $field_errors]);

        setcookie('afeb_form_submit_error_' . $this->form_id, esc_html($message), time() + 2);
        wp_safe_redirect(wp_get_referer() ?: home_url());
        exit;
    }

    private function handle_success($message, $redirect = '')
    {
        if ($this->ajax) wp_send_json_success(['message' => $message, 'redirect' => $redirect]);

        setcookie('afeb_form_submit_success_' . $this->form_id, esc_html($message), time() + 2);

        if ($redirect) wp_safe_redirect($redirect);
        else wp_safe_redirect(wp_get_referer() ?: home_url());

        exit;
    }

    public function _unstable_get_super_global_value($super_global, $key)
    {
        if (!isset($super_global[$key])) return null;

        if ($super_global === $_FILES)
        {
            $super_global[$key]['name'] = sanitize_file_name($super_global[$key]['name']);
            return $super_global[$key];
        }

        return wp_kses_post_deep(wp_unslash($super_global[$key]));
    }

    public function get_client_ip()
    {
        $server_ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($server_ip_keys as $key)
        {
            $value = $this->_unstable_get_super_global_value($_SERVER, $key);
            if ($value && filter_var($value, FILTER_VALIDATE_IP))
                return $value;
        }

        // Fallback Local IP
        return '127.0.0.1';
    }

    public function send_email($settings = [], $form_fields = [])
    {
        $GLOBALS['afeb_form_fields'] = $form_fields;

        $content_type = isset($settings['email_content_type']) ? sanitize_text_field($settings['email_content_type']) : 'html';

        $line_break = $content_type == 'html' ? '<br>' : "\n";
        $email_content = isset($settings['email_content']) ? sanitize_textarea_field($settings['email_content']) : '[all-fields]';

        if ($email_content == '[all-fields]' || str_contains($email_content, '[all-fields]'))
        {
            $all_email_content = [];
            $replace_shortcode_with_value = function ($matches)
            {
                $field_id = $matches[1];

                foreach ($GLOBALS['afeb_form_fields'] as $form_field)
                {
                    $id = $form_field['_id'] ?? '';

                    if ($id == $field_id)
                    {
                        $value = $form_field['value'] ?? '';
                        return is_array($value) ? implode("\n", $value) . "\n" : $value . "\n";
                    }
                }

                return '';
            };

            foreach ($GLOBALS['afeb_form_fields'] as $form_field)
            {
                $label = $form_field['label'] ?? '';
                $value = $form_field['value'] ?? '';

                // Clean up multi-file upload URLs to remove trailing pipes (%7C issue)
                if (($form_field['type'] ?? '') === 'upload') {
                    if (!is_array($value)) {
                        $parts = explode('|', rtrim($value, '|'));
                        $parts = array_filter($parts);
                        $value = $parts; // now $value is an array of clean URLs
                    }
                }

                $all_email_content[] = is_array($value)
                    ? trim($label) . ': ' . implode("\n", $value) . "\n"
                    : trim($label) . ': ' . $value . "\n";
            }

            $all_email_content = implode("\n", $all_email_content);
            $processed_message = str_replace('[all-fields]', $all_email_content, $email_content);
            $processed_message = preg_replace_callback(
                '/\[id="([^"]+)"]/',
                $replace_shortcode_with_value,
                $processed_message
            );
        }
        else
        {
            $replace_shortcode_with_value = function ($matches)
            {
                $field_id = $matches[1];

                foreach ($GLOBALS['afeb_form_fields'] as $form_field)
                {
                    $id = $form_field['_id'] ?? '';

                    if ($id == $field_id)
                    {
                        $label = $form_field['label'] ?? '';
                        $value = $form_field['value'] ?? '';

                        return is_array($value) ? trim($label) . ': ' . implode("\n", $value) . "\n" : trim($label) . ': ' . $value . "\n";
                    }
                }

                return '';
            };

            $processed_message = preg_replace_callback(
                '/\[id="([^"]+)"]/',
                $replace_shortcode_with_value,
                $email_content
            );
        }

        $meta_keys = $settings['form_metadata'] ?? ['date', 'time', 'credit'];
        $meta_fields = [];

        foreach ($meta_keys as $metadata_type)
        {
            switch ($metadata_type)
            {
                case 'date':
                    $meta_fields['date'] = [
                        'title' => esc_html__('Date', 'addons-for-elementor-builder'),
                        'value' => date_i18n(get_option('date_format')),
                    ];
                    break;

                case 'time':
                    $meta_fields['time'] = [
                        'title' => esc_html__('Time', 'addons-for-elementor-builder'),
                        'value' => date_i18n(get_option('time_format')),
                    ];
                    break;

                case 'page_url':
                    $value = isset($_POST['referrer']) ? sanitize_url(wp_unslash($_POST['referrer'])) : '';
                    $meta_fields['page_url'] = [
                        'title' => esc_html__('Page URL', 'addons-for-elementor-builder'),
                        'value' => $value,
                    ];
                    break;

                case 'page_title':
                    $value = isset($_POST['referer_title']) ? sanitize_text_field(wp_unslash($_POST['referer_title'])) : '';
                    $meta_fields['page_title'] = [
                        'title' => esc_html__('Page Title', 'addons-for-elementor-builder'),
                        'value' => $value,
                    ];
                    break;

                case 'user_agent':
                    $meta_fields['user_agent'] = [
                        'title' => esc_html__('User Agent', 'addons-for-elementor-builder'),
                        'value' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_textarea_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '',
                    ];
                    break;

                case 'remote_ip':
                    $meta_fields['remote_ip'] = [
                        'title' => esc_html__('Remote IP', 'addons-for-elementor-builder'),
                        'value' => $this->get_client_ip(),
                    ];
                    break;

                case 'credit':
                    $meta_fields['credit'] = [
                        'title' => esc_html__('Powered by', 'addons-for-elementor-builder'),
                        'value' => esc_html__('Vertex Addons', 'addons-for-elementor-builder'),
                    ];
                    break;
            }
        }

        $email_meta = [];

        foreach ($meta_fields as $value)
            $email_meta[] = $value['title'] . ': ' . $value['value'];

        $fields = [
            'email_to' => isset($settings['email_to']) ?
                sanitize_email($settings['email_to']) : get_option('admin_email'),

            /* translators: %s: Site title. */
            'email_subject' => isset($settings['email_subject']) ?
                sanitize_text_field($settings['email_subject']) : sprintf(esc_html__('New message from "%s"', 'addons-for-elementor-builder'), get_bloginfo('name')),

            'email_content' => $processed_message . $line_break . '-----' . $line_break . implode($line_break, $email_meta),

            'email_from_name' => isset($settings['email_from_name']) ?
                sanitize_text_field($settings['email_from_name']) : get_bloginfo('name'),

            'email_from' => isset($settings['email_from']) ?
                sanitize_email($settings['email_from']) : get_bloginfo('admin_email'),

            'email_reply_to' => isset($settings['email_reply_to']) ?
                sanitize_email($settings['email_reply_to']) : '',

            'email_to_cc' => $settings['email_to_cc'] ?? '',

            'email_to_bcc' => $settings['email_to_bcc'] ?? '',
        ];

        $headers = sprintf('From: %s <%s>' . "\r\n", $fields['email_from_name'], $fields['email_from']);
        $headers .= sprintf('Reply-To: %s' . "\r\n", $fields['email_reply_to']);

        if ($content_type == 'html')
            $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";


        $cc_header = '';
        if (!empty($fields['email_to_cc']))
            $cc_header = 'Cc: ' . $fields['email_to_cc'] . "\r\n";

        $email_sent = wp_mail(
            $fields['email_to'],
            $fields['email_subject'],
            $fields['email_content'],
            $headers . $cc_header
        );

        if (!empty($fields['email_to_bcc']))
        {
            $bcc_emails = explode(',', $fields['email_to_bcc']);
            foreach ($bcc_emails as $bcc_email)
            {
                wp_mail(
                    trim($bcc_email),
                    $fields['email_subject'],
                    $fields['email_content'],
                    $headers
                );
            }
        }

        return (bool) $email_sent;
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
