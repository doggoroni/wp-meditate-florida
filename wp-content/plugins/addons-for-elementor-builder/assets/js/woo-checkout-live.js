(function($){
    // Global flag to track initialization
    let checkoutInitialized = false;

    function initializeWooCommerceCheckout() {
        if (checkoutInitialized) return;

        // Ensure WooCommerce checkout is properly initialized
        if (typeof wc_checkout_params !== 'undefined') {
            // Trigger WooCommerce initialization
            $(document.body).trigger('init_checkout');

            // Initialize select2 if available
            if ($.fn.select2 && $('select.country_select, select.state_select').length) {
                $('select.country_select, select.state_select').select2();
            }

            checkoutInitialized = true;
            console.log('WooCommerce checkout initialized');
        }
    }

    function applyVar($scope, el, cssVar, cssProp) {
        if (!el) return;
        const val = getComputedStyle($scope[0]).getPropertyValue(cssVar).trim();
        if (val) el.style[cssProp] = val;
    }

    function updateLayout($scope) {
        // Apply layout-specific styles
        const layout = $scope.hasClass('elementor-wc-checkout-layout-two-column') ? 'two-column' : 'one-column';

        if (layout === 'two-column') {
            // Apply two-column specific styles
            $scope.find('.elementor-wc-checkout-left').each(function(){
                applyVar($scope, this, '--two-column-left-width', 'width');
            });

            $scope.find('.elementor-wc-checkout').each(function(){
                applyVar($scope, this, '--two-column-gap', 'gap');
            });

            // Handle sticky right column
            if ($scope.find('.elementor-wc-checkout-right').hasClass('sticky')) {
                $scope.find('.elementor-wc-checkout-right').each(function(){
                    applyVar($scope, this, '--sticky-top-offset', 'top');
                    applyVar($scope, this, '--sticky-zindex', 'zIndex');
                });
            }
        } else {
            // Apply one-column specific styles
            $scope.find('.elementor-wc-checkout').each(function(){
                applyVar($scope, this, '--one-column-max-width', 'maxWidth');
                applyVar($scope, this, '--one-column-padding', 'padding');
            });
        }
    }

    function updateCheckout($scope){

        function updateNormal() {
            // Buttons
            const buttons = [
                $scope.find('#place_order').get(0),
                $scope.find('.checkout_coupon button').get(0)
            ];
            buttons.forEach(function(btn){
                if (!btn) return;
                applyVar($scope, btn, '--button-bg-color', 'backgroundColor');
                applyVar($scope, btn, '--button-text-color', 'color');
                applyVar($scope, btn, '--button-border-radius', 'borderRadius');
                applyVar($scope, btn, '--button-padding', 'padding');
                applyVar($scope, btn, '--button-font-family', 'fontFamily');
                applyVar($scope, btn, '--button-font-size', 'fontSize');
                applyVar($scope, btn, '--button-font-weight', 'fontWeight');
            });

            // Fields
            $scope.find(
                '.woocommerce-checkout .form-row .input-text,' +
                '.woocommerce-checkout .form-row textarea,' +
                '.woocommerce-checkout .form-row select,' +
                '#coupon_code,' +
                '.select2-container--default .select2-selection--single'
            ).each(function(){
                applyVar($scope, this, '--forms-fields-normal-background', 'backgroundColor');
                applyVar($scope, this, '--forms-fields-normal-color', 'color');
                applyVar($scope, this, '--forms-fields-border-color', 'borderColor');
                applyVar($scope, this, '--forms-fields-border-radius', 'borderRadius');
                applyVar($scope, this, '--forms-fields-normal-box-shadow', 'boxShadow');
                applyVar($scope, this, '--forms-fields-padding', 'padding');
            });

            // Sections
            $scope.find('.e-checkout__order_review, .woocommerce-billing-fields, .woocommerce-additional-fields, .woocommerce-shipping-fields, .checkout_coupon').each(function(){
                applyVar($scope, this, '--sections-background-color', 'backgroundColor');
                applyVar($scope, this, '--sections-border-color', 'borderColor');
                applyVar($scope, this, '--sections-border-radius', 'borderRadius');
                applyVar($scope, this, '--sections-padding', 'padding');
                applyVar($scope, this, '--sections-margin', 'margin');
                applyVar($scope, this, '--sections-box-shadow', 'boxShadow');
            });

            // Payment section
            $scope.find('#payment').each(function(){
                applyVar($scope, this, '--payment-background-color', 'backgroundColor');
                applyVar($scope, this, '--payment-border-color', 'borderColor');
                applyVar($scope, this, '--payment-border-radius', 'borderRadius');
                applyVar($scope, this, '--payment-padding', 'padding');
            });

            // Order table
            $scope.find('.woocommerce table.shop_table thead th').each(function(){
                applyVar($scope, this, '--order-table-header-bg', 'backgroundColor');
                applyVar($scope, this, '--order-table-header-color', 'color');
            });

            $scope.find('.woocommerce table.shop_table tfoot th, .woocommerce table.shop_table tfoot td').each(function(){
                applyVar($scope, this, '--order-table-footer-bg', 'backgroundColor');
                applyVar($scope, this, '--order-table-footer-color', 'color');
            });

            $scope.find('.woocommerce table.shop_table th, .woocommerce table.shop_table td').each(function(){
                applyVar($scope, this, '--order-table-cell-padding', 'padding');
            });

            // Section titles
            $scope.find('.woocommerce-billing-fields h3, .woocommerce-shipping-fields h3, .woocommerce-additional-fields h3, .e-checkout__order_review h3').each(function(){
                applyVar($scope, this, '--sections-title-color', 'color');
            });

            // Form labels
            $scope.find('.woocommerce form .form-row label').each(function(){
                applyVar($scope, this, '--forms-labels-color', 'color');
            });
        }

        function updateFocusStates() {
            // Focus states for fields
            $scope.find(
                '.woocommerce-checkout .form-row .input-text,' +
                '.woocommerce-checkout .form-row textarea,' +
                '.woocommerce-checkout .form-row select,' +
                '#coupon_code,' +
                '.select2-container--default .select2-selection--single'
            ).each(function(){
                const field = this;

                // Focus event
                $(field).on('focus.afeb', function(){
                    applyVar($scope, this, '--forms-fields-focus-background', 'backgroundColor');
                    applyVar($scope, this, '--forms-fields-focus-color', 'color');
                    applyVar($scope, this, '--forms-fields-focus-border-color', 'borderColor');
                });

                // Blur event
                $(field).on('blur.afeb', function(){
                    applyVar($scope, this, '--forms-fields-normal-background', 'backgroundColor');
                    applyVar($scope, this, '--forms-fields-normal-color', 'color');
                    applyVar($scope, this, '--forms-fields-border-color', 'borderColor');
                });
            });
        }

        // Initial updates
        updateNormal();
        updateFocusStates();
        updateLayout($scope);

        // Button hover states
        $scope.off('mouseenter.afeb mouseleave.afeb', '#place_order, .checkout_coupon button')
            .on('mouseenter.afeb', '#place_order, .checkout_coupon button', function(){
                applyVar($scope, this, '--button-bg-hover-color', 'backgroundColor');
                applyVar($scope, this, '--button-text-hover-color', 'color');
                this.style.transform = 'translateY(-1px)';
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            })
            .on('mouseleave.afeb', '#place_order, .checkout_coupon button', function(){
                applyVar($scope, this, '--button-bg-color', 'backgroundColor');
                applyVar($scope, this, '--button-text-color', 'color');
                this.style.transform = '';
                this.style.boxShadow = '';
            });
    }

    function handleWooCommerceUpdates($scope) {
        // Listen for WooCommerce AJAX updates
        $(document).on('updated_checkout', function() {
            setTimeout(function() {
                updateCheckout($scope);
                initializeWooCommerceCheckout();
            }, 100);
        });

        // Listen for coupon updates
        $(document).on('applied_coupon removed_coupon', function() {
            setTimeout(function() {
                updateCheckout($scope);
            }, 50);
        });
    }

    function handleElementorEditor($scope) {
        // Disable sticky in Elementor editor
        if ($('body').hasClass('elementor-editor-active')) {
            $scope.find('.elementor-wc-checkout-right').removeClass('sticky');
        }

        // Listen for Elementor control changes
        if (typeof elementor !== 'undefined' && elementor.channels) {
            elementor.channels.editor.on('change', function(component, view) {
                if (component.model && component.model.attributes.widgetType === 'afeb_woo_checkout') {
                    setTimeout(function() {
                        updateCheckout($scope);
                        // Reinitialize WooCommerce after style changes
                        checkoutInitialized = false;
                        initializeWooCommerceCheckout();
                    }, 300);
                }
            });
        }
    }

    function initCheckoutWidget($scope) {
        // Initial setup
        updateCheckout($scope);
        initializeWooCommerceCheckout();
        handleWooCommerceUpdates($scope);
        handleElementorEditor($scope);

        // DOM mutations inside widget
        const observer = new MutationObserver(function(mutations) {
            let shouldUpdate = false;

            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0) {
                    shouldUpdate = true;
                }
            });

            if (shouldUpdate) {
                setTimeout(function() {
                    updateCheckout($scope);
                    // Reinitialize if form fields were added/removed
                    if ($scope.find('.woocommerce-checkout .form-row').length > 0) {
                        checkoutInitialized = false;
                        initializeWooCommerceCheckout();
                    }
                }, 100);
            }
        });

        observer.observe($scope[0], {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'style']
        });

        // Handle window resize for responsive layout
        let resizeTimer;
        $(window).on('resize.afeb', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                updateLayout($scope);
            }, 250);
        });
    }

    // Main initialization
    elementorFrontend.hooks.addAction('frontend/element_ready/afeb_woo_checkout.default', function($scope){
        // Wait for both Elementor and WooCommerce to be ready
        function checkDependencies() {
            if (typeof elementorFrontend !== 'undefined' && typeof wc_checkout_params !== 'undefined') {
                initCheckoutWidget($scope);
            } else {
                setTimeout(checkDependencies, 100);
            }
        }

        checkDependencies();
    });

    // Handle dynamic content loading
    $(document).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_woo_checkout.default', function($scope){
            if (typeof wc_checkout_params !== 'undefined') {
                initCheckoutWidget($scope);
            }
        });
    });

    // Fallback: Initialize when document is ready
    $(document).ready(function() {
        if ($('.elementor-widget-afeb_woo_checkout').length) {
            initializeWooCommerceCheckout();
        }
    });

})(jQuery);
