/**
 * media.js — progressive-enhancement visual behaviours.
 *
 *   • Scroll-reveal  ([data-reveal])         fade/translate sections into view.
 *   • Count-up       ([data-countup])        animate stat numerals on first view.
 *   • Lightbox       ([data-lightbox])       accessible image viewer (keyboard + focus trap).
 *
 * Every feature degrades gracefully: without JS the content is fully visible,
 * and prefers-reduced-motion short-circuits all animation.
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ----------------------------------------------------------------------
       Scroll-reveal
    ---------------------------------------------------------------------- */
    function initReveal() {
        var nodes = document.querySelectorAll('[data-reveal]');
        if (!nodes.length) {
            return;
        }

        if (reduceMotion || !('IntersectionObserver' in window)) {
            nodes.forEach(function (node) {
                node.classList.add('is-visible');
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -10% 0px', threshold: 0.1 });

        nodes.forEach(function (node) {
            observer.observe(node);
        });
    }

    /* ----------------------------------------------------------------------
       Count-up stats
    ---------------------------------------------------------------------- */
    function formatNumber(value, decimals) {
        return value.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    }

    function animateCount(el) {
        var target = parseFloat(el.getAttribute('data-countup'));
        if (isNaN(target)) {
            return;
        }

        var prefix = el.getAttribute('data-countup-prefix') || '';
        var suffix = el.getAttribute('data-countup-suffix') || '';
        var decimals = (String(target).split('.')[1] || '').length;

        if (reduceMotion) {
            el.textContent = prefix + formatNumber(target, decimals) + suffix;
            return;
        }

        var duration = 1600;
        var start = null;

        function step(timestamp) {
            if (start === null) {
                start = timestamp;
            }
            var progress = Math.min((timestamp - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            el.textContent = prefix + formatNumber(target * eased, decimals) + suffix;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        }

        window.requestAnimationFrame(step);
    }

    function initCountUp() {
        var nodes = document.querySelectorAll('[data-countup]');
        if (!nodes.length) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            nodes.forEach(animateCount);
            return;
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCount(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        nodes.forEach(function (node) {
            observer.observe(node);
        });
    }

    /* ----------------------------------------------------------------------
       Lightbox
    ---------------------------------------------------------------------- */
    function initLightbox() {
        var triggers = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
        if (!triggers.length) {
            return;
        }

        var overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-[60] hidden items-center justify-center bg-black/90 p-4';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-label', 'Image viewer');
        overlay.innerHTML =
            '<button type="button" data-lightbox-close aria-label="Close image viewer" ' +
            'class="absolute right-4 top-4 inline-flex h-11 w-11 items-center justify-center rounded-md text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">' +
            '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>' +
            '<button type="button" data-lightbox-prev aria-label="Previous image" class="absolute left-2 top-1/2 hidden -translate-y-1/2 rounded-md p-3 text-white hover:bg-white/10 sm:block focus:outline-none focus-visible:ring-2 focus-visible:ring-white">' +
            '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>' +
            '<img data-lightbox-image alt="" class="max-h-[85vh] max-w-full rounded-md object-contain">' +
            '<button type="button" data-lightbox-next aria-label="Next image" class="absolute right-2 top-1/2 hidden -translate-y-1/2 rounded-md p-3 text-white hover:bg-white/10 sm:block focus:outline-none focus-visible:ring-2 focus-visible:ring-white">' +
            '<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>';
        document.body.appendChild(overlay);

        var image = overlay.querySelector('[data-lightbox-image]');
        var closeBtn = overlay.querySelector('[data-lightbox-close]');
        var prevBtn = overlay.querySelector('[data-lightbox-prev]');
        var nextBtn = overlay.querySelector('[data-lightbox-next]');
        var current = 0;
        var lastFocused = null;

        function show(index) {
            current = (index + triggers.length) % triggers.length;
            var trigger = triggers[current];
            var src = trigger.getAttribute('data-lightbox') || trigger.getAttribute('href') ||
                (trigger.querySelector('img') && trigger.querySelector('img').src);
            var alt = trigger.getAttribute('data-lightbox-alt') ||
                (trigger.querySelector('img') && trigger.querySelector('img').alt) || '';
            image.src = src;
            image.alt = alt;
            var multiple = triggers.length > 1;
            prevBtn.classList.toggle('sm:block', multiple);
            nextBtn.classList.toggle('sm:block', multiple);
        }

        function open(index) {
            lastFocused = document.activeElement;
            show(index);
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            document.body.style.overflow = 'hidden';
            closeBtn.focus();
            document.addEventListener('keydown', onKey);
        }

        function close() {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.body.style.overflow = '';
            document.removeEventListener('keydown', onKey);
            if (lastFocused) {
                lastFocused.focus();
            }
        }

        function onKey(event) {
            if (event.key === 'Escape') {
                close();
            } else if (event.key === 'ArrowLeft') {
                show(current - 1);
            } else if (event.key === 'ArrowRight') {
                show(current + 1);
            } else if (event.key === 'Tab') {
                // Simple focus trap inside the overlay.
                event.preventDefault();
            }
        }

        triggers.forEach(function (trigger, index) {
            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                open(index);
            });
        });

        closeBtn.addEventListener('click', close);
        prevBtn.addEventListener('click', function () { show(current - 1); });
        nextBtn.addEventListener('click', function () { show(current + 1); });
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                close();
            }
        });
    }

    function init() {
        initReveal();
        initCountUp();
        initLightbox();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
