(function ($, elementor) {
    'use strict';
    let afeb_widgetOffcanvas = function ($scope, $) {

        let init = function ($scope, $) {
            let off_canvas_trigger = $scope.find('.afeb-offcanvas-trigger');
            if (!off_canvas_trigger.length) return;

            let nav_toggle;
            let overlay = $('<div class="afeb-offcanvas-overlay"></div>');
            let esc_cls = 0;
            let any_wr_cls = 0;
            $('body').find('.afeb-offcanvas-overlay').remove();
            off_canvas_trigger.off('click', '.afeb-offcanvas-trigger-btn')
                .on('click', '.afeb-offcanvas-trigger-btn', function (e) {
                    let body = $('body');
                    let settings = $(this).parent().data('settings');
                    let id = settings.id !== undefined ? settings.id : '';
                    nav_toggle = $('#afeb-offcanvas-nav-toggle-' + id);
                    let anim = settings.anim !== undefined ? settings.anim : '';
                    let anim_spd = settings.anim_spd !== undefined ? settings.anim_spd : 500;
                    let ovrly = settings.ovrly !== undefined ? settings.ovrly : 1;
                    esc_cls = settings.esc_cls !== undefined ? settings.esc_cls : 1;
                    any_wr_cls = settings.any_wr_cls !== undefined ? settings.any_wr_cls : 1;

                    body.find('.afeb-offcanvas-overlay').remove();
                    if (ovrly == 1) {
                        body.append(overlay);
                        overlay.css({
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });
                    }

                    if (anim == 'none') {
                        if (!nav_toggle.hasClass('afeb-offcanvas-open'))
                            nav_toggle.addClass('afeb-offcanvas-open');
                    } else if (anim == 'slide') {
                        if (!nav_toggle.hasClass('afeb-offcanvas-open'))
                            nav_toggle.css({
                                '-webkit-transition':
                                    'transform ' + anim_spd + 'ms ease-in-out',
                                'transition':
                                    'transform ' + anim_spd + 'ms ease-in-out'
                            }).addClass('afeb-offcanvas-open');
                    } else if (anim == 'push') {

                    }
                });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('.afeb-offcanvas-nav').length &&
                    !$(e.target).closest('.afeb-offcanvas-trigger-btn').length &&
                    any_wr_cls == 1) {
                    nav_toggle.removeClass('afeb-offcanvas-open');
                    overlay.removeAttr('style');
                }
            });

            $(document).on('keydown', function (e) {
                if (e.which == 27 && esc_cls == 1) {
                    nav_toggle.removeClass('afeb-offcanvas-open');
                    overlay.removeAttr('style');
                }
            });
        }

        init($scope, $);
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_offcanvas.default', afeb_widgetOffcanvas);
    });

}(jQuery, window.elementorFrontend));