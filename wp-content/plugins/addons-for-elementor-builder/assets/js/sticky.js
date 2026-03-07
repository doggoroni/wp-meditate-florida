(function ($, elementor) {

    'use strict';

    $(window).on('elementor/frontend/init', function () {
        let module_handler = elementorModules.frontend.handlers.Base;
        let sticky = module_handler.extend({
            sticky_instance: null,
            current_config: {},
            debounced_reactivate: null,
            bindEvents() {
                elementorFrontend.addListenerOnce(this.getUniqueHandlerID() + 'sticky', 'resize', this.reactivateOnResize);
            },
            unbindEvents() {
                elementorFrontend.removeListeners(this.getUniqueHandlerID() + 'sticky', 'resize', this.reactivateOnResize);
            },
            isStickyInstanceActive() {
                return this.sticky_instance !== null;
            },
            getResponsiveSetting(setting) {
                let element_settings = this.getElementSettings();
                return elementorFrontend.getCurrentDeviceSetting(element_settings, setting);
            },
            getResponsiveSettingList(setting) {
                let breakpoints = Object.keys(elementorFrontend.config.responsive.activeBreakpoints);
                return ['', ...breakpoints].map(suffix => {
                    return suffix ? `${setting}_${suffix}` : setting;
                });
            },
            getConfig() {
                let element_settings = this.getElementSettings(),
                    sticky_options = {
                        to: 'top',
                        offset: this.getResponsiveSetting('afeb_sticky_top_spacing') || 0,
                        classes: {
                            sticky: 'afeb-sticky',
                            stickyActive: 'afeb-sticky-active',
                            stickyEffects: 'afeb-sticky-effects',
                            spacer: 'afeb-sticky-spacer',
                        },
                        isRTL: elementorFrontend.config.is_rtl,
                        handleScrollbarWidth: elementorFrontend.isEditMode()
                    },
                    wp_admin_bar = elementorFrontend.elements.$wpAdminBar,
                    is_parent_container = this.isContainerElement(this.$element[0]) && !this.isContainerElement(this.$element[0].parentElement);

                if (wp_admin_bar.length && wp_admin_bar.css('position') === 'fixed') {
                    sticky_options.offset += wp_admin_bar.height();
                }

                if (element_settings.afeb_sticky_stay_in_column && !is_parent_container) {
                    sticky_options.parent = '.e-con, .e-con-inner, .elementor-widget-wrap';
                }

                return sticky_options;
            },
            activate() {
                this.current_config = this.getConfig();
                this.sticky_instance = new window.Sticky(this.$element, this.current_config);
            },
            deactivate() {
                if (!this.isStickyInstanceActive()) {
                    return;
                }

                this.sticky_instance.destroy();
                this.sticky_instance = null;
            },
            run(refresh) {
                if (!this.getElementSettings('afeb_sticky_enable') ||
                    document.body.classList.contains('elementor-editor-active')) {
                    this.deactivate();
                    return;
                }

                if (refresh === true) {
                    this.reactivate();
                } else if (!this.isStickyInstanceActive()) {
                    this.activate();
                }
            },
            reactivateOnResize() {
                let $this = this;

                clearTimeout($this.debounced_reactivate);
                $this.debounced_reactivate = setTimeout(function () {
                    let config = $this.getConfig(),
                        is_different_config = JSON.stringify(config) !== JSON.stringify($this.current_config);
                    if (is_different_config) {
                        $this.run(true);
                    }
                }, 300);
            },
            reactivate() {
                if (!this.getElementSettings('afeb_sticky_enable') ||
                    document.body.classList.contains('elementor-editor-active')) {
                    return;
                }

                this.deactivate();
                this.activate();
            },
            onElementChange(setting_key) {
                if (['afeb_sticky_enable'].indexOf(setting_key) !== -1) {
                    this.run(true);
                }

                let settings = [...this.getResponsiveSettingList('afeb_sticky_top_spacing'), 'sticky_parent'];

                if (settings.indexOf(setting_key) !== -1) {
                    this.reactivate();
                }
            },
            onDeviceModeChange() {
                setTimeout(() => this.run(true));
            },
            onInit() {
                elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);

                if (elementorFrontend.isEditMode()) {
                    window.elementor.listenTo(window.elementor.channels.deviceMode, 'change', () => this.onDeviceModeChange());
                }
                this.run();
            },
            onDestroy() {
                elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(this, arguments);
                this.deactivate();
            },
            isContainerElement(element) {
                let container_classes = ['e-con', 'e-con-inner'];

                return container_classes.some(container_class => {
                    return element?.classList.contains(container_class);
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(sticky, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));