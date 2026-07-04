<?php

namespace App\Models;

class SettingModel extends BaseModel
{
    protected string $table = 'settings';
    protected bool $softDeletes = true;

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'created_at', 'updated_at', 'key', 'value', 'group'];
    public function getByGroup(string $group): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE `group` = ? ORDER BY id ASC";
        if ($this->softDeletes) {
            $sql = "SELECT * FROM {$this->table} WHERE `group` = ? AND deleted_at IS NULL ORDER BY id ASC";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group]);
        return $stmt->fetchAll();
    }

    public function getByKey(string $key): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE `key` = ?";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function updateByKey(string $key, string $value): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET value = ?, updated_at = ? WHERE `key` = ?");
        return $stmt->execute([$value, date('Y-m-d H:i:s'), $key]);
    }

    public function getAllGrouped(): array
    {
        $all = $this->getAll('id ASC');
        $grouped = [];
        foreach ($all as $setting) {
            $grouped[$setting['group']][] = $setting;
        }
        return $grouped;
    }

    /**
     * Get trashed settings
     */
    public function getTrashed(): array
    {
        return $this->onlyTrashed();
    }
}