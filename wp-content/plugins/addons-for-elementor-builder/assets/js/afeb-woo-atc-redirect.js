(function($){
    $(document).on('click', '.afeb-woo-atc-button', function(e){
        var $btn = $(this);
        var redirect   = $btn.data('redirect') || 'stay';
        var productId  = $btn.data('product_id');
        var qty        = parseInt($btn.closest('.afeb-woo-atc-inner').find('input.qty').val(), 10) || 1;

        if (window.elementorFrontend && elementorFrontend.isEditMode()) {
            e.preventDefault();
            return;
        }

        if (redirect === 'stay') {
            return; // leave WooCommerce AJAX alone
        }

        e.preventDefault();

        // Use localized URLs instead of relying on wc_add_to_cart_params
        var baseUrl = (redirect === 'cart')
            ? afeb_atc_params.cart_url
            : afeb_atc_params.checkout_url;

        if (!baseUrl) {
            console.error('Redirect URL not provided.');
            return;
        }

        var targetUrl = baseUrl + (baseUrl.indexOf('?') >= 0 ? '&' : '?') +
            'add-to-cart=' + encodeURIComponent(productId) +
            '&quantity=' + encodeURIComponent(qty);

        window.location.assign(targetUrl);
    });
})(jQuery);
