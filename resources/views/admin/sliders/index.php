<div class="page-head">
    <h4 class="page-title">Slider</h4>
    <a href="<?= url('/admin/sliders/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a>
</div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-images"></i></div><p>Belum ada slider.</p></div>
<?php else: ?>
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th width="36"></th><th width="64">Gambar</th><th>Judul</th><th width="80">Status</th><th width="90">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/sliders/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle"></i></td>
                <td><?php if ($item['image']): ?><img src="<?= upload_url($item['image']) ?>" alt="" class="table-thumb"><?php endif; ?></td>
                <td><strong><?= e($item['title']) ?></strong><?php if ($item['subtitle']): ?><br><small style="color:var(--warm-gray)"><?= e($item['subtitle']) ?></small><?php endif; ?></td>
                <td><span class="<?= $item['is_active'] ? 'tag-active' : 'tag-inactive' ?>"><?= $item['is_active'] ? 'Aktif' : 'Off' ?></span></td>
                <td><div class="d-flex gap-1"><a href="<?= url('/admin/sliders/'.$item['id'].'/edit') ?>" class="btn-act"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/sliders/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
