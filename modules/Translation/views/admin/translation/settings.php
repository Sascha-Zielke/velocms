<?php declare(strict_types=1); ?>
<?php $this->extend('admin') ?>
<?php $this->section('content') ?>

<div class="vcms-page-header">
    <h1><?= t('translation.headline') ?> — <?= t('translation.settings') ?></h1>
    <a href="/admin/apps/translation" class="vcms-btn vcms-btn--ghost"><?= t('translation.dashboard') ?></a>
</div>

<form method="POST" action="/admin/apps/translation/settings" style="max-width:600px;margin-top:24px" id="trans-settings-form">
    <?= csrf_field() ?>

    <!-- Active Languages -->
    <div class="vcms-form-group">
        <label class="vcms-label"><?= t('translation.settings_languages') ?></label>

        <!-- Selected language tags -->
        <div id="selected-langs" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;min-height:36px"></div>

        <!-- Search input -->
        <div style="position:relative;margin-top:12px">
            <input type="text" id="lang-search" placeholder="Sprache suchen (z. B. FR, Französisch)…"
                   autocomplete="nope" class="vcms-input">
            <div id="lang-dropdown"
                 style="display:none;position:absolute;top:100%;left:0;right:0;z-index:200;
                        background:var(--vcms-card-bg,#fff);border:1px solid var(--vcms-border,#dde3ee);
                        border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.1);max-height:220px;overflow-y:auto">
            </div>
        </div>
        <p style="font-size:12px;color:var(--vcms-muted);margin-top:6px">
            <?= t('translation.settings_default_hint') ?>
        </p>
    </div>

    <!-- Default Language -->
    <div class="vcms-form-group" style="margin-top:20px">
        <label class="vcms-label" for="default_language"><?= t('translation.settings_default') ?></label>
        <select name="default_language" id="default_language" class="vcms-input">
            <?php foreach ($activeLangs as $lng): ?>
            <option value="<?= e($lng) ?>"<?= $lng === $defaultLang ? ' selected' : '' ?>>
                <?= t('lang.' . $lng, [], strtoupper($lng)) ?> (<?= strtoupper(e($lng)) ?>)
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <!-- Provider -->
    <div class="vcms-form-group" style="margin-top:20px">
        <label class="vcms-label" for="translation_provider"><?= t('translation.settings_provider') ?></label>
        <select name="translation_provider" id="translation_provider" class="vcms-input">
            <option value="deepl"     <?= $provider === 'deepl'     ? 'selected' : '' ?>><?= t('translation.provider_deepl') ?></option>
            <option value="anthropic" <?= $provider === 'anthropic' ? 'selected' : '' ?>><?= t('translation.provider_anthropic') ?></option>
        </select>
    </div>

    <!-- DeepL API Key -->
    <div class="vcms-form-group" style="margin-top:20px">
        <label class="vcms-label" for="deepl_api_key">DeepL API-Key</label>
        <input type="password" name="deepl_api_key" id="deepl_api_key" class="vcms-input"
               value="<?= e($deeplKey) ?>"
               placeholder="<?= $deeplKey !== '' ? '(gespeichert)' : 'DeepL-Auth-Key …' ?>">
        <p class="vcms-hint" style="margin-top:4px"><?= t('translation.settings_deepl_hint') ?></p>
    </div>

    <!-- Anthropic API Key -->
    <div class="vcms-form-group" style="margin-top:16px">
        <label class="vcms-label" for="anthropic_api_key">Anthropic API-Key</label>
        <input type="password" name="anthropic_api_key" id="anthropic_api_key" class="vcms-input"
               value="<?= e($anthropicKey) ?>"
               placeholder="<?= $anthropicKey !== '' ? '(gespeichert)' : 'sk-ant-…' ?>">
        <p class="vcms-hint" style="margin-top:4px"><?= t('translation.settings_anth_hint') ?></p>
    </div>

    <div style="margin-top:28px">
        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('action.save') ?></button>
    </div>
