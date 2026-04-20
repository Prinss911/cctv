(function() {
    'use strict';
    var THEME_KEY = 'cctv_admin_theme';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        var icon = document.getElementById('admin-theme-icon');
        if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    window.toggleAdminTheme = function() {
        var cur = localStorage.getItem(THEME_KEY) || 'light';
        var next = cur === 'light' ? 'dark' : 'light';
        localStorage.setItem(THEME_KEY, next);
        applyTheme(next);
    };

    applyTheme(localStorage.getItem(THEME_KEY) || 'light');

    document.addEventListener('DOMContentLoaded', function() {
        var toggleBtn = document.getElementById('sidebar-toggle');
        var sidebar   = document.getElementById('sidebar');
        var overlay   = document.getElementById('sidebar-overlay');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('show');
                    if (overlay) overlay.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        var sortable = document.getElementById('sortable-list');
        if (sortable && typeof Sortable !== 'undefined') {
            new Sortable(sortable, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    var ids = Array.from(sortable.querySelectorAll('[data-id]')).map(function(el) {
                        return parseInt(el.dataset.id);
                    });
                    fetch(sortable.dataset.url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: ids })
                    }).then(function(r) { if (r.ok) toast('Urutan diperbarui'); });
                }
            });
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
                if (!confirm('Yakin ingin menghapus?')) e.preventDefault();
            });
        });

        document.querySelectorAll('.admin-alert').forEach(function(el) {
            setTimeout(function() {
                var a = bootstrap.Alert.getOrCreateInstance(el);
                if (a) a.close();
            }, 4000);
        });
    });

    function toast(msg) {
        var d = document.createElement('div');
        d.className = 'position-fixed bottom-0 end-0 p-3';
        d.style.zIndex = '1090';
        d.innerHTML = '<div class="toast show" role="alert" style="background:var(--ink);color:white;border:none;font-size:0.85rem"><div class="d-flex"><div class="toast-body"><i class="fas fa-check me-1" style="color:var(--rust)"></i>' + msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>';
        document.body.appendChild(d);
        setTimeout(function() { d.remove(); }, 3000);
    }
})();
