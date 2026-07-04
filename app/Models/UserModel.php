<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected string $table = 'users';
    protected bool $softDeletes = true;

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'created_at', 'updated_at', 'name', 'email', 'is_active', 'is_locked'];

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Update user's password by ID
     */
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Get trashed users
     */
    public function getTrashed(): array
    {
        return $this->onlyTrashed();
    }
}