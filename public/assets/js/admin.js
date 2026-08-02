(function() {
    'use strict';
    var THEME_KEY = 'cctv_admin_theme';

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

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        var icon = document.getElementById('admin-theme-icon');
        if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    window.toggleAdminTheme = function() {
        var cur = getStoredTheme();
        var next = cur === 'light' ? 'dark' : 'light';
        setStoredTheme(next);
        applyTheme(next);
    };

    applyTheme(getStoredTheme());

    document.addEventListener('DOMContentLoaded', function() {
        var toggleBtn = document.getElementById('sidebar-toggle');
        var sidebar   = document.getElementById('sidebar');
        var overlay   = document.getElementById('sidebar-overlay');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                    if (overlay) overlay.classList.toggle('show');
                    toggleBtn.setAttribute('aria-expanded', sidebar.classList.contains('show') ? 'true' : 'false');
                } else {
                    sidebar.classList.toggle('collapsed');
                    toggleBtn.setAttribute('aria-expanded', sidebar.classList.contains('collapsed') ? 'false' : 'true');
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        var adminThemeToggle = document.getElementById('admin-theme-toggle');
        if (adminThemeToggle) {
            adminThemeToggle.addEventListener('click', window.toggleAdminTheme);
        }

        initIconPicker();
        initColorSync();
        var sortable = document.getElementById('sortable-list');
        if (sortable && typeof Sortable !== 'undefined') {
            var sortableLastPage = parseInt(sortable.dataset.lastPage || '1', 10);
            if (sortableLastPage <= 1) {
                new Sortable(sortable, {
                    handle: '.handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                            onEnd: function() {
                                var ids = Array.from(sortable.querySelectorAll('[data-id]')).map(function(el) {
                                    return parseInt(el.dataset.id);
                                });
                                var csrfToken = document.querySelector('[name=_csrf]')?.value || '';
                                fetch(sortable.dataset.url, {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ ids: ids, _csrf: csrfToken })
                                }).then(function(r) { if (r.ok) toast('Urutan diperbarui'); });
                            }
                });
            } else {
                sortable.classList.add('sortable-disabled');
            }
        }

        document.querySelectorAll('input[type="file"][accept="image/*"]').forEach(function(input) {
            input.addEventListener('change', function(e) {
                var f = e.target.files[0];
                if (!f) return;
                var r = new FileReader();
                r.onload = function(ev) {
                    var old = input.parentElement.querySelector('.dyn-preview');
                    if (old) old.remove();
                    var w = document.createElement('div');
                    w.className = 'dyn-preview img-preview-box mt-2';
                    var img = document.createElement('img');
                    img.src = ev.target.result;
                    w.appendChild(img);
                    input.parentElement.appendChild(w);
                };
                r.readAsDataURL(f);
            });
        });

        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var message = form.getAttribute('data-confirm') || 'Yakin ingin menghapus?';
                if (!confirm(message)) e.preventDefault();
            });
        });

        document.querySelectorAll('.admin-alert').forEach(function(el) {
            setTimeout(function() {
                var a = bootstrap.Alert.getOrCreateInstance(el);
                if (a) a.close();
            }, 4000);
        });

        initFormSubmit();
    });

    function initIconPicker() {
        var picker = document.getElementById('icon-picker');
        if (!picker) return;
        picker.querySelectorAll('.icon-option').forEach(function(el) {
            el.addEventListener('click', function() {
                picker.querySelectorAll('.icon-option').forEach(function(o) { o.classList.remove('selected'); });
                this.classList.add('selected');
                document.getElementById('icon_custom').value = this.dataset.icon;
            });
        });
        var customInput = document.getElementById('icon_custom');
        if (customInput) {
            customInput.addEventListener('input', function() {
                picker.querySelectorAll('.icon-option').forEach(function(o) { o.classList.remove('selected'); });
            });
        }
    }

    function initColorSync() {
        var colorPicker = document.getElementById('border_color');
        var colorText = document.getElementById('border_color_text');
        if (!colorPicker || !colorText) return;

        colorPicker.addEventListener('input', function() {
            colorText.value = colorPicker.value;
        });

        colorText.addEventListener('input', function() {
            colorPicker.value = colorText.value;
        });
    }

    function initFormSubmit() {
        document.querySelectorAll('.admin-form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = form.querySelector('[type=submit]');
                if (btn) btn.disabled = true;
            });
        });
    }

    function toast(msg) {
        var d = document.createElement('div');
        d.className = 'position-fixed bottom-0 end-0 p-3';
        d.style.zIndex = '1090';

        var toastEl = document.createElement('div');
        toastEl.className = 'toast show';
        toastEl.setAttribute('role', 'alert');
        toastEl.style.background = 'var(--ink)';
        toastEl.style.color = 'white';
        toastEl.style.border = 'none';
        toastEl.style.fontSize = '0.85rem';

        var flex = document.createElement('div');
        flex.className = 'd-flex';

        var body = document.createElement('div');
        body.className = 'toast-body';

        var icon = document.createElement('i');
        icon.className = 'fas fa-check me-1';
        icon.style.color = 'var(--rust)';

        body.appendChild(icon);
        body.appendChild(document.createTextNode(msg));

        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close btn-close-white me-2 m-auto';
        closeBtn.setAttribute('data-bs-dismiss', 'toast');

        flex.appendChild(body);
        flex.appendChild(closeBtn);
        toastEl.appendChild(flex);
        d.appendChild(toastEl);

        document.body.appendChild(d);
        setTimeout(function() { d.remove(); }, 3000);
    }
})();