</form>

<script>
(function () {
    const LANGS = {
        'AR':'Arabisch','BG':'Bulgarisch','CS':'Tschechisch','DA':'Dänisch',
        'DE':'Deutsch','EL':'Griechisch','EN':'Englisch','ES':'Spanisch',
        'ET':'Estnisch','FI':'Finnisch','FR':'Französisch','HU':'Ungarisch',
        'ID':'Indonesisch','IT':'Italienisch','JA':'Japanisch','KO':'Koreanisch',
        'LT':'Litauisch','LV':'Lettisch','NB':'Norwegisch','NL':'Niederländisch',
        'PL':'Polnisch','PT':'Portugiesisch','RO':'Rumänisch','RU':'Russisch',
        'SK':'Slowakisch','SL':'Slowenisch','SR':'Serbisch','SV':'Schwedisch',
        'TR':'Türkisch','UK':'Ukrainisch','ZH':'Chinesisch'
    };

    const DEFAULT_LANG  = '<?= strtoupper(e($defaultLang)) ?>';
    const initial       = <?= json_encode(array_map('strtoupper', $activeLangs)) ?>;

    let selected = [...new Set(initial)];

    const container  = document.getElementById('selected-langs');
    const search     = document.getElementById('lang-search');
    const dropdown   = document.getElementById('lang-dropdown');
    const form       = document.getElementById('trans-settings-form');

    function renderTags() {
        container.innerHTML = '';
        selected.forEach(code => {
            const tag = document.createElement('span');
            tag.style.cssText = 'display:inline-flex;align-items:center;gap:5px;padding:4px 10px;' +
                'background:var(--vcms-accent,#3b6bdb);color:#fff;border-radius:20px;font-size:13px;font-weight:600';
            tag.textContent = code + ' – ' + (LANGS[code] || code);

            if (code !== DEFAULT_LANG) {
                const rm = document.createElement('button');
                rm.type = 'button';
                rm.textContent = '×';
                rm.style.cssText = 'background:none;border:none;color:#fff;cursor:pointer;font-size:15px;' +
                    'padding:0 0 0 2px;line-height:1';
                rm.addEventListener('click', () => {
                    selected = selected.filter(c => c !== code);
                    renderTags();
                });
                tag.appendChild(rm);
            }
            container.appendChild(tag);
        });
    }

    function showDropdown(query) {
        const q = query.trim().toLowerCase();
        if (q === '') { dropdown.style.display = 'none'; return; }

        const matches = Object.entries(LANGS).filter(([code, name]) =>
            !selected.includes(code) &&
            (code.toLowerCase().startsWith(q) || name.toLowerCase().includes(q))
        ).slice(0, 8);

        if (!matches.length) { dropdown.style.display = 'none'; return; }

        dropdown.innerHTML = '';
        matches.forEach(([code, name]) => {
            const item = document.createElement('div');
            item.style.cssText = 'padding:9px 14px;cursor:pointer;font-size:14px';
            item.textContent = code + ' – ' + name;
            item.addEventListener('mouseenter', () => item.style.background = 'var(--vcms-sidebar-hover,#f0f4ff)');
            item.addEventListener('mouseleave', () => item.style.background = '');
            item.addEventListener('mousedown', e => {
                e.preventDefault();
                selected.push(code);
                search.value = '';
                dropdown.style.display = 'none';
                renderTags();
            });
            dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
    }

    search.addEventListener('input', () => showDropdown(search.value));
    search.addEventListener('blur',  () => setTimeout(() => dropdown.style.display = 'none', 150));

    form.addEventListener('submit', () => {
        selected.forEach(code => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'active_languages[]';
            inp.value = code.toLowerCase();
            form.appendChild(inp);
        });
    });

    renderTags();
})();
</script>

<?php $this->endSection() ?>
