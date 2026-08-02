<nav class="navbar navbar-expand-lg sticky-top site-nav" aria-label="Main navigation">
    <div class="container nav-inner">
        <a class="nav-brand" href="<?= url('/') ?>">
            <?php if (setting('logo')): ?>
            <img src="<?= upload_url(setting('logo')) ?>" alt="<?= e(setting('site_name')) ?>" height="36">
            <?php else: ?>
            <span class="nav-brand-mark"><i class="fas fa-shield-halved"></i></span>
            <span><?= e(setting('site_name', 'Bayu CCTV')) ?></span>
            <?php endif; ?>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Toggle navigasi" aria-expanded="false" aria-controls="navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navMain">
            <ul class="nav-links d-lg-flex me-3">
                <li><a href="#beranda">Beranda</a></li>
                <li><a href="#tentang">Tentang</a></li>
                <li><a href="#harga">Harga</a></li>
                <li><a href="#galeri">Galeri</a></li>
                <li><a href="#testimoni">Testimoni</a></li>
                <li><a href="#kontak">Kontak</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="tel:<?= e(setting('phone_number')) ?>" class="nav-phone d-none d-lg-inline">
                    <i class="fas fa-phone-alt me-1"></i><?= e(setting('phone_number', '0812-3456-7890')) ?>
                </a>
                <button id="theme-toggle" type="button" class="theme-btn" title="Ganti tema" aria-label="Toggle dark mode">
                    <i id="theme-icon" class="fas fa-moon"></i>
                </button>
                <a href="https://wa.me/<?= e(setting('whatsapp_number', '6281234567890')) ?>" target="_blank" rel="noopener noreferrer" class="nav-cta">
                    <i class="fab fa-whatsapp me-1"></i>Hubungi
                </a>
            </div>
        </div>
    </div>
</nav>
