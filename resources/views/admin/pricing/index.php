<div class="page-head"><h4 class="page-title">Paket Harga</h4><a href="<?= url('/admin/pricing/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-tags"></i></div><p>Belum ada paket.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th style="min-width:36px;"></th><th>Judul</th><th class="d-none d-md-table-cell" style="min-width:80px;">Brand</th><th class="d-none d-md-table-cell" style="min-width:70px;">Kamera</th><th style="min-width:150px;">Harga</th><th class="d-none d-md-table-cell" style="min-width:60px;">Fitur</th><th class="d-none d-md-table-cell" style="min-width:80px;">Label</th><th class="d-none d-md-table-cell" style="min-width:70px;">Status</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/pricing/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle" role="button" aria-grabbed="false" aria-label="Drag to reorder"></i></td>
                <td><strong><?= e($item['name']) ?></strong><?php if ($item['description']): ?><br><small style="color:var(--warm-gray)"><?= e(mb_substr($item['description'],0,45)) ?></small><?php endif; ?></td>
                <td class="d-none d-md-table-cell"><?php if ($item['brand_name']): ?><?= e($item['brand_name']) ?><?php else: ?><span style="color:var(--warm-gray)">—</span><?php endif; ?></td>
                <td class="d-none d-md-table-cell"><?= (int)$item['camera_count'] ?></td>
                <td><strong style="color:var(--green)"><?= format_rupiah($item['price']) ?></strong><?php if ($item['price_original']>$item['price']): ?><br><small style="text-decoration:line-through;color:var(--warm-gray)"><?= format_rupiah($item['price_original']) ?></small><?php endif; ?></td>
                <td class="d-none d-md-table-cell"><?= count($item['features']??[]) ?></td>
                <td class="d-none d-md-table-cell"><?= $item['is_featured']?'<span class="tag-featured">Unggulan</span>':'<span style="color:var(--warm-gray)">—</span>' ?></td>
                <td class="d-none d-md-table-cell"><span class="<?= $item['is_active']?'tag-active':'tag-inactive' ?>"><?= $item['is_active']?'Aktif':'Off' ?></span></td>
                <td class="text-nowrap"><div class="d-flex gap-1"><a href="<?= url('/admin/pricing/'.$item['id'].'/edit') ?>" class="btn-act" aria-label="Edit <?= e($item['name']) ?>"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/pricing/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger" aria-label="Delete <?= e($item['name']) ?>"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>