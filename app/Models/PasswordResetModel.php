<?php

namespace App\Models;

use PDO;

class PasswordResetModel extends BaseModel
{
    protected string $table = 'password_resets';
    protected bool $softDeletes = false;
    protected array $allowedOrderBy = ['id', 'created_at'];

    /**
     * Create a password reset token for the given email
     * Deletes any existing tokens for this email and creates a new one
     * Only the SHA-256 hash is stored at rest; the raw token is returned for the URL
     */
    public function createToken(string $email): string
    {
        // Delete existing tokens for this email
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$email]);

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

        $stmt = $this->db->prepare(
            "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)"
        );
        $stmt->execute([$email, hash('sha256', $token), $expiresAt]);

        return $token;
    }

    /**
     * Verify if a token is valid (not used, not expired)
     * Returns the reset record or null if invalid
     */
    public function verifyToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > datetime('now')"
        );
        $stmt->execute([hash('sha256', $token)]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Record a password reset request attempt (rate limiting)
     */
    public function recordAttempt(string $email, string $ip): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO password_reset_attempts (email, ip_address, attempted_at) VALUES (?, ?, ?)"
        );
        $stmt->execute([$email, $ip, time()]);
    }

    /**
     * Count recent reset requests for an email OR IP within the window
     */
    public function countRecentRequests(string $email, string $ip, int $windowSeconds = 3600): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM password_reset_attempts
             WHERE (email = ? OR ip_address = ?) AND attempted_at > ?"
        );
        $stmt->execute([$email, $ip, time() - $windowSeconds]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Clean up reset attempt records older than the given age
     * Returns number of deleted records
     */
    public function cleanupResetAttempts(int $olderThanSeconds = 86400): int
    {
        $stmt = $this->db->prepare("DELETE FROM password_reset_attempts WHERE attempted_at < ?");
        $stmt->execute([time() - $olderThanSeconds]);
        return $stmt->rowCount();
    }

    /**
     * Mark a reset token as used (one-time use)
     */
    public function markAsUsed(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Clean up expired tokens
     * Returns number of deleted records
     */
    public function cleanupExpired(): int
    {
        $stmt = $this->db->exec("DELETE FROM password_resets WHERE expires_at < datetime('now')");
        return $stmt;
    }
}
