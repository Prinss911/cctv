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
            http_response_code(403);
            die('CSRF token mismatch. Please refresh the page and try again.');
        }
        // Rotate token after successful verification
        self::generate();
    }
}
