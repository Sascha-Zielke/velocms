/**
 * VeloCMS Visual Editor
 *
 * Injected into the frontend page when a logged-in admin accesses /{slug}?ve_edit=1.
 * Finds elements with [data-ve-box] and [data-ve-section] and adds overlay controls.
 *
 * Communicates with:
 *   GET  /admin/pages/box/{id}/data  → fetch current box JSON
 *   POST /admin/pages/box/{id}/save  → save updated box JSON
 */

(function () {
    'use strict';

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ── State ──────────────────────────────────────────────────────────────
    let activeBoxId  = null;
    let activeBoxData = null;

    // ── Helpers ────────────────────────────────────────────────────────────
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function setStatus(msg, cls) {
        const el = document.getElementById('ve-status');
        if (!el) return;
        el.textContent = msg;
        el.className = 've-status' + (cls ? ' ' + cls : '');
        if (cls === 'is-saved') {
            setTimeout(() => {
                el.textContent = 'Visual Editor aktiv';
                el.className = 've-status';
            }, 2500);
        }
    }

    function fieldLabel(key) {
        const map = {
            headline:  'Überschrift', subline:   'Unterzeile',   tagline:  'Tagline',
            title:     'Titel',       subtitle:   'Untertitel',   label:    'Label',
            text:      'Text',        html:       'Inhalt (HTML)',
            desc:      'Beschreibung',name:       'Name',         icon:     'Icon',
            price:     'Preis',       genre:      'Genre',        role:     'Rolle',
            address:   'Adresse',     email:      'E-Mail',       hours:    'Öffnungszeiten',
            items:     'Punkte (als JSON-Array)',
        };
        return map[key] || key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
    }

    // ── Toolbar ────────────────────────────────────────────────────────────
    function buildToolbar() {
        document.body.classList.add('ve-active');

        const bar = document.createElement('div');
        bar.className = 've-toolbar';
        bar.innerHTML = `
            <div class="ve-toolbar__inner">
                <a href="/admin/pages" class="ve-back-btn">← Alle Seiten</a>
                <span class="ve-page-name" id="ve-page-name">${escHtml(document.title.split('—')[0].trim())}</span>
                <span class="ve-hint">Hover über eine Box → ⚙ klicken zum Bearbeiten</span>
                <span class="ve-status" id="ve-status">Visual Editor aktiv</span>
            </div>
        `;
        document.body.prepend(bar);
    }

    // ── Section badges ─────────────────────────────────────────────────────
    function buildSectionBadges() {
        document.querySelectorAll('[data-ve-section]').forEach((el, idx) => {
            // Make sure the element can host absolute children
            const pos = getComputedStyle(el).position;
            if (pos === 'static') el.style.position = 'relative';

            const badge = document.createElement('span');
            badge.className = 've-section-badge';
            badge.textContent = el.dataset.veLabel || ('Sektion ' + (idx + 1));
            el.prepend(badge);
        });
    }

    // ── Box overlays ───────────────────────────────────────────────────────
    function buildBoxOverlays() {
        document.querySelectorAll('[data-ve-box]').forEach(el => {
            const boxId = el.dataset.veBox;
            if (!boxId || boxId === '0') return;

            // Wrap the element
            const wrap = document.createElement('div');
            wrap.className = 've-box-wrap';
            el.parentNode.insertBefore(wrap, el);
            wrap.appendChild(el);

            // Gear button
            const btn = document.createElement('button');
            btn.className = 've-box-btn';
            btn.textContent = '⚙';
            btn.title = 'Box bearbeiten (ID ' + boxId + ')';
            btn.type = 'button';
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                openPanel(boxId);
            });
            wrap.appendChild(btn);
        });
    }

    // ── Edit Panel ─────────────────────────────────────────────────────────
    function buildPanel() {
        const panel = document.createElement('div');
        panel.id = 've-panel';
        panel.className = 've-panel';
        panel.innerHTML = `
            <div class="ve-panel__header">
                <span class="ve-panel__title">Box bearbeiten</span>
                <button class="ve-panel__close" id="ve-panel-close" title="Schließen" type="button">✕</button>
            </div>
            <div class="ve-panel__body" id="ve-panel-body">
                <p class="ve-loading">Wähle eine Box zum Bearbeiten.</p>
            </div>
            <div class="ve-panel__footer">
                <button class="ve-btn-save" id="ve-save-btn" type="button" disabled>Speichern</button>
            </div>
        `;
        document.body.appendChild(panel);

        document.getElementById('ve-panel-close').addEventListener('click', closePanel);
        document.getElementById('ve-save-btn').addEventListener('click', saveBox);

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePanel();
        });
    }

    function openPanel(boxId) {
        activeBoxId   = boxId;
        activeBoxData = null;

        const panel  = document.getElementById('ve-panel');
        const body   = document.getElementById('ve-panel-body');
        const saveBtn = document.getElementById('ve-save-btn');

        // Highlight active box
        document.querySelectorAll('.ve-box-wrap').forEach(w => w.classList.remove('is-active'));
        const wrap = document.querySelector(`[data-ve-box="${boxId}"]`)?.closest('.ve-box-wrap');
        if (wrap) wrap.classList.add('is-active');

        panel.classList.add('is-open');
        body.innerHTML = '<p class="ve-loading">Lade…</p>';
        saveBtn.disabled = true;

        fetch(`/admin/pages/box/${boxId}/data`, {
            headers: { 'X-CSRF-Token': CSRF }
        })
            .then(r => r.json())
            .then(data => {
                if (!data.ok) {
                    body.innerHTML = '<p class="ve-loading">Fehler beim Laden.</p>';
                    return;
                }
                activeBoxData = data;
                renderFields(data.content || {});
                saveBtn.disabled = false;
            })
            .catch(() => {
                body.innerHTML = '<p class="ve-loading">Netzwerkfehler.</p>';
            });
    }

    function closePanel() {
        document.getElementById('ve-panel')?.classList.remove('is-open');
        document.querySelectorAll('.ve-box-wrap').forEach(w => w.classList.remove('is-active'));
        activeBoxId   = null;
        activeBoxData = null;
    }

    function renderFields(content) {
        const body = document.getElementById('ve-panel-body');
        if (!content || Object.keys(content).length === 0) {
            body.innerHTML = '<p class="ve-loading">Keine editierbaren Felder gefunden.</p>';
            return;
        }

        body.innerHTML = Object.entries(content).map(([key, value]) => {
            const label = fieldLabel(key);
            const val   = value === null || value === undefined ? '' : String(value);
            const isHtml     = key === 'html';
            const isLongText = val.length > 80 || val.includes('\n');
            const isItems    = key === 'items'; // JSON array field

            if (isHtml || isLongText || isItems) {
                const rows = isHtml ? 8 : (isItems ? 5 : 4);
                const hint = isHtml
                    ? '<p class="ve-field-hint">HTML erlaubt. Wird über safe_html() ausgegeben.</p>'
                    : (isItems ? '<p class="ve-field-hint">JSON-Array, z.B. ["Punkt 1","Punkt 2"]</p>' : '');
                return `<div class="ve-field">
                    <label class="ve-label">${escHtml(label)}</label>
                    <textarea class="ve-textarea" data-field="${escHtml(key)}" rows="${rows}">${escHtml(val)}</textarea>
                    ${hint}
                </div>`;
            }

            return `<div class="ve-field">
                <label class="ve-label">${escHtml(label)}</label>
                <input type="text" class="ve-input" data-field="${escHtml(key)}" value="${escHtml(val)}">
            </div>`;
        }).join('');
    }

    // ── Save ───────────────────────────────────────────────────────────────
    function saveBox() {
        if (!activeBoxId || !activeBoxData) return;

        const saveBtn = document.getElementById('ve-save-btn');
        saveBtn.disabled = true;
        setStatus('Speichern…', 'is-saving');

        // Collect field values from the panel
        const updatedContent = {};
        document.querySelectorAll('#ve-panel-body [data-field]').forEach(el => {
            updatedContent[el.dataset.field] = el.value;
        });

        // Build full payload (preserve layout + settings, update content)
        const payload = Object.assign({}, activeBoxData.data || {}, {
            content: Object.assign({}, (activeBoxData.data || {}).content || {}, updatedContent)
        });

        fetch(`/admin/pages/box/${activeBoxId}/save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF
            },
            body: JSON.stringify(Object.assign({ _csrf: CSRF }, payload))
        })
            .then(r => r.json())
            .then(result => {
                saveBtn.disabled = false;
                if (result.ok) {
                    setStatus('✓ Gespeichert', 'is-saved');
                    closePanel();
                    // Reload to show updated content (keeps ?ve_edit=1)
                    window.location.reload();
                } else {
                    setStatus('Fehler beim Speichern!', '');
                }
            })
            .catch(() => {
                saveBtn.disabled = false;
                setStatus('Netzwerkfehler!', '');
            });
    }

    // ── Bootstrap ──────────────────────────────────────────────────────────
    const IS_EMBEDDED = new URLSearchParams(window.location.search).has('ve_embedded');

    document.addEventListener('DOMContentLoaded', function () {
        if (!IS_EMBEDDED) buildToolbar();
        buildSectionBadges();
        buildBoxOverlays();
        buildPanel();
    });

})();
