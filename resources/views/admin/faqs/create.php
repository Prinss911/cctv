<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Tambah FAQ</h4>
    <a href="<?= url('/admin/faqs') ?>" class="btn-admin-back">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form method="POST" action="<?= url('/admin/faqs') ?>" class="admin-form">
            <?= \App\Helpers\Csrf::field() ?>
            <div class="mb-3">
                <label for="question" class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                <textarea class="form-control" id="question" name="question" rows="3" required placeholder="Tulis pertanyaan..."></textarea>
            </div>
            <div class="mb-3">
                <label for="answer" class="form-label">Jawaban <span class="text-danger">*</span></label>
                <textarea class="form-control" id="answer" name="answer" rows="5" required placeholder="Tulis jawaban detail..."></textarea>
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
            <button type="submit" class="btn-admin btn-admin-fill w-100 w-auto-md">
                <i class="fas fa-save me-1"></i>Simpan
            </button>
        </form>
    </div>
</div>
