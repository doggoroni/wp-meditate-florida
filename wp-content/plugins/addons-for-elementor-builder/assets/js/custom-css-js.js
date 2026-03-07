jQuery(document).ready(function ($) {
    window.addEventListener('message', function (e) {
        if (e.origin != window.origin || e.source.location.href != window.parent.location.href) return;
        let data = e.data;
        let custom_js = (typeof data.afeb_cstm_js !== 'undefined' && data.afeb_cstm_js !== null) ? data.afeb_cstm_js : '';
        let custom_header_js = (typeof data.afeb_cstm_hdr_js !== 'undefined' && data.afeb_cstm_hdr_js !== null) ? data.afeb_cstm_hdr_js : '';
        let custom_footer_js = (typeof data.afeb_cstm_ftr_js !== 'undefined' && data.afeb_cstm_ftr_js !== null) ? data.afeb_cstm_ftr_js : '';

        if (custom_js == 'afeb_cstm_hdr_js') {
            $('#afeb-custom-header-script-js').remove();
            $('head').find('.afeb-custom-header-js').remove();

            if (custom_header_js != '') {
                $('head').append('<script class="afeb-custom-header-js">' + custom_header_js + '</script>');
            }
        }
        if (custom_js == 'afeb_cstm_ftr_js') {
            $('#afeb-custom-footer-script-js').remove();
            $('body').find('.afeb-custom-footer-js').remove();
            if (custom_footer_js != '') {
                $('body').append('<script class="afeb-custom-footer-js">' + custom_footer_js + '</script>');
            }
        }
    });
});
