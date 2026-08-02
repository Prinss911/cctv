<?php \App\Helpers\View::partial('crud-index', [
    'title' => 'Paket Harga',
    'addUrl' => '/admin/pricing/create',
    'emptyIcon' => 'fa-tags',
    'emptyMessage' => 'Belum ada paket.',
    'columns' => [
        ['key' => 'name', 'label' => 'Judul', 'render' => function($item) {
            $html = '<strong>'.e($item['name']).'</strong>';
            if ($item['description']) {
                $html .= '<br><small style="color:var(--warm-gray)">'.e(mb_substr($item['description'], 0, 45)).'</small>';
            }
            return $html;
        }],
        ['key' => 'brand', 'label' => 'Brand', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:80px', 'render' => function($item) {
            return $item['brand_name'] ? e($item['brand_name']) : '<span style="color:var(--warm-gray)">—</span>';
        }],
        ['key' => 'camera', 'label' => 'Kamera', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:70px', 'render' => function($item) {
            return (string)(int)$item['camera_count'];
        }],
        ['key' => 'price', 'label' => 'Harga', 'style' => 'min-width:150px', 'render' => function($item) {
            $html = '<strong style="color:var(--green)">'.format_rupiah($item['price']).'</strong>';
            if (($item['price_original'] ?? 0) > $item['price']) {
                $html .= '<br><small style="text-decoration:line-through;color:var(--warm-gray)">'.format_rupiah($item['price_original']).'</small>';
            }
            return $html;
        }],
        ['key' => 'features', 'label' => 'Fitur', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:60px', 'render' => function($item) {
            return (string)count($item['features'] ?? []);
        }],
        ['key' => 'label', 'label' => 'Label', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:80px', 'render' => function($item) {
            return $item['is_featured'] ? '<span class="tag-featured">Unggulan</span>' : '<span style="color:var(--warm-gray)">—</span>';
        }],
        ['key' => 'status', 'label' => 'Status', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:70px', 'render' => function($item) {
            return '<span class="'.($item['is_active']?'tag-active':'tag-inactive').'">'.($item['is_active']?'Aktif':'Off').'</span>';
        }],
    ],
    'reorderUrl' => '/admin/pricing/reorder',
    'baseEditUrl' => '/admin/pricing',
    'baseDeleteUrl' => '/admin/pricing',
    'items' => $items,
]) ?>
<?php \App\Helpers\View::partial('pagination', compact('pagination')); ?>