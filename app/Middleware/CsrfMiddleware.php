<?php
namespace App\Middleware;

use App\Helpers\Csrf;
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
                SecurityLogger::logCsrfFailure($route);
                throw $e;
            }
        }
    }
}