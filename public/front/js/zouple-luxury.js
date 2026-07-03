(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    function addBackToTop() {
        if (document.querySelector('.zouple-back-to-top')) {
            return;
        }

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'zouple-back-to-top';
        button.setAttribute('aria-label', 'Back to top');
        button.innerHTML = '<i class="fa fa-angle-up" aria-hidden="true"></i>';
        document.body.appendChild(button);

        function toggle() {
            if (window.pageYOffset > 420) {
                button.classList.add('is-visible');
            } else {
                button.classList.remove('is-visible');
            }
        }

        button.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
        });

        window.addEventListener('scroll', toggle, { passive: true });
        toggle();
    }

    function prepareImages() {
        var images = document.querySelectorAll('img');
        for (var i = 0; i < images.length; i++) {
            var img = images[i];

            if (!img.getAttribute('alt')) {
                img.setAttribute('alt', 'Zouple signage');
            }

            if (!img.hasAttribute('decoding')) {
                img.setAttribute('decoding', 'async');
            }

            if (i > 2 && !img.closest('.fixedTopMenu') && !img.closest('.carousel') && !img.closest('.owl-carousel') && !img.hasAttribute('loading')) {
                img.setAttribute('loading', 'lazy');
            }
        }
    }

    function prepareReveal() {
        if (prefersReducedMotion || window.innerWidth < 992) {
            document.querySelectorAll('[data-aos]').forEach(function (element) {
                element.removeAttribute('data-aos');
                element.removeAttribute('data-aos-duration');
                element.removeAttribute('data-aos-once');
                element.removeAttribute('data-aos-offset');
            });
            return;
        }

        var selectors = [
            '.productColumn',
            '.productBox',
            '.featureProduct',
            '.newArrivals',
            '.quickLinks',
            '.paymentThro',
            '.zouple-footer-brand',
            '.zouple-footer-newsletter'
        ];

        selectors.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (element) {
                if (!element.hasAttribute('data-aos') && !element.closest('.modal')) {
                    element.setAttribute('data-aos', 'fade-up');
                    element.setAttribute('data-aos-duration', '650');
                    element.setAttribute('data-aos-once', 'true');
                }
            });
        });

        if (window.AOS && typeof window.AOS.init === 'function') {
            window.AOS.init({
                duration: 650,
                easing: 'ease-out-cubic',
                once: true,
                offset: 40,
                disable: function () {
                    return window.innerWidth < 992;
                }
            });
        }
    }

    ready(function () {
        document.documentElement.classList.add('zouple-luxury-ready');
        prepareImages();
        prepareReveal();
        addBackToTop();
    });
})();
