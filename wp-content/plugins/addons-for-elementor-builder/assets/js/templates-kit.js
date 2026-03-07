jQuery(document).ready(function ($) {
    let afeb_templates_kit = {
        required_plugins: false,
        required_theme: true,
        events: function () {

            $(document).off('click', '.afeb-template-kit-section .afeb-template-kit-import-btn')
                .on('click', '.afeb-template-kit-section .afeb-template-kit-import-btn', function () {
                    let kit_section = $(this).closest('.afeb-template-kit-section');
                    let src = kit_section.find('.afeb-box-header>img');
                    let name = kit_section.find('.afeb-box-body>.afeb-element-search-text');
                    let dialog = $('#afeb-template-import-dialog');
                    let body = $('.afeb-body');

                    dialog.find('.afeb-template-import, .afeb-template-import-successful').hide();
                    $('.afeb-template-import-image').attr('src', src.attr('src'));
                    dialog.find('.afeb-template-import-name').text(name.text());
                    dialog.find('.afeb-template-import-confirmation').attr('style', 'display:flex !important');

                    if (!body.hasClass('ui-dialog-show'))
                        $('.afeb-body').addClass('ui-dialog-show');

                    $('#afeb-template-import-dialog').parent().find('.ui-dialog-titlebar-close').hide();

                    setTimeout(function () {
                        $('#afeb-template-import-dialog').dialog('open');
                    }, 100);
                });

            $(document).off('click', '.afeb-template-kit-cancel-btn')
                .on('click', '.afeb-template-kit-cancel-btn', function () {
                    $('#afeb-template-import-dialog').dialog('close');
                });

            $(document).off('click', '.afeb-template-import-confirmation .afeb-template-kit-import-btn')
                .on('click', '.afeb-template-import-confirmation .afeb-template-kit-import-btn', function () {
                    afeb_templates_kit.import_templates_kit('nonprofit-organization-v1');
                });

            $(document).off('click', '.afeb-template-import-progress-markers .afeb-refresh')
                .on('click', '.afeb-template-import-progress-markers .afeb-refresh', function () {
                    let $this = $(this);
                    let current_class = $this.attr('class');
                    let kit_id = 'nonprofit-organization-v1';

                    if (current_class.indexOf('markers-plugins') !== -1) {
                        let plugins = $('.afeb-template-kit-section-' + kit_id).data('plugins');

                        afeb_templates_kit.progress('plugins');
                        afeb_templates_kit.ajax_request({
                            action: 'afeb_activate_required_plugins',
                            plugins: plugins,
                        }, function (response) {

                            if (typeof response.success !== 'undefined' &&
                                response.success == true) {

                                afeb_templates_kit.progress('settings');
                                afeb_templates_kit.ajax_request({
                                    action: 'afeb_import_settings',
                                    afeb_templates_kit: kit_id,
                                }, function (response) {

                                    if (typeof response.success !== 'undefined' &&
                                        response.success == true) {

                                        afeb_templates_kit.progress('content');
                                        afeb_templates_kit.ajax_request({
                                            action: 'afeb_import_templates_kit',
                                            afeb_templates_kit: kit_id,
                                            afeb_templates_kit_single: false
                                        }, function (response) {

                                            if (typeof response.success !== 'undefined' &&
                                                response.success == true) {

                                                setTimeout(function () {
                                                    afeb_templates_kit.progress('finish');
                                                }, 1000);

                                            } else {

                                                if (typeof response.data !== 'undefined' &&
                                                    typeof response.data.message !== 'undefined') {
                                                    afeb_templates_kit.failed('content', response.data.message);
                                                }

                                            }

                                        });

                                    } else {

                                        if (typeof response.data !== 'undefined' &&
                                            typeof response.data.message !== 'undefined') {
                                            afeb_templates_kit.failed('settings', response.data.message);
                                        }

                                    }

                                });

                            } else {

                                if (typeof response.data !== 'undefined' &&
                                    typeof response.data.message !== 'undefined') {
                                    afeb_templates_kit.failed('plugins', response.data.message);
                                }

                            }

                        });

                    } else if (current_class.indexOf('markers-settings') !== -1) {

                        afeb_templates_kit.progress('settings');
                        afeb_templates_kit.ajax_request({
                            action: 'afeb_import_settings',
                            afeb_templates_kit: kit_id,
                        }, function (response) {

                            if (typeof response.success !== 'undefined' &&
                                response.success == true) {

                                afeb_templates_kit.progress('content');
                                afeb_templates_kit.ajax_request({
                                    action: 'afeb_import_templates_kit',
                                    afeb_templates_kit: kit_id,
                                    afeb_templates_kit_single: false
                                }, function (response) {

                                    if (typeof response.success !== 'undefined' &&
                                        response.success == true) {

                                        setTimeout(function () {
                                            afeb_templates_kit.progress('finish');
                                        }, 1000);

                                    } else {

                                        if (typeof response.data !== 'undefined' &&
                                            typeof response.data.message !== 'undefined') {
                                            afeb_templates_kit.failed('content', response.data.message);
                                        }

                                    }

                                });

                            } else {

                                if (typeof response.data !== 'undefined' &&
                                    typeof response.data.message !== 'undefined') {
                                    afeb_templates_kit.failed('settings', response.data.message);
                                }

                            }

                        });

                    } else if (current_class.indexOf('markers-content') !== -1) {

                        afeb_templates_kit.progress('content');
                        afeb_templates_kit.ajax_request({
                            action: 'afeb_import_templates_kit',
                            afeb_templates_kit: kit_id,
                            afeb_templates_kit_single: false
                        }, function (response) {

                            if (typeof response.success !== 'undefined' &&
                                response.success == true) {

                                setTimeout(function () {
                                    afeb_templates_kit.progress('finish');
                                }, 1000);

                            } else {

                                if (typeof response.data !== 'undefined' &&
                                    typeof response.data.message !== 'undefined') {
                                    afeb_templates_kit.failed('content', response.data.message);
                                }

                            }

                        });
                    }

                });

            $(document).off('click', '.afeb-template-kit-section .afeb-preview')
                .on('click', '.afeb-template-kit-section .afeb-preview', function () {
                    let row = $(this).closest('.afeb-row');
                    let element_section = $(this).closest('.afeb-element-section');
                    let kit_id = element_section.data('kit-id');
                    let pages = element_section.data('pages');
                    let pages_title = element_section.data('pages-title');
                    let pages_meta = element_section.data('pages-meta');
                    let preview_image = element_section.data('image-url');
                    let preview_text = element_section.data('preview-text');
                    let preview_url = element_section.data('preview-url');
                    let back_text = element_section.data('back-text');
                    let template_pages = '';

                    pages = pages !== '' ? pages.split(',') : pages;
                    pages.forEach(function (page, i) {
                        let margin = i > 2 ? 'afeb-mr-top-20' : '';
                        let end_preview_url = '';

                        if (page != 'home') {
                            if (typeof pages_meta[page] === 'undefined') {
                                end_preview_url = preview_url + '/' + page + '-' + kit_id;
                            } else {
                                end_preview_url = preview_url + '/' + pages_meta[page];
                            }
                        } else {
                            end_preview_url = preview_url;
                        }

                        template_pages += '<div class="afeb-element-search-section afeb-col-lg-4 ' + margin + '">' +
                            '<div class="afeb-box-section afeb-element-section afeb-template-kit-section">' +
                            '<div class="afeb-box-header">' +
                            '<span class="afeb-loader"><i class="fa fa-spin fa-spinner"></i></span>' +
                            '<img src="' +
                            preview_image + page + '.webp' +
                            '">' +
                            '</div>' +
                            '<div class="afeb-box-body">' +
                            '<h3 class="afeb-element-search-text">' +
                            pages_title[page] +
                            '</h3>' +
                            '<div class="afeb-template-kit-btns">' +
                            '<a class="afeb-template-kit-preview-btn" href="' +
                            end_preview_url +
                            '" target="_blank">' +
                            preview_text +
                            '<span class="fa fa-eye"></span>' +
                            '</a>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                    });

                    let back = '<div class="afeb-template-preview-kit-back-btn afeb-col-lg-12"><button class="afeb-box-btn afeb-primary-btn" type="button">' + back_text + '</button></div>';

                    if (template_pages !== '') {
                        $('.afeb-elements-heading').fadeOut(80);
                        row.fadeOut(80).next().html(template_pages + back).css('display', 'flex');
                    }
                });

            $(document).off('click', '.afeb-template-preview-kit-back-btn')
                .on('click', '.afeb-template-preview-kit-back-btn', function () {
                    $('.afeb-elements-heading').fadeIn(100);
                    $('.afeb-template-preview-kit-section').html('').hide();
                    $('.afeb-template-kit-section-row').css('display', 'flex');
                });
        },
        init: function () {
            afeb_templates_kit.events();
        },
        import_templates_kit: function (kit_id) {
            let plugins = $('.afeb-template-kit-section-' + kit_id).data('plugins');

            afeb_templates_kit.progress('plugins');
            afeb_templates_kit.ajax_request({
                action: 'afeb_activate_required_plugins',
                plugins: plugins,
            }, function (response) {

                if (typeof response.success !== 'undefined' &&
                    response.success == true) {

                    afeb_templates_kit.progress('settings');
                    afeb_templates_kit.ajax_request({
                        action: 'afeb_import_settings',
                        afeb_templates_kit: kit_id,
                    }, function (response) {

                        if (typeof response.success !== 'undefined' &&
                            response.success == true) {

                            afeb_templates_kit.progress('content');
                            afeb_templates_kit.ajax_request({
                                action: 'afeb_import_templates_kit',
                                afeb_templates_kit: kit_id,
                                afeb_templates_kit_single: false
                            }, function (response) {

                                if (typeof response.success !== 'undefined' &&
                                    response.success == true) {

                                    setTimeout(function () {
                                        afeb_templates_kit.progress('finish');
                                    }, 1000);

                                } else {

                                    if (typeof response.data !== 'undefined' &&
                                        typeof response.data.message !== 'undefined') {
                                        afeb_templates_kit.failed('content', response.data.message);
                                    }

                                }

                            });

                        } else {

                            if (typeof response.data !== 'undefined' &&
                                typeof response.data.message !== 'undefined') {
                                afeb_templates_kit.failed('settings', response.data.message);
                            }

                        }

                    });

                } else {

                    if (typeof response.data !== 'undefined' &&
                        typeof response.data.message !== 'undefined') {
                        afeb_templates_kit.failed('plugins', response.data.message);
                    }

                }

            });
        },
        ajax_request: function (data, success_callback, error_callback) {
            $.ajax({
                type: 'POST',
                async: true,
                timeout: 1 * 60 * 60 * 1000, // 1 hours
                url: AFEB.ajax_url,
                data: Object.assign(data, { nonce: AFEB.nonce, }),
                success: function (response) {
                    success_callback(response);
                },
                error: function (response) {
                    error_callback(response);
                }
            });
        },
        progress: function (step) {
            let dialog = $('#afeb-template-import-dialog');
            let ui_dialog = $('div[aria-describedby="afeb-template-import-dialog"]');
            let please_wait = dialog.find('.afeb-template-import-please-wait');
            let heading = dialog.find('.afeb-template-import-heading');
            let description = dialog.find('.afeb-template-import-description');
            let tip = dialog.find('.afeb-template-import-tip');
            let import_status = dialog.find('.afeb-template-import-status');
            let status = import_status.data('status');

            $('#afeb-template-import-dialog').parent().find('.ui-dialog-titlebar-close').hide();

            please_wait.text(please_wait.data('message'));
            heading.text(heading.data('message'));
            description.text(description.data('message'));
            tip.text(tip.data('message'));

            if (step == 'plugins') {
                $('.afeb-template-import-progress').animate({ 'width': '24%' }, 500);
                $('.afeb-template-import-progress-markers-plugins').removeClass('afeb-refresh').find('>div>span').attr('class', 'fa fa-hourglass-start fa-spin');
                import_status.text(status.plugins + ' ...');

                dialog.find('.afeb-template-import-confirmation,.afeb-template-import-successful').fadeOut(100);
                setTimeout(function () {
                    let ui_dialog_top = ui_dialog.css('top');
                    ui_dialog.css({ top: (parseInt(ui_dialog_top) - 60 + 'px') });

                    dialog.find('.afeb-template-import').attr('style', 'display:block !important');
                }, 105);

            } else if (step == 'settings') {
                $('.afeb-template-import-progress-markers-plugins>div>span').attr('class', 'fa fa-check')
                $('.afeb-template-import-progress').animate({ 'width': '49%' }, 500);
                setTimeout(function () {
                    $('.afeb-template-import-progress-markers-settings').removeClass('afeb-refresh').find('>div>span').attr('class', 'fa fa-hourglass-start fa-spin');
                    import_status.text(status.settings + ' ...');
                }, 800);
            } else if (step == 'content') {
                $('.afeb-template-import-progress-markers-settings>div>span').attr('class', 'fa fa-check')
                $('.afeb-template-import-progress').animate({ 'width': '74%' }, 500);
                setTimeout(function () {
                    $('.afeb-template-import-progress-markers-content').removeClass('afeb-refresh').find('>div>span').attr('class', 'fa fa-hourglass-start fa-spin');
                    import_status.text(status.content + ' ...');
                }, 800);
            } else if (step == 'finish') {
                $('.afeb-template-import-progress').animate({ 'width': '100%' }, 500);
                $('.afeb-template-import-progress-markers-content>div>span').attr('class', 'fa fa-check');
                setTimeout(function () {
                    dialog.find('.afeb-template-import-confirmation,.afeb-template-import').hide();
                    let import_successful = dialog.find('.afeb-template-import-successful');
                    let import_btn = dialog.find('.afeb-template-kit-import-btn');
                    let ui_dialog_top = ui_dialog.css('top');

                    ui_dialog.css({ top: (parseInt(ui_dialog_top) + 40 + 'px') });
                    import_successful.find('.afeb-template-import-successful-get-help').show();
                    import_btn.text(import_btn.data('text'));
                    import_btn.find('>span').attr('class', 'fa fa-eye');
                    $('#afeb-template-import-dialog').parent().find('.ui-dialog-titlebar-close').show();
                    dialog.find('.afeb-template-import-successful').attr('style', 'display:block !important');
                    $('.afeb-template-import-progress').css({ 'width': '24%' });
                }, 1500);
            }
        },
        failed: function (step, message) {
            let dialog = $('#afeb-template-import-dialog');
            let please_wait = dialog.find('.afeb-template-import-please-wait');
            let heading = dialog.find('.afeb-template-import-heading');
            let description = dialog.find('.afeb-template-import-description');
            let tip = dialog.find('.afeb-template-import-tip');
            let marker_content = dialog.find('.afeb-template-import-progress-markers-' + step);
            let import_status = dialog.find('.afeb-template-import-status');

            please_wait.text('');
            heading.text(heading.data('error-message'));
            description.text(description.data('error-message'));
            tip.text(message);
            import_status.text('');

            $('#afeb-template-import-dialog').parent().find('.ui-dialog-titlebar-close').show();

            marker_content.addClass('afeb-refresh').find('>div>span').attr('class', 'fa fa-retweet');
        }
    }

    afeb_templates_kit.init();
})