<div class="page-head"><h4 class="page-title">Edit Testimoni</h4><a href="<?= url('/admin/testimonials') ?>" class="btn-admin-back"><i class="fas fa-arrow-left"></i> Kembali</a></div>
<div class="admin-card"><div class="card-body">
    <form method="POST" action="<?= url('/admin/testimonials/'.$item['id']) ?>" enctype="multipart/form-data" class="admin-form">
        <?= \App\Helpers\Csrf::field() ?>
        <div class="mb-3"><label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label><input type="text" class="form-control" name="customer_name" value="<?= e($item['customer_name']) ?>" required></div>
        <div class="mb-3"><label class="form-label">Isi Testimoni</label><textarea class="form-control" name="content" rows="3"><?= e($item['content']) ?></textarea></div>
        <div class="mb-3"><label class="form-label">Screenshot</label><?php if ($item['screenshot']): ?><div class="img-preview-box mb-2"><img src="<?= upload_url($item['screenshot']) ?>" alt=""></div><?php endif; ?><input type="file" class="form-control" name="screenshot" accept="image/*"><div class="form-text">Kosongkan jika tidak ingin mengganti.</div></div>
        <div class="row"><div class="col-md-4 mb-3"><label class="form-label">Rating</label><select class="form-select" name="rating"><?php for($r=5;$r>=1;$r--): ?><option value="<?= e($r) ?>" <?= $item['rating']==$r ? 'selected' : '' ?>><?= e($r) ?></option><?php endfor; ?></select></div><div class="col-md-4 mb-3"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="<?= (int)$item['sort_order'] ?>" min="0"></div><div class="col-md-4 mb-3 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= $item['is_active']?'checked':'' ?>><label class="form-check-label" for="is_active">Aktif</label></div></div></div>
        <button type="submit" class="btn-admin btn-admin-fill"><i class="fas fa-check"></i> Simpan</button>
    </form>
</div></div>
