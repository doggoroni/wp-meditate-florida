<?php

namespace AFEB;

use AFEB\Controls\CHelper;
use AFEB\PostTypes\Builder;
use AFEB\PostTypes\Popup;
use Elementor\Core\Documents_Manager;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Documents Class
 * 
 * @class Documents
 * @version 1.2.0
 */
class Documents extends Base
{
    /**
     * @var CHelper
     */
    private $CHelper;

    /**
     * @var Widgets
     */
    public $Widgets;

    /**
     * Initialize "Vertex Addons for Elementor" Documents
     * 
     * @since 1.2.0
     */
    public function init()
    {
        $this->CHelper = new CHelper();
        $this->Widgets = new Widgets();
        $this->actions();
    }

    /**
     * Documents Class Actions
     * 
     * @since 1.2.0
     */
    public function actions()
    {
        add_action('elementor/documents/register', [$this, 'register_document_type']);
        add_action('afeb/document/settings/after_render_setting_section', [$this, 'add_request_feature_section']);
        add_action('afeb/document/settings/after_render_setting_section', [$this, 'add_bug_report_section']);
    }

    /**
     * Register document type for the popup creator
     *
     * @param Documents_Manager $documents_manager
     * @since 1.2.0
     *
     */
    public function register_document_type(Documents_Manager $documents_manager)
    {
        new Documents\Builder();
        new Documents\Popup();

        $documents_manager->register_document_type(Builder::BUILDER_POST_TYPE, Documents\Builder::class);
        $documents_manager->register_document_type(Popup::POPUP_POST_TYPE, Documents\Popup::class);
    }

    /**
     * Add feature request section in all documents
     *
     * @param Documents\Popup $document
     * @since 1.2.0
     *
     */
    public function add_request_feature_section(Documents\Popup $document)
    {
        $this->CHelper->add_set_sctn($document, 'ranf', esc_html__('Request a New Feature', 'addons-for-elementor-builder'), function ($obj) {
            $this->Widgets->request_feature_section($this->CHelper, $obj);
        });
    }

    /**
     * Add bug report section in all documents
     * 
     * @since 1.2.0
     * 
     * @param Documents\Popup $document
     */
    public function add_bug_report_section(Documents\Popup $document)
    {
        $this->CHelper->add_set_sctn($document, 'rab', esc_html__('Report a Bug', 'addons-for-elementor-builder'), function ($obj) {
            $this->Widgets->bug_report_section($this->CHelper, $obj);
        });
    }
}
