<div class="page-head"><h4 class="page-title">Client</h4><a href="<?= url('/admin/clients/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-handshake"></i></div><p>Belum ada client.</p></div>
<?php else: ?>
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th width="36"></th><th width="64">Logo</th><th>Nama</th><th>Website</th><th width="70">Status</th><th width="90">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/clients/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle"></i></td>
                <td><?php if ($item['logo']): ?><img src="<?= upload_url($item['logo']) ?>" alt="" class="table-thumb" style="object-fit:contain;background:var(--cream)"><?php endif; ?></td>
                <td><strong><?= e($item['name']) ?></strong></td>
                <td><?php if ($item['website']): ?><a href="<?= e($item['website']) ?>" target="_blank" style="color:var(--warm-gray);font-size:0.82rem"><i class="fas fa-external-link me-1"></i><?= e($item['website']) ?></a><?php else: ?><span style="color:var(--warm-gray)">—</span><?php endif; ?></td>
                <td><span class="<?= $item['is_active']?'tag-active':'tag-inactive' ?>"><?= $item['is_active']?'Aktif':'Off' ?></span></td>
                <td><div class="d-flex gap-1"><a href="<?= url('/admin/clients/'.$item['id'].'/edit') ?>" class="btn-act"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/clients/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
