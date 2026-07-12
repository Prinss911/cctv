<div class="page-head">
    <h4 class="page-title">Pengguna</h4>
    <a href="<?= url('/admin/users/create') ?>" class="btn-admin btn-admin-fill"><i class="fas fa-plus"></i> Tambah</a>
</div>
<?php if (empty($users)): ?>
<div class="empty-box"><div class="empty-icon"><i class="fas fa-users"></i></div><p>Belum ada pengguna.</p></div>
<?php else: ?>
<div class="table-responsive">
<div class="admin-table">
    <table class="table table-hover">
        <thead><tr><th>Nama</th><th class="d-none d-sm-table-cell">Email</th><th style="min-width:80px;">Role</th><th class="d-none d-md-table-cell">Dibuat</th><th class="text-nowrap" style="min-width:90px;">Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><strong><?= e($u['name']) ?></strong></td>
                <td class="d-none d-sm-table-cell"><?= e($u['email']) ?></td>
                <td><span class="tag-active" style="text-transform:lowercase;letter-spacing:0"><?= e($u['role']) ?></span></td>
                <td class="d-none d-md-table-cell" style="color:var(--warm-gray);font-size:0.82rem"><?= e(date('d M Y', strtotime($u['created_at']))) ?></td>
                <td class="text-nowrap">
                    <div class="d-flex gap-1">
                        <a href="<?= url('/admin/users/'.$u['id'].'/edit') ?>" class="btn-act" aria-label="Edit <?= e($u['name']) ?>"><i class="fas fa-pen"></i></a>
                        <?php if (\App\Helpers\Auth::user()['id'] != $u['id']): ?>
                        <form method="POST" action="<?= url('/admin/users/'.$u['id'].'/delete') ?>" class="delete-form" onsubmit="return confirm('Hapus pengguna <?= e($u['name']) ?>?')">
                            <?= \App\Helpers\Csrf::field() ?>
                            <button type="submit" class="btn-act danger" aria-label="Delete <?= e($u['name']) ?>"><i class="fas fa-trash-can"></i></button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php endif; ?>
