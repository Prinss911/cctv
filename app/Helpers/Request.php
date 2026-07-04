<?php
namespace App\Helpers;

class Request
{
    public static function post(string $key = null, $default = null)
    {
        if ($key === null) return $_POST;
        return $_POST[$key] ?? $default;
    }
    
    public static function get(string $key = null, $default = null)
    {
        if ($key === null) return $_GET;
        return $_GET[$key] ?? $default;
    }
    
    public static function file(string $key = null)
    {
        if ($key === null) return $_FILES;
        return $_FILES[$key] ?? null;
    }
    
    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }
    
    public static function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }
    
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
    
    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }
    
    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }
}
