<?php

namespace AFEB\Widgets\FormBuilder;

use Elementor\Icons_Manager;
use Elementor\Utils;
use function wp_handle_upload;

/**
 * "Vertex Addons for Elementor" FormBuilder Widget Helper Class
 *
 * @class Helper
 * @version 1.4.0
 */
class Helper
{
    public function form_fields_render_attributes($context, $i, $settings, $item)
    {
        $field_identifier = $this->get_field_identifier($item);

        $context->add_render_attribute(
            [
                'field-group' . $i => [
                    'class' => [
                        'afeb-form-field-type-' . $item['field_type'],
                        'afeb-field-group',
                        'afeb-column',
                        'afeb-field-group-' . esc_attr($field_identifier),
                    ],
                ],
                'input' . $i => [
                    'type' => ('acceptance' === $item['field_type']) ? 'checkbox' : (($item['field_type'] === 'upload') ? 'file' : $item['field_type']),
                    'name' => $this->get_attribute_name($item),
                    'id' => $this->get_attribute_id($item),
                    'class' => [
                        'afeb-form-field',
                        empty($item['css_classes']) ? '' : esc_attr($item['css_classes']),
                    ],
                ],
                'label' . $i => [
                    'for' => $this->get_attribute_id($item),
                    'class' => 'afeb-form-field-label',
                ],
            ]
        );

        if (empty($item['width']))
            $item['width'] = '100';

        $context->add_render_attribute('field-group' . $i, 'class', 'elementor-repeater-item-' . esc_attr($item['_id']));

        if ($settings['show_placeholders'] == 'true' && !Utils::is_empty($item['placeholder']))
            $context->add_render_attribute('input' . $i, 'placeholder', $item['placeholder']);

        if (!empty($item['field_value']))
            $context->add_render_attribute('input' . $i, 'value', $item['field_value']);

        if (!$settings['show_labels'])
            $context->add_render_attribute('label' . $i, 'class', 'afeb-hidden-element');

        if (!empty($item['required']))
        {
            $class = 'afeb-form-field-required';
            if (!empty($settings['mark_required']))
                $class .= ' afeb-mark-required';

            $context->add_render_attribute('field-group' . $i, 'class', $class);
            $this->add_required_attribute($context, 'input' . $i);
        }
    }

    public function get_attribute_name($item): string
    {
        $field_identifier = $this->get_field_identifier($item);
        if ($field_identifier === '') return 'form_fields[]';

        return "form_fields[$field_identifier]";
    }

    private function get_field_identifier($item)
    {
        if (!empty($item['field_id'])) return $item['field_id'];

        return $item['_id'] ?? '';
    }

    public function get_attribute_id($item): string
    {
        $id_suffix = !empty($item['field_id']) ? $item['field_id'] : $item['_id'];
        return 'form-field-' . $id_suffix;
    }

    private function add_required_attribute($context, $element)
    {
        $context->add_render_attribute($element, 'required', 'required');
        $context->add_render_attribute($element, 'aria-required', 'true');
    }

    public function make_textarea_field($context, $item, $item_index): string
    {
        $context->add_render_attribute('textarea' . $item_index, [
            'class' => [
                'afeb-form-field-textual',
                'afeb-form-field',
                'elementor-field-textual',
                esc_attr($item['css_classes']),
            ],
            'name' => $this->get_attribute_name($item),
            'id' => $this->get_attribute_id($item),
            'rows' => $item['rows'],
        ]);

        if ($context->get_settings_for_display()['show_placeholders'] == 'true' && !Utils::is_empty($item['placeholder']))
        {
            $context->add_render_attribute('textarea' . $item_index, 'placeholder', $item['placeholder']);
        }

        if ($item['required'])
        {
            $this->add_required_attribute($context, 'textarea' . $item_index);
        }

        $value = empty($item['field_value']) ? '' : $item['field_value'];

        return '<textarea ' . $context->get_render_attribute_string('textarea' . $item_index) . '>' . $value . '</textarea>';
    }

