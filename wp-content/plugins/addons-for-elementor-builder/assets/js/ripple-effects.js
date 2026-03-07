(function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        let module_handler = elementorModules.frontend.handlers.Base;

        let debounce = function (func, wait, immediate) {
            let timeout;
            return function () {
                let context = this, args = arguments;
                let later = function () {
                    timeout = null;

                    if (!immediate) {
                        func.apply(context, args);
                    }
                };
                let call_now = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);

                if (call_now) {
                    func.apply(context, args);
                }
            };
        };

        let ripple_effects = module_handler.extend({
            bindEvents: function () {
                this.run();
            },
            getDefaultSettings: function () {
                return {
                    multi: true,
                };
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('afeb_ripple_') !== -1) {
                    this.run();
                }
            }, 400),
            settings: function (key) {
                return this.getElementSettings('afeb_ripple_' + key);
            },
            run: function () {
                let options = this.getDefaultSettings(),
                    $widget_id = 'afeb-' + this.getID(),
                    $widget_class_select = '.elementor-element-' + this.getID(),
                    $selector = '';

                $(document).on('click', '[href="#"]', function (e) { e.preventDefault(); });
                if (this.settings('on')) {
                    options.on = this.settings('on');
                }
                if (this.settings('easing')) {
                    options.easing = this.settings('easing');
                }
                if (this.settings('duration.size')) {
                    options.duration = this.settings('duration.size');
                }
                if (this.settings('opacity.size')) {
                    options.opacity = this.settings('opacity.size');
                }
                if (this.settings('color')) {
                    options.color = this.settings('color');
                }

                if (this.settings('selector') === 'container') {
                    $($widget_class_select).removeClass('afeb-ripple-effect-container');
                    $($widget_class_select).removeData('afeb-ripple-options');
                    $($widget_class_select).removeData('afeb-ripple-type');
                    $($widget_class_select).removeClass('disable-ripple');

                    $selector = $widget_class_select;
                } else {
                    $($widget_class_select).addClass('disable-ripple');
                }
                if (this.settings('selector') === 'widgets') {
                    $($widget_class_select).addClass('afeb-ripple-effect-container');
                    $($widget_class_select).data('afeb-ripple-options', options);
                    $($widget_class_select).data('afeb-ripple-type', 'widgets');

                    $selector = $widget_class_select + ' .elementor-widget-container';
                }
                if (this.settings('selector') === 'images') {
                    $($widget_class_select).addClass('afeb-ripple-effect-container');
                    $($widget_class_select).data('afeb-ripple-options', options);
                    $($widget_class_select).data('afeb-ripple-type', 'images');
                    $selector = $widget_class_select + ' img';
                }
                if (this.settings('selector') === 'buttons') {
                    $($widget_class_select).addClass('afeb-ripple-effect-container');
                    $($widget_class_select).data('afeb-ripple-options', options);
                    $($widget_class_select).data('afeb-ripple-type', 'buttons');
                    $selector = $widget_class_select + ' a';
                }
                if (this.settings('selector') === 'both') {
                    $($widget_class_select).addClass('afeb-ripple-effect-container');
                    $($widget_class_select).data('afeb-ripple-options', options);
                    $($widget_class_select).data('afeb-ripple-type', 'both');
                    $selector = $widget_class_select + ' a,' + $widget_class_select + ' img';
                }
                if (this.settings('selector') === 'custom' && this.settings('custom_selector')) {
                    $selector = $widget_class_select + ' ' + this.settings('custom_selector');
                }

                let container = $($widget_class_select).closest('.afeb-ripple-effect-container');
                let $return = this.settings('enable') !== 'yes' ? true : false;

                if (container.length > 0) {
                    $return = false;

                    options = container.data('afeb-ripple-options');
                    let type = container.data('afeb-ripple-type');

                    if (type == 'widgets') {
                        $selector = $widget_class_select + ' .elementor-widget-container';
                    }
                    if (type == 'images') {
                        $selector = $widget_class_select + ' img';
                    }
                    if (type == 'buttons') {
                        $selector = $widget_class_select + ' a';
                    }
                    if (type == 'both') {
                        $selector = $widget_class_select + ' a,' + $widget_class_select + ' img';
                    }
                }

                if ($return == true) {
                    return;
                }

                document.querySelectorAll($selector).forEach(function (el) {
                    if (el.tagName == 'IMG') {
                        let $image = $(el);
                        $image.wrap('<div id="afeb-ripple-effect-img-wrapper-' + $widget_id + '"></div>');
                        window.rippler = $.ripple('#afeb-ripple-effect-img-wrapper-' + $widget_id, options);
                    } else {
                        window.rippler = $.ripple($selector, options);
                    }
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ripple_effects, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));