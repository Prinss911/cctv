<?php

namespace App\Models;

use App\Models\Database;

class PricingBrandModel
{
    private $db;
    private $table = 'pricing_brands';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all brands with optional filters
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
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
     * Get active brands for frontend display
     */
    public function getActive()
    {
        return $this->getAll(['is_active' => true]);
    }

    /**
     * Get brand by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get brand by slug
     */
    public function getBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    /**
     * Create new brand
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, slug, logo, is_active, sort_order, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['logo'] ?? null,
            $data['is_active'] ?? 1,
            $data['sort_order'] ?? 0
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update brand
     */
    public function update($id, $data)
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

        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete brand
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Update brand sort order (for drag-drop reordering)
     */
    public function updateSortOrder($updates)
    {
        // $updates = [[id => sort_order], ...]
        $this->db->beginTransaction();
        try {
            foreach ($updates as $update) {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET sort_order = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$update['sort_order'], $update['id']]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
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
}