jQuery(document).ready(function ($) {
    let add_query_args = function (key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(key, value);
        window.history.pushState({ path: url.href }, '', url.href);
        window.history.replaceState({ path: url.href }, '', url.href)
    }
    let remove_query_args = function (key) {
        const url = new URL(window.location.href);
        url.searchParams.delete(key);
        window.history.pushState({ path: url.href }, '', url.href);
        window.history.replaceState({ path: url.href }, '', url.href)
    }
    $(document).on('click', '.afeb-show-signin-form', function (e) {
        let frm_bx = $(this).parent().parent().parent();
        remove_query_args('afeb-register');
        remove_query_args('afeb-lostpassword');
        add_query_args('afeb-login', 'yes');
        frm_bx.find('.afeb-lr-form-succ-box, .afeb-lr-form-err-box').remove();
        frm_bx.find('.afeb-lostpassword-form').hide();
        frm_bx.find('.afeb-register-form').hide();
        frm_bx.find('.afeb-login-form').show();
    });
    $(document).on('click', '.afeb-show-signup-form', function (e) {
        let frm_bx = $(this).parent().parent().parent();
        remove_query_args('afeb-login');
        remove_query_args('afeb-lostpassword');
        add_query_args('afeb-register', 'yes');
        frm_bx.find('.afeb-lr-form-succ-box, .afeb-lr-form-err-box').remove();
        frm_bx.find('.afeb-login-form').hide();
        frm_bx.find('.afeb-register-form').show();
    });
    $(document).on('click', '.afeb-lp-show-signin-form', function (e) {
        let frm_bx = $(this).parent().parent().parent();
        remove_query_args('afeb-register');
        remove_query_args('afeb-lostpassword');
        add_query_args('afeb-login', 'yes');
        frm_bx.find('.afeb-lr-form-succ-box, .afeb-lr-form-err-box').remove();
        frm_bx.find('.afeb-lostpassword-form').hide();
        frm_bx.find('.afeb-login-form').show();
    });
    $(document).on('click', '.afeb-show-lp-form', function (e) {
        let frm_bx = $(this).parent().parent().parent();
        remove_query_args('afeb-login');
        remove_query_args('afeb-register');
        add_query_args('afeb-lostpassword', 'yes');
        frm_bx.find('.afeb-login-form').hide();
        frm_bx.find('.afeb-lostpassword-form').show();
    });
    $(document).on('click', '.afeb-password-visibility', function (e) {
        let input = $(this).parent().find('input');
        if (input.attr('type') == 'password') {
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            input.attr('type', 'text');
            $('input[name="afeb-register-confirmpassword"]').attr('type', 'text');
            $('input[name="afeb-resetpassword-confirm-new-password"]').attr('type', 'text');
        } else {
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            input.attr('type', 'password');
            $('input[name="afeb-register-confirmpassword"]').attr('type', 'password');
            $('input[name="afeb-resetpassword-confirm-new-password"]').attr('type', 'password');
        }
    });
    $(document).on('click', '.afeb-help-description', function (e) {
        $(this).parent().find('.afeb-help-description-text').slideToggle();
    });
});