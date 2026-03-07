<?php

namespace AFEB\Widgets\FormBuilder;

use AFEB\Assets;
use AFEB\Controls\CHelper as OldCHelper;
use AFEB\Controls\Helper as CHelper;
use AFEB\Widgets;
use AFEB\Widgets\FormBuilder\Helper as FormBuilderHelper;
use Elementor\Icons_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" FormBuilder Widget Class
 * 
 * @class FormBuilder
 * @version 1.4.0
 */
class FormBuilder extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * @var bool
     */
    private $is_editor;

    /**
     * @var string
     */
    private $last_prev_btn_text;

    /**
     * @var string $keys
     */
    private $keys = 'form_fields,error_message,success_message,email_content_type,email_content,form_metadata,' .
        'email_to,email_subject,email_from_name,email_from,email_reply_to,email_to_cc,email_to_bcc,submit_actions,redirect_to';

    /**
     * FormBuilder Constructor
     * 
     * @since 1.4.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->is_editor = Plugin::instance()->editor->is_edit_mode();
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->form_builder_style();
        $this->assets->form_builder_script();
        // In your plugin's main file (e.g., addons-for-elementor-builder.php)
        add_action('wp_enqueue_scripts', function() {
            // Frontend form scripts
            wp_enqueue_script(
                    'afeb-form-frontend',
                    $this->assets->assets_url('js/frontend-form.js'),
                    ['jquery'],
                    true
            );

            // Localize with required data
            wp_localize_script('afeb-form-frontend', 'afeb_ajax', [
                    'ajax_url'   => admin_url('admin-ajax.php'),
                    'nonce'      => wp_create_nonce('afeb_form_nonce'),
                    'max_upload' => wp_max_upload_size(),
                    'text'       => [
                            'file_too_large' => __('File exceeds maximum size', 'addons-for-elementor-builder'),
                            'invalid_type'   => __('Invalid file type', 'addons-for-elementor-builder')
                    ]
            ]);
        });

    }

    /**
     * Get widget name
     *
     * @since 1.4.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_form_builder';
    }

    /**
     * Get widget title
     *
     * @since 1.4.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html('Form Builder', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.4.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-form-builder';
    }

    /**
     * Get widget categories
     *
     * @since 1.4.0
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['afeb_basic'];
    }

    /**
     * Get widget keywords
     *
     * @since 1.4.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return [];
    }

    /**
     * Retrieve the list of style dependencies the widget requires
     *
     * @since 1.4.0
     *
     * @return array Widget style dependencies
     */
    public function get_style_depends(): array
    {
        return ['afeb-form-builder-style'];
    }

    /**
     * Retrieve the list of script dependencies the widget requires
     *
     * @since 1.4.0
     *
     * @return array Widget script dependencies
     */
    public function get_script_depends()
    {
        return ['afeb-form-builder-script'];
    }

    public function before_start_repeater() {}

    public function file_fields_options() {}

    public function recaptcha_v3_fields_options() {}

    public function steps_fields_options() {}

    public function register_step_content_section() {}

    public function register_step_style_section() {}

    /**
     * Register FormBuilder widget controls
     *
     * @since 1.4.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('form_content_section', [
            'label' => esc_html__('Form', 'addons-for-elementor-builder')
        ], function () {
            $repeater = new Repeater();
            $repeater_controls = new CHelper($repeater);
            $GLOBALS['afeb_repeater_controls'] = $repeater_controls;

            /**
             * Tabs
             */
            $repeater_controls->tabs('form_tabs', [
                'form_tab_content' => [
                    'label' => esc_html__('Content', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text('form_name', [
                            'label' => esc_html__('Form Name', 'addons-for-elementor-builder'),
                            'placeholder' => esc_html__('Form Name', 'addons-for-elementor-builder'),
                            'ai' => ['active' => false,],
                            'dynamic' => ['active' => false,],
                        ]);

                        $this->controls->text('form_id', [
                            'label' => esc_html__('Form ID', 'addons-for-elementor-builder'),
                            'ai' => ['active' => false,],
                            'placeholder' => 'new_form_id',
                            'description' => sprintf(
                                esc_html__('Please make sure the ID is unique and not used elsewhere on the page. This field allows %1$sA-z 0-9%2$s & underscore chars without spaces.', 'addons-for-elementor-builder'),
                                '<code>',
                                '</code>'
                            ),
                        ]);

                        $this->before_start_repeater($this->controls);

                        $repeater_controls = $GLOBALS['afeb_repeater_controls'];
                        $repeater_controls->select('field_type', [
                            'label' => esc_html__('Type', 'addons-for-elementor-builder'),
                            'meta_options' => [
                                $this->get_name(),
                                [
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
                                    'pv_upload' => esc_html__('File Upload', 'addons-for-elementor-builder'),
                                    'password' => esc_html__('Password', 'addons-for-elementor-builder'),
                                    'html' => esc_html__('HTML', 'addons-for-elementor-builder'),
                                    'pv_recaptcha_v3' => esc_html__('reCAPTCHA V3', 'addons-for-elementor-builder'),
                                    'hidden' => esc_html__('Hidden', 'addons-for-elementor-builder'),
                                    'pv_step' => esc_html__('Step', 'addons-for-elementor-builder'),
                                ]
                            ],
                            'default' => 'text',
                        ]);

                        $repeater_controls->text('field_label', [
                            'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                            'label_block' => true,
                            'default' => '',
                        ]);

                        $repeater_controls->text('placeholder', [
                            'label' => esc_html__('Placeholder', 'addons-for-elementor-builder'),
                            'label_block' => true,
                            'default' => '',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'field_type',
                                        'operator' => 'in',
                                        'value' => [
                                            'tel',
                                            'text',
                                            'email',
                                            'textarea',
                                            'number',
                                            'url',
                                            'password',
                                        ],
                                    ],
                                ],
                            ],
                        ]);

                        $repeater_controls->sh_switcher('required', [
                            'label' => esc_html__('Required', 'addons-for-elementor-builder'),
                            'return_value' => 'true',
                            'default' => '',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'field_type',
                                        'operator' => '!in',
                                        'value' => ['recaptcha_v3', 'hidden', 'html', 'step',],
                                    ],
                                ],
                            ],
                        ]);

                        $repeater_controls->text_area('field_options', [
                            'label' => esc_html__('Options', 'addons-for-elementor-builder'),
                            'default' => '',
                            'description' => esc_html__('Insert options in separate lines. For different label/values separate them with a pipe char ("|"). Like: First Option|f_option', 'addons-for-elementor-builder'),
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'field_type',
                                        'operator' => 'in',
                                        'value' => ['select', 'checkbox', 'radio',],
                                    ],
                                ],
                            ],
                        ]);

                        $repeater_controls->yn_switcher('inline_list', [
                            'label' => esc_html__('Inline List', 'addons-for-elementor-builder'),
                            'return_value' => 'afeb-inline-sub-group',
                            'default' => '',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'field_type',
                                        'operator' => 'in',
                                        'value' => ['checkbox', 'radio',],
                                    ],
                                ],
                            ],
                        ]);

                        $this->file_fields_options();

                        $repeater_controls->text_area('field_html', [
                            'label' => esc_html__('HTML', 'addons-for-elementor-builder'),
                            'default' => '',
                            'dynamic' => ['active' => true,],
                            'conditions' => [
                                'terms' => [
                                    ['name' => 'field_type', 'value' => 'html',],
                                ],
                            ],
                        ]);

                        $this->recaptcha_v3_fields_options();
                        $this->steps_fields_options();

                        $repeater_controls->responsive()->slider('width', [
                            'label' => esc_html__('Column Width (%)', 'addons-for-elementor-builder'),
                            'size_units' => ['%'],
                            'range' => ['%' => ['min' => 10, 'max' => 100,],],
                            'default' => ['unit' => '%', 'size' => 100,],
                            'selectors' => ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}%;',],
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'field_type',
                                        'operator' => '!in',
                                        'value' => ['hidden', 'recaptcha_v3', 'step',],
                                    ],
                                ],
                            ],
                        ]);

                        $repeater_controls->number('rows', [
                            'label' => esc_html__('Rows', 'addons-for-elementor-builder'),
                            'default' => 4,
                            'conditions' => ['terms' => [['name' => 'field_type', 'value' => 'textarea',],],],
                        ]);

                        $repeater_controls->hidden('css_classes', [
                            'label' => esc_html__('CSS Classes', 'addons-for-elementor-builder'),
                            'default' => '',
                            'title' => esc_html__('Add your custom class WITHOUT the dot. e.g: my-class', 'addons-for-elementor-builder'),
                        ]);
                    },
                ],
                'form_fields_tab_advanced' => [
                    'label' => esc_html__('Advanced', 'addons-for-elementor-builder'),
                    'condition' => [
                        'field_type!' => 'html',
                    ],
                    'callback' => function () {
                        $repeater_controls = $GLOBALS['afeb_repeater_controls'];
                        $repeater_controls->text('field_value', [
                            'label' => esc_html__('Default Value', 'addons-for-elementor-builder'),
                            'label_block' => true,
                            'default' => '',
                            'ai' => ['active' => false,],
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'field_type',
                                        'operator' => 'in',
                                        'value' => ['text', 'email', 'textarea', 'url', 'tel', 'radio', 'select', 'number', 'date', 'time', 'hidden',],
                                    ],
                                ],
                            ],
                        ]);

                        $repeater_controls->text('field_id', [
                            'label' => esc_html__('ID', 'addons-for-elementor-builder'),
                            'label_block' => true,
                            'description' => esc_html__('Element ID should be unique and not used elsewhere in this widget.', 'addons-for-elementor-builder'),
                            'render_type' => 'none',
                            'required' => true,
                            'ai' => ['active' => false,],
                        ]);

                        $shortcode_value = '{{ view.container.settings.get( \'field_id\' ) }}';

                        $repeater_controls->raw_html('shortcode', [
                            'label' => esc_html__('Shortcode', 'addons-for-elementor-builder'),
                            'classes' => 'forms-field-shortcode',
                            'raw' => '<input class="afeb-form-field-shortcode" value=\'[id="' . $shortcode_value . '"]\' readonly />',
                        ]);
                    },
                ],
            ]);

            $this->controls->repeater('form_fields', [
                'label' => esc_html__('Items', 'addons-for-elementor-builder'),
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'field_type' => 'text',
                        'field_label' => esc_html__('Name', 'addons-for-elementor-builder'),
                        'placeholder' => esc_html__('Name', 'addons-for-elementor-builder'),
                        'width' => '100',
                        'dynamic' => ['active' => true,],
                    ],
                    [
                        'field_type' => 'email',
                        'required' => 'true',
                        'field_label' => esc_html__('Email', 'addons-for-elementor-builder'),
                        'placeholder' => esc_html__('Email', 'addons-for-elementor-builder'),
                        'width' => '100',
                    ],
                    [
                        'field_type' => 'textarea',
                        'field_label' => esc_html__('Message', 'addons-for-elementor-builder'),
                        'placeholder' => esc_html__('Message', 'addons-for-elementor-builder'),
                        'width' => '100',
                    ],
                ],
                'title_field' => '{{{ field_label }}}',
                'separator' => 'before',
            ]);

            $this->controls->sh_switcher('show_labels', [
                'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                'return_value' => 'true',
                'default' => 'true',
                'separator' => 'before',
            ]);

            $this->controls->sh_switcher('mark_required', [
                'label' => esc_html__('Required Mark', 'addons-for-elementor-builder'),
                'default' => '',
                'condition' => [
                    'show_labels!' => '',
                ],
            ]);

            $this->controls->sh_switcher('show_placeholders', [
                'label' => esc_html__('Show Placeholders', 'addons-for-elementor-builder'),
                'return_value' => 'true',
                'default' => 'true',
            ]);

            $this->controls->hidden('label_position', [
                'label' => esc_html__('Label Position', 'addons-for-elementor-builder'),
                'options' => [
                    'above' => esc_html__('Above', 'addons-for-elementor-builder'),
                    'inline' => esc_html__('Inline', 'addons-for-elementor-builder'),
                ],
                'default' => 'above',
                'condition' => ['show_labels!' => '',],
            ]);
        });
        /**
         *
         * Buttons
         *
         */
        $this->controls->tab_content_section('buttons_content_section', [
            'label' => esc_html__('Buttons', 'addons-for-elementor-builder')
        ], function () {
            $this->controls->responsive()->slider('button_width', [
                'label' => esc_html__('Width (%)', 'addons-for-elementor-builder'),
                'size_units' => ['%'],
                'range' => ['%' => ['min' => 10, 'max' => 100,],],
                'default' => ['unit' => '%', 'size' => 100,],
                'selectors' => [
                    '{{WRAPPER}} .afeb-field-group.afeb-form-field-type-submit' => 'width: {{SIZE}}%;',
                    // '{{WRAPPER}} .afeb-stp-btns-wrap' => 'width: {{SIZE}}%;'
                ],
                'frontend_available' => true,
            ]);

            $this->controls->choose('button_align', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors_dictionary' => [
                    'left' => 'margin-left: 0; margin-right: auto;',
                    'center' => 'margin-left: auto; margin-right: auto;',
                    'right' => 'margin-left: auto; margin-right: 0;'
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-field-group.afeb-form-field-type-submit>button' => '{{VALUE}}',
                    // '{{WRAPPER}} .afeb-stp-btns-wrap' => '{{VALUE}}',
                    // '{{WRAPPER}} .afeb-step-tab:first-of-type .afeb-step-next' => '{{VALUE}}',
                ],
            ]);

            $this->controls->heading('heading_submit_button', [
                'label' => esc_html__('Submit Button', 'addons-for-elementor-builder'),
            ]);

            $this->controls->text('button_text', [
                'label' => esc_html__('Submit', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'default' => esc_html__('Send', 'addons-for-elementor-builder'),
                'placeholder' => esc_html__('Send', 'addons-for-elementor-builder'),
            ]);

            $this->controls->icons('selected_button_icon', [
                'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                'skin' => 'inline',
                'label_block' => false,
            ]);

            $start = is_rtl() ? 'right' : 'left';
            $end = is_rtl() ? 'left' : 'right';

            $this->controls->choose('button_icon_align', [
                'label' => esc_html__('Icon Position', 'addons-for-elementor-builder'),
                'default' => is_rtl() ? 'left' : 'right',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'addons-for-elementor-builder'),
                        'icon' => "eicon-h-align-$start",
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'addons-for-elementor-builder'),
                        'icon' => "eicon-h-align-$end",
                    ],
                ],
                'condition' => [
                    'selected_button_icon[value]!' => '',
                ],
            ]);

            $this->controls->responsive()->slider('button_icon_indent', [
                'label' => esc_html__('Icon Spacing', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'condition' => [
                    'selected_button_icon[value]!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-field-button .afeb-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .afeb-form-field-button .afeb-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}}',
                ],
            ]);

            $this->controls->text('button_css_id', [
                'label' => esc_html__('Button ID', 'addons-for-elementor-builder'),
                'default' => '',
                'ai' => ['active' => false,],
                'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'addons-for-elementor-builder'),
                'description' => esc_html__('Element ID should be unique and not used elsewhere in this widget.', 'addons-for-elementor-builder'),
                'separator' => 'before',
            ]);
        });

        /**
         *
         * Actions
         *
         */
        $this->controls->tab_content_section('actions_content_section', [
            'label' => esc_html__('Actions', 'addons-for-elementor-builder')
        ], function () {
            $default_submit_actions = ['submission'];

            $this->controls->select2('submit_actions', [
                'label' => esc_html__('Actions After Submit', 'addons-for-elementor-builder'),
                'multiple' => true,
                'options' => [
                    'submission' => esc_html__('Submission', 'addons-for-elementor-builder'),
                    'email' => esc_html__('Email', 'addons-for-elementor-builder'),
                    'redirect' => esc_html__('Redirect', 'addons-for-elementor-builder'),
                ],
                'render_type' => 'none',
                'label_block' => true,
                'default' => $default_submit_actions,
                'description' => esc_html__('Select actions to be executed following a user\'s form submission (e.g., send an email notification). Upon choosing an action, its settings will appear below.', 'addons-for-elementor-builder'),
            ]);
        });

        /**
         *
         * Email
         *
         */
        $this->controls->tab_content_section('email_content_section', [
            'label' => esc_html__('Email', 'addons-for-elementor-builder'),
            'condition' => [
                'submit_actions' => 'email',
            ],
        ], function () {
            $this->controls->text('email_to', [
                'label' => esc_html__('To', 'addons-for-elementor-builder'),
                'default' => get_option('admin_email'),
                'ai' => ['active' => false,],
                'label_block' => true,
                'title' => esc_html__('Separate emails with commas', 'addons-for-elementor-builder'),
                'render_type' => 'none',
            ]);

            // maybe esc_html not necessary
            /* translators: %s: Site title. */
            $default_message = sprintf(esc_html__('New message from %s', 'addons-for-elementor-builder'), get_option('blogname'));

            $this->controls->text('email_subject', [
                'label' => esc_html__('Subject', 'addons-for-elementor-builder'),
                'default' => $default_message,
                'ai' => ['active' => false,],
                'placeholder' => $default_message,
                'label_block' => true,
                'render_type' => 'none',
            ]);

            $this->controls->text_area('email_content', [
                'label' => esc_html__('Message', 'addons-for-elementor-builder'),
                'default' => '[all-fields]',
                'ai' => ['active' => false,],
                'placeholder' => '[all-fields]',
                'description' => sprintf(
                    esc_html__('By default, form sends all fields. To modify this behaviour, copy the shortcode you wish from fields and paste it instead of %s.', 'addons-for-elementor-builder'),
                    '<code>[all-fields]</code>'
                ),
                'render_type' => 'none',
            ]);

            $site_domain = str_ireplace('www.', '', parse_url(home_url(), PHP_URL_HOST));

            $this->controls->text('email_from', [
                'label' => esc_html__('From Email', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'description' => esc_html__('Shortcode like [id="email"] can be inserted according ID of the associated mail field.', 'addons-for-elementor-builder'),
                'default' => 'email@' . $site_domain,
                'render_type' => 'none',
            ]);

            $this->controls->text('email_from_name', [
                'label' => esc_html__('From Name', 'addons-for-elementor-builder'),
                'default' => get_bloginfo('name'),
                'ai' => ['active' => false,],
                'render_type' => 'none',
            ]);

            $this->controls->text('email_reply_to', [
                'label' => esc_html__('Reply To', 'addons-for-elementor-builder'),
                'default' => 'email@' . $site_domain,
                'ai' => ['active' => false,],
                'render_type' => 'none'
            ]);

            $this->controls->text('email_to_cc', [
                'label' => esc_html__('Cc', 'addons-for-elementor-builder'),
                'default' => '',
                'ai' => ['active' => false,],
                'title' => esc_html__('Separate emails with commas', 'addons-for-elementor-builder'),
                'render_type' => 'none',
            ]);

            $this->controls->text('email_to_bcc', [
                'label' => esc_html__('Bcc', 'addons-for-elementor-builder'),
                'default' => '',
                'ai' => ['active' => false,],
                'title' => esc_html__('Separate emails with commas', 'addons-for-elementor-builder'),
                'render_type' => 'none',
            ]);

            $this->controls->select2('form_metadata', [
                'label' => esc_html__('Meta Data', 'addons-for-elementor-builder'),
                'multiple' => true,
                'label_block' => true,
                'separator' => 'before',
                'default' => ['date', 'time', 'credit'],
                'options' => [
                    'date' => esc_html__('Date', 'addons-for-elementor-builder'),
                    'time' => esc_html__('Time', 'addons-for-elementor-builder'),
                    'page_url' => esc_html__('Page URL', 'addons-for-elementor-builder'),
                    'page_title' => esc_html__('Page Title', 'addons-for-elementor-builder'),
                    'user_agent' => esc_html__('User Agent', 'addons-for-elementor-builder'),
                    'remote_ip' => esc_html__('Remote IP', 'addons-for-elementor-builder'),
                    'credit' => esc_html__('Credit', 'addons-for-elementor-builder'),
                ],
                'render_type' => 'none',
            ]);

            $this->controls->select('email_content_type', [
                'label' => esc_html__('Send As', 'addons-for-elementor-builder'),
                'default' => 'html',
                'ai' => ['active' => false,],
                'render_type' => 'none',
                'options' => [
                    'html' => esc_html__('HTML', 'addons-for-elementor-builder'),
                    'plain' => esc_html__('Plain', 'addons-for-elementor-builder'),
                ],
            ]);
        });

        /**
         *
         * Redirect
         *
         */
        $this->controls->tab_content_section('redirect_content_section', [
            'label' => esc_html__('Redirect', 'addons-for-elementor-builder'),
            'condition' => [
                'submit_actions' => 'redirect',
            ],
        ], function () {
            $this->controls->text('redirect_to', [
                'label' => esc_html__('Redirect To', 'addons-for-elementor-builder'),
                'placeholder' => esc_html__('https://your-link.com', 'addons-for-elementor-builder'),
                'default' => '',
                'ai' => ['active' => false,],
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::TEXT_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'render_type' => 'none',
            ]);
        });

        $this->register_step_content_section();

        /**
         *
         * Messages
         *
         */
        $this->controls->tab_content_section('messages_content_section', [
            'label' => esc_html__('Validation Messages', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->heading('error_messages_heading', [
                'label' => esc_html__('Error Messages', 'addons-for-elementor-builder')
            ]);

            $this->controls->sh_switcher('error_message_box_preview', [
                'label' => esc_html__('Preview In Editor', 'addons-for-elementor-builder'),
                'return_value' => 'true',
                'default' => 'true',
                'separator' => 'before',
            ]);

            $this->controls->tabs('afeb_icon_tab_control', [
                'form_builder_error_message_email_tab' => [
                    'label' => esc_html__('Email', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $default = esc_html__('Please use a valid email', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message_email', [
                            'label' => esc_html__('Invalid Email', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'rows' => 10,
                        ]);
                    },
                ],
                'form_builder_error_message_url_tab' => [
                    'label' => esc_html__('URL', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $default = esc_html__('Please use a valid url', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message_url', [
                            'label' => esc_html__('Invalid URL', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'rows' => 10,
                        ]);
                    },
                ],
                'form_builder_error_message_number_tab' => [
                    'label' => esc_html__('Number', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $default = esc_html__('Please use a valid number', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message_number', [
                            'label' => esc_html__('Invalid Number', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'rows' => 10,
                        ]);
                    },
                ],
                'form_builder_error_message_tel_tab' => [
                    'label' => esc_html__('Tel', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $default = esc_html__('Please use a valid tel', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message_tel', [
                            'label' => esc_html__('Invalid Tel', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'rows' => 10,
                        ]);
                    },
                ],
                'form_builder_error_message_date_tab' => [
                    'label' => esc_html__('Date', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $default = esc_html__('Please use a valid date', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message_date', [
                            'label' => esc_html__('Invalid Date', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'rows' => 10,
                        ]);
                    },
                ],
                'form_builder_error_message_time_tab' => [
                    'label' => esc_html__('Time', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $default = esc_html__('Please use a valid time', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message_time', [
                            'label' => esc_html__('Invalid Time', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'rows' => 10,
                        ]);
                    },
                ],
                'form_builder_error_message_more_tab' => [
                    'label' => esc_html__('More', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $default = esc_html__('Please fill out this field', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message_empty_field', [
                            'label' => esc_html__('Empty Field', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'rows' => 10,
                        ]);

                        $default = esc_html__('The operation was unsuccessful.', 'addons-for-elementor-builder');
                        $this->controls->text_area('error_message', [
                            'label' => esc_html__('Failure Message', 'addons-for-elementor-builder'),
                            'default' => $default,
                            'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default,
                            'ai' => ['active' => false,],
                            'dynamic' => ['active' => false,],
                        ]);

                    },
                ],
            ]);

            $this->controls->heading('success_messages_heading', [
                'label' => esc_html__('Success Messages', 'addons-for-elementor-builder')
            ]);

            $this->controls->sh_switcher('success_message_box_preview', [
                'label' => esc_html__('Preview In Editor', 'addons-for-elementor-builder'),
                'return_value' => 'true',
                'default' => 'true',
                'separator' => 'before',
            ]);

            $this->controls->text_area('success_message', [
                'label' => esc_html__('Success Message', 'addons-for-elementor-builder'),
                'default' => esc_html__('The operation was successful.', 'addons-for-elementor-builder'),
                'placeholder' => esc_html__('Eg. The operation was successful.', 'addons-for-elementor-builder'),
                'ai' => ['active' => false,],
                'dynamic' => ['active' => false,],
            ]);
            // 🆕 Add File Upload Error Messages
            $default_type = esc_html__('File type is not supported.', 'addons-for-elementor-builder');
            $this->controls->text_area('error_message_file_type', [
                    'label' => esc_html__('File Type Not Supported', 'addons-for-elementor-builder'),
                    'default' => $default_type,
                    'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default_type,
                    'rows' => 3,
            ]);

            $default_size = esc_html__('File size exceeds the allowed limit.', 'addons-for-elementor-builder');
            $this->controls->text_area('error_message_file_size', [
                    'label' => esc_html__('File Size Not Allowed', 'addons-for-elementor-builder'),
                    'default' => $default_size,
                    'placeholder' => esc_html__('Eg.', 'addons-for-elementor-builder') . $default_size,
                    'rows' => 3,
            ]);
        });

        do_action('afeb/widget/content/after_render_content_section', $this);

        /**
         *
         * Form Labels
         *
         */
        $this->controls->tab_style_section('form_labels_style_section', [
            'label' => esc_html__('Form Labels', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->text_color('form_labels_color', [
                'selectors' => ['{{WRAPPER}} .afeb-field-group > label' => 'color: {{VALUE}}; fill: {{VALUE}};',],
                'global' => ['default' => '',]
            ]);

            $this->controls->typography([
                'name' => 'form_labels_typography',
                'selector' => '{{WRAPPER}} .afeb-field-group > label',
                'global' => ['default' => '',]
            ]);

            $this->controls->responsive()->margin('form_labels_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-field-group > label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->padding('form_labels_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-field-group > label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);
        });

        /**
         *
         * Required Mark
         *
         */
        $this->controls->tab_style_section('required_mark_style_section', [
            'label' => esc_html__('Required Mark', 'addons-for-elementor-builder'),
            'condition' => [
                'mark_required' => 'yes'
            ]
        ], function () {
            $this->controls->text_color('required_mark_color', [
                'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                'selectors' => ['{{WRAPPER}} .afeb-mark-required .afeb-form-field-label:after' => 'color: {{VALUE}}; fill: {{VALUE}};',],
                'global' => ['default' => '',]
            ]);

            $this->controls->typography([
                'name' => 'required_mark_typography',
                'selector' => '{{WRAPPER}} .afeb-mark-required .afeb-form-field-label:after',
                'global' => ['default' => '',]
            ]);

            $this->controls->responsive()->slider('required_mark_gap', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'custom'],
                'range' => ['px' => ['max' => 100,],],
                'selectors' => ['{{WRAPPER}} .afeb-mark-required .afeb-form-field-label:after' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',],
            ]);
        });

        /**
         *
         * Form Field
         *
         */
        $this->controls->tab_style_section('field_style_section', [
            'label' => esc_html__('Form Field', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->responsive()->slider('field_row_gap', [
                'label' => esc_html__('Row Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'custom'],
                'range' => [
                    'px' => ['max' => 250,],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->slider('field_column_gap', [
                'label' => esc_html__('Column Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'custom'],
                'range' => [
                    'px' => ['max' => 100,],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-fields-wrap, {{WRAPPER}} .afeb-form-builder-message-group' => 'margin-left: calc(-{{SIZE}}{{UNIT}} / 2);margin-right: calc(-{{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .afeb-field-group' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2);padding-right: calc({{SIZE}}{{UNIT}} / 2);',
                ],
            ]);

            $this->controls->tabs('field_style_tab', [
                'field_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->text_color('field_background_color', [
                            'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group:not(.afeb-form-field-type-upload) .afeb-form-field:not(.afeb-select-wrap)' => 'background-color: {{VALUE}} !important',
                                '{{WRAPPER}} .afeb-field-group .afeb-select-wrap select' => 'background-color: {{VALUE}} !important',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->text_color('field_text_color', [
                            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field select' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field svg' => 'fill: {{VALUE}}',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->text_color('field_placeholder_color', [
                            'label' => esc_html__('Placeholder', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field::placeholder' => 'color: {{VALUE}}',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->border([
                            'name' => 'field_border',
                            'selector' => '{{WRAPPER}} .afeb-form-field-textual',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('field_border_radius', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-form-field-textual' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
                'field_style_focus' => [
                    'label' => esc_html__('Focus', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->text_color('field_background_color_focus', [
                            'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group:not(.afeb-form-field-type-upload) .afeb-form-field:not(.afeb-select-wrap):focus' => 'background-color: {{VALUE}} !important',
                                '{{WRAPPER}} .afeb-field-group .afeb-select-wrap select:focus' => 'background-color: {{VALUE}} !important',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->text_color('field_text_color_focus', [
                            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field:focus' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field select:focus' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field:focus svg' => 'fill: {{VALUE}}',
                                '{{WRAPPER}} .afeb-field-group input[type="radio"]:focus + label' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-field-group input[type="checkbox"]:focus + label' => 'color: {{VALUE}}'
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->text_color('field_placeholder_color_focus', [
                            'label' => esc_html__('Placeholder', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field:focus::placeholder' => 'color: {{VALUE}}',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->border([
                            'name' => 'field_border_focus',
                            'selector' => '{{WRAPPER}} .afeb-form-field-textual:focus',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('field_border_radius_focus', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-form-field-textual:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
                'field_style_error' => [
                    'label' => esc_html__('Error', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->text_color('field_background_color_error', [
                            'label' => esc_html__('Background Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group:not(.afeb-form-field-type-upload) .afeb-form-field:not(.afeb-select-wrap).afeb-form-error' => 'background-color: {{VALUE}};',
                                '{{WRAPPER}} .afeb-field-group .afeb-select-wrap select.afeb-form-error' => 'background-color: {{VALUE}};',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->text_color('field_text_color_error', [
                            'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field.afeb-form-error' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .afeb-field-group input[type="radio"].afeb-form-error' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .afeb-field-group input[type="checkbox"].afeb-form-error' => 'color: {{VALUE}};',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->text_color('field_placeholder_color_error', [
                            'label' => esc_html__('Placeholder', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-field-group .afeb-form-field.afeb-form-error::placeholder' => 'color: {{VALUE}}',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->text_color('field_text_color_error_2', [
                            'label' => esc_html__('Error Text Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-submit-error' => 'color: {{VALUE}};',
                            ],
                            'global' => ['default' => '',]
                        ]);

                        $this->controls->border([
                            'name' => 'field_border_error',
                            'selector' => '{{WRAPPER}} .afeb-form-field-textual.afeb-form-error',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('field_border_radius_error', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-form-field-textual.afeb-form-error' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('separator_field_style_controls');

            $this->controls->typography([
                'name' => 'field_typography',
                'selector' => '{{WRAPPER}} .afeb-field-group .afeb-form-field',
                'global' => ['default' => '',]
            ]);

            $this->controls->responsive()->margin('field_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('field_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-field-group:not(.afeb-form-field-type-upload) .afeb-form-field:not(.afeb-select-wrap)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .afeb-field-group .afeb-select-wrap select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .afeb-field-group input[type="date"]::before' => 'right: {{RIGHT}}{{UNIT}};',
                    '{{WRAPPER}} .afeb-field-group input[type="time"]::before' => 'right: {{RIGHT}}{{UNIT}};',
                ],
            ]);
        });

        /**
         *
         * Checkbox & Radio
         *
         */
        $this->controls->tab_style_section('radion_checkbox_style_section', [
            'label' => esc_html__('Checkbox & Radio', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->text_color('radion_checkbox_color', [
                'selectors' => ['{{WRAPPER}} .afeb-field-sub-group label' => 'color: {{VALUE}}',],
                'global' => ['default' => '',]
            ]);

            $this->controls->typography([
                'name' => 'radion_checkbox_typography',
                'selector' => '{{WRAPPER}} .afeb-field-sub-group label',
                'global' => ['default' => '',]
            ]);

            $this->controls->responsive()->margin('radion_checkbox_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-form-field-option' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->responsive()->slider('radion_checkbox_gutter', [
                'label' => esc_html__('Inner Gutter', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 50,],],
                'default' => ['unit' => 'px', 'size' => 5,],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-field-option label' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.afeb-custom-styles-yes .afeb-form-field-option label:before' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]);
        });
        /**
         *
         * Button
         *
         */
        $this->controls->tab_style_section('button_style_section', [
            'label' => esc_html__('Button', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->tabs('button_style_tab', [
                'button_style_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->background([
                            'name' => 'button_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-form-field-button',
                        ]);

                        $this->controls->text_color('button_color', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-form-field-type-submit .afeb-form-field-button>span>span' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);
                    },
                ],
                'button_style_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {
                        $this->controls->background([
                            'name' => 'button_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-form-field-button:hover',
                        ]);

                        $this->controls->text_color('button_color_hover', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-form-field-type-submit .afeb-form-field-button:hover>span>span' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('separator_button_style_section');

            $this->controls->typography([
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .afeb-form-field-button',
            ]);

            $this->controls->responsive()->margin('button_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-field-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('button_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-field-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .afeb-form-field-button',
            ]);

            $this->controls->responsive()->border_radius('button_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-field-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'button_box_shadow',
                'exclude' => ['box_shadow_position',],
                'selector' => '{{WRAPPER}} .afeb-form-field-button',
            ]);
        });
        $this->register_step_style_section();
        /**
         *
         * Error Message Box
         *
         */
        $this->controls->tab_style_section('error_message_box_style_section', [
            'label' => esc_html__('Error Message Box', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->raw_html('error_message_box_description', [
                'raw' => esc_html__('For a better view of the changes. You can enable the error message box preview mode from Content Tab » Validation Messages Section » Error Messages » Preview In Editor', 'addons-for-elementor-builder'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => ['error_message_box_preview!' => 'yes']
            ]);

            $this->controls->background([
                'name' => 'error_message_box_background',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-form-builder-error-box',
            ]);

            $this->controls->text_color('error_message_box_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-builder-error-box' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]);

            $this->controls->typography([
                'name' => 'error_message_box_typography',
                'selector' => '{{WRAPPER}} .afeb-form-builder-error-box',
            ]);

            $this->controls->responsive()->margin('error_message_box_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-builder-error-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('error_message_box_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-builder-error-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'error_message_box_border',
                'selector' => '{{WRAPPER}} .afeb-form-builder-error-box',
            ]);

            $this->controls->responsive()->border_radius('error_message_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-builder-error-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'error_message_box_shadow',
                'exclude' => ['box_shadow_position',],
                'selector' => '{{WRAPPER}} .afeb-form-builder-error-box',
            ]);
        });

        /**
         *
         * Success Message Box
         *
         */
        $this->controls->tab_style_section('success_message_box_style_section', [
            'label' => esc_html__('Success Message Box', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->raw_html('success_message_box_description', [
                'raw' => esc_html__('For a better view of the changes. You can enable the success message box preview mode from Content Tab » Validation Messages Section » Success Messages » Preview In Editor', 'addons-for-elementor-builder'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => ['success_message_box_preview!' => 'yes']
            ]);

            $this->controls->background([
                'name' => 'success_message_box_background',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-form-builder-success-box',
            ]);

            $this->controls->text_color('success_message_box_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-builder-success-box' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]);

            $this->controls->typography([
                'name' => 'success_message_box_typography',
                'selector' => '{{WRAPPER}} .afeb-form-builder-success-box',
            ]);

            $this->controls->responsive()->margin('success_message_box_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-builder-success-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('success_message_box_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-form-builder-success-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'success_message_box_border',
                'selector' => '{{WRAPPER}} .afeb-form-builder-success-box',
            ]);

            $this->controls->responsive()->border_radius('success_message_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => ['{{WRAPPER}} .afeb-form-builder-success-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',],
            ]);

            $this->controls->box_shadow([
                'name' => 'success_message_box_shadow',
                'exclude' => ['box_shadow_position',],
                'selector' => '{{WRAPPER}} .afeb-form-builder-success-box',
            ]);
            $this->controls->hidden('field_order', [
                    'default' => function() {
                        $settings = $this->get_settings_for_display();
                        return isset($settings['form_fields'])
                                ? wp_json_encode(array_map(function($field) {
                                    return $field['_id']; // Use _id as the unique identifier
                                }, $settings['form_fields']))
                                : '[]';
                    },
                    'render_type' => 'none'
            ]);
        });
        // In register_controls() method, add:

    }

    public function before_render()
    {
        parent::before_render();
        $settings = $this->get_settings_for_display();
        $render_settings = Widgets::render_settings($settings, explode(',', $this->keys));

        update_post_meta(get_the_ID(), '_elementor_settings_for_preview_' . $this->get_id(), $render_settings);
    }

    /**
     * Render FormBuilder widget output on the frontend
     *
     * @since 1.4.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $render_settings = [];
        $render_settings = Widgets::render_settings($settings, explode(',', $this->keys));

        update_post_meta(get_the_ID(), '_elementor_settings_for_editor_' . $this->get_id(), $render_settings);

        $this->add_render_attribute(
            [
                'wrapper' => [
                    'class' => [
                        'afeb-form-fields-wrap',
                        'afeb-labels-' . $settings['label_position'],
                    ],
                ],
                'submit-group' => [
                    'class' => [
                        'afeb-field-group',
                        'afeb-column',
                        'afeb-form-field-type-submit',
                    ],
                ],
                'button' => [
                    'class' => 'afeb-form-field-button',
                    'data-text' => !empty($settings['button_text']) ? esc_attr($settings['button_text']) : esc_html__('Send', 'addons-for-elementor-builder'),
                    'data-loading-text' => !empty($settings['button_loading_text']) ? esc_attr($settings['button_loading_text']) : esc_html__('Please wait ...', 'addons-for-elementor-builder'),
                ],
                'icon-align' => [
                    'class' => [
                        empty($settings['button_icon_align']) ? '' :
                            'afeb-align-icon-' . $settings['button_icon_align'],
                        'elementor-button-icon',
                    ],
                ],
            ]
        );

        if (!empty($settings['form_id'])) {
            $this->add_render_attribute('form', 'id', $settings['form_id']);
        }

        if (!empty($settings['form_name'])) {
            $this->add_render_attribute('form', 'name', $settings['form_name']);
        }

        $this->add_render_attribute('form', 'page', get_post()->post_title);
        $this->add_render_attribute('form', 'page_id', get_post()->ID);

        if (!empty($settings['ajax_form'])) {
            $this->add_render_attribute('form', 'data-ajax-form', 'yes');
        }

        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', $settings['button_css_id']);
        }

        $referer_title = trim(wp_title('', false));

        if (is_home() && !$referer_title) {
            $referer_title = get_option('blogname');
        }

        $empty_fields = !empty($settings['error_message_empty_field']) ? $settings['error_message_empty_field'] : esc_html__('Please fill out this field', 'addons-for-elementor-builder');

        ?>
        <form class="afeb-form" method="post" <?php echo $this->get_render_attribute_string('form'); ?> novalidate>
            <input class="afeb-form-error-message" type="hidden" name="file_type_error_message"
                   value="<?php echo esc_attr(!empty($settings['error_message_file_type'])
                           ? $settings['error_message_file_type']
                           : esc_html__('File type is not supported.', 'addons-for-elementor-builder')); ?>" />

            <input class="afeb-form-error-message" type="hidden" name="file_size_error_message"
                   value="<?php echo esc_attr(!empty($settings['error_message_file_size'])
                           ? $settings['error_message_file_size']
                           : esc_html__('File size exceeds the allowed limit.', 'addons-for-elementor-builder')); ?>" />

            <input type="hidden" name="page_id"
                value="<?php
                        echo get_the_ID(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                        ?>" />
            <input type="hidden" name="form_id" value="<?php echo esc_attr($this->get_id()); ?>" />

            <?php if (!empty($settings['form_name'])) { ?>
                <input type="hidden" name="form_name" value="<?php echo esc_attr($settings['form_name']); ?>" />
            <?php } ?>

            <input class="afeb-form-error-message" type="hidden" name="empty_error_message" value="<?php echo esc_attr($empty_fields); ?>" />
            <?php
            $current_request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
            $current_referrer = !empty($current_request_uri) ? home_url($current_request_uri) : home_url();
            ?>
            <input type="hidden" name="referrer" value="<?php echo esc_url($current_referrer); ?>" />
            <input type="hidden" name="referer_title" value="<?php echo esc_attr($referer_title); ?>" />
            <?php wp_nonce_field('afeb_form_submit_action', 'afeb_form_submit_nonce'); ?>
            <input type="hidden" name="afeb_form_submit" value="" />

            <?php if (is_singular()) { ?>
                <input type="hidden" name="queried_id" value="<?php echo get_the_ID(); ?>" />
            <?php }
            $step_count1 = 0;
            $step_exists = '';
            $step_icon = [];
            $step_label = [];
            $step_sub_label = [];

            foreach ($settings['form_fields'] as $value) {
                if ($value['field_type'] == 'step') {
                    $step_exists = 'exists';
                    $step_count1++;

                    ob_start();
                    Icons_Manager::render_icon($value['step_icon'], ['aria-hidden' => 'true']);
                    $step_icon[] = ob_get_clean();
                    $step_label[] = sprintf(
                        '<span class="afeb-step-main-label">%s</span>',
                        esc_html($value['field_label'])
                    );
                    $step_sub_label[] = sprintf(
                        '<span class="afeb-step-sub-label">%s</span>',
                        esc_html($value['field_sub_label'])
                    );
                }
            }

            $step_wrap_class  = !empty($settings['show_separator']) ? 'afeb-step-wrap' : 'afeb-step-wrap afeb-separator-off';
            echo '<div class="' . $step_wrap_class . '">';

            if (!isset($settings['step_type'])) {
                $settings['step_type'] = 'none';
            }

            if ($settings['step_type'] == 'progress_bar') {
                echo '<div class="afeb-step-progress">';
                echo '<div class="afeb-step-progress-fill"></div>';
                echo '</div>';
            } else {
                $i = 0;

                while ($i < $step_count1):

                    if ($settings['step_type'] == 'none')
                        $step_html = '<span class="afeb-step"></span>';
                    else if ($settings['step_type'] == 'text')
                        $step_html = '<span class="afeb-step">' . $step_label[$i] . $step_sub_label[$i] . '</span>';
                    else if ($settings['step_type'] == 'icon')
                        $step_html = '<span class="afeb-step"><span class="afeb-step-content">' . $step_icon[$i] . '</span></span>';
                    else if ($settings['step_type'] == 'number')
                        $step_html = '<span class="afeb-step"><span class="afeb-step-content"><span class="afeb-step-number">' . ($i + 1) . '</span></span></span>';
                    else if ($settings['step_type'] == 'number_text')
                        $step_html = '<span class="afeb-step"><span class="afeb-step-content"><span class="afeb-step-number">' . ($i + 1) . '</span></span><span class="afeb-step-label">' . $step_label[$i] . $step_sub_label[$i] . '</span></span>';
                    else if ($settings['step_type'] == 'icon_text')
                        $step_html = '<span class="afeb-step"><span class="afeb-step-content">' . $step_icon[$i] . '</span><span class="afeb-step-label">' . $step_label[$i] . $step_sub_label[$i] . '</span></span>';

                    echo wp_kses_post($step_html);

                    if ($settings['show_separator'] == 'yes') {
                        echo '<span class="afeb-step-sep"></span>';
                    }

                    $i++;
                endwhile;
            }
            echo '</div>';
            ?>
            <div <?php $this->print_render_attribute_string('wrapper'); ?>>
                <?php
                $step_count = 0;
                $field_count = 0;
                $helper = new FormBuilderHelper();

                foreach ($settings['form_fields'] as $item_index => $item):
                    if ($item['field_type'] !== 'step') {
                        $field_count++;
                    }

                    $helper->form_fields_render_attributes($this, $item_index, $settings, $item);

                    $print_label = !in_array($item['field_type'], ['hidden', 'html', 'step'], true);
                    if ($item['field_type'] == 'step') {
                        if (isset($item['previous_button_text'])) {
                            $this->last_prev_btn_text = $item['previous_button_text'];
                        }

                        if ($step_count === 0) {
                            echo '<div class="afeb-step-tab afeb-step-tab-hidden">';
                        } else {
                            echo '<div class="afeb-stp-btns-wrap">';
                            $this->render_step_prev_button($item['previous_button_text']);
                            $this->render_step_next_button($item['next_button_text']);
                            echo '</div>';
                            echo '</div>';
                            echo '<div class="afeb-step-tab afeb-step-tab-hidden">';
                        }

                        $step_count++;
                    }
                ?>
                    <div <?php $this->print_render_attribute_string('field-group' . $item_index); ?>>
                        <?php
                        if (
                            $print_label &&
                            !empty($settings['show_labels']) &&
                            $item['field_type'] != 'recaptcha_v3' &&
                            ($item['field_label'] || $item['required'])
                        ) {
                        ?>
                            <label <?php echo $this->get_render_attribute_string('label' . $item_index); ?>>
                                <?php
                                // PHPCS - the variable $item['field_label'] is safe.
                                echo $item['field_label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                ?>
                            </label>
                        <?php
                        }

                        switch ($item['field_type']):
                            case 'html':
                                echo do_shortcode($item['field_html']);
                                break;

                            case 'textarea':
                                // PHPCS - the method make_textarea_field is safe.
                                echo $helper->make_textarea_field($this, $item, $item_index); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                break;

                            case 'select':
                                // PHPCS - the method make_select_field is safe.
                                echo $helper->make_select_field($this, $item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                break;

                            case 'radio':
                            case 'checkbox':
                                // PHPCS - the method make_radio_checkbox_field is safe.
                                echo $helper->make_radio_checkbox_field($this, $item, $item['field_type']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                break;

                            case 'recaptcha_v3':
                                $this->render_recaptcha_v3_field($item, $item_index);
                                break;

                            case 'text':
                            case 'email':
                            case 'url':
                            case 'tel':
                            case 'password':
                            case 'hidden':
                            case 'number':
                            case 'date':
                            case 'time':
                                $this->add_render_attribute('input' . $item_index, 'class', 'afeb-form-field-textual');
                                echo '<input size="1"' . $this->get_render_attribute_string('input' . $item_index) . '>';
                                break;

                            case 'upload':
                                $this->render_upload_field($item, $item_index);
                                break;

                            case 'step':
                                $this->render_step_field($item, $item_index);
                                break;
                        endswitch;

                        switch ($item['field_type']) {
                            case 'email':
                                $error_message = !empty($settings['error_message_email']) ? $settings['error_message_email'] : esc_html__('Please use a valid email', 'addons-for-elementor-builder');
                                echo '<input class="afeb-form-error-message" type="hidden" name="email_error_message" value="' . esc_attr($error_message) . '" />';
                                break;
                            case 'url':
                                $error_message = !empty($settings['error_message_url']) ? $settings['error_message_url'] : esc_html__('Please use a valid url', 'addons-for-elementor-builder');
                                echo '<input class="afeb-form-error-message" type="hidden" name="url_error_message" value="' . esc_attr($error_message) . '" />';
                                break;
                            case 'tel':
                                $error_message = !empty($settings['error_message_tel']) ? $settings['error_message_tel'] : esc_html__('Please use a valid tel', 'addons-for-elementor-builder');
                                echo '<input class="afeb-form-error-message" type="hidden" name="tel_error_message" value="' . esc_attr($error_message) . '" />';
                                break;
                            case 'number':
                                $error_message = !empty($settings['error_message_number']) ? $settings['error_message_number'] : esc_html__('Please use a valid number', 'addons-for-elementor-builder');
                                echo '<input class="afeb-form-error-message" type="hidden" name="number_error_message" value="' . esc_attr($error_message) . '" />';
                                break;
                            case 'date':
                                $error_message = !empty($settings['error_message_date']) ? $settings['error_message_date'] : esc_html__('Please use a valid date', 'addons-for-elementor-builder');
                                echo '<input class="afeb-form-error-message" type="hidden" name="date_error_message" value="' . esc_attr($error_message) . '" />';
                                break;
                            case 'time':
                                $error_message = !empty($settings['error_message_time']) ? $settings['error_message_time'] : esc_html__('Please use a valid time', 'addons-for-elementor-builder');
                                echo '<input class="afeb-form-error-message" type="hidden" name="time_error_message" value="' . esc_attr($error_message) . '" />';
                                break;
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
                <?php
                if ($step_exists === 'exists') {
                    if ($step_count > 1) {
                        $this->add_render_attribute('submit-group', 'class', 'afeb-stp-btns-wrap');
                    }

                    echo '<div ' . $this->get_render_attribute_string('submit-group') . '>';

                    if ($step_count <= 2) {
                        $this->render_step_prev_button($this->last_prev_btn_text);
                    }

                    $helper->render_submit_button($this, $settings);
                    echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div ' . $this->get_render_attribute_string('submit-group') . '>';
                    $helper->render_submit_button($this, $settings);
                    echo '</div>';
                }
                ?>
            </div>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['success_message_box_preview'])))
                $_COOKIE['afeb_form_submit_success_' . $this->get_id()] = !empty($settings['success_message']) ?
                    $settings['success_message'] : OldCHelper::$LIM;
            $form_submit_success_cookie_key = 'afeb_form_submit_success_' . $this->get_id();
            $form_submit_success_message = '';
            if (!empty($_COOKIE[$form_submit_success_cookie_key])) {
                $form_submit_success_message = sanitize_text_field(wp_unslash($_COOKIE[$form_submit_success_cookie_key]));
            }
            if (!empty($form_submit_success_message)): ?>
                <div class="afeb-field-group afeb-form-builder-message-group">
                    <div class="afeb-form-builder-success-box">
                        <?php echo esc_html($form_submit_success_message); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['error_message_box_preview'])))
                $_COOKIE['afeb_form_submit_error_' . $this->get_id()] = !empty($settings['error_message']) ?
                    $settings['error_message'] : OldCHelper::$LIM;
            $form_submit_error_cookie_key = 'afeb_form_submit_error_' . $this->get_id();
            $form_submit_error_message = '';
            if (!empty($_COOKIE[$form_submit_error_cookie_key])) {
                $form_submit_error_message = sanitize_text_field(wp_unslash($_COOKIE[$form_submit_error_cookie_key]));
            }
            if (!empty($form_submit_error_message)) :
            ?>
                <div class="afeb-field-group afeb-form-builder-message-group">
                    <div class="afeb-form-builder-error-box">
                        <?php echo esc_html($form_submit_error_message); ?>
                    </div>
                </div>
            <?php endif; ?>
        </form>
<?php
    }

    protected function render_upload_field($field, $index) {}

    protected function render_recaptcha_v3_field($field, $index) {}

    protected function render_step_field($field, $index) {}

    protected function render_step_prev_button($text) {}

    protected function render_step_next_button($text) {}
}
