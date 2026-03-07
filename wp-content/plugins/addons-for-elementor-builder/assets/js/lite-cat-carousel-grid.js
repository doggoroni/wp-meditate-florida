(function ($) {
    'use strict';

    const parseSwiperSettings = ($el) => {
        let settings = $el.data('swiper-settings');

        if (!settings) {
            const settingsAttr = $el.attr('data-swiper-settings');
            if (settingsAttr) {
                try {
                    settings = JSON.parse(settingsAttr);
                } catch (error) {
                    console.error('Invalid swiper settings JSON:', error);
                }
            }
        }

        return settings || {};
    };

    const initSwiper = ($el) => {
        if (typeof Swiper === 'undefined') {
            console.error('Swiper library not loaded');
            return;
        }

        if ($el.data('afebSwiperInstance') || ($el[0] && $el[0].swiper)) {
            return;
        }

        const settings = parseSwiperSettings($el);
        const items = settings.items || {};

        const desktopItems = items.desktop || 3;
        const tabletItems = items.tablet || desktopItems || 2;
        const mobileItems = items.mobile || 1;

        const navigationEnabled = settings.navigation;
        const paginationEnabled = settings.pagination;

        const instance = new Swiper($el[0], {
            slidesPerView: desktopItems,
            spaceBetween: settings.spaceBetween || 20,
            loop: !!settings.loop,
            autoplay: settings.autoplay || false,
            navigation: navigationEnabled ? {
                nextEl: $el.find('.swiper-button-next')[0],
                prevEl: $el.find('.swiper-button-prev')[0],
            } : false,
            pagination: paginationEnabled ? {
                el: $el.find('.swiper-pagination')[0],
                clickable: true,
            } : false,
            breakpoints: {
                768: {
                    slidesPerView: tabletItems
                },
                1024: {
                    slidesPerView: desktopItems
                },
                0: {
                    slidesPerView: mobileItems
                }
            }
        });

        $el.data('afebSwiperInstance', instance);
    };

    const initScope = ($scope) => {
        const $targets = $scope.hasClass('afebp-woo-cat-swiper')
            ? $scope
            : $scope.find('.afebp-woo-cat-swiper');

        $targets.each(function () {
            initSwiper($(this));
        });
    };

    $(function () {
        initScope($(document));
    });

    $(window).on('elementor/frontend/init', function () {
        const widgetHandler = function ($scope) {
            initScope($scope);
        };

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/afeb_woo_product_category.default',
            widgetHandler
        );
    });
})(jQuery);
