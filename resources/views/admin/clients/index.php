<div class="page-head"><h4 class="page-title">Client</h4><a href="<?= url('/admin/clients/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-handshake"></i></div><p>Belum ada client.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th style="min-width:36px;"></th><th class="d-none d-sm-table-cell" style="min-width:64px;">Logo</th><th>Nama</th><th class="d-none d-md-table-cell">Website</th><th style="min-width:70px;">Status</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/clients/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle" role="button" aria-grabbed="false" aria-label="Drag to reorder"></i></td>
                <td class="d-none d-sm-table-cell"><?php if ($item['logo']): ?><img src="<?= upload_url($item['logo']) ?>" alt="Logo: <?= e($item['name']) ?>" class="table-thumb" style="object-fit:contain;background:var(--cream)"><?php endif; ?></td>
                <td><strong><?= e($item['name']) ?></strong></td>
                <td class="d-none d-md-table-cell"><?php if ($item['website']): ?><a href="<?= e($item['website']) ?>" target="_blank" style="color:var(--warm-gray);font-size:0.82rem" aria-label="Visit <?= e($item['name']) ?> website (opens in new tab)"><i class="fas fa-external-link me-1"></i><?= e($item['website']) ?></a><?php else: ?><span style="color:var(--warm-gray)">—</span><?php endif; ?></td>
                <td><span class="<?= $item['is_active']?'tag-active':'tag-inactive' ?>"><?= $item['is_active']?'Aktif':'Off' ?></span></td>
                <td class="text-nowrap"><div class="d-flex gap-1"><a href="<?= url('/admin/clients/'.$item['id'].'/edit') ?>" class="btn-act" aria-label="Edit <?= e($item['name']) ?>"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/clients/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger" aria-label="Delete <?= e($item['name']) ?>"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>
