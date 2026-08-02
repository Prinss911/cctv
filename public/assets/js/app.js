(function() {
    'use strict';
    var THEME_KEY = 'cctv_theme';

    function getStoredTheme() {
        try {
            return localStorage.getItem(THEME_KEY) || 'light';
        } catch (error) {
            return 'light';
        }
    }

    function setStoredTheme(theme) {
        try {
            localStorage.setItem(THEME_KEY, theme);
        } catch (error) {
            // Ignore storage failures to preserve runtime behavior.
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function applyTheme(theme) {
        var root = document.getElementById('html-root');
        if (root) {
            root.setAttribute('data-bs-theme', theme);
        }
        var icon = document.getElementById('theme-icon');
        if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    window.toggleTheme = function() {
        var cur = getStoredTheme();
        var next = cur === 'light' ? 'dark' : 'light';
        setStoredTheme(next);
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

    function initScrollHandlers() {
        var nav = document.querySelector('.site-nav');
        var btn = document.getElementById('back-to-top');
        var secs = document.querySelectorAll('section[id]');
        var links = document.querySelectorAll('.nav-links a');
        
        function checkAll() {
            if (nav) nav.classList.toggle('scrolled', window.scrollY > 30);
            if (btn) btn.classList.toggle('visible', window.scrollY > 400);
            if (secs.length && links.length) {
                var y = window.scrollY + 100;
                secs.forEach(function(s) {
                    if (y >= s.offsetTop && y < s.offsetTop + s.offsetHeight) {
                        links.forEach(function(l) { l.classList.toggle('active', l.getAttribute('href') === '#' + s.id); });
                    }
                });
            }
        }
        
        var debouncedCheck = debounce(checkAll, 100);
        window.addEventListener('scroll', debouncedCheck, { passive: true });
        
        var debouncedResize = debounce(function() {
            if (secs.length && links.length) {
                var y = window.scrollY + 100;
                secs.forEach(function(s) {
                    if (y >= s.offsetTop && y < s.offsetTop + s.offsetHeight) {
                        links.forEach(function(l) { l.classList.toggle('active', l.getAttribute('href') === '#' + s.id); });
                    }
                });
            }
        }, 100);
        window.addEventListener('resize', debouncedResize, { passive: true });
        
        if (btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
        }
        
        checkAll();
    }

    function initLightbox() {
        var modal = document.getElementById('galleryModal');
        if (!modal || typeof bootstrap === 'undefined') return;
        modal.addEventListener('show.bs.modal', function(e) {
            var src = e.relatedTarget.getAttribute('data-img');
            var img = document.getElementById('galleryModalImg');
            if (img) {
                img.src = src;
                var alt = e.relatedTarget.querySelector('img')?.getAttribute('alt') || 'Gallery image';
                img.alt = alt;
            }
        });
    }

    function initFaqToggles() {
        document.querySelectorAll('.faq-question').forEach(function(button) {
            button.addEventListener('click', function() {
                var item = button.closest('.faq-item');
                if (!item) return;
                var isOpen = item.classList.toggle('open');
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });
    }

    function initHeroCarousel() {
        var carouselEl = document.getElementById('heroCarousel');
        if (!carouselEl) return;
        
        // Initialize with Bootstrap Carousel API for more control
        var heroCarousel = new bootstrap.Carousel(carouselEl, {
            interval: 6000,   // matches data-bs-interval in HTML
            ride: 'carousel', // auto-start
            pause: 'hover',   // pause on mouse enter
            wrap: true,       // continuous loop
            keyboard: true    // keyboard navigation (default)
        });
        
        // Pause carousel when not visible (performance optimization)
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) {
                    heroCarousel.pause();
                } else {
                    heroCarousel.cycle();
                }
            });
        }, { threshold: 0 });
        observer.observe(carouselEl);
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
        var themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', window.toggleTheme);
        }

        applyTheme(getStoredTheme());
        initReveal();
        initCounters();
        initScrollHandlers();
        initLightbox();
        initFaqToggles();
        initMobileClose();
        initHeroCarousel();
    });
})();
