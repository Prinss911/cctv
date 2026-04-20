<div class="page-head"><h4 class="page-title">Tambah Testimoni</h4><a href="<?= url('/admin/testimonials') ?>" class="btn-admin-back"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="admin-card"><div class="card-body">
    <form method="POST" action="<?= url('/admin/testimonials') ?>" enctype="multipart/form-data" class="admin-form">
        <?= \App\Helpers\Csrf::field() ?>
        <div class="mb-3"><label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label><input type="text" class="form-control" name="customer_name" required></div>
        <div class="mb-3"><label class="form-label">Isi Testimoni</label><textarea class="form-control" name="content" rows="3"></textarea></div>
        <div class="mb-3"><label class="form-label">Screenshot</label><input type="file" class="form-control" name="screenshot" accept="image/*"><div class="form-text">Opsional. JPG/PNG, maks 5MB.</div></div>
        <div class="row"><div class="col-md-4 mb-3"><label class="form-label">Rating</label><select class="form-select" name="rating"><option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option></select></div><div class="col-md-4 mb-3"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="0" min="0"></div><div class="col-md-4 mb-3 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked><label class="form-check-label" for="is_active">Aktif</label></div></div></div>
        <button type="submit" class="btn-admin btn-admin-fill"><i class="fas fa-check"></i> Simpan</button>
    </form>
</div></div>
