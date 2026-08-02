<?php \App\Helpers\View::partial('crud-index', [
    'title' => 'Fitur Tentang Kami',
    'addUrl' => '/admin/about-features/create',
    'addLabel' => 'Tambah',
    'emptyIcon' => 'fa-info-circle',
    'emptyMessage' => 'Belum ada fitur tentang kami.',
    'reorderUrl' => '/admin/about-features/reorder',
    'baseEditUrl' => '/admin/about-features',
    'baseDeleteUrl' => '/admin/about-features',
    'items' => $items,
    'columns' => [
        [
            'label' => 'Ikon',
            'style' => 'min-width:44px',
            'render' => function($item) {
                return '<i class="fas ' . e($item['icon']) . '" style="font-size:1.4rem;color:var(--accent,#d4a373)"></i>';
            },
        ],
        [
            'label' => 'Judul',
            'render' => function($item) {
                return '<strong>' . e($item['title']) . '</strong>';
            },
        ],
        [
            'label' => 'Deskripsi',
            'class' => 'd-none d-md-table-cell',
            'render' => function($item) {
                $desc = e(mb_substr($item['description'], 0, 80));
                if (mb_strlen($item['description']) > 80) {
                    $desc .= '...';
                }
                return $desc;
            },
        ],
        [
            'label' => 'Urutan',
            'style' => 'min-width:56px',
            'render' => function($item) {
                return (string)(int)$item['sort_order'];
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
