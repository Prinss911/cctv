<?php
$metaTitle = setting('meta_title', setting('site_name', 'Bayu CCTV'));
$metaDesc = setting('meta_description', '');
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="light" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDesc) ?>">
    <meta property="og:title" content="<?= e($metaTitle) ?>">
    <meta property="og:description" content="<?= e($metaDesc) ?>">
    <?php if (setting('og_image')): ?>
    <meta property="og:image" content="<?= upload_url(setting('og_image')) ?>">
    <?php endif; ?>
    <title><?= e($metaTitle) ?></title>
    <?php if (setting('favicon')): ?>
    <link rel="icon" href="<?= upload_url(setting('favicon')) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script>
        (function(){
            var t = localStorage.getItem('cctv_theme') || 'light';
            document.getElementById('html-root').setAttribute('data-bs-theme', t);
        })();
    </script>
</head>
<body>
    <?php \App\Helpers\View::partial('nav'); ?>
    <main><?= $content ?></main>
    <?php \App\Helpers\View::partial('footer'); ?>
    <?php \App\Helpers\View::partial('whatsapp-widget'); ?>
    <a href="#" id="back-to-top" class="back-top"><i class="fas fa-arrow-up"></i></a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
