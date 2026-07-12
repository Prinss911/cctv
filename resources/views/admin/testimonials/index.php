<div class="page-head"><h4 class="page-title">Testimoni</h4><a href="<?= url('/admin/testimonials/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-star"></i></div><p>Belum ada testimoni.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th style="min-width:36px;"></th><th class="d-none d-sm-table-cell" style="min-width:50px;"></th><th>Nama</th><th class="d-none d-md-table-cell" style="min-width:100px;">Rating</th><th class="d-none d-lg-table-cell">Konten</th><th style="min-width:70px;">Status</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/testimonials/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle" role="button" aria-grabbed="false" aria-label="Drag to reorder"></i></td>
                <td class="d-none d-sm-table-cell"><?php if ($item['screenshot']): ?><img src="<?= upload_url($item['screenshot']) ?>" alt="Screenshot: <?= e($item['customer_name']) ?>" class="table-thumb"><?php else: ?><div style="width:36px;height:36px;background:var(--navy);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem"><?= strtoupper(substr($item['customer_name'],0,1)) ?></div><?php endif; ?></td>
                <td><strong><?= e($item['customer_name']) ?></strong></td>
                <td class="d-none d-md-table-cell"><?php for($s=1;$s<=5;$s++): ?><i class="fas fa-star" style="font-size:0.65rem;color:<?= $s<=$item['rating']?'var(--rust)':'var(--border)' ?>"></i><?php endfor; ?></td>
                <td class="d-none d-lg-table-cell"><small style="color:var(--warm-gray)"><?= e(mb_substr($item['content']??'',0,50)) ?><?= strlen($item['content']??'')>50?'...':'' ?></small></td>
                <td><span class="<?= $item['is_active']?'tag-active':'tag-inactive' ?>"><?= $item['is_active']?'Aktif':'Off' ?></span></td>
                <td class="text-nowrap"><div class="d-flex gap-1"><a href="<?= url('/admin/testimonials/'.$item['id'].'/edit') ?>" class="btn-act" aria-label="Edit <?= e($item['customer_name']) ?>"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/testimonials/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger" aria-label="Delete <?= e($item['customer_name']) ?>"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>
