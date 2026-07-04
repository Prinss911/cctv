<div class="page-head"><h4 class="page-title">Ganti Password</h4><a href="<?= url('/admin') ?>" class="btn-admin-back"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="admin-card" style="max-width:500px"><div class="card-body">
    <form method="POST" action="<?= url('/admin/users/password') ?>" class="admin-form">
        <?= \App\Helpers\Csrf::field() ?>
        <div class="mb-3">
            <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
            <input type="password" class="form-control" name="current_password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
            <input type="password" class="form-control" name="new_password" required minlength="8">
            <div class="form-text">Minimal 8 karakter.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
            <input type="password" class="form-control" name="confirm_password" required minlength="8">
        </div>
        <button type="submit" class="btn-admin btn-admin-fill"><i class="fas fa-check"></i> Ganti Password</button>
    </form>
</div></div>
