<?php

namespace AFEB\Widgets\LoginRegister;

use AFEB\Assets;
use AFEB\Base;
use AFEB\Controls\CHelper;
use AFEB\Controls\Helper as New_Helper;
use AFEB\Helper;
use AFEB\Widgets;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" LoginRegister Widget Class
 *
 * @class LoginRegister
 * @version 1.0.3
 */
class LoginRegister extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var ControlsHelper
     */
    private $CHelper;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * @var Helper
     */
    private $Helper;

    /**
     * @var bool
     */
    private $is_editor;

    /**
     * @var int
     */
    public $page_id;

    /**
     * @var string
     */
    public $widget_id;

    /**
     * @var bool
     */
    protected $user_can_register;

    /**
     * @var array
     */
    protected $signin_cndtn = [
        'relation' => 'or',
        'terms' => [
            [
                'relation' => 'and',
                'terms' => [['name' => 'frm_typ', 'operator' => '===', 'value' => 'login']],
            ],
            [
                'relation' => 'and',
                'terms' => [
                    ['name' => 'frm_typ', 'operator' => '===', 'value' => 'register'],
                    ['name' => 'reg_itm_sh_sgnin_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'reg_itm_sgnin_act_lbc', 'operator' => '===', 'value' => 'signin_form'],
                ],
            ],
            [
                'relation' => 'and',
                'terms' => [
                    ['name' => 'frm_typ', 'operator' => '===', 'value' => 'lostpassword'],
                    ['name' => 'lp_itm_sh_sgnin_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'lp_itm_sgnin_act_lbc', 'operator' => '===', 'value' => 'signin_form'],
                ],
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $signup_cndtn = [
        'relation' => 'or',
        'terms' => [
            [
                'relation' => 'and',
                'terms' => [['name' => 'frm_typ', 'operator' => '===', 'value' => 'register']],
            ],
            [
                'relation' => 'and',
                'terms' => [
                    ['name' => 'frm_typ', 'operator' => '===', 'value' => 'login'],
                    ['name' => 'lgn_itm_sh_sgnup_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'lgn_itm_sgnup_act_lbc', 'operator' => '===', 'value' => 'signup_form'],
                ],
            ],
            [
                'relation' => 'and',
                'terms' => [
                    ['name' => 'frm_typ', 'operator' => '===', 'value' => 'lostpassword'],
                    ['name' => 'lp_itm_sh_sgnin_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'lp_itm_sgnin_act_lbc', 'operator' => '===', 'value' => 'signin_form'],
                    ['name' => 'lgn_itm_sh_sgnup_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'lgn_itm_sgnup_act_lbc', 'operator' => '===', 'value' => 'signup_form'],
                ],
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $lp_form_cndtn = [
        'relation' => 'or',
        'terms' => [
            [
                'relation' => 'and',
                'terms' => [['name' => 'frm_typ', 'operator' => '===', 'value' => 'lostpassword']],
            ],
            [
                'relation' => 'and',
                'terms' => [
                    ['name' => 'frm_typ', 'operator' => '===', 'value' => 'login'],
                    ['name' => 'lp_itm_sh_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'lp_itm_act_lbc', 'operator' => '===', 'value' => 'lp_form'],
                ],
            ],
            [
                'relation' => 'and',
                'terms' => [
                    ['name' => 'frm_typ', 'operator' => '===', 'value' => 'register'],
                    ['name' => 'reg_itm_sh_sgnin_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'reg_itm_sgnin_act_lbc', 'operator' => '===', 'value' => 'signin_form'],
                    ['name' => 'lp_itm_sh_lnk', 'operator' => '===', 'value' => 'yes'],
                    ['name' => 'lp_itm_act_lbc', 'operator' => '===', 'value' => 'lp_form'],
                ],
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $crp_form_cndtn = [];

    /**
     * @var string
     */
    private $message;

    /**
     * LoginRegister Constructor
     *
     * @since 1.0.3
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->is_editor = Plugin::instance()->editor->is_edit_mode();
        $this->page_id = Plugin::$instance->documents->get_current() ? Plugin::$instance->documents->get_current()->get_main_id() : intval(get_the_ID());
        $this->widget_id = sanitize_text_field($this->get_id());
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->controls = new New_Helper($this);
        $this->Helper = new Helper();
        $this->assets->lrform_style();
        $this->assets->lrform_script();
        $this->user_can_register = get_option('users_can_register');
        $this->message = sprintf(
            '<strong>%s</strong>%s',
            esc_html__('Note', 'addons-for-elementor-builder'),
            esc_html__('You can use dynamic content in the email body like [fieldname]. For example [username] will be replaced by user-typed username. Available tags are: [username], [email], [firstname],[lastname], [website], [password_reset_link] and [sitetitle]', 'addons-for-elementor-builder')
        );
        $this->crp_form_cndtn = $this->lp_form_cndtn;
        $merge = [['name' => 'cstm_rst_pass_frm', 'operator' => '===', 'value' => 'yes']];
        $this->crp_form_cndtn['terms'][0]['terms'] = array_merge($this->crp_form_cndtn['terms'][0]['terms'], $merge);
        $this->crp_form_cndtn['terms'][1]['terms'] = array_merge($this->crp_form_cndtn['terms'][1]['terms'], $merge);
    }

    /**
     * Get widget name
     *
     * @return string Widget name
     * @since 1.0.3
     *
     */
    public function get_name()
    {
        return 'afeb_login_register';
    }

    /**
     * Get widget title
     *
     * @return string Widget title
     * @since 1.0.3
     *
     */
    public function get_title()
    {
        return esc_html__('Login, Register', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @return string Widget icon
     * @since 1.0.3
     *
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-login-register';
    }

    /**
     * Get widget categories
     *
     * @return array Widget categories
     * @since 1.0.3
     *
     */
    public function get_categories()
    {
        return ['afeb_basic'];
    }

    /**
     * Get widget keywords
     *
     * @return array Widget keywords
     * @since 1.0.3
     *
     */
    public function get_keywords()
    {
        return ['login_register', 'loginregister', esc_html__('Login, Register', 'addons-for-elementor-builder')];
    }

    /**
     * Register LoginRegister widget skins
     *
     * @since 1.0.3
     */
    protected function register_skins()
    {
        do_action('afeb/widget/login_register/register_skins', $this);
    }

    /**
     * Register LoginRegister widget controls
     *
     * @since 1.0.3
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('General', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->slct($obj, 'frm_typ', esc_html__('Form Type', 'addons-for-elementor-builder'), [
                'login' => esc_html__('Login', 'addons-for-elementor-builder'),
                'register' => esc_html__('Registration', 'addons-for-elementor-builder'),
                'lostpassword' => esc_html__('Lost Password', 'addons-for-elementor-builder'),
            ], 'login');
            if (!$this->user_can_register) {
                $this->CHelper->raw_html(
                    $obj,
                    'usr_reg_nt',
                    /* translators: %1$s is replaced with "Start Link Tag" And %2$s replaced with "End Link Tag" */
                    sprintf(__('Registration is disabled on your site. Please enable it to use registration form. You can enable it from Dashboard » Settings » General » %1$sMembership%2$s.', 'addons-for-elementor-builder'), '<a href="' . esc_attr(esc_url(admin_url('options-general.php'))) . '" target="_blank">', '</a>'),
                    'elementor-panel-alert elementor-panel-alert-warning',
                    ['frm_typ' => 'register']
                );
            }
        });
        /**
         *
         * Login Form Controls
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Login Form Fields', 'addons-for-elementor-builder'), function ($obj) {
            $conditions = ['reg_itm_sh_sgnin_lnk' => 'yes', 'reg_itm_sgnin_act_lbc' => 'signin_form'];
            $this->CHelper->yn_swtchr($obj, 'lgn_frm_prev', esc_html__('Preview Form In Editor', 'addons-for-elementor-builder'), 0, $conditions);

            $items = new Repeater();
            $this->CHelper->chse($items, 'lgn_itms', esc_html__('Field Type', 'addons-for-elementor-builder'), [
                'UserName' => [
                    'title' => esc_html__('User Name', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-user-edit',
                ],
                'Password' => [
                    'title' => esc_html__('Password', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-user-lock',
                ],
            ], [], 1, 'UserName');
            $this->CHelper->icn($items, 'lgn_itm_ic', '', '', '', 1);
            $this->CHelper->txt($items, 'lgn_itm_lbl', esc_html__('Label', 'addons-for-elementor-builder'), '', esc_html__('e.g User Name', 'addons-for-elementor-builder'));
            $this->CHelper->txt($items, 'lgn_itm_plc_hldr', esc_html__('Placeholder', 'addons-for-elementor-builder'), '', esc_html__('e.g User Name', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($items, 'lgn_itm_sh_rqurd', esc_html__('Show Required Mark', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($items, 'lgn_itm_hlp_desc', esc_html__('Enable Help Description', 'addons-for-elementor-builder'));
            $this->CHelper->txt_area($items, 'lgn_itm_hlp_desc_txt', esc_html__('Help Description Text', 'addons-for-elementor-builder'), '', '', '', ['lgn_itm_hlp_desc' => 'yes']);
            $this->CHelper->yn_swtchr($items, 'lgn_itm_pv_ic', esc_html__('Password Visibility Icon', 'addons-for-elementor-builder'), '', ['lgn_itms' => 'Password']);
            $slctr = ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 0, 'max' => 1000, 'step' => 5]];
            $this->CHelper->res_sldr($items, 'lgn_itm_wdt', esc_html__('Field Width', 'addons-for-elementor-builder'), $slctr, $range, CHelper::BDSU);
            $this->CHelper->rptr($obj, 'lgn_itms_rpt', $items->get_controls(), [
                [
                    'lgn_itms' => 'UserName',
                    'lgn_itm_ic' => [
                        'library' => 'fa-solid',
                        'value' => 'fas fa-user-edit',
                    ],
                    'lgn_itm_lbl' => esc_html__('User Name', 'addons-for-elementor-builder'),
                    'lgn_itm_plc_hldr' => esc_html__('User Name', 'addons-for-elementor-builder'),
                ],
                [
                    'lgn_itms' => 'Password',
                    'lgn_itm_ic' => [
                        'library' => 'fa-solid',
                        'value' => 'fas fa-lock',
                    ],
                    'lgn_itm_lbl' => esc_html__('Password', 'addons-for-elementor-builder'),
                    'lgn_itm_plc_hldr' => esc_html__('Password', 'addons-for-elementor-builder'),
                ],
            ], '{{{ elementor.helpers.renderIcon( this, lgn_itm_ic, {}, "i", "panel" ) || \'<i class="{{ lgn_itm_ic }}" aria-hidden="true"></i>\' }}}{{lgn_itms}}', '');
            $this->CHelper->dvdr($obj, 'div_1');
            $this->CHelper->hed($obj, 'lgn_itm_rm_hed', esc_html__('Remember Me', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'lgn_itm_sh_rm', esc_html__('Show Remember Me', 'addons-for-elementor-builder'), 1);
            $conditions = ['lgn_itm_sh_rm' => 'yes'];
            $this->CHelper->yn_swtchr($obj, 'lgn_itm_rm_chkd', esc_html__('Checked By Default', 'addons-for-elementor-builder'), [], $conditions);
            $this->CHelper->txt($obj, 'lgn_itm_rm_txt', esc_html__('Remember Me Text', 'addons-for-elementor-builder'), esc_html__('Remember Me', 'addons-for-elementor-builder'), '', 'lblk,dnmc,dai', $conditions);
            $this->CHelper->dvdr($obj, 'div_2');
            $this->CHelper->hed($obj, 'lgn_itm_btn_hed', esc_html__('Login Button', 'addons-for-elementor-builder'));
            $txt = esc_html__('Log In', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'login_itm_btn_txt', esc_html__('Button Text', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dnmc,dai');
            if ($this->user_can_register) {
                $this->CHelper->dvdr($obj, 'div_3');
                $this->CHelper->hed($obj, 'lgn_itm_sgnup_hed', esc_html__('Sign Up', 'addons-for-elementor-builder'));
                $this->CHelper->yn_swtchr($obj, 'lgn_itm_sh_sgnup_lnk', esc_html__('Show Sign Up Link', 'addons-for-elementor-builder'), 1);
                $txt = esc_html__('Sign up', 'addons-for-elementor-builder');
                $this->CHelper->txt($obj, 'lgn_itm_sgnup_txt', esc_html__('Sign Up Text', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dnmc,dai', ['lgn_itm_sh_sgnup_lnk' => 'yes']);
                $this->CHelper->slct($obj, 'lgn_itm_sgnup_act_lbc', esc_html__('Sign Up Action', 'addons-for-elementor-builder'), [
                    '' => esc_html__('WP Registration Page', 'addons-for-elementor-builder'),
                    'custom_url' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                    'signup_form' => esc_html__('Show Sign Up Form', 'addons-for-elementor-builder'),
                ], 'signup_form', ['lgn_itm_sh_sgnup_lnk' => 'yes']);
                $conditions = ['lgn_itm_sh_sgnup_lnk' => 'yes', 'lgn_itm_sgnup_act_lbc' => 'custom_url'];
                $this->CHelper->url($obj, 'lgn_itm_sgnup_cstm_url', 1, Base::AFEB_URL, $conditions, 0, 1);
                $conditions = ['frm_typ!' => 'register', 'lgn_itm_sh_sgnup_lnk' => 'yes', 'lgn_itm_sgnup_act_lbc' => 'signup_form'];
                $this->CHelper->txt($obj, 'lgn_itm_sgnup_back_btn_txt', esc_html__('Back Button Text', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), 'lblk,dnmc,dai', $conditions);
            } else {
                $this->CHelper->hddn($obj, 'lgn_itm_sh_sgnup_lnk', esc_html__('Show Sign Up Link', 'addons-for-elementor-builder'), 'no');
            }
            $this->CHelper->dvdr($obj, 'div_5');
            $this->CHelper->hed($obj, 'lp_itm_hed', esc_html__('Lost Password', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'lp_itm_sh_lnk', esc_html__('Lost Password Link', 'addons-for-elementor-builder'), 1);
            $txt = esc_html__('Lost Password?', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'lp_itm_txt', esc_html__('Lost Password Text', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dnmc,dai', ['lp_itm_sh_lnk' => 'yes']);
            $this->CHelper->slct($obj, 'lp_itm_act_lbc', esc_html__('Lost Password Action', 'addons-for-elementor-builder'), [
                '' => esc_html__('WP Default Page', 'addons-for-elementor-builder'),
                'custom_url' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                'lp_form' => esc_html__('Show Lost Password Form', 'addons-for-elementor-builder'),
            ], 'lp_form', ['lp_itm_sh_lnk' => 'yes']);
            $conditions = ['lp_itm_sh_lnk' => 'yes', 'lp_itm_act_lbc' => 'custom_url'];
            $this->CHelper->url($obj, 'lp_itm_cstm_url', 1, Base::AFEB_URL, $conditions, 0, 1);
            $conditions = ['frm_typ!' => 'lostpassword', 'lp_itm_sh_lnk' => 'yes', 'lp_itm_act_lbc' => 'lp_form'];
            $this->CHelper->txt($obj, 'lp_itm_back_btn_txt', esc_html__('Back Button Text', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), 'lblk,dnmc,dai', $conditions);
        }, [], [], $this->signin_cndtn);
        /**
         *
         * After Logged In
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs3', esc_html__('After Logged In', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'lgnin_frm_prev', esc_html__('Preview Form In Editor', 'addons-for-elementor-builder'), 0);
            $this->controls->sh_switcher('show_avatar', [
                'label' => esc_html__('Avatar', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
            $this->CHelper->hddn($obj, 'lgnin_act_lbc', '', 'default');
            $this->CHelper->slct($obj, 'lgnin_slct_temp_lbc', esc_html__('Select Template', 'addons-for-elementor-builder'), Widgets::get_templates(), '', ['lgnin_act_lbc' => 'custom_template']);
            $cndtn = ['lgnin_act_lbc' => 'default'];
            $def = '<strong>' . esc_html__('Hi [username] 👋', 'addons-for-elementor-builder') . "</strong>\r\n\r\n";
            $def .= '<strong>' . esc_html__('Welcome to [sitetitle]', 'addons-for-elementor-builder') . "</strong>\r\n\r\n";
            $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
            $this->CHelper->wysiwyg($obj, 'def_lgnin_msg', esc_html__('Message', 'addons-for-elementor-builder'), $def, $plc_hldr, '', $cndtn);
        }, [], [], $this->signin_cndtn);
        /**
         *
         * Register Form Controls
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs4', esc_html__('Register Form Fields', 'addons-for-elementor-builder'), function ($obj) {
            $conditions = ['lgn_itm_sh_sgnup_lnk' => 'yes', 'lgn_itm_sgnup_act_lbc' => 'signup_form', 'frm_typ!' => 'register'];
            $this->CHelper->yn_swtchr($obj, 'reg_frm_prev', esc_html__('Preview Form In Editor', 'addons-for-elementor-builder'), 0, $conditions);

            $items = new Repeater();
            $this->CHelper->slct($items, 'reg_itms', esc_html__('Field Type', 'addons-for-elementor-builder'), [
                'UserName' => [
                    'title' => esc_html__('User Name', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-user-edit',
                ],
                'Email' => [
                    'title' => esc_html__('Email', 'addons-for-elementor-builder'),
                    'icon' => 'far fa-envelope',
                ],
                'ConfirmEmail' => [
                    'title' => esc_html__('Confirm Email', 'addons-for-elementor-builder'),
                    'icon' => 'far fa-envelope',
                ],
                'Password' => [
                    'title' => esc_html__('Password', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-user-lock',
                ],
                'ConfirmPassword' => [
                    'title' => esc_html__('Confirm Password', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-user-lock',
                ],
                'FirstName' => [
                    'title' => esc_html__('First Name', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-spell-check',
                ],
                'LastName' => [
                    'title' => esc_html__('Last Name', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-spell-check',
                ],
                'Website' => [
                    'title' => esc_html__('Website', 'addons-for-elementor-builder'),
                    'icon' => 'fas fa-globe-americas',
                ],
            ], 'UserName');
            $this->CHelper->icn($items, 'reg_itm_ic', '', '', '', 1);
            $this->CHelper->txt($items, 'reg_itm_lbl', esc_html__('Label', 'addons-for-elementor-builder'), '', esc_html__('e.g User Name', 'addons-for-elementor-builder'));
            $this->CHelper->txt($items, 'reg_itm_plc_hldr', esc_html__('Placeholder', 'addons-for-elementor-builder'), '', esc_html__('e.g User Name', 'addons-for-elementor-builder'));
            $conditions = ['reg_itms!' => ['ConfirmEmail', 'ConfirmPassword']];
            $this->CHelper->num($items, 'reg_itm_min_lnt', esc_html__('Min Limit Fields Length', 'addons-for-elementor-builder'), null, null, null, '', '', '', $conditions);
            $this->CHelper->num($items, 'reg_itm_max_lnt', esc_html__('Max Limit Fields Length', 'addons-for-elementor-builder'), null, null, null, '', '', '', $conditions);
            $conditions = ['reg_itms!' => ['UserName', 'Email', 'ConfirmEmail', 'ConfirmPassword']];
            $this->CHelper->yn_swtchr($items, 'reg_itm_rqurd', esc_html__('Required', 'addons-for-elementor-builder'), 0, $conditions);
            $this->CHelper->yn_swtchr($items, 'reg_itm_sh_rqurd', esc_html__('Show Required Mark', 'addons-for-elementor-builder'), 0, [], [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'or',
                        'terms' => [
                            ['name' => 'reg_itms', 'operator' => '===', 'value' => 'UserName'],
                            ['name' => 'reg_itms', 'operator' => '===', 'value' => 'Email'],
                            ['name' => 'reg_itms', 'operator' => '===', 'value' => 'ConfirmEmail'],
                            ['name' => 'reg_itms', 'operator' => '===', 'value' => 'ConfirmPassword'],
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            ['name' => 'reg_itm_rqurd', 'operator' => '===', 'value' => 'yes'],
                            ['name' => 'reg_itms', 'operator' => '!=', 'value' => 'UserName'],
                            ['name' => 'reg_itms', 'operator' => '!=', 'value' => 'Email'],
                        ],
                    ],
                ],
            ]);
            $this->CHelper->yn_swtchr($items, 'reg_itm_hlp_desc', esc_html__('Enable Help Description', 'addons-for-elementor-builder'));
            $this->CHelper->txt_area($items, 'reg_itm_hlp_desc_txt', esc_html__('Help Description Text', 'addons-for-elementor-builder'), '', '', 0, ['reg_itm_hlp_desc' => 'yes']);
            $conditions = ['reg_itms' => 'Password'];
            $this->CHelper->yn_swtchr($items, 'reg_itm_pv_ic', esc_html__('Password Visibility Icon', 'addons-for-elementor-builder'), 0, $conditions);
            $slctr = ['{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 0, 'max' => 1000, 'step' => 5]];
            $this->CHelper->res_sldr($items, 'reg_itm_wdt', esc_html__('Field Width', 'addons-for-elementor-builder'), $slctr, $range, CHelper::BDSU);
            $this->CHelper->rptr($obj, 'reg_itms_rpt', $items->get_controls(), [
                [
                    'reg_itms' => 'UserName',
                    'reg_itm_ic' => [
                        'library' => 'fa-solid',
                        'value' => 'fas fa-user-edit',
                    ],
                    'reg_itm_lbl' => esc_html__('User Name', 'addons-for-elementor-builder'),
                    'reg_itm_plc_hldr' => esc_html__('User Name', 'addons-for-elementor-builder'),
                ],
                [
                    'reg_itms' => 'Email',
                    'reg_itm_ic' => [
                        'library' => 'fa-solid',
                        'value' => 'far fa-envelope',
                    ],
                    'reg_itm_lbl' => esc_html__('Email', 'addons-for-elementor-builder'),
                    'reg_itm_plc_hldr' => esc_html__('Email', 'addons-for-elementor-builder'),
                ],
                [
                    'reg_itms' => 'Password',
                    'reg_itm_ic' => [
                        'library' => 'fa-solid',
                        'value' => 'fas fa-lock',
                    ],
                    'reg_itm_lbl' => esc_html__('Password', 'addons-for-elementor-builder'),
                    'reg_itm_plc_hldr' => esc_html__('Password', 'addons-for-elementor-builder'),
                ],
            ], '{{{ elementor.helpers.renderIcon( this, reg_itm_ic, {}, "i", "panel" ) || \'<i class="{{ reg_itm_ic }}" aria-hidden="true"></i>\' }}}{{reg_itms}}', '');
            $this->CHelper->dvdr($obj, 'div_6');
            $this->CHelper->hed($obj, 'reg_itm_btn_hed', esc_html__('Register Button', 'addons-for-elementor-builder'));
            $txt = esc_html__('Register', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'register_itm_btn_txt', esc_html__('Button Text', 'addons-for-elementor-builder'), $txt, $txt, 'dnmc,dai');
            $this->CHelper->dvdr($obj, 'div_12');
            $this->CHelper->hed($obj, 'reg_itm_sgnin_hed', esc_html__('Sign in', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'reg_itm_sh_sgnin_lnk', esc_html__('Show Sign In Link', 'addons-for-elementor-builder'), 1);
            $this->CHelper->txt($obj, 'reg_itm_sgnin_txt', esc_html__('Sign In Text', 'addons-for-elementor-builder'), esc_html__('Sign in', 'addons-for-elementor-builder'), esc_html__('Sign in', 'addons-for-elementor-builder'), 'lblk,dnmc,dai', ['reg_itm_sh_sgnin_lnk' => 'yes']);
            $this->CHelper->slct($obj, 'reg_itm_sgnin_act_lbc', esc_html__('Sign In Action', 'addons-for-elementor-builder'), [
                '' => esc_html__('WP Login Page', 'addons-for-elementor-builder'),
                'custom_url' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                'signin_form' => esc_html__('Show Sign In Form', 'addons-for-elementor-builder'),
            ], 'signin_form', ['reg_itm_sh_sgnin_lnk' => 'yes']);
            $conditions = ['reg_itm_sh_sgnin_lnk' => 'yes', 'reg_itm_sgnin_act_lbc' => 'custom_url'];
            $this->CHelper->url($obj, 'reg_itm_sgnin_cstm_url', 1, Base::AFEB_URL, $conditions, 0, 1);
            $conditions = ['frm_typ!' => 'login', 'reg_itm_sh_sgnin_lnk' => 'yes', 'reg_itm_sgnin_act_lbc' => 'signin_form'];
            $this->CHelper->txt($obj, 'reg_itm_sgnin_back_btn_txt', esc_html__('Back Button Text', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), 'lblk,dnmc,dai', $conditions);
        }, [], [], $this->signup_cndtn);
        /**
         *
         * Register Email Options
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs5', esc_html__('Register Email Options', 'addons-for-elementor-builder'), function ($obj) {
            $obj->start_controls_tabs('afeb_icn_tb_cntrl');
            /**
             * New User Email
             */
            $this->CHelper->add_tb($obj, 'reo_new_usr_eml_tab', esc_html__('New User Email', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct($obj, 'reg_usr_eml_typ', esc_html__('Email Type', 'addons-for-elementor-builder'), [
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                    'default' => esc_html__('WordPress Default', 'addons-for-elementor-builder'),
                    'custom' => esc_html__('Custom', 'addons-for-elementor-builder'),
                ], 'none');

                /* translators: %s: Site Name */
                $def = sprintf(__('Thank you for registering on "%s"!', 'addons-for-elementor-builder'), get_option('blogname'));
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $cndtn = ['reg_usr_eml_typ' => 'custom'];
                $this->CHelper->txt_area($obj, 'reg_usr_eml_sbjct', esc_html__('Email Subject', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai', $cndtn);

                $def = $def . "\r\n\r\n";
                $def .= esc_html__('Username: [username]', 'addons-for-elementor-builder') . "\r\n\r\n";
                $def .= esc_html__('Password: [password]', 'addons-for-elementor-builder') . "\r\n\r\n";
                $def .= esc_html__('To reset your password, visit the following address:', 'addons-for-elementor-builder') . "\r\n\r\n";
                $def .= "[password_reset_link]\r\n\r\n";
                $def .= esc_html__('Please click the following address to login to your account:', 'addons-for-elementor-builder') . "\r\n\r\n";
                $def .= wp_login_url() . "\r\n";
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->wysiwyg($obj, 'reg_usr_eml_msg', esc_html__('Email Message', 'addons-for-elementor-builder'), $def, $plc_hldr, '', $cndtn);
                $this->CHelper->raw_html($obj, 'reg_usr_eml_cnt_not', $this->message, 'elementor-panel-alert elementor-panel-alert-info', $cndtn);
                $this->CHelper->slct($obj, 'reg_usr_eml_cnt_typ', esc_html__('Email Content Type', 'addons-for-elementor-builder'), [
                    'html' => esc_html__('HTML', 'addons-for-elementor-builder'),
                    'plain' => esc_html__('Plain', 'addons-for-elementor-builder'),
                ], 'html', $cndtn);
            });
            /**
             * New User Admin Email
             */
            $this->CHelper->add_tb($obj, 'reo_new_usr_eml_admn_tab', esc_html__('New User Admin Email', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct($obj, 'reg_usr_admn_eml_typ', esc_html__('Email Type', 'addons-for-elementor-builder'), [
                    'none' => esc_html__('None', 'addons-for-elementor-builder'),
                    'default' => esc_html__('WordPress Default', 'addons-for-elementor-builder'),
                    'custom' => esc_html__('Custom', 'addons-for-elementor-builder'),
                ], 'none');

                /* translators: %s: Site Name */
                $def = sprintf(__('["%s"] New User Registration', 'addons-for-elementor-builder'), get_option('blogname'));
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $cndtn = ['reg_usr_admn_eml_typ' => 'custom'];
                $this->CHelper->txt_area($obj, 'reg_usr_admn_eml_sbjct', esc_html__('Email Subject', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai', $cndtn);

                /* translators: %s is replaced with "Blog Name" */
                $def = sprintf(__("New user registration on your site %s", 'addons-for-elementor-builder'), get_option('blogname')) . "\r\n\r\n";
                $def .= esc_html__('Username: [username]', 'addons-for-elementor-builder') . "\r\n\r\n";
                $def .= esc_html__('Email: [email]', 'addons-for-elementor-builder') . "\r\n\r\n";
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->wysiwyg($obj, 'reg_usr_admn_eml_msg', esc_html__('Email Message', 'addons-for-elementor-builder'), $def, $plc_hldr, '', $cndtn);
                $this->CHelper->raw_html($obj, 'reg_usr_admn_eml_cnt_not', $this->message, 'elementor-panel-alert elementor-panel-alert-info', $cndtn);
                $this->CHelper->slct($obj, 'reg_usr_admn_eml_cnt_typ', esc_html__('Email Content Type', 'addons-for-elementor-builder'), [
                    'html' => esc_html__('HTML', 'addons-for-elementor-builder'),
                    'plain' => esc_html__('Plain', 'addons-for-elementor-builder'),
                ], 'html', $cndtn);
            });
            $obj->end_controls_tabs();
        }, [], [], $this->signup_cndtn);
        /**
         *
         * Lost Password Form Controls
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs6', esc_html__('Lost Password Form Fields', 'addons-for-elementor-builder'), function ($obj) {
            $conditions = ['lp_itm_sh_lnk' => 'yes', 'lp_itm_act_lbc' => 'lp_form'];
            $this->CHelper->yn_swtchr($obj, 'lp_frm_prev', esc_html__('Preview Form In Editor', 'addons-for-elementor-builder'), 0, $conditions);
            $this->CHelper->icn($obj, 'lp_itm_ic', 'far fa-envelope', 'fa-solid', ' ', 1);
            $txt = esc_html__('Username or Email Address', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'lp_itm_lbl', esc_html__('Label', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dai');
            $this->CHelper->txt($obj, 'lp_itm_plc_hldr', esc_html__('Placeholder', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dai');
            $this->CHelper->yn_swtchr($obj, 'lp_itm_sh_requrd', esc_html__('Show Required Mark', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'lp_itm_hlp_desc', esc_html__('Enable Help Description', 'addons-for-elementor-builder'));
            $this->CHelper->txt_area($obj, 'lp_itm_hlp_desc_txt', esc_html__('Help Description Text', 'addons-for-elementor-builder'), '', '', 0, ['lp_itm_hlp_desc' => 'yes']);
            $this->CHelper->dvdr($obj, 'div_7');
            $this->CHelper->hed($obj, 'lp_itm_btn_hed', esc_html__('Lost Password Button', 'addons-for-elementor-builder'));
            $txt = esc_html__('Reset Password', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'lostpassword_itm_btn_txt', esc_html__('Button Text', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dnmc,dai');
            $this->CHelper->dvdr($obj, 'div_13');
            $this->CHelper->hed($obj, 'lp_itm_sgnin_hed', esc_html__('Sign in', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'lp_itm_sh_sgnin_lnk', esc_html__('Show Sign In Link', 'addons-for-elementor-builder'), 1);
            $this->CHelper->txt($obj, 'lp_itm_sgnin_txt', esc_html__('Sign In Text', 'addons-for-elementor-builder'), esc_html__('Sign in', 'addons-for-elementor-builder'), esc_html__('Sign in', 'addons-for-elementor-builder'), 'lblk,dnmc,dai', ['lp_itm_sh_sgnin_lnk' => 'yes']);
            $this->CHelper->slct($obj, 'lp_itm_sgnin_act_lbc', esc_html__('Sign In Action', 'addons-for-elementor-builder'), [
                '' => esc_html__('WP Login Page', 'addons-for-elementor-builder'),
                'custom_url' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                'signin_form' => esc_html__('Show Sign In Form', 'addons-for-elementor-builder'),
            ], 'signin_form', ['lp_itm_sh_sgnin_lnk' => 'yes']);
            $conditions = ['lp_itm_sh_sgnin_lnk' => 'yes', 'lp_itm_sgnin_act_lbc' => 'custom_url'];
            $this->CHelper->url($obj, 'lp_itm_sgnin_cstm_url', 1, Base::AFEB_URL, $conditions, 0, 1);
            $conditions = ['frm_typ!' => 'login', 'lp_itm_sh_sgnin_lnk' => 'yes', 'lp_itm_sgnin_act_lbc' => 'signin_form'];
            $this->CHelper->txt($obj, 'lp_itm_sgnin_back_btn_txt', esc_html__('Back Button Text', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), esc_html__('Back', 'addons-for-elementor-builder'), 'lblk,dnmc,dai', $conditions);
            $this->CHelper->dvdr($obj, 'div_14');
            $slctr = ['{{WRAPPER}} .afeb-lostpassword-form .afeb-lr-form-group' => 'width: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 0, 'max' => 1000, 'step' => 5]];
            $this->CHelper->res_sldr($obj, 'lp_itm_wdt', esc_html__('Field Width', 'addons-for-elementor-builder'), $slctr, $range, CHelper::BDSU);
        }, [], [], $this->lp_form_cndtn);
        /**
         *
         * Lost Password Email Options
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs7', esc_html__('Lost Password Email Options', 'addons-for-elementor-builder'), function ($obj) {
            $def = esc_html__('Password Reset Confirmation', 'addons-for-elementor-builder');
            $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
            $this->CHelper->txt($obj, 'lp_eml_sbjct', esc_html__('Email Subject', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');

            $def = esc_html__('Someone has requested a password reset for the following account:', 'addons-for-elementor-builder') . "\r\n\r\n";
            $def .= esc_html__('Sitename: [sitetitle]', 'addons-for-elementor-builder') . "\r\n\r\n";
            $def .= esc_html__('Username: [username]', 'addons-for-elementor-builder') . "\r\n\r\n";
            $def .= esc_html__('If this was a mistake, ignore this email and nothing will happen.', 'addons-for-elementor-builder') . "\r\n\r\n";
            $def .= esc_html__('To reset your password, visit the following address:', 'addons-for-elementor-builder') . "\r\n\r\n";
            $def .= '[password_reset_link]' . "\r\n\r\n";
            $def .= esc_html__('Thanks!', 'addons-for-elementor-builder');
            $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;

            $this->CHelper->wysiwyg($obj, 'lp_eml_msg', esc_html__('Email Message', 'addons-for-elementor-builder'), $def, $plc_hldr);
            $this->CHelper->raw_html($obj, 'lp_eml_cnt_not', $this->message, 'elementor-panel-alert elementor-panel-alert-info');

            $def = esc_html__('Click here to reset your password', 'addons-for-elementor-builder');
            $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
            $this->CHelper->txt_area($obj, 'lp_eml_msg_rst_lnk_txt', esc_html__('Reset Link Text', 'addons-for-elementor-builder'), $def, $plc_hldr, 'dai');
            $this->CHelper->slct($obj, 'lp_eml_cnt_typ', esc_html__('Email Content Type', 'addons-for-elementor-builder'), [
                'html' => esc_html__('HTML', 'addons-for-elementor-builder'),
                'plain' => esc_html__('Plain', 'addons-for-elementor-builder'),
            ], 'html');
        }, [], [], $this->lp_form_cndtn);
        /**
         *
         * Custom Reset Password Form
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs8', esc_html__('Custom Reset Password Form', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'cstm_rst_pass_frm', esc_html__('Custom Reset Password Form', 'addons-for-elementor-builder'), 0, [], [
                'relation' => 'or',
                'terms' => [['name' => 'frm_typ', 'value' => 'lostpassword']],
            ]);
            $this->CHelper->yn_swtchr($obj, 'crp_frm_prev', esc_html__('Preview Form In Editor', 'addons-for-elementor-builder'), 0, [], $this->crp_form_cndtn);

            $obj->start_controls_tabs('afeb_icn_tb_cntrl0');
            /**
             * New Password
             */
            $this->CHelper->add_tb($obj, 'crp_new_pass_tab', esc_html__('New Password', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->icn($obj, 'crp_np_itm_ic', 'fas fa-user-lock', 'fa-solid', ' ', 1);
                $txt = esc_html__('New Password', 'addons-for-elementor-builder');
                $this->CHelper->txt($obj, 'crp_np_itm_lbl', esc_html__('Label', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dai');
                $this->CHelper->txt($obj, 'crp_np_itm_plc_hldr', esc_html__('Placeholder', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dai');
                $this->CHelper->num($obj, 'crp_np_itm_lnt', esc_html__('Limit Fields Length', 'addons-for-elementor-builder'));
                $this->CHelper->yn_swtchr($obj, 'crp_np_itm_sh_requrd', esc_html__('Show Required Mark', 'addons-for-elementor-builder'));
                $this->CHelper->yn_swtchr($obj, 'crp_np_itm_hlp_desc', esc_html__('Enable Help Description', 'addons-for-elementor-builder'));
                $this->CHelper->txt_area($obj, 'crp_np_itm_hlp_desc_txt', esc_html__('Help Description Text', 'addons-for-elementor-builder'), '', '', 0, ['crp_np_itm_hlp_desc' => 'yes']);
                $this->CHelper->yn_swtchr($obj, 'crp_np_itm_pv_ic', esc_html__('Password Visibility Icon', 'addons-for-elementor-builder'), '');
                $slctr = ['{{WRAPPER}} .afeb-resetpassword-form .afeb-lr-form-group:first-child' => 'width: {{SIZE}}{{UNIT}}'];
                $range = ['px' => ['min' => 0, 'max' => 1000, 'step' => 5]];
                $this->CHelper->res_sldr($obj, 'crp_np_itm_wdt', esc_html__('Field Width', 'addons-for-elementor-builder'), $slctr, $range, CHelper::BDSU);
            });
            /**
             * Confirm New Password
             */
            $this->CHelper->add_tb($obj, 'crp_new_cnp_tab', esc_html__('Confirm New Password', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->icn($obj, 'crp_cnp_itm_ic', 'fas fa-user-lock', 'fa-solid', ' ', 1);
                $txt = esc_html__('Confirm New Password', 'addons-for-elementor-builder');
                $this->CHelper->txt($obj, 'crp_cnp_itm_lbl', esc_html__('Label', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dai');
                $this->CHelper->txt($obj, 'crp_cnp_itm_plc_hldr', esc_html__('Placeholder', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dai');
                $this->CHelper->yn_swtchr($obj, 'crp_cnp_itm_sh_requrd', esc_html__('Show Required Mark', 'addons-for-elementor-builder'));
                $this->CHelper->yn_swtchr($obj, 'crp_cnp_itm_hlp_desc', esc_html__('Enable Help Description', 'addons-for-elementor-builder'));
                $this->CHelper->txt_area($obj, 'crp_cnp_itm_hlp_desc_txt', esc_html__('Help Description Text', 'addons-for-elementor-builder'), '', '', 0, ['crp_cnp_itm_hlp_desc' => 'yes']);
                $slctr = ['{{WRAPPER}} .afeb-resetpassword-form .afeb-lr-form-group:last-child' => 'width: {{SIZE}}{{UNIT}}'];
                $range = ['px' => ['min' => 0, 'max' => 1000, 'step' => 5]];
                $this->CHelper->res_sldr($obj, 'crp_cnp_itm_wdt', esc_html__('Field Width', 'addons-for-elementor-builder'), $slctr, $range, CHelper::BDSU);
            });
            $obj->end_controls_tabs();

            $this->CHelper->hed($obj, 'crp_itm_btn_hed', esc_html__('Reset Password Button', 'addons-for-elementor-builder'));
            $txt = esc_html__('Save Password', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'resetpassword_itm_btn_txt', esc_html__('Button Text', 'addons-for-elementor-builder'), $txt, $txt, 'dnmc,dai');
        }, [], [], $this->lp_form_cndtn);
        /**
         *
         * Additional Form Options
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs9', esc_html__('Additional Form Options', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'frm_sec_field', esc_html__('Show Security Field', 'addons-for-elementor-builder'));
            $cndtn = ['frm_sec_field' => 'yes'];
            $this->CHelper->icn($obj, 'sec_itm_ic', 'fas fa-pencil-alt', 'fa-solid', ' ', 1, 0, $cndtn);
            $txt = esc_html__('CAPTCHA', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'sec_itm_lbl', esc_html__('Label', 'addons-for-elementor-builder'), $txt, $txt, 'lblk,dai', $cndtn);
            $this->CHelper->yn_swtchr($obj, 'sec_itm_sh_requrd', esc_html__('Show Required Mark', 'addons-for-elementor-builder'), 0, $cndtn);
            $this->CHelper->yn_swtchr($obj, 'sec_itm_hlp_desc', esc_html__('Enable Help Description', 'addons-for-elementor-builder'), 0, $cndtn);
            $this->CHelper->txt_area($obj, 'sec_itm_hlp_desc_txt', esc_html__('Help Description Text', 'addons-for-elementor-builder'), '', '', 0, ['sec_itm_hlp_desc' => 'yes']);
            $slctr = ['{{WRAPPER}} .afeb-sec-field-group' => 'width: {{SIZE}}{{UNIT}}'];
            $range = ['px' => ['min' => 0, 'max' => 1000, 'step' => 5]];
            $this->CHelper->res_sldr($obj, 'sec_itm_wdt', esc_html__('Field Width', 'addons-for-elementor-builder'), $slctr, $range, CHelper::BDSU, $cndtn);
            $this->CHelper->hed($obj, 'frm_opt', esc_html__('Form Type', 'addons-for-elementor-builder'));
            $obj->start_controls_tabs('afeb_icn_tb_cntrl1');
            /**
             * Login
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_lgn_frm_opt_tab', esc_html__('Login', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct($obj, 'rdrct_aftr_lgn_act_lbc', esc_html__('Redirect After Login', 'addons-for-elementor-builder'), [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    'custom_url' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                    'previous_page' => esc_html__('Redirect to Previous Page', 'addons-for-elementor-builder'),
                ]);
                $this->CHelper->url($obj, 'rdrct_aftr_lgn_cstm_url', 1, Base::AFEB_URL, ['rdrct_aftr_lgn_act_lbc' => 'custom_url'], 0);
            });
            /**
             * Register
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_reg_frm_opt_tab', esc_html__('Register', 'addons-for-elementor-builder'), function ($obj) {
                $user_roles = current_user_can('administrator') ? $this->Helper->get_user_roles() : ['' => esc_html__('Default', 'addons-for-elementor-builder')];
                $this->CHelper->slct($obj, 'reg_usr_rol', esc_html__('New User Role', 'addons-for-elementor-builder'), $user_roles);
                $this->CHelper->slct($obj, 'aftr_reg_act_lbc', esc_html__('After Register Actions', 'addons-for-elementor-builder'), [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    'redirect' => esc_html__('Redirect', 'addons-for-elementor-builder'),
                    'auto_login' => esc_html__('Auto Login', 'addons-for-elementor-builder'),
                ]);
                $this->CHelper->url($obj, 'rdrct_aftr_reg_cstm_url', 1, Base::AFEB_URL, ['aftr_reg_act_lbc' => 'redirect'], 0);
            });
            /**
             * LostPassword
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_lp_frm_opt_tab', esc_html__('LostPassword', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct($obj, 'rdrct_aftr_lp_act_lbc', esc_html__('Redirect After Send Email', 'addons-for-elementor-builder'), [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    'custom_url' => esc_html__('Custom URL', 'addons-for-elementor-builder'),
                    'previous_page' => esc_html__('Redirect to Previous Page', 'addons-for-elementor-builder'),
                ]);
                $this->CHelper->url($obj, 'rdrct_aftr_lp_cstm_url', 1, Base::AFEB_URL, ['rdrct_aftr_lp_act_lbc' => 'custom_url'], 0);
            });
            /**
             * ResetPassword
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_crp_frm_opt_tab', esc_html__('ResetPassword', 'addons-for-elementor-builder'), function ($obj) {
                $this->CHelper->slct($obj, 'aftr_crp_act_lbc', esc_html__('After Register Actions', 'addons-for-elementor-builder'), [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    'redirect' => esc_html__('Redirect', 'addons-for-elementor-builder'),
                    'auto_login' => esc_html__('Auto Login', 'addons-for-elementor-builder'),
                ]);
                $this->CHelper->url($obj, 'rdrct_aftr_crp_cstm_url', 1, Base::AFEB_URL, ['aftr_crp_act_lbc' => 'redirect'], 0);
            });
            $obj->end_controls_tabs();
        });
        /**
         *
         * Validation Messages
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs10', esc_html__('Validation Messages', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->hed($obj, 'err_msgs', esc_html__('Error Messages', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'err_msg_bx_prev', esc_html__('Preview In Editor', 'addons-for-elementor-builder'), 0);
            $obj->start_controls_tabs('afeb_icn_tb_cntrl2');
            /**
             * User Name
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_usrname_err_msg_tab', esc_html__('UserName', 'addons-for-elementor-builder'), function ($obj) {
                $def = esc_html__('You have used an invalid username', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg. Your username is invalid.', 'addons-for-elementor-builder');
                $this->CHelper->txt_area($obj, 'err_usrnm', esc_html__('Invalid Username', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__('This username already exists on the site', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg. Your username is already registered.', 'addons-for-elementor-builder');
                $this->CHelper->txt_area($obj, 'err_usrnm_usd', esc_html__('Username already in use', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
            });
            /**
             * Email
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_email_err_msg_tab', esc_html__('Email', 'addons-for-elementor-builder'), function ($obj) {
                $def = esc_html__('Please enter a valid email', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg. Your email is invalid.', 'addons-for-elementor-builder');
                $this->CHelper->txt_area($obj, 'err_eml', esc_html__('Invalid Email', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__('Email is missing or Invalid', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg. Email is missing or Invalid', 'addons-for-elementor-builder');
                $this->CHelper->txt_area($obj, 'err_eml_mis', esc_html__('Email is missing', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__('This email already exists on the site', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg. Your email is already in use', 'addons-for-elementor-builder');
                $this->CHelper->txt_area($obj, 'err_eml_usd', esc_html__('Already Used Email', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__('The confirmed email did not match', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg. Confirm email mismatch', 'addons-for-elementor-builder');
                $this->CHelper->txt_area($obj, 'err_eml_cfrm_did_mtch', esc_html__('Invalid Email Confirmed', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
            });
            /**
             * Password
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_pass_err_msg_tab', esc_html__('Password', 'addons-for-elementor-builder'), function ($obj) {
                $def = esc_html__("Please enter a valid Password", 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'err_pass', esc_html__('Invalid Password', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__('The confirmed password did not match', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'err_conf_pass', esc_html__('Invalid Password Confirmed', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__('Your password reset link appears to be invalid. Please request a new link', 'addons-for-elementor-builder');
                $plc_hldr = sprintf('%s%s', esc_html__('Eg. ', 'addons-for-elementor-builder'), $def);
                $this->CHelper->txt_area($obj, 'err_rp_key_exprd', esc_html__('Reset Password Expired Error', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
            });
            /**
             * More
             */
            $this->CHelper->add_tb($obj, 'lgn_rgstr_more_err_msg_tab', esc_html__('More', 'addons-for-elementor-builder'), function ($obj) {
                $def = esc_html__("You are already logged in", 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'err_logdin', esc_html__('Already Logged In', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__("Invalid security answer, Please try again", 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'err_sec', esc_html__('Security Captcha Field', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__("Something went wrong!", 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'err_unkn', esc_html__('Other Errors', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
            });
            $obj->end_controls_tabs();
            $this->CHelper->dvdr($obj, 'div_11');
            $this->CHelper->hed($obj, 'sucs_msg_hed', esc_html__('Success Messages', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'succ_msg_bx_prev', esc_html__('Preview In Editor', 'addons-for-elementor-builder'), 0);
            $obj->start_controls_tabs('afeb_icn_tb_cntrl3');
            /**
             * Register
             */
            $this->CHelper->add_tb($obj, 'reg_sucs_msg_tab', esc_html__('Register', 'addons-for-elementor-builder'), function ($obj) {
                $def = esc_html__('Your registration was successful, Now you can login to the site', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'sucs_msg_reg', esc_html__('Register Form Success', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
                $def = esc_html__('Your registration was successful, Please check your email inbox for the password', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'sucs_msg_reg_no_pass', esc_html__("Register Form Success (Password Not Set)", 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
            });
            /**
             * LostPassword
             */
            $this->CHelper->add_tb($obj, 'lp_sucs_msg_tab', esc_html__('LostPassword', 'addons-for-elementor-builder'), function ($obj) {
                $def = esc_html__('Check your email for the confirmation link', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'sucs_msg_lp', esc_html__('Lost Password Form Success', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
            });
            /**
             * ResetPassword
             */
            $this->CHelper->add_tb($obj, 'crp_sucs_msg_tab', esc_html__('ResetPassword', 'addons-for-elementor-builder'), function ($obj) {
                $def = esc_html__('Password changed successfully, Now you can login to the site', 'addons-for-elementor-builder');
                $plc_hldr = esc_html__('Eg.', 'addons-for-elementor-builder') . ' ' . $def;
                $this->CHelper->txt_area($obj, 'sucs_msg_crp', esc_html__('ResetPassword Form Success', 'addons-for-elementor-builder'), $def, $plc_hldr, 'lblk,dai');
            });
            $obj->end_controls_tabs();
        });
        /**
         *
         * Form
         *
         */
        $dd_btn_slctr = '{{WRAPPER}} .afeb-dropdown-btn ';
        $mdl_btn_slctr = '{{WRAPPER}} .afeb-modal-btn ';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            if (!defined('AFEBP_LITE_VS')) {
                $this->CHelper->hddn($obj, '_skin');
                $this->CHelper->hddn($obj, 'afebp_lr_dropdown_skin_ddn_btn_bfr_ic');
                $this->CHelper->hddn($obj, 'afebp_lr_modal_skin_mdl_btn_bfr_ic');
                $this->CHelper->hddn($obj, 'afebp_lr_dropdown_skin_ddn_btn_aftr_ic');
                $this->CHelper->hddn($obj, 'afebp_lr_modal_skin_mdl_btn_aftr_ic');
            }
            $cmbn_slctr = $opt[0] . ',' . $opt[1];
            $this->CHelper->typo($obj, 'ddmdl_btn_typo', $cmbn_slctr);
            $this->CHelper->chse($obj, 'ddmdl_btn_crsr', esc_html__('Mouse Cursor', 'addons-for-elementor-builder'), [
                'default' => ['title' => esc_html__('Default', 'addons-for-elementor-builder'), 'icon' => 'eicon-button'],
                'pointer' => ['title' => esc_html__('Pointer', 'addons-for-elementor-builder'), 'icon' => 'eicon-click'],
            ], [$cmbn_slctr => 'cursor: {{VALUE}}']);
            $slctr = [$opt[0] . ' > .afeb-dropdown-btn-before-icon > *, {{WRAPPER}} .afeb-modal-btn > .afeb-modal-btn-before-icon > *' => 'font-size: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'];
            $bfr_cndtn_term = [
                'relation' => 'or',
                'terms' => [
                    ['name' => 'afebp_lr_dropdown_skin_ddn_btn_bfr_ic', 'operator' => '!=', 'value' => ''],
                    ['name' => 'afebp_lr_modal_skin_mdl_btn_bfr_ic', 'operator' => '!=', 'value' => ''],
                ],
            ];
            $aftr_cndtn_term = [
                'relation' => 'or',
                'terms' => [
                    ['name' => 'afebp_lr_dropdown_skin_ddn_btn_aftr_ic', 'operator' => '!=', 'value' => ''],
                    ['name' => 'afebp_lr_modal_skin_mdl_btn_aftr_ic', 'operator' => '!=', 'value' => ''],
                ],
            ];
            $cndtn = [
                'relation' => 'or',
                'terms' => array_merge($bfr_cndtn_term['terms'], $aftr_cndtn_term['terms']),
            ];
            $this->CHelper->res_sldr($obj, 'ddmdl_btn_bfr_ic_sze', esc_html__('Before Icon Size', 'addons-for-elementor-builder'), $slctr, [], ['px', 'em', 'rem', 'custom'], [], [], $bfr_cndtn_term);
            $slctr = [$opt[0] . ' > .afeb-dropdown-btn-after-icon > *, {{WRAPPER}} .afeb-modal-btn > .afeb-modal-btn-after-icon > *' => 'font-size: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'ddmdl_btn_aftr_ic_sze', esc_html__('After Icon Size', 'addons-for-elementor-builder'), $slctr, [], ['px', 'em', 'rem', 'custom'], [], [], $aftr_cndtn_term);
            $slctr = [$cmbn_slctr => 'gap: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'ddmdl_btn_ic_spc', esc_html__('Icon Spacing', 'addons-for-elementor-builder'), $slctr, [], ['px', 'em', 'rem', 'custom'], [], [], $cndtn);
            $obj->start_controls_tabs('itms_stl_tbs_0');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'ddmdl_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'ddmdl_btn_bg', $opt[2]);
                $this->CHelper->clr($obj, 'ddmdl_btn_clr', $opt[0] . '*,' . $opt[1] . '*', esc_html__('Content Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_mar($obj, 'ddmdl_btn_mar', $opt[2]);
                $this->CHelper->res_pad($obj, 'ddmdl_btn_pad', $opt[2]);
                $this->CHelper->brdr($obj, 'ddmdl_btn_brdr', $opt[2]);
                $this->CHelper->brdr_rdus($obj, 'ddmdl_btn_rdus', [$opt[2] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
                $this->CHelper->bx_shdo($obj, 'ddmdl_btn_bx_shdo', $opt[2]);
            }, [$opt[0], $opt[1], $cmbn_slctr]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'ddmdl_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $cmbn_slctr = $opt[0] . ',' . $opt[1];
                $this->CHelper->bg_grp_ctrl($obj, 'ddmdl_btn_bg_hvr', $cmbn_slctr);
                $this->CHelper->clr($obj, 'ddmdl_btn_clr_hvr', $opt[0] . '>*,' . $opt[1] . '>*');
                $this->CHelper->h_anim($obj, 'ddmdl_btn_anim_hvr');
                $this->CHelper->res_pad($obj, 'ddmdl_btn_pad_hvr', $cmbn_slctr);
                $this->CHelper->brdr($obj, 'ddmdl_btn_brdr_hvr', $cmbn_slctr);
                $this->CHelper->brdr_rdus($obj, 'ddmdl_btn_rdus_hvr', [$cmbn_slctr => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
                $this->CHelper->bx_shdo($obj, 'ddmdl_btn_bx_shdo_hvr', $cmbn_slctr);
            }, [$opt[0] . ':hover', $opt[1] . ':hover']);
            $obj->end_controls_tabs();
        }, [$dd_btn_slctr, $mdl_btn_slctr], [], [
            'relation' => 'or',
            'terms' => [
                ['name' => '_skin', 'operator' => '===', 'value' => 'afebp_lr_dropdown_skin'],
                ['name' => '_skin', 'operator' => '===', 'value' => 'afebp_lr_modal_skin'],
            ],
        ]);
        /**
         *
         * Form
         *
         */
        $lr_box_slctr = '{{WRAPPER}} .afeb-lr-box';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Form', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'frm_bg', $opt[0]);
            $this->CHelper->res_mar($obj, 'frm_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'frm_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'frm_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'frm_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'frm_bx_shdo', $opt[0]);
        }, [$lr_box_slctr]);
        /**
         *
         * Form Fields Group
         *
         */
        $lr_ffg_slctr = $lr_box_slctr . ' .afeb-lr-form-group';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Form Fields Group', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'ffg_bg', $opt[0]);
            $this->CHelper->res_mar($obj, 'ffg_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'ffg_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'ffg_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ffg_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'ffg_bx_shdo', $opt[0]);
        }, [$lr_ffg_slctr]);
        /**
         *
         * Form Labels
         *
         */
        $lr_flbl_slctr = $lr_ffg_slctr . ' label';
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Form Labels', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'flbl_bg', $opt[0]);
            $this->CHelper->clr($obj, 'flbl_clr', $opt[0]);
            $this->CHelper->typo($obj, 'flbl_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'flbl_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'flbl_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'flbl_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'flbl_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'flbl_bx_shdo', $opt[0]);
        }, [$lr_flbl_slctr]);
        /**
         *
         * Form Fields Box
         *
         */
        $lr_ffb_slctr = $lr_ffg_slctr . ' .afeb-lr-form-control-box';
        $this->CHelper->add_stl_sctn($this, 'ss5', esc_html__('Form Fields Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'ffb_bg', $opt[0]);
            $this->CHelper->res_mar($obj, 'ffb_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'ffb_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'ffb_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ffb_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'ffb_bx_shdo', $opt[0]);
        }, [$lr_ffb_slctr]);
        /**
         *
         * Help Description
         *
         */
        $lr_hlp_desc_slctr = $lr_flbl_slctr . ' .afeb-help-description';
        $this->CHelper->add_stl_sctn($this, 'ss6', esc_html__('Help Description', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'hlpd_bg', $opt[0]);
            $this->CHelper->clr($obj, 'hlpd_clr', $opt[0], esc_html__('Icon Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'hlpd_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'hlpd_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'hlpd_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'hlpd_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'hlpd_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
        }, [$lr_hlp_desc_slctr]); //$this->hlp_desc_cndtn
        /**
         *
         * Help Description Text
         *
         */
        $lr_hlp_desc_txt_slctr = $lr_flbl_slctr . ' .afeb-help-description-text';
        $this->CHelper->add_stl_sctn($this, 'ss7', esc_html__('Help Description Text', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'hlpdt_bg', $opt[0]);
            $this->CHelper->clr($obj, 'hlpdt_clr', $opt[0]);
            $this->CHelper->typo($obj, 'hlpdt_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'hlpdt_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'hlpdt_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'hlpdt_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'hlpdt_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'hlpdt_bx_shdo', $opt[0]);
        }, [$lr_hlp_desc_txt_slctr]); //$this->hlp_desc_cndtn
        /**
         *
         * Required Mark
         *
         */
        $lr_rqrd_mrk_slctr = $lr_flbl_slctr . ' .afeb-required-mark';
        $this->CHelper->add_stl_sctn($this, 'ss8', esc_html__('Required Mark', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'rqrd_mrk_clr', $opt[0]);
            $this->CHelper->typo($obj, 'rqrd_mrk_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'rqrd_mrk_mar', $opt[0]);
        }, [$lr_rqrd_mrk_slctr]); //$this->rqurd_mrk_cndtn
        /**
         *
         * Form Fields Icons
         *
         */
        $lr_ffi_slctr = $lr_ffg_slctr . ' .afeb-lr-form-control-box .afeb-lr-form-icon';
        $this->CHelper->add_stl_sctn($this, 'ss9', esc_html__('Form Fields Icons', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'ffi_bg', $opt[0]);
            $this->CHelper->clr($obj, 'ffi_clr', $opt[0] . '>i,' . $opt[0] . '>svg', esc_html__('Icon Color', 'addons-for-elementor-builder'));
            $slctr = [$opt[0] => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'ffi_bx_size', esc_html__('Box Size', 'addons-for-elementor-builder'), $slctr, ['px' => ['min' => 20, 'max' => 100]], ['px']);
            $slctr = [$opt[0] . '>i,' . $opt[0] . '>svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}}'];
            $this->CHelper->res_sldr($obj, 'ffi_ic_size', esc_html__('Icon Size', 'addons-for-elementor-builder'), $slctr, ['px' => ['min' => 15, 'max' => 80]], ['px']);
            $this->CHelper->res_mar($obj, 'ffi_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'ffi_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'ffi_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ffi_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'ffi_bx_shdo', $opt[0]);
        }, [$lr_ffi_slctr]);
        /**
         *
         * Form Fields Input
         *
         */
        $lr_ffinpt_slctr = $lr_ffg_slctr . ' .afeb-lr-form-control-box input';
        $this->CHelper->add_stl_sctn($this, 'ss10', esc_html__('Form Fields Input', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_clr($obj, 'ffinpt_bg', $opt[0]);
            $this->CHelper->clr($obj, 'ffinpt_clr', $opt[0]);
            $this->CHelper->clr($obj, 'ffinpt_phldr_clr', $opt[0] . '::placeholder', esc_html__('Placeholder Color', 'addons-for-elementor-builder'));
            $this->CHelper->typo($obj, 'ffinpt_typo', $opt[0]);
            $this->CHelper->typo($obj, 'ffinpt_plcdr_typo', $opt[0] . '::placeholder', esc_html__('Placeholder Typography', 'addons-for-elementor-builder'));
            $this->CHelper->res_mar($obj, 'ffinpt_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'ffinpt_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'ffinpt_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'ffinpt_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'ffinpt_bx_shdo', $opt[0]);
        }, [$lr_ffinpt_slctr]);
        /**
         *
         * Remember Me
         *
         */
        $lr_rmbr_slctr = $lr_box_slctr . ' .afeb-lr-form-remember-box';
        $this->CHelper->add_stl_sctn($this, 'ss11', esc_html__('Remember Me', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $slctr = $opt[0] . '>label::before';
            $aftr_slctr = $opt[0] . '>input[type=checkbox]:checked+label:after';
            $lbl_slctr = $opt[0] . '>label';
            $this->CHelper->bg_clr($obj, 'rmbr_bg', $slctr);
            $this->CHelper->cstm_clr($obj, 'rmbr_brdr_clr', $slctr, 'border-color: {{VALUE}}', esc_html__('Border Color', 'addons-for-elementor-builder'));
            $this->CHelper->cstm_clr($obj, 'rmbr_chk_clr', $aftr_slctr, 'border-color: {{VALUE}}', esc_html__('Check Mark Color', 'addons-for-elementor-builder'));
            $this->CHelper->clr($obj, 'rmbr_txt_clr', $lbl_slctr);
            $this->CHelper->typo($obj, 'rmbr_txt_typo', $lbl_slctr);
            $range = ['px' => ['min' => 2, 'max' => 15]];
            $this->CHelper->sldr($obj, 'rmbr_gap', esc_html__('Gap', 'addons-for-elementor-builder'), [$slctr => 'margin-right: {{SIZE}}{{UNIT}}'], $range);
            $this->CHelper->brdr_rdus($obj, 'rmbr_rdus', [$slctr => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
        }, [$lr_rmbr_slctr], [], $this->signin_cndtn);
        /**
         *
         * Button
         *
         */
        $lr_lgnbtn_slctr = $lr_box_slctr . ' .afeb-lr-form-submit-box input[name="afeb-login-submit"],' .
            $lr_box_slctr . ' .afeb-lr-form-submit-box input[name="afeb-register-submit"],' .
            $lr_box_slctr . ' .afeb-lr-form-submit-box input[name="afeb-lostpassword-submit"]';

        $this->CHelper->add_stl_sctn($this, 'ss12', esc_html__('Button', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('itms_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'lgnbtn_bg', $opt[0]);
                $this->CHelper->clr($obj, 'lgnbtn_clr', $opt[0]);
                $this->CHelper->typo($obj, 'lgnbtn_typo', $opt[0]);
                $slctr = [$opt[0] => 'width: {{SIZE}}{{UNIT}}'];
                $range = ['px' => ['min' => 0, 'max' => 1000, 'step' => 5]];
                $this->CHelper->res_sldr($obj, 'lgnbtn_wdt', esc_html__('Width', 'addons-for-elementor-builder'), $slctr, $range, CHelper::BDSU);
                $this->controls->responsive()->alignment('button_alignment', [
                    'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                    'options' => [
                        'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-left',],
                        'center' => ['title' => esc_html__('Center', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-center',],
                        'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-h-align-right',],
                    ],
                    'default' => 'center',
                    'selectors' => ['{{WRAPPER}} .afeb-lr-form-submit-box' => 'text-align: {{VALUE}};',],
                ]);
                $this->CHelper->res_mar($obj, 'lgnbtn_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'lgnbtn_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'lgnbtn_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'lgnbtn_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
                $this->CHelper->bx_shdo($obj, 'lgnbtn_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'lgnbtn_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'lgnbtn_clr_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'lgnbtn_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'lgnbtn_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'lgnbtn_rdus_hvr', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
                $this->CHelper->bx_shdo($obj, 'lgnbtn_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$lr_lgnbtn_slctr], []);
        /**
         *
         * Form Switching Link
         *
         */
        $lr_lpfrm_lnk_slctr = $lr_box_slctr . ' .afeb-show-signup-form,' .
            $lr_box_slctr . ' .afeb-show-signin-form,' .
            $lr_box_slctr . ' .afeb-show-lp-form';

        $this->CHelper->add_stl_sctn($this, 'ss14', esc_html__('Form Switching Link', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('itms_stl_tbs_6');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'lpfrm_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'lpfrm_lnk_bg', $opt[0]);
                $this->CHelper->clr($obj, 'lpfrm_lnk_clr', $opt[0]);
                $this->CHelper->typo($obj, 'lpfrm_lnk_typo', $opt[0]);
                $this->CHelper->res_mar($obj, 'lpfrm_lnk_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'lpfrm_lnk_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'lpfrm_lnk_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'lpfrm_lnk_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
                $this->CHelper->bx_shdo($obj, 'lpfrm_lnk_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'lpfrm_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'lpfrm_lnk_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'lpfrm_lnk_clr_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'lpfrm_lnk_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'lpfrm_lnk_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'lpfrm_lnk_rdus_hvr', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
                $this->CHelper->bx_shdo($obj, 'lpfrm_lnk_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$lr_lpfrm_lnk_slctr], ['lp_itm_sh_lnk' => 'yes']);
        /**
         *
         * Error Message Box
         *
         */
        $err_msg_bx_slctr = $lr_box_slctr . ' .afeb-lr-form-err-box';
        $this->CHelper->add_stl_sctn($this, 'ss15', esc_html__('Error Message Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->raw_html(
                $obj,
                'err_msg_bx',
                esc_html__('For a better view of the changes. You can enable the error message box preview mode from Content Tab » Validation Messages Section » Error Messages » Preview In Editor', 'addons-for-elementor-builder'),
                'elementor-panel-alert elementor-panel-alert-info',
                ['err_msg_bx_prev!' => 'yes']
            );
            $this->CHelper->bg_grp_ctrl($obj, 'err_msg_bx_bg', $opt[0]);
            $this->CHelper->clr($obj, 'err_msg_bx_clr', $opt[0]);
            $this->CHelper->typo($obj, 'err_msg_bx_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'err_msg_bx_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'err_msg_bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'err_msg_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'err_msg_bx_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'err_msg_bx_shdo', $opt[0]);
        }, [$err_msg_bx_slctr]);
        /**
         *
         * Success Message Box
         *
         */
        $succ_msg_bx_slctr = $lr_box_slctr . ' .afeb-lr-form-succ-box';
        $this->CHelper->add_stl_sctn($this, 'ss16', esc_html__('Success Message Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->raw_html(
                $obj,
                'succ_msg_bx',
                esc_html__('For a better view of the changes. You can enable the success message box preview mode from Content Tab » Validation Messages Section » Success Messages » Preview In Editor', 'addons-for-elementor-builder'),
                'elementor-panel-alert elementor-panel-alert-info',
                ['succ_msg_bx_prev!' => 'yes']
            );
            $this->CHelper->bg_grp_ctrl($obj, 'succ_msg_bx_bg', $opt[0]);
            $this->CHelper->clr($obj, 'succ_msg_bx_clr', $opt[0]);
            $this->CHelper->typo($obj, 'succ_msg_bx_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'succ_msg_bx_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'succ_msg_bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'succ_msg_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'succ_msg_bx_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], CHelper::BDSU);
            $this->CHelper->bx_shdo($obj, 'succ_msg_bx_shdo', $opt[0]);
        }, [$succ_msg_bx_slctr]);
    }

    /**
     * Render LoginRegister widget output on the frontend
     *
     * @since 1.0.3
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $options = ['page_id' => $this->page_id, 'widget_id' => $this->widget_id, 'frm_typ' => $settings['frm_typ']];

        $this->lr_output($settings, $options);
    }

    /**
     * Print the final output of the forms
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    public function lr_output($settings = [], $options = [])
    {
        // Classes
        $classes = [];
        $classes[] = 'afeb-lr-box';
        $classes[] = 'afeb-' . $options['frm_typ'] . '-form-box';
        $classes[] = $this->is_editor == 'yes' && $settings['reg_frm_prev'] == 'yes' ? 'afeb-prev-reg-frm' : '';
        $classes[] = $this->is_editor == 'yes' && $settings['lgn_frm_prev'] == 'yes' ? 'afeb-prev-lgn-frm' : '';
        $classes[] = $this->is_editor == 'yes' && $settings['lp_frm_prev'] == 'yes' ? 'afeb-prev-lp-frm' : '';
        $classes[] = 'afeb-lr-style-1';
?>
        <div class="<?php echo implode(' ', array_filter($classes, 'esc_attr')); ?>">
            <?php
            if ((is_user_logged_in() && $this->is_editor != 'yes') || !empty($settings['lgnin_frm_prev'])) {
                $this->after_logged_in($settings, $options);
            } else if (!empty($_GET['afeb-reset-password']) || ($this->is_editor == 'yes' && !empty($settings['crp_frm_prev']))) {
                if (($this->is_editor == 'yes' && !empty($settings['crp_frm_prev']))) {
                    $this->reset_password_form($settings, $options);
                } else {
                    $options['reset_password'] = true;
                    $key = $user_login = 'null';
                    $options['err_msg'] = '';

                    if (isset($_GET['afeb-key'])) $key = sanitize_text_field(wp_unslash($_GET['afeb-key']));
                    if (isset($_GET['afeb-login'])) $user_login = sanitize_text_field(wp_unslash($_GET['afeb-login']));

                    $user = check_password_reset_key($key, $user_login);
                    if (is_wp_error($user)) {
                        if ($user->get_error_code() === 'expired_key') {
                            $options['err_msg'] = !empty($settings['err_rp_key_exprd']) ? esc_html($settings['err_rp_key_exprd']) : esc_html__('Your password reset link appears to be invalid. Please request a new link', 'addons-for-elementor-builder');
                        } else {
                            $code = $user->get_error_code();
                            if (empty($code)) $code = '00';

                            /* translators: %s: Error Code */
                            $options['err_msg'] = sprintf(esc_html__('That key is no longer valid. Please reset your password again. Code: %s', 'addons-for-elementor-builder'), $code);
                        }
                    }

                    $this->reset_password_form($settings, $options);
                }
            } else {
                $db_opts = get_option('afeb-settings', []);
                $lr_db_opts = !empty($db_opts['widgets']['login_register']) ?
                    $db_opts['widgets']['login_register'] : [];

                $settings['lgn_url'] = wp_login_url();
                $settings['reg_url'] = wp_registration_url();
                $settings['lp_url'] = wp_lostpassword_url();
                $settings['lp_lgn_url'] = $settings['lgn_url'];

                $settings['lgn_atts'] = '';
                $settings['reg_atts'] = '';
                $settings['lp_atts'] = '';
                $settings['lp_lgn_atts'] = '';

                $settings['sgnin_frm'] = '';
                $settings['sgnup_frm'] = '';
                $settings['lp_frm'] = '';
                $settings['lp_sgnin_frm'] = '';

                switch ($settings['reg_itm_sgnin_act_lbc'] ?? '') {
                    case 'custom_url':
                        $settings['lgn_url'] = $settings['reg_itm_sgnin_cstm_url']['url'] ?? '';
                        $settings['lgn_atts'] = isset($settings['reg_itm_sgnin_cstm_url']['is_external']) ? ' target="_blank"' : '';
                        $settings['lgn_atts'] .= isset($settings['reg_itm_sgnin_cstm_url']['nofollow']) ? ' rel="nofollow"' : '';
                        break;
                    case 'signin_form':
                        $settings['lgn_url'] = '';
                        $settings['sgnin_frm'] = 'afeb-show-signin-form';
                        break;
                }

                switch ($settings['lgn_itm_sgnup_act_lbc'] ?? '') {
                    case 'custom_url':
                        $settings['reg_url'] = $settings['lgn_itm_sgnup_cstm_url']['url'] ?? '';
                        $settings['reg_atts'] = isset($settings['lgn_itm_sgnup_cstm_url']['is_external']) ? ' target="_blank"' : '';
                        $settings['reg_atts'] .= isset($settings['lgn_itm_sgnup_cstm_url']['nofollow']) ? ' rel="nofollow"' : '';
                        break;
                    case 'signup_form':
                        $settings['reg_url'] = '';
                        $settings['sgnup_frm'] = 'afeb-show-signup-form';
                        break;
                    default:
                        if (
                            isset($lr_db_opts['default_register_page']) &&
                            intval($lr_db_opts['default_register_page']) == get_the_ID()
                        ) {
                            $lr_db_opts['default_register_page'] = '';
                            $db_opts['widgets']['login_register'] = $lr_db_opts;
                            update_option('afeb-settings', $db_opts);
                        }
                        break;
                }

                switch ($settings['lp_itm_act_lbc'] ?? '') {
                    case 'custom_url':
                        $settings['lp_url'] = $settings['lp_itm_cstm_url']['url'] ?? '';
                        $settings['lp_atts'] = isset($settings['lp_itm_cstm_url']['is_external']) ? ' target="_blank"' : '';
                        $settings['lp_atts'] .= isset($settings['lp_itm_cstm_url']['nofollow']) ? ' rel="nofollow"' : '';
                        break;
                    case 'lp_form':
                        $settings['lp_url'] = '';
                        $settings['lp_frm'] = 'afeb-show-lp-form';
                        break;
                    default:
                        if (
                            isset($lr_db_opts['default_lostpass_page']) &&
                            intval($lr_db_opts['default_lostpass_page']) == get_the_ID()
                        ) {
                            $lr_db_opts['default_lostpass_page'] = '';
                            $db_opts['widgets']['login_register'] = $lr_db_opts;
                            update_option('afeb-settings', $db_opts);
                        }
                        break;
                }

                switch ($settings['lp_itm_sgnin_act_lbc'] ?? '') {
                    case 'custom_url':
                        $settings['lp_lgn_url'] = $settings['lp_itm_sgnin_cstm_url']['url'] ?? '';
                        $settings['lp_lgn_atts'] = isset($settings['lp_itm_sgnin_cstm_url']['is_external']) ? ' target="_blank"' : '';
                        $settings['lp_lgn_atts'] .= isset($settings['lp_itm_sgnin_cstm_url']['nofollow']) ? ' rel="nofollow"' : '';
                        break;
                    case 'signin_form':
                        $settings['lp_lgn_url'] = '';
                        $settings['lp_sgnin_frm'] = 'afeb-show-signin-form';
                        break;
                    default:
                        if (
                            isset($lr_db_opts['default_login_page']) &&
                            intval($lr_db_opts['default_login_page']) == get_the_ID()
                        ) {
                            $lr_db_opts['default_login_page'] = '';
                            $db_opts['widgets']['login_register'] = $lr_db_opts;
                            update_option('afeb-settings', $db_opts);
                        }
                        break;
                }

                if ($options['frm_typ'] == 'login') {
                    $options['show_login_frm'] = (!empty($_GET['afeb-register']) || !empty($_GET['afeb-lostpassword'])) ? 'display:none;' : '';

                    if (((
                        isset($lr_db_opts['default_register_page']) &&
                        intval($lr_db_opts['default_register_page']) == get_the_ID() &&
                        !empty($_GET['afeb-register']) &&
                        $settings['lgn_itm_sgnup_act_lbc'] == 'custom_url'
                    ) || (
                        isset($lr_db_opts['default_lostpass_page']) &&
                        intval($lr_db_opts['default_lostpass_page']) == get_the_ID() &&
                        !empty($_GET['afeb-lostpassword']) &&
                        $settings['lp_itm_act_lbc'] == 'custom_url'
                    ))):
                        $options['show_login_frm'] = '';
                    endif;

                    $this->login_form($settings, $options);
                    if ($settings['sgnup_frm'] == 'afeb-show-signup-form') :
                        $options['frm_typ'] = 'register';
                        $options['show_reg_frm'] = !empty($_GET['afeb-register']) ? 'display:block;' : '';
                        $this->register_form($settings, $options);
                    endif;
                    if ($settings['lp_frm'] == 'afeb-show-lp-form') :
                        $options['frm_typ'] = 'lostpassword';
                        $options['show_lp_frm'] = !empty($_GET['afeb-lostpassword']) ? 'display:block;' : '';
                        $this->lost_password_form($settings, $options);
                    endif;
                } else if ($options['frm_typ'] == 'register') {
                    $options['show_reg_frm'] = (!empty($_GET['afeb-login']) || !empty($_GET['afeb-lostpassword'])) ? 'display:none;' : '';

                    if (
                        isset($lr_db_opts['default_lostpass_page']) &&
                        intval($lr_db_opts['default_lostpass_page']) == get_the_ID() &&
                        !empty($_GET['afeb-lostpassword']) &&
                        $settings['lp_itm_act_lbc'] == 'custom_url'
                    ):
                        $options['show_reg_frm'] = '';
                    endif;

                    $this->register_form($settings, $options);
                    if ($settings['sgnin_frm'] == 'afeb-show-signin-form') :
                        $options['frm_typ'] = 'login';
                        $options['show_login_frm'] = !empty($_GET['afeb-login']) ? 'display:block;' : '';
                        $this->login_form($settings, $options);
                        if ($settings['lp_frm'] == 'afeb-show-lp-form') :
                            $options['frm_typ'] = 'lostpassword';
                            $options['show_lp_frm'] = !empty($_GET['afeb-lostpassword']) ? 'display:block;' : '';
                            $this->lost_password_form($settings, $options);
                        endif;
                    endif;
                } else if ($options['frm_typ'] == 'lostpassword') {
                    $options['show_lp_frm'] = (!empty($_GET['afeb-login']) || !empty($_GET['afeb-register'])) ? 'display:none;' : '';

                    if (
                        isset($lr_db_opts['default_register_page']) &&
                        intval($lr_db_opts['default_register_page']) == get_the_ID() &&
                        !empty($_GET['afeb-register']) &&
                        $settings['lgn_itm_sgnup_act_lbc'] == 'custom_url'
                    ):
                        $options['show_lp_frm'] = '';
                    endif;

                    $this->lost_password_form($settings, $options);
                    if ($settings['lp_sgnin_frm'] == 'afeb-show-signin-form') {
                        $options['frm_typ'] = 'login';
                        $options['show_login_frm'] = !empty($_GET['afeb-login']) ? 'display:block;' : '';
                        $this->login_form($settings, $options);
                        if (
                            !empty($settings['lgn_itm_sh_sgnup_lnk']) &&
                            (isset($settings['lgn_itm_sgnup_act_lbc']) &&
                                $settings['lgn_itm_sgnup_act_lbc'] == 'signup_form')
                        ) {
                            $options['frm_typ'] = 'register';
                            $options['show_reg_frm'] = !empty($_GET['afeb-register']) ? 'display:block;' : '';
                            $this->register_form($settings, $options);
                        }
                    }
                }
            }
            ?>
        </div>
    <?php
    }

    /**
     * Print the necessary fields on the page
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    public function common_hidden_fields_required($settings = [], $options = [])
    {
        $options['frm_typ'] = isset($options['reset_password']) ? 'resetpassword' : $options['frm_typ'];
        wp_nonce_field('afeb-' . esc_attr($options['frm_typ']) . '-action', 'afeb-' . esc_attr(str_replace('_', '-', $options['frm_typ'])) . '-nonce');
    ?>
        <input type="hidden" name="page_id" value="<?php echo esc_attr($options['page_id']); ?>">
        <input type="hidden" name="widget_id" value="<?php echo esc_attr($options['widget_id']); ?>">
        <?php
        $redirect_to_prev_page = '';
        $raw_referer = wp_get_raw_referer();
        if (empty($raw_referer) && isset($_SERVER['HTTP_REFERER'])) {
            $raw_referer = wp_unslash($_SERVER['HTTP_REFERER']);
        }
        if (!empty($raw_referer)) {
            $redirect_to_prev_page = sanitize_url($raw_referer);
        }

        $afeb_key = !empty($_GET['afeb-key']) ? sanitize_text_field(wp_unslash($_GET['afeb-key'])) : '';
        $afeb_login = !empty($_GET['afeb-login']) ? sanitize_text_field(wp_unslash($_GET['afeb-login'])) : '';
        ?>
        <input type="hidden" name="<?php echo esc_attr('afeb-redirect-to-prev-page'); ?>"
            value="<?php echo esc_attr($redirect_to_prev_page); ?>">
        <?php if (!empty($afeb_key)): ?>
            <input type="hidden" name="afeb-key" value="<?php echo esc_attr($afeb_key); ?>">
        <?php endif; ?>
        <?php if (!empty($afeb_login)): ?>
            <input type="hidden" name="afeb-login" value="<?php echo esc_attr($afeb_login); ?>">
        <?php endif; ?>
        <div class="afeb-lr-form-submit-box">
            <input type="submit" name="afeb-<?php echo esc_attr($options['frm_typ']); ?>-submit"
                value="<?php echo esc_attr($settings[$options['frm_typ'] . '_itm_btn_txt']); ?>">
        </div>
    <?php
    }

    /**
     * Display the login form
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    public function login_form($settings = [], $options = [])
    { ?>
        <form id="afeb-login-form-<?php echo esc_attr($options['widget_id']); ?>" class="afeb-login-form" action=""
            method="post" style="<?php echo esc_attr($options['show_login_frm']); ?>">
            <?php foreach ($settings['lgn_itms_rpt'] as $item): ?>
                <div class="afeb-lr-form-group <?php printf('elementor-repeater-item-%s', esc_attr($item['_id'])); ?>">
                    <?php if (!empty($item['lgn_itm_lbl'])): ?>
                        <label for="afeb-login-<?php echo esc_attr(strtolower($item['lgn_itms'])); ?>">
                            <?php echo esc_html($item['lgn_itm_lbl']); ?>
                            <?php if (!empty($item['lgn_itm_hlp_desc'])): ?><span
                                    class="afeb-help-description far fa-question-circle"></span><?php endif; ?>
                            <?php if (!empty($item['lgn_itm_sh_rqurd'])): ?>
                                <span class="afeb-required-mark">*</span>
                            <?php endif; ?>
                            <?php if (!empty($item['lgn_itm_hlp_desc_txt'])): ?>
                                <div
                                    class="afeb-help-description-text"><?php echo esc_html($item['lgn_itm_hlp_desc_txt']); ?></div><?php endif; ?>
                        </label>
                    <?php endif; ?>
                    <?php $placeholder = !empty($item['lgn_itm_plc_hldr']) ? 'placeholder="' . esc_attr($item['lgn_itm_plc_hldr']) . '"' : ''; ?>
                    <div class="afeb-lr-form-control-box">
                        <?php $type = $item['lgn_itms'] == 'Password' ? 'password' : 'text'; ?>
                        <span class="afeb-lr-form-icon"><?php Icons_Manager::render_icon($item['lgn_itm_ic']); ?></span>
                        <input type="<?php echo esc_attr($type); ?>"
                            name="afeb-login-<?php echo esc_attr(strtolower($item['lgn_itms'])); ?>"
                            id="afeb-login-<?php echo esc_attr(strtolower($item['lgn_itms'])); ?>"
                            class="afeb-lr-form-control" <?php echo $placeholder; ?>>
                        <?php if (!empty($item['lgn_itm_pv_ic'])): ?>
                            <span class="afeb-password-visibility far fa-eye"></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php $this->security_input_field($settings); ?>
            <?php if (!empty($settings['lgn_itm_sh_rm'])): ?>
                <div class="afeb-lr-form-remember-box">
                    <input id="afeb-rememberme" type="checkbox" name="afeb-rememberme"
                        value="forever" <?php echo !empty($settings['lgn_itm_rm_chkd']) ? 'checked' : ''; ?>>
                    <label for="afeb-rememberme"><?php echo esc_html($settings['lgn_itm_rm_txt']); ?></label>
                </div>
            <?php endif; ?>
            <?php $this->common_hidden_fields_required($settings, $options); ?>
            <div class="afeb-lr-form-footer-box">
                <?php if (!empty($settings['lgn_itm_sh_sgnup_lnk']) && !empty($settings['lgn_itm_sgnup_txt'])): ?>
                    <a <?php echo $settings['sgnup_frm'] ? 'class="' . esc_attr($settings['sgnup_frm']) . '"' : ''; ?><?php echo $settings['reg_url'] ? 'href="' . esc_url($settings['reg_url']) . '"' : ''; ?><?php echo esc_attr($settings['reg_atts']); ?>><?php echo esc_html($settings['lgn_itm_sgnup_txt']); ?></a>
                <?php endif; ?>
                <?php if ($settings['frm_typ'] != 'login' && empty($settings['lgn_itm_sh_sgnup_lnk']) && !empty($settings['reg_itm_sgnin_back_btn_txt']) && empty($settings['lgn_frm_prev'])): ?>
                    <a class="afeb-show-signup-form"><?php echo esc_html($settings['reg_itm_sgnin_back_btn_txt']); ?></a>
                <?php endif; ?>
                <?php if (!empty($settings['lp_itm_sh_lnk']) && !empty($settings['lp_itm_txt'])): ?>
                    <a <?php echo $settings['lp_frm'] ? 'class="' . esc_attr($settings['lp_frm']) . '"' : ''; ?><?php echo $settings['lp_url'] ? 'href="' . esc_url($settings['lp_url']) . '"' : ''; ?><?php echo esc_attr($settings['lp_atts']); ?>><?php echo esc_html($settings['lp_itm_txt']); ?></a>
                <?php endif; ?>
                <?php if ($settings['frm_typ'] != 'login' && !empty($settings['lp_itm_sgnin_back_btn_txt']) && empty($settings['lp_itm_sh_lnk']) && empty($settings['lgn_frm_prev'])): ?>
                    <a class="afeb-show-lp-form"><?php echo esc_html($settings['lp_itm_sgnin_back_btn_txt']); ?></a>
                <?php endif; ?>
            </div>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['succ_msg_bx_prev'])))
                $_COOKIE['afeb_login_success_' . $options['widget_id']] = CHelper::$LIM;
            $login_success_cookie_key = 'afeb_login_success_' . $options['widget_id'];
            $login_success_message = '';
            if (!empty($_COOKIE[$login_success_cookie_key])) {
                $login_success_message = sanitize_text_field(wp_unslash($_COOKIE[$login_success_cookie_key]));
            }
            if (!empty($login_success_message)): ?>
                <div class="afeb-lr-form-succ-box">
                    <?php echo esc_html($login_success_message); ?>
                </div>
            <?php endif; ?>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['err_msg_bx_prev'])))
                $_COOKIE['afeb_login_error_' . $options['widget_id']] = CHelper::$LIM;
            $login_error_cookie_key = 'afeb_login_error_' . $options['widget_id'];
            $login_error_message = '';
            if (!empty($_COOKIE[$login_error_cookie_key])) {
                $login_error_message = sanitize_text_field(wp_unslash($_COOKIE[$login_error_cookie_key]));
            }
            if (!empty($login_error_message)) :
            ?>
                <div class="afeb-lr-form-err-box">
                    <?php echo esc_html($login_error_message); ?>
                </div>
            <?php endif; ?>
        </form>
    <?php
    }

    /**
     * Display the register form
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    public function register_form($settings = [], $options = [])
    {
        $str_fields = '';
        if (isset($settings['reg_itms_rpt'])) {
            $fields = $settings['reg_itms_rpt'];
            $errormsg = '';

            foreach ($fields as $field) :
                $field_value = strtolower($field['reg_itms']);

                if ($field_value == 'confirmemail') {
                    $field_value = 'cfmail';
                } else if ($field_value == 'confirmpassword') {
                    $field_value = 'cfmpass';
                }
                $str_fields .= $field_value;
            endforeach;
            foreach ($fields as $field) :
                $field_value = strtolower($field['reg_itms']);

                if ($field_value == 'confirmemail') {
                    $field_value = 'cfmail';
                } else if ($field_value == 'confirmpassword') {
                    $field_value = 'cfmpass';
                }
                if (preg_match_all('/username/', $str_fields) < 1 || preg_match_all('/email/', $str_fields) < 1) : $errormsg = esc_html__('The username and email fields are required and cannot be removed.', 'addons-for-elementor-builder');
                    break;
                endif;
                if (preg_match_all('/' . $field_value . '/', $str_fields) > 1) :
                    $errormsg = esc_html__('You are not allowed to use a duplicate field.', 'addons-for-elementor-builder');
                    break;
                endif;
            endforeach;
            if ($errormsg != '') :
                echo wp_kses(Helper::front_notice($errormsg, 'error'), Helper::allowed_tags());
                return;
            endif;
        } else {
            echo wp_kses(Helper::front_notice(esc_html__('There is no field to display in the registration form.', 'addons-for-elementor-builder'), 'error'), Helper::allowed_tags());
            return;
        }
    ?>
        <form id="afeb-register-form-<?php echo esc_attr($options['widget_id']); ?>" class="afeb-register-form"
            action="" method="post" style="<?php echo esc_attr($options['show_reg_frm']); ?>">
            <?php foreach ($settings['reg_itms_rpt'] as $item) : ?>
                <div class="afeb-lr-form-group <?php printf('elementor-repeater-item-%s', esc_attr($item['_id'])); ?>">
                    <?php $fields_type = strtolower($item['reg_itms']); ?>
                    <?php if (!empty($item['reg_itm_lbl'])) : ?>
                        <label for="afeb-register-<?php echo esc_attr($fields_type); ?>">
                            <?php echo esc_html($item['reg_itm_lbl']); ?>
                            <?php if (!empty($item['reg_itm_hlp_desc'])): ?><span
                                    class="afeb-help-description far fa-question-circle"></span><?php endif; ?>
                            <?php if (!empty($item['reg_itm_sh_rqurd'])) : ?>
                                <span class="afeb-required-mark">*</span>
                            <?php endif; ?>
                            <?php if (!empty($item['reg_itm_hlp_desc_txt'])): ?>
                                <div
                                    class="afeb-help-description-text"><?php echo esc_html($item['reg_itm_hlp_desc_txt']); ?></div><?php endif; ?>
                        </label>
                    <?php endif; ?>
                    <?php $placeholder = !empty($item['reg_itm_plc_hldr']) ? 'placeholder="' . esc_attr($item['reg_itm_plc_hldr']) . '"' : ''; ?>
                    <div class="afeb-lr-form-control-box">
                        <span class="afeb-lr-form-icon"><?php Icons_Manager::render_icon($item['reg_itm_ic']); ?></span>
                        <span class="afeb-lr-form-input-box">
                            <input
                                type="<?php echo ($fields_type == 'password' || $fields_type == 'confirmpassword') ? 'password' : 'text'; ?>"
                                name="afeb-register-<?php echo esc_attr($fields_type); ?>"
                                id="afeb-register-<?php echo esc_attr($fields_type); ?>"
                                class="afeb-lr-form-control" <?php echo $placeholder; ?>>
                        </span>
                        <?php if (!empty($item['reg_itm_pv_ic'])) : ?>
                            <span class="afeb-password-visibility far fa-eye"></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php $this->security_input_field($settings); ?>
            <?php $this->common_hidden_fields_required($settings, $options); ?>
            <div class="afeb-lr-form-footer-box">
                <?php if (!empty($settings['reg_itm_sh_sgnin_lnk'])) : ?>
                    <a <?php echo $settings['sgnin_frm'] ? 'class="' . esc_attr($settings['sgnin_frm']) . '"' : ''; ?><?php echo $settings['lgn_url'] ? 'href="' . esc_url($settings['lgn_url']) . '"' : ''; ?><?php echo esc_attr($settings['lgn_atts']); ?>><?php echo esc_html($settings['reg_itm_sgnin_txt']); ?></a>
                <?php endif; ?>
                <?php if ($settings['frm_typ'] != 'register' && !empty($settings['lgn_itm_sgnup_back_btn_txt']) && empty($settings['reg_itm_sh_sgnin_lnk']) && empty($settings['reg_frm_prev'])) : ?>
                    <a class="afeb-show-signin-form"><?php echo esc_html($settings['lgn_itm_sgnup_back_btn_txt']); ?></a>
                <?php endif; ?>
            </div>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['succ_msg_bx_prev'])))
                $_COOKIE['afeb_register_success_' . $options['widget_id']] = CHelper::$LIM;
            $register_success_cookie_key = 'afeb_register_success_' . $options['widget_id'];
            $register_success_message = '';
            if (!empty($_COOKIE[$register_success_cookie_key])) {
                $register_success_message = sanitize_text_field(wp_unslash($_COOKIE[$register_success_cookie_key]));
            }
            if (!empty($register_success_message)) : ?>
                <div class="afeb-lr-form-succ-box">
                    <?php echo esc_html($register_success_message); ?>
                </div>
            <?php endif; ?>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['err_msg_bx_prev'])))
                $_COOKIE['afeb_register_error_' . $options['widget_id']] = CHelper::$LIM;
            $register_error_cookie_key = 'afeb_register_error_' . $options['widget_id'];
            $register_error_message = '';
            if (!empty($_COOKIE[$register_error_cookie_key])) {
                $register_error_message = sanitize_text_field(wp_unslash($_COOKIE[$register_error_cookie_key]));
            }
            if (!empty($register_error_message)) : ?>
                <div class="afeb-lr-form-err-box">
                    <?php echo esc_html($register_error_message); ?>
                </div>
            <?php endif; ?>
        </form>
    <?php
    }

    /**
     * Display the lost password form
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    public function lost_password_form($settings = [], $options = [])
    {
    ?>
        <form id="afeb-lostpassword-form-<?php echo esc_attr($options['widget_id']); ?>" class="afeb-lostpassword-form"
            action="" method="post" style="<?php echo esc_attr($options['show_lp_frm']); ?>">
            <div class="afeb-lr-form-group">
                <?php if (!empty($settings['lp_itm_lbl'])) : ?>
                    <label for="afeb-lostpassword-username-email">
                        <?php echo esc_html($settings['lp_itm_lbl']); ?>
                        <?php if (!empty($settings['lp_itm_hlp_desc'])): ?><span
                                class="afeb-help-description far fa-question-circle"></span><?php endif; ?>
                        <?php if (!empty($settings['lp_itm_sh_requrd'])) : ?>
                            <span class="afeb-required-mark">*</span>
                        <?php endif; ?>
                        <?php if (!empty($settings['lp_itm_hlp_desc_txt'])): ?>
                            <div
                                class="afeb-help-description-text"><?php echo esc_html($settings['lp_itm_hlp_desc_txt']); ?></div><?php endif; ?>
                    </label>
                <?php endif; ?>
                <?php $placeholder = !empty($settings['lp_itm_plc_hldr']) ? 'placeholder="' . esc_attr($settings['lp_itm_plc_hldr']) . '"' : ''; ?>
                <div class="afeb-lr-form-control-box">
                    <span class="afeb-lr-form-icon"><?php Icons_Manager::render_icon($settings['lp_itm_ic']); ?></span>
                    <input type="text" name="afeb-lostpassword-username-email" id="afeb-lostpassword-username-email"
                        class="afeb-lr-form-control" <?php echo $placeholder; ?>>
                </div>
            </div>
            <?php $this->security_input_field($settings); ?>
            <?php $this->common_hidden_fields_required($settings, $options); ?>
            <div class="afeb-lr-form-footer-box">
                <?php if (!empty($settings['lp_itm_sh_sgnin_lnk'])) : ?>
                    <a <?php echo $settings['lp_sgnin_frm'] ? 'class="' . esc_attr($settings['lp_sgnin_frm']) . '"' : ''; ?><?php echo $settings['lp_lgn_url'] ? 'href="' . esc_url($settings['lp_lgn_url']) . '"' : ''; ?><?php echo esc_attr($settings['lp_lgn_atts']); ?>><?php echo esc_html($settings['lp_itm_sgnin_txt']); ?></a>
                <?php endif; ?>
                <?php if ($settings['frm_typ'] != 'lostpassword' && !empty($settings['lp_itm_back_btn_txt']) && empty($settings['lp_itm_sh_sgnin_lnk']) && empty($settings['lp_frm_prev'])) : ?>
                    <a class="afeb-lp-show-signin-form"><?php echo esc_html($settings['lp_itm_back_btn_txt']); ?></a>
                <?php endif; ?>
            </div>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['succ_msg_bx_prev'])))
                $_COOKIE['afeb_lostpassword_success_' . $options['widget_id']] = CHelper::$LIM;
            $lostpassword_success_cookie_key = 'afeb_lostpassword_success_' . $options['widget_id'];
            $lostpassword_success_message = '';
            if (!empty($_COOKIE[$lostpassword_success_cookie_key])) {
                $lostpassword_success_message = sanitize_text_field(wp_unslash($_COOKIE[$lostpassword_success_cookie_key]));
            }
            if (!empty($lostpassword_success_message)) : ?>
                <div class="afeb-lr-form-succ-box">
                    <?php echo esc_html($lostpassword_success_message); ?>
                </div>
            <?php endif; ?>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['err_msg_bx_prev'])))
                $_COOKIE['afeb_lostpassword_error_' . $options['widget_id']] = CHelper::$LIM;
            $lostpassword_error_cookie_key = 'afeb_lostpassword_error_' . $options['widget_id'];
            $lostpassword_error_message = '';
            if (!empty($_COOKIE[$lostpassword_error_cookie_key])) {
                $lostpassword_error_message = sanitize_text_field(wp_unslash($_COOKIE[$lostpassword_error_cookie_key]));
            }
            if (!empty($lostpassword_error_message)) : ?>
                <div class="afeb-lr-form-err-box">
                    <?php echo esc_html($lostpassword_error_message); ?>
                </div>
            <?php endif; ?>
        </form>
    <?php
    }

    /**
     * Display the reset password form
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    public function reset_password_form($settings = [], $options = [])
    {
    ?>
        <form id="afeb-resetpassword-form-<?php echo esc_attr($options['widget_id']); ?>"
            class="afeb-resetpassword-form" action="" method="post">
            <?php if (empty($options['err_msg'])): ?>
                <div class="afeb-lr-form-group">
                    <?php if (!empty($settings['crp_np_itm_lbl'])) : ?>
                        <label for="afeb-resetpassword-new-password">
                            <?php echo esc_html($settings['crp_np_itm_lbl']); ?>
                            <?php if (!empty($settings['crp_np_itm_hlp_desc'])): ?><span
                                    class="afeb-help-description far fa-question-circle"></span><?php endif; ?>
                            <?php if (!empty($settings['crp_np_itm_sh_requrd'])) : ?>
                                <span class="afeb-required-mark">*</span>
                            <?php endif; ?>
                            <?php if (!empty($settings['crp_np_itm_hlp_desc_txt'])): ?>
                                <div
                                    class="afeb-help-description-text"><?php echo esc_html($settings['crp_np_itm_hlp_desc_txt']); ?></div><?php endif; ?>
                        </label>
                    <?php endif; ?>
                    <?php $placeholder = !empty($settings['crp_np_itm_plc_hldr']) ? 'placeholder="' . esc_attr($settings['crp_np_itm_plc_hldr']) . '"' : ''; ?>
                    <div class="afeb-lr-form-control-box">
                        <span
                            class="afeb-lr-form-icon"><?php Icons_Manager::render_icon($settings['crp_np_itm_ic']); ?></span>
                        <input type="password" name="afeb-resetpassword-new-password"
                            id="afeb-resetpassword-new-password"
                            class="afeb-lr-form-control" <?php echo $placeholder; ?>>
                        <?php if (!empty($settings['crp_np_itm_pv_ic'])) : ?>
                            <span class="afeb-password-visibility far fa-eye"></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="afeb-lr-form-group">
                    <?php if (!empty($settings['crp_cnp_itm_lbl'])) : ?>
                        <label for="afeb-resetpassword-confirm-new-password">
                            <?php echo esc_html($settings['crp_cnp_itm_lbl']); ?>
                            <?php if (!empty($settings['crp_cnp_itm_hlp_desc'])): ?><span
                                    class="afeb-help-description far fa-question-circle"></span><?php endif; ?>
                            <?php if (!empty($settings['crp_cnp_itm_sh_requrd'])) : ?>
                                <span class="afeb-required-mark">*</span>
                            <?php endif; ?>
                            <?php if (!empty($settings['crp_cnp_itm_hlp_desc_txt'])): ?>
                                <div
                                    class="afeb-help-description-text"><?php echo esc_html($settings['crp_cnp_itm_hlp_desc_txt']); ?></div><?php endif; ?>
                        </label>
                    <?php endif; ?>
                    <?php $placeholder = !empty($settings['crp_cnp_itm_plc_hldr']) ? 'placeholder="' . esc_attr($settings['crp_cnp_itm_plc_hldr']) . '"' : ''; ?>
                    <div class="afeb-lr-form-control-box">
                        <span
                            class="afeb-lr-form-icon"><?php Icons_Manager::render_icon($settings['crp_cnp_itm_ic']); ?></span>
                        <input type="password" name="afeb-resetpassword-confirm-new-password"
                            id="afeb-resetpassword-confirm-new-password"
                            class="afeb-lr-form-control" <?php echo $placeholder; ?>>
                    </div>
                </div>
                <?php $this->security_input_field($settings); ?>
                <?php $this->common_hidden_fields_required($settings, $options); ?>
                <div class="afeb-lr-form-footer-box"></div>
            <?php endif; ?>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['succ_msg_bx_prev'])))
                $_COOKIE['afeb_resetpassword_success_' . $options['widget_id']] = CHelper::$LIM;
            $resetpassword_success_cookie_key = 'afeb_resetpassword_success_' . $options['widget_id'];
            $resetpassword_success_message = '';
            if (!empty($_COOKIE[$resetpassword_success_cookie_key])) {
                $resetpassword_success_message = sanitize_text_field(wp_unslash($_COOKIE[$resetpassword_success_cookie_key]));
            }
            if (!empty($resetpassword_success_message)) : ?>
                <div class="afeb-lr-form-succ-box">
                    <?php echo esc_html($resetpassword_success_message); ?>
                </div>
            <?php else:
                $_COOKIE['afeb_resetpassword_error_' . $options['widget_id']] = $options['err_msg'];
            endif; ?>
            <?php
            if (($this->is_editor == 'yes' && !empty($settings['err_msg_bx_prev'])))
                $_COOKIE['afeb_resetpassword_error_' . $options['widget_id']] = CHelper::$LIM;
            $resetpassword_error_cookie_key = 'afeb_resetpassword_error_' . $options['widget_id'];
            $resetpassword_error_message = '';
            if (!empty($_COOKIE[$resetpassword_error_cookie_key])) {
                $resetpassword_error_message = sanitize_text_field(wp_unslash($_COOKIE[$resetpassword_error_cookie_key]));
            }
            if (!empty($resetpassword_error_message)) : ?>
                <div class="afeb-lr-form-err-box">
                    <?php echo esc_html($resetpassword_error_message); ?>
                </div>
            <?php endif; ?>
        </form>
    <?php
    }

    /**
     * What to do after logging in?
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    protected function after_logged_in($settings = [], $options = [])
    {
        $this->default_loggedin_form($settings, $options);
    }

    /**
     * Print default loggedin form
     *
     * @param array $settings
     * @param array $options
     * @since 1.0.3
     *
     */
    protected function default_loggedin_form($settings = [], $options = [])
    {
        global $current_user;
        if (function_exists('wp_get_current_user'))
            wp_get_current_user();
    ?>
        <div class="afeb-def-loggedin-form">
            <?php
            $avatar = '';
            if ($settings['show_avatar'] == 'yes' && !empty($current_user->user_email)) {
                $avatar = get_avatar($current_user->user_email, 200);
            }

            $placeholders = ['/\[username]/', '/\[sitetitle]/'];
            $replacement = [$current_user->display_name, get_option('blogname')];
            echo '<div class="afeb-msg-box">' . $avatar . preg_replace($placeholders, $replacement, wp_kses_post($settings['def_lgnin_msg'])) . '</div>';
            ?>
        </div>
        <?php
    }

    /**
     * Generate security input
     *
     * @param array $settings
     * @since 1.0.3
     *
     */
    public static function security_input_field($settings = [])
    {
        if (!empty($settings['frm_sec_field'])):
            $num_a = wp_rand(1, 10);
            $num_b = wp_rand(1, 10);
        ?>
            <div class="afeb-lr-form-group afeb-sec-field-group">
                <?php if (!empty($settings['sec_itm_lbl'])) : ?>
                    <label for="afeb-sec-field">
                        <?php echo esc_html($settings['sec_itm_lbl']); ?>
                        <?php if (!empty($settings['sec_itm_hlp_desc'])): ?><span
                                class="afeb-help-description far fa-question-circle"></span><?php endif; ?>
                        <?php if (!empty($settings['sec_itm_sh_requrd'])) : ?>
                            <span class="afeb-required-mark">*</span>
                        <?php endif; ?>
                        <?php if (!empty($settings['sec_itm_hlp_desc_txt'])): ?>
                            <div
                                class="afeb-help-description-text"><?php echo esc_html($settings['sec_itm_hlp_desc_txt']); ?></div><?php endif; ?>
                    </label>
                <?php endif; ?>
                <div class="afeb-lr-form-control-box">
                    <span class="afeb-lr-form-icon"><?php Icons_Manager::render_icon($settings['sec_itm_ic']); ?></span>
                    <input title="" name="afeb-sec-field" type="text"
                        placeholder="<?php echo esc_attr($num_a . ' + ' . $num_b); ?> = ?">
                    <input name="afeb-sec-field-ans" type="hidden" value="<?php echo md5(esc_attr($num_a + $num_b));
                                                                            ?>">
                </div>
            </div>
<?php
        endif;
    }
}
