jQuery(document).ready(function ($) {
    $(document).off('keyup', '.afeb-elements-search-input')
        .on('keyup', '.afeb-elements-search-input', function () {
            let filter = $(this).val();

            $('.afeb-element-search-section').each(function () {
                if ($(this).find('.afeb-element-search-text').text().trim().search(new RegExp(filter, 'i')) < 0) {
                    $(this).fadeOut(150);
                } else {
                    $(this).fadeIn(150);
                }
            });
        });

    $(document).off('click', '.afeb-elements-activate-all-btn')
        .on('click', '.afeb-elements-activate-all-btn', function () {
            $('.afeb-element-status').removeClass('afeb-element-status-deactive');
            $('.afeb-element-checkbox-box').removeClass('afeb-deactive-checkbox');
            $('.afeb-element-section .afeb-checkbox').prop('checked', true);
        });

    $(document).off('click', '.afeb-elements-deactivate-all-btn')
        .on('click', '.afeb-elements-deactivate-all-btn', function () {
            $('.afeb-element-status').addClass('afeb-element-status-deactive');
            $('.afeb-element-checkbox-box').addClass('afeb-deactive-checkbox');
            $('.afeb-element-section .afeb-checkbox').prop('checked', false);
        });

    $(document).off('change', '.afeb-element-checkbox-box .afeb-checkbox')
        .on('change', '.afeb-element-checkbox-box .afeb-checkbox', function () {
            if ($(this).is(':checked')) {
                $(this).parent().removeClass('afeb-deactive-checkbox');
                $(this).parent().parent().parent().find('.afeb-element-status').removeClass('afeb-element-status-deactive');
            } else {
                $(this).parent().addClass('afeb-deactive-checkbox');
                $(this).parent().parent().parent().find('.afeb-element-status').addClass('afeb-element-status-deactive');
            }
        });

    // ==== Builder ==== \\
    $(".page-title-action[href$='post_type=afeb-builder']").removeAttr('href').attr({ id: 'afeb-create-new-template' });
    $(document).off('click', '#afeb-create-new-template')
        .on('click', '#afeb-create-new-template', function (e) {
            e.preventDefault();
            let body = $('.afeb-body');
            if (!body.hasClass('ui-dialog-show'))
                $('.afeb-body').addClass('ui-dialog-show');

            $('input[name="afeb_new_template_name"]').val('');
            $('.afeb-new-template-msg').remove();
            $('#afeb-new-template-dialog').dialog('open');

        });

    $(document).off('click', '#afeb-new-template-dialog>button')
        .on('click', '#afeb-new-template-dialog>button', function (e) {
            e.preventDefault();

            let $this = $(this);
            let loading = $this.find('>span');

            if (loading.css('display') != 'none') return;

            let type = $('select[name="afeb_new_template_type"]').val();
            let name_input = $('input[name="afeb_new_template_name"]').val();

            $this.parent().find('.afeb-new-template-msg').remove();
            loading.css('display', 'inline-block');
            $.ajax({
                url: AFEB.ajax_url, type: 'POST',
                data: {
                    action: 'afeb_create_template', nonce: AFEB.nonce, data: { name: name_input, type: type }
                },
                success: function (res) {
                    loading.css('display', 'none');
                    if (res.success) {
                        $this.after('<h4 class="afeb-new-template-msg afeb-sucs">' + res.data.message + '</h4>');
                        setTimeout(function () {
                            window.location.href = res.data.redirect;
                        }, 100);
                    } else $this.after('<h4 class="afeb-new-template-msg afeb-err">' + res.data.message + '</h4>');
                },
                error: function () {
                    loading.css('display', 'none');
                    $this.after('<h4 class="afeb-new-template-msg afeb-err">' + $this.data('err-msg') + '</h4>');
                }
            });
        });

    $(document).off('keypress', '.afeb-new-template-name>input')
        .on('keypress', '.afeb-new-template-name>input', function (e) {
            if (e.originalEvent.keyCode == 13)
                $('#afeb-new-template-dialog>button').click();
        });

    $(document).off('input', '.afeb-new-template-name>input')
        .on('input', '.afeb-new-template-name>input', function (e) {
            if ($(this).val().trim() != '')
                $(this).parent().parent().find('.afeb-new-template-msg').remove();
        });

    // ==== Popup Builder ==== \\
    $(".page-title-action[href$='post_type=afeb-popup']").removeAttr('href').attr({ id: 'afeb-create-new-popup' });
    $(document).off('click', '#afeb-create-new-popup')
        .on('click', '#afeb-create-new-popup', function (e) {
            e.preventDefault();
            let body = $('.afeb-body');
            if (!body.hasClass('ui-dialog-show'))
                $('.afeb-body').addClass('ui-dialog-show');

            $('input[name="afeb_new_popup_name"]').val('');
            $('.afeb-new-popup-msg').remove();
            $('#afeb-new-popup-dialog').dialog('open');

        });

    $(document).off('click', '#afeb-new-popup-dialog>button')
        .on('click', '#afeb-new-popup-dialog>button', function (e) {
            e.preventDefault();

            let $this = $(this);
            let loading = $this.find('>span');

            if (loading.css('display') != 'none') return;

            let name_input = $('input[name="afeb_new_popup_name"]').val();

            $this.parent().find('.afeb-new-popup-msg').remove();
            loading.css('display', 'inline-block');
            $.ajax({
                url: AFEB.ajax_url, type: 'POST',
                data: {
                    action: 'afeb_create_popup', nonce: AFEB.nonce, data: { name: name_input }
                },
                success: function (res) {
                    loading.css('display', 'none');
                    if (res.success) {
                        $this.after('<h4 class="afeb-new-popup-msg afeb-sucs">' + res.data.message + '</h4>');
                        setTimeout(function () {
                            window.location.href = res.data.redirect;
                        }, 100);
                    } else $this.after('<h4 class="afeb-new-popup-msg afeb-err">' + res.data.message + '</h4>');
                },
                error: function () {
                    loading.css('display', 'none');
                    $this.after('<h4 class="afeb-new-popup-msg afeb-err">' + $this.data('err-msg') + '</h4>');
                },
            });
        });

    $(document).off('keypress', '.afeb-new-popup-name>input')
        .on('keypress', '.afeb-new-popup-name>input', function (e) {
            if (e.originalEvent.keyCode == 13)
                $('#afeb-new-popup-dialog>button').click();
        });

    $(document).off('input', '.afeb-new-popup-name>input')
        .on('input', '.afeb-new-popup-name>input', function (e) {
            if ($(this).val().trim() != '')
                $(this).parent().parent().find('.afeb-new-popup-msg').remove();
        });

    // ==== Edit Nav Menu ==== \\
    $('.afeb-edit-advanced-menu-field-icon').fontIconPicker({ emptyIconValue: '' });
    $('.afeb-colorpicker').wpColorPicker();
    $(document).off('click', '.afeb-edit-advanced-menu-activation>label')
        .on('click', '.afeb-edit-advanced-menu-activation>label', function () {
            let mgm_fields = $(this).parent().parent().find('.afeb-edit-advanced-menu-fields');
            let checkbox = $(this).parent().find('.afeb-edit-advanced-menu-activation-checkbox');
            let txt = $(this).text();
            let parent = $(this).closest('.menu.ui-sortable');
            let advanced_sub_menu_wrap = parent.find('.afeb-edit-advanced-sub-menu-wrap');

            $(this).text($(this).data('label'));
            $(this).data('label', txt);

            if (checkbox.is(":checked")) {
                checkbox.prop("checked", false);
                $(this).parent().addClass('afeb-edit-advanced-menu-deactive');
                mgm_fields.slideUp();
                advanced_sub_menu_wrap.hide();
            } else {
                mgm_fields.find('.afeb-edit-advanced-menu-field-icon').fontIconPicker({ emptyIconValue: '' });
                mgm_fields.find('.afeb-colorpicker').wpColorPicker();
                checkbox.prop("checked", true);
                $(this).parent().removeClass('afeb-edit-advanced-menu-deactive');
                mgm_fields.slideDown();
                advanced_sub_menu_wrap.show();
            }
        });

    $(document).off('change', '#afeb-edit-advanced-menu-field-type')
        .on('change', '#afeb-edit-advanced-menu-field-type', function () {
            let value = this.value;

            if (value == '') {
                $(this).parent().parent().parent().find('.afeb-edit-advanced-menu-field-width-wrap').hide();
            }
        });

    $(document).off('click', '.afeb-edit-advanced-sub-menu-activation>label')
        .on('click', '.afeb-edit-advanced-sub-menu-activation>label', function () {
            let fields = $(this).parent().parent().find('.afeb-edit-advanced-sub-menu-fields');
            let checkbox = $(this).parent().find('.afeb-edit-advanced-menu-activation-checkbox');

            if (checkbox.is(":checked")) {
                checkbox.prop("checked", false);
                $(this).parent().addClass('afeb-edit-advanced-sub-menu-deactive');
                fields.slideUp();
            } else {
                fields.find('.afeb-edit-advanced-menu-field-icon').fontIconPicker({ emptyIconValue: '' });
                checkbox.prop("checked", true);
                $(this).parent().removeClass('afeb-edit-advanced-sub-menu-deactive');
                fields.slideDown();
            }
        });

    setInterval(function () {
        $('.menu.ui-sortable').find('.menu-item').each(function () {
            let item = $(this);
            let depth_match = item.attr('class').match(/menu-item-depth-[0-9]{0,}/ig);
            let advanced_menu_wrap = item.find('.afeb-edit-advanced-menu-wrap');
            let items = advanced_menu_wrap.find('.afeb-edit-advanced-menu-field-type-wrap,' +
                '.afeb-edit-advanced-menu-field-template-id-wrap,' +
                '.afeb-edit-advanced-menu-field-shortcode-wrap,' +
                '.afeb-edit-advanced-menu-field-width-wrap');
            let heading = item.find('.afeb-edit-advanced-menu-field-heading-wrap');
            let depth = item.find('#afeb-edit-advanced-menu-field-depth');

            depth_match = typeof depth_match[0] != 'undefined' ? parseInt(depth_match[0].replace('menu-item-depth-', '')) : '';
            depth.val(depth_match);

            if (depth_match != 0) {
                if (depth_match == 1) heading.show();
                items.each(function () { $(this).hide(); })
            } else {
                heading.hide();
                $(items.get(0)).show();
                let mgm_type = $(items.get(0)).find('#afeb-edit-advanced-menu-field-type');
                let mgm_type_val = mgm_type.val();

                if (mgm_type_val != '') $(items.get(1)).show();
            }
        });
    }, 250);
});