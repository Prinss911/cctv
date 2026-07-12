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

if (!function_exists('base_url')) {
    function base_url(): string {
        // Detect scheme from X-Forwarded-Proto (Cloudflare) or direct HTTPS
        $scheme = 'http';
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $scheme = 'https';
        } elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $scheme = 'https';
        }

        // 1) Use X-Forwarded-Host if present and valid (Cloudflare / reverse proxy)
        $forwardedHost = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? '';
        if ($forwardedHost !== '') {
            $hostPart = explode(':', $forwardedHost)[0];
            if (filter_var($hostPart, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                return $scheme . '://' . rtrim($forwardedHost, '/');
            }
        }

        // 2) Use HTTP_HOST if it's a real domain name (not an IP)
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host !== '') {
            $hostPart = explode(':', $host)[0];
            if (!filter_var($hostPart, FILTER_VALIDATE_IP)) {
                if (filter_var($hostPart, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                    return $scheme . '://' . rtrim($host, '/');
                }
            }
        }

        // 3) Fall back to APP_URL from .env
        return rtrim(env('APP_URL', ''), '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return base_url() . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('upload_url')) {
    function upload_url(string $path): string {
        return base_url() . '/uploads/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        return base_url() . '/' . ltrim($path, '/');
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
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    // Use custom session save path (www-data writable, not /tmp)
    $sessionPath = BASE_PATH . '/storage/sessions';
    if (!is_dir($sessionPath)) {
        @mkdir($sessionPath, 0755, true);
    }
    session_save_path($sessionPath);
    session_start();
}

// Idle session timeout: 30 minutes of inactivity
$idleTimeout = 1800;
$lastActivity = $_SESSION['last_activity'] ?? 0;
if ($lastActivity && (time() - $lastActivity) > $idleTimeout) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// Generate CSRF token if not exists (for new sessions)
if (empty($_SESSION['csrf_token'])) {
    \App\Helpers\Csrf::generate();
}

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data:; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self';");
header("Permissions-Policy: geolocation=(), microphone=(), camera=(), fullscreen=(self), payment=();");

// Remove PHP version disclosure
header_remove('X-Powered-By');


\App\Models\Database::getInstance();

// Global Exception Handler
set_exception_handler(function (\Throwable $e) {
    $isProduction = env('APP_ENV') === 'production';
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    $logDir = BASE_PATH . '/storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    error_log(json_encode($logEntry) . PHP_EOL, 3, $logDir . '/error.log');

    if ($isProduction) {
        http_response_code(500);
        // Include generic error view if exists
        $errorView = VIEWS_PATH . '/errors/500.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo '<h1>Internal Server Error</h1><p>Something went wrong. Please try again later.</p>';
        }
    } else {
        // Development: show detailed error
        echo '<h1>' . htmlspecialchars(get_class($e)) . '</h1>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
});
