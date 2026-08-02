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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"></noscript>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
<script src="<?= asset('js/theme-init.js') ?>" data-theme-key="cctv_admin_theme"></script>
</head>
<body>
<a href="#main-content" class="skip-link visually-hidden-focusable">Skip to main content</a>
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
            <ul class="nav flex-column" aria-label="Admin navigation">
                <li class="nav-item"><a class="nav-link <?= $currentPath === '/admin' ? 'active' : '' ?>" href="<?= url('/admin') ?>"<?= $currentPath === '/admin' ? ' aria-current="page"' : '' ?>><i class="fas fa-th-large"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/sliders') ? 'active' : '' ?>" href="<?= url('/admin/sliders') ?>"<?= str_starts_with($currentPath, '/admin/sliders') ? ' aria-current="page"' : '' ?>><i class="fas fa-images"></i>Slider</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/pricing') ? 'active' : '' ?>" href="<?= url('/admin/pricing') ?>"<?= str_starts_with($currentPath, '/admin/pricing') ? ' aria-current="page"' : '' ?>><i class="fas fa-tags"></i>Paket Harga</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/brands') ? 'active' : '' ?>" href="<?= url('/admin/brands') ?>"<?= str_starts_with($currentPath, '/admin/brands') ? ' aria-current="page"' : '' ?>><i class="fas fa-tag"></i>Merk</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/gallery') ? 'active' : '' ?>" href="<?= url('/admin/gallery') ?>"<?= str_starts_with($currentPath, '/admin/gallery') ? ' aria-current="page"' : '' ?>><i class="fas fa-camera"></i>Galeri</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/testimonials') ? 'active' : '' ?>" href="<?= url('/admin/testimonials') ?>"<?= str_starts_with($currentPath, '/admin/testimonials') ? ' aria-current="page"' : '' ?>><i class="fas fa-star"></i>Testimoni</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/clients') ? 'active' : '' ?>" href="<?= url('/admin/clients') ?>"<?= str_starts_with($currentPath, '/admin/clients') ? ' aria-current="page"' : '' ?>><i class="fas fa-handshake"></i>Client</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/advantages') ? 'active' : '' ?>" href="<?= url('/admin/advantages') ?>"<?= str_starts_with($currentPath, '/admin/advantages') ? ' aria-current="page"' : '' ?>><i class="fas fa-star"></i>Keunggulan</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/faqs') ? 'active' : '' ?>" href="<?= url('/admin/faqs') ?>"<?= str_starts_with($currentPath, '/admin/faqs') ? ' aria-current="page"' : '' ?>><i class="fas fa-question-circle"></i>FAQ</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/about-features') ? 'active' : '' ?>" href="<?= url('/admin/about-features') ?>"<?= str_starts_with($currentPath, '/admin/about-features') ? ' aria-current="page"' : '' ?>><i class="fas fa-circle-info"></i>Fitur Tentang Kami</a></li>
            </ul>
            <div class="sidebar-section">Sistem</div>
            <ul class="nav flex-column" aria-label="System navigation">
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/users') ? 'active' : '' ?>" href="<?= url('/admin/users') ?>"<?= str_starts_with($currentPath, '/admin/users') ? ' aria-current="page"' : '' ?>><i class="fas fa-users"></i>Pengguna</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPath, '/admin/settings') ? 'active' : '' ?>" href="<?= url('/admin/settings') ?>"<?= str_starts_with($currentPath, '/admin/settings') ? ' aria-current="page"' : '' ?>><i class="fas fa-sliders"></i>Pengaturan</a></li>
            </ul>
            <div class="sidebar-sep"></div>
            <ul class="nav flex-column" aria-label="Account navigation">
                <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>" target="_blank" rel="noopener noreferrer" aria-label="Lihat website (opens in new tab)"><i class="fas fa-external-link"></i>Lihat Website</a></li>
                <li class="nav-item"><form method="POST" action="<?= url('/admin/logout') ?>" class="d-inline"><input type="hidden" name="_csrf" value="<?= \App\Helpers\Csrf::token() ?>"><button type="submit" class="nav-link text-danger bg-transparent border-0 text-start w-100" title="Logout"><i class="fas fa-right-from-bracket"></i>Logout</button></form></li>
            </ul>
        </nav>
        <div id="main-content" class="flex-grow-1">
            <div class="admin-topbar">
                <button class="topbar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar" aria-controls="sidebar" aria-expanded="true"><i class="fas fa-bars"></i></button>
                <div class="topbar-right">
                    <button class="topbar-theme" id="admin-theme-toggle" type="button" title="Tema" aria-label="Toggle dark mode">
                        <i id="admin-theme-icon" class="fas fa-moon"></i>
                    </button>
                    <a href="<?= url('/admin/users/password') ?>" class="topbar-theme" title="Ganti Password" aria-label="Ganti Password">
                        <i class="fas fa-key"></i>
                    </a>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
<script src="<?= asset('js/admin.js') ?>" defer></script>

</body>
</html>
