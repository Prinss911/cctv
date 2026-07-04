<?php

namespace App\Models;

class ClientModel extends BaseModel
{
    protected string $table = 'clients';
    protected bool $softDeletes = true;

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'sort_order', 'created_at', 'updated_at', 'name', 'is_active'];
    /**
     * Get trashed clients
     */
    public function getTrashed(): array
    {
        return $this->onlyTrashed();
    }
}