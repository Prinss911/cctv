<?php

namespace App\Models;

class TestimonialModel extends BaseModel
{
    protected string $table = 'testimonials';
    protected bool $softDeletes = true;

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'sort_order', 'created_at', 'updated_at', 'customer_name', 'rating', 'is_active', 'user_id'];
    /**
     * Get testimonials with user info
     */
    public function getWithUser($filters = []): array
    {
        $sql = "
            SELECT t.*, u.name as user_name
            FROM {$this->table} t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE 1=1
        ";
        if ($this->softDeletes) {
            $sql .= " AND t.deleted_at IS NULL";
        }
        $params = [];

        if (!empty($filters['is_active'])) {
            $sql .= " AND t.is_active = ?";
            $params[] = $filters['is_active'] ? 1 : 0;
        }

        $sql .= " ORDER BY t.sort_order ASC, t.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get trashed testimonials
     */
    public function getTrashed(): array
    {
        return $this->onlyTrashed();
    }
}