    public function make_select_field($context, $item)
    {
        ob_start();
        ?>
        <div class="afeb-form-field afeb-select-wrap <?php echo esc_attr($item['css_classes']); ?>">
            <select
                id="<?php echo $this->get_attribute_id($item); ?>"
                class="afeb-form-field-textual elementor-field-textual"
                title=""
                name="<?php echo $this->get_attribute_name($item); ?>"
                <?php echo $item['required'] ? 'required' : '' ?>>

                <?php
                $options_temp = [];
                $options = preg_split("/\\r\\n|\\r|\\n/", $item['field_options']);

                foreach ($options as $key => $option)
                {
                    $option_value = esc_attr($option);
                    $option_label = esc_html($option);

                    if (strpos($option, '|') !== false)
                    {
                        [$label, $value] = explode('|', $option);
                        $option_value = esc_attr($value);
                        $option_label = esc_html($label);
                    }

                    if (isset($options_temp[$option_value]) || !trim($option_value))
                    {
                        continue;
                    }
                    ?>
                    <option value="<?php echo esc_attr($option_value); ?>">
                        <?php
                        // PHPCS - $option_label is already escaped
                        echo $option_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                        ?>
                    </option>
                    <?php
                    $options_temp[$option_value] = $option_label;
                } ?>
            </select>
        </div>
        <?php
        return ob_get_clean();
    }

