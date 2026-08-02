<?php

namespace App\Helpers;

class Csrf
{
    /**
     * Generate a new CSRF token
     * @return string
     */
    public static function generate(): string
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Get the current CSRF token, generating if needed
     * @return string
     */
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            self::generate();
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function verify(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (empty($token)) {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['_csrf'] ?? '';
        }
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            throw new \RuntimeException('CSRF token mismatch');
        }

        if (!self::isAjaxRequest()) {
            self::generate();
        }
    }

    private static function isAjaxRequest(): bool
    {
        $contentType = (string)($_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        $requestedWith = (string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

        return stripos($contentType, 'application/json') === 0
            || strcasecmp($requestedWith, 'XMLHttpRequest') === 0;
    }
}
