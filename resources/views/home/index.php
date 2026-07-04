<?php
// JSON-LD for homepage
$homeData = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => setting('site_name', 'Bayu CCTV'),
    'url' => setting('app_url', 'http://localhost'),
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => setting('app_url', 'http://localhost') . '/?s={search_term_string}',
        'query-input' => 'required name=search_term_string'
    ]
];
if (setting('logo')) {
    $homeData['image'] = upload_url(setting('logo'));
}
// Organization
$orgData = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => setting('site_name', 'Bayu CCTV'),
    'url' => setting('app_url', 'http://localhost'),
    'logo' => setting('logo') ? upload_url(setting('logo')) : null,
    'telephone' => setting('whatsapp_number'),
    'address' => setting('address') ? [
        '@type' => 'PostalAddress',
        'streetAddress' => setting('address')
    ] : null,
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => setting('whatsapp_number'),
        'contactType' => 'Customer service',
        'areaServed' => 'ID'
    ]
];
// Remove null values
$orgData = array_filter($orgData, fn($v) => $v !== null);
echo '<script type="application/ld+json">' . json_encode($homeData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';
echo '<script type="application/ld+json">' . json_encode($orgData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';
?>
<!-- HERO -->
<section id="beranda" class="p-0" aria-label="Hero Banner">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000" role="region" aria-roledescription="carousel" aria-label="Hero Banner">
        <div class="carousel-inner">
            <?php foreach ($sliders as $i => $slider): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" role="group" aria-roledescription="slide" aria-label="<?= $i + 1 ?> of <?= count($sliders) ?>: <?= e($slider['title']) ?>">
                <?php
                    $imagePath = $slider['image'];
                    $imageUrl = upload_url($imagePath);
                ?>
<?php $loadingAttr = $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"'; ?>
                 <img src="<?= $imageUrl ?>" 
                      <?= $loadingAttr ?>
                      width="1600" height="900"
                      alt="<?= e($slider['title']) ?>"
                      class="d-block w-100 hero-slide-img">
                <div class="hero-overlay">
                    <div class="container">
                        <div class="hero-content">
                            <div class="hero-tag"><?= e($slider['tag'] ?? setting('hero_tag_default', 'Keamanan Terpercaya')) ?></div>
                            <h1><?= e($slider['title']) ?></h1>
                            <p><?= e($slider['subtitle']) ?></p>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="https://wa.me/<?= e(setting('whatsapp_number')) ?>" target="_blank" rel="noopener" class="btn-hero btn-hero-fill">
                                    <i class="fab fa-whatsapp"></i> Hubungi Kami
                                </a>
                                <a href="#harga" class="btn-hero btn-hero-ghost">
                                    Lihat Paket <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
         </div>
         <?php if (count($sliders) > 1): ?>
         <div class="carousel-indicators">
             <?php foreach ($sliders as $i => $slider): ?>
             <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>: <?= e($slider['title']) ?>"></button>
             <?php endforeach; ?>
         </div>
         <?php endif; ?>
         <?php if (count($sliders) > 1): ?>
<button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous slide"><span class="carousel-control-prev-icon"></span></button>
         <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next slide"><span class="carousel-control-next-icon"></span></button>
        <?php endif; ?>
    </div>
</section>

<!-- ABOUT -->
<section id="tentang" class="section">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-6 reveal">
                <div class="section-header">
                    <div class="section-label">Tentang Kami</div>
                    <h2 class="section-heading"><?= e(setting('about_title', 'Kenapa Memilih Kami?')) ?></h2>
                    <p class="section-desc"><?= e(setting('about_text')) ?></p>
                </div>
                <div class="mt-4">
                    <div class="feature-row reveal reveal-d1">
                        <div class="feature-icon-box"><i class="fas fa-tools"></i></div>
                        <div class="feature-text">
                            <strong>Teknisi Berpengalaman</strong>
                            <span>Tim tersertifikasi dengan pengalaman bertahun-tahun</span>
                        </div>
                    </div>
                    <div class="feature-row reveal reveal-d2">
                        <div class="feature-icon-box"><i class="fas fa-award"></i></div>
                        <div class="feature-text">
                            <strong>Garansi Resmi</strong>
                            <span>Produk dan pemasangan bergaransi penuh</span>
                        </div>
                    </div>
                    <div class="feature-row reveal reveal-d3">
                        <div class="feature-icon-box"><i class="fas fa-mobile-alt"></i></div>
                        <div class="feature-text">
                            <strong>Pantau dari HP</strong>
                            <span>Remote monitoring via smartphone kapan saja</span>
                        </div>
                    </div>
                    <div class="feature-row reveal reveal-d4">
                        <div class="feature-icon-box"><i class="fas fa-headset"></i></div>
                        <div class="feature-text">
                            <strong>After-Sales Support</strong>
                            <span>Layanan purna jual yang responsif</span>
                        </div>
                    </div>
                </div>
                <a href="https://wa.me/<?= e(setting('whatsapp_number')) ?>" target="_blank" rel="noopener" class="btn-hero btn-hero-fill mt-4" style="display:inline-flex">
                    <i class="fab fa-whatsapp"></i> Konsultasi Gratis
                </a>
            </div>
            <div class="col-lg-6 reveal">
                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-num" data-count="<?= e(setting('about_experience_years', '5')) ?>" data-suffix="+">0+</div>
                        <div class="stat-text">Tahun</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num" data-count="<?= e(setting('about_total_clients', '1000')) ?>" data-suffix="+">0+</div>
                        <div class="stat-text">Pelanggan</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num" data-count="<?= e(setting('about_total_cities', '20')) ?>" data-suffix="+">0+</div>
                        <div class="stat-text">Kota</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section id="harga" class="section section-cream">
    <div class="container">
        <div class="section-header text-center reveal" style="max-width:500px;margin:0 auto 3.5rem">
            <div class="section-label" style="justify-content:center">Paket Harga</div>
            <h2 class="section-heading" style="max-width:100%">Pilih Paket Sesuai Kebutuhan</h2>
            <p class="section-desc" style="margin:1rem auto 0">Sudah termasuk pemasangan, konfigurasi, dan garansi</p>
        </div>

        <?php if (!empty($brands)): ?>
        <!-- Brand Tabs -->
        <div class="brand-tabs-wrapper reveal">
            <ul class="brand-tabs nav nav-pills justify-content-center mb-4" id="brandTabs" role="tablist">
                <?php foreach ($brands as $i => $brand): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" id="tab-<?= $brand['slug'] ?>" data-bs-toggle="pill" data-bs-target="#panel-<?= $brand['slug'] ?>" type="button" role="tab">
                        <?php if ($brand['logo']): ?>
                        <img src="<?= upload_url($brand['logo']) ?>" alt="<?= e($brand['name']) ?>" style="height: 24px; margin-right: 8px;">
                        <?php endif; ?>
                        <?= e($brand['name']) ?>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content" id="brandTabsContent">
                <?php foreach ($brands as $i => $brand): ?>
                <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="panel-<?= $brand['slug'] ?>" role="tabpanel">
                    <div class="row g-3 justify-content-center">
                        <?php if (!empty($packages[$brand['id']])): ?>
                            <?php foreach ($packages[$brand['id']] as $idx => $pkg): ?>
                            <div class="col-lg-3 col-md-6 reveal reveal-d<?= min($idx + 1, 4) ?>">
                                <div class="price-card <?= $pkg['is_featured'] ? 'featured' : '' ?>">
                                    <div class="price-head">
                                        <div class="price-cam-num"><?= $pkg['camera_count'] ?></div>
                                        <div class="price-cam-label">Kamera</div>
                                        <div class="price-name"><?= e($pkg['name']) ?></div>
                                    </div>
                                    <div class="price-body">
                                        <?php if ($pkg['price_original'] > 0): ?>
                                        <div class="price-original"><?= format_rupiah($pkg['price_original']) ?></div>
                                        <?php endif; ?>
                                        <div class="price-amount"><?= format_rupiah($pkg['price']) ?></div>
                                        <?php if ($pkg['price_original'] > $pkg['price']): ?>
                                        <div class="price-save">Hemat <?= format_rupiah($pkg['price_original'] - $pkg['price']) ?></div>
                                        <?php endif; ?>
                                        <ul class="price-features">
                                            <?php foreach ($pkg['features'] as $feat): ?>
                                            <li><i class="fas fa-check"></i> <?= e($feat['feature']) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                         <a href="https://wa.me/<?= e(setting('whatsapp_number')) ?>?text=<?= urlencode($pkg['whatsapp_message']) ?>"
                                            target="_blank" rel="noopener" class="btn-price">
                                             <i class="fab fa-whatsapp me-1"></i>Pesan Sekarang
                                         </a>
                                     </div>
                                 </div>
                             </div>
                             <?php endforeach; ?>
                         <?php else: ?>
                        <div class="col-12 text-center text-muted py-5">
                            <p>Belum ada paket untuk brand ini.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Fallback: show all packages without tabs (legacy) -->
        <div class="row g-3 justify-content-center">
            <?php foreach ($packages as $idx => $pkg): ?>
            <div class="col-lg-3 col-md-6 reveal reveal-d<?= min($idx + 1, 4) ?>">
                <div class="price-card <?= $pkg['is_featured'] ? 'featured' : '' ?>">
                    <div class="price-head">
                        <div class="price-cam-num"><?= $pkg['camera_count'] ?></div>
                        <div class="price-cam-label">Kamera</div>
                        <div class="price-name"><?= e($pkg['name']) ?></div>
                    </div>
                    <div class="price-body">
                        <?php if ($pkg['price_original'] > 0): ?>
                        <div class="price-original"><?= format_rupiah($pkg['price_original']) ?></div>
                        <?php endif; ?>
                        <div class="price-amount"><?= format_rupiah($pkg['price']) ?></div>
                        <?php if ($pkg['price_original'] > $pkg['price']): ?>
                        <div class="price-save">Hemat <?= format_rupiah($pkg['price_original'] - $pkg['price']) ?></div>
                        <?php endif; ?>
                        <ul class="price-features">
                            <?php foreach ($pkg['features'] as $feat): ?>
                            <li><i class="fas fa-check"></i> <?= e($feat['feature']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                         <a href="https://wa.me/<?= e(setting('whatsapp_number')) ?>?text=<?= urlencode($pkg['whatsapp_message']) ?>"
                            target="_blank" rel="noopener" class="btn-price">
                             <i class="fab fa-whatsapp me-1"></i>Pesan Sekarang
                         </a>
                     </div>
                 </div>
             </div>
             <?php endforeach; ?>
         </div>
         <?php endif; ?>
     </div>
</section>

<!-- GALLERY -->
<section id="galeri" class="section">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-8 reveal">
                <div class="section-label">Galeri</div>
                <h2 class="section-heading">Hasil Pemasangan</h2>
            </div>
        </div>
        <div class="gallery-grid reveal">
            <?php foreach ($gallery as $photo): ?>
            <?php
                $imagePath = $photo['image'];
                $imageUrl = upload_url($imagePath);
            ?>
            <div class="gallery-cell" data-bs-toggle="modal" data-bs-target="#galleryModal" data-img="<?= $imageUrl ?>">
                <img src="<?= $imageUrl ?>"
                     alt="<?= e($photo['title']) ?>"
                     loading="lazy">
                <?php if ($photo['title']): ?>
                <span class="gallery-label"><?= e($photo['title']) ?></span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center p-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close gallery" style="z-index:10"></button>
                <img id="galleryModalImg" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- TESTIMONIALS -->
<section id="testimoni" class="section section-cream">
    <div class="container">
        <div class="section-header reveal" style="margin-bottom:3rem">
            <div class="section-label">Testimoni</div>
            <h2 class="section-heading">Kata Mereka</h2>
        </div>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                <?php $chunks = array_chunk($testimonials, 3); foreach ($chunks as $i => $chunk): ?>
<div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" role="group" aria-roledescription="slide" aria-label="Slide <?= $i + 1 ?> of <?= count($chunks) ?>">
                    <div class="row g-3">
                        <?php foreach ($chunk as $testi): ?>
                        <div class="col-md-4">
                            <div class="testi-card">
                                <?php if ($testi['screenshot']): ?>
                                <img src="<?= upload_url($testi['screenshot']) ?>" alt="" class="img-fluid mb-3" loading="lazy" style="border:1px solid var(--border)">
                                <?php endif; ?>
                                <div class="testi-stars">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="fas fa-star <?= $s <= $testi['rating'] ? '' : 'opacity-25' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($testi['content']): ?>
                                <p class="testi-text">"<?= e($testi['content']) ?>"</p>
                                <?php endif; ?>
                                <div class="testi-author">
                                    <div class="testi-avatar"><?= strtoupper(substr($testi['customer_name'], 0, 1)) ?></div>
                                    <div>
                                        <div class="testi-name"><?= e($testi['customer_name']) ?></div>
                                        <div class="testi-role">Pelanggan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($chunks) > 1): ?>
            <div class="mt-4 d-flex gap-2">
                <button class="btn-hero btn-hero-ghost" style="padding:0.5rem 1.2rem;font-size:0.75rem" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <i class="fas fa-arrow-left me-1"></i> Prev
                </button>
                <button class="btn-hero btn-hero-ghost" style="padding:0.5rem 1.2rem;font-size:0.75rem;background:var(--ink);color:white;border-color:var(--ink)" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    Next <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CLIENTS -->
<?php if (!empty($clients)): ?>
<section class="section">
    <div class="container">
        <div class="text-center mb-4 reveal">
            <div class="section-label" style="justify-content:center">Mitra Kami</div>
            <h2 class="section-heading" style="max-width:100%;margin:0 auto">Brand yang Kami Gunakan</h2>
        </div>
        <div class="row g-0 justify-content-center reveal">
            <?php foreach ($clients as $client): ?>
            <div class="col-6 col-md-3">
                <div class="client-item">
                    <img src="<?= upload_url($client['logo']) ?>" alt="<?= e($client['name']) ?>" loading="lazy" title="<?= e($client['name']) ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section">
    <div class="container cta-inner text-center reveal">
        <h2 class="cta-heading mb-3">Siap Pasang CCTV?</h2>
        <p style="color:rgba(255,255,255,0.6);max-width:450px;margin:0 auto 2rem;font-size:1rem;line-height:1.8">
            Konsultasi gratis dan survey lokasi tanpa biaya. Hubungi kami untuk penawaran terbaik.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="https://wa.me/<?= e(setting('whatsapp_number')) ?>?text=<?= urlencode('Halo, saya ingin konsultasi pemasangan CCTV.') ?>" target="_blank" rel="noopener" class="btn-hero btn-hero-fill">
                <i class="fab fa-whatsapp"></i> Chat WhatsApp
            </a>
            <a href="tel:<?= e(setting('phone_number')) ?>" class="btn-hero btn-hero-ghost">
                <i class="fas fa-phone"></i> Telepon Sekarang
            </a>
        </div>
    </div>
</section>