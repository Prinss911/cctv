<div class="page-head">
    <h4 class="page-title">Dashboard</h4>
    <span style="font-size:0.78rem;color:var(--warm-gray);font-weight:500;letter-spacing:0.03em"><?= date('l, d F Y') ?></span>
</div>

<div class="welcome-banner">
    <div>
        <h5>Selamat datang, <?= e(\App\Helpers\Auth::user()['name'] ?? 'Admin') ?></h5>
        <p>Kelola konten website <?= e(setting('site_name', 'Bayu CCTV')) ?> dari sini.</p>
    </div>
    <a href="<?= url('/') ?>" target="_blank" class="btn-admin" style="font-size:0.78rem;white-space:nowrap">
        <i class="fas fa-external-link"></i> Lihat Website
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl col-md-4 col-6">
        <div class="stat-admin c-blue">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-admin-num"><?= $stats['sliders'] ?></div>
                    <div class="stat-admin-label">Slider</div>
                </div>
                <div class="stat-admin-icon"><i class="fas fa-images"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-6">
        <div class="stat-admin c-green">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-admin-num"><?= $stats['packages'] ?></div>
                    <div class="stat-admin-label">Paket</div>
                </div>
                <div class="stat-admin-icon"><i class="fas fa-tags"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-6">
        <div class="stat-admin c-amber">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-admin-num"><?= $stats['gallery'] ?></div>
                    <div class="stat-admin-label">Galeri</div>
                </div>
                <div class="stat-admin-icon"><i class="fas fa-camera"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-6">
        <div class="stat-admin c-rose">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-admin-num"><?= $stats['testimonials'] ?></div>
                    <div class="stat-admin-label">Testimoni</div>
                </div>
                <div class="stat-admin-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 col-6">
        <div class="stat-admin c-purple">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-admin-num"><?= $stats['clients'] ?></div>
                    <div class="stat-admin-label">Client</div>
                </div>
                <div class="stat-admin-icon"><i class="fas fa-handshake"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="card-header">Aksi Cepat</div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-4 col-6">
                <a href="<?= url('/admin/sliders/create') ?>" class="quick-action">
                    <i class="fas fa-plus"></i>Tambah Slider
                </a>
            </div>
            <div class="col-md-4 col-6">
                <a href="<?= url('/admin/pricing/create') ?>" class="quick-action">
                    <i class="fas fa-plus"></i>Tambah Paket
                </a>
            </div>
            <div class="col-md-4 col-6">
                <a href="<?= url('/admin/gallery/create') ?>" class="quick-action">
                    <i class="fas fa-plus"></i>Tambah Foto
                </a>
            </div>
            <div class="col-md-4 col-6">
                <a href="<?= url('/admin/testimonials/create') ?>" class="quick-action">
                    <i class="fas fa-plus"></i>Tambah Testimoni
                </a>
            </div>
            <div class="col-md-4 col-6">
                <a href="<?= url('/admin/clients/create') ?>" class="quick-action">
                    <i class="fas fa-plus"></i>Tambah Client
                </a>
            </div>
            <div class="col-md-4 col-6">
                <a href="<?= url('/admin/settings') ?>" class="quick-action">
                    <i class="fas fa-sliders"></i>Pengaturan
                </a>
            </div>
        </div>
    </div>
</div>
