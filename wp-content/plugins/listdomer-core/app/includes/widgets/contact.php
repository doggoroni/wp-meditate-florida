<?php

class LSDRC_Widgets_Contact extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'LSDRC_Widgets_Contact',
            esc_html__('(Listdomer) Contact', 'listdomer-core'),
            ['description' => esc_html__('A simple contact details widget to include in footer', 'listdomer-core')]
        );
    }

    public function widget($args, $instance)
    {
        $email = isset($instance['email']) && is_email($instance['email']) ? $instance['email'] : '';
        $phone = $instance['phone'] ?? '';
        $facebook = $instance['facebook'] ?? '';
        $twitter = $instance['twitter'] ?? '';
        $instagram = $instance['instagram'] ?? '';
        $linkedin = $instance['linkedin'] ?? '';
        $pinterest = $instance['pinterest'] ?? '';
        $youtube = $instance['youtube'] ?? '';
        $whatsapp = $instance['whatsapp'] ?? '';
        $telegram = $instance['telegram'] ?? '';

        $contact_title = $instance['contact_title'] ?? '';
        $social_title = $instance['social_title'] ?? '';
        $hide_logo = !empty($instance['hide_logo']);

        // Before Widget
        echo isset($args['before_widget']) ? LSDRC_Base::kses($args['before_widget']) : '';

        echo '<div class="listdomer-contact-widget">';

        $listdomer_logo = LSDRC_Settings::get('site_logo');

        echo '<div class="listdomer-contact-widget-logo-name">';

        // Main Title
        if (!empty($instance['title']))
        {
            echo ($args['before_title'] ?? '')
                . apply_filters('widget_title', $instance['title'])
                . ($args['after_title'] ?? '');
        }

        if (!$hide_logo)
        {
            // Logo
            if (isset($listdomer_logo['url']) && trim($listdomer_logo['url']))
            {
                echo '<a href="' . esc_url(home_url()) . '" class="custom-logo-link">
                        <img src="' . esc_url($listdomer_logo['url']) . '" alt="' . esc_attr__('Site Logo', 'listdomer-core') . '" class="custom-logo">
                      </a>';
            }
            else echo get_custom_logo();
        }

        echo '</div>';

        echo '<div class="listdomer-contact-section">';
        // Contact Info Section
        if (trim($contact_title)) echo '<h4 class="listdomer-contact-widget-section-title lsdr-title">' . esc_html($contact_title) . '</h4>';

        echo '<ul class="listdomer-contact-widget-info">
            ' . (trim($phone) ? '<li><span>' . esc_html__('Phone', 'listdomer-core') . '</span>' . esc_html($phone) . '</li>' : '') . '
            ' . (trim($email) ? '<li><span>' . esc_html__('Email', 'listdomer-core') . '</span>' . esc_html($email) . '</li>' : '') . '
        </ul>';
        echo '</div>';

        echo '<div class="listdomer-social-section">';
        // Social Section
        if (trim($social_title)) echo '<h4 class="listdomer-contact-widget-section-title lsdr-title">' . esc_html($social_title) . '</h4>';

        echo '<ul class="listdomer-contact-widget-social">
            ' . (trim($facebook) ? '<li><a href="' . esc_url($facebook) . '" target="_blank"><i class="fab fa-facebook-f"></i></a></li>' : '') . '
            ' . (trim($twitter) ? '<li><a href="' . esc_url($twitter) . '" target="_blank"><i class="fa fa-times"></i></a></li>' : '') . '
            ' . (trim($instagram) ? '<li><a href="' . esc_url($instagram) . '" target="_blank"><i class="fab fa-instagram"></i></a></li>' : '') . '
            ' . (trim($linkedin) ? '<li><a href="' . esc_url($linkedin) . '" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>' : '') . '
            ' . (trim($pinterest) ? '<li><a href="' . esc_url($pinterest) . '" target="_blank"><i class="fab fa-pinterest"></i></a></li>' : '') . '
            ' . (trim($youtube) ? '<li><a href="' . esc_url($youtube) . '" target="_blank"><i class="fab fa-youtube"></i></a></li>' : '') . '
            ' . (trim($whatsapp) ? '<li><a href="' . esc_url($whatsapp) . '" target="_blank"><i class="fab fa-whatsapp"></i></a></li>' : '') . '
            ' . (trim($telegram) ? '<li><a href="' . esc_url($telegram) . '" target="_blank"><i class="fab fa-telegram"></i></a></li>' : '') . '
        </ul>';
        echo '</div>';

        echo '</div>';

        // After Widget
        echo isset($args['after_widget']) ? LSDRC_Base::kses($args['after_widget']) : '';
    }

    public function form($instance)
    {
        echo '<div id="' . esc_attr($this->get_field_id('lsdrc_wrapper')) . '">';

        $hide_logo = !empty($instance['hide_logo']);

        // Main title
        echo '<p>
            <label for="' . esc_attr($this->get_field_id('title')) . '">' . esc_html__('Widget Title', 'listdomer-core') . '</label>
            <input class="widefat" type="text" id="' . esc_attr($this->get_field_id('title')) . '" name="' . esc_attr($this->get_field_name('title')) . '" value="' . esc_attr($instance['title'] ?? '') . '">
        </p>';

        echo '<p>
            <label for="' . esc_attr($this->get_field_id('hide_logo')) . '">' . esc_html__('Disable logo', 'listdomer-core') . '</label>
            <input type="checkbox" id="' . esc_attr($this->get_field_id('hide_logo')) . '" name="' . esc_attr($this->get_field_name('hide_logo')) . '" value="1" ' . checked($hide_logo, true, false) . '>
        </p>';

        // Contact Section Title
        echo '<p>
            <label for="' . esc_attr($this->get_field_id('contact_title')) . '">' . esc_html__('Contact Section Title', 'listdomer-core') . '</label>
            <input class="widefat" type="text" id="' . esc_attr($this->get_field_id('contact_title')) . '" name="' . esc_attr($this->get_field_name('contact_title')) . '" value="' . esc_attr($instance['contact_title'] ?? '') . '">
        </p>';

        // Social Section Title
        echo '<p>
            <label for="' . esc_attr($this->get_field_id('social_title')) . '">' . esc_html__('Social Section Title', 'listdomer-core') . '</label>
            <input class="widefat" type="text" id="' . esc_attr($this->get_field_id('social_title')) . '" name="' . esc_attr($this->get_field_name('social_title')) . '" value="' . esc_attr($instance['social_title'] ?? '') . '">
        </p>';

        // Rest of the form fields (email, phone, social links)
        $fields = [
            'email' => ['label' => 'Email', 'type' => 'email'],
            'phone' => ['label' => 'Phone', 'type' => 'tel'],
            'facebook' => ['label' => 'Facebook', 'type' => 'url'],
            'twitter' => ['label' => 'X', 'type' => 'url'],
            'instagram' => ['label' => 'Instagram', 'type' => 'url'],
            'linkedin' => ['label' => 'LinkedIn', 'type' => 'url'],
            'pinterest' => ['label' => 'Pinterest', 'type' => 'url'],
            'youtube' => ['label' => 'YouTube', 'type' => 'url'],
            'whatsapp' => ['label' => 'WhatsApp', 'type' => 'url'],
            'telegram' => ['label' => 'Telegram', 'type' => 'url'],
        ];

        foreach ($fields as $key => $data)
        {
            echo '<p>
                <label for="' . esc_attr($this->get_field_id($key)) . '">' . esc_html__($data['label'], 'listdomer-core') . '</label>
                <input class="widefat" type="' . esc_attr($data['type']) . '" id="' . esc_attr($this->get_field_id($key)) . '" name="' . esc_attr($this->get_field_name($key)) . '" value="' . esc_attr($instance[$key] ?? '') . '">
            </p>';
        }

        echo '</div>';
    }

    public function update($new_instance, $old_instance): array
    {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        $instance['contact_title'] = sanitize_text_field($new_instance['contact_title'] ?? '');
        $instance['social_title'] = sanitize_text_field($new_instance['social_title'] ?? '');
        $instance['hide_logo'] = !empty($new_instance['hide_logo']) ? 1 : 0;
        $instance['email'] = isset($new_instance['email']) && is_email($new_instance['email']) ? sanitize_text_field($new_instance['email']) : '';
        $instance['phone'] = sanitize_text_field($new_instance['phone'] ?? '');

        $social_fields = ['facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'youtube', 'whatsapp', 'telegram'];
        foreach ($social_fields as $field) $instance[$field] = esc_url($new_instance[$field] ?? '');

        return $instance;
    }
}
