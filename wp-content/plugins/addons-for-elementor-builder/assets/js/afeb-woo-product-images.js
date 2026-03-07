(function($){
    var widgets = {};

    function initProductImage($widget){    if (typeof Swiper === 'undefined') {
        setTimeout(function() { initProductImage($widget); }, 50);
        return;
    }

        var widgetId = $widget.attr('id');

        // Destroy previous swipers if they exist
        if (widgets[widgetId]) {
            if (widgets[widgetId].mainSwiper) widgets[widgetId].mainSwiper.destroy(true, true);
            if (widgets[widgetId].thumbSwiper) widgets[widgetId].thumbSwiper.destroy(true, true);
        }

        var $main   = $widget.find('.afeb-main-swiper');
        var $thumbs = $widget.find('.afeb-woo-pi-thumbs.swiper');
        var thumbsPosClass = $widget.hasClass('afeb-thumbs-left') || $widget.hasClass('afeb-thumbs-right');

        // ✅ Ensure arrows + pagination exist inside main swiper
// Read icon classes from the widget wrapper
        var prevIcon = $widget.data('prev-icon') || 'fas fa-arrow-circle-left';
        var nextIcon = $widget.data('next-icon') || 'fas fa-arrow-circle-right';

// Inject icons inside the nav buttons
        if (!$main.find('.swiper-button-prev').length) {
            $main.append(
                '<div class="swiper-button-prev"><i class="' + prevIcon + '"></i></div>' +
                '<div class="swiper-button-next"><i class="' + nextIcon + '"></i></div>' +
                '<div class="swiper-pagination"></div>'
            );
        }

        // Get arrow + pagination DOM elements
        var nextArrow = $main.find('.swiper-button-next')[0];
        var prevArrow = $main.find('.swiper-button-prev')[0];
        var paginationEl = $main.find('.swiper-pagination')[0];

        // ✅ Read gap from CSS variable for thumbs in slider mode
        var thumbGap = parseInt(
            getComputedStyle($widget[0]).getPropertyValue('--afeb-thumb-gap')
        ) || 10;

        // Init thumbs swiper
        var thumbSwiper = new Swiper($thumbs[0], {
            direction: thumbsPosClass ? 'vertical' : 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: thumbGap, // Now respects Elementor control
            freeMode: true,
            watchSlidesProgress: true,
            watchSlidesVisibility: true
        });

        // Init main swiper with DOM-based navigation + pagination
        var mainSwiper = new Swiper($main[0], {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 0,
            grabCursor: true,
            navigation: {
                nextEl: nextArrow,
                prevEl: prevArrow
            },
            pagination: {
                el: paginationEl,
                clickable: true
            },
            thumbs: {
                swiper: thumbSwiper
            }
        });

        // Highlight active thumbnail
        mainSwiper.on('slideChange', function(){
            var realIdx = mainSwiper.realIndex;
            $thumbs.find('.swiper-slide').removeClass('thumb-active');
            $thumbs.find('.swiper-slide').eq(realIdx).addClass('thumb-active');
        });

        // Thumb click sync
        $thumbs.find('.swiper-slide').off('click').on('click', function(){
            mainSwiper.slideToLoop($(this).index());
        });

        // ✅ Scoped Lightbox init AFTER Swiper ready
        mainSwiper.on('afterInit', function(){
            var selector = '#' + widgetId + ' .glightbox';
            if ($(selector).length && typeof GLightbox === 'function') {
                GLightbox({
                    selector: selector,
                    touchNavigation: true,
                    loop: true,
                    autoplayVideos: false
                });
            }
        });

        // Save references
        widgets[widgetId] = {
            mainSwiper: mainSwiper,
            thumbSwiper: thumbSwiper
        };
    }

    // Elementor hook
    $(window).on('elementor/frontend/init', function(){
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/afeb-product-image.default',
            function($scope){
                initProductImage($scope);
            }
        );
    });

    // Update swipers on resize
    $(window).on('resize', function(){
        $.each(widgets, function(id, widget){
            if (widget.mainSwiper) widget.mainSwiper.update();
            if (widget.thumbSwiper) widget.thumbSwiper.update();
        });
    });

})(jQuery);
