<?php

spl_autoload_register(function ($class) {
    $map = [
        'App\\Controllers\\' => __DIR__ . '/../app/Controllers/',
        'App\\Models\\' => __DIR__ . '/../app/Models/',
        'App\\Middleware\\' => __DIR__ . '/../app/Middleware/',
        'App\\Helpers\\' => __DIR__ . '/../app/Helpers/',
    ];

    foreach ($map as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
