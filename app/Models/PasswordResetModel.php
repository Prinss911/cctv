<?php

namespace App\Models;

use PDO;

class PasswordResetModel extends BaseModel
{
    protected string $table = 'password_resets';
    protected bool $softDeletes = false;
    protected array $allowedOrderBy = ['id', 'created_at'];

    /**
     * Get token by token string (for internal use)
     */
    public function getByToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a password reset token for the given email
     * Deletes any existing tokens for this email and creates a new one
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
        $stmt->execute([$email, $token, $expiresAt]);

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
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        return $result ?: null;
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
