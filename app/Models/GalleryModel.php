<?php

namespace App\Models;

class GalleryModel extends BaseModel
{
    protected string $table = 'gallery';

    public function getByCategory(string $category): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE category = ? AND is_active = 1 ORDER BY sort_order ASC");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    public function getCategories(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT category FROM {$this->table} WHERE is_active = 1 ORDER BY category ASC");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
