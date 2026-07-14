<?php

namespace App\Models;

class FaqModel extends BaseModel
{
    protected string $table = 'faqs';
    protected bool $softDeletes = false;

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'sort_order', 'created_at', 'updated_at', 'question', 'is_active'];

    /**
     * Get active FAQs sorted by sort_order for frontend display
     */
    public function getActive(string $orderBy = 'sort_order ASC', ?int $limit = null, ?int $offset = null): array
    {
        return parent::getActive($orderBy, $limit, $offset);
    }
}
