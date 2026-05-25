<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('media.headline') ?></h1>
    <label class="vcms-btn vcms-btn--primary" for="vcms-upload-input">
        + <?= t('media.upload') ?>
    </label>
</div>

<!-- Upload Zone -->
<div class="vcms-upload-zone" id="vcms-upload-zone">
    <input type="file" id="vcms-upload-input" name="file"
           accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
           multiple style="display:none">
    <div class="vcms-upload-hint">
        <?= t('media.drop_hint') ?>
    </div>
    <div class="vcms-upload-progress" id="vcms-upload-progress" style="display:none">
        <div class="vcms-progress-bar" id="vcms-progress-bar"></div>
    </div>
</div>

<!-- Media Grid -->
<div class="vcms-media-grid" id="vcms-media-grid">
<?php foreach ($media as $item): ?>
    <div class="vcms-media-item" data-id="<?= (int)$item['id'] ?>">
        <?php if (str_starts_with($item['mime'], 'image/')): ?>
            <div class="vcms-media-thumb">
                <img src="<?= e($item['path']) ?>" alt="<?= e($item['alt_de']) ?>" loading="lazy">
            </div>
        <?php else: ?>
            <div class="vcms-media-thumb vcms-media-thumb--file">
                <span>📄</span>
            </div>
        <?php endif ?>
        <div class="vcms-media-info">
            <span class="vcms-media-name" title="<?= e($item['original']) ?>"><?= e(mb_strimwidth($item['original'], 0, 24, '…')) ?></span>
            <div class="vcms-media-actions">
                <button class="vcms-btn vcms-btn--sm vcms-btn--ghost"
                        onclick="vcmsMediaCopy('<?= e($item['path']) ?>')"
                        title="URL kopieren">📋</button>
                <button class="vcms-btn vcms-btn--sm vcms-btn--ghost"
                        onclick="vcmsMediaAlt(<?= (int)$item['id'] ?>, '<?= e(addslashes($item['alt_de'])) ?>', '<?= e(addslashes($item['alt_en'])) ?>')"
                        title="Alt-Text">✏️</button>
                <button class="vcms-btn vcms-btn--sm vcms-btn--danger"
                        onclick="vcmsMediaDelete(<?= (int)$item['id'] ?>)"
                        title="Löschen">🗑️</button>
            </div>
        </div>
    </div>
<?php endforeach ?>
<?php if (empty($media)): ?>
    <div class="vcms-media-empty"><?= t('media.empty') ?></div>
<?php endif ?>
</div>

<?php if ($total > $perPage): ?>
<div class="vcms-pagination">
    <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
        <a href="/admin/media?page=<?= $i ?>" class="vcms-btn vcms-btn--sm vcms-btn--ghost <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor ?>
</div>
<?php endif ?>

<!-- Alt-Text Modal -->
<div class="vcms-modal-overlay" id="vcms-alt-modal">
    <div class="vcms-modal" style="width:440px">
        <div class="vcms-modal-header">
            <h3><?= t('media.alt_title') ?></h3>
            <button class="vcms-modal-close" onclick="document.getElementById('vcms-alt-modal').classList.remove('open')">&times;</button>
        </div>
        <div class="vcms-modal-body">
            <input type="hidden" id="alt-media-id">
            <div class="vcms-field">
                <label><?= t('media.alt_de') ?></label>
                <input type="text" id="alt-de" maxlength="255">
            </div>
            <div class="vcms-field">
                <label><?= t('media.alt_en') ?></label>
                <input type="text" id="alt-en" maxlength="255">
            </div>
        </div>
        <div class="vcms-modal-footer">
            <button class="vcms-btn vcms-btn--ghost" onclick="document.getElementById('vcms-alt-modal').classList.remove('open')"><?= t('action.cancel') ?></button>
            <button class="vcms-btn vcms-btn--primary" onclick="vcmsMediaAltSave()"><?= t('action.save') ?></button>
        </div>
    </div>
</div>

<script>
const VCMS_CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// Upload handler
document.getElementById('vcms-upload-input').addEventListener('change', async (e) => {
    await vcmsUploadFiles(e.target.files);
});

// Drag-drop
const zone = document.getElementById('vcms-upload-zone');
zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', async (e) => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    await vcmsUploadFiles(e.dataTransfer.files);
});

async function vcmsUploadFiles(files) {
    const progress = document.getElementById('vcms-upload-progress');
    const bar      = document.getElementById('vcms-progress-bar');
    progress.style.display = 'block';

    for (const file of files) {
        const form = new FormData();
        form.append('file', file);
        form.append('_csrf', VCMS_CSRF);
        bar.style.width = '0%';
        bar.textContent = file.name;

        try {
            const res = await fetch('/admin/media/upload', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': VCMS_CSRF },
                body: form,
            });
            const data = await res.json();
            if (data.ok) {
                bar.style.width = '100%';
                bar.style.background = 'var(--vcms-success)';
            } else {
                bar.textContent = data.error ?? 'Fehler';
                bar.style.background = 'var(--vcms-danger)';
            }
        } catch {
            bar.textContent = 'Netzwerkfehler';
            bar.style.background = 'var(--vcms-danger)';
        }
    }

    setTimeout(() => { progress.style.display = 'none'; location.reload(); }, 800);
}

function vcmsMediaCopy(path) {
    const url = location.origin + path;
    navigator.clipboard?.writeText(url).then(() => alert('URL kopiert: ' + url)).catch(() => prompt('URL:', url));
}

function vcmsMediaAlt(id, altDe, altEn) {
    document.getElementById('alt-media-id').value = id;
    document.getElementById('alt-de').value = altDe;
    document.getElementById('alt-en').value = altEn;
    document.getElementById('vcms-alt-modal').classList.add('open');
}

async function vcmsMediaAltSave() {
    const id    = document.getElementById('alt-media-id').value;
    const altDe = document.getElementById('alt-de').value;
    const altEn = document.getElementById('alt-en').value;
    await fetch(`/admin/media/${id}/alt`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': VCMS_CSRF },
        body: JSON.stringify({ alt_de: altDe, alt_en: altEn }),
    });
    document.getElementById('vcms-alt-modal').classList.remove('open');
}

async function vcmsMediaDelete(id) {
    if (!confirm('<?= t('confirm.delete') ?>')) return;
    const res  = await fetch(`/admin/media/${id}/delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': VCMS_CSRF },
        body: JSON.stringify({}),
    });
    const data = await res.json();
    if (data.ok) document.querySelector(`.vcms-media-item[data-id="${id}"]`)?.remove();
}
</script>
<?php $this->endSection() ?>
