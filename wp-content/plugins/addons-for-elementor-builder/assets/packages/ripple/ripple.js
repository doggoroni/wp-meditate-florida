(function ($, document, Math) {
    'use strict';

    /**
     * Lightweight ripple effect implementation used by the Ripple Effects extension.
     * The logic mirrors the shipped ripple.min.js file but is kept human-readable
     * so the source can be reviewed and extended easily.
     *
     * @param {string} selector CSS selector that identifies the clickable elements.
     * @param {Object} options  Optional behaviour overrides.
     */
    $.ripple = function (selector, options) {
        var instance = this;

        var log = instance.log = function () {
            if (!instance.defaults.debug) {
                return;
            }

            if (typeof console !== 'undefined' && console.log) {
                console.log.apply(console, arguments);
            }
        };

        instance.selector = selector;
        instance.defaults = {
            debug: false,
            on: 'mousedown',
            opacity: 0.4,
            color: 'auto',
            multi: false,
            duration: 0.7,
            rate: function (pixels) {
                return pixels;
            },
            easing: 'linear'
        };

        instance.defaults = $.extend({}, instance.defaults, options);

        /**
         * Handles the ripple animation for the active element.
         *
         * @param {Event} event Triggering DOM event.
         */
        var triggerRipple = function (event) {
            if ($(this).hasClass('disable-ripple')) {
                return;
            }

            var $element = $(this);
            var opts = $.extend({}, instance.defaults, options);
            var $ripple;

            $element.addClass('has-ripple');

            if (opts.multi || (!opts.multi && $element.find('.ripple').length === 0)) {
                $ripple = $('<span></span>').addClass('ripple').appendTo($element);
                log('Create: Ripple');

                if (!$ripple.height() && !$ripple.width()) {
                    var dimension = Math.max($element.outerWidth(), $element.outerHeight());
                    $ripple.css({
                        height: dimension,
                        width: dimension
                    });
                    log('Set: Ripple size');
                }

                if (opts.rate && typeof opts.rate === 'function') {
                    var pixelsPerSecond = Math.round($ripple.width() / opts.duration);
                    var adjustedRate = opts.rate(pixelsPerSecond);
                    var newDuration = $ripple.width() / adjustedRate;

                    if (opts.duration.toFixed(2) !== newDuration.toFixed(2)) {
                        log('Update: Ripple Duration', { from: opts.duration, to: newDuration });
                        opts.duration = newDuration;
                    }
                }

                var backgroundColor = opts.color === 'auto' ? $element.css('color') : opts.color;
                var rippleCss = {
                    animationDuration: opts.duration.toString() + 's',
                    animationTimingFunction: opts.easing,
                    background: backgroundColor,
                    opacity: opts.opacity
                };

                log('Set: Ripple CSS', rippleCss);
                $ripple.css(rippleCss);
            }

            if (!opts.multi) {
                log('Set: Ripple Element');
                $ripple = $element.find('.ripple');
            }

            log('Destroy: Ripple Animation');
            $ripple.removeClass('ripple-animate');

            var offsetLeft = event.pageX - $element.offset().left - $ripple.width() / 2;
            var offsetTop = event.pageY - $element.offset().top - $ripple.height() / 2;

            if (opts.multi) {
                log('Set: Ripple animationend event');
                $ripple.one('animationend webkitAnimationEnd oanimationend MSAnimationEnd', function () {
                    log('Note: Ripple animation ended');
                    log('Destroy: Ripple');
                    $(this).remove();
                });
            }

            log('Set: Ripple location');
            log('Set: Ripple animation');
            $ripple
                .css({
                    top: offsetTop + 'px',
                    left: offsetLeft + 'px'
                })
                .addClass('ripple-animate');
        };

        document.querySelectorAll(instance.selector).forEach(function (element) {
            element.addEventListener(instance.defaults.on, triggerRipple);
        });
    };
})(jQuery, document, Math);
