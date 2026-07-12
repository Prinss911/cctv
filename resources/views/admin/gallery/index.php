<div class="page-head">
    <h4 class="page-title">Galeri</h4>
    <a href="<?= url('/admin/gallery/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a>
</div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-camera"></i></div><p>Belum ada foto.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th style="min-width:36px;"></th><th class="d-none d-sm-table-cell" style="min-width:64px;">Foto</th><th>Judul</th><th class="d-none d-md-table-cell">Kategori</th><th class="d-none d-md-table-cell" style="min-width:80px;">Status</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/gallery/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle" role="button" aria-grabbed="false" aria-label="Drag to reorder"></i></td>
                <td class="d-none d-sm-table-cell"><?php if ($item['image']): ?><img src="<?= upload_url($item['image']) ?>" alt="Thumbnail: <?= e($item['title'] ?: 'gallery') ?>" class="table-thumb"><?php endif; ?></td>
                <td><strong><?= e($item['title'] ?: '—') ?></strong></td>
                <td class="d-none d-md-table-cell"><span style="font-size:0.78rem;color:var(--warm-gray)"><?= e($item['category']) ?></span></td>
                <td class="d-none d-md-table-cell"><span class="<?= $item['is_active'] ? 'tag-active' : 'tag-inactive' ?>"><?= $item['is_active'] ? 'Aktif' : 'Off' ?></span></td>
                <td class="text-nowrap"><div class="d-flex gap-1"><a href="<?= url('/admin/gallery/'.$item['id'].'/edit') ?>" class="btn-act" aria-label="Edit <?= e($item['title'] ?: 'item') ?>"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/gallery/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger" aria-label="Delete <?= e($item['title'] ?: 'item') ?>"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>
