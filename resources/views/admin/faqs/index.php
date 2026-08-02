<?php $counter = 1; ?>
<?php \App\Helpers\View::partial('crud-index', [
    'title' => 'FAQ',
    'addUrl' => '/admin/faqs/create',
    'addLabel' => 'Tambah',
    'emptyIcon' => 'fa-info-circle',
    'emptyMessage' => 'Belum ada FAQ.',
    'reorderUrl' => '/admin/faqs/reorder',
    'baseEditUrl' => '/admin/faqs',
    'baseDeleteUrl' => '/admin/faqs',
    'items' => $items,
    'columns' => [
        [
            'label' => 'No',
            'style' => 'min-width:44px',
            'render' => function($item) use (&$counter) {
                return (string)($item['sort_order'] ?? $counter++);
            },
        ],
        [
            'label' => 'Pertanyaan',
            'style' => 'min-width:150px',
            'render' => function($item) {
                $q = e(mb_substr($item['question'], 0, 60));
                if (mb_strlen($item['question']) > 60) {
                    $q .= '...';
                }
                return $q;
            },
        ],
        [
            'label' => 'Jawaban',
            'class' => 'd-none d-md-table-cell',
            'style' => 'min-width:200px',
            'render' => function($item) {
                $a = e(mb_substr($item['answer'], 0, 100));
                if (mb_strlen($item['answer']) > 100) {
                    $a .= '...';
                }
                return $a;
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
