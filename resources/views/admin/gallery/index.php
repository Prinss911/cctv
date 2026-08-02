<?php \App\Helpers\View::partial('crud-index', [
    'title' => 'Galeri',
    'addUrl' => '/admin/gallery/create',
    'emptyIcon' => 'fa-camera',
    'emptyMessage' => 'Belum ada foto.',
    'columns' => [
        ['key' => 'image', 'label' => 'Foto', 'class' => 'd-none d-sm-table-cell', 'style' => 'min-width:64px', 'render' => function($item) {
            return $item['image'] ? '<img src="'.upload_url($item['image']).'" alt="Thumbnail: '.e($item['title'] ?: 'gallery').'" class="table-thumb">' : '';
        }],
        ['key' => 'title', 'label' => 'Judul', 'render' => function($item) {
            return '<strong>'.e($item['title'] ?: '—').'</strong>';
        }],
        ['key' => 'category', 'label' => 'Kategori', 'class' => 'd-none d-md-table-cell', 'render' => function($item) {
            return '<span style="font-size:0.78rem;color:var(--warm-gray)">'.e($item['category']).'</span>';
        }],
        ['key' => 'status', 'label' => 'Status', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:80px', 'render' => function($item) {
            return '<span class="'.($item['is_active']?'tag-active':'tag-inactive').'">'.($item['is_active']?'Aktif':'Off').'</span>';
        }],
    ],
    'reorderUrl' => '/admin/gallery/reorder',
    'baseEditUrl' => '/admin/gallery',
    'baseDeleteUrl' => '/admin/gallery',
    'items' => $items,
]) ?>
<?php \App\Helpers\View::partial('pagination', compact('pagination')); ?>
