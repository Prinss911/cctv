<div class="page-head"><h4 class="page-title">Pengaturan</h4></div>
<form method="POST" action="<?= url('/admin/settings') ?>" enctype="multipart/form-data" class="admin-form">
    <?= \App\Helpers\Csrf::field() ?>
    <?php $tabLabels = ['general'=>'Umum','contact'=>'Kontak','seo'=>'SEO','social'=>'Sosial']; $first = true; ?>
    <ul class="nav nav-tabs settings-tabs mb-4">
        <?php foreach ($settings as $group => $fields): ?>
        <li class="nav-item"><button class="nav-link <?= $first?'active':'' ?>" data-bs-toggle="tab" data-bs-target="#tab-<?= e($group) ?>" type="button"><?= e($tabLabels[$group]??ucfirst($group)) ?></button></li>
        <?php $first = false; endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php $first = true; foreach ($settings as $group => $fields): ?>
        <div class="tab-pane fade <?= $first?'show active':'' ?>" id="tab-<?= e($group) ?>">
            <div class="admin-card"><div class="card-body">
                <?php foreach ($fields as $setting): ?>
                <div class="mb-4">
                    <label class="form-label"><?= e($setting['label'] ?? $setting['key']) ?></label>
                    <?php if ($setting['type'] === 'image'): ?>
                        <?php if ($setting['value']): ?><div class="img-preview-box mb-2"><img src="<?= upload_url($setting['value']) ?>" alt=""></div><?php endif; ?>
                        <input type="file" class="form-control" name="setting_<?= e($setting['key']) ?>" accept="image/*"><div class="form-text">Kosongkan jika tidak ingin mengganti.</div>
                    <?php elseif ($setting['type'] === 'textarea'): ?>
                        <textarea class="form-control" name="<?= e($setting['key']) ?>" rows="3"><?= e($setting['value']) ?></textarea>
                    <?php elseif ($setting['type'] === 'boolean'): ?>
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="<?= e($setting['key']) ?>" value="1" id="s_<?= e($setting['key']) ?>" <?= $setting['value']?'checked':'' ?>><label class="form-check-label" for="s_<?= e($setting['key']) ?>">Aktif</label></div>
                    <?php else: ?>
                        <input type="text" class="form-control" name="<?= e($setting['key']) ?>" value="<?= e($setting['value']) ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div></div>
        </div>
        <?php $first = false; endforeach; ?>
    </div>
    <div class="mt-4"><button type="submit" class="btn-admin btn-admin-fill"><i class="fas fa-check"></i> Simpan Pengaturan</button></div>
</form>
