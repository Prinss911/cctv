<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Keunggulan</h4>
    <a href="<?= url('/admin/advantages') ?>" class="btn-admin-back">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form method="POST" action="<?= url('/admin/advantages/' . $item['id']) ?>" class="admin-form">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="mb-3">
                <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="<?= e($item['title']) ?>" required maxlength="255">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?= e($item['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Ikon</label>
                <?php \App\Helpers\View::partial('icon-picker', ['icons' => ['fa-shield-alt','fa-user-tie','fa-hard-hat','fa-hand-holding-heart','fa-headset','fa-layer-group','fa-check-circle','fa-cogs','fa-tools','fa-star','fa-heart','fa-thumbs-up','fa-check','fa-bolt','fa-gem','fa-leaf','fa-shield-halved','fa-clock','award'], 'selected' => $item['icon'] ?? 'fa-check-circle']); ?>
            </div>
            <div class="mb-3">
                <label for="border_color" class="form-label">Warna Border / Aksen</label>
                <div class="input-group">
                    <input type="color" class="form-control form-control-color" id="border_color" name="border_color" value="<?= e($item['border_color']) ?>" style="max-width:60px">
                    <input type="text" class="form-control" id="border_color_text" value="<?= e($item['border_color']) ?>" maxlength="7">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sort_order" class="form-label">Urutan</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= (int)$item['sort_order'] ?>" min="0">
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= $item['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-admin btn-admin-fill w-100 w-auto-md">
                <i class="fas fa-save me-1"></i>Simpan Perubahan
            </button>
        </form>
    </div>
</div>

