<?php

namespace App\Models;

use PDO;

abstract class BaseModel
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(string $orderBy = 'sort_order ASC'): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY $orderBy");
        return $stmt->fetchAll();
    }

    public function getActive(string $orderBy = 'sort_order ASC'): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY $orderBy");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->db->prepare("UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?");
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }

    public function updateSortOrder(array $ids): void
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET sort_order = ? WHERE {$this->primaryKey} = ?");
        foreach ($ids as $order => $id) {
            $stmt->execute([$order + 1, $id]);
        }
    }
}
