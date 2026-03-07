jQuery(document).ready(function ($) {
    elementor.on("panel:init", function () {
        let custom_css_js_id = ['afeb_cstm_hdr_js', 'afeb_cstm_ftr_js'];
        $.each(custom_css_js_id, function (i, value) {
            elementor.settings.page.addChangeCallback(value, function () {
                let attrs = this.model.attributes;
                let data = {
                    afeb_cstm_hdr_js: attrs.afeb_cstm_hdr_js,
                    afeb_cstm_ftr_js: attrs.afeb_cstm_ftr_js,
                    afeb_cstm_js: value,
                }

                $("#elementor-preview-iframe")[0].contentWindow.postMessage(data);
            });
        });

        let goingup_controls_id = ['afeb_gup_ic', 'afeb_gup_ttl_sh', 'afeb_gup_ttl'];
        $.each(goingup_controls_id, function (i, value) {
            elementor.settings.page.addChangeCallback(value, function () {
                let attrs = this.model.attributes;
                let data = {
                    afeb_gup_ic: attrs.afeb_gup_ic,
                    afeb_gup_ttl_sh: attrs.afeb_gup_ttl_sh,
                    afeb_gup_ttl: attrs.afeb_gup_ttl,
                }

                $("#elementor-preview-iframe")[0].contentWindow.postMessage(data);
            });
        });

        let preloader_controls_id = [
            'afeb_preloader_loader',
            'afeb_preloader_loader_animation',
            'afeb_preloader_entrance_animation',
            'afeb_preloader_exit_animation',
            'afeb_preloader_animation_duration'
        ];
        let preloader_post_message_timeout = null;
        $.each(preloader_controls_id, function (i, value) {
            elementor.settings.page.addChangeCallback(value, function (value) {
                let $this = this;

                clearTimeout(preloader_post_message_timeout);
                preloader_post_message_timeout = setTimeout(function () {
                    let attrs = $this.model.attributes;
                    let data = {
                        afeb_preloader_loader: attrs.afeb_preloader_loader,
                        afeb_preloader_loader_animation: attrs.afeb_preloader_loader_animation,
                        afeb_preloader_entrance_animation: attrs.afeb_preloader_entrance_animation,
                        afeb_preloader_exit_animation: attrs.afeb_preloader_exit_animation,
                        afeb_preloader_animation_duration: attrs.afeb_preloader_animation_duration,
                    }

                    $("#elementor-preview-iframe")[0].contentWindow.postMessage(data);
                }, 300);
            });
        });
    });

    elementor.hooks.addFilter('editor/style/styleText', function (css, context) {
        if (!context) {
            return;
        }

        let model = context.model,
            custom_css = model.get('settings').get('afeb_cstm_hdr_css'),
            selector = '.elementor-element.elementor-element-' + model.get('id');

        if ('document' === model.get('elType')) {
            selector = elementor.config.document.settings.cssWrapperSelector;
        }

        if (custom_css) {
            css += custom_css.replace(/selector/g, selector);
        }

        return css;
    });

    elementor.channels.editor.on('section:activated', function (section_name, editor) {
        let edited_element = editor.getOption('editedElementView');

        if (edited_element.model.get('widgetType') == 'afeb_flip_box') {
            let is_side_b_section = ['back_content_section', 'back_style_section'].indexOf(section_name) !== -1;
            edited_element.$el.toggleClass('afeb-flip-box-flipped', is_side_b_section);
            let $back_layer = edited_element.$el.find('.afeb-flip-box-back');

            if (is_side_b_section) {
                $back_layer.css('transition', 'none');
            }
            if (!is_side_b_section) {
                setTimeout(function () {
                    $back_layer.css('transition', '');
                }, 10);
            }
        }
    });

    $(document).off('change', 'select[data-setting="template_type"]')
        .on('change', 'select[data-setting="template_type"]', function () {
            $e.run('document/save/auto', {
                force: true,
                onSuccess: function () {
                    elementor.reloadPreview();
                    elementor.once('preview:loaded', () => {
                        $e.route('panel/page-settings/settings');
                    });
                }
            });
        });

    $(document).off('click', '.afeb-update-preview>button')
        .on('click', '.afeb-update-preview>button', function () {
            $e.run('document/save/auto', {
                force: true,
                onSuccess: function () {
                    elementor.dynamicTags.cleanCache();
                    let is_initial_document = elementor.config.initial_document.id === elementor.documents.getCurrentId();

                    if (is_initial_document) elementor.reloadPreview();
                    else $e.internal('editor/documents/attach-preview');
                }
            });
        });

    setInterval(function () {
        let body = $('body');
        let preloader_section = $('.elementor-control-afeb-stng-ext-preloader');

        if (preloader_section.length) {
            let preloader_section_parent = preloader_section.closest('#elementor-kit-panel-content');

            if (preloader_section_parent.data('afeb-preloader-show-preview-action') != 'yes') {
                let data = {
                    afeb_preloader_loader: elementor.settings.page.model.get('afeb_preloader_loader'),
                    afeb_preloader_loader_animation: elementor.settings.page.model.get('afeb_preloader_loader_animation'),
                    afeb_preloader_entrance_animation: elementor.settings.page.model.get('afeb_preloader_entrance_animation'),
                    afeb_preloader_exit_animation: elementor.settings.page.model.get('afeb_preloader_exit_animation'),
                    afeb_preloader_animation_duration: elementor.settings.page.model.get('afeb_preloader_animation_duration'),
                    afeb_preloader_show_preview_action: 'yes',
                };

                $("#elementor-preview-iframe")[0].contentWindow.postMessage(data);
                preloader_section.closest('#elementor-kit-panel-content').data('afeb-preloader-show-preview-action', 'yes');
                body.data('afeb-preloader-show-preview-action', 'yes');
            }
        } else if (body.data('afeb-preloader-show-preview-action') == 'yes') {
            let data = { afeb_preloader_show_preview_action: 'no', };

            $("#elementor-preview-iframe")[0].contentWindow.postMessage(data);
            body.data('afeb-preloader-show-preview-action', '');
        }

        $(document).find('.elementor-control-display_condition_archive_advanced_section')
            .parent().find('.elementor-control-display_condition.elementor-control-type-repeater select[data-setting="conditon_group"]').val('archive').change();

        $(document).find('.elementor-control-display_condition_single_advanced_section')
            .parent().find('.elementor-control-display_condition.elementor-control-type-repeater select[data-setting="conditon_group"]').val('singular').change();
    }, 100);
});