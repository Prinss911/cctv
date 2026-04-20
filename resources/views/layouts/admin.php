<?php
use App\Helpers\Auth;
use App\Helpers\Flash;
$user = Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$isLogin = $currentPath === '/admin/login';
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= e(setting('site_name', 'Bayu CCTV')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* =============================================
           INDUSTRIAL EDITORIAL — Admin Design System
           Palette : Ink / Rust / Cream / Warm-Gray
           Type    : DM Serif Display + DM Sans
           Corners : 4px square — no shadows, borders
        ============================================= */

        :root {
            --ink:         #1a1a18;
            --ink-soft:    #2e2e2b;
            --rust:        #b5451b;
            --rust-light:  #d4562a;
            --cream:       #f5f0e8;
            --cream-dark:  #ede8df;
            --warm-gray:   #8a8070;
            --warm-gray2:  #c4b9a8;
            --border:      #d0c9bc;
            --border-dark: #b0a898;
            --white:       #ffffff;

            --font-display: 'DM Serif Display', Georgia, serif;
            --font-body:    'DM Sans', system-ui, sans-serif;

            --radius:    4px;
            --sidebar-w: 240px;
            --topbar-h:  56px;
        }

        [data-bs-theme="dark"] {
            --ink:         #f0ebe0;
            --ink-soft:    #d4cfc4;
            --cream:       #1c1b18;
            --cream-dark:  #252420;
            --border:      #3a3830;
            --border-dark: #4a4840;
            --warm-gray:   #9a9080;
            --white:       #1c1b18;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            background: var(--cream);
            color: var(--ink);
            font-size: 0.9rem;
            margin: 0;
            min-height: 100vh;
        }

        /* ---- SIDEBAR ---- */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--ink-soft);
            border-right: 2px solid var(--rust);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.25s ease;
            overflow-y: auto;
        }

        .sidebar-head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-mark {
            width: 36px;
            height: 36px;
            background: var(--rust);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-name {
            display: block;
            font-family: var(--font-display);
            font-size: 0.95rem;
            color: #fff;
            line-height: 1.2;
            letter-spacing: 0.01em;
        }

        .sidebar-sub {
            display: block;
            font-size: 0.62rem;
            color: var(--warm-gray2);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 600;
        }

        .sidebar-section {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--warm-gray2);
            padding: 1.1rem 1rem 0.4rem;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.52rem 1rem;
            color: rgba(255,255,255,0.62);
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: color 0.15s, border-color 0.15s, background 0.15s;
            text-decoration: none;
        }

        #sidebar .nav-link i {
            width: 16px;
            text-align: center;
            font-size: 0.8rem;
            opacity: 0.7;
        }

        #sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
            border-left-color: var(--warm-gray2);
        }

        #sidebar .nav-link.active {
            color: #fff;
            background: rgba(181,69,27,0.18);
            border-left-color: var(--rust);
        }

        #sidebar .nav-link.active i { opacity: 1; }

        #sidebar .nav-link.text-danger { color: #e87a5d !important; }

        .sidebar-sep {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 0.5rem 0;
        }

        /* ---- SIDEBAR OVERLAY (mobile) ---- */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(26,26,24,0.55);
            z-index: 1039;
        }

        /* ---- MAIN CONTENT ---- */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ---- TOPBAR ---- */
        .admin-topbar {
            height: var(--topbar-h);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-toggle {
            background: none;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--ink);
            width: 34px;
            height: 34px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: border-color 0.15s, color 0.15s;
        }

        .topbar-toggle:hover { border-color: var(--rust); color: var(--rust); }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-theme {
            background: none;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--warm-gray);
            width: 34px;
            height: 34px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            transition: border-color 0.15s, color 0.15s;
        }

        .topbar-theme:hover { border-color: var(--rust); color: var(--rust); }

        .topbar-avatar {
            width: 30px;
            height: 30px;
            background: var(--rust);
            color: #fff;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 700;
            font-family: var(--font-display);
        }

        .topbar-name {
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--ink);
        }

        /* ---- CONTENT AREA ---- */
        .admin-content {
            padding: 1.5rem;
            flex-grow: 1;
        }

        /* ---- FLASH ALERTS ---- */
        .admin-alert {
            border-radius: var(--radius);
            border-left: 4px solid;
            font-size: 0.85rem;
            box-shadow: none;
        }

        .admin-alert.alert-success { border-left-color: #2d7a4f; }
        .admin-alert.alert-danger  { border-left-color: var(--rust); }
        .admin-alert.alert-warning { border-left-color: #b57a1b; }

        /* ---- PAGE HEADER ---- */
        .page-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 1.6rem;
            color: var(--ink);
            margin: 0;
            letter-spacing: -0.01em;
        }

        /* ---- CARDS ---- */
        .admin-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: none;
        }

        .admin-card .card-body { padding: 1.25rem; }

        .admin-card .card-header {
            background: var(--cream-dark);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.25rem;
            font-family: var(--font-display);
            font-size: 1rem;
            border-radius: var(--radius) var(--radius) 0 0;
        }

        /* ---- STAT CARDS ---- */
        .stat-admin {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1rem 1.1rem;
        }

        .stat-admin-num {
            font-family: var(--font-display);
            font-size: 2rem;
            line-height: 1;
            margin-bottom: 0.2rem;
        }

        .stat-admin-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--warm-gray);
        }

        .stat-admin-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
        }

        .stat-admin.c-blue   { border-top: 3px solid #2563eb; }
        .stat-admin.c-green  { border-top: 3px solid #16a34a; }
        .stat-admin.c-amber  { border-top: 3px solid #d97706; }
        .stat-admin.c-rose   { border-top: 3px solid #e11d48; }
        .stat-admin.c-purple { border-top: 3px solid #7c3aed; }

        .stat-admin.c-blue   .stat-admin-icon { background: #eff6ff; color: #2563eb; }
        .stat-admin.c-green  .stat-admin-icon { background: #f0fdf4; color: #16a34a; }
        .stat-admin.c-amber  .stat-admin-icon { background: #fffbeb; color: #d97706; }
        .stat-admin.c-rose   .stat-admin-icon { background: #fff1f2; color: #e11d48; }
        .stat-admin.c-purple .stat-admin-icon { background: #f5f3ff; color: #7c3aed; }

        /* ---- WELCOME BANNER ---- */
        .welcome-banner {
            background: var(--ink-soft);
            border: 1px solid var(--ink);
            border-radius: var(--radius);
            padding: 1.1rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            color: #fff;
        }

        .welcome-banner h5 {
            font-family: var(--font-display);
            font-size: 1.05rem;
            color: #fff;
            margin-bottom: 0.15rem;
        }

        .welcome-banner p {
            font-size: 0.8rem;
            color: var(--warm-gray2);
            margin: 0;
        }

        /* ---- BUTTONS ---- */
        .btn-admin {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            background: var(--rust);
            color: #fff;
            border: 1px solid var(--rust);
            border-radius: var(--radius);
            font-size: 0.82rem;
            font-weight: 600;
            font-family: var(--font-body);
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }

        .btn-admin:hover { background: var(--rust-light); border-color: var(--rust-light); color: #fff; }

        .btn-admin-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            background: transparent;
            color: var(--ink);
            border: 1px solid var(--border-dark);
            border-radius: var(--radius);
            font-size: 0.82rem;
            font-weight: 600;
            font-family: var(--font-body);
            text-decoration: none;
            cursor: pointer;
            transition: border-color 0.15s, color 0.15s;
        }

        .btn-admin-outline:hover { border-color: var(--rust); color: var(--rust); }

        /* ---- QUICK ACTIONS ---- */
        .quick-action {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--ink);
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            transition: border-color 0.15s, color 0.15s, background 0.15s;
        }

        .quick-action:hover {
            border-color: var(--rust);
            color: var(--rust);
            background: var(--cream-dark);
        }

        .quick-action i { font-size: 0.75rem; color: var(--rust); }

        /* ---- TABLES ---- */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .admin-table thead th {
            font-size: 0.67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--warm-gray);
            border-bottom: 2px solid var(--border);
            padding: 0.6rem 0.75rem;
            background: var(--cream-dark);
        }

        .admin-table tbody td {
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid var(--border);
            color: var(--ink);
            vertical-align: middle;
        }

        .admin-table tbody tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover td { background: var(--cream); }

        /* ---- FORMS ---- */
        .admin-form .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--warm-gray);
            margin-bottom: 0.35rem;
        }

        .admin-form .form-control,
        .admin-form .form-select {
            border: 1px solid var(--border-dark);
            border-radius: var(--radius);
            font-size: 0.88rem;
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--white);
            box-shadow: none;
            transition: border-color 0.15s;
        }

        .admin-form .form-control:focus,
        .admin-form .form-select:focus {
            border-color: var(--rust);
            box-shadow: none;
            outline: none;
        }

        /* ---- BADGES ---- */
        .badge-admin {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.55rem;
            font-size: 0.67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            border-radius: var(--radius);
            border: 1px solid;
        }

        .badge-admin.active   { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
        .badge-admin.inactive { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

        /* ---- LOGIN PAGE ---- */
        .login-page {
            min-height: 100vh;
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 3px solid var(--rust);
            border-radius: var(--radius);
            padding: 2rem 2rem 2.25rem;
        }

        .login-mark {
            width: 44px;
            height: 44px;
            background: var(--ink-soft);
            color: var(--rust);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 1.1rem;
        }

        .login-title {
            font-family: var(--font-display);
            font-size: 1.55rem;
            color: var(--ink);
            margin-bottom: 0.2rem;
        }

        .login-sub {
            font-size: 0.82rem;
            color: var(--warm-gray);
            margin-bottom: 1.5rem;
        }

        .login-btn {
            width: 100%;
            padding: 0.65rem;
            background: var(--rust);
            color: #fff;
            border: 1px solid var(--rust);
            border-radius: var(--radius);
            font-size: 0.88rem;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }

        .login-btn:hover { background: var(--rust-light); border-color: var(--rust-light); }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            #main-content { margin-left: 0; }
        }

        @media (min-width: 769px) {
            .topbar-toggle { display: none; }
        }

        /* ---- DARK MODE ---- */
        [data-bs-theme="dark"] .admin-topbar  { background: var(--cream-dark); }
        [data-bs-theme="dark"] .admin-card    { background: var(--cream-dark); }
        [data-bs-theme="dark"] .stat-admin    { background: var(--cream-dark); }
        [data-bs-theme="dark"] .quick-action  { background: var(--cream); }
        [data-bs-theme="dark"] .admin-table thead th { background: var(--cream); }
        [data-bs-theme="dark"] .admin-table tbody tr:hover td { background: var(--cream); }
        [data-bs-theme="dark"] .admin-form .form-control,
        [data-bs-theme="dark"] .admin-form .form-select { background: var(--cream); color: var(--ink); }
    </style>
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <script>(function(){var t=localStorage.getItem('cctv_admin_theme')||'light';document.documentElement.setAttribute('data-bs-theme',t)})();</script>
</head>
<body>
<?php if ($isLogin): ?>
    <?php $flash = Flash::get(); ?>
    <?= $content ?>
<?php else: ?>
    <div class="d-flex" id="admin-wrapper">
        <div id="sidebar-overlay" class="sidebar-overlay"></div>
        <nav id="sidebar">
            <div class="sidebar-head">
                <div class="sidebar-mark"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <span class="sidebar-name"><?= e(setting('site_name', 'Bayu CCTV')) ?></span>
                    <span class="sidebar-sub">Admin Panel</span>
                </div>
            </div>
            <div class="sidebar-section">Menu</div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link <?= $currentPath === '/admin' ? 'active' : '' ?>" href="<?= url('/admin') ?>"><i class="fas fa-th-large"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/sliders') ? 'active' : '' ?>" href="<?= url('/admin/sliders') ?>"><i class="fas fa-images"></i>Slider</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/pricing') ? 'active' : '' ?>" href="<?= url('/admin/pricing') ?>"><i class="fas fa-tags"></i>Paket Harga</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/gallery') ? 'active' : '' ?>" href="<?= url('/admin/gallery') ?>"><i class="fas fa-camera"></i>Galeri</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/testimonials') ? 'active' : '' ?>" href="<?= url('/admin/testimonials') ?>"><i class="fas fa-star"></i>Testimoni</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/clients') ? 'active' : '' ?>" href="<?= url('/admin/clients') ?>"><i class="fas fa-handshake"></i>Client</a></li>
            </ul>
            <div class="sidebar-section">Sistem</div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/settings') ? 'active' : '' ?>" href="<?= url('/admin/settings') ?>"><i class="fas fa-sliders"></i>Pengaturan</a></li>
            </ul>
            <div class="sidebar-sep"></div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>" target="_blank"><i class="fas fa-external-link"></i>Lihat Website</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="<?= url('/admin/logout') ?>"><i class="fas fa-right-from-bracket"></i>Logout</a></li>
            </ul>
        </nav>
        <div id="main-content" class="flex-grow-1">
            <div class="admin-topbar">
                <button class="topbar-toggle" id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <div class="topbar-right">
                    <button class="topbar-theme" onclick="toggleAdminTheme()" title="Tema">
                        <i id="admin-theme-icon" class="fas fa-moon"></i>
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <div class="topbar-avatar"><?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?></div>
                        <span class="topbar-name d-none d-sm-inline"><?= e($user['name'] ?? 'Admin') ?></span>
                    </div>
                </div>
            </div>
            <div class="admin-content">
                <?php $flash = Flash::get(); if ($flash): ?>
                <div class="admin-alert alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show mb-3">
                    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?= $content ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
<script>
(function () {
    function applyTheme(t) {
        document.documentElement.setAttribute('data-bs-theme', t);
        var icon = document.getElementById('admin-theme-icon');
        if (icon) icon.className = t === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }

    applyTheme(localStorage.getItem('cctv_admin_theme') || 'light');

    window.toggleAdminTheme = function () {
        var current = document.documentElement.getAttribute('data-bs-theme');
        var next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('cctv_admin_theme', next);
        applyTheme(next);
    };

    var toggle  = document.getElementById('sidebar-toggle');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');

    function openSidebar()  { if (sidebar) sidebar.classList.add('open'); if (overlay) overlay.classList.add('open'); }
    function closeSidebar() { if (sidebar) sidebar.classList.remove('open'); if (overlay) overlay.classList.remove('open'); }

    if (toggle)  toggle.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
})();
</script>
</body>
</html>
