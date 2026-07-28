(function () {
    'use strict';

    var header = document.querySelector('[data-home-header]');

    if (!header) {
        return;
    }

    var transparentClass = 'site-header--transparent';
    var scrolledClass = 'site-header--scrolled';
    var ticking = false;

    function updateHeader() {
        var isAtTop = window.scrollY <= 8;

        header.classList.toggle(transparentClass, isAtTop);
        header.classList.toggle(scrolledClass, !isAtTop);
        ticking = false;
    }

    function requestUpdate() {
        if (ticking) {
            return;
        }

        ticking = true;
        window.requestAnimationFrame(updateHeader);
    }

    updateHeader();
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate, { passive: true });
})();
