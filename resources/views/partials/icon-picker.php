<div class="row g-2 mb-2" id="icon-picker">
    <?php foreach ($icons as $ic): ?>
    <div class="col-auto">
        <label class="icon-option <?= $ic === $selected ? 'selected' : '' ?>" data-icon="<?= $ic ?>">
            <input type="radio" name="<?= $name ?>" value="<?= $ic ?>" <?= $ic === $selected ? 'checked' : '' ?>>
            <?php if ($ic === 'award'): ?>
            <i class="fas fa-award"></i>
            <?php else: ?>
            <i class="fas <?= $ic ?>"></i>
            <?php endif; ?>
        </label>
    </div>
    <?php endforeach; ?>
</div>
<input type="text" class="form-control mt-2" id="icon_custom" placeholder="Atau ketik kelas ikon kustom (cth: fa-shield-alt)" value="<?= e($selected) ?>">
<div class="form-text">Pilih ikon dari daftar atau ketik kelas Font Awesome kustom.</div>
