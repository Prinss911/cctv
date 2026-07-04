<?php

return [
    'uploads_path' => __DIR__ . '/../public/uploads',
    'allowed_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    'max_size' => 5 * 1024 * 1024,
    'allowed_directories' => ['sliders', 'gallery', 'testimonials', 'clients', 'logo'],
];
