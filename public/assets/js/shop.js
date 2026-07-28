/**
 * shop.js - ecommerce interactivity (progressive enhancement).
 *
 * - Product search/sort with result count, loading and empty states.
 * - Category navigation loading feedback.
 * - AJAX add-to-cart with cart badge + toast updates.
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

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function toast(message, type) {
        if (typeof window.createToastNotification === 'function') {
            window.createToastNotification(message, type);
        }
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
                button.dataset.originalText = button.dataset.originalText || button.textContent;
                button.textContent = 'Adding...';
            }

            fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: new FormData(form),
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        return { ok: response.ok, payload: payload };
                    });
                })
                .then(function (result) {
                    if (!result.ok || result.payload.success === false) {
                        toast(result.payload.message || 'Could not add to cart.', 'error');
                        return;
                    }
                    if (typeof result.payload.cart_count !== 'undefined') {
                        updateCartCount(result.payload.cart_count);
                    }
                    toast(result.payload.message || 'Added to cart.', 'success');
                })
                .catch(function () {
                    toast('Network error. Please try again.', 'error');
                })
                .finally(function () {
                    if (button) {
                        button.disabled = false;
                        button.removeAttribute('aria-busy');
                        button.textContent = button.dataset.originalText || 'Add to cart';
                    }
                });
        });
    }

    function bindProductBrowse(page) {
        var grid = page.querySelector('[data-shop-grid]');
        var cards = grid ? Array.prototype.slice.call(grid.querySelectorAll('[data-product-card]')) : [];
        var searches = Array.prototype.slice.call(page.querySelectorAll('[data-shop-search]'));
        var sort = page.querySelector('[data-shop-sort]');
        var loading = page.querySelector('[data-shop-loading]');
        var empty = page.querySelector('[data-shop-empty]');
        var count = page.querySelector('[data-shop-results-count]');
        var clearButtons = Array.prototype.slice.call(page.querySelectorAll('[data-shop-clear]'));
        var timer = null;

        function setLoading(isLoading) {
            if (!loading) {
                return;
            }
            loading.classList.toggle('hidden', !isLoading);
        }

        function currentSearch() {
            return searches.length ? searches[0].value.trim().toLowerCase() : '';
        }

        function syncSearches(value, source) {
            searches.forEach(function (input) {
                if (input !== source) {
                    input.value = value;
                }
            });
        }

        function updateCount(visible) {
            if (!count) {
                return;
            }
            var label = count.dataset.countLabel || '{count} products';
            count.textContent = label.replace('{count}', String(visible));
        }

        function updateClearButtons(term) {
            clearButtons.forEach(function (button) {
                button.classList.toggle('hidden', term === '');
            });
        }

        function applyFilters() {
            var term = currentSearch();
            var visible = 0;

            cards.forEach(function (card) {
                var text = card.textContent.toLowerCase();
                var match = term === '' || text.indexOf(term) !== -1;
                card.classList.toggle('hidden', !match);
                card.setAttribute('aria-hidden', match ? 'false' : 'true');
                if (match) {
                    visible += 1;
                }
            });

            if (grid) {
                grid.classList.toggle('hidden', visible === 0 && cards.length > 0);
            }
            if (empty) {
                empty.classList.toggle('hidden', !(visible === 0 && cards.length > 0));
            }

            updateCount(visible || cards.length);
            updateClearButtons(term);
        }

        function scheduleApply() {
            setLoading(true);
            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                applyFilters();
                setLoading(false);
            }, 160);
        }

        searches.forEach(function (input) {
            input.addEventListener('input', function () {
                syncSearches(input.value, input);
                scheduleApply();
            });
        });

        clearButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                syncSearches('', null);
                searches.forEach(function (input) {
                    input.value = '';
                });
                scheduleApply();
                if (searches[0]) {
                    searches[0].focus();
                }
            });
        });

        if (sort && grid) {
            sort.addEventListener('change', function () {
                setLoading(true);
                window.setTimeout(function () {
                    cards.sort(function (a, b) {
                        if (sort.value === 'price-low') {
                            return Number(a.dataset.price) - Number(b.dataset.price);
                        }
                        if (sort.value === 'price-high') {
                            return Number(b.dataset.price) - Number(a.dataset.price);
                        }
                        return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                    }).forEach(function (card) {
                        grid.appendChild(card);
                    });
                    applyFilters();
                    setLoading(false);
                }, 120);
            });
        }

        page.querySelectorAll('[data-shop-category-link]').forEach(function (link) {
            link.addEventListener('click', function () {
                setLoading(true);
            });
        });

        applyFilters();
    }

    function init() {
        document.querySelectorAll('form[data-add-to-cart]').forEach(bindAddToCart);
        document.querySelectorAll('[data-shop-page]').forEach(bindProductBrowse);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
