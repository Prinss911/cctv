<?php

namespace App\Models;

use PDO;

abstract class BaseModel
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $softDeletes = false;
    protected array $allowedOrderBy = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(string $orderBy = 'sort_order ASC', ?int $limit = null, ?int $offset = null): array
    {
        $orderBy = $this->validateOrderBy($orderBy);
        $sql = "SELECT * FROM {$this->table}";
        if ($this->softDeletes) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        $sql .= " ORDER BY $orderBy";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        if ($offset !== null) {
            $sql .= " OFFSET " . (int)$offset;
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getActive(string $orderBy = 'sort_order ASC', ?int $limit = null, ?int $offset = null): array
    {
        $orderBy = $this->validateOrderBy($orderBy);
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " ORDER BY $orderBy";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        if ($offset !== null) {
            $sql .= " OFFSET " . (int)$offset;
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Paginate records with safe prepared statements
     * 
     * @param string $orderBy Order by clause (e.g., "id ASC", "name DESC")
     * @param int $perPage Items per page
     * @param int $page Current page number
     * @param array $where WHERE conditions as array of strings (each element is a condition like "column = ?")
     * @param array $params Parameters for prepared statement
     * @return array Pagination result with data, total, pagination info
     */
    public function paginate(string $orderBy = 'id ASC', int $perPage = 10, int $page = 1, array $where = [], array $params = []): array
    {
        $orderBy = $this->validateOrderBy($orderBy);

        // Build WHERE clause safely
        $whereSql = '';
        if (!empty($where)) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        // Count total rows - safely execute with params
        $countSql = "SELECT COUNT(*) FROM {$this->table}";
        if ($this->softDeletes) {
            $softDeleteClause = " WHERE deleted_at IS NULL";
            $countSql .= $whereSql ? $softDeleteClause . " AND (" . implode(' AND ', $where) . ")" : $softDeleteClause;
        } else {
            $countSql .= $whereSql;
        }
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // Fetch page rows - safely execute with params
        $offset = ($page - 1) * $perPage;
        $dataSql = "SELECT * FROM {$this->table}";
        if ($this->softDeletes) {
            $softDeleteClause = " WHERE deleted_at IS NULL";
            $dataSql .= $whereSql ? $softDeleteClause . " AND (" . implode(' AND ', $where) . ")" : $softDeleteClause;
        } else {
            $dataSql .= $whereSql;
        }
        $dataSql .= " ORDER BY $orderBy LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($dataSql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        if ($this->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findWithTrashed(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function onlyTrashed(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        return $stmt->fetchAll();
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
        if ($this->softDeletes) {
            return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function restore(int $id): bool
    {
        if (!$this->softDeletes) {
            return false;
        }
        return $this->update($id, ['deleted_at' => null]);
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($this->softDeletes) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function countWithTrashed(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return (int) $stmt->fetchColumn();
    }

    public function updateSortOrder(array $ids): void
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET sort_order = ? WHERE {$this->primaryKey} = ?");
        $this->db->beginTransaction();

        try {
            foreach ($ids as $order => $id) {
                $validatedId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if ($validatedId === false) {
                    throw new \InvalidArgumentException('Invalid sort order id');
                }

                $stmt->execute([$order + 1, (int) $validatedId]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Define a belongs-to relationship
     */
    protected function belongsTo(string $relatedModel, ?string $foreignKey = null, string $ownerKey = 'id'): ?array
    {
        $foreignKey = $foreignKey ?? strtolower(class_basename($relatedModel)) . '_id';
        $related = new $relatedModel();
        $stmt = $this->db->prepare("SELECT * FROM {$related->getTable()} WHERE {$ownerKey} = ?");
        $stmt->execute([$this->{$foreignKey} ?? null]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Define a has-many relationship
     */
    protected function hasMany(string $relatedModel, ?string $foreignKey = null, string $localKey = 'id'): array
    {
        $foreignKey = $foreignKey ?? strtolower(class_basename(get_class($this))) . '_id';
        $related = new $relatedModel();
        $stmt = $this->db->prepare("SELECT * FROM {$related->getTable()} WHERE {$foreignKey} = ?");
        $stmt->execute([$this->{$localKey} ?? null]);
        return $stmt->fetchAll();
    }

    /**
     * Validate ORDER BY clause against allowed columns
     * Prevents SQL injection via orderBy parameter
     * Supports table.column format for joins (e.g., "users.name ASC")
     */
    protected function validateOrderBy(string $orderBy): string
    {
        // Get allowed columns from the property, if not set use a safe default
        $allowed = $this->allowedOrderBy;
        if (empty($allowed)) {
            $allowed = ['id', 'sort_order', 'created_at', 'updated_at', 'name', 'title', 'is_active'];
        }

        // Parse orderBy: can be 'column', 'column ASC', 'column DESC', 'table.column ASC', 'col1 ASC, col2 DESC'
        $parts = array_map('trim', explode(',', $orderBy));
        $validated = [];

        foreach ($parts as $part) {
            $tokens = preg_split('/\s+/', $part);
            $column = $tokens[0] ?? '';
            $direction = isset($tokens[1]) ? strtoupper($tokens[1]) : 'ASC';

            // Validate column name - allows table.column format (e.g., "users.name")
            // Regex: starts with letter/underscore, allows alphanumeric + underscore, optional dot + another identifer
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*(\.[a-zA-Z_][a-zA-Z0-9_]*)?$/', $column)) {
                continue; // Skip invalid column
            }

            // Extract just the column part for whitelist check (remove table prefix)
            $columnOnly = strstr($column, '.', true) ?: $column;
            
            // Check against whitelist
            if (!in_array($columnOnly, $allowed, true)) {
                continue; // Skip non-allowed column
            }

            // Validate direction
            $direction = in_array($direction, ['ASC', 'DESC'], true) ? $direction : 'ASC';

            $validated[] = "$column $direction";
        }

        // Fallback to default if nothing valid
        return $validated ? implode(', ', $validated) : 'id ASC';
    }
}
