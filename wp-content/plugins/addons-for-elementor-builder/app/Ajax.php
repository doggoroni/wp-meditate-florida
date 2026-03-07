<?php

namespace AFEB;

use AFEB\Modules\WPImport\WPImport;
use AFEB\PostTypes\Builder;
use AFEB\PostTypes\Popup;
use AFEB\Widgets\SearchForm\Helper as Search_Form_Helper;
use Elementor\Core\Kits\Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Plugin_Upgrader;
use WP_Ajax_Upgrader_Skin;
use WP_Filesystem_Base;
use WP_Query;

/**
 * "Vertex Addons for Elementor" General Ajax Class
 *
 * @class Ajax
 * @version 1.0.4
 */
class Ajax extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" Ajax
     *
     * @since 1.0.4
     */
    public function init()
    {
        $this->actions();
    }

    /**
     * Ajax Class Actions
     *
     * @since 1.0.4
     */
    public function actions()
    {
        add_action('wp_ajax_afeb_gup_ext_render_icon', [$this, 'gup_render_icon']);
        add_action('wp_ajax_nopriv_afeb_gup_ext_render_icon', [$this, 'gup_render_icon']);

        add_action('wp_ajax_afeb_create_template', [$this, 'create_template']);
        add_action('wp_ajax_afeb_create_popup', [$this, 'create_popup']);
        add_action('wp_ajax_afeb_activate_required_plugins', [$this, 'activate_required_plugins']);
        add_action('wp_ajax_afeb_import_templates_kit', [$this, 'import_templates_kit']);
        add_action('wp_ajax_afeb_import_settings', [$this, 'import_settings']);
        add_action('wp_ajax_afeb_search_form_live_results', [$this, 'search_form_live_results']);
        add_action('wp_ajax_nopriv_afeb_search_form_live_results', [$this, 'search_form_live_results']);
        add_action('wp_ajax_afeb_update_submission', [$this, 'afeb_update_submission_handler']);
    }

    /**
     * Render icon of GoingUp extension on the frontend
     *
     * @since 1.0.4
     */
    public function gup_render_icon()
    {
        check_ajax_referer('afeb_ajax_nonce', 'nonce');
        $data = isset($_POST['data']) && is_array($_POST['data']) ? map_deep($_POST['data'], 'sanitize_text_field') : [
            'library' => 'fa-regular',
            'value' => "far fa-arrow-alt-circle-up",
        ];

        if (empty($data)) wp_send_json_error();
        ob_start();
        echo wp_kses(Icons_Manager::render_icon($data), Helper::allowed_tags(['svg']));

        wp_send_json_success(str_replace('</svg>1', '</svg>', ob_get_clean()), 200);
    }

    /**
     * Create elementor template
     *
     * @since 1.3.0
     */
    public function create_template()
    {
        check_ajax_referer('afeb_ajax_nonce', 'nonce');

        $type = isset($_POST['data']['type']) ? sanitize_text_field(wp_unslash($_POST['data']['type'])) : '';

        if (!trim($type))
        {
            wp_send_json_error(['message' => esc_html__('Please use a valid template type', 'addons-for-elementor-builder')]);
        }

        $name = isset($_POST['data']['name']) ? sanitize_text_field(wp_unslash($_POST['data']['name'])) : '';

        $id = wp_insert_post([
            'post_status' => 'publish',
            'post_title' => $name,
            'post_type' => Builder::BUILDER_POST_TYPE,
            'post_name' => Builder::BUILDER_POST_TYPE,
        ]);

        if ($id)
        {
            $template_name = '';
            $page_settings = [
                // 'enable_display_conditons' => 'no',
                'display_condition' => [['_id' => 'ff0d1cf', 'conditon_group' => 'entire']],
                'display_condition_temp' => [],
                'activeItemIndex' => 1,
            ];

            switch ($type)
            {
                case 'archive':
                    $template_name = 'Archive';
                    $page_settings['display_condition'][0]['conditon_group'] = 'archive';
                    break;
                case 'dynamic-loop-item':
                    $template_name = 'Dynamic Loop Item';
                    $page_settings = [];
                    break;
                case 'footer':
                    $template_name = 'Footer';
                    break;
                case 'header':
                    $template_name = 'Header';
                    break;
                case 'single':
                    $template_name = 'Single';
                    $page_settings['display_condition'][0]['conditon_group'] = 'singular';
                    break;
            }

            if ($name == '')
            {
                $id = intval($id);
                wp_update_post([
                    'ID' => $id,
                    'post_title' => "Vertex $template_name Template_$id",
                ]);
            }

            update_post_meta($id, '_afeb_document_type', $type);
            update_post_meta($id, '_elementor_template_type', Builder::BUILDER_POST_TYPE);

            if (count($page_settings))
                update_post_meta($id, '_elementor_page_settings', $page_settings);

            $redirect = get_edit_post_link($id, 'raw');

            if (did_action('elementor/loaded') && class_exists(Plugin::class))
            {
                $document = Plugin::instance()->documents->get($id);
                if ($document)
                {
                    $redirect = $document->get_edit_url();
                }
            }

            wp_send_json_success([
                'redirect' => $redirect,
                'message' => esc_html__('The new template has been created', 'addons-for-elementor-builder'),
            ]);
        }

        wp_send_json_error(['message' => esc_html__('Something went wrong, Please try again', 'addons-for-elementor-builder')]);
    }

    /**
     * Create elementor popup
     *
     * @since 1.2.0
     */
    public function create_popup()
    {
        check_ajax_referer('afeb_ajax_nonce', 'nonce');

        $name = isset($_POST['data']['name']) ? sanitize_text_field(wp_unslash($_POST['data']['name'])) : '';

        $id = wp_insert_post([
            'post_status' => 'publish',
            'post_title' => $name,
            'post_type' => Popup::POPUP_POST_TYPE,
            'post_name' => Popup::POPUP_POST_TYPE,
        ]);

        if ($id)
        {
            if ($name == '')
            {
                $id = intval($id);
                wp_update_post([
                    'ID' => $id,
                    'post_title' => "Vertex Popup_$id",
                ]);
            }

            update_post_meta($id, '_elementor_template_type', Popup::POPUP_POST_TYPE);

            $redirect = get_edit_post_link($id, 'raw');

            if (did_action('elementor/loaded') && class_exists(Plugin::class))
            {
                $document = Plugin::instance()->documents->get($id);
                if ($document)
                {
                    $redirect = $document->get_edit_url();
                }
            }

            wp_send_json_success([
                'redirect' => $redirect,
                'message' => esc_html__('The new popup has been created', 'addons-for-elementor-builder'),
            ]);
        }

        wp_send_json_error(['message' => esc_html__('Something went wrong, Please try again', 'addons-for-elementor-builder')]);
    }

    /**
     * Activate Required Plugins
     *
     * @since 1.4.0
     */
    public function activate_required_plugins()
    {
        check_ajax_referer('afeb_ajax_nonce', 'nonce');

        $error = '';

        if (!current_user_can('install_plugins'))
            $error = esc_html__('Sorry, you are not allowed to install plugins on this site.', 'addons-for-elementor-builder');

        $plugins = isset($_POST['plugins']) ? map_deep($_POST['plugins'], 'sanitize_text_field') : [];
        $time_limit = ini_get('max_execution_time');

        set_time_limit(0);

        foreach ($plugins as $plugin_slug => $plugin_path)
        {
            if (Helper::is_plugin_installed($plugin_slug, $plugin_path))
            {
                if (!is_plugin_active($plugin_path))
                {
                    activate_plugin($plugin_path);
                }
            }
            else
            {
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

                $api = plugins_api(
                    'plugin_information',
                    [
                        'slug' => $plugin_slug,
                        'fields' => ['sections' => false],
                    ]
                );

                if (is_wp_error($api))
                    $error = $api->get_error_message();

                $skin = new WP_Ajax_Upgrader_Skin();
                $upgrader = new Plugin_Upgrader($skin);
                $result = $upgrader->install($api->download_link);

                if (is_wp_error($result)) $error = $result->get_error_message();
                else if (is_wp_error($skin->result)) $error = $skin->result->get_error_message();
                else if ($skin->get_errors()->has_errors()) $error = $skin->get_error_messages();
                else if (is_null($result))
                {
                    $error = esc_html__('Unable to connect to the filesystem. Please confirm your credentials.', 'addons-for-elementor-builder');

                    global $wp_filesystem;
                    if ($wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->has_errors())
                        $error = esc_html($wp_filesystem->errors->get_error_message());
                }

                activate_plugin($plugin_path);
            }
        }

        set_time_limit($time_limit);

        if (!empty($error)) wp_send_json_error(['message' => $error]);
        else wp_send_json_success();
    }

    /**
     * Import Settings
     *
     * @since 1.4.0
     */
    public function import_settings()
    {
        check_ajax_referer('afeb_ajax_nonce', 'nonce');

        $time_limit = ini_get('max_execution_time');

        if (!did_action('elementor/loaded') || !class_exists(Plugin::class))
        {
            wp_send_json_error(['message' => esc_html__('Elementor must be active to import settings.', 'addons-for-elementor-builder')]);
        }

        set_time_limit(0);

        $kit_id = isset($_POST['afeb_templates_kit']) ? sanitize_file_name(wp_unslash($_POST['afeb_templates_kit'])) : '';
        $random_number = substr(str_shuffle('0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ'), 0, 7);
        $error = '';
        $site_settings_url = 'https://cdn.webilia.com/u/c/vertex/' . $kit_id . '/site-settings.json?' . $random_number;

        $response = wp_remote_get($site_settings_url, ['timeout' => 20]);

        if (is_wp_error($response))
        {
            $error = esc_html__('The site settings file could not be retrieved from the destination server. The file address may be incorrect or the destination server may be temporarily experiencing issues.', 'addons-for-elementor-builder');
        }
        else
        {
            $body = wp_remote_retrieve_body($response);

            if (!empty($body))
            {
                $decoded_settings = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_settings) && !empty($decoded_settings['settings']))
                {
                    $site_settings = $decoded_settings;

                    $kit = Plugin::instance()->kits_manager->get_active_kit();
                    $active_kit_id = $kit->get_id();

                    if (!$active_kit_id)
                    {
                        $created_default_kit = Plugin::instance()->kits_manager->create_default();

                        if (!$created_default_kit)
                        {
                            set_time_limit($time_limit);
                            wp_send_json_error(['message' => esc_html__('There was a problem creating the default Elementor kit, the default kit cannot be created.', 'addons-for-elementor-builder')]);
                        }

                        update_option(Manager::OPTION_ACTIVE, $created_default_kit);
                        $active_kit_id = $created_default_kit;
                    }

                    $default_kit = Plugin::instance()->documents->get_doc_for_frontend($active_kit_id);
                    $kit_settings = $default_kit->get_settings();
                    $new_settings = $site_settings['settings'];
                    $settings = array_merge($kit_settings, $new_settings);
                    $save_new_settings = $default_kit->save(['settings' => $settings]);

                    if ($save_new_settings === true)
                    {
                        update_option('elementor_disable_color_schemes', 'yes');
                        update_option('elementor_disable_typography_schemes', 'yes');

                        set_time_limit($time_limit);
                        wp_send_json_success();
                    }
                }
                else
                {
                    $error = esc_html__('No settings exist. The site settings file may have been downloaded incompletely or it may be corrupted.', 'addons-for-elementor-builder');
                }
            }
            else
            {
                $error = esc_html__('No settings exist. The site settings file may have been downloaded incompletely or it may be corrupted.', 'addons-for-elementor-builder');
            }
        }

        set_time_limit($time_limit);

        if (!empty($error))
        {
            wp_send_json_error(['message' => $error]);
        }
    }

    /**
     * Import Templates Kit
     *
     * @since 1.4.0
     */
    public function import_templates_kit()
    {
        check_ajax_referer('afeb_ajax_nonce', 'nonce');

        $args = [
            'post_type' => [
                'page',
                'post',
                'product',
                'afeb-builder',
                'afeb-popup',
                'elementor_library',
                'attachment',
            ],
            'post_status' => 'any',
            'posts_per_page' => '-1',
            'meta_key' => '_afeb_demo_import_item',
        ];
        $error = esc_html__('Previous imported files will not be reset.', 'addons-for-elementor-builder');

        $time_limit = ini_get('max_execution_time');
        set_time_limit(0);

        $imported_items = new WP_Query($args);

        if ($imported_items->have_posts())
        {
            while ($imported_items->have_posts())
            {
                $imported_items->the_post();

                // Dont Delete Elementor Kit
                if (get_the_title() == 'Default Kit') continue;
                wp_delete_post(get_the_ID(), true);
            }

            wp_reset_query();

            $imported_terms = get_terms([
                'meta_key' => '_afeb_demo_import_item',
                'posts_per_page' => -1,
                'hide_empty' => false,
            ]);

            if (!empty($imported_terms))
            {
                foreach ($imported_terms as $imported_term)
                    wp_delete_term($imported_term->term_id, $imported_term->taxonomy);
            }
        }

        $error = '';
        add_filter('upload_mimes', function ($mimes)
        {
            // Allow SVG files.
            $mimes['svg'] = 'image/svg+xml';
            $mimes['svgz'] = 'image/svg+xml';

            // Allow XML files.
            $mimes['xml'] = 'text/xml';

            // Allow JSON files.
            $mimes['json'] = 'application/json';

            return $mimes;
        }, 99);

        add_filter('wp_handle_upload_prefilter', function ($file)
        {
            if ($file['type'] === 'image/svg+xml')
            {
                $file_content = file_get_contents($file['tmp_name']);
                $sanitized_content = (new Security())->sanitize_svg($file_content);
                file_put_contents($file['tmp_name'], $sanitized_content);
            }

            return $file;
        });

        if (!defined('WP_LOAD_IMPORTERS'))
            define('WP_LOAD_IMPORTERS', true);

        if (!class_exists('WP_Import'))
        {
            $wp_importer = AFEB_ABSPATH . '/app/Modules/WPImport/WPImporter.php';
            if (file_exists($wp_importer)) require $wp_importer;
        }

        if (class_exists('AFEB\Modules\WPImport\WPImport'))
        {
            $kit_id = isset($_POST['afeb_templates_kit']) ? sanitize_file_name(wp_unslash($_POST['afeb_templates_kit'])) : '';

            add_filter('intermediate_image_sizes_advanced', [new Helper, 'disable_extra_image_sizes'], 10, 3);

            $templates_kit = new TemplatesKit();
            $local_file_path = $templates_kit->download_template($kit_id);
            $wp_import = new WPImport($local_file_path, ['fetch_attachments' => true]);

            ob_start();
            $wp_import->run();
            ob_end_clean();

            unlink($local_file_path);

            $result = serialize($wp_import);

            if (strpos($result, 'summary') !== false)
            {

                $templates_kit->fix_elementor_images($kit_id);
                $templates_kit->setup_templates($kit_id);

                $post = get_page_by_path('hello-world', OBJECT, 'post');
                if ($post) wp_delete_post($post->ID, true);

                Helper::regenerate_extra_image_sizes();

                set_time_limit($time_limit);
                wp_send_json_success();
            }
            else
            {
                $wp_import = array_values((array) $wp_import);

                foreach ($wp_import as $value)
                {
                    if (
                        isset($value['status']) &&
                        isset($value['errors'])
                    )
                    {
                        $error = isset($value['errors'][0]) ? esc_html($value['errors'][0]) : '';
                    }
                }
            }
        }
        else
        {
            $error = esc_html__('Operation not completed, Class WPImport does not exist.', 'addons-for-elementor-builder');
        }

        set_time_limit($time_limit);
        wp_send_json_error(['message' => $error]);
    }

    /**
     * Live results output for Search Form widget
     *
     * @since 1.5.0
     */
    public function search_form_live_results()
    {
        check_ajax_referer('afeb_ajax_nonce', 'nonce');

        $template_id = !empty($_POST['data']['template_id']) ? intval(wp_unslash($_POST['data']['template_id'])) : -1;

        if ($template_id !== -1)
        {
            $post_type = !empty($_POST['data']['post_type']) ? sanitize_text_field(wp_unslash($_POST['data']['post_type'])) : '';
            $number_of_items = !empty($_POST['data']['number_of_items']) ? intval(wp_unslash($_POST['data']['number_of_items'])) : 5;
            $orderby = !empty($_POST['data']['orderby']) ? sanitize_text_field(wp_unslash($_POST['data']['orderby'])) : '';
            $order = !empty($_POST['data']['order']) ? sanitize_text_field(wp_unslash($_POST['data']['order'])) : '';
            $s = !empty($_POST['data']['s']) ? sanitize_text_field(wp_unslash($_POST['data']['s'])) : '';

            $settings = [
                'template_id' => $template_id,
                'post_type' => $post_type,
                'number_of_items' => $number_of_items,
                'orderby' => $orderby,
                'order' => $order,
                's' => $s,
            ];

            $helper = new Search_Form_Helper();
            $query = $helper->get_query($settings);
            $is_content = $helper->dynamic_loop_item($template_id, $query->posts);

            if (!$is_content)
            {
                echo -1;
            }

            exit;
        }

        echo -1;
        exit;
    }

    public function afeb_update_submission_handler()
    {

        if (!current_user_can('edit_posts'))
        {
            wp_send_json_error(__('Permission denied.', 'addons-for-elementor-builder'));
        }

        $submission_id = intval($_POST['post_id'] ?? 0);
        if (!$submission_id)
        {
            wp_send_json_error(__('Invalid submission ID.', 'addons-for-elementor-builder'));
        }

        // Get original form reference
        $page_id = intval(get_post_meta($submission_id, 'afeb_page_id', true));
        $form_id = sanitize_text_field(get_post_meta($submission_id, 'afeb_form_id', true));

        if (!$page_id || !$form_id)
        {
            wp_send_json_error(__('Parent form reference not found.', 'addons-for-elementor-builder'));
        }

        $current_meta = get_post_meta($submission_id, 'afeb_form_fields', true);
        $current_meta = is_array($current_meta) ? $current_meta : [];

        // Load Elementor widget settings
        $settings = Widgets::get_widget_preview_settings($page_id, $form_id);
        $original_fields = $settings['form_fields'] ?? [];

        // Posted values from admin editor
        $submitted_fields = isset($_POST['form_fields']) && is_array($_POST['form_fields']) ? $_POST['form_fields'] : [];

        if (empty($submitted_fields))
        {
            $fallback_fields = isset($_POST['afeb-form-fields']) && is_array($_POST['afeb-form-fields']) ? wp_unslash($_POST['afeb-form-fields']) : [];

            if (!empty($fallback_fields) && !empty($current_meta))
            {
                foreach ($current_meta as $index => $meta_field)
                {
                    $raw_value = null;

                    if (array_key_exists($index, $fallback_fields))
                    {
                        $raw_value = $fallback_fields[$index];
                    }
                    else if (isset($meta_field['_id']) && array_key_exists($meta_field['_id'], $fallback_fields))
                    {
                        $raw_value = $fallback_fields[$meta_field['_id']];
                    }

                    if (null === $raw_value)
                    {
                        continue;
                    }

                    if ('checkbox' === ($meta_field['type'] ?? ''))
                    {
                        $raw_value = is_array($raw_value)
                            ? array_values(array_filter(array_map('wp_unslash', $raw_value), 'strlen'))
                            : (array) wp_unslash($raw_value);
                    }
                    else if ('upload' === ($meta_field['type'] ?? ''))
                    {
                        $raw_value = array_values(array_filter((array) $raw_value));
                    }

                    $submitted_fields[] = array_merge($meta_field, ['value' => $raw_value]);
                }
            }
        }

        if (!isset($_FILES['form_files']) && isset($_FILES['afeb-form-fields']) && !empty($current_meta))
        {
            $files = $_FILES['afeb-form-fields'];
            $_FILES['form_files'] = [
                'name' => [],
                'type' => [],
                'tmp_name' => [],
                'error' => [],
                'size' => [],
            ];

            foreach ($current_meta as $index => $meta_field)
            {
                $fid = $meta_field['_id'] ?? $index;

                foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $prop)
                {
                    if (isset($files[$prop][$index]))
                    {
                        $_FILES['form_files'][$prop][$fid] = $files[$prop][$index];
                    }
                }
            }
        }
        $new_fields_meta = [];
        $field_errors = [];

        if (!empty($submitted_fields) && !empty($current_meta))
        {
            $current_meta_by_id = [];
            foreach ($current_meta as $meta_field)
            {
                if (empty($meta_field['_id']))
                {
                    continue;
                }
                $current_meta_by_id[$meta_field['_id']] = $meta_field;
            }

            foreach ($submitted_fields as $idx => $field)
            {
                $fid = $field['_id'] ?? '';

                if ($fid && isset($current_meta_by_id[$fid]))
                {
                    $submitted_fields[$idx] = array_merge($current_meta_by_id[$fid], $field);
                }
                else if (isset($current_meta[$idx]) && is_array($current_meta[$idx]))
                {
                    $submitted_fields[$idx] = array_merge($current_meta[$idx], $field);
                }
            }
        }

        foreach ($submitted_fields as $field)
        {
            $fid = $field['_id'] ?? '';
            $type = $field['type'] ?? 'text';
            $value = $field['value'] ?? [];

            // Match saved Elementor definition
            $original = null;
            foreach ($original_fields as $of)
            {
                if (!empty($of['field_id']) && $of['field_id'] === $fid)
                {
                    $original = $of;
                    break;
                }
            }

            if ($type === 'upload' && $original)
            {
                // Extract validation rules
                $allowed_exts = !empty($original['allowed_file_types'])
                    ? array_map('strtolower', array_map('trim', explode(',', $original['allowed_file_types'])))
                    : [];
                $allowed_mimes = wp_get_mime_types();
                if (!empty($allowed_exts))
                {
                    $allowed_mimes = array_intersect_key($allowed_mimes, array_flip($allowed_exts));
                }
                $max_size = !empty($original['max_file_size']) ? intval($original['max_file_size']) * 1024 : 0; // KB → bytes
                $is_required = !empty($original['required']);

                // Normalize from $_FILES
                $file_data = [];
                if (isset($_FILES['form_files']['name'][$fid]))
                {
                    foreach ((array) $_FILES['form_files']['name'][$fid] as $idx => $name)
                    {
                        if (!$name)
                        {
                            continue;
                        }
                        $file_data[] = [
                            'name' => $_FILES['form_files']['name'][$fid][$idx],
                            'type' => $_FILES['form_files']['type'][$fid][$idx],
                            'tmp_name' => $_FILES['form_files']['tmp_name'][$fid][$idx],
                            'error' => $_FILES['form_files']['error'][$fid][$idx],
                            'size' => $_FILES['form_files']['size'][$fid][$idx],
                        ];
                    }
                }

                // Validate
                $validation = \AFEB\Widgets\FormBuilder\Helper::validate_uploaded_files(
                    !empty($file_data) ? $file_data : (array) $value,
                    [
                        'mime_types' => $allowed_mimes,
                        'exts' => $allowed_exts,
                        'max_size' => $max_size,
                    ],
                    $is_required
                );

                if (!$validation['valid'])
                {
                    $field_errors[$fid] = $validation['errors'];
                }

                $new_fields_meta[] = array_merge($field, ['value' => $validation['files'] ?? []]);
            }
            else
            {
                // Non-upload fields, keep sanitized value
                if (is_array($value))
                {
                    $sanitized = map_deep($value, 'sanitize_text_field');
                }
                else
                {
                    $sanitized = sanitize_text_field($value);
                }
                $new_fields_meta[] = array_merge($field, ['value' => $sanitized]);
            }
        }

        if (!empty($field_errors))
        {
            wp_send_json_error([
                'message' => __('Some fields have errors.', 'addons-for-elementor-builder'),
                'errors' => $field_errors,
            ]);
        }

        // Save updated meta
        update_post_meta($submission_id, 'afeb_form_fields', $new_fields_meta);

        wp_send_json_success(__('Submission updated successfully.', 'addons-for-elementor-builder'));
    }
}
