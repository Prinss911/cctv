<?php

namespace App\Models;

class AdvantageModel extends BaseModel
{
    protected string $table = 'advantages';
    protected bool $softDeletes = false;

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'sort_order', 'created_at', 'updated_at', 'title', 'is_active'];

    /**
     * Get active advantages sorted by sort_order for frontend display
     */
    public function getActive(string $orderBy = 'sort_order ASC', ?int $limit = null, ?int $offset = null): array
    {
        return parent::getActive($orderBy, $limit, $offset);
    }
}
