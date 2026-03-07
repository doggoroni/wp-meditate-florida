// Released under the MIT License
(function ($) {
    'use strict';

    /**
     * Elementor control panel helper that hides repeater add buttons once the
     * maximum supported items have been added for selected widgets.
     */
    $(document).ready(function () {
        var namespaces = ['elementor', 'wrapper', 'control', 'repeater', 'fields', 'button'];
        var baseSelector = '.' + namespaces[0] + '-' + namespaces[2] + '-';

        /**
         * Returns the jQuery object for a widget repeater button.
         *
         * @param {string} handle Short widget handle (e.g. `accrdn`).
         * @returns {jQuery}
         */
        var getButton = function (handle) {
            return $(baseSelector + handle).find('.' + namespaces[0] + '-' + namespaces[5] + '-' + namespaces[1] + ' > button');
        };

        /**
         * Hide or show the repeater button depending on the current item count.
         *
         * @param {string} handle Widget handle.
         * @param {number} limit Maximum allowed items.
         * @param {number} buttonIndex Index within the tracked button collection.
         */
        var toggleButton = function (handle, limit, buttonIndex) {
            var buttons = [
                getButton('accrdn'),
                getButton('fnctxt'),
                getButton('htspt'),
                getButton('prcbx'),
                getButton('tsl'),
                getButton('tllst')
            ];

            var $repeater = $(baseSelector + handle).find('.' + namespaces[0] + '-' + namespaces[3] + '-' + namespaces[4]);
            var threshold = Math.sqrt(limit);

            if ($repeater.length >= threshold) {
                buttons[buttonIndex].hide();
            } else {
                buttons[buttonIndex].show();
            }
        };

        setInterval(function () {
            toggleButton('accrdn', 36, 0);
            toggleButton('fnctxt', 9, 1);
            toggleButton('htspt', 16, 2);
            toggleButton('prcbx', 16, 3);
            toggleButton('tsl', 16, 4);
            toggleButton('tllst', 9, 5);
        }, 32);
    });
})(jQuery);
