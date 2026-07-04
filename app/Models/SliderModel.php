<?php

namespace App\Models;

class SliderModel extends BaseModel
{
    protected string $table = 'sliders';
    protected bool $softDeletes = true;

    // Allowed columns for ORDER BY clause (prevents SQL injection)
    protected array $allowedOrderBy = ['id', 'sort_order', 'created_at', 'updated_at', 'title', 'subtitle', 'tag', 'is_active'];

    /**
     * Get trashed sliders
     */
    public function getTrashed(): array
    {
        return $this->onlyTrashed();
    }
}