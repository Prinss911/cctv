<div class="login-page">
    <div class="login-box">
        <div class="login-mark"><i class="fas fa-key"></i></div>
        <h4 class="login-title">Reset Password</h4>
        <p class="login-sub">Masukkan password baru untuk akun Anda</p>

        <?php if ($flash = \App\Helpers\Flash::get()): ?>
        <div class="admin-alert alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show mb-3">
            <i class="fas fa-exclamation-triangle me-1"></i><?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/admin/reset-password') ?>" class="admin-form">
            <?= \App\Helpers\Csrf::field() ?>
            <input type="hidden" name="token" value="<?= e($_GET['token'] ?? '') ?>">
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Masukkan password baru" required minlength="6">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                       placeholder="Ulangi password baru" required minlength="6">
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-save me-2"></i>Simpan Password Baru
            </button>
        </form>
        <div class="login-text-center mt-3">
            <a href="<?= url('/admin/login') ?>" class="text-muted">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
            </a>
        </div>
    </div>
</div>
