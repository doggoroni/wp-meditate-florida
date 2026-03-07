jQuery(document).ready(function($) {
    // Helper: safely parse message text
    function parseMessage(message) {
        if (!message) return 'No response message received.';
        if (typeof message === 'string') return message;
        if (Array.isArray(message)) return message.join('<br>');
        if (typeof message === 'object') {
            if (message.message) return parseMessage(message.message);
            if (message.errors) return parseMessage(message.errors);
            return Object.values(message).map(parseMessage).join('<br>');
        }
        return 'Unexpected response format.';
    }

    // Client-side validation
    function validateUploadField(field) {
        const rawMaxSize = field.data('max-size');
        const fallbackMax = field.data('maxFileSize');
        let maxSizeMB = parseFloat(rawMaxSize ?? fallbackMax);
        maxSizeMB = Number.isFinite(maxSizeMB) && maxSizeMB > 0 ? maxSizeMB : null;
        const maxSize = maxSizeMB ? maxSizeMB * 1024 * 1024 : Number.POSITIVE_INFINITY;

        const dataAllowed = field.data('allowed-types');
        const acceptAttr = field.attr('accept');
        let allowedTypes = [];

        if (typeof dataAllowed === 'string' && dataAllowed.trim()) {
            allowedTypes = dataAllowed.split(',').map(type => type.trim().toLowerCase()).filter(Boolean);
        } else if (typeof acceptAttr === 'string' && acceptAttr.trim()) {
            allowedTypes = acceptAttr.split(',')
                .map(type => type.replace('.', '').trim().toLowerCase())
                .filter(Boolean);
        }

        let valid = true;
        let errors = [];

        $.each(field[0].files, function(i, file) {
            // Check file size
            if (Number.isFinite(maxSize) && file.size > maxSize) {
                errors.push(`File "${file.name}" exceeds maximum size.`);
                valid = false;
            }

            // Check file type
            const extension = file.name.split('.').pop().toLowerCase();
            if (allowedTypes.length && !allowedTypes.includes(extension)) {
                errors.push(`File "${file.name}" has invalid type.`);
                valid = false;
            }
        });

        return { valid, errors };
    }

    // Form submission handler
    $(document).on('submit', '.afeb-form', function(e) {
        const $form = $(this);

        if ($form.data('ajax-form') !== 'yes') return true;

        e.preventDefault();

        const $submitBtn = $form.find('[type="submit"]');
        const $message = $form.find('.afeb-form-message');

        // Reset UI
        $form.find('.afeb-field-error').remove();
        $form.find('.has-error').removeClass('has-error');
        $message.hide().removeClass('success error');
        $submitBtn.prop('disabled', true).addClass('loading');

        // Validate uploads first
        let hasErrors = false;
        $form.find('input[type="file"]').each(function() {
            const $field = $(this).closest('.elementor-field-group');
            const validation = validateUploadField($(this));

            if (!validation.valid) {
                $field.addClass('has-error');
                validation.errors.forEach(error => {
                    $field.append(`<span class="afeb-field-error">${error}</span>`);
                });
                hasErrors = true;
            }
        });

        if (hasErrors) {
            $submitBtn.prop('disabled', false).removeClass('loading');
            return false;
        }

        // Prepare form data
        const formData = new FormData(this);
        formData.set('afeb_form_submit', '1');
        formData.set('action', 'afeb_form_submit');

        // Add field order
        const fieldOrder = [];
        $form.find('[name^="form_fields"]').each(function() {
            const matches = $(this).attr('name').match(/\[(.*?)\]/);
            if (matches && matches[1]) fieldOrder.push(matches[1]);
        });
        formData.append('field_order', JSON.stringify(fieldOrder));

        // AJAX submission
        $.ajax({
            url: window.afeb_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
        .done(function(response) {
            const success = response && response.success;
            const data = response && response.data ? response.data : {};
            const messageText = parseMessage(data.message || (success ? 'Form submitted successfully.' : 'Submission failed.'));

            $message
            .removeClass('success error')
            .addClass(success ? 'success' : 'error')
            .html(messageText)
            .show();

            if (!success && data.errors) {
                $.each(data.errors, function(fieldId, error) {
                    const $field = $form.find(`[name*="[${fieldId}]"]`).closest('.elementor-field-group');
                    $field.addClass('has-error').append(`<span class="afeb-field-error">${parseMessage(error)}</span>`);
                });
            }

            if (success && data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .fail(function(xhr) {
            let msg = 'An error occurred. Please try again.';
            try {
                const res = JSON.parse(xhr.responseText);
                msg = parseMessage(res?.data?.message || res?.message || msg);
            } catch (e) {}
            $message.addClass('error').html(msg).show();
        })
        .always(function() {
            $submitBtn.prop('disabled', false).removeClass('loading');
        });
    });
});
