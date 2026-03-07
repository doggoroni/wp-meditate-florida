<?php

class LSD_API_Controllers_Profile extends LSD_API_Controller
{
    public function get(WP_REST_Request $request): WP_REST_Response
    {
        // Get Current User
        $user = wp_get_current_user();

        /** @var WP_Error $user */
        if (is_wp_error($user)) return $this->response([
            'data' => new WP_Error('400', $user->get_error_message()),
            'status' => 400,
        ]);

        // Response
        return $this->response([
            'data' => [
                'success' => 1,
                'user' => LSD_API_Resources_User::get($user),
            ],
            'status' => 200,
        ]);
    }

    public function update(WP_REST_Request $request): WP_REST_Response
    {
        $vars = $request->get_params();

        $first_name = isset($vars['first_name']) ? sanitize_text_field($vars['first_name']) : null;
        $last_name = isset($vars['last_name']) ? sanitize_text_field($vars['last_name']) : null;
        $description = isset($vars['description']) ? sanitize_textarea_field($vars['description']) : null;
        $phone = isset($vars['phone']) ? sanitize_text_field($vars['phone']) : null;
        $mobile = isset($vars['mobile']) ? sanitize_text_field($vars['mobile']) : null;
        $fax = isset($vars['fax']) ? sanitize_text_field($vars['fax']) : null;
        $job_title = isset($vars['job_title']) ? sanitize_text_field($vars['job_title']) : null;
        $linkedin = isset($vars['linkedin']) ? esc_url_raw($vars['linkedin']) : null;
        $twitter = isset($vars['twitter']) ? esc_url_raw($vars['twitter']) : null;
        $facebook = isset($vars['facebook']) ? esc_url_raw($vars['facebook']) : null;
        $pinterest = isset($vars['pinterest']) ? esc_url_raw($vars['pinterest']) : null;

        // Current User ID
        $user_id = get_current_user_id();

        // Update User
        wp_update_user([
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => trim(($first_name ?? '') . ' ' . ($last_name ?? '')),
            'description' => $description,
        ]);

        // Update Meta Data
        update_user_meta($user_id, 'lsd_phone', $phone);
        update_user_meta($user_id, 'lsd_mobile', $mobile);
        update_user_meta($user_id, 'lsd_fax', $fax);
        update_user_meta($user_id, 'lsd_job_title', $job_title);
        update_user_meta($user_id, 'lsd_linkedin', $linkedin);
        update_user_meta($user_id, 'lsd_twitter', $twitter);
        update_user_meta($user_id, 'lsd_facebook', $facebook);
        update_user_meta($user_id, 'lsd_pinterest', $pinterest);

        // Trigger Action
        do_action('lsd_api_user_profile_updated', $user_id, $request);

        // Response
        return $this->response([
            'data' => [
                'success' => 1,
                'user' => LSD_API_Resources_User::get($user_id),
            ],
            'status' => 200,
        ]);
    }
}
