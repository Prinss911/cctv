<?php \App\Helpers\View::partial('crud-index', [
    'title' => 'Slider',
    'addUrl' => '/admin/sliders/create',
    'emptyIcon' => 'fa-images',
    'emptyMessage' => 'Belum ada slider.',
    'columns' => [
        ['key' => 'image', 'label' => 'Gambar', 'class' => 'd-none d-sm-table-cell', 'style' => 'min-width:64px', 'render' => function($item) {
            return $item['image'] ? '<img src="'.upload_url($item['image']).'" alt="Thumbnail: '.e($item['title']).'" class="table-thumb">' : '';
        }],
        ['key' => 'title', 'label' => 'Judul', 'render' => function($item) {
            $html = '<strong>'.e($item['title']).'</strong>';
            if ($item['subtitle']) $html .= '<br><small style="color:var(--warm-gray)">'.e($item['subtitle']).'</small>';
            return $html;
        }],
        ['key' => 'status', 'label' => 'Status', 'class' => 'd-none d-md-table-cell', 'style' => 'min-width:80px', 'render' => function($item) {
            return '<span class="'.($item['is_active']?'tag-active':'tag-inactive').'">'.($item['is_active']?'Aktif':'Off').'</span>';
        }],
    ],
    'reorderUrl' => '/admin/sliders/reorder',
    'baseEditUrl' => '/admin/sliders',
    'baseDeleteUrl' => '/admin/sliders',
    'items' => $items,
]) ?>
<?php \App\Helpers\View::partial('pagination', compact('pagination')); ?>
