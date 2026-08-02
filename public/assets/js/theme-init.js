(function() {
    'use strict';

    var script = document.currentScript;
    var themeKey = script && script.dataset ? script.dataset.themeKey : '';
    var root = document.getElementById('html-root') || document.documentElement;
    var theme = 'light';

    try {
        if (themeKey) {
            theme = localStorage.getItem(themeKey) || 'light';
        }
    } catch (error) {
        theme = 'light';
    }

    if (root) {
        root.setAttribute('data-bs-theme', theme);
    }
})();
