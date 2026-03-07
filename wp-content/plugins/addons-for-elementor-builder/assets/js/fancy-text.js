(function ($, elementor) {
    'use strict';
    let afeb_widgetFancyText = function ($scope, $) {

        let typing_character = function (parent_item, characters, cursor_pos, delay, call_back) {
            parent_item.html(function (_, html) {
                return html + characters[cursor_pos];
            });

            if (cursor_pos < characters.length - 1) {
                setTimeout(function () {
                    typing_character(parent_item, characters, cursor_pos + 1, delay, call_back);
                }, delay);
            } else {
                call_back();
            }
        }

        let clearing_character = function (parent_item, delay, call_back) {
            let length;
            parent_item.html(function (_, html) {
                length = html.length;
                return html.substr(0, length - 1);
            });
            if (length > 1) {
                setTimeout(function () {
                    clearing_character(parent_item, delay, call_back);
                }, delay);
            } else {
                call_back();
            }
        }

        let show_typing_character = function (opts) {
            let defaults = { parent_item: null, type_delay: 100, clear_delay: 20, pause: 1500, items: [] };
            let settings = $.extend(defaults, opts);

            let typing_loop = function (parent_item, i) {
                typing_character(parent_item, settings.items[i], 0, settings.type_delay, function () {
                    setTimeout(function () {
                        clearing_character(parent_item, settings.clear_delay, function () {
                            typing_loop(parent_item, (i + 1) % settings.items.length)
                        });
                    }, settings.pause);
                });
            };

            settings.parent_item.html('');
            typing_loop(settings.parent_item, 0);
        }

        let show_characters = function (character, current_item, options, bool) {
            character.addClass('afeb-anim-text-in').removeClass('afeb-anim-text-out');

            if (!character.is(':last-child')) {
                setTimeout(function () {
                    show_characters(character.next(), current_item, options, bool);
                }, options.duration);
            } else {
                if (!bool) {
                    setTimeout(function () {
                        hide_item(current_item);
                    }, options.delay)
                }
            }
        }

        let get_next_item = function (current_item) {
            return (!current_item.is(':last-child')) ?
                current_item.next() : current_item.parent().children().eq(0);
        }

        let switch_item = function (current_item, next_item) {
            current_item.removeClass('afeb-anim-text-visible').addClass('afeb-anim-text-hidden');
            next_item.removeClass('afeb-anim-text-hidden').addClass('afeb-anim-text-visible');
        }

        let hide_item = function (current_item, options) {
            let next_item = get_next_item(current_item);
            let anim_text = current_item.parents('.afeb-fancy-anim-text');
            let anim_text_inner = anim_text.find('.afeb-fancy-anim-text-inner');

            if (anim_text.hasClass('afeb-anim-text-type-clip')) {
                anim_text_inner.animate({
                    width: '2px'
                }, options.duration, function () {
                    switch_item(current_item, next_item);
                    show_next_clip_item(next_item, options);
                });
            }
        }
    }

    let init = function ($scope, $) {
        let fancytext = $scope.find('.afeb-fancy-text');
        if (!fancytext.length) return;

        let settings = fancytext.data('settings');
        let anim_text = fancytext.find('.afeb-fancy-anim-text');
        let anim_text_inner = anim_text.find('.afeb-fancy-anim-text-inner');
        let options = {
            duration: parseFloat(settings.dur, 10) * 1000,
            delay: parseFloat(settings.dly, 10) * 1000,
            animTextLength: anim_text_inner.find('b').length,
            showAnimTextCount: 0
        };

        anim_text_inner.find('b').first().addClass('afeb-anim-text-visible');

        setTimeout(function () {
            let current_item = anim_text.find('.afeb-anim-text-visible').eq(0);
            hide_item(current_item, options);
        }, options.delay);
    }

    init($scope, $);
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/afeb_fancy_text.default', afeb_widgetFancyText);
});

}(jQuery, window.elementorFrontend));