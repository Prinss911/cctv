<?php \App\Helpers\View::partial('crud-index', [
    'title' => 'Testimoni',
    'addUrl' => '/admin/testimonials/create',
    'emptyIcon' => 'fa-star',
    'emptyMessage' => 'Belum ada testimoni.',
    'columns' => [
        ['key' => 'screenshot', 'label' => '', 'class' => 'd-none d-sm-table-cell', 'style' => 'min-width:50px', 'render' => function($item) {
            if ($item['screenshot']) {
                return '<img src="'.upload_url($item['screenshot']).'" alt="Screenshot: '.e($item['customer_name']).'" class="table-thumb">';
            }
            return '<div style="width:36px;height:36px;background:var(--navy);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem">'.strtoupper(substr($item['customer_name'], 0, 1)).'</div>';
        }],
        ['key' => 'customer_name', 'label' => 'Nama', 'render' => function($item) {
            return '<strong>'.e($item['customer_name']).'</strong>';
        }],
        ['key' => 'rating', 'label' => 'Rating', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:100px', 'render' => function($item) {
            $html = '';
            for ($s = 1; $s <= 5; $s++) {
                $color = $s <= $item['rating'] ? 'var(--rust)' : 'var(--border)';
                $html .= '<i class="fas fa-star" style="font-size:0.65rem;color:'.$color.'"></i>';
            }
            return $html;
        }],
        ['key' => 'content', 'label' => 'Konten', 'class' => 'd-none d-lg-table-cell', 'render' => function($item) {
            $content = $item['content'] ?? '';
            $excerpt = e(mb_substr($content, 0, 50));
            if (strlen($content) > 50) $excerpt .= '...';
            return '<small style="color:var(--warm-gray)">'.$excerpt.'</small>';
        }],
        ['key' => 'status', 'label' => 'Status', 'style' => 'min-width:70px', 'render' => function($item) {
            return '<span class="'.($item['is_active']?'tag-active':'tag-inactive').'">'.($item['is_active']?'Aktif':'Off').'</span>';
        }],
    ],
    'reorderUrl' => '/admin/testimonials/reorder',
    'baseEditUrl' => '/admin/testimonials',
    'baseDeleteUrl' => '/admin/testimonials',
    'items' => $items,
]) ?>
<?php \App\Helpers\View::partial('pagination', compact('pagination')); ?>
