(function ($) {
    'use strict';

    var afebWooCatWidget = function ($scope, $) {
        var $container = $scope.find('.afebp-woo-cat-swiper');

        if ($container.length) {
            var settings = $scope.data('settings') || {};

            var swiperSettings = {
                slidesPerView: settings.carousel_items || 3,
                spaceBetween: settings.item_gap?.size || 20,
                loop: settings.carousel_loop === 'yes',
                autoplay: settings.carousel_autoplay === 'yes' ? {
                    delay: parseInt(settings.carousel_speed) || 3000,
                    disableOnInteraction: false
                } : false,
                navigation: settings.carousel_arrows === 'yes' ? {
                    nextEl: $scope.find('.swiper-button-next')[0],
                    prevEl: $scope.find('.swiper-button-prev')[0],
                } : {},
                pagination: settings.carousel_dots === 'yes' ? {
                    el: $scope.find('.swiper-pagination')[0],
                    clickable: true,
                } : {},
                breakpoints: {
                    768: { slidesPerView: settings.carousel_items_tablet || 2 },
                    1024: { slidesPerView: settings.carousel_items || 3 },
                }
            };
            new Swiper($container[0], swiperSettings);
        }
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/afeb_woo_product_category.default',
            afebWooCatWidget
        );
    });

}(jQuery));
