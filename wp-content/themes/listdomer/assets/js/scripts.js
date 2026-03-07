/**
 * File scripts.js.
 *
 * Main Javascript Codes
 */

function listdomer_responsive_menu_handle_key(e) {
    if (e.keyCode === 9) {
        let focusable = document
        .querySelector("#site-navigation")
        .querySelectorAll("input,button,select,textarea,a");
        let lastLiA = document.querySelectorAll(
            "#site-navigation > div > ul > li:last-child > a"
        )[0];
        let lastLiUL = document.querySelectorAll(
            "#site-navigation > div > ul > li:last-child > ul"
        )[0];

        if (focusable.length) {
            let first = document.getElementById("main-navigation-close");
            let last = focusable[focusable.length - 1];

            let shift = e.shiftKey;
            if (shift) {
                if (e.target === first) {
                    // shift-tab pressed on first input in dialog
                    if (lastLiUL && lastLiUL.style.display === "block") {
                        last.focus();
                    } else {
                        lastLiA.focus();
                    }
                    e.preventDefault();
                }
            } else {
                if (
                    e.target === last ||
                    (e.target === lastLiA &&
                        lastLiUL &&
                        lastLiUL.style.display !== "block")
                ) {
                    // tab pressed on last input in dialog
                    first.focus();
                    e.preventDefault();
                }
            }
        }
    }
}

