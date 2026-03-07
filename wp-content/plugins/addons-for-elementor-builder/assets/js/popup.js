(function ($, elementor) {
    'use strict';
    let afeb_Popup = {
        init: function () {
            $(document).ready(function () {
                if (!$('.afeb-popup-output.afeb-template-popup').length)
                    return;

                afeb_Popup.open_init();
                afeb_Popup.close_init();
            });
        },
        open_init: function () {
            $('.afeb-popup-output.afeb-template-popup').each(function () {
                let popup = $(this);
                let settings = popup.data('settings');

                if (settings.ppup_opn_evnt == 'load') {
                    let load_delay = settings.ppup_opn_dly * 1000;

                    setTimeout(function () {
                        afeb_Popup.open(popup, settings);
                    }, load_delay);
                } else if (settings.ppup_opn_evnt == 'exit') {
                    $(document).on('mouseleave', function (e) {
                        if (!popup.hasClass('afeb-popup-open'))
                            afeb_Popup.open(popup, settings);
                    });
                }
            });
        },
        open: function (popup, settings) {
            let id = popup.attr('id').replaceAll('-', '_');

            if (settings.ppup_shonc) {
                let ppup_shonc = localStorage.getItem(id);

                if (ppup_shonc != null) return false;
                localStorage.setItem(id, 'show');
            } else {
                localStorage.removeItem(id);
            }

            if (settings.ppup_dsabl_pge_scrl == 'yes')
                $('body').css('overflow', 'hidden');

            popup.addClass('afeb-popup-open').css('display', 'flex');
            popup.find('.afeb-popup-container').addClass('animated ' + settings.ppup_anim);
            $(window).trigger('resize');
            $('.afeb-popup-overlay').hide().fadeIn();
        },
        close_init: function () {
            $('.afeb-popup-close-btn').on('click', function (e) {
                afeb_Popup.close($(this).closest('.afeb-popup-output.afeb-template-popup'));
            });
            $('.afeb-popup-overlay').on('click', function () {
                let popup = $(this).closest('.afeb-popup-output.afeb-template-popup');
                let settings = popup.data('settings');

                if (settings.ppup_ovrly_cls == 'yes')
                    afeb_Popup.close(popup);
            });
            $(document).on('keyup', function (e) {
                let popup = $('.afeb-popup-open');
                let settings = popup.data('settings');

                if (popup.length && e.keyCode == 27 && settings.ppup_esc_cls == 'yes')
                    afeb_Popup.close(popup);
            });
        },
        close: function (popup) {
            popup.fadeOut();
            $('body').css('overflow', 'visible');
            $(window).trigger('resize');
        }
    }

    afeb_Popup.init();

}(jQuery, window.elementorFrontend));