<?php
namespace App\Helpers;

use App\Models\UserModel;
use App\Models\Database;
use App\Helpers\SecurityLogger;
use PDO;

class Auth
{
    public const MAX_LOGIN_ATTEMPTS = 5;
    public const LOCKOUT_DURATION = 900; // 15 minutes in seconds
    public const RATE_LIMIT_WINDOW = 900; // 15 minutes in seconds
    public const CLEANUP_AGE = 86400; // 24 hours in seconds

    public static function attempt(string $email, string $password): bool
    {
        // Get client IP for IP-based rate limiting
        $ipAddress = self::getClientIp();
        $db = Database::getInstance();

        // Clean up old attempts (older than 24 hours) - run periodically
        self::cleanupOldAttempts($db);

        // Check IP-based rate limiting
        $stmt = $db->prepare(
            "SELECT lockout_until FROM login_attempts 
             WHERE ip_address = ? AND email = ? AND lockout_until > ?"
        );
        $stmt->execute([$ipAddress, $email, time()]);
        $lockoutUntil = (int)$stmt->fetchColumn();

        if ($lockoutUntil && time() < $lockoutUntil) {
            return false; // IP-based lockout active
        }

        $model = new UserModel();
        $user = $model->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            // Record failed attempt for IP-based rate limiting using atomic upsert
            // SQLite uses BEGIN IMMEDIATE for write lock (row-level locking via FOR UPDATE not supported)
            $driver = Database::getDriver();
            if ($driver === 'sqlite') {
                $db->exec('BEGIN IMMEDIATE');
            } else {
                $db->beginTransaction();
            }
            try {
                // Lock the row for this IP/email combo
                $stmt = $db->prepare(
                    "SELECT attempted_at, lockout_until FROM login_attempts 
                     WHERE ip_address = ? AND email = ?"
                );
                $stmt->execute([$ipAddress, $email]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $attemptedAt = $row['attempted_at'] ?? 0;
                $lockoutUntil = $row['lockout_until'] ?? 0;

                if ($lockoutUntil && time() < $lockoutUntil) {
                    $db->rollBack();
                    return false; // Lockout still active
                }

                $newAttempts = 1;
                if ($attemptedAt > time() - self::RATE_LIMIT_WINDOW) {
                    // Count recent attempts in the last 15 minutes
                    $stmt = $db->prepare(
                        "SELECT COUNT(*) FROM login_attempts 
                         WHERE ip_address = ? AND email = ? AND attempted_at > ?"
                    );
                    $stmt->execute([$ipAddress, $email, time() - self::RATE_LIMIT_WINDOW]);
                    $newAttempts = (int)$stmt->fetchColumn() + 1;
                }

                $newLockoutUntil = 0;
                if ($newAttempts >= self::MAX_LOGIN_ATTEMPTS) {
                    $newLockoutUntil = time() + self::LOCKOUT_DURATION;
                }

                // Check if row exists
                $stmt = $db->prepare(
                    "SELECT id FROM login_attempts WHERE ip_address = ? AND email = ?"
                );
                $stmt->execute([$ipAddress, $email]);
                $existing = $stmt->fetch();

                if ($existing) {
                    // Update existing row
                    $stmt = $db->prepare(
                        "UPDATE login_attempts SET attempted_at = ?, lockout_until = ? WHERE ip_address = ? AND email = ?"
                    );
                    $stmt->execute([time(), $newLockoutUntil, $ipAddress, $email]);
                } else {
                    // Insert new row
                    $stmt = $db->prepare(
                        "INSERT INTO login_attempts (ip_address, email, attempted_at, lockout_until) VALUES (?, ?, ?, ?)"
                    );
                    $stmt->execute([$ipAddress, $email, time(), $newLockoutUntil]);
                }

                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // Log authentication failure
            SecurityLogger::logAuthFailure($email, 'invalid_credentials');
            return false;
        }

        // Regenerate session ID BEFORE storing user data (prevents session fixation)
        session_regenerate_id(true);

        // Clear IP-based attempts for this IP/email combo
        $stmt = $db->prepare(
            "DELETE FROM login_attempts WHERE ip_address = ? AND email = ?"
        );
        $stmt->execute([$ipAddress, $email]);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

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
        $_SESSION = [];
        session_destroy();
        
        // Clear session cookie to remove stale cookie from browser
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
    }

    /**
     * Get client IP address, handling proxies and load balancers
     * @return string
     */
    public static function getClientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Clean up old login attempts older than 24 hours
     * @param PDO $db
     */
    private static function cleanupOldAttempts(\PDO $db): void
    {
        // Delete attempts older than 24 hours
        $stmt = $db->prepare(
            "DELETE FROM login_attempts WHERE attempted_at < ?"
        );
        $stmt->execute([time() - self::CLEANUP_AGE]);
    }
}