(function ($) {
    // Document is Ready!
    $(function () {
        $("span").tooltip();

        function makeMegaMenus() {
            $("ul#primary-menu li > ul").each(function () {
                const $submenu = $(this);
                const itemCount = $submenu.children('li').length;

                if (itemCount > 15) {
                    $submenu.css({
                        'display': 'flex',
                        'flex-direction': 'column',
                        'flex-wrap': 'wrap',
                        'column-gap': '30px',
                        'max-height': '1024px',
                        'height': '500px',
                        'min-width': 'max-content',
                    });
                } else {
                    $submenu.css({
                        'display': '',
                        'flex-direction': '',
                        'flex-wrap': '',
                        'column-gap': '',
                        'max-height': '',
                        'height': '',
                    });
                }

                $submenu.find("ul").css({
                    'z-index': 999,
                    'min-height': 'max-content',
                });
            });
        }

        if ($(document).width() > 768) {
            makeMegaMenus();
            $("ul#primary-menu li > ul").hide();
        }

        if ($(document).width() < 768) {
            $("ul#primary-menu li.menu-item-has-children > a").append('<span class="lsd-submenu-arrow"></span>');
            $("ul#primary-menu li.menu-item-has-children > a > .lsd-submenu-arrow").on('click', function () {
                $(this).parent().next().toggle();
                return false;
            });
        } else {
            $("ul#primary-menu li.menu-item-has-children > a").append('<span class="lsd-submenu-arrow"></span>');
            $("ul#primary-menu li.menu-item-has-children").on('mouseenter', function () {
                $(this).children("ul").stop().fadeIn();
            });

            $("ul#primary-menu li.menu-item-has-children").on('mouseleave', function () {
                $(this).children("ul").stop().fadeOut();
            });

            $("ul#primary-menu > li.menu-item-has-children:last-child > ul li:last-child > a").focusout(function () {
                $("ul#primary-menu > li ul").stop().fadeOut();
            });
        }

        if ($("li.wpml-ls-current-language").length) {
            $("li.wpml-ls-current-language").prependTo(
                ".listdomer-language-switcher ul"
            );
        }

        $(".wpml-ls-current-language a").on("click", function () {
            $(this)
            .parent()
            .parent()
            .children("li")
            .not($(this).parent())
            .fadeToggle();
            $(".listdomer-user-logister-wrapper").fadeOut();
            return false;
        });

        if ($("li.current-lang").length) {
            $("li.current-lang").prependTo(".listdomer-language-switcher ul");
        }

        $(".current-lang a").on("click", function () {
            $(this)
            .parent()
            .parent()
            .children("li")
            .not($(this).parent())
            .fadeToggle();
            $(".listdomer-user-logister-wrapper").fadeOut();
            return false;
        });

        $("#listdomer-user-button").on("click", function () {
            $(this).next().fadeToggle();
            $(".listdomer-language-switcher li")
            .not($(".wpml-ls-current-language"))
            .not($(".current-lang"))
            .fadeOut();
            return false;
        });

        $(document).on("click", function (event) {
            const $target = $(event.target);
            if (!$target.closest(".listdomer-language-switcher").length) {
                $(".listdomer-language-switcher li")
                .not($(".wpml-ls-current-language"))
                .not($(".current-lang"))
                .fadeOut();
            }

            if (!$target.closest(".listdomer-user").length) {
                $(".listdomer-user-logister-wrapper").fadeOut();
            }
        });

        $(".listdomer-login-register-toggle").on("click", function () {
            const target = $(this).data('target');
            const $login = $("form[name=listdomer-loginform]");
            const $register = $("form[name=listdomer-registerform]");

            if (target === "register") {
                $login.addClass("listdomer-util-hide");
                $register.removeClass("listdomer-util-hide");
            } else {
                $login.removeClass("listdomer-util-hide");
                $register.addClass("listdomer-util-hide");
            }
        });

        $("select")
        .not($("select[data-enhanced=0]"))
        .select2({
            minimumResultsForSearch: 0,
            shouldFocusInput: () => false
        });

        // Carousel
        $(".listdomer-carousel").each(function () {
            let $carousel = $(this);

            let count = $carousel.data("count");
            let navigation = $carousel.data("navigation");
            let autoplay = $carousel.data("autoplay");
            let loop = $carousel.data("loop");

            $carousel.owlCarousel({
                items: count,
                loop: loop,
                autoplay: autoplay,
                autoplayHoverPause: true,
                dots: navigation === "dots",
                nav: navigation === "nav",
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                    },
                    568: {
                        items: Math.min(2, count),
                    },
                    1024: {
                        items: Math.min(3, count),
                    },
                    1440: {
                        items: Math.min(4, count),
                    },
                },
            });
        });

        $(".main-navigation-close").on("click", function () {
            $("#site-navigation").slideUp();
            $("body").removeClass('listdomer-responsive-menu-open');

            $(".listdomer-language-switcher").fadeToggle();
            $(".listdomer-add-listing").fadeToggle();
        });

        $("#listdomer-responsive-sidebar-menu").on("click", function () {
            $("#site-navigation").slideToggle();
            $("body").addClass('listdomer-responsive-menu-open');

            $(".listdomer-language-switcher").fadeToggle();
            $(".listdomer-add-listing").fadeToggle();
        });

        if ($(document).width() > 768) {
            let $a = $("ul#primary-menu li > a");
            $a.on('mousedown', function () {
                $(this).addClass('listdomer-primary-menu-clicked');
                setTimeout(() => $(this).removeClass('listdomer-primary-menu-clicked'), 500);
            });
            $a.on('focus', function () {
                if ($(this).hasClass('listdomer-primary-menu-clicked')) return;
                $(this)
                .parent()
                .parent()
                .children("li")
                .children("ul")
                .not($(this).next())
                .hide();

                if ($(this).parent().hasClass("menu-item-has-children")) {
                    $(this).next().stop().fadeToggle();
                    $(this).next().children("li").children("ul").hide();
                }
            });
        }

        $("li.menu-item-has-children > a").focus(function () {
            $(this).parent().addClass("focus");
        });

        $(document).on("keypress", function (e) {
            if (e.which === 13 || e.which === 32) {
                const focused_element = $(":focus");
                const focused_element_id = focused_element.attr("id");

                if (focused_element.parent().hasClass("menu-item-has-children")) {
                    focused_element.next().toggle();
                    return false;
                }

                if (focused_element.hasClass("main-navigation-close")) {
                    $(".listdomer-responsive-sidebar-menu").trigger("click");
                    $("#listdomer-responsive-sidebar-menu").focus();
                    window.removeEventListener(
                        "keydown",
                        listdomer_responsive_menu_handle_key
                    );
                }

                if (focused_element_id === "listdomer-user-button") {
                    $(":focus").trigger("click");
                    return false;
                }

                if (focused_element_id === "listdomer-responsive-sidebar-menu") {
                    $(":focus").trigger("click");
                    $("#site-navigation").children(".main-navigation-close").focus();

                    window.addEventListener(
                        "keydown",
                        listdomer_responsive_menu_handle_key
                    );

                    return false;
                }
            }
        });
    });

    $(window).on('beforeunload', function () {
        $('#listdomer-preloader-box').fadeIn('fast');
    });

    $(window).on('pageshow', function (event) {
        if (event.originalEvent.persisted) {
            fadeOutPreloader();
        }
    });

    $(window).on('load', function () {
        clearTimeout(fallbackTimeout);
        fadeOutPreloader();
    });

    // Fallback timeout in case the load event is delayed
    let fallbackTimeout = setTimeout(fadeOutPreloader, 6000);

    function fadeOutPreloader() {
        $('#listdomer-preloader-box').fadeOut(500);
    }

    $('.lsd-description-toggle').on('click', function () {
        const $button = $(this);
        const $container = $('.lsd-description-content');
        const state = $button.data('state');

        if (state === 'less') {
            $container.html($container.data('full'));
            $button.text($button.data('show-less-label')).data('state', 'more');
        } else {
            $container.html('<p>' + $container.data('short') + '</p>');
            $button.text($button.data('show-more-label')).data('state', 'less');
        }
    });
})(jQuery);
