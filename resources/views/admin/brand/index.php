<div class="page-head"><h4 class="page-title">Brand / Merek</h4><a href="<?= url('/admin/brands/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($brands)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-tags"></i></div><p>Belum ada brand.</p></div>
<?php else: ?>
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th width="36"></th><th>Brand</th><th width="80">Logo</th><th width="80">Paket</th><th width="70">Status</th><th width="80">Urutan</th><th width="90">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/brands/reorder') ?>">
        <?php foreach ($brands as $brand): ?>
            <tr data-id="<?= $brand['id'] ?>">
                <td><i class="fas fa-grip-vertical handle"></i></td>
                <td><strong><?= e($brand['name']) ?></strong></td>
                <td><?php if ($brand['logo']): ?><img src="<?= url('/uploads/' . $brand['logo']) ?>" alt="<?= e($brand['name']) ?>" style="height: 32px;"><?php else: ?><span style="color:var(--warm-gray)">—</span><?php endif; ?></td>
                <td><?= (int)$brand['package_count'] ?></td>
                <td><span class="<?= $brand['is_active']?'tag-active':'tag-inactive' ?>"><?= $brand['is_active']?'Aktif':'Off' ?></span></td>
                <td><?= (int)$brand['sort_order'] ?></td>
                <td><div class="d-flex gap-1"><a href="<?= url('/admin/brands/'.$brand['id'].'/edit') ?>" class="btn-act"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/brands/'.$brand['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>