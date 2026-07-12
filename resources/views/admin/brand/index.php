<div class="page-head"><h4 class="page-title">Brand / Merek</h4><a href="<?= url('/admin/brands/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($brands)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-tags"></i></div><p>Belum ada brand.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th style="min-width:36px;"></th><th>Brand</th><th class="d-none d-sm-table-cell" style="min-width:60px;">Logo</th><th class="d-none d-md-table-cell" style="min-width:60px;">Paket</th><th style="min-width:70px;">Status</th><th class="d-none d-md-table-cell" style="min-width:60px;">Urutan</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/brands/reorder') ?>">
        <?php foreach ($brands as $brand): ?>
            <tr data-id="<?= $brand['id'] ?>">
                <td><i class="fas fa-grip-vertical handle" role="button" aria-grabbed="false" aria-label="Drag to reorder"></i></td>
                <td><strong><?= e($brand['name']) ?></strong></td>
                <td class="d-none d-sm-table-cell"><?php if ($brand['logo']): ?><img src="<?= upload_url($brand['logo']) ?>" alt="Logo: <?= e($brand['name']) ?>" style="height: 32px;"><?php else: ?><span style="color:var(--warm-gray)">—</span><?php endif; ?></td>
                <td class="d-none d-md-table-cell"><?= (int)$brand['package_count'] ?></td>
                <td><span class="<?= $brand['is_active']?'tag-active':'tag-inactive' ?>"><?= $brand['is_active']?'Aktif':'Off' ?></span></td>
                <td class="d-none d-md-table-cell"><?= (int)$brand['sort_order'] ?></td>
                <td class="text-nowrap"><div class="d-flex gap-1"><a href="<?= url('/admin/brands/'.$brand['id'].'/edit') ?>" class="btn-act" aria-label="Edit <?= e($brand['name']) ?>"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/brands/'.$brand['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger" aria-label="Delete <?= e($brand['name']) ?>"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>