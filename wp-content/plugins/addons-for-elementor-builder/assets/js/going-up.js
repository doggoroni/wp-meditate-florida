jQuery(document).ready(function ($) {
	let svg = '<svg class="e-font-icon-svg e-fas-angle-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path d="M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z"></path></svg>';
	let spinner_svg = '<svg class="e-font-icon-svg e-fas-spinner" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">' +
		'<path d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"></path>' +
		'</svg>';
	let gup = $(".afeb-gotop-btn");
	window.addEventListener('message', function (e) {
		if (e.origin != window.origin || e.source.location.href != window.parent.location.href) return;
		let data = e.data;
		let gup_icon = (typeof data.afeb_gup_ic !== 'undefined' &&
			typeof data.afeb_gup_ic === 'object' && data.afeb_gup_ic !== null) ? data.afeb_gup_ic : false;
		let gup_icon_value = (typeof gup_icon.value !== 'undefined') ? gup_icon.value : '';
		let gup_title_show = typeof data.afeb_gup_ttl_sh !== 'undefined' ? data.afeb_gup_ttl_sh : false;
		let gup_title = typeof data.afeb_gup_ttl !== 'undefined' ? data.afeb_gup_ttl : '';
		let gup = $('div.afeb-gotop');
		let gup_btn = gup.find('.afeb-gotop-btn');
		let gup_btn_icon = gup_btn.find('.afeb-gotop-ic');
		let gup_btn_title = gup_btn.find('.afeb-gotop-title');

		if (gup_icon !== false && gup_icon_value != '') {
			if (gup_btn_icon.length == 0) gup_btn.prepend('<span class="afeb-gotop-ic afeb-gotop-spin-anim">' + spinner_svg + '</span>');
			else gup_btn_icon.addClass('afeb-gotop-spin-anim').html(spinner_svg);
			gup_btn_icon = gup_btn.find('.afeb-gotop-ic');
			$.ajax({
				url: AFEB.ajax_url, type: "POST",
				data: { action: "afeb_gup_ext_render_icon", nonce: AFEB.nonce, data: gup_icon },
				success: function (res) {
					if (res.success)
						if (gup_btn_icon.length == 0) gup_btn.prepend('<span class="afeb-gotop-ic">' + res.data + '</span>');
						else gup_btn_icon.removeClass('afeb-gotop-spin-anim').html(res.data);
				},
				error: function () {
					if (gup_btn_icon.length == 0) gup_btn.prepend('<span class="afeb-gotop-ic">' + svg + '</span>');
					else gup_btn_icon.removeClass('afeb-gotop-spin-anim').html(svg);
				},
			});
		} else {
			gup_btn.find('.afeb-gotop-ic').remove();
		}

		if (gup_title_show == 'yes' && gup_title.trim() != '') {
			if (!gup_btn.hasClass('show-title')) gup_btn.addClass('show-title');
			if (gup_btn_title.length == 0) gup_btn.append('<span class="afeb-gotop-title">' + gup_title + '</span>');
			else gup_btn_title.text(gup_title);
		} else {
			gup_btn.removeClass('show-title');
			gup_btn.find('.afeb-gotop-title').remove();
		}
	});

	$(window).scroll(function () {
		let top_pos = $(this).scrollTop();
		if (top_pos > 100) $(gup).css("opacity", "1");
		else $(gup).css("opacity", "0");
	});

	$(gup).click(function () {
		$('html, body').animate({
			scrollTop: 0
		}, 800);
		return false;

	});
});
