<?php
$siteName = setting('site_name', 'Bayu CCTV');
$siteTagline = setting('site_tagline', '');
$metaTitle = $siteName . ($siteTagline ? ' - ' . $siteTagline : '');
$metaDesc = setting('meta_description', '');
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url('')) ?>">
    <meta property="og:title" content="<?= e($metaTitle) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <?php if (setting('og_image')): ?>
    <meta property="og:image" content="<?= upload_url(setting('og_image')) ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?= e(setting('site_name')) ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($metaTitle) ?>">
    <meta name="twitter:description" content="<?= e($metaDesc) ?>">
    <?php if (setting('og_image')): ?>
    <meta name="twitter:image" content="<?= upload_url(setting('og_image')) ?>">
    <?php endif; ?>
    <title><?= e($metaTitle) ?></title>
    <?php if (setting('favicon')): ?>
    <link rel="icon" href="<?= upload_url(setting('favicon')) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"></noscript>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php
    // Critical CSS for above-the-fold content — selector-based extraction
    function extractCriticalCss($css, $patterns) {
        $len = strlen($css);
        $pos = 0;
        $out = '';
        while ($pos < $len) {
            $brace = strpos($css, '{', $pos);
            if ($brace === false) break;
            $selector = trim(substr($css, $pos, $brace - $pos));
            $depth = 1;
            $i = $brace + 1;
            while ($depth > 0 && $i < $len) {
                if ($css[$i] === '{') $depth++;
                if ($css[$i] === '}') $depth--;
                $i++;
            }
            $block = substr($css, $brace, $i - $brace);
            $full = $selector . $block;
            foreach ($patterns as $p) {
                if (str_contains($full, $p)) { $out .= $full . "\n"; break; }
            }
            $pos = $i;
        }
        return $out;
    }
    $cssPath = __DIR__ . '/../../../public/assets/css/app.css';
    if (file_exists($cssPath)) {
        $css = file_get_contents($cssPath);
        $selectors = [':root', '[data-bs-theme="dark"]', 'html', 'body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '.display-heading', 'img', 'a', '.section', '.section-cream', '.section-dark', '.section-header', '.section-label', '.section-heading', '.section-desc', '.site-nav', '.nav-links', '.nav-cta', '.theme-btn', '.hero-slide', '.hero-content', '.about-'];
        $criticalCss = extractCriticalCss($css, $selectors);
        echo '<style>' . $criticalCss . '</style>';
    }
    ?>
    <link rel="preload" href="<?= asset('css/app.css') ?>" as="style" onload="this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= asset('css/app.css') ?>"></noscript>
    <script src="<?= asset('js/theme-init.js') ?>" data-theme-key="cctv_theme"></script>
    <?php
    // JSON-LD Structured Data
    try {
        // LocalBusiness
        $localBusiness = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => setting('site_name', 'Bayu CCTV'),
            'url' => setting('app_url', 'http://localhost'),
            'telephone' => setting('whatsapp_number'),
        ];
        if (setting('address')) {
            $localBusiness['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => setting('address'),
            ];
        }
        if (setting('logo')) {
            $localBusiness['image'] = upload_url(setting('logo'));
        } elseif (setting('og_image')) {
            $localBusiness['image'] = upload_url(setting('og_image'));
        }
        echo '<script type="application/ld+json">' . json_encode($localBusiness, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';

        // Product
        $products = (new \App\Models\PricingModel)->getActiveWithBrand();
        if (!empty($products)) {
            $productList = [];
            foreach ($products as $product) {
                $productList[] = [
                    '@type' => 'Product',
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => preg_replace('/[^0-9.,]/', '', $product['price']),
                        'priceCurrency' => 'IDR',
                        'availability' => 'https://schema.org/InStock'
                    ]
                ];
            }
            echo '<script type="application/ld+json">' . json_encode(['@context' => 'https://schema.org', '@graph' => $productList], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';
        }

        // Review (aggregate from testimonials)
        $testimonials = (new \App\Models\TestimonialModel)->getActive();
        if (!empty($testimonials)) {
            $totalRating = array_sum(array_column($testimonials, 'rating'));
            $reviewAggregate = [
                '@context' => 'https://schema.org',
                '@type' => 'AggregateRating',
                'itemReviewed' => [
                    '@type' => 'LocalBusiness',
                    'name' => setting('site_name', 'Bayu CCTV')
                ],
                'ratingCount' => count($testimonials),
                'ratingValue' => round($totalRating / count($testimonials), 1)
            ];
            echo '<script type="application/ld+json">' . json_encode($reviewAggregate, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';
        }
    } catch (\Exception $e) {
        // Silently fail if there's an error (e.g., model not found, etc.)
    }
?>
<body>
    <a href="#main-content" class="visually-hidden-focusable position-absolute start-0 z-3 p-2 bg-white text-dark">Langsung ke konten utama</a>
    <?php \App\Helpers\View::partial('nav'); ?>
    <main id="main-content"><?= $content ?></main>
    <?php \App\Helpers\View::partial('footer'); ?>
    <?php \App\Helpers\View::partial('whatsapp-widget'); ?>
    <a href="#" id="back-to-top" class="back-top"><i class="fas fa-arrow-up"></i></a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
