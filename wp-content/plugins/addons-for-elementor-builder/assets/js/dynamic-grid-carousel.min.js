(function ($, elementor) {

    'use strict';

    // Utility function to generate animation effects for in and out transitions
    const getAnimationEffects = (animation, direction) => {
        let effects = '';
        const effectPrefix = direction === 'in' ? 'in' : 'out';

        // Check for custom effects
        if (animation[`${effectPrefix}_custom_effect`] && animation[`${effectPrefix}_custom_effect`].includes('fade')) {
            effects += ' fade ';
        }

        // Check for scale, translation, and rotation effects
        const transformProps = ['scale', 'translate_x', 'translate_y', 'translate_z', 'rotate_x', 'rotate_y', 'rotate_z'];
        transformProps.forEach(prop => {
            const value = animation[`${effectPrefix}_${prop}`];
            if (value) {
                const propertyName = prop.replace('_', '').replace(/x|y|z/, match => match.toUpperCase());
                if (prop.includes('translate')) {
                    // Add 'px' for translate effects
                    effects += ` ${propertyName}(${parseFloat(value)}px) `;
                } else if (prop.includes('rotate')) {
                    // Add 'deg' for rotation effects
                    effects += ` ${propertyName}(${parseFloat(value)}deg) `;
                } else {
                    effects += ` ${propertyName}(${parseFloat(value)}) `;
                }
            }
        });

        return effects;
    };

    // Main widget function
    const afebWidgetDynamicGridCarousel = function ($scope) {
        const dynamicGridCarousel = $scope.find('.afeb-dynamic-grid-carousel');
        const itemStyle = $scope.find('.afeb-dynamic-grid-carousel-items > style:first-child').html();

        // Append item styles to head if they exist
        if (itemStyle) {
            $scope.find('.afeb-dynamic-grid-carousel-items > style').remove();
            $('head').append(`<style>${itemStyle}</style>`);
        }

        const animation = dynamicGridCarousel.data('attrs');
        let effectsIn = getAnimationEffects(animation, 'in');
        let effectsOut = getAnimationEffects(animation, 'out');

        // Handle animation effect overrides
        effectsIn = animation.effect_in !== 'custom' ? animation.effect_in : effectsIn;
        effectsOut = animation.effect_out !== 'custom' ? animation.effect_out : effectsOut;

        const enableAnimation = animation.enable === 'yes';

        dynamicGridCarousel.attr('style', ''); // Reset inline styles

        // Destroy existing MixitUp instance if exists
        if (window.afebDynamicGridMixer) {
            window.afebDynamicGridMixer.destroy();
        }

        // Initialize MixitUp
        window.afebDynamicGridMixer = mixitup(dynamicGridCarousel, {
            selectors: { target: '.afeb-dynamic-grid-carousel-item' },
            controls: { scope: 'local' },
            animation: {
                enable: enableAnimation,
                duration: animation.duration || 180,
                applyPerspective: animation.perspective === '',
                easing: 'ease-in-out',
                effectsIn,
                effectsOut,
                perspectiveDistance: animation.perspective_distance ? `${animation.perspective_distance}px` : '2000px',
                perspectiveOrigin: '100% 0',
            },
            load: { filter: 'none' }
        });

        // Show the items with animation after initialization
        window.afebDynamicGridMixer.show();

        // Hide the items when leaving the page (if animation is enabled)
        if (enableAnimation) {
            $(window).on('beforeunload', () => {
                // Hide animations or changes before the page reloads or navigates
                window.afebDynamicGridMixer.hide();

                // Prevent the "Changes you made may not be saved" warning by returning undefined
                return undefined;  // This tells the browser that there are no unsaved changes
            });

        }

        // Initialize Slick carousel if needed
        const carouselItems = $scope.find('.afeb-dynamic-carousel .afeb-dynamic-grid-carousel-items');

        if (!$scope.find('.afeb-dynamic-carousel > div.afeb-error').length) {
            carouselItems.slick();
        }
    };

    // Hook into Elementor's frontend initialization
    jQuery(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_dynamic_grid_carousel.default', afebWidgetDynamicGridCarousel);
    });

})(jQuery, window.elementorFrontend);
