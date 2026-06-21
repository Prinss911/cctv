<?php

define('BASE_PATH', dirname(__DIR__));
define('VIEWS_PATH', BASE_PATH . '/resources/views');
define('PUBLIC_PATH', BASE_PATH . '/public');

$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, '"\'');
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false) return $default;
        $map = ['true' => true, 'false' => false, 'null' => null, '' => null];
        $lower = strtolower($value);
        return array_key_exists($lower, $map) ? $map[$lower] : $value;
    }
}

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        $base = rtrim(env('APP_URL', ''), '/');
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $base = rtrim('https://' . $_SERVER['HTTP_X_FORWARDED_HOST'], '/');
        } elseif (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost:8000' && $_SERVER['HTTP_HOST'] !== '127.0.0.1:8000') {
            $base = rtrim('https://' . $_SERVER['HTTP_HOST'], '/');
        }
        return $base . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('upload_url')) {
    function upload_url(string $path): string {
        $base = rtrim(env('APP_URL', ''), '/');
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $base = rtrim('https://' . $_SERVER['HTTP_X_FORWARDED_HOST'], '/');
        } elseif (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost:8000' && $_SERVER['HTTP_HOST'] !== '127.0.0.1:8000') {
            $base = rtrim('https://' . $_SERVER['HTTP_HOST'], '/');
        }
        return $base . '/uploads/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $base = rtrim(env('APP_URL', ''), '/');
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $base = rtrim('https://' . $_SERVER['HTTP_X_FORWARDED_HOST'], '/');
        } elseif (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost:8000' && $_SERVER['HTTP_HOST'] !== '127.0.0.1:8000') {
            $base = rtrim('https://' . $_SERVER['HTTP_HOST'], '/');
        }
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('setting')) {
    function setting(string $key, $default = null) {
        static $cache = [];
        if (empty($cache)) {
            try {
                $db = \App\Models\Database::getInstance();
                $stmt = $db->query("SELECT `key`, value FROM settings");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $cache[$row['key']] = $row['value'];
                }
            } catch (\Exception $e) {
                return $default;
            }
        }
        return $cache[$key] ?? $default;
    }
}

if (!function_exists('format_rupiah')) {
    function format_rupiah(int $amount): string {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

require __DIR__ . '/autoload.php';

$config = require BASE_PATH . '/config/app.php';
date_default_timezone_set($config['timezone'] ?? 'Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

\App\Models\Database::getInstance();
