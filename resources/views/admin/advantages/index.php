<div class="page-head"><h4 class="page-title">Keunggulan</h4><a href="<?= url('/admin/advantages/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-star"></i></div><p>Belum ada keunggulan.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th style="min-width:36px;"></th><th style="min-width:44px;">Ikon</th><th>Judul</th><th class="d-none d-md-table-cell">Deskripsi</th><th style="min-width:56px;">Urutan</th><th style="min-width:70px;">Status</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/advantages/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle" role="button" aria-grabbed="false" aria-label="Drag to reorder"></i></td>
                <td><i class="fas <?= e($item['icon']) ?>" style="color:<?= e($item['border_color']) ?>;font-size:1.4rem"></i></td>
                <td><strong><?= e($item['title']) ?></strong></td>
                <td class="d-none d-md-table-cell" style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e(mb_substr($item['description'], 0, 80)) ?><?= mb_strlen($item['description']) > 80 ? '...' : '' ?></td>
                <td><?= (int)$item['sort_order'] ?></td>
                <td><span class="<?= $item['is_active']?'tag-active':'tag-inactive' ?>"><?= $item['is_active']?'Aktif':'Off' ?></span></td>
                <td class="text-nowrap"><div class="d-flex gap-1"><a href="<?= url('/admin/advantages/'.$item['id'].'/edit') ?>" class="btn-act" aria-label="Edit <?= e($item['title']) ?>"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/advantages/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger" aria-label="Delete <?= e($item['title']) ?>"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>
