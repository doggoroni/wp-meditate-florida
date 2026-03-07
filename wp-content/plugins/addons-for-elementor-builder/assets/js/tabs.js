(function ($, elementor) {
	'use strict';
	let afeb_widgetTabs = function ($scope, $) {

		let init = function ($scope, $) {
			let tabs = $scope.find('.afeb-tabs');
			if (!tabs.length) return;

			let settings = tabs.data('settings');

			tabs.find('.afeb-tab-a').each(function () {
				let $this = $(this),
					id = 'afeb-' + Math.random().toString(36).substring(2, 9);

				$this.attr('data-tab', id);
				$this.next('div').attr('id', id);
			});

			// Convert tabs nav
			if (!tabs.find('.afeb-tabs-nav').length)
				tabs[tabs.hasClass('afeb-tabs-nav-after') ? 'append' : 'prepend']('<div class="afeb-tabs-nav afeb-clr"><div class="afeb-clr"></div></div>');

			tabs.find('.afeb-tabs-nav div').html('');
			tabs.find('.afeb-tab-a').each(function () {
				tabs.find('.afeb-tabs-nav div').prepend($(this).clone());
			});

			// Mobile dropdown.
			if (!tabs.find('> select').length) tabs.prepend('<select />');
			else tabs.find('> select').html('');

			tabs.find('.afeb-tabs-nav div a').each(function () {
				tabs.find('> select').append($('<option />').attr('value', $(this).data('tab')).html($(this).text()));
			});
			tabs.find('> select').on('change', function () {
				tabs.find('a[data-tab="' + this.value + '"]').trigger('click');
			});

			// onClick tabs nav
			tabs.on((tabs.hasClass('afeb-tabs-on-hover') ? 'mouseenter click' : 'click'), '.afeb-tab-a', function () {
				let en = $(this),
					id = en.data('tab'),
					par = en.closest('.afeb-tabs'),
					tab = $('#' + id, par);

				// Set tab active class
				en.addClass('afeb-active').siblings().removeClass('afeb-active');
				$('.afeb-tab', par).hide();
				tab.show();
			});

			// Active tab
			tabs.find('.afeb-tabs-nav a').removeClass('afeb-d-none afeb-active');
			tabs.find('.afeb-tabs-nav a:nth-child(' + settings.activeTab + ')').addClass('afeb-active').trigger('click');
		}

		init($scope, $);
	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/afeb_tabs.default', afeb_widgetTabs);
		elementorFrontend.hooks.addAction('frontend/element_ready/afebp_edd_tabs.default', afeb_widgetTabs);
	});

}(jQuery, window.elementorFrontend));