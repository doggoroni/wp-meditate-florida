<?php

namespace AFEB\Widgets\SearchForm;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" SearchForm Widget Class
 * 
 * @class SearchForm
 * @version 1.0.0
 */
class SearchForm extends Widget_Base
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
     * SearchForm Constructor
     * 
     * @since 1.0.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new CHelper($this);
        $this->assets->search_form_style();
        $this->assets->search_form_script();
    }

    /**
     * Get widget name
     *
     * @since 1.0.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_search_form';
    }

    /**
     * Get widget title
     *
     * @since 1.0.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Search Form (AJAX)', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.0.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-search-form';
    }

    /**
     * Get widget categories
     *
     * @since 1.0.0
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
     * @since 1.0.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['search_form', 'searchform', esc_html__('Search Form', 'addons-for-elementor-builder')];
    }

    /**
     * Retrieve the list of style dependencies the widget requires
     *
     * @since 1.0.0
     *
     * @return array Widget style dependencies
     */
    public function get_style_depends(): array
    {
        return ['afeb-search-form-style'];
    }

    /**
     * Retrieve the list of script dependencies the widget requires
     *
     * @since 1.5.0
     *
     * @return array Widget script dependencies
     */
    public function get_script_depends()
    {
        return ['afeb-search-form-script'];
    }

    /**
     * Register SearchForm widget controls
     *
     * @since 1.0.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('cs1', [
            'label' => esc_html__('Search Form', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->heading('heading_input', [
                'label' => esc_html__('Input', 'addons-for-elementor-builder'),
            ]);


            $this->controls->text('plcdr', [
                'label' => esc_html__('Placeholder', 'addons-for-elementor-builder'),
                'label_block' => true,
                'default' => esc_html__('Search...', 'addons-for-elementor-builder'),
            ]);

            $this->controls->heading('heading_submit', [
                'label' => esc_html__('Submit', 'addons-for-elementor-builder'),
            ]);

            $this->controls->text('btn_txt', [
                'label' => esc_html__('Button Text', 'addons-for-elementor-builder'),
                'label_block' => true,
                'default' => esc_html__('Search', 'addons-for-elementor-builder'),
            ]);

            $icon_prefix = Icons_Manager::is_migration_allowed() ? 'fas ' : 'fa ';
            $this->controls->icons('btn_ic', [
                'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
                'fa4compatibility' => 'icon',
                'default' => ['value' => $icon_prefix . 'fa-search', 'library' => 'fa-solid',],
            ]);
        });
        /**
         *
         * Results
         *
         */
        $this->controls->tab_content_section('results_content_section', [
            'label' => esc_html__('Results', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->yn_switcher('live_results', [
                'label' => esc_html__('Live Results', 'addons-for-elementor-builder'),
                'default' => '',
                'frontend_available' => true,
            ]);

            $this->controls->dynamic_select('template_id', [
                'label' => esc_html__('Choose a template', 'addons-for-elementor-builder'),
                'options' => 'get_templates',
                'query_slug' => 'dynamic-loop-item',
                'condition' => ['live_results!' => '',]
            ]);

            $this->controls->hidden('minimum_search_characters', [
                'default' => 3,
                'condition' => ['live_results!' => '', 'template_id!' => '',]
            ]);

            /*$this->controls->select('number_of_columns', [
                'label' => esc_html__('Columns', 'addons-for-elementor-builder'),
                'options' => [
                    24 => esc_html__('Four', 'addons-for-elementor-builder'),
                    32 => esc_html__('Three', 'addons-for-elementor-builder'),
                    49 => esc_html__('Two', 'addons-for-elementor-builder'),
                    99 => esc_html__('One', 'addons-for-elementor-builder')
                ],
                'default' => 99,
                'condition' => ['live_results' => 'yes', 'template_id!' => '',],
            ]);*/

            $this->controls->number('number_of_items', [
                'label' => esc_html__('Items', 'addons-for-elementor-builder'),
                'default' => 5,
                'min' => 1,
                'condition' => ['live_results' => 'yes', 'template_id!' => '',],
            ]);

            $this->controls->yn_switcher('enable_loader', [
                'label' => esc_html__('Enable Loader', 'addons-for-elementor-builder'),
                'default' => '',
                'condition' => ['live_results' => 'yes', 'template_id!' => '',],
                'separator' => 'before',
            ]);

            $this->controls->text_area('nothing_found_message_text', [
                'label' => esc_html__('Nothing Found Message', 'addons-for-elementor-builder'),
                'default' => esc_html__('It seems we can’t find what you’re looking for.', 'addons-for-elementor-builder'),
                'condition' => ['live_results' => 'yes', 'template_id!' => '',],
            ]);

            $this->controls->select('nothing_found_message_html_tag', [
                'label' => esc_html__('HTML Tag', 'addons-for-elementor-builder'),
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                ],
                'default' => 'div',
                'condition' => ['enable_nothing_found_message!' => '',],
            ]);
        });
        /**
         *
         * Query
         *
         */
        $this->controls->tab_content_section('query_content_section', [
            'label' => esc_html__('Query', 'addons-for-elementor-builder')
        ], function () {

            $post_types = get_post_types(['public' => true], 'objects');
            unset($post_types['attachment']);
            $types = ['' => esc_html__('All', 'addons-for-elementor-builder')];
            foreach ($post_types as $post_type) {
                $types[$post_type->name] = $post_type->labels->singular_name;
            }

            $this->controls->select('ptyp', [
                'label' => esc_html__('Source', 'addons-for-elementor-builder'),
                'options' => $types,
                'default' => '',
            ]);

            $this->controls->select('orderby', [
                'label' => esc_html__('Sort By', 'addons-for-elementor-builder'),
                'options' => [
                    '' => esc_html__('Default', 'addons-for-elementor-builder'),
                    'title' => esc_html__('Title', 'addons-for-elementor-builder'),
                    'ID' => esc_html__('ID', 'addons-for-elementor-builder'),
                    'date' => esc_html__('Date', 'addons-for-elementor-builder'),
                    'author' => esc_html__('Author', 'addons-for-elementor-builder'),
                    'comment_count' => esc_html__('Comment Count', 'addons-for-elementor-builder'),
                    'rand' => esc_html__('Random', 'addons-for-elementor-builder'),
                    'modified' => esc_html__('Modified', 'addons-for-elementor-builder'),
                    'parent' => esc_html__('Parent ID', 'addons-for-elementor-builder'),
                ],
            ]);

            $this->controls->choose('order', [
                'label' => esc_html__('Order', 'addons-for-elementor-builder'),
                'options' => [
                    'ASC' => ['title' => esc_html__('Ascending', 'addons-for-elementor-builder'), 'icon' => 'eicon-arrow-up'],
                    'DESC' => ['title' => esc_html__('Descending', 'addons-for-elementor-builder'), 'icon' => 'eicon-arrow-down'],
                ],
                'default' => 'DESC',
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Input Box
         *
         */
        $this->controls->tab_style_section('ss3', [
            'label' => esc_html__('Input Box', 'addons-for-elementor-builder'),
        ], function () {

            $this->controls->background([
                'name' => 'box_bg',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-search-form'
            ]);

            $this->controls->text_color('bx_txt_clr', [
                'selectors' => ['{{WRAPPER}} .afeb-search-form input[type=text]' => 'color: {{VALUE}}; fill: {{VALUE}};',],
            ]);

            $this->controls->text_color('plcdr_txt_clr', [
                'label' => esc_html__('Placeholder Color', 'addons-for-elementor-builder'),
                'selectors' => ['{{WRAPPER}} .afeb-search-form input[type=text]::placeholder' => 'color: {{VALUE}}; fill: {{VALUE}};',],
            ]);

            $this->controls->typography([
                'label' => esc_html__('Text Typography', 'addons-for-elementor-builder'),
                'name' => 'inpt_typo',
                'selector' => '{{WRAPPER}} .afeb-search-form input[type=text]',
            ]);

            $this->controls->typography([
                'label' => esc_html__('Placeholder Typography', 'addons-for-elementor-builder'),
                'name' => 'plcdr_typo',
                'selector' => '{{WRAPPER}} .afeb-search-form input[type=text]::placeholder',
            ]);

            $this->controls->responsive()->padding('bx_pad', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'bx_brdr',
                'selector' => '{{WRAPPER}} .afeb-search-form',
                'separator' => '',
            ]);

            $this->controls->responsive()->border_radius('bx_rdus', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        });
        /**
         *
         * Button
         *
         */
        $this->controls->tab_style_section('ss1', [
            'label' => esc_html__('Button', 'addons-for-elementor-builder'),
            'condition' => ['btn_txt!' => '',],
        ], function () {

            $this->controls->tabs('btn_stl_tbs', [
                't1' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'btn_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-search-form-btn',
                        ]);

                        $this->controls->text_color('btn_clr', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-search-form-btn' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'btn_typography',
                            'selector' => '{{WRAPPER}} .afeb-search-form-btn',
                        ]);

                        $this->controls->responsive()->padding('btn_padd', [
                            'size_units' => ['px', 'em', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-search-form-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'btn_brdr',
                            'selector' => '{{WRAPPER}} .afeb-search-form-btn',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('btn_rdus', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-search-form-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'btn_bx_shdo',
                            'selector' => '{{WRAPPER}} .afeb-search-form-btn',
                        ]);
                    },
                ],
                't2' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'button_background_hover',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-search-form-btn:hover',
                        ]);

                        $this->controls->text_color('btn_clr_hvr', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-search-form-btn:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
                            ],
                        ]);

                        $this->controls->responsive()->padding('btn_pad_hvr', [
                            'size_units' => ['px', 'em', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-search-form-btn:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'btn_brdr_hvr',
                            'selector' => '{{WRAPPER}} .afeb-search-form-btn:hover',
                            'separator' => '',
                        ]);

                        $this->controls->responsive()->border_radius('btn_rdus_hvr', [
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-search-form-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]);

                        $this->controls->box_shadow([
                            'name' => 'btn_bx_shdo_hvr',
                            'selector' => '{{WRAPPER}} .afeb-search-form-btn:hover',
                        ]);
                    },
                ],
            ]);
        });
        /**
         *
         * Icon Style
         *
         */
        $this->controls->tab_style_section('ss2', [
            'label' => esc_html__('Icon', 'addons-for-elementor-builder'),
            'condition' => ['btn_ic[value]!' => '']
        ], function () {

            $this->controls->background([
                'name' => 'ic_bg',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon',
            ]);

            $this->controls->color('ic_clr', [
                'label' => esc_html__('Icon Color', 'addons-for-elementor-builder'),
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon > i,' .
                        '{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon > svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]);

            $this->controls->responsive()->slider('ic_size', [
                'label' => esc_html__('Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => ['px' => ['min' => 20, 'max' => 50,],],
                'selectors' => ['{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon > i,' .
                    '{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon > svg' => 'width: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->responsive()->slider('ic_vp', [
                'label' => esc_html__('Vertical Position', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => ['px' => ['min' => -200, 'max' => 200,],],
                'selectors' => ['{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon' => 'top: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->responsive()->slider('ic_hp', [
                'label' => esc_html__('Horizontal Position', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => ['px' => ['min' => -200, 'max' => 200,],],
                'selectors' => ['{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon' => 'left: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->responsive()->padding('ic_pad', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'ic_brdr',
                'selector' => '{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon',
                'separator' => '',
            ]);

            $this->controls->responsive()->border_radius('ic_rdus', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form-btn .afeb-search-form-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        });
        /**
         *
         * Results
         *
         */
        $this->controls->tab_style_section('results_style_section', [
            'label' => esc_html__('Results', 'addons-for-elementor-builder'),
            'condition' => [
                'live_results' => 'yes',
                'template_id!' => '',
            ],
        ], function () {

            $this->controls->background([
                'name' => 'results_background',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-search-form-live-results',
            ]);

            $this->controls->responsive()->slider('results_max_height', [
                'label' => esc_html__('Max Height', 'addons-for-elementor-builder'),
                'size_units' => ['px', '%', 'em', 'rem', 'vh', 'custom'],
                'range' => [
                    'px' => ['max' => 500,],
                    '%' => ['max' => 50,],
                    'em' => ['max' => 50,],
                    'rem' => ['max' => 50,],
                    'vw' => ['max' => 50,],
                ],
                'selectors' => ['{{WRAPPER}} .afeb-search-form-live-results' => 'max-height: {{SIZE}}{{UNIT}}',],
            ]);

            $this->controls->responsive()->margin('results_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form-live-results' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('results_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form-live-results' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'results_border',
                'selector' => '{{WRAPPER}} .afeb-search-form-live-results',
                'separator' => '',
            ]);

            $this->controls->responsive()->border_radius('results_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form-live-results' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'results_shadow',
                'selector' => '{{WRAPPER}} .afeb-search-form-live-results',
            ]);
        });
        /**
         *
         * Loader
         *
         */
        $this->controls->tab_style_section('loader_style_section', [
            'label' => esc_html__('Loader', 'addons-for-elementor-builder'),
            'condition' => [
                'live_results' => 'yes',
                'template_id!' => '',
                'enable_loader' => 'yes',
            ],
        ], function () {

            $this->controls->color('loader_color', [
                'label' => esc_html__('Color', 'addons-for-elementor-builder'),
                'selectors' => ['{{WRAPPER}} .afeb-search-form-live-results-loader svg' => 'fill: {{VALUE}}',],
            ]);

            $this->controls->responsive()->slider('loader_size', [
                'label' => esc_html__('Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'selectors' => ['{{WRAPPER}} .afeb-search-form-live-results-loader' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',],
            ]);
        });
        /**
         *
         * Nothing Found Message
         *
         */
        $this->controls->tab_style_section('nothing_found_message_style_section', [
            'label' => esc_html__('Nothing Found Message', 'addons-for-elementor-builder'),
            'condition' => ['live_results' => 'yes', 'template_id!' => '',],
        ], function () {

            $this->controls->responsive()->alignment('nothing_found_message_alignment', [
                'selectors' => ['{{WRAPPER}} .afeb-search-form-live-results-nothing-found-message' => 'text-align: {{VALUE}};',],
            ]);

            $this->controls->text_color('nothing_found_message_color', [
                'selectors' => ['{{WRAPPER}} .afeb-search-form-live-results-nothing-found-message' => 'color: {{VALUE}}',],
            ]);

            $this->controls->typography([
                'name' => 'nothing_found_message_typography',
                'selector' => '{{WRAPPER}} .afeb-search-form-live-results-nothing-found-message',
            ]);

            $this->controls->responsive()->padding('nothing_found_message_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-search-form-live-results-nothing-found-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->text_shadow([
                'selector' => '{{WRAPPER}} .afeb-search-form-live-results-nothing-found-message',
            ]);

            $this->controls->text_stroke([
                'selector' => '{{WRAPPER}} .afeb-search-form-live-results-nothing-found-message',
            ]);
        });
    }

    /**
     * Render SearchForm widget output on the frontend
     *
     * @since 1.0.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
?>
        <div class="afeb-search-form">
            <form action="<?php echo esc_url(home_url()); ?>" method="get">
                <input type="text" name="s" placeholder="<?php echo esc_attr($settings['plcdr']); ?>" autocomplete="off" />
                <?php if (!empty($settings['btn_txt']) || !empty($settings['btn_ic']['value'])): ?>
                    <button type="submit" class="afeb-search-form-btn">
                        <?php echo esc_html($settings['btn_txt']); ?>
                        <div class="afeb-search-form-icon">
                            <?php Icons_Manager::render_icon($settings['btn_ic']); ?>
                        </div>
                    </button>
                <?php endif; ?>
                <?php if (!empty($settings['ptyp']) && $settings['ptyp'] !== ''): ?>
                    <input type="hidden" name="post_type" value="<?php echo esc_attr($settings['ptyp']); ?>">
                <?php endif; ?>
                <?php if (!empty($settings['orderby'])): ?>
                    <input type="hidden" name="orderby" value="<?php echo esc_attr($settings['orderby']); ?>">
                <?php endif; ?>
                <?php if (!empty($settings['order'])): ?>
                    <input type="hidden" name="order" value="<?php echo esc_attr($settings['order']); ?>">
                <?php endif; ?>
            </form>
            <?php $this->live_results($settings); ?>
        </div>
        <?php
    }

    protected function live_results($settings = [])
    {
        if (!empty($settings['live_results']) && !empty($settings['template_id'])) { ?>
            <input type="hidden" name="template_id" value="<?php echo intval($settings['template_id']); ?>">
            <input type="hidden" name="number_of_items" value="<?php echo intval($settings['number_of_items']); ?>">
            <div class="afeb-search-form-live-results">
                <div class="afeb-search-form-live-results-flex">
                    <?php if (!empty($settings['enable_loader'])): ?>
                        <div class="afeb-search-form-live-results-loader fa fa-spin">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28">
                                <path fill-rule="evenodd" d="M14 .188c.587 0 1.063.475 1.063 1.062V5.5a1.063 1.063 0 0 1-2.126 0V1.25c0-.587.476-1.063 1.063-1.063ZM4.182 4.181a1.063 1.063 0 0 1 1.503 0L8.73 7.228A1.062 1.062 0 1 1 7.228 8.73L4.182 5.685a1.063 1.063 0 0 1 0-1.503Zm19.636 0a1.063 1.063 0 0 1 0 1.503L20.772 8.73a1.062 1.062 0 1 1-1.502-1.502l3.045-3.046a1.063 1.063 0 0 1 1.503 0ZM.188 14c0-.587.475-1.063 1.062-1.063H5.5a1.063 1.063 0 0 1 0 2.126H1.25A1.063 1.063 0 0 1 .187 14Zm21.25 0c0-.587.475-1.063 1.062-1.063h4.25a1.063 1.063 0 0 1 0 2.126H22.5A1.063 1.063 0 0 1 21.437 14ZM8.73 19.27a1.062 1.062 0 0 1 0 1.502l-3.045 3.046a1.063 1.063 0 0 1-1.503-1.503l3.046-3.046a1.063 1.063 0 0 1 1.502 0Zm10.54 0a1.063 1.063 0 0 1 1.502 0l3.046 3.045a1.063 1.063 0 0 1-1.503 1.503l-3.046-3.046a1.063 1.063 0 0 1 0-1.502ZM14 21.438c.587 0 1.063.475 1.063 1.062v4.25a1.063 1.063 0 0 1-2.126 0V22.5c0-.587.476-1.063 1.063-1.063Z" />
                            </svg>
                        </div>
                    <?php endif; ?>

                    <div class="afeb-search-form-live-results-content"></div>

                    <?php $nothing_found_message_html_tag = Utils::validate_html_tag($settings['nothing_found_message_html_tag']); ?>
                    <<?php Utils::print_validated_html_tag($nothing_found_message_html_tag); ?> class="afeb-search-form-live-results-nothing-found-message">
                        <?php
                        if (!trim($settings['nothing_found_message_text'] . '')) {
                            $settings['nothing_found_message_text'] = esc_html__('It seems we can’t find what you’re looking for.', 'addons-for-elementor-builder');
                        }

                        echo wp_kses_post($settings['nothing_found_message_text']);
                        ?>
                    </<?php Utils::print_validated_html_tag($nothing_found_message_html_tag); ?>>
                </div>
            </div>
<?php
        }
    }
}
