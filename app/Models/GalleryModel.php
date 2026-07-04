<?php

namespace App\Models;

class GalleryModel extends BaseModel
{
    protected string $table = 'gallery';
    protected array $allowedOrderBy = ['id', 'category', 'sort_order', 'is_active', 'created_at', 'updated_at'];
    public function getByCategory(string $category): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE category = ? AND is_active = 1";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    public function getCategories(): array
    {
        $sql = "SELECT DISTINCT category FROM {$this->table} WHERE is_active = 1";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " ORDER BY category ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Get trashed gallery items
     */
    public function getTrashed(): array
    {
        return $this->onlyTrashed();
    }
}