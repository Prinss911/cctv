<div class="page-head">
    <h4 class="page-title">Tambah Slider</h4>
    <a href="<?= url('/admin/sliders') ?>" class="btn-admin-back"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>
<div class="admin-card"><div class="card-body">
    <form method="POST" action="<?= url('/admin/sliders') ?>" enctype="multipart/form-data" class="admin-form">
        <?= \App\Helpers\Csrf::field() ?>
        <div class="mb-3"><label class="form-label">Judul <span class="text-danger">*</span></label><input type="text" class="form-control" name="title" required></div>
        <div class="mb-3"><label class="form-label">Subtitle</label><input type="text" class="form-control" name="subtitle"></div>
        <div class="mb-3"><label class="form-label">Tag <span class="text-danger">*</span></label><input type="text" class="form-control" name="tag" value="Keamanan Terpercaya" required><div class="form-text">Label kecil di atas judul, misal: "Keamanan Terpercaya"</div></div>
        <div class="mb-3"><label class="form-label">Gambar <span class="text-danger">*</span></label><input type="file" class="form-control" name="image" accept="image/*" required><div class="form-text">JPG, PNG, WebP. Maks 5MB.</div></div>
        <div class="mb-3">
            <label class="form-label">Atau URL Gambar</label>
            <input type="url" class="form-control" name="image_url" placeholder="https://example.com/gambar.jpg">
            <div class="form-text">Atau masukkan URL gambar dari internet.</div>
        </div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Teks Tombol</label><input type="text" class="form-control" name="button_text" value="Hubungi Kami"></div><div class="col-md-6 mb-3"><label class="form-label">URL Tombol</label><input type="text" class="form-control" name="button_url" value="#"></div></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="0" min="0"></div><div class="col-md-6 mb-3 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked><label class="form-check-label" for="is_active">Aktif</label></div></div></div>
        <button type="submit" class="btn-admin btn-admin-fill"><i class="fas fa-check"></i> Simpan</button>
    </form>
</div></div>
