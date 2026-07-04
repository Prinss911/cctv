<?php

namespace App\Models;

class PricingBrandModel extends BaseModel
{
    protected string $table = 'pricing_brands';
    protected bool $softDeletes = true;

    /**
     * Get all brands with optional filters (kept for backward compatibility)
     */
    public function getAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $params = [];

        if (!empty($filters['is_active'])) {
            $sql .= " AND is_active = ?";
            $params[] = $filters['is_active'] ? 1 : 0;
        }

        $sql .= " ORDER BY sort_order ASC, name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get all brands - compatible with BaseModel::getAll()
     */
    public function getAll(string $orderBy = 'sort_order ASC, name ASC', ?int $limit = null, ?int $offset = null): array
    {
        return parent::getAll($orderBy, $limit, $offset);
    }

    /**
     * Get active brands for frontend display
     */
    public function getActive(string $orderBy = 'sort_order ASC, name ASC', ?int $limit = null, ?int $offset = null): array
    {
        return parent::getActive($orderBy, $limit, $offset);
    }

    /**
     * Get brand by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get brand by slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    /**
     * Create new brand
     */
    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $result = $stmt->execute(array_values($data));

        if ($result) {
            return (int) $this->db->lastInsertId();
        }
        return 0;
    }

    /**
     * Update brand
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];

        $allowedFields = ['name', 'slug', 'logo', 'is_active', 'sort_order'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = "updated_at = ?";
        $params[] = date('Y-m-d H:i:s');
        $params[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Soft delete brand
     */
    public function delete(int $id): bool
    {
        return parent::delete($id);
    }

    /**
     * Force delete brand (permanent)
     */
    public function forceDelete(int $id): bool
    {
        return parent::forceDelete($id);
    }

    /**
     * Restore soft deleted brand
     */
    public function restore(int $id): bool
    {
        return parent::restore($id);
    }


    /**
     * Get brand with package count
     */
    public function getWithPackageCount($brandId = null)
    {
        $sql = "
            SELECT b.*, COUNT(p.id) as package_count
            FROM {$this->table} b
            LEFT JOIN pricing_packages p ON p.brand_id = b.id AND p.is_active = 1
            WHERE 1=1
        ";
        if ($this->softDeletes) {
            $sql .= " AND b.deleted_at IS NULL";
        }
        $params = [];

        if ($brandId) {
            $sql .= " AND b.id = ?";
            $params[] = $brandId;
        }

        $sql .= " GROUP BY b.id ORDER BY b.sort_order ASC, b.name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        if ($brandId) {
            return $stmt->fetch();
        }
        return $stmt->fetchAll();
    }

    /**
     * Generate slug from name
     */
    public function generateSlug($name, $excludeId = null)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM {$this->table} WHERE slug = ?";
            $params = [$slug];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            if (!$stmt->fetch()) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists (excluding optional ID)
     */
    public function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT id FROM {$this->table} WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Get trashed brands
     */
    public function getTrashed()
    {
        return $this->onlyTrashed();
    }
}