(function ($, elementor) {

    'use strict';

    let afeb_widget_team_member_carousel = function ($scope, $) {

        let init = function ($scope, $) {
            let carousel = $('.afeb-team-member-carousel');
            carousel.slick({
                customPaging: function (slider, i) {
                    return '<span></span>';
                }
            });
        }

        init($scope, $);
    }

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_team_member_carousel.default', afeb_widget_team_member_carousel);
    });
}(jQuery, window.elementorFrontend));