    public function make_radio_checkbox_field($context, $item, $type)
    {
        $options = array_values(
            array_filter(
                preg_split("/\\r\\n|\\r|\\n/", $item['field_options']),
                static function ($option)
                {
                    return trim($option) !== '';
                }
            )
        );

        if (empty($options))
        {
            return '';
        }

        $options_temp = [];
        ob_start();
        ?>
        <div
            class="afeb-field-sub-group <?php echo esc_attr($item['css_classes']) . ' ' . esc_attr($item['inline_list']); ?>">
            <?php
            foreach ($options as $key => $option):
                $option_label = $option;
                $option_value = $option;

                if (strpos($option, '|') !== false)
                {
                    [$option_label, $option_value] = explode('|', $option);
                }

                if (isset($options_temp[$option_value]) || !trim($option_value))
                {
                    continue;
                }
                ?>
                <span class="afeb-form-field-option"
                      data-key="form-field-<?php echo esc_attr($this->get_field_identifier($item)); ?>">
                    <input
                        id="<?php echo esc_attr($this->get_attribute_id($item) . '-' . $key) ?>"
                        type="<?php echo esc_attr($type) ?>"
                        name="<?php echo esc_attr($this->get_attribute_name($item) . (($type == 'checkbox' && count($options) > 1) ? '[]' : '')); ?>"
                        value="<?php echo esc_attr($option_value); ?>"
                        <?php echo $item['required'] ? 'required' : '' ?>>
                    <?php if ($type == 'checkbox'): ?>
                        <input
                            type="hidden"
                            name="<?php echo esc_attr($this->get_attribute_name($item) . (count($options) > 1 ? '[]' : '')); ?>">
                    <?php endif; ?>
                    <label
                        for="<?php echo esc_attr($this->get_attribute_id($item) . '-' . $key) ?>"><?php echo esc_html($option_label); ?></label>
                </span>
                <?php
                $options_temp[$option_value] = $option_label;
            endforeach;
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_form_icon($context, $settings)
    {
        ?>
        <span <?php $context->print_render_attribute_string('icon-align'); ?>>
            <?php Icons_Manager::render_icon($settings['selected_button_icon'], ['aria-hidden' => 'true']); ?>
            <?php if (empty($settings['button_text'])): ?>
                <span class="afeb-hidden-element">
                    <?php echo esc_html__('Submit', 'addons-for-elementor-builder'); ?>
                </span>
            <?php endif; ?>
        </span>
        <?php
    }

    public function render_submit_button($context, $settings)
    {
        ?>
        <button type="submit" <?php $context->print_render_attribute_string('button'); ?>>
            <span <?php $context->print_render_attribute_string('content-wrapper'); ?>>
                <?php if (
                    !empty($settings['selected_button_icon']) &&
                    $settings['button_icon_align'] === 'left'
                ): ?>
                    <?php $this->render_form_icon($context, $settings); ?>
                <?php endif; ?>
                <?php if (!empty($settings['button_text'])): ?>
                    <span><?php echo wp_kses_post($settings['button_text']); ?></span>
                <?php endif; ?>
                <?php if (
                    !empty($settings['selected_button_icon']) &&
                    $settings['button_icon_align'] === 'right'
                ): ?>
                    <?php $this->render_form_icon($context, $settings); ?>
                <?php endif; ?>
            </span>
        </button>
        <?php
    }

    /**
     * Get all available field types for the form builder.
     *
     * @return array
     */
    public static function get_field_types(): array
    {
        return [
            'text' => esc_html__('Text', 'addons-for-elementor-builder'),
            'textarea' => esc_html__('Textarea', 'addons-for-elementor-builder'),
            'email' => esc_html__('Email', 'addons-for-elementor-builder'),
            'url' => esc_html__('URL', 'addons-for-elementor-builder'),
            'number' => esc_html__('Number', 'addons-for-elementor-builder'),
            'tel' => esc_html__('Tel', 'addons-for-elementor-builder'),
            'radio' => esc_html__('Radio', 'addons-for-elementor-builder'),
            'select' => esc_html__('Select', 'addons-for-elementor-builder'),
            'checkbox' => esc_html__('Checkbox', 'addons-for-elementor-builder'),
            'date' => esc_html__('Date', 'addons-for-elementor-builder'),
            'time' => esc_html__('Time', 'addons-for-elementor-builder'),
            'upload' => esc_html__('File Upload', 'addons-for-elementor-builder'),
            'password' => esc_html__('Password', 'addons-for-elementor-builder'),
            'html' => esc_html__('HTML', 'addons-for-elementor-builder'),
            'recaptcha_v3' => esc_html__('reCAPTCHA V3', 'addons-for-elementor-builder'),
            'hidden' => esc_html__('Hidden', 'addons-for-elementor-builder'),
            'step' => esc_html__('Step', 'addons-for-elementor-builder'),
        ];
    }

    /**
     * Get the default fields structure for a new form.
     *
     * @return array
     */
    public static function get_default_fields(): array
    {
        return [
            [
                '_id' => 'field_' . wp_generate_password(8, false),
                'type' => 'text',
                'label' => esc_html__('Untitled', 'addons-for-elementor-builder'),
                'placeholder' => '',
                'required' => false,
                'value' => '',
                'column_width' => 100,
            ],
        ];
    }

    /**
     * Whitelisted file types for upload fields.
     *
     * @return array
     */
    public static function get_whitelisted_file_types(): array
    {
        $defaults = [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip'],
            'mime_types' => [
                'image/jpeg', 'image/png', 'image/gif',
                'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/zip',
            ],
            'max_size' => 5 * 1024 * 1024, // 5MB
        ];

        return apply_filters('afeb/form_builder/whitelisted_file_types', $defaults);
    }

    public static function validate_uploaded_files($files, $rules = [], $is_required = false): array
    {
        $normalized_files = [];
        $errors = [];

        // Convert MB to bytes if needed (common case where max_size is in MB)
        $max_size = !empty($rules['max_size']) ?
            (is_numeric($rules['max_size']) ? (int) $rules['max_size'] * 1024 * 1024 : wp_max_upload_size())
            : wp_max_upload_size();

        $allowed_exts = !empty($rules['exts']) ? array_map('strtolower', (array) $rules['exts']) : [];
        $allowed_mimes = !empty($rules['mime_types']) ? (array) $rules['mime_types'] : wp_get_mime_types();

        if (!is_array($files))
        {
            $files = $files ? [$files] : [];
        }

        foreach ($files as $file)
        {
            // Handle newly uploaded files
            if (is_array($file) && isset($file['tmp_name']))
            {
                // Skip empty files
                if (empty($file['name']) || empty($file['tmp_name'])) continue;

                // Basic security check
                if (!is_uploaded_file($file['tmp_name']))
                {
                    $errors[] = __('Security verification failed for uploaded file.', 'addons-for-elementor-builder');
                    continue;
                }

                // Check for upload errors
                if ($file['error'] !== UPLOAD_ERR_OK)
                {
                    $errors[] = self::get_upload_error_message($file['error'], $file['name']);
                    continue;
                }

                // Validate file type
                $filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mimes);
                if (!$filetype['ext'] || !$filetype['type'])
                {
                    $errors[] = sprintf(__('"%s" is not a valid file type.', 'addons-for-elementor-builder'), esc_html($file['name']));
                    continue;
                }

                // Check against allowed extensions
                if (!empty($allowed_exts) && !in_array(strtolower($filetype['ext']), $allowed_exts, true))
                {
                    $errors[] = sprintf(
                        __('"%s" is not an allowed file type. Allowed: %s', 'addons-for-elementor-builder'),
                        esc_html($file['name']),
                        esc_html(implode(', ', $allowed_exts))
                    );
                    continue;
                }

                // Check file size
                if ($file['size'] > $max_size)
                {
                    $errors[] = sprintf(
                        __('"%s" exceeds maximum file size of %s.', 'addons-for-elementor-builder'),
                        esc_html($file['name']),
                        size_format($max_size)
                    );
                    continue;
                }

                if (!function_exists('wp_handle_upload')) require_once ABSPATH . 'wp-admin/includes/file.php';

                // Handle the upload
                $upload = wp_handle_upload($file, [
                    'test_form' => false,
                    'test_type' => true,
                    'mimes' => $allowed_mimes,
                ]);

                if (isset($upload['error']))
                {
                    $errors[] = sprintf(__('Error uploading "%s": %s', 'addons-for-elementor-builder'), esc_html($file['name']), esc_html($upload['error']));
                    continue;
                }

                $normalized_files[] = esc_url_raw($upload['url']);
            }
            // Handle existing URLs (for multi-step forms)
            else if (is_string($file) && filter_var($file, FILTER_VALIDATE_URL)) $normalized_files[] = esc_url_raw($file);
        }

        // Required field check
        if ($is_required && empty($normalized_files)) $errors[] = __('This field is required.', 'addons-for-elementor-builder');

        return [
            'valid' => empty($errors),
            'files' => $normalized_files,
            'errors' => $errors,
        ];
    }


    /**
     * Get user-friendly upload error message
     */
    private static function get_upload_error_message($error_code, $filename): string
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => __('"%s" exceeds server upload limit.', 'addons-for-elementor-builder'),
            UPLOAD_ERR_FORM_SIZE => __('"%s" exceeds form upload limit.', 'addons-for-elementor-builder'),
            UPLOAD_ERR_PARTIAL => __('"%s" was only partially uploaded.', 'addons-for-elementor-builder'),
            UPLOAD_ERR_NO_FILE => __('No file was uploaded for "%s".', 'addons-for-elementor-builder'),
            UPLOAD_ERR_NO_TMP_DIR => __('Missing temporary folder for "%s".', 'addons-for-elementor-builder'),
            UPLOAD_ERR_CANT_WRITE => __('Failed to write "%s" to disk.', 'addons-for-elementor-builder'),
            UPLOAD_ERR_EXTENSION => __('Server extension stopped "%s" upload.', 'addons-for-elementor-builder'),
        ];

        return sprintf(
            $messages[$error_code] ?? __('Upload error for "%s".', 'addons-for-elementor-builder'),
            esc_html($filename)
        );
    }
}
