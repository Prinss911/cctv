<?php $flash = \App\Helpers\Flash::get(); ?>
<div class="login-page">
    <div class="login-box">
        <div class="login-mark"><i class="fas fa-shield-halved"></i></div>
        <h4 class="login-title"><?= e(setting('site_name', 'Bayu CCTV')) ?></h4>
        <p class="login-sub">Masuk ke panel admin</p>

        <?php if ($flash): ?>
        <div class="admin-alert alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show mb-3">
            <i class="fas fa-exclamation-triangle me-1"></i><?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/admin/login') ?>" class="admin-form">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="admin@bayucctv.com" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-right-to-bracket me-2"></i>Masuk
            </button>
            <div class="login-text-center mt-3">
                <a href="<?= url('/admin/forgot-password') ?>" class="text-muted">
                    <i class="fas fa-key me-1"></i>Lupa Password?
                </a>
            </div>
        </form>
    </div>
</div>
