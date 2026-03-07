(function ($, elementor) {
	'use strict';

	let afeb_widget_advanced_menus = function ($scope, $) {

		let init = function ($scope, $) {

			let advanced_menu = $scope.find('.afeb-advanced-menu');
			if (!advanced_menu.length) return;

			$scope.find('.afeb-advanced-menu-main,' +
				'.afeb-advanced-menu-toggle,' +
				'.afeb-advanced-menu-toggle-dropdown').css({
					'opacity': '1',
					'position': 'unset',
					'z-index': 'unset'
				});

			advanced_menu.each(function () {
				let classes = ($(this).attr('class').replaceAll('afeb-advanced-menu', '')) + '';
				let indicators = '';

				if (classes.trim() !== '') {
					classes = classes.replaceAll('_', ' ');
					indicators = '<i class="' + classes + '"></i>';
				}

				$(this).smartmenus({
					markCurrentItem: true,
					subIndicators: true,
					subIndicatorsPos: 'append',
					subIndicatorsText: indicators,
					subMenusMaxWidth: '1000px',
				});
			});

			$(document).off('click', '.afeb-advanced-menu-toggle').on('click', '.afeb-advanced-menu-toggle', function (e) {
				if ($(this).hasClass('afeb-active-toggle')) {
					$(this).removeClass('afeb-active-toggle');
				} else {
					let object = $(this).parent().parent();
					let fit_to_section = object.hasClass('afeb-advanced-menu-fit-to-section') ? true : false;
					let full_width = object.hasClass('afeb-advanced-menu-full-width') ? true : false;
					let dropdown = object.find('.afeb-advanced-menu-container.afeb-advanced-menu-dropdown');

					if (fit_to_section) {
						let container_offset = object.offset().left;
						let container_width = object.width();
						let top = $(this).outerHeight();

						dropdown.attr('style', 'width: ' + container_width + 'px !important;left:' + (container_offset - dropdown.parent().offset().left) + 'px !important;top:' + top + 'px;');

					} else if (full_width) {
						let width = $(window).width();
						let body = $(document.body);
						let body_offset = body.offset().left;
						let top = $(this).outerHeight();

						dropdown.attr('style', 'width: ' + width + 'px !important;left:' + (body_offset - dropdown.parent().offset().left) + 'px !important;top:' + top + 'px;');
					} else {
						dropdown.attr('style', '');
					}

					$(this).addClass('afeb-active-toggle');
				}
			});
		}

		init($scope, $);
	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/afeb_advanced_menus.default', afeb_widget_advanced_menus);
	});

}(jQuery, window.elementorFrontend));