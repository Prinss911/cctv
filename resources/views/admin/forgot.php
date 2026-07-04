<div class="login-page">
    <div class="login-box">
        <div class="login-mark"><i class="fas fa-key"></i></div>
        <h4 class="login-title">Lupa Password?</h4>
        <p class="login-sub">Masukkan email untuk mendapatkan link reset password</p>

        <?php if ($flash = \App\Helpers\Flash::get()): ?>
        <div class="admin-alert alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show mb-3">
            <i class="fas fa-exclamation-triangle me-1"></i><?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/admin/forgot-password') ?>" class="admin-form">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="admin@bayucctv.com" required autofocus>
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-paper-plane me-2"></i>Kirim Link Reset
            </button>
        </form>
        <div class="login-text-center mt-3">
            <a href="<?= url('/admin/login') ?>" class="text-muted">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
            </a>
        </div>
    </div>
</div>
