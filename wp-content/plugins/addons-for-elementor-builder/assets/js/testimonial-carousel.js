(function ($, elementor) {
    'use strict';
    let afeb_widgetTCarousel = function ($scope, $) {

        let init = function ($scope, $) {
            let carousel = $scope.find('.afeb-testimonial-carousel');
            carousel.slick({
                customPaging: function (slider, i) {
                    return '<span></span>';
                }
            });
        }

        init($scope, $);
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_testimonial_carousel.default', afeb_widgetTCarousel);
    });

}(jQuery, window.elementorFrontend));