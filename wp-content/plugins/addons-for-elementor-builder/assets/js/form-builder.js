jQuery(function ($) {
    'use strict';

    if (typeof elementor !== 'undefined' && elementor.hooks) {
        elementor.hooks.addAction('panel/open_editor/widget/afeb_form_builder', function (panel, model, view) {

            const customIdFieldSelector = 'input[data-setting="field_id"]';
            const shortcodeFieldSelector = '.afeb-form-field-shortcode';

            // When a section in the Elementor panel is activated, update field options
            elementor.channels.editor.on('section:activated', function () {
                updateDynamicOptions(view);
            });

            // Listen for changes in form fields and update options accordingly
            view.listenTo(view.model.get('settings').get('form_fields'), 'add change remove', function () {
                updateDynamicOptions(view);
            });

            /**
             * Updates field IDs and associated shortcode values
             */
            function updateDynamicOptions(view) {
                const formFields = view.model.get('settings').get('form_fields');
                const options = { 'none': 'None' };
                let prevFieldId = null;

                formFields.each(function (field) {
                    const fieldLabel = field.get('field_label');
                    const fieldId = field.get('field_id');
                    const fieldUid = field.attributes._id;

                    // Ensure field_id is unique and not empty
                    if (!fieldId || prevFieldId === fieldId) {
                        setFieldIdAndShortcode(fieldUid);
                        field.attributes.field_id = fieldUid;
                    }

                    prevFieldId = fieldId;
                    options[fieldId] = fieldLabel;
                });
            }

            /**
             * Sets the field_id input and the shortcode input for a field
             */
            function setFieldIdAndShortcode(fieldUid) {
                const $fieldGroup = $('#elementor-panel')
                    .find(`:input[value="${fieldUid}"]`)
                    .closest('.elementor-repeater-fields');

                if ($fieldGroup.length) {
                    $fieldGroup.find(':input[data-setting="field_id"]').val(fieldUid);
                    $fieldGroup.find('.afeb-form-field-shortcode').val(`[id="${fieldUid}"]`);
                }
            }

            // Automatically update shortcode when field_id is changed
            $('#elementor-panel').on('input', customIdFieldSelector, function () {
                const newFieldId = $(this).val();
                const $shortcodeField = $(this).closest('.elementor-repeater-fields').find(shortcodeFieldSelector);

                if ($shortcodeField.length) {
                    const currentShortcode = $shortcodeField.val() || '';
                    const match = currentShortcode.match(/\[id="[^"]*"\]/);
                    const updatedShortcode = match
                        ? currentShortcode.replace(match[0], `[id="${newFieldId}"]`)
                        : `[id="${newFieldId}"]`;

                    $shortcodeField.val(updatedShortcode);
                }
            });
        });
    }
});

(function ($, elementor) {
    'use strict';

    const afebWidgetFormBuilder = function ($scope) {

        const init = function () {
            $scope.find('.afeb-form-field-button').on('click', function (e) {
                e.preventDefault();

                const radioNames = [];
                const uncheckedGroups = [];

                // Remove any previous hidden radio inputs
                $scope.find('.afeb-form-field-option-radio-hidden').remove();

                // Collect all radio group names
                $scope.find('input[type="radio"]').each(function () {
                    const name = $(this).attr('name');
                    if (!radioNames.includes(name)) {
                        radioNames.push(name);
                    }
                });

                // Detect radio groups that have no checked option
                $.each(radioNames, function (i, name) {
                    if ($scope.find(`input[type="radio"][name="${name}"]:checked`).length === 0) {
                        uncheckedGroups.push(name);
                    }
                });

                // Add hidden inputs for unchecked radio groups
                $.each(uncheckedGroups, function (i, name) {
                    const parent = $scope.find(`input[type="radio"][name="${name}"]`).closest('.afeb-field-sub-group');
                    parent.append(`<input class="afeb-form-field-option-radio-hidden" type="hidden" name="${name}">`);
                });

                // Remove hidden inputs for checked checkboxes
                $scope.find('input[type="checkbox"]').each(function () {
                    if ($(this).is(':checked')) {
                        $(this).parent().find('input[type="hidden"]').remove();
                    }
                });

                // Validate and submit the form if valid (and not in editor mode)
                if (window.afeb_validate_form($scope) && !document.body.classList.contains('elementor-editor-active')) {
                    $(this).closest('form').trigger('submit');
                }
            });
        };

        /**
         * Form validation logic
         */
        window.afeb_validate_form = function ($scope) {
            let hasError = false;
            let $fieldWrap = $scope.find('.afeb-form-fields-wrap > div.afeb-step-tab:not(.afeb-step-tab-hidden)');

            if ($fieldWrap.length === 0) {
                $fieldWrap = $scope.find('.afeb-form-fields-wrap');
            }

            $fieldWrap.find('input, select, textarea').each(function () {
                const $input = $(this);
                const type = $input.attr('type');
                const value = $input.val().trim();
                const isRequired = $input.closest('.afeb-field-group')
                    .find('.afeb-form-field, .afeb-form-field-textual')
                    .attr('required') === 'required';

                if (!hasError) {
                    if (value === '' && isRequired) {
                        const wrapper = $input.prop('tagName') === 'SELECT' ? '.afeb-select-wrap' : '.afeb-field-group';
                        const message = $scope.find('input[name="empty_error_message"]').val();
                        $input.closest(wrapper).append(`<p class="afeb-submit-error afeb-required-error">${message}</p>`);
                        hasError = true;
                    } else if (value !== '' && type) {
                        $scope.find('.afeb-submit-error').remove();
                        const $fieldGroup = $input.closest('.afeb-field-group');
                        const message = $fieldGroup.find('.afeb-form-error-message').val();

                        switch (type) {
                            case 'email':
                                if (!isValidEmail(value)) {
                                    $fieldGroup.append(`<p class="afeb-submit-error afeb-email-error">${message}</p>`);
                                    hasError = true;
                                }
                                break;
                            case 'url':
                                if (!isValidURL(value)) {
                                    $fieldGroup.append(`<p class="afeb-submit-error afeb-url-error">${message}</p>`);
                                    hasError = true;
                                }
                                break;
                            case 'number':
                            case 'tel':
                                if (!isValidNumber(value)) {
                                    $fieldGroup.append(`<p class="afeb-submit-error afeb-${type}-error">${message}</p>`);
                                    hasError = true;
                                }
                                break;
                            case 'date':
                                if (!isValidDate(value)) {
                                    $fieldGroup.append(`<p class="afeb-submit-error afeb-date-error">${message}</p>`);
                                    hasError = true;
                                }
                                break;
                            case 'time':
                                if (!isValidTime(value)) {
                                    $fieldGroup.append(`<p class="afeb-submit-error afeb-time-error">${message}</p>`);
                                    hasError = true;
                                }
                                break;
                        }
                    }
                }
            });

            return !hasError;
        };

        // Validation helper functions

        const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

        const isValidURL = (url) => {
            try {
                return Boolean(new URL(url));
            } catch {
                return false;
            }
        };

        const isValidNumber = (num) => /^\d+$/.test(num.trim().replace(/[\s\-\(\)]/g, '').replace(/^\+/, ''));

        const isValidDate = (dateStr) => {
            const date = new Date(dateStr);
            return !isNaN(date.getTime());
        };

        const isValidTime = (timeStr) => {
            const regex = /^(?:(?:0?[0-9]|1[0-2]):[0-5][0-9](?::[0-5][0-9])?\s?(?:AM|PM)?)|(?:[01]?[0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/i;
            return regex.test(timeStr);
        };

        // Initialize form builder functionality
        init();
    };

    // Hook into Elementor frontend rendering
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_form_builder.default', afebWidgetFormBuilder);
    });

})(jQuery, window.elementorFrontend);
