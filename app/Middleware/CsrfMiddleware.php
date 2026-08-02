<?php
namespace App\Middleware;

use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\SecurityLogger;
use App\Helpers\Request;

class CsrfMiddleware
{
    public static function handle(): void
    {
        $method = Request::method();
        $mutatingMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        
        if (in_array($method, $mutatingMethods, true)) {
            try {
                Csrf::verify();
            } catch (\Exception $e) {
                $route = Request::input('_url', $_SERVER['REQUEST_URI'] ?? 'unknown');
                if (method_exists(SecurityLogger::class, 'logCsrfFailure')) {
                    SecurityLogger::logCsrfFailure($route);
                }

                $requestedWith = (string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
                if (strcasecmp($requestedWith, 'XMLHttpRequest') === 0) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'CSRF token mismatch.']);
                    exit;
                }

                Flash::set('error', 'CSRF token mismatch.');
                $redirectTo = $_SERVER['HTTP_REFERER'] ?? '/admin';
                header('Location: ' . $redirectTo);
                exit;
            }
        }
    }
}
