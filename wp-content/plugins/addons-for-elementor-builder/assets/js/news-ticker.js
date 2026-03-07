(function ($, elementor) {
    'use strict';
    let afeb_widgetNewsTicker = function ($scope, $) {

        let init = function ($scope, $) {
            let items = $('.afeb-news-ticker .afeb-news-ticker-items');
            items.slick();
        }

        init($scope, $);
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_news_ticker.default', afeb_widgetNewsTicker);
    });

}(jQuery, window.elementorFrontend));