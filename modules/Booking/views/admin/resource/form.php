<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<?php
$isEdit  = $resource !== null;
$action  = $isEdit
    ? '/admin/apps/booking/resources/update/' . $resource->id
    : '/admin/apps/booking/resources/store';
$weekdayLabels = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
?>

<div class="vcms-page-header">
    <h1><?= $isEdit ? t('action.edit') : t('action.new') ?></h1>
    <a href="/admin/apps/booking/resources" class="vcms-btn vcms-btn--ghost"><?= t('action.back') ?></a>
</div>

<form method="POST" action="<?= e($action) ?>" class="vcms-form" style="max-width:600px">
    <?= csrf_field() ?>

    <div class="vcms-form-group">
        <label><?= t('field.name') ?></label>
        <input type="text" name="name" value="<?= $isEdit ? e($resource->name) : '' ?>" required class="vcms-input">
    </div>

    <div class="vcms-form-group">
        <label><?= t('booking.field_resource') ?> Typ</label>
        <select name="type" class="vcms-select">
            <?php foreach ($types as $type): ?>
            <option value="<?= e($type->value) ?>"<?= $isEdit && $resource->type === $type ? ' selected' : '' ?>>
                <?= e($type->label()) ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="vcms-form-group">
        <label><?= t('booking.field_template') ?></label>
        <input type="text" name="template_key" value="<?= $isEdit ? e($resource->templateKey) : 'generic' ?>" class="vcms-input">
    </div>

    <?php if ($isEdit): ?>
    <div class="vcms-form-group">
        <label>
            <input type="checkbox" name="is_active" value="1"<?= $resource->isActive ? ' checked' : '' ?>>
            Aktiv
        </label>
    </div>
    <?php endif ?>

    <hr style="margin:24px 0">

    <h2 style="font-size:1rem;margin-bottom:12px"><?= t('booking.field_duration') ?> / Slots</h2>
    <p style="color:#888;font-size:0.85rem;margin-bottom:16px">Verfügbare Zeitfenster pro Wochentag</p>

    <div id="slots-container">
        <?php foreach ($slots as $slot): ?>
        <div class="vcms-slot-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center">
            <select name="slot_weekday[]" class="vcms-select" style="width:80px">
                <?php for ($d = 0; $d <= 6; $d++): ?>
                <option value="<?= $d ?>"<?= $slot->weekday === $d ? ' selected' : '' ?>><?= $weekdayLabels[$d] ?></option>
                <?php endfor ?>
            </select>
            <input type="time" name="slot_start[]" value="<?= e(substr($slot->startTime, 0, 5)) ?>" class="vcms-input" style="width:110px">
            <span>–</span>
            <input type="time" name="slot_end[]" value="<?= e(substr($slot->endTime, 0, 5)) ?>" class="vcms-input" style="width:110px">
            <button type="button" onclick="this.closest('.vcms-slot-row').remove()" class="vcms-btn vcms-btn--sm vcms-btn--danger">✕</button>
        </div>
        <?php endforeach ?>
    </div>

    <button type="button" id="add-slot" class="vcms-btn vcms-btn--ghost vcms-btn--sm" style="margin-top:8px">+ Slot hinzufügen</button>

    <div class="vcms-form-actions" style="margin-top:24px">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>
</form>

<script>
const weekdays = <?= json_encode($weekdayLabels) ?>;
document.getElementById('add-slot').addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'vcms-slot-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;align-items:center';
    const sel = weekdays.map((d,i) => `<option value="${i}">${d}</option>`).join('');
    row.innerHTML = `<select name="slot_weekday[]" class="vcms-select" style="width:80px">${sel}</select>
        <input type="time" name="slot_start[]" class="vcms-input" style="width:110px">
        <span>–</span>
        <input type="time" name="slot_end[]" class="vcms-input" style="width:110px">
        <button type="button" onclick="this.closest('.vcms-slot-row').remove()" class="vcms-btn vcms-btn--sm vcms-btn--danger">✕</button>`;
    document.getElementById('slots-container').appendChild(row);
});
</script>

<?php $this->endSection() ?>
