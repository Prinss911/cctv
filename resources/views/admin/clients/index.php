<?php \App\Helpers\View::partial('crud-index', [
    'title' => 'Client',
    'addUrl' => '/admin/clients/create',
    'addLabel' => 'Tambah',
    'emptyIcon' => 'fa-handshake',
    'emptyMessage' => 'Belum ada client.',
    'reorderUrl' => '/admin/clients/reorder',
    'baseEditUrl' => '/admin/clients',
    'baseDeleteUrl' => '/admin/clients',
    'items' => $items,
    'columns' => [
        [
            'label' => 'Logo',
            'class' => 'd-none d-sm-table-cell',
            'style' => 'min-width:64px',
            'render' => function($item) {
                if ($item['logo']) {
                    return '<img src="' . upload_url($item['logo']) . '" alt="Logo: ' . e($item['name']) . '" class="table-thumb" style="object-fit:contain;background:var(--cream)">';
                }
                return '';
            },
        ],
        [
            'label' => 'Nama',
            'render' => function($item) {
                return '<strong>' . e($item['name']) . '</strong>';
            },
        ],
        [
            'label' => 'Website',
            'class' => 'd-none d-md-table-cell',
            'render' => function($item) {
                if ($item['website']) {
                    return '<a href="' . e($item['website']) . '" target="_blank" style="color:var(--warm-gray);font-size:0.82rem" aria-label="Visit ' . e($item['name']) . ' website (opens in new tab)"><i class="fas fa-external-link me-1"></i>' . e($item['website']) . '</a>';
                }
                return '<span style="color:var(--warm-gray)">—</span>';
            },
        ],
        [
            'label' => 'Status',
            'style' => 'min-width:70px',
            'render' => function($item) {
                $class = $item['is_active'] ? 'tag-active' : 'tag-inactive';
                $text = $item['is_active'] ? 'Aktif' : 'Off';
                return '<span class="' . $class . '">' . $text . '</span>';
            },
        ],
    ],
]); ?>
<?php \App\Helpers\View::partial('pagination', compact('pagination')); ?>
