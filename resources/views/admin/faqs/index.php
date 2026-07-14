<div class="page-head"><h4 class="page-title">FAQ</h4><a href="<?= url('/admin/faqs/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a></div>
<?php if (empty($items)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-info-circle"></i></div><p>Belum ada FAQ.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th style="min-width:36px;"></th><th style="min-width:44px;">No</th><th style="min-width:150px;">Pertanyaan</th><th class="d-none d-md-table-cell" style="min-width:200px;">Jawaban</th><th style="min-width:70px;">Status</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody id="sortable-list" data-url="<?= url('/admin/faqs/reorder') ?>">
        <?php foreach ($items as $index => $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle" role="button" aria-grabbed="false" aria-label="Drag to reorder"></i></td>
                <td><?= (int)$item['sort_order'] ?? ($index + 1) ?></td>
                <td><?= e(mb_substr($item['question'], 0, 60)) ?><?= mb_strlen($item['question']) > 60 ? '...' : '' ?></td>
                <td class="d-none d-md-table-cell" style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e(mb_substr($item['answer'], 0, 100)) ?><?= mb_strlen($item['answer']) > 100 ? '...' : '' ?></td>
                <td><span class="<?= $item['is_active']?'tag-active':'tag-inactive' ?>"><?= $item['is_active']?'Aktif':'Off' ?></span></td>
                <td class="text-nowrap"><div class="d-flex gap-1"><a href="<?= url('/admin/faqs/'.$item['id'].'/edit') ?>" class="btn-act" aria-label="Edit FAQ"><i class="fas fa-pen"></i></a><form method="POST" action="<?= url('/admin/faqs/'.$item['id'].'/delete') ?>" class="delete-form"><?= \App\Helpers\Csrf::field() ?><button type="submit" class="btn-act danger" aria-label="Delete FAQ"><i class="fas fa-trash-can"></i></button></form></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>
