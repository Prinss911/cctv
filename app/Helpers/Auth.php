<?php

namespace App\Helpers;

use App\Models\UserModel;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $key = 'login_attempts';
        $lockKey = 'login_locked_until';

        if (isset($_SESSION[$lockKey]) && time() < $_SESSION[$lockKey]) {
            return false;
        }

        $model = new UserModel();
        $user = $model->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
            if ($_SESSION[$key] >= 5) {
                $_SESSION[$lockKey] = time() + (15 * 60);
                $_SESSION[$key] = 0;
            }
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION[$key] = 0;
        unset($_SESSION[$lockKey]);

        return true;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
        ];
    }

    public static function logout(): void
    {
        session_destroy();
    }

    public static function isLockedOut(): bool
    {
        return isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until'];
    }

    public static function lockoutRemaining(): int
    {
        if (!self::isLockedOut()) return 0;
        return $_SESSION['login_locked_until'] - time();
    }
}
