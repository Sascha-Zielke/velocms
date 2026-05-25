<?php declare(strict_types=1); ?>
<div class="vcms-box-block vcms-box--<?= e($box['type']) ?>" data-box-id="<?= (int)$box['id'] ?>">
    <div class="vcms-box-toolbar">
        <span class="vcms-box-type"><?= e($box['type']) ?></span>
        <button class="vcms-btn vcms-btn--xs vcms-btn--danger btn-delete-box"
                data-box-id="<?= (int)$box['id'] ?>"><?= t('action.delete') ?></button>
    </div>

    <div class="vcms-box-form">
        <?php if ($box['type'] === 'text'): ?>
            <label class="vcms-label"><?= t('field.content') ?> (DE)</label>
            <textarea class="vcms-textarea" data-field="text" rows="4"><?= e($box['data']['content']['text'] ?? '') ?></textarea>
            <label class="vcms-label"><?= t('field.content') ?> (EN)</label>
            <textarea class="vcms-textarea" data-field="text_en" rows="4"><?= e($box['data']['content']['text_en'] ?? '') ?></textarea>

        <?php elseif ($box['type'] === 'image'): ?>
            <label class="vcms-label"><?= t('field.image_path') ?></label>
            <input type="text" class="vcms-input" data-field="src" placeholder="/uploads/..."
                   value="<?= e($box['data']['content']['src'] ?? '') ?>">
            <label class="vcms-label"><?= t('field.alt') ?></label>
            <input type="text" class="vcms-input" data-field="alt"
                   value="<?= e($box['data']['content']['alt'] ?? '') ?>">

        <?php elseif ($box['type'] === 'button'): ?>
            <label class="vcms-label"><?= t('field.label') ?> (DE)</label>
            <input type="text" class="vcms-input" data-field="label"
                   value="<?= e($box['data']['content']['label'] ?? '') ?>">
            <label class="vcms-label"><?= t('field.url') ?></label>
            <input type="text" class="vcms-input" data-field="href"
                   value="<?= e($box['data']['content']['href'] ?? '') ?>">

        <?php elseif ($box['type'] === 'video'): ?>
            <label class="vcms-label"><?= t('field.video_id') ?> (YouTube/Vimeo ID)</label>
            <input type="text" class="vcms-input" data-field="video_id"
                   value="<?= e($box['data']['content']['video_id'] ?? '') ?>">
            <label class="vcms-label"><?= t('field.video_provider') ?></label>
            <select class="vcms-select" data-field="provider">
                <option value="youtube" <?= ($box['data']['content']['provider'] ?? '') === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                <option value="vimeo"   <?= ($box['data']['content']['provider'] ?? '') === 'vimeo'   ? 'selected' : '' ?>>Vimeo</option>
            </select>
            <p class="vcms-hint"><?= t('editor.video_2click_hint') ?></p>

        <?php elseif ($box['type'] === 'spacer'): ?>
            <label class="vcms-label"><?= t('field.height') ?> (px)</label>
            <input type="number" class="vcms-input" data-field="height" min="10" max="500"
                   value="<?= (int)($box['data']['settings']['height'] ?? 40) ?>">
        <?php endif ?>

        <div class="vcms-form-group">
            <label class="vcms-label"><?= t('editor.cols') ?> (1-12)</label>
            <input type="number" class="vcms-input" data-field="cols" min="1" max="12"
                   value="<?= (int)($box['data']['layout']['cols'] ?? 12) ?>">
        </div>

        <button class="vcms-btn vcms-btn--sm vcms-btn--primary btn-save-box"
                data-box-id="<?= (int)$box['id'] ?>"
                data-type="<?= e($box['type']) ?>"><?= t('action.save') ?></button>
    </div>
</div>
