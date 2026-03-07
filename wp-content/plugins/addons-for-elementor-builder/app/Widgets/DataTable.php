<?php

namespace AFEB\Widgets;

use AFEB\Assets;
use AFEB\Controls\CHelper;
use AFEB\Helper;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" DataTable Widget Class
 * 
 * @class DataTable
 * @version 1.2.0
 */
class DataTable extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $CHelper;

    /**
     * @var Button
     */
    private $BTN_Error;

    /**
     * DataTable Constructor
     * 
     * @since 1.2.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->CHelper = new CHelper();
        $this->assets->data_table_pkg_style();
        $this->assets->data_table_btns_pkg_style();
        $this->assets->data_table_pkg_script();
        $this->assets->data_table_csv_pkg_script();
        $this->assets->data_table_btns_pkg_script();
        $this->assets->data_table_style();
        $this->assets->data_table_script();
    }

    /**
     * Get widget name
     *
     * @since 1.2.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_data_table';
    }

    /**
     * Get widget title
     *
     * @since 1.2.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Data Table', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.2.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-datatable';
    }

    /**
     * Get widget categories
     *
     * @since 1.2.0
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
     * @since 1.2.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['data_table', 'table', 'row', 'column', esc_html__('Data Table', 'addons-for-elementor-builder')];
    }

    /**
     * Register DataTable widget controls
     *
     * @since 1.2.0
     */
    public function register_controls()
    {
        $this->CHelper->add_cnt_sctn($this, 'cs1', esc_html__('Data Table', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->slct($obj, 'src', esc_html__('Source', 'addons-for-elementor-builder'), [
                'cstm' => esc_html__('Custom', 'addons-for-elementor-builder'),
                'csv' => esc_html__('CSV', 'addons-for-elementor-builder')
            ], 'cstm');
            $this->CHelper->mda($obj, 'csv', ['src' => 'csv'], $this->assets->assets_url('/docs/sample.csv'), esc_html__('Custom', 'addons-for-elementor-builder'), 1, ['*']);
        });
        /**
         *
         * Table Header
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs2', esc_html__('Table Header', 'addons-for-elementor-builder'), function ($obj) {
            $items = new Repeater();
            $this->CHelper->wysiwyg($items, 'hdr_cnt', esc_html__('Content', 'addons-for-elementor-builder'), '', '', 'lblk');
            $this->CHelper->rptr($obj, 'hdr_lbls', $items->get_controls(), [
                ['hdr_cnt' => sprintf('<p>%s</p>', esc_html__('Name', 'addons-for-elementor-builder'))],
                ['hdr_cnt' => sprintf('<p>%s</p>', esc_html__('Position', 'addons-for-elementor-builder'))],
                ['hdr_cnt' => sprintf('<p>%s</p>', esc_html__('Office', 'addons-for-elementor-builder'))],
                ['hdr_cnt' => sprintf('<p>%s</p>', esc_html__('Age', 'addons-for-elementor-builder'))],
                ['hdr_cnt' => sprintf('<p>%s</p>', esc_html__('Start Date', 'addons-for-elementor-builder'))],
                ['hdr_cnt' => sprintf('<p>%s</p>', esc_html__('Salary', 'addons-for-elementor-builder'))]
            ], 'hdr_cnt', ' ');
        }, [], ['src' => 'cstm']);
        /**
         *
         * Table Body
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs3', esc_html__('Table Body', 'addons-for-elementor-builder'), function ($obj) {
            $items = new Repeater();
            $this->CHelper->slct($items, 'bdy_acts', esc_html__('Action', 'addons-for-elementor-builder'), [
                'Cell' => esc_html__('Add New Cell', 'addons-for-elementor-builder'),
                'Start Row' => esc_html__('Start New Row', 'addons-for-elementor-builder')
            ], 'Cell');
            $def = esc_html__('Sample Content', 'addons-for-elementor-builder');
            $this->CHelper->wysiwyg($items, 'bdy_cnt', esc_html__('Content', 'addons-for-elementor-builder'), $def, '', '', ['bdy_acts' => 'Cell']);
            $this->CHelper->rptr($obj, 'bdy_itms', $items->get_controls(), [
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('Airi Satou', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('Accountant', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('Tokyo', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('33', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('2008/11/28', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('$162,700', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Start Row'
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('Angelica Ramos', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('Chief Executive Officer (CEO)', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('London', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('47', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('2009/10/09', 'addons-for-elementor-builder'))
                ],
                [
                    'bdy_acts' => 'Cell',
                    'bdy_cnt' => sprintf('<p>%s</p>', esc_html__('$1,200,000', 'addons-for-elementor-builder'))
                ]
            ], '{{{ bdy_acts }}}: {{{ bdy_cnt }}}', ' ');
        }, [], ['src' => 'cstm']);
        /**
         *
         * Settings
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs4', esc_html__('Settings', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'btns', esc_html__('Buttons', 'addons-for-elementor-builder'), 1);
            $btns = new Repeater();
            $this->CHelper->slct($btns, 'btn_typ', esc_html__('Type', 'addons-for-elementor-builder'), [
                'Copy' => esc_html__('Copy', 'addons-for-elementor-builder'),
                'Excel' => esc_html__('Excel', 'addons-for-elementor-builder'),
                'CSV' => esc_html__('CSV', 'addons-for-elementor-builder'),
                'Print' => esc_html__('Print', 'addons-for-elementor-builder')
            ], 'Copy');
            $this->CHelper->txt($btns, 'btn_txt', esc_html__('Text', 'addons-for-elementor-builder'), '', '', 'dai');
            $this->CHelper->txt($btns, 'btn_ttl', esc_html__('Tooltip Text', 'addons-for-elementor-builder'), '', '', 'dai');
            $this->CHelper->icn($btns, 'btn_bfr_ic', '', '', esc_html__('Before Icon', 'addons-for-elementor-builder'), 1);
            $this->CHelper->icn($btns, 'btn_aftr_ic', '', '', esc_html__('After Icon', 'addons-for-elementor-builder'), 1);
            $this->CHelper->rptr($obj, 'btn_itms', $btns->get_controls(), [
                [
                    'btn_typ' => 'Copy',
                    'btn_txt' => 'copy'
                ],
                [
                    'btn_typ' => 'Excel',
                    'btn_txt' => 'excel'
                ],
                [
                    'btn_typ' => 'CSV',
                    'btn_txt' => 'csv'
                ],
                [
                    'btn_typ' => 'Print',
                    'btn_txt' => 'print'
                ]
            ], 'btn_typ', ' ', ['btns' => 'yes']);
            $this->CHelper->yn_swtchr($obj, 'lnt_chng', esc_html__('Show Items Per Page', 'addons-for-elementor-builder'), 1);
            $this->CHelper->txt($obj, 'lnt_mnu', esc_html__('Items Per Page', 'addons-for-elementor-builder'), '5, 10, 25, 50, 100', '', 'dai', ['lnt_chng' => 'yes']);
            $this->CHelper->num($obj, 'pge_lnt', esc_html__('Default Value', 'addons-for-elementor-builder'), 1, 100, null, 10, '', '', ['pge' => 'yes', 'lnt_chng' => 'yes']);
            $this->CHelper->dvdr($obj, 'div_1');
            $this->CHelper->hed($obj, 'srch_hed', esc_html__('Search', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'srch', esc_html__('Searching', 'addons-for-elementor-builder'), 1);
            $lbl = esc_html__('Label', 'addons-for-elementor-builder');
            $txt = esc_html__('Search:', 'addons-for-elementor-builder');
            $cndtn = ['srch' => 'yes'];
            $this->CHelper->txt($obj, 'srch_lbl', $lbl, $txt, '', 'dai', $cndtn);
            $lbl = esc_html__('Placeholder', 'addons-for-elementor-builder');
            $txt = esc_html__('Type Here To Search...', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'srch_plc_hldr', $lbl, $txt, '', 'lblk,dai', $cndtn);
            $this->CHelper->icn($obj, 'srch_lbl_bfr_ic', '', '', esc_html__('Before Icon', 'addons-for-elementor-builder'), 1, 0, $cndtn);
            $this->CHelper->icn($obj, 'srch_lbl_aftr_ic', '', '', esc_html__('After Icon', 'addons-for-elementor-builder'), 1, 0, $cndtn);
            $this->CHelper->dvdr($obj, 'div_2');
            $this->CHelper->yn_swtchr($obj, 'ordr', esc_html__('Ordering', 'addons-for-elementor-builder'), 1);
            $this->CHelper->yn_swtchr($obj, 'info', esc_html__('Info', 'addons-for-elementor-builder'), 1);
            $this->CHelper->dvdr($obj, 'div_3');
            $this->CHelper->hed($obj, 'pge_hed', esc_html__('Pagination', 'addons-for-elementor-builder'));
            $this->CHelper->yn_swtchr($obj, 'pge', esc_html__('Paging', 'addons-for-elementor-builder'), 1);
            $cndtn = ['pge' => 'yes'];
            $this->CHelper->slct($obj, 'pge_typ', esc_html__('Paging Type', 'addons-for-elementor-builder'), [
                'numbers' => esc_html__('Numbers', 'addons-for-elementor-builder'),
                'simple' => esc_html__('Previous/Next', 'addons-for-elementor-builder'),
                'simple_numbers' => esc_html__('Numbers + Previous/Next', 'addons-for-elementor-builder'),
                'full' => esc_html__('First/Last + Previous/Next', 'addons-for-elementor-builder'),
                'first_last_numbers' => esc_html__('First/Last + Numbers', 'addons-for-elementor-builder')
            ], 'simple_numbers', $cndtn);
            $obj->start_controls_tabs('afeb_icn_tb_cntrl');
            /**
             * First
             */
            $cndtn = ['pge' => 'yes', 'pge_typ!' => 'numbers'];
            $this->CHelper->add_tb($obj, 'pge_tb_frst', esc_html__('First', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Text', 'addons-for-elementor-builder');
                $txt = esc_html__('First', 'addons-for-elementor-builder');

                $this->CHelper->txt($obj, 'pge_frst', $lbl, $txt, '', 'dai', $opt[0]);
                $this->CHelper->icn($obj, 'pge_frst_bfr_ic', '', '', esc_html__('Before Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
                $this->CHelper->icn($obj, 'pge_frst_aftr_ic', '', '', esc_html__('After Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
            }, [$cndtn], $cndtn);
            /**
             * Last
             */
            $this->CHelper->add_tb($obj, 'pge_tb_lst', esc_html__('Last', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Text', 'addons-for-elementor-builder');
                $txt = esc_html__('Last', 'addons-for-elementor-builder');
                $this->CHelper->txt($obj, 'pge_lst', $lbl, $txt, '', 'dai', $opt[0]);
                $this->CHelper->icn($obj, 'pge_lst_bfr_ic', '', '', esc_html__('Before Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
                $this->CHelper->icn($obj, 'pge_lst_aftr_ic', '', '', esc_html__('After Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
            }, [$cndtn], $cndtn);
            /**
             * Previous
             */
            $this->CHelper->add_tb($obj, 'pge_tb_prv', esc_html__('Previous', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Text', 'addons-for-elementor-builder');
                $txt = esc_html__('Prev', 'addons-for-elementor-builder');
                $this->CHelper->txt($obj, 'pge_prv', $lbl, $txt, '', 'dai', $opt[0]);
                $this->CHelper->icn($obj, 'pge_prv_bfr_ic', '', '', esc_html__('Before Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
                $this->CHelper->icn($obj, 'pge_prv_aftr_ic', '', '', esc_html__('After Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
            }, [$cndtn], $cndtn);
            /**
             * Next
             */
            $this->CHelper->add_tb($obj, 'pge_tb_nxt', esc_html__('Next', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $lbl = esc_html__('Text', 'addons-for-elementor-builder');
                $txt = esc_html__('Next', 'addons-for-elementor-builder');
                $this->CHelper->txt($obj, 'pge_nxt', $lbl, $txt, '', 'dai', $opt[0]);
                $this->CHelper->icn($obj, 'pge_nxt_bfr_ic', '', '', esc_html__('Before Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
                $this->CHelper->icn($obj, 'pge_nxt_aftr_ic', '', '', esc_html__('After Icon', 'addons-for-elementor-builder'), 1, 0, $opt[0]);
            }, [$cndtn], $cndtn);
            $obj->end_controls_tabs();
        });
        /**
         *
         * Translation
         *
         */
        $this->CHelper->add_cnt_sctn($this, 'cs7', esc_html__('Translations', 'addons-for-elementor-builder'), function ($obj) {
            $lbl = esc_html__('Menu Length', 'addons-for-elementor-builder');
            $txt = esc_html__('Show [MENU] entries', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'trnsltn_mre_lnt_mnu', $lbl, $txt, '', 'lblk,dai');
            $lbl = esc_html__('Info', 'addons-for-elementor-builder');
            $txt = esc_html__('Showing page [PAGE] of [PAGES]', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'trnsltn_mre_info', $lbl, $txt, '', 'lblk,dai');
            $lbl = esc_html__('Empty Table', 'addons-for-elementor-builder');
            $txt = esc_html__('No data available in table', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'trnsltn_mre_empty_tbl', $lbl, $txt, '', 'lblk,dai');
            $lbl = esc_html__('Info Empty', 'addons-for-elementor-builder');
            $txt = esc_html__('No entries to show', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'trnsltn_mre_info_empty', $lbl, $txt, '', 'lblk,dai');
            $lbl = esc_html__('Loading Records', 'addons-for-elementor-builder');
            $txt = esc_html__('Please wait - loading...', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'trnsltn_mre_ldng_rcrd', $lbl, $txt, '', 'lblk,dai');
            $lbl = esc_html__('Zero Records', 'addons-for-elementor-builder');
            $txt = esc_html__('No matching records found', 'addons-for-elementor-builder');
            $this->CHelper->txt($obj, 'trnsltn_mre_zro_rcrd', $lbl, $txt, '', 'lblk,dai');
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Content Styles
         *
         */
        $bx_slctr = '{{WRAPPER}} .afeb-data-table';
        $this->CHelper->add_stl_sctn($this, 'ss1', esc_html__('Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'bx_bg', $opt[0], [], []);
            $this->CHelper->res_pad($obj, 'bx_pad', $opt[0], [], [], '', null, 0, '');
            $this->CHelper->brdr($obj, 'bx_brdr', $opt[0], '');
            $this->CHelper->brdr_rdus($obj, 'bx_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], [], [], '', null, 0, '');
            $this->CHelper->bx_shdo($obj, 'bx_shdo', $opt[0], '');
        }, [$bx_slctr]);
        /**
         *
         * Buttons
         *
         */
        $btns_slctr = $bx_slctr . ' .dt-buttons > button';
        $this->CHelper->add_stl_sctn($this, 'ss2', esc_html__('Buttons', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('btns_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 't1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_bg', $opt[0]);
                $this->CHelper->cstm_clr($obj, 'btn_clr', $opt[0], 'color: {{VALUE}}!important;', esc_html__('Text Color', 'addons-for-elementor-builder'));
                $this->CHelper->typo($obj, 'btn_typo', $opt[0]);
                $this->CHelper->res_mar($obj, 'btn_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'btn_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 't2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'btn_bg_hvr', $opt[0]);
                $this->CHelper->cstm_clr($obj, 'btn_clr_hvr', $opt[0], 'color: {{VALUE}}!important;', esc_html__('Text Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_mar($obj, 'btn_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'btn_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'btn_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'btn_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'btn_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$btns_slctr]);
        /**
         *
         * Buttons Icon(s)
         *
         */
        $btns_icn_slctr = $bx_slctr . ' .dt-buttons > button span >';
        $this->CHelper->add_stl_sctn($this, 'ss3', esc_html__('Button Icons', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('btns_icn_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'btns_icn_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->hed($obj, 'btn_bfr_icn_hed', esc_html__('Before Icon', 'addons-for-elementor-builder'));
                $clr = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->clr($obj, 'btn_bfr_icn_clr', $opt[0], $clr);
                $size = ['px' => ['min' => 0, 'max' => 100]];
                $slctr = [$opt[0] => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}'];
                $this->CHelper->res_sldr($obj, 'btn_bfr_icn_sze', esc_html__('Size', 'addons-for-elementor-builder'), $slctr, $size, ['px']);
                $this->CHelper->res_mar($obj, 'btn_bfr_icn_mar', $opt[0]);
                $this->CHelper->dvdr($obj, 'div_5');
                $this->CHelper->hed($obj, 'btn_aftr_icn_hed', esc_html__('After Icon', 'addons-for-elementor-builder'));
                $this->CHelper->clr($obj, 'btn_aftr_icn_clr', $opt[1], $clr);
                $this->CHelper->res_sldr($obj, 'btn_aftr_icn_sze', esc_html__('Size', 'addons-for-elementor-builder'), [$opt[1] => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}'], $size, ['px']);
                $this->CHelper->res_mar($obj, 'btn_aftr_icn_mar', $opt[1]);
            }, [$opt[0], $opt[1]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'btns_icn_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->hed($obj, 'btn_bfr_icn_hed_hvr', esc_html__('Before Icon', 'addons-for-elementor-builder'));
                $clr = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->clr($obj, 'btn_bfr_icn_clr_hvr', $opt[0] . ':hover span > *:first-child', $clr);
                $this->CHelper->dvdr($obj, 'div_6');
                $this->CHelper->hed($obj, 'btn_aftr_icn_hed_hvr', esc_html__('After Icon', 'addons-for-elementor-builder'));
                $this->CHelper->clr($obj, 'btn_aftr_icn_clr_hvr', $opt[0] . ':hover span > *:last-child', $clr);
            }, [$opt[2]]);
            $obj->end_controls_tabs();
        }, [$btns_icn_slctr . '.afeb-bfr-ic', $btns_icn_slctr . '.afeb-aftr-ic', $btns_slctr]);
        /**
         *
         * Items Per Page Box
         *
         */
        $lnt_bx_slctr = $bx_slctr . ' .dt-length';
        $this->CHelper->add_stl_sctn($this, 'ss4', esc_html__('Items Per Page Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('lnt_bx_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'lnt_bx_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'lnt_bx_bg', $opt[0]);
                $this->CHelper->clr($obj, 'lnt_bx_clr', $opt[0]);
                $this->CHelper->typo($obj, 'lnt_bx_typo', $opt[0]);
                $this->CHelper->res_mar($obj, 'lnt_bx_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'lnt_bx_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'lnt_bx_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'lnt_bx_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'lnt_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'lnt_bx_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'lnt_bx_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'lnt_bx_clr_hvr', $opt[0]);
                $this->CHelper->res_mar($obj, 'lnt_bx_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'lnt_bx_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'lnt_bx_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'lnt_bx_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'lnt_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$lnt_bx_slctr]);
        /**
         *
         * Items Per Page Select Box
         *
         */
        $lnt_slct_bx_slctr = $lnt_bx_slctr . ' .dt-input';
        $this->CHelper->add_stl_sctn($this, 'ss5', esc_html__('Items Per Page Select Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->clr($obj, 'lnt_slct_bx_clr', $opt[0]);
            $this->CHelper->typo($obj, 'lnt_slct_bx_typo', $opt[0]);
            $this->CHelper->res_mar($obj, 'lnt_slct_bx_mar', $opt[0]);
            $this->CHelper->res_pad($obj, 'lnt_slct_bx_pad', $opt[0]);
            $this->CHelper->brdr($obj, 'lnt_slct_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'lnt_slct_bx_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'lnt_slct_bx_shdo', $opt[0]);
        }, [$lnt_slct_bx_slctr]);
        /**
         *
         * Search Box
         *
         */
        $srch_bx_slctr = $bx_slctr . ' .dt-search';
        $this->CHelper->add_stl_sctn($this, 'ss6', esc_html__('Search Box', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('srch_bx_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'srch_bx_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'srch_bx_bg', $opt[0]);
                $this->CHelper->res_mar($obj, 'srch_bx_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'srch_bx_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'srch_bx_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'srch_bx_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'srch_bx_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'srch_bx_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'srch_bx_bg_hvr', $opt[0]);
                $this->CHelper->res_mar($obj, 'srch_bx_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'srch_bx_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'srch_bx_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'srch_bx_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'srch_bx_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$srch_bx_slctr]);
        /**
         *
         * Search Label
         *
         */
        $srch_lbl_slctr = $srch_bx_slctr . ' label';
        $this->CHelper->add_stl_sctn($this, 'ss7', esc_html__('Search Label', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('srch_lbl_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'srch_lbl_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'srch_lbl_bg', $opt[0]);
                $this->CHelper->clr($obj, 'srch_lbl_clr', $opt[0]);
                $this->CHelper->typo($obj, 'srch_lbl_typo', $opt[0]);
                $this->CHelper->res_mar($obj, 'srch_lbl_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'srch_lbl_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'srch_lbl_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'srch_lbl_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'srch_lbl_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'srch_lbl_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'srch_lbl_bg_hvr', $opt[0]);
                $this->CHelper->clr($obj, 'srch_lbl_clr_hvr', $opt[0]);
                $this->CHelper->res_mar($obj, 'srch_lbl_mar_hvr', $opt[0]);
                $this->CHelper->res_pad($obj, 'srch_lbl_pad_hvr', $opt[0]);
                $this->CHelper->brdr($obj, 'srch_lbl_brdr_hvr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'srch_lbl_rdus_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'srch_lbl_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover']);
            $obj->end_controls_tabs();
        }, [$srch_lbl_slctr]);
        /**
         *
         * Search Label Icon(s)
         *
         */
        $srch_lbl_icn_slctr = $srch_lbl_slctr . '>*';
        $this->CHelper->add_stl_sctn($this, 'ss8', esc_html__('Search Label Icons', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('srch_lbl_icn_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'srch_lbl_icn_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->hed($obj, 'srch_lbl_bfr_icn_hed', esc_html__('Before Icon', 'addons-for-elementor-builder'));
                $clr = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->clr($obj, 'srch_lbl_bfr_icn_clr', $opt[0], $clr);
                $slctr = [$opt[0] => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}'];
                $size = ['px' => ['min' => 0, 'max' => 100]];
                $this->CHelper->res_sldr($obj, 'srch_lbl_bfr_icn_sze', esc_html__('Size', 'addons-for-elementor-builder'), $slctr, $size, ['px']);
                $this->CHelper->res_mar($obj, 'srch_lbl_bfr_icn_mar', $opt[0]);
                $this->CHelper->dvdr($obj, 'div_8');
                $this->CHelper->hed($obj, 'srch_lbl_aftr_icn_hed', esc_html__('After Icon', 'addons-for-elementor-builder'));
                $this->CHelper->clr($obj, 'srch_lbl_aftr_icn_clr', $opt[1], $clr);
                $slctr = [$opt[1] => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}'];
                $size = ['px' => ['min' => 0, 'max' => 100]];
                $this->CHelper->res_sldr($obj, 'srch_lbl_aftr_icn_sze', esc_html__('Size', 'addons-for-elementor-builder'), $slctr, $size, ['px']);
                $this->CHelper->res_mar($obj, 'srch_lbl_aftr_icn_mar', $opt[1]);
            }, [$opt[0], $opt[1]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'srch_lbl_icn_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->hed($obj, 'srch_lbl_bfr_icn_hed_hvr', esc_html__('Before Icon', 'addons-for-elementor-builder'));
                $clr = esc_html__('Color', 'addons-for-elementor-builder');
                $this->CHelper->clr($obj, 'srch_lbl_bfr_icn_clr_hvr', $opt[0] . ':hover > *:first-child', $clr);
                $this->CHelper->dvdr($obj, 'div_10');
                $this->CHelper->hed($obj, 'srch_lbl_aftr_icn_hed_hvr', esc_html__('After Icon', 'addons-for-elementor-builder'));
                $this->CHelper->clr($obj, 'srch_lbl_aftr_icn_clr_hvr', $opt[0] . ':hover > *:last-child', $clr);
            }, [$opt[2]]);
            $obj->end_controls_tabs();
        }, [$srch_lbl_icn_slctr . ':first-child', $srch_lbl_icn_slctr . ':last-child', $srch_lbl_slctr]);
        /**
         *
         * Search Input
         *
         */
        $srch_inpt_slctr = $srch_bx_slctr . ' input.dt-input';
        $this->CHelper->add_stl_sctn($this, 'ss9', esc_html__('Search Input', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('srch_inpt_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'srch_inpt_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'srch_inpt_bg', $opt[0]);
                $this->CHelper->clr($obj, 'srch_inpt_clr', $opt[0]);
                $this->CHelper->clr($obj, 'srch_inpt_plcdr_clr', $opt[1], esc_html__('Placeholder Color', 'addons-for-elementor-builder'));
                $this->CHelper->typo($obj, 'srch_inpt_typo', $opt[0] . ',' . $opt[1]);
                $this->CHelper->res_mar($obj, 'srch_inpt_mar', $opt[0]);
                $this->CHelper->res_pad($obj, 'srch_inpt_pad', $opt[0]);
                $this->CHelper->brdr($obj, 'srch_inpt_brdr', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'srch_inpt_rdus', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'srch_inpt_shdo', $opt[0]);
            }, [$opt[0], $opt[1]]);
            /**
             * Focus Tab
             */
            $this->CHelper->add_tb($obj, 'srch_inpt_t2', esc_html__('Focus', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_grp_ctrl($obj, 'srch_inpt_bg_fcs', $opt[0]);
                $this->CHelper->clr($obj, 'srch_inpt_clr_fcs', $opt[0]);
                $this->CHelper->clr($obj, 'srch_inpt_plcdr_clr_fcs', $opt[1], esc_html__('Placeholder Color', 'addons-for-elementor-builder'));
                $this->CHelper->brdr($obj, 'srch_inpt_brdr_fcs', $opt[0]);
                $this->CHelper->brdr_rdus($obj, 'srch_inpt_rdus_fcs', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'srch_inpt_shdo_fcs', $opt[0]);
            }, [$opt[0] . ':focus', $opt[0] . ':focus::placeholder']);
            $obj->end_controls_tabs();
        }, [$srch_inpt_slctr, $srch_inpt_slctr . '::placeholder']);
        /**
         *
         * Table Styles
         *
         */
        $tbl_slctr = $bx_slctr . ' .dt-scroll';
        $this->CHelper->add_stl_sctn($this, 'ss10', esc_html__('Table', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->bg_grp_ctrl($obj, 'tbl_bg', $opt[0], [], []);
            $this->CHelper->res_mar($obj, 'tbl_mar', $opt[0], [], ['top', 'bottom']);
            $slctr = '.afeb-data-table .dt-scroll,' .
                '.afeb-data-table .afeb-inner-table>thead>tr>*,' .
                '.afeb-data-table .afeb-inner-table>tbody>tr>*,' .
                '.afeb-data-table .afeb-inner-table>tbody>tr:first-child>*,' .
                '.afeb-data-table .afeb-inner-table>tbody>tr:last-child>*,' .
                '.afeb-data-table div.dt-container.dt-empty-footer .dt-scroll-body tbody>tr:last-child>*';
            $this->CHelper->slct($obj, 'tbl_brdr_typ', esc_html__('Border Type', 'addons-for-elementor-builder'), [
                '' => esc_html__('Default', 'addons-for-elementor-builder'),
                'none' => esc_html__('None', 'addons-for-elementor-builder'),
                'solid' => esc_html__('Solid', 'addons-for-elementor-builder'),
                'double' => esc_html__('Double', 'addons-for-elementor-builder'),
                'dotted' => esc_html__('Dotted', 'addons-for-elementor-builder'),
                'dashed' => esc_html__('Dashed', 'addons-for-elementor-builder'),
                'groove' => esc_html__('Groove', 'addons-for-elementor-builder')
            ], '', [], [$slctr => 'border-style: {{VALUE}} !important;']);
            $size = ['px' => ['min' => 1, 'max' => 10]];
            $wdt_slctr = [
                '.afeb-data-table .dt-scroll' => 'border-width: {{SIZE}}{{UNIT}} !important;',
                '.afeb-data-table .afeb-inner-table>thead>tr>*,' .
                    '.afeb-data-table .afeb-inner-table>tbody>tr>*,' .
                    '.afeb-data-table div.dt-container.dt-empty-footer .dt-scroll-body tbody>tr:last-child>*' => 'border-left-width: {{SIZE}}{{UNIT}} !important;',
                '.afeb-data-table .afeb-inner-table>tbody>tr>*' => 'border-bottom-width: {{SIZE}}{{UNIT}} !important;',
                '.afeb-data-table .afeb-inner-table>tbody>tr:first-child>*' => 'border-top-width: {{SIZE}}{{UNIT}} !important;'
            ];
            $this->CHelper->res_sldr($obj, 'tbl_brdr_wdt', esc_html__('Border Width', 'addons-for-elementor-builder'), $wdt_slctr, $size, ['px'], ['tbl_brdr_typ!' => ['', 'none']]);
            $this->CHelper->cstm_clr($obj, 'tbl_brdr_clr', $slctr, 'border-color: {{VALUE}} !important;', esc_html__('Border Color', 'addons-for-elementor-builder'), '', ['tbl_brdr_typ!' => ['', 'none']]);
            $this->CHelper->brdr_rdus($obj, 'tbl_rdus', [$opt[0] => CHelper::FILL_BR_RADIUS], [], [], '', null, 0, '');
            $this->CHelper->bx_shdo($obj, 'tbl_shdo', $opt[0], '');
        }, [$tbl_slctr]);
        /**
         *
         * Table Header
         *
         */
        $tbl_hdr_slctr = $bx_slctr . ' thead tr th';
        $this->CHelper->add_stl_sctn($this, 'ss12', esc_html__('Header', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('tbl_hdr_stl_tbs');
            /**
             * Normal Tab
             */
            $this->CHelper->add_tb($obj, 'tbl_hdr_t1', esc_html__('Normal', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_clr($obj, 'tbl_hdr_bg', $opt[0], '', '', [], false);
                $ttl_slctr = $opt[0] . ' span.dt-column-title';
                $ic_slctr = $opt[0] . ' span.dt-column-order';

                $this->CHelper->clr($obj, 'tbl_hdr_ttl_clr', $ttl_slctr, esc_html__('Title Color', 'addons-for-elementor-builder'), [], '', false);
                $this->CHelper->clr($obj, 'tbl_hdr_srt_ic_clr', $ic_slctr, esc_html__('Sorting Icon Color', 'addons-for-elementor-builder'));
                $this->CHelper->typo($obj, 'tbl_hdr_ttl_typo', $ttl_slctr, esc_html__('Title Typography', 'addons-for-elementor-builder'));
                $this->CHelper->res_talmnt($obj, 'tbl_hdr_almnt', $opt[0]);
                $str_almnt = [
                    'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon'  => 'eicon-text-align-left'],
                    'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon'  => 'eicon-text-align-right']
                ];
                $slctr = ['{{WRAPPER}} .afeb-data-table thead .dt-column-order' => '{{VALUE}}: 10px'];
                $this->CHelper->chse($obj, 'tbl_hdr_srt_almnt', esc_html__('Sorting Icon Alignment', 'addons-for-elementor-builder'), $str_almnt, $slctr, 1, !is_rtl() ? 'right' : 'left');
                $slctr = [$ic_slctr => 'font-size: {{SIZE}}px'];
                $size = ['px' => ['min' => 10, 'max' => 30]];
                $this->CHelper->res_sldr($obj, 'tbl_hdr_srt_ic_sze', esc_html__('Sorting Icon Size', 'addons-for-elementor-builder'), $slctr, $size, ['px']);
                $slctr_key = '{{WRAPPER}} .afeb-data-table thead .dt-column-order';
                $slctr = [$slctr_key => 'left: {{SIZE}}{{UNIT}}'];
                $size = ['px' => ['min' => 0, 'max' => 1000]];
                $this->CHelper->res_sldr($obj, 'tbl_hdr_srt_lft_ofst', esc_html__('Sorting Icon Left Offset', 'addons-for-elementor-builder'), $slctr, $size, ['px'], ['tbl_hdr_srt_almnt' => 'left']);
                $slctr = [$slctr_key => 'right: {{SIZE}}{{UNIT}}'];
                $this->CHelper->res_sldr($obj, 'tbl_hdr_srt_rght_ofst', esc_html__('Sorting Icon Right Offset', 'addons-for-elementor-builder'), $slctr, $size, ['px'], ['tbl_hdr_srt_almnt' => 'right']);
                $size = ['px' => ['min' => 0, 'max' => 1000], 'vw' => ['min' => 0, 'max' => 100], 'rem' => ['min' => 0, 'max' => 100]];
                $this->CHelper->res_sldr($obj, 'tbl_hdr_min_wdt', esc_html__('Minumum Width', 'addons-for-elementor-builder'), [$opt[0] => 'min-width: {{SIZE}}{{UNIT}}'], $size, ['px', 'vw', 'rem']);
                $this->CHelper->res_pad($obj, 'tbl_hdr_pad', $opt[0]);
                $size = ['px' => ['min' => 0, 'max' => 50]];
                $this->CHelper->bx_shdo($obj, 'tbl_hdr_shdo', $opt[0]);
            }, [$opt[0]]);
            /**
             * Hover Tab
             */
            $this->CHelper->add_tb($obj, 'tbl_hdr_t2', esc_html__('Hover', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->bg_clr($obj, 'tbl_hdr_bg_hvr', $opt[0], '', '', [], false);
                $ttl_slctr = $opt[1] . ':hover span.dt-column-title';
                $ic_slctr = $opt[1] . ':hover span.dt-column-order';
                $this->CHelper->clr($obj, 'tbl_hdr_ttl_clr_hvr', $ttl_slctr, esc_html__('Title Color', 'addons-for-elementor-builder'), [], '', false);
                $this->CHelper->clr($obj, 'tbl_hdr_srt_ic_clr_hvr', $ic_slctr, esc_html__('Sorting Icon Color', 'addons-for-elementor-builder'));
                $this->CHelper->res_pad($obj, 'tbl_hdr_pad_hvr', $opt[0]);
                $this->CHelper->bx_shdo($obj, 'tbl_hdr_shdo_hvr', $opt[0]);
            }, [$opt[0] . ':hover', $opt[0]]);
            $obj->end_controls_tabs();
        }, [$tbl_hdr_slctr]);
        /**
         *
         * Content Styles
         *
         */
        $tbl_hdr_img_slctr  = $tbl_hdr_slctr . ' img';
        $this->CHelper->add_stl_sctn($this, 'ss14', esc_html__('Header Image', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $this->CHelper->res_mar($obj, 'tbl_hdr_img_mar', $opt[0]);
            $this->CHelper->brdr($obj, 'tbl_hdr_img_bx_brdr', $opt[0]);
            $this->CHelper->brdr_rdus($obj, 'tbl_hdr_img_bx_rdus', $opt[0]);
            $this->CHelper->bx_shdo($obj, 'tbl_hdr_img_bx_shdo', $opt[0]);
        }, [$tbl_hdr_img_slctr]);
        /**
         *
         * Table Body
         *
         */
        $tbl_bdy_slctr = $bx_slctr . ' tbody tr';
        $this->CHelper->add_stl_sctn($this, 'ss15', esc_html__('Body', 'addons-for-elementor-builder'), function ($obj, $opt) {
            $obj->start_controls_tabs('tbl_bdy_stl_tbs');
            /**
             * Odd Tab
             */
            $odd_slctr = $opt[0] . ':nth-child(odd) td';
            $odd_slctr_hvr = $opt[0] . ':nth-child(odd):hover td';
            $this->CHelper->add_tb($obj, 'tbl_bdy_t1', esc_html__('Odd', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->hed($obj, 'tbl_bdy_nrml_odd', esc_html__('Normal', 'addons-for-elementor-builder'));
                $this->CHelper->bg_grp_ctrl($obj, 'tbl_bdy_bg_odd', $opt[0]);
                $this->CHelper->clr($obj, 'tbl_bdy_clr_odd', $opt[0]);
                $this->CHelper->dvdr($obj, 'div_20');
                $this->CHelper->hed($obj, 'tbl_bdy_hvr_odd', esc_html__('Hover', 'addons-for-elementor-builder'));
                $this->CHelper->bg_grp_ctrl($obj, 'tbl_bdy_bg_odd_hvr', $opt[1]);
                $this->CHelper->clr($obj, 'tbl_bdy_clr_odd_hvr', $opt[1]);
            }, [$odd_slctr, $odd_slctr_hvr]);
            /**
             * Even Tab
             */
            $evn_slctr = $opt[0] . ':nth-child(even) td';
            $evn_slctr_hvr = $opt[0] . ':nth-child(even):hover td';
            $this->CHelper->add_tb($obj, 'tbl_bdy_t2', esc_html__('Even', 'addons-for-elementor-builder'), function ($obj, $opt) {
                $this->CHelper->hed($obj, 'tbl_bdy_nrml_evn', esc_html__('Normal', 'addons-for-elementor-builder'));
                $this->CHelper->bg_grp_ctrl($obj, 'tbl_bdy_bg_evn', $opt[0]);
                $this->CHelper->clr($obj, 'tbl_bdy_clr_evn', $opt[0]);
                $this->CHelper->dvdr($obj, 'div_25');
                $this->CHelper->hed($obj, 'tbl_bdy_hvr_evn', esc_html__('Hover', 'addons-for-elementor-builder'));
                $this->CHelper->bg_grp_ctrl($obj, 'tbl_bdy_bg_evn_hvr', $opt[1]);
                $this->CHelper->clr($obj, 'tbl_bdy_clr_evn_hvr', $opt[1]);
            }, [$evn_slctr, $evn_slctr_hvr]);
            $obj->end_controls_tabs();
            $this->CHelper->dvdr($obj, 'div_30');
            $slctr = $opt[0] . ' td';
            $this->CHelper->typo($obj, 'tbl_bdy_typo', $slctr, esc_html__('Typography', 'addons-for-elementor-builder'));
            $this->CHelper->res_talmnt($obj, 'tbl_bdy_almnt', $slctr);
            $this->CHelper->res_pad($obj, 'tbl_bdy_pad', $slctr);
        }, [$tbl_bdy_slctr]);
    }

    /**
     * Render attributes
     *
     * @since 1.2.0
     * 
     * @param array $settings
     */
    protected function render_attrs($settings = [])
    {
        $btns = [];
        $str_btns = '';
        $settings['btn_itms'] = empty($settings['btn_itms']) ? [] : $settings['btn_itms'];
        foreach ($settings['btn_itms'] as $item):
            if (isset($item['btn_typ']) && strpos($str_btns, $item['btn_typ']) === false) {
                ob_start();
                Icons_Manager::render_icon(!empty($item['btn_bfr_ic']) ? $item['btn_bfr_ic'] : '', ['class' => 'afeb-bfr-ic']);
                $bfr_ic = ob_get_clean();

                ob_start();
                Icons_Manager::render_icon(!empty($item['btn_aftr_ic']) ? $item['btn_aftr_ic'] : '', ['class' => 'afeb-aftr-ic']);
                $aftr_ic = ob_get_clean();

                array_push($btns, [
                    'className' => 'elementor-repeater-item-' . esc_attr($item['_id']),
                    'extend' => esc_attr(strtolower($item['btn_typ'])),
                    'text' => ($bfr_ic) . (!empty($item['btn_txt']) ? esc_attr($item['btn_txt']) : '') . ($aftr_ic),
                    'titleAttr' => !empty($item['btn_ttl']) ? esc_attr($item['btn_ttl']) : ''
                ]);

                $str_btns .= $item['btn_typ'];
            } else {
                $this->BTN_Error = true;
            }
        endforeach;

        ob_start();
        Icons_Manager::render_icon($settings['srch_lbl_bfr_ic']);
        $srch_lbl_bfr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['srch_lbl_aftr_ic']);
        $srch_lbl_aftr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_frst_bfr_ic']);
        $frst_bfr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_frst_aftr_ic']);
        $frst_aftr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_lst_bfr_ic']);
        $lst_bfr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_lst_aftr_ic']);
        $lst_aftr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_nxt_bfr_ic']);
        $nxt_bfr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_nxt_aftr_ic']);
        $nxt_aftr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_prv_bfr_ic']);
        $prv_bfr_ic = ob_get_clean();

        ob_start();
        Icons_Manager::render_icon($settings['pge_prv_aftr_ic']);
        $prv_aftr_ic = ob_get_clean();

        $lnt_mnu = !empty($settings['lnt_mnu']) ?
            explode(',', preg_replace('/\s+/', '', $settings['lnt_mnu'])) :
            [5, 10, 25, 50, 100];
        $pge_lnt = empty($settings['pge_lnt']) ? 10 : intval($settings['pge_lnt']);

        $ary_srch = array_search($pge_lnt, $lnt_mnu);
        if (!$ary_srch) {
            $lnt_mnu = array_merge($lnt_mnu, [$pge_lnt]);
            sort($lnt_mnu);
        }

        $this->add_render_attribute(
            [
                'data_table' => [
                    'class' => 'afeb-data-table afeb-alia-theme',
                    'data-settings' => [
                        wp_json_encode([
                            'src' => isset($settings['src']) ? esc_attr($settings['src']) : 'cstm',
                            'csv_src' => isset($settings['csv']['url']) ? esc_url($settings['csv']['url']) : '',
                            'buttons' => empty($settings['btns']) ? [] : $btns,
                            'dom' => '<"dt-layout-row"B><"dt-layout-row" <"dt-layout-cell dt-layout-start"l><"dt-layout-cell dt-layout-end"f>>rt' .
                                '<"dt-layout-row" <"dt-layout-cell dt-layout-start"i><"dt-layout-cell dt-layout-end"p>>',
                            'info' => empty($settings['info']) ?  false : true,
                            'lengthChange' => empty($settings['lnt_chng']) ?  false : true,
                            'lengthMenu' => array_merge($lnt_mnu),
                            'language' => [
                                'emptyTable' => !empty($settings['trnsltn_mre_empty_tbl']) ? esc_attr($settings['trnsltn_mre_empty_tbl']) : '',
                                'info' => !empty($settings['trnsltn_mre_info']) ? esc_attr(str_replace('[', '_', str_replace(']', '_', $settings['trnsltn_mre_info']))) :
                                    sprintf(esc_html__('Showing page %s of %s', 'addons-for-elementor-builder'), '_PAGE_', '_PAGES_'),
                                'infoEmpty' => !empty($settings['trnsltn_mre_info_empty']) ? esc_attr($settings['trnsltn_mre_info_empty']) : '',
                                'infoFiltered' => sprintf(esc_html__('%s-%sfiltered from %s records', 'addons-for-elementor-builder'), ' ', ' ', '_MAX_'),
                                'lengthMenu' => !empty($settings['trnsltn_mre_lnt_mnu']) ? esc_attr(str_replace('[', '_', str_replace(']', '_', $settings['trnsltn_mre_lnt_mnu']))) :
                                    sprintf(esc_html__('Show %s entries', 'addons-for-elementor-builder'), '_MENU_'),
                                'loadingRecords' => !empty($settings['trnsltn_mre_ldng_rcrd']) ? esc_attr($settings['trnsltn_mre_ldng_rcrd']) : '',
                                'paginate' => [
                                    'first' => ($frst_bfr_ic) . (!empty($settings['pge_frst']) ? esc_attr($settings['pge_frst']) : '') . ($frst_aftr_ic),
                                    'last' => ($lst_bfr_ic) . (!empty($settings['pge_lst']) ? esc_attr($settings['pge_lst']) : '') . ($lst_aftr_ic),
                                    'next' => ($nxt_bfr_ic) . (!empty($settings['pge_nxt']) ? esc_attr($settings['pge_nxt']) : '') . ($nxt_aftr_ic),
                                    'previous' => ($prv_bfr_ic) . (!empty($settings['pge_prv']) ? esc_attr($settings['pge_prv']) : '') . ($prv_aftr_ic)
                                ],
                                'search' => ($srch_lbl_bfr_ic) . (!empty($settings['srch_lbl']) ? esc_attr($settings['srch_lbl']) : '') . ($srch_lbl_aftr_ic),
                                'searchPlaceholder' => !empty($settings['srch_plc_hldr']) ? esc_attr($settings['srch_plc_hldr']) : '',
                                'thousands' => ',',
                                'url' => '',
                                'zeroRecords' => !empty($settings['trnsltn_mre_zro_rcrd']) ? esc_attr($settings['trnsltn_mre_zro_rcrd']) : ''
                            ],
                            'ordering' => empty($settings['ordr']) ?  false : true,
                            'paging' => empty($settings['pge']) ?  false : true,
                            'pageLength' => $pge_lnt,
                            'pagingType' => empty($settings['pge_typ']) ? 'simple_numbers' : esc_attr($settings['pge_typ']),
                            'searching' => empty($settings['srch']) ?  false : true,
                            'scrollX' => true
                        ])
                    ]
                ]
            ]
        );

        echo wp_kses_post($this->get_render_attribute_string('data_table'));
    }

    /**
     * Render DataTable widget output on the frontend
     *
     * @since 1.2.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $wid = $this->get_id();
?>
        <div <?php $this->render_attrs($settings); ?>>
            <?php
            if ($this->BTN_Error):
                echo wp_kses(
                    Helper::front_notice(
                        esc_html__('You cannot add a repeated button. Please modify it to another button or delete it', 'addons-for-elementor-builder'),
                        'error'
                    ),
                    Helper::allowed_tags(['div'])
                );
            elseif ($settings['src'] == 'cstm'):
                $header_count = count($settings['hdr_lbls']) + 1;
                $row_count = floor(count($settings['bdy_itms']) / $header_count);
                $count = 0;
                foreach ($settings['bdy_itms'] as $item):
                    if ($item['bdy_acts'] == 'Start Row')
                        $count++;
                endforeach;
                if ($count == $row_count): ?>
                    <table id="afeb-inner-table-<?php echo esc_attr($wid); ?>" class="afeb-inner-table cell-border">
                        <thead>
                            <tr>
                                <?php foreach ($settings['hdr_lbls'] as $item): ?>
                                    <th class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">
                                        <?php echo wp_kses_post(do_shortcode($item['hdr_cnt'])); ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php foreach ($settings['bdy_itms'] as $item): ?>
                                    <?php if ($item['bdy_acts'] == 'Cell'): ?>
                                        <td class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">
                                            <?php echo wp_kses_post(do_shortcode($item['bdy_cnt'])); ?>
                                        </td>
                                    <?php else:
                                        echo '</tr><tr>';
                                    endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                <?php
                elseif ($count < $row_count):
                    echo wp_kses(
                        Helper::front_notice(
                            esc_html__('You must start a new row, Please change the action to "Start New Row"', 'addons-for-elementor-builder'),
                            'error'
                        ),
                        Helper::allowed_tags(['div'])
                    );
                else:
                    echo wp_kses(
                        Helper::front_notice(
                            esc_html__('You must add a new cell, Please change the action to "Add New Cell"', 'addons-for-elementor-builder'),
                            'error'
                        ),
                        Helper::allowed_tags(['div'])
                    );
                endif;
                ?>
            <?php endif; ?>
        </div>
<?php
    }
}
