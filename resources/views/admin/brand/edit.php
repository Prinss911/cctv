<div class="page-head"><h4 class="page-title">Edit Brand: <?= e($brand['name']) ?></h4><a href="<?= url('/admin/brands') ?>" class="btn-admin-back"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="admin-card"><div class="card-body">
    <form method="POST" action="<?= url('/admin/brands/'.$brand['id']) ?>" class="admin-form" enctype="multipart/form-data">
        <?= \App\Helpers\Csrf::field() ?>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Nama Brand <span class="text-danger">*</span></label><input type="text" class="form-control" name="name" value="<?= e($brand['name']) ?>" required></div><div class="col-md-6 mb-3"><label class="form-label">Slug <span class="text-danger">*</span></label><input type="text" class="form-control" name="slug" value="<?= e($brand['slug']) ?>" required></div></div>
        <div class="mb-3">
            <label class="form-label">Logo</label>
            <?php if ($brand['logo']): ?>
            <div class="mb-2 p-2 bg-light rounded d-inline-block">
                <img src="<?= upload_url($brand['logo']) ?>" alt="" style="max-height:80px;max-width:200px;object-fit:contain">
            </div>
            <br>
            <?php endif; ?>
            <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png,image/webp,image/gif">
            <div class="form-text">Kosongkan jika tidak ingin mengganti logo.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Atau URL Logo</label>
            <input type="url" class="form-control" name="logo_url" placeholder="https://example.com/logo.png">
            <div class="form-text">Isi jika ingin menggunakan URL, kosongkan jika upload file.</div>
        </div>
        <div class="row"><div class="col-md-4 mb-3"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="<?= (int)$brand['sort_order'] ?>" min="0"></div><div class="col-md-4 mb-3 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= $brand['is_active'] ? 'checked' : '' ?>><label class="form-check-label" for="is_active">Aktif</label></div></div></div>
        <button type="submit" class="btn-admin btn-admin-fill w-100 w-auto-md"><i class="fas fa-check"></i> Simpan</button>
    </form>
</div></div>
