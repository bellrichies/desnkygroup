(function () {
    'use strict';

    function serializeForm(form) {
        return new FormData(form);
    }

    function setFormLoading(form, isLoading) {
        form.classList.toggle('is-submitting', isLoading);

        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (button) {
            button.disabled = isLoading;
            button.dataset.originalText = button.dataset.originalText || button.textContent;
            button.textContent = isLoading ? 'Sending...' : button.dataset.originalText;
        });
    }

    function clearValidationErrors(form) {
        form.querySelectorAll('[data-field-error]').forEach(function (node) {
            node.remove();
        });

        form.querySelectorAll('[aria-invalid="true"]').forEach(function (field) {
            field.removeAttribute('aria-invalid');
        });
    }

    function showValidationErrors(form, errors) {
        Object.keys(errors || {}).forEach(function (fieldName) {
            var field = form.querySelector('[name="' + CSS.escape(fieldName) + '"]');
            var message = Array.isArray(errors[fieldName]) ? errors[fieldName][0] : errors[fieldName];

            if (!field || !message) {
                return;
            }

            field.setAttribute('aria-invalid', 'true');

            var error = document.createElement('p');
            error.className = 'form-error';
            error.dataset.fieldError = fieldName;
            error.textContent = message;

            field.insertAdjacentElement('afterend', error);
        });
    }

    function validateRequiredFields(form) {
        var valid = true;

        clearValidationErrors(form);

        form.querySelectorAll('[required]').forEach(function (field) {
            var fieldIsValid = true;

            if (field.type === 'checkbox' && !field.checked) {
                fieldIsValid = false;
            } else if (field.type !== 'checkbox' && !field.value.trim()) {
                fieldIsValid = false;
            }

            if (!fieldIsValid && !form.querySelector('[data-field-error="' + field.name + '"]')) {
                valid = false;
                showValidationErrors(form, Object.fromEntries([[field.name, 'This field is required.']]));
            }
        });

        return valid;
    }

    window.createToastNotification = function (message, type, duration) {
        type = type || 'success';
        duration = duration || 3000;

        var container = document.querySelector('[data-toast-container]');

        if (!container) {
            container = document.createElement('div');
            container.dataset.toastContainer = 'true';
            container.className = 'fixed right-4 top-24 z-[70] grid w-[min(24rem,calc(100vw-2rem))] gap-3';
            document.body.appendChild(container);
        }

        var palette = {
            success: 'border-green-200 bg-green-50 text-green-800',
            error: 'border-red-200 bg-red-50 text-red-800',
            warning: 'border-yellow-200 bg-yellow-50 text-yellow-900',
            info: 'border-blue-200 bg-blue-50 text-blue-800',
        };

        var toast = document.createElement('div');
        toast.className = 'rounded-md border px-4 py-3 text-sm shadow-card ' + (palette[type] || palette.success);
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.textContent = message;

        container.appendChild(toast);

        window.setTimeout(function () {
            toast.remove();
        }, duration);
    };

    window.showLoadingSpinner = function (target) {
        target = target || document.body;

        if (target.querySelector('[data-loading-spinner]')) {
            return;
        }

        var overlay = document.createElement('div');
        overlay.dataset.loadingSpinner = 'true';
        overlay.className = 'absolute inset-0 z-40 flex items-center justify-center bg-white/70';
        overlay.innerHTML = '<span class="h-8 w-8 animate-spin rounded-full border-4 border-desnky-blue border-t-transparent"></span>';

        if (getComputedStyle(target).position === 'static') {
            target.style.position = 'relative';
        }

        target.appendChild(overlay);
    };

    window.hideLoadingSpinner = function (target) {
        target = target || document.body;
        var spinner = target.querySelector('[data-loading-spinner]');

        if (spinner) {
            spinner.remove();
        }
    };

    function bindForm(form, options) {
        if (!form || form.dataset.ajaxBound === 'true') {
            return;
        }
        form.dataset.ajaxBound = 'true';

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (!validateRequiredFields(form)) {
                window.createToastNotification('Please complete the required fields.', 'warning');
                return;
            }

            var endpoint = options.endpoint || form.action || window.location.href;
            var method = options.method || form.method || 'POST';

            setFormLoading(form, true);
            window.showLoadingSpinner(form);

            fetch(endpoint, {
                method: method.toUpperCase(),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: serializeForm(form),
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        return { ok: response.ok, payload: payload };
                    });
                })
                .then(function (result) {
                    clearValidationErrors(form);

                    if (!result.ok || result.payload.success === false) {
                        showValidationErrors(form, result.payload.errors || {});
                        window.createToastNotification(
                            result.payload.message || 'Please review the form and try again.',
                            'error'
                        );

                        if (typeof options.onError === 'function') {
                            options.onError(result.payload);
                        }

                        return;
                    }

                    window.createToastNotification(result.payload.message || 'Submitted successfully.', 'success');

                    if (options.resetOnSuccess !== false) {
                        form.reset();
                    }

                    if (typeof options.onSuccess === 'function') {
                        options.onSuccess(result.payload);
                    }
                })
                .catch(function (error) {
                    window.createToastNotification('Network error. Please try again.', 'error');

                    if (typeof options.onError === 'function') {
                        options.onError(error);
                    }
                })
                .finally(function () {
                    setFormLoading(form, false);
                    window.hideLoadingSpinner(form);
                });
        });
    }

    // Public API: bind every form matching the selector (idempotent).
    window.submitForm = function (formSelector, options) {
        options = options || {};
        document.querySelectorAll(formSelector).forEach(function (form) {
            bindForm(form, options);
        });
    };

    // Auto-wire newsletter forms (footer appears on every page). Contact pages
    // wire their own form explicitly with service-interest handling.
    document.addEventListener('DOMContentLoaded', function () {
        window.submitForm('[data-newsletter-form]', { resetOnSuccess: true });
    });
}());
