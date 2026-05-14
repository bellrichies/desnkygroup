(function () {
    'use strict';

    function track(eventName, parameters) {
        if (typeof window.gtag !== 'function') {
            return;
        }

        window.gtag('event', eventName, parameters || {});
    }

    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (form.matches('[data-ajax-form], form[action="/contact/submit"], form[action="/newsletter"]')) {
            track('generate_lead', {
                form_action: form.getAttribute('action') || window.location.pathname
            });
        }

        if (form.matches('form[action="/shop/checkout"]')) {
            track('begin_checkout', {});
        }
    }, true);

    document.addEventListener('click', function (event) {
        var target = event.target instanceof Element ? event.target.closest('a, button') : null;

        if (!target) {
            return;
        }

        if (target.matches('[href*="/shop/product/"], [data-product-card] a')) {
            track('select_item', {
                link_url: target.getAttribute('href') || ''
            });
        }

        if (target.matches('[href="/contact"], [href="/contact#contact-form"]')) {
            track('contact_intent', {
                link_url: target.getAttribute('href') || ''
            });
        }
    });

    window.addEventListener('load', function () {
        if (!('PerformanceObserver' in window)) {
            return;
        }

        try {
            new PerformanceObserver(function (entryList) {
                var entries = entryList.getEntries();
                var lastEntry = entries[entries.length - 1];

                if (lastEntry) {
                    track('web_vitals_lcp', {
                        value: Math.round(lastEntry.startTime),
                        event_category: 'Web Vitals',
                        non_interaction: true
                    });
                }
            }).observe({ type: 'largest-contentful-paint', buffered: true });

            var cls = 0;
            new PerformanceObserver(function (entryList) {
                entryList.getEntries().forEach(function (entry) {
                    if (!entry.hadRecentInput) {
                        cls += entry.value;
                    }
                });

                track('web_vitals_cls', {
                    value: Number(cls.toFixed(4)),
                    event_category: 'Web Vitals',
                    non_interaction: true
                });
            }).observe({ type: 'layout-shift', buffered: true });

            new PerformanceObserver(function (entryList) {
                entryList.getEntries().forEach(function (entry) {
                    track('web_vitals_fid', {
                        value: Math.round(entry.processingStart - entry.startTime),
                        event_category: 'Web Vitals',
                        non_interaction: true
                    });
                });
            }).observe({ type: 'first-input', buffered: true });
        } catch (error) {
            // Unsupported metric observers should not affect the page.
        }
    });
})();
