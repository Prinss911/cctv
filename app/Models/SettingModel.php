<?php

namespace App\Models;

class SettingModel extends BaseModel
{
    protected string $table = 'settings';

    public function getByGroup(string $group): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE `group` = ? ORDER BY id ASC");
        $stmt->execute([$group]);
        return $stmt->fetchAll();
    }

    public function getByKey(string $key): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE `key` = ?");
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
}
