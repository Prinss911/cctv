<div class="page-head"><h4 class="page-title">Testimoni</h4><a href="<?= url('/admin/testimonials/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-star"></i></div><p>Belum ada testimoni.</p></div>
<?php else: ?>
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th width="36"></th><th width="50"></th><th>Nama</th><th width="100">Rating</th><th>Konten</th><th width="70">Status</th><th width="90">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/testimonials/reorder') ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle"></i></td>
                <td><?php if ($item['screenshot']): ?><img src="<?= upload_url($item['screenshot']) ?>" alt="" class="table-thumb"><?php else: ?><div style="width:36px;height:36px;background:var(--navy);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem"><?= strtoupper(substr($item['customer_name'],0,1)) ?></div><?php endif; ?></td>
                <td><strong><?= e($item['customer_name']) ?></strong></td>
                <td><?php for($s=1;$s<=5;$s++): ?><i class="fas fa-star" style="font-size:0.65rem;color:<?= $s<=$item['rating']?'var(--rust)':'var(--border)' ?>"></i><?php endfor; ?></td>
                <td><small style="color:var(--warm-gray)"><?= e(mb_substr($item['content']??'',0,50)) ?><?= strlen($item['content']??'')>50?'...':'' ?></small></td>
                <td><span class="<?= $item['is_active']?'tag-active':'tag-inactive' ?>"><?= $item['is_active']?'Aktif':'Off' ?></span></td>
                <td><div class="d-flex gap-1"><a href="<?= url('/admin/testimonials/'.$item['id'].'/edit') ?>" class="btn-act"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/testimonials/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
