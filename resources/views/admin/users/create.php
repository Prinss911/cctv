<div class="page-head"><h4 class="page-title">Tambah Pengguna</h4><a href="<?= url('/admin/users') ?>" class="btn-admin-back"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="admin-card"><div class="card-body">
    <form method="POST" action="<?= url('/admin/users') ?>" class="admin-form">
        <?= \App\Helpers\Csrf::field() ?>
        <div class="mb-3"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" class="form-control" name="name" required></div>
        <div class="mb-3"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" class="form-control" name="email" required></div>
        <div class="mb-3"><label class="form-label">Password <span class="text-danger">*</span></label><input type="password" class="form-control" name="password" required minlength="8"><div class="form-text">Minimal 8 karakter.</div></div>
        <div class="mb-3"><label class="form-label">Role</label>
            <select class="form-select" name="role">
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
                <option value="operator">Operator</option>
            </select>
        </div>
        <button type="submit" class="btn-admin btn-admin-fill w-100 w-auto-md"><i class="fas fa-check"></i> Simpan</button>
    </form>
</div></div>
