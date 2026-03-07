(function ($, window, document) {
    'use strict';

    /**
     * Human-readable source for the sticky element helper bundled with the plugin.
     * The implementation mirrors assets/packages/sticky/sticky.min.js.
     */
    var Sticky = function (element, options) {
        var settings = $.extend(true, {
            to: 'top',
            offset: 0,
            effectsOffset: 0,
            parent: false,
            classes: {
                sticky: 'sticky',
                stickyActive: 'sticky-active',
                stickyEffects: 'sticky-effects',
                spacer: 'sticky-spacer'
            },
            isRTL: false,
            handleScrollbarWidth: false
        }, options);

        this.settings = settings;

        var $element = $(element).addClass(settings.classes.sticky);

        var state = {
            $window: $(window),
            $parent: null,
            $spacer: null
        };

        var layout = {
            width: null,
            leftOffset: null
        };

        var isSticky = false;
        var isFollowingParent = false;
        var hasEffectsClass = false;

        /**
         * Store the current CSS properties so they can be restored later.
         */
        var backupCss = function ($target, key, properties) {
            var original = {};
            var style = $target[0].style;

            properties.forEach(function (property) {
                original[property] = typeof style[property] !== 'undefined' ? style[property] : '';
            });

            $target.data('css-backup-' + key, original);
        };

        var restoreCss = function ($target, key) {
            return $target.data('css-backup-' + key);
        };

        /**
         * Measure the current width and left offset of the element.
         */
        var updateLayout = function () {
            layout.width = measureBox($element, 'width');
            layout.leftOffset = $element.offset().left;

            if (settings.isRTL) {
                var viewportWidth = settings.handleScrollbarWidth ? window.innerWidth : document.body.offsetWidth;
                layout.leftOffset = Math.max(viewportWidth - layout.width - layout.leftOffset, 0);
            }
        };

        var createSpacer = function () {
            state.$spacer = $element
                .clone()
                .addClass(settings.classes.spacer)
                .css({
                    visibility: 'hidden',
                    transition: 'none',
                    animation: 'none'
                });

            $element.after(state.$spacer);
        };

        var removeSpacer = function () {
            state.$spacer.remove();
        };

        var applyStickyStyles = function () {
            backupCss($element, 'unsticky', [
                'position',
                'width',
                'margin-top',
                'margin-bottom',
                'top',
                'bottom',
                'inset-inline-start'
            ]);

            var css = {
                position: 'fixed',
                width: layout.width,
                marginTop: 0,
                marginBottom: 0
            };

            css[settings.to] = settings.offset;
            css[settings.to === 'top' ? 'bottom' : 'top'] = '';

            if (layout.leftOffset) {
                css['inset-inline-start'] = layout.leftOffset + 'px';
            }

            $element.css(css).addClass(settings.classes.stickyActive);
        };

        var removeStickyStyles = function () {
            $element
                .css(restoreCss($element, 'unsticky'))
                .removeClass(settings.classes.stickyActive);
        };

        /**
         * Calculate the total dimension (width or height) including optional margins.
         */
        var measureBox = function ($target, property, includeMargin) {
            var computed = getComputedStyle($target[0]);
            var size = parseFloat(computed[property]);
            var axes = property === 'height' ? ['top', 'bottom'] : ['left', 'right'];
            var extras = [];

            if (computed.boxSizing !== 'border-box') {
                extras.push('border', 'padding');
            }

            if (includeMargin) {
                extras.push('margin');
            }

            extras.forEach(function (type) {
                axes.forEach(function (direction) {
                    size += parseFloat(computed[type + '-' + direction]);
                });
            });

            return size;
        };

        var measurePositions = function ($target) {
            var scrollTop = state.$window.scrollTop();
            var height = measureBox($target, 'height');
            var viewportHeight = window.innerHeight;
            var offsetTop = $target.offset().top - scrollTop;
            var offsetBottom = offsetTop - viewportHeight;

            return {
                top: {
                    fromTop: offsetTop,
                    fromBottom: offsetBottom
                },
                bottom: {
                    fromTop: offsetTop + height,
                    fromBottom: offsetBottom + height
                }
            };
        };

        var unstick = function () {
            removeStickyStyles();
            removeSpacer();
            isSticky = false;
            $element.trigger('sticky:unstick');
        };

        var handleParentFollow = function () {
            var elementPositions = measurePositions($element);
            var stickingToTop = settings.to === 'top';

            if (isFollowingParent) {
                var resetNeeded = stickingToTop
                    ? elementPositions.top.fromTop > settings.offset
                    : elementPositions.bottom.fromBottom < -settings.offset;

                if (resetNeeded) {
                    state.$parent.css(restoreCss(state.$parent, 'childNotFollowing'));
                    $element.css(restoreCss($element, 'notFollowing'));
                    isFollowingParent = false;
                }

                return;
            }

            var parentPositions = measurePositions(state.$parent);
            var parentStyle = getComputedStyle(state.$parent[0]);
            var borderWidth = parseFloat(parentStyle[stickingToTop ? 'borderBottomWidth' : 'borderTopWidth']);
            var distance = stickingToTop
                ? parentPositions.bottom.fromTop - borderWidth
                : parentPositions.top.fromBottom + borderWidth;

            var shouldFollow = stickingToTop
                ? distance <= elementPositions.bottom.fromTop
                : distance >= elementPositions.top.fromBottom;

            if (!shouldFollow) {
                return;
            }

            backupCss(state.$parent, 'childNotFollowing', ['position']);
            state.$parent.css('position', 'relative');

            backupCss($element, 'notFollowing', ['position', 'inset-inline-start', 'top', 'bottom']);

            var css = { position: 'absolute' };
            layout.leftOffset = state.$spacer.position().left;
            if (settings.isRTL) {
                var parentWidth = $element.parent().outerWidth();
                var spacerLeft = state.$spacer.position().left;
                layout.width = state.$spacer.outerWidth();
                layout.leftOffset = Math.max(parentWidth - layout.width - spacerLeft, 0);
            }

            css['inset-inline-start'] = layout.leftOffset + 'px';
            css[settings.to] = '';
            css[settings.to === 'top' ? 'bottom' : 'top'] = 0;

            $element.css(css);
            isFollowingParent = true;
        };

        var evaluateSticky = function () {
            var offset = settings.offset;
            var stickingToTop = settings.to === 'top';
            var delta;

            if (isSticky) {
                var spacerPositions = measurePositions(state.$spacer);
                delta = stickingToTop
                    ? spacerPositions.top.fromTop - offset
                    : -spacerPositions.bottom.fromBottom - offset;

                if (settings.parent) {
                    handleParentFollow();
                }

                if (delta > 0) {
                    unstick();
                }
            } else {
                var elementPositions = measurePositions($element);
                delta = stickingToTop
                    ? elementPositions.top.fromTop - offset
                    : -elementPositions.bottom.fromBottom - offset;

                if (delta <= 0) {
                    updateLayout();
                    createSpacer();
                    applyStickyStyles();
                    isSticky = true;
                    $element.trigger('sticky:stick');

                    if (settings.parent) {
                        handleParentFollow();
                    }
                }
            }

            toggleEffects(delta);
        };

        var handleScroll = function () {
            evaluateSticky();
        };

        var handleResize = function () {
            if (!isSticky) {
                return;
            }

            removeStickyStyles();
            removeSpacer();
            updateLayout();
            createSpacer();
            applyStickyStyles();

            if (settings.parent) {
                isFollowingParent = false;
                handleParentFollow();
            }
        };

        var toggleEffects = function (delta) {
            if (hasEffectsClass && -delta < settings.effectsOffset) {
                $element.removeClass(settings.classes.stickyEffects);
                hasEffectsClass = false;
            } else if (!hasEffectsClass && -delta >= settings.effectsOffset) {
                $element.addClass(settings.classes.stickyEffects);
                hasEffectsClass = true;
            }
        };

        this.destroy = function () {
            if (isSticky) {
                unstick();
            }

            state.$window.off('scroll', handleScroll).off('resize', handleResize);
            $element.removeClass(settings.classes.sticky);
        };

        // Initialise
        if (settings.parent) {
            state.$parent = $element.parent();
            if (settings.parent !== 'parent') {
                state.$parent = state.$parent.closest(settings.parent);
            }
        }

        state.$window.on({
            scroll: handleScroll,
            resize: handleResize
        });

        evaluateSticky();
    };

    $.fn.sticky = function (options) {
        var isMethodCall = typeof options === 'string';

        return this.each(function () {
            var $target = $(this);

            if (isMethodCall) {
                var instance = $target.data('sticky');

                if (!instance) {
                    throw new Error('Trying to perform the `' + options + '` method prior to initialization');
                }

                if (!instance[options]) {
                    throw new ReferenceError('Method `' + options + '` not found in sticky instance');
                }

                instance[options].apply(instance, Array.prototype.slice.call(arguments, 1));

                if (options === 'destroy') {
                    $target.removeData('sticky');
                }

                return;
            }

            $target.data('sticky', new Sticky(this, options));
        });
    };

    window.Sticky = Sticky;
})(jQuery, window, document);
