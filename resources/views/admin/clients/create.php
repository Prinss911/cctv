<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tambah Client</h4>
    <a href="<?= url('/admin/clients') ?>" class="btn-admin-back">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form method="POST" action="<?= url('/admin/clients') ?>" enctype="multipart/form-data" class="admin-form">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="mb-3">
                <label for="name" class="form-label">Nama Client <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                <div class="form-text">Format: JPG, PNG, WebP, SVG. Maks 5MB. Rekomendasi: background transparan (PNG).</div>
            </div>
            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="url" class="form-control" id="website" name="website" placeholder="https://example.com">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sort_order" class="form-label">Urutan</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-admin btn-admin-fill">
                <i class="fas fa-save me-1"></i>Simpan
            </button>
        </form>
    </div>
</div>
