(function() {
    'use strict';
    var THEME_KEY = 'cctv_theme';

    function applyTheme(theme) {
        document.getElementById('html-root').setAttribute('data-bs-theme', theme);
        var icon = document.getElementById('theme-icon');
        if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    window.toggleTheme = function() {
        var cur = localStorage.getItem(THEME_KEY) || 'light';
        var next = cur === 'light' ? 'dark' : 'light';
        localStorage.setItem(THEME_KEY, next);
        applyTheme(next);
    };

    function initReveal() {
        var els = document.querySelectorAll('.reveal');
        if (!els.length) return;
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        els.forEach(function(el) { io.observe(el); });
    }

    function initCounters() {
        var els = document.querySelectorAll('[data-count]');
        if (!els.length) return;
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (!e.isIntersecting) return;
                var el = e.target, target = parseInt(el.dataset.count), suffix = el.dataset.suffix || '', dur = 1800, start = null;
                function tick(ts) {
                    if (!start) start = ts;
                    var p = Math.min((ts - start) / dur, 1);
                    el.textContent = Math.floor(p * target) + suffix;
                    if (p < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
                io.unobserve(el);
            });
        }, { threshold: 0.4 });
        els.forEach(function(el) { io.observe(el); });
    }

    function initNavScroll() {
        var nav = document.querySelector('.site-nav');
        if (!nav) return;
        function check() { nav.classList.toggle('scrolled', window.scrollY > 30); }
        window.addEventListener('scroll', check, { passive: true });
        check();
    }

    function initActiveLink() {
        var secs = document.querySelectorAll('section[id]');
        var links = document.querySelectorAll('.nav-links a');
        if (!secs.length || !links.length) return;
        window.addEventListener('scroll', function() {
            var y = window.scrollY + 100;
            secs.forEach(function(s) {
                if (y >= s.offsetTop && y < s.offsetTop + s.offsetHeight) {
                    links.forEach(function(l) { l.classList.toggle('active', l.getAttribute('href') === '#' + s.id); });
                }
            });
        }, { passive: true });
    }

    function initBackTop() {
        var btn = document.getElementById('back-to-top');
        if (!btn) return;
        window.addEventListener('scroll', function() {
            btn.classList.toggle('visible', window.scrollY > 400);
        }, { passive: true });
        btn.addEventListener('click', function(e) { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
    }

    function initLightbox() {
        var modal = document.getElementById('galleryModal');
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function(e) {
            var src = e.relatedTarget.getAttribute('data-img');
            document.getElementById('galleryModalImg').src = src;
        });
    }

    function initMobileClose() {
        document.querySelectorAll('a[href^="#"]').forEach(function(a) {
            a.addEventListener('click', function() {
                var c = document.querySelector('.navbar-collapse.show');
                if (c) { var b = bootstrap.Collapse.getInstance(c); if (b) b.hide(); }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        applyTheme(localStorage.getItem(THEME_KEY) || 'light');
        initReveal();
        initCounters();
        initNavScroll();
        initActiveLink();
        initBackTop();
        initLightbox();
        initMobileClose();
    });
})();
