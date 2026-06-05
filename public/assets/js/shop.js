/**
 * shop.js — ecommerce interactivity (progressive enhancement).
 *
 *   • [data-add-to-cart]   AJAX add-to-cart that updates the header badge + toast.
 *   • Live cart-count badge synchronisation.
 *
 * Falls back to a normal form POST when JS is unavailable.
 */
(function () {
    'use strict';

    function updateCartCount(count) {
        var badge = document.querySelector('[data-cart-count]');
        if (!badge) {
            return;
        }
        badge.textContent = count;
        badge.classList.toggle('hidden', !(count > 0));
    }

    function bindAddToCart(form) {
        if (form.dataset.ajaxBound === 'true') {
            return;
        }
        form.dataset.ajaxBound = 'true';

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
            }

            fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        return { ok: response.ok, payload: payload };
                    });
                })
                .then(function (result) {
                    if (!result.ok || result.payload.success === false) {
                        window.createToastNotification(result.payload.message || 'Could not add to cart.', 'error');
                        return;
                    }
                    if (typeof result.payload.cart_count !== 'undefined') {
                        updateCartCount(result.payload.cart_count);
                    }
                    window.createToastNotification(result.payload.message || 'Added to cart.', 'success');
                })
                .catch(function () {
                    window.createToastNotification('Network error. Please try again.', 'error');
                })
                .finally(function () {
                    if (button) {
                        button.disabled = false;
                        button.removeAttribute('aria-busy');
                    }
                });
        });
    }

    function init() {
        document.querySelectorAll('form[data-add-to-cart]').forEach(bindAddToCart);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
