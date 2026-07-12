<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Client</h4>
    <a href="<?= url('/admin/clients') ?>" class="btn-admin-back">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form method="POST" action="<?= url('/admin/clients/' . $item['id']) ?>" enctype="multipart/form-data" class="admin-form">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="mb-3">
                <label for="name" class="form-label">Nama Client <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?= e($item['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                <?php if ($item['logo']): ?>
                <div class="mb-2 p-2 bg-light rounded d-inline-block">
                    <img src="<?= upload_url($item['logo']) ?>" alt="" style="max-height:80px;max-width:200px;object-fit:contain">
                </div>
                <br>
                <?php endif; ?>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                <div class="form-text">Kosongkan jika tidak ingin mengganti logo.</div>
            </div>
            <div class="mb-3">
                <label for="logo_url" class="form-label">Atau URL Logo</label>
                <input type="url" class="form-control" id="logo_url" name="logo_url" placeholder="https://example.com/logo.png">
                <div class="form-text">Isi jika ingin menggunakan URL, kosongkan jika upload file.</div>
            </div>
            <div class="mb-3">
                <label for="website" class="form-label">Website</label>
                <input type="url" class="form-control" id="website" name="website" value="<?= e($item['website']) ?>" placeholder="https://example.com">
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
