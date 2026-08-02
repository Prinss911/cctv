<?php $crudLastPage = (int) ($pagination['last_page'] ?? 1); ?>
<div class="page-head">
    <h4 class="page-title"><?= e($title) ?></h4>
    <a href="<?= url($addUrl) ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> <?= e($addLabel ?? 'Tambah') ?></a>
</div>
<?php if (empty($items)): ?>
<div class="empty-box">
    <div class="empty-icon"><i class="fas <?= e($emptyIcon ?? 'fa-info-circle') ?>"></i></div>
    <p><?= e($emptyMessage ?? 'Belum ada data.') ?></p>
</div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr>
            <th style="min-width:36px;"></th>
            <?php foreach ($columns as $col): ?>
            <th<?php if (!empty($col['class'])): ?> class="<?= e($col['class']) ?>"<?php endif; ?><?php if (!empty($col['style'])): ?> style="<?= e($col['style']) ?>"<?php endif; ?>><?= e($col['label']) ?></th>
            <?php endforeach; ?>
            <th class="text-nowrap" style="min-width:90px;">Aksi</th>
        </tr></thead>
        <tbody id="sortable-list" data-url="<?= url($reorderUrl) ?>" data-last-page="<?= (int) $crudLastPage ?>">
        <?php foreach ($items as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
                <td><i class="fas fa-grip-vertical handle<?= $crudLastPage > 1 ? ' disabled' : '' ?>" role="button" aria-grabbed="false" aria-label="Drag to reorder"<?= $crudLastPage > 1 ? ' title="Urutan tidak bisa diubah saat daftar memiliki lebih dari 1 halaman"' : '' ?>></i></td>
                <?php foreach ($columns as $col): ?>
                <td<?php if (!empty($col['class'])): ?> class="<?= e($col['class']) ?>"<?php endif; ?>><?= $col['render']($item) ?></td>
                <?php endforeach; ?>
                <td class="text-nowrap">
                    <div class="d-flex gap-1">
                        <a href="<?= url($baseEditUrl . '/' . $item['id'] . '/edit') ?>" class="btn-act" aria-label="Edit"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="<?= url($baseDeleteUrl . '/' . $item['id'] . '/delete') ?>" class="delete-form" data-confirm="Yakin ingin menghapus?">
                            <?= \App\Helpers\Csrf::field() ?>
                            <button type="submit" class="btn-act danger" aria-label="Delete"><i class="fas fa-trash-can"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>
