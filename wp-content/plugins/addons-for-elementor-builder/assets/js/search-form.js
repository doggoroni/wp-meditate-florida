(function ($, elementor) {
    'use strict';

    let afeb_widget_search_form = function ($scope, $) {

        let init = function ($scope, $) {
            let search_input = $scope.find('.afeb-search-form > form > input[name="s"]');

            if (!search_input.length) {
                return;
            }

            $(document).on('click keydown', function (e) {
                let $search_form = $(e.target).closest('.afeb-search-form');

                if ($search_form.length < 1 || (e.type == 'keydown' && e.keyCode == 27)) {
                    $('.afeb-search-form .afeb-search-form-live-results').fadeOut(80);
                }
            });

            let search_form_live_results = null;
            search_input.on('input focus', function (e) {
                let $this = $(this),
                    $search_form = $this.closest('.afeb-search-form'),
                    $live_results = $search_form.find('.afeb-search-form-live-results'),
                    $live_results_loader = $live_results.find('.afeb-search-form-live-results-loader'),
                    $nothing_found_message = $live_results.find('.afeb-search-form-live-results-nothing-found-message'),
                    $live_results_content = $live_results.find('.afeb-search-form-live-results-content');

                clearTimeout(search_form_live_results);
                if (e.type == 'focus') {

                    if ($live_results_content.html() != '') {
                        $live_results.fadeIn(100);
                    }

                    return false;
                }

                search_form_live_results = setTimeout(function () {

                    $live_results_content.html('');
                    $live_results_loader.show();
                    $nothing_found_message.hide();

                    if ($this.val().length >= 3) {
                        $.ajax({
                            url: AFEB.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'afeb_search_form_live_results', nonce: AFEB.nonce, data: {
                                    template_id: $search_form.find('input[name="template_id"]').val(),
                                    number_of_items: $search_form.find('input[name="number_of_items"]').val(),
                                    post_type: $search_form.find('input[name="post_type"]').val(),
                                    orderby: $search_form.find('input[name="orderby"]').val(),
                                    order: $search_form.find('input[name="order"]').val(),
                                    s: $this.val()
                                }
                            },
                            success: function (response) {
                                $live_results_loader.hide();
                                if (response == -1) {
                                    $nothing_found_message.show();
                                } else {
                                    $nothing_found_message.hide();
                                    $live_results_content.html(response);
                                }

                                $live_results.fadeIn(100);
                            }
                        });
                    } else {
                        $live_results_content.html('');
                        $live_results_loader.hide();
                        $live_results.fadeOut(80);
                    }
                }, 400);
            });

            $(document).on('submit', '.afeb-search-form>form', function (e) {
                let template_id = $(this).parent().find('input[name="template_id"]').length;

                if (template_id > 0) {
                    e.preventDefault();
                }
            });
        }

        init($scope, $);
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_search_form.default', afeb_widget_search_form);
    });

}(jQuery, window.elementorFrontend));