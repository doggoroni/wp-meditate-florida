jQuery(document).ready(function ($) {
	window.addEventListener('message', function (e) {
		if (e.origin != window.origin || e.source.location.href != window.parent.location.href) return;
		let data = e.data;
		let show_preview_action = (typeof data.afeb_preloader_show_preview_action !== 'undefined' &&
			data.afeb_preloader_show_preview_action !== null) ?
			data.afeb_preloader_show_preview_action : '';

		let loader = (typeof data.afeb_preloader_loader !== 'undefined' &&
			data.afeb_preloader_loader !== null) ?
			data.afeb_preloader_loader : 'none';

		let loader_animation = (typeof data.afeb_preloader_loader_animation !== 'undefined' &&
			data.afeb_preloader_loader_animation !== null) ?
			data.afeb_preloader_loader_animation : 'modern';

		let entrance_animation = (typeof data.afeb_preloader_entrance_animation !== 'undefined' &&
			data.afeb_preloader_entrance_animation !== null) ?
			data.afeb_preloader_entrance_animation : 'none';

		let exit_animation = (typeof data.afeb_preloader_exit_animation !== 'undefined' &&
			data.afeb_preloader_exit_animation !== null) ?
			data.afeb_preloader_exit_animation : 'none';

		let animation_duration = (typeof data.afeb_preloader_animation_duration !== 'undefined' &&
			typeof data.afeb_preloader_animation_duration === 'object' &&
			data.afeb_preloader_animation_duration !== null) ?
			data.afeb_preloader_animation_duration : 500;

		animation_duration = (typeof animation_duration.size !== 'undefined') ? animation_duration.size : 300;

		if (loader !== 'none') {
			let preloader = $('.afeb-preloader');
			let append = '<div class="afeb-preloader">';
			let loader_animation_content = '';

			if (preloader.length > 0) {
				$('.afeb-preloader').remove();
			}

			if (loader == 'animation') {
				switch (loader_animation) {
					case 'modern':
						loader_animation_content += '<div class="afeb-modern-loader afeb-loader">';
						for (let i = 0; i < 5; i++)
							loader_animation_content += '<div class="afeb-bar"></div>';

						loader_animation_content += '</div>';
						break;
					case 'whirlwind':
						loader_animation_content += '<div class="afeb-loader"><div class="afeb-whirlwind-loader"></div></div>';
						break;
					case 'speedster':
						loader_animation_content += '<div class="afeb-speedster-loader afeb-loader">';
						for (let i = 0; i < 4; i++)
							loader_animation_content += '<div class="afeb-bar"></div>';

						loader_animation_content += '</div>';
						break;
					case 'worm-crawl':
						loader_animation_content += '<div class="afeb-worm-crawl-loader afeb-loader">';
						for (let i = 0; i < 12; i++)
							loader_animation_content += '<div class="afeb-circle"></div>';

						loader_animation_content += '</div>';
						break;
				}
			}

			append += loader_animation_content + '</div>';
			$('body').append(append);

			switch (entrance_animation) {
				case 'fade-in':
					jQuery('.afeb-preloader').css({
						'opacity': 1,
						'z-index': 9999999
					}).hide().fadeIn(animation_duration);
					break;
				case 'slide-down':
					jQuery('.afeb-preloader').css({
						'opacity': 1,
						'z-index': 9999999
					}).hide().slideDown(animation_duration);
					break;
				default:
					jQuery('.afeb-preloader').css({
						'opacity': 1,
						'z-index': 9999999
					}).show();
					break
			}

			if (show_preview_action !== 'yes') {
				setTimeout(function () {
					switch (exit_animation) {
						case 'fade-out':
							jQuery('.afeb-preloader').fadeOut(animation_duration, function () {
								jQuery('.afeb-preloader').css({
									'opacity': 0,
									'z-index': -9999999,
								});
								jQuery('.afeb-preloader').css({
									'opacity': 1,
									'z-index': 9999999,
								}).show();
							});
							break;
						case 'slide-up':
							jQuery('.afeb-preloader').slideUp(animation_duration, function () {
								jQuery('.afeb-preloader').css({
									'opacity': 0,
									'z-index': -9999999,
								});
								jQuery('.afeb-preloader').css({
									'opacity': 1,
									'z-index': 9999999,
								}).show();
							});
							break;
					}
				}, 1500);
			}
		} else {
			$('.afeb-preloader').remove();
		}
	});
});