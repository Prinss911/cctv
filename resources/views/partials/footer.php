<footer class="site-footer" id="kontak">
    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <?php if (setting('logo')): ?>
                    <img src="<?= upload_url(setting('logo')) ?>" alt="" height="30" style="filter:brightness(0) invert(1)">
                    <?php else: ?>
                    <span class="nav-brand-mark" style="background:var(--rust)"><i class="fas fa-shield-halved" style="font-size:0.7rem"></i></span>
                    <span style="font-family:var(--font-display);color:white;font-size:1.1rem"><?= e(setting('site_name', 'Bayu CCTV')) ?></span>
                    <?php endif; ?>
                </div>
                <p style="line-height:1.8;font-size:0.88rem"><?= e(setting('site_tagline', 'Solusi Keamanan Terpercaya')) ?></p>
                <div class="footer-social mt-3">
                    <a href="https://wa.me/<?= e(setting('whatsapp_number')) ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="mailto:<?= e(setting('email')) ?>"><i class="fas fa-envelope"></i></a>
                    <a href="tel:<?= e(setting('phone_number')) ?>"><i class="fas fa-phone"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="footer-heading">Navigasi</h6>
                <ul class="footer-links">
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="#tentang">Tentang Kami</a></li>
                    <li><a href="#harga">Paket Harga</a></li>
                    <li><a href="#galeri">Galeri</a></li>
                    <li><a href="#testimoni">Testimoni</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4">
                <h6 class="footer-heading">Kontak</h6>
                <ul class="footer-links">
                    <li><a href="tel:<?= e(setting('phone_number')) ?>"><i class="fas fa-phone me-2" style="color:var(--rust)"></i><?= e(setting('phone_number')) ?></a></li>
                    <li><a href="https://wa.me/<?= e(setting('whatsapp_number')) ?>" target="_blank"><i class="fab fa-whatsapp me-2" style="color:var(--rust)"></i><?= e(setting('whatsapp_number')) ?></a></li>
                    <li><a href="mailto:<?= e(setting('email')) ?>"><i class="fas fa-envelope me-2" style="color:var(--rust)"></i><?= e(setting('email')) ?></a></li>
                    <li><span><i class="fas fa-map-marker-alt me-2" style="color:var(--rust)"></i><?= e(setting('address')) ?></span></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-4">
                <h6 class="footer-heading">Lokasi</h6>
                <?php if (setting('maps_embed')): ?>
                <iframe src="<?= e(setting('maps_embed')) ?>" width="100%" height="160" style="border:0;filter:grayscale(0.5)" allowfullscreen="" loading="lazy"></iframe>
                <?php else: ?>
                <div style="height:160px;border:1px dashed rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:0.8rem">
                    <i class="fas fa-map me-1"></i>Peta belum dikonfigurasi
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer-bar">
            <span><?= e(setting('footer_copyright', '© 2024 Bayu CCTV. All rights reserved.')) ?></span>
            <span>Solusi Keamanan Terpercaya</span>
        </div>
    </div>
</footer>
