jQuery(document).ready(function ($) {
	let body = $('body');
	body.on('click', '.afeb-alert-box .close-alert-box', function (e) {
		e.preventDefault();
		let alert = $(this).parent();
		if (body.hasClass('elementor-editor-active') || body.hasClass('elementor-editor-preview')) {
			alert.slideUp(200);
			setTimeout(function () {
				alert.slideDown(200);
			}, 2000);
		} else {
			alert.slideUp(200, function () {
				alert.remove();
			});
		}
	});
});