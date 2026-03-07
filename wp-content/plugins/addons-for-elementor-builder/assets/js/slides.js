(function ($, elementor) {

    'use strict';

    let afeb_widget_slides = function ($scope, $) {

        let init = function ($scope, $) {
            let carousel = $('.afeb-slides-carousel');
            carousel.slick({
                customPaging: function (slider, i) {
                    return '<span></span>';
                }
            });
        }

        init($scope, $);
    }

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_slides.default', afeb_widget_slides);
    });
}(jQuery, window.elementorFrontend));