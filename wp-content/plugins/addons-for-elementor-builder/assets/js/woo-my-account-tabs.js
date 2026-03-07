(function($) {
    var isEditor = $('body').hasClass('elementor-editor-active') ||
        $('body').hasClass('elementor-editor-preview');

    var endpoints = {
        main: ['dashboard', 'orders', 'downloads', 'edit-address', 'edit-account', 'payment-methods', 'customer-logout'],
        sub: ['view-order', 'add-payment-method', 'billing', 'shipping']
    };
    var formEndpoints = ['edit-account', 'edit-address', 'billing', 'shipping'];
    var cachedMyAccountBaseUrl = null;

    function ensureTrailingSlash(url) {
        if (typeof url !== 'string') {
            return url;
        }

        return url.endsWith('/') ? url : url + '/';
    }

    function deriveBaseUrl(url) {
        if (!url) {
            return null;
        }

        try {
            var parsed = new URL(url, window.location.origin);
            var path = parsed.pathname;
            var segments = path.split('/').filter(Boolean);
            var endpointIndex = -1;

            for (var i = 0; i < segments.length; i++) {
                if (endpoints.main.includes(segments[i]) || endpoints.sub.includes(segments[i])) {
                    endpointIndex = i;
                    break;
                }
            }

            var baseSegments = endpointIndex === -1 ? segments : segments.slice(0, endpointIndex);
            var basePath = baseSegments.length ? '/' + baseSegments.join('/') + '/' : '/';

            return ensureTrailingSlash(parsed.origin + basePath);
        } catch (error) {
            return null;
        }
    }

    function getMyAccountBaseUrl() {
        if (cachedMyAccountBaseUrl) {
            return cachedMyAccountBaseUrl;
        }

        if (typeof wc_myaccount_params === 'object' && wc_myaccount_params && wc_myaccount_params.myaccount_url) {
            cachedMyAccountBaseUrl = ensureTrailingSlash(wc_myaccount_params.myaccount_url);
            return cachedMyAccountBaseUrl;
        }

        var baseFromNav = null;
        $('.woocommerce-MyAccount-navigation a').each(function() {
            var href = $(this).attr('href');
            var derived = deriveBaseUrl(href);

            if (derived) {
                baseFromNav = derived;
                return false;
            }
        });

        if (baseFromNav) {
            cachedMyAccountBaseUrl = baseFromNav;
            return cachedMyAccountBaseUrl;
        }

        var baseFromLocation = deriveBaseUrl(window.location.href);

        if (baseFromLocation) {
            cachedMyAccountBaseUrl = baseFromLocation;
            return cachedMyAccountBaseUrl;
        }

        return null;
    }

    /**
     * Detect the current WooCommerce endpoint from the URL.
     * Works with any base (e.g. /my-account/, /local-elemetor-widget/).
     */
    function getCurrentEndpoint(url) {
        url = url || window.location.href;
        var path = new URL(url, window.location.origin).pathname;
        var parts = path.split('/').filter(Boolean);

        // Check sub-endpoints first
        if (parts.includes('view-order')) return 'view-order';
        if (parts.includes('add-payment-method')) return 'add-payment-method';
        if (parts.includes('edit-address')) {
            if (parts.includes('billing')) return 'billing';
            if (parts.includes('shipping')) return 'shipping';
            return 'edit-address';
        }

        var endpoint = parts.find(p =>
            endpoints.main.includes(p) || endpoints.sub.includes(p)
        );

        return endpoint || 'dashboard';
    }

    /**
     * Highlight the active navigation tab and show its content.
     */
    function setActiveNav(endpoint)
    {
        var $navItems = $('.woocommerce-MyAccount-navigation li');
        var $links = $navItems.find('a');

        $navItems.removeClass('is-active');

        $links.each(function ()
        {
            var href = $(this).attr('href');
            var hrefEndpoint = getCurrentEndpoint(href);

            if (hrefEndpoint === endpoint) $(this).parent().addClass('is-active');
        });

        var $contentWrapper = $('.woocommerce-MyAccount-content');
        $contentWrapper.removeClass('is-active').css({
            display: 'block',
            opacity: '1'
        });

        var $activeContent = $contentWrapper.filter('[data-endpoint="' + endpoint + '"]');
        $activeContent.addClass('is-active').css({
            display: 'block',
            opacity: '1'
        });

        if (formEndpoints.includes(endpoint)) $activeContent.find('form').addClass('is-active');
    }

    function loadContent(url) {
        var $container = $('.afeb-my-account-tab');

        if (!$container.length) {
            return;
        }

        var endpoint = getCurrentEndpoint(url);
        var $content = $container.find('.woocommerce-MyAccount-content');
        $container.addClass('loading');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                var $response = $(response);
                var newContent = $response.find('.woocommerce-MyAccount-content').first();
                if (newContent.length) {
                    var scrollPos = $('html, body').scrollTop();
                    $content.html(newContent.html())
                        .attr('data-endpoint', endpoint)
                        .addClass('is-active')
                        .css('display', 'block !important')
                        .css('opacity', '1 !important');
                    if (formEndpoints.includes(endpoint)) {
                        $content.find('form').addClass('is-active');
                    }
                    if (typeof $.fn.select2 === 'function') {
                        $('select:visible').select2();
                    }
                    $(document.body).trigger('wc_fragment_refresh');
                    $('html, body').scrollTop(scrollPos);
                }
                setActiveNav(endpoint);
            },
            error: function() {
                if ($container.length) {
                    window.location.href = url;
                }
            },
            complete: function() {
                $container.removeClass('loading');
            }
        });
    }

    function handleEditorPreview(endpoint) {
        var $content = $('.woocommerce-MyAccount-content');
        $content.removeClass('is-active')
            .css('display', 'block')
            .css('opaciy', '1');
        switch(endpoint) {
            case 'view-order':
                $content.filter('[data-endpoint="orders"]')
                    .find('.woocommerce-orders-table__row--status-completed a:first')
                    .closest('.woocommerce-MyAccount-content')
                    .addClass('is-active')
                    .css('display', 'block !important')
                    .css('opacity', '1 !important');
                break;
            case 'billing':
            case 'shipping':
                $content.filter('[data-endpoint="edit-address"]')
                    .addClass('is-active')
                    .css('display', 'block !important')
                    .css('opacity', '1 !important')
                    .find('.woocommerce-address-fields[data-address-type="' + endpoint + '"]')
                    .addClass('is-active');
                break;
            case 'edit-account':
                $content.filter('[data-endpoint="edit-account"]')
                    .addClass('is-active')
                    .css('display', 'block !important')
                    .css('opacity', '1 !important')
                    .find('form')
                    .addClass('is-active');
                break;
            default:
                $content.filter('[data-endpoint="' + endpoint + '"]')
                    .addClass('is-active')
                    .css('display', 'block !important')
                    .css('opacity', '1 !important');
        }
    }

    function initWidget() {
        var $container = $('.afeb-my-account-tab');

        if (!$container.length) {
            return;
        }

        var currentEndpoint = getCurrentEndpoint();
        if (!window.afebMyAccountInitialized) {
            window.afebMyAccountInitialized = true;
            setActiveNav(currentEndpoint);
            if (currentEndpoint === 'dashboard') {
                loadContent(window.location.href);
            } else {
                $('.woocommerce-MyAccount-content[data-endpoint="' + currentEndpoint + '"]').addClass('is-active');
            }
        }
        setActiveNav(currentEndpoint);

        $(document).on('click', '.woocommerce-MyAccount-navigation a, ' +
            '.woocommerce-orders-table a, ' +
            '.woocommerce-addresses a.edit, ' +
            '.woocommerce-EditAccountForm a', function(e) {
            var url = $(this).attr('href');
            var endpoint = getCurrentEndpoint(url);
            if (isEditor) {
                e.preventDefault();
                handleEditorPreview(endpoint);
                setActiveNav(endpoint);
                return;
            }
            e.preventDefault();
            if ($(this).hasClass('edit')) {
                var $content = $('.woocommerce-MyAccount-content');
                $content.removeClass('is-active')
                    .css('display', 'block')
                    .css('opacity', '1');
                $content.filter('[data-endpoint="edit-address"]')
                    .addClass('is-active')
                    .css('display', 'block !important')
                    .css('opacity', '1 !important')
                    .find('.woocommerce-address-fields[data-address-type="' + endpoint + '"]')
                    .addClass('is-active');
                history.pushState({ endpoint }, '', url);
                return;
            }
            history.pushState({ endpoint }, '', url);
            loadContent(url);
        });

        window.addEventListener('popstate', function(e) {
            if (!isEditor && e.state && e.state.endpoint) {
                loadContent(window.location.href);
            }
        });

        if (isEditor) {
            $('.woocommerce-MyAccount-content form').css({
                'pointer-events': 'auto',
                'opacity': '1'
            });
        }
        setTimeout(function() {
            if (!$('.woocommerce-MyAccount-content.is-active').is(':visible')) {
                $('.woocommerce-MyAccount-content[data-endpoint="' + currentEndpoint + '"]')
                    .addClass('is-active')
                    .css('display', 'block !important')
                    .css('opacity', '1 !important');
            }
        }, 300);
    }

    function handleElementorChanges() {
        if (typeof elementor !== 'undefined' && elementor.on) {
            elementor.on('frontend:element:change', function(model) {
                if (model.get('widgetType') === 'afeb_woo_my_account') {
                    if (model.changed && model.changed.tabs) {
                        var baseUrl = getMyAccountBaseUrl();
                        var $navigationList = $('.woocommerce-MyAccount-navigation ul');

                        $navigationList.empty();
                        var tabs = model.get('settings').tabs;
                        tabs.forEach(function(tab) {
                            var tabUrl = baseUrl ? baseUrl + tab.field_key + '/' : '#';

                            $navigationList.append(
                                '<li><a href="' + tabUrl + '">' + tab.tab_name + '</a></li>'
                            );
                        });
                        initWidget();
                    }
                    // 🚀 Inject live dashboard refresh on select change
                    if (model.changed && model.changed.customize_dashboard_select !== undefined && isEditor) {
                        var dashboardEndpoint = 'dashboard';
                        var $dashboardContent = $('.woocommerce-MyAccount-content[data-endpoint="' + dashboardEndpoint + '"]');
                        var baseUrl = getMyAccountBaseUrl();

                        if (baseUrl) {
                            $.ajax({
                                url: baseUrl + dashboardEndpoint + '/',
                                type: 'GET',
                                dataType: 'html',
                                success: function(response) {
                                    var $newContent = $(response).find('.woocommerce-MyAccount-content[data-endpoint="' + dashboardEndpoint + '"]');
                                    if ($newContent.length) {
                                        $dashboardContent.html($newContent.html());
                                        handleEditorPreview(dashboardEndpoint);
                                        setActiveNav(dashboardEndpoint);
                                    }
                                }
                            });
                        }
                    }
                }
            });
        }
    }

    $(function() {
        var hasWidget = $('.afeb-my-account-tab').length > 0;

        if (isEditor && typeof elementor !== 'undefined') {
            elementor.on('preview:loaded', function() {
                setTimeout(initWidget, 500);
            });
        } else if (hasWidget) {
            initWidget();
        }

        if (hasWidget || (isEditor && typeof elementor !== 'undefined')) {
            handleElementorChanges();
        }
    });
})(jQuery);
