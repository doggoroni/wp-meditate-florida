
if (afeb === undefined) var afeb = {};

jQuery(document).ready(function ($) {
    afeb.Component = {
        init: function () {
            let self = afeb.Component;

            self.accordion();
            self.dialog();
        },
        accordion: function () {
            afeb.Component.accordionInit = function (object) {
                let self = $(object);

                $(self).accordion({
                    active: self.data('active') || 0,
                    animate: self.data('animate') || {},
                    classes: self.data('classes') || {},
                    collapsible: self.data('collapsible') || true,
                    disabled: self.data('disabled') || false,
                    event: self.data('event') || 'click',
                    header: self.data('header') || '> li > :first-child,> :not(li):even',
                    heightStyle: self.data('height-style') || 'auto',
                    icons: '',
                });
            }

            let accordion = $('.afeb-component-accordion');
            accordion.each(function () {
                afeb.Component.accordionInit($(this));
            });
        },
        dialog: function () {
            let dialog = $('.afeb-component-dialog');
            dialog.each(function () {
                $(this).dialog({
                    autoOpen: $(this).data('auto-open') || false,
                    buttons: eval($(this).data('buttons')) || null,
                    closeOnEscape: $(this).data('close-on-escape') || true,
                    closeText: $(this).data('close-text') || '',
                    draggable: $(this).data('draggable') || false,
                    height: $(this).data('height') || 400,
                    hide: $(this).data('hide') || {
                        effect: 'scale',
                        duration: 200
                    },
                    maxHeight: $(this).data('max-height') || false,
                    maxWidth: $(this).data('max-width') || false,
                    minHeight: $(this).data('min-height') || 150,
                    minWidth: $(this).data('min-width') || 150,
                    modal: $(this).data('modal') || true,
                    // position: $(this).data('position') || { my: 'bottom', at: 'bottom', of: 'window' },
                    resizable: $(this).data('resizable') || false,
                    show: $(this).data('show') || {
                        effect: 'scale',
                        duration: 200
                    },
                    width: $(this).data('width') || 400,
                    open: function (event, ui) {
                        ($(this).data('scrollable') == false) ? $(this).css('overflow', 'hidden') : null;
                    },
                    close: function (event, ui) {
                        $('.afeb-body').removeClass('ui-dialog-show');
                    }
                });
            });
        }
    }

    afeb.Component.init();
});