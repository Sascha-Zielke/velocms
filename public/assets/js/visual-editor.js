/**
 * VeloCMS Visual Editor  — v2.0
 *
 * Global — works with any tenant layout that includes ve_head() + ve_scripts().
 *
 * Features:
 *  - Gear-icon content editing (panel → fetch → save)
 *  - Gridstack drag-and-resize of boxes within sections (24-column grid)
 *  - SortableJS field reordering within the edit panel
 *  - Embedded mode (?ve_embedded=1) suppresses the toolbar
 *
 * Backend endpoints (Pages module):
 *   GET  /admin/pages/box/{id}/data      → fetch current box content
 *   POST /admin/pages/box/{id}/save      → save updated box content
 *   POST /admin/pages/{pageId}/grid/save → save grid layout (all boxes)
 */

(function () {
    'use strict';

    // ── Constants ──────────────────────────────────────────────────────────────
    const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const PAGE_ID     = document.querySelector('meta[name="ve-page-id"]')?.content  || '0';
    const IS_EMBEDDED = new URLSearchParams(window.location.search).has('ve_embedded');

    // ── State ──────────────────────────────────────────────────────────────────
    let activeBoxId   = null;
    let activeBoxData = null;
    let gridInstances = [];    // GridStack instances keyed per section
    let gridSaveTimer = null;  // debounce handle

    // ── Helpers ────────────────────────────────────────────────────────────────
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
        el.className   = 've-status' + (cls ? ' ' + cls : '');
        if (cls === 'is-saved') {
            setTimeout(() => {
                el.textContent = 'Visual Editor aktiv';
                el.className   = 've-status';
            }, 2500);
        }
    }

    function fieldLabel(key) {
        const map = {
            headline: 'Überschrift', subline:  'Unterzeile',    tagline:   'Tagline',
            title:    'Titel',       subtitle:  'Untertitel',    label:     'Label',
            text:     'Text',        html:      'Inhalt (HTML)',
            desc:     'Beschreibung',name:      'Name',          icon:      'Icon',
            price:    'Preis',       genre:     'Genre',         role:      'Rolle',
            address:  'Adresse',     email:     'E-Mail',        hours:     'Öffnungszeiten',
            items:    'Punkte (als JSON-Array)',
        };
        return map[key] || key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
    }

    // ── Toolbar (standalone mode only) ─────────────────────────────────────────
    function buildToolbar() {
        document.body.classList.add('ve-active');

        const bar = document.createElement('div');
        bar.className = 've-toolbar';
        bar.innerHTML = `
            <div class="ve-toolbar__inner">
                <a href="/admin/pages" class="ve-back-btn">← Alle Seiten</a>
                <span class="ve-page-name" id="ve-page-name">${escHtml(document.title.split('—')[0].trim())}</span>
                <span class="ve-hint">Hover über eine Box → ⚙ bearbeiten · ⠿ ziehen zum Verschieben</span>
                <span class="ve-status" id="ve-status">Visual Editor aktiv</span>
            </div>
        `;
        document.body.prepend(bar);
    }

    // ── Section badges ─────────────────────────────────────────────────────────
    function buildSectionBadges() {
        document.querySelectorAll('[data-ve-section]').forEach((el, idx) => {
            const pos = getComputedStyle(el).position;
            if (pos === 'static') el.style.position = 'relative';

            const badge = document.createElement('span');
            badge.className   = 've-section-badge';
            badge.textContent = el.dataset.veLabel || ('Sektion ' + (idx + 1));
            el.prepend(badge);
        });
    }

    // ── Box overlays ───────────────────────────────────────────────────────────
    // Each [data-ve-box] element is wrapped inside:
    //   .ve-box-wrap.grid-stack-item       ← Gridstack positions this
    //     .ve-box-inner.grid-stack-item-content
    //       .ve-drag-handle                ← drag handle
    //       [data-ve-box] original element
    //       button.ve-box-btn              ← gear icon
    function buildBoxOverlays() {
        document.querySelectorAll('[data-ve-box]').forEach(el => {
            const boxId = el.dataset.veBox;
            if (!boxId || boxId === '0') return;

            // Read Gridstack position from data-gs-* attributes
            const gsX       = el.dataset.gsX             ?? '0';
            const gsY       = el.dataset.gsY             ?? '0';
            const gsW       = el.dataset.gsW             ?? '24';
            const gsH       = el.dataset.gsH             ?? '4';
            const gsAutoPos = el.dataset.gsAutoPosition  ?? null;

            // Outer wrapper = Gridstack item
            const wrap = document.createElement('div');
            wrap.className       = 've-box-wrap grid-stack-item';
            wrap.dataset.veBoxId = boxId;
            wrap.setAttribute('gs-w', gsW);
            wrap.setAttribute('gs-h', gsH);
            if (gsAutoPos) {
                wrap.setAttribute('gs-auto-position', '1');
            } else {
                wrap.setAttribute('gs-x', gsX);
                wrap.setAttribute('gs-y', gsY);
            }

            // Inner div required by Gridstack
            const inner = document.createElement('div');
            inner.className = 've-box-inner grid-stack-item-content';

            // Drag handle — only handle for Gridstack dragging
            const handle = document.createElement('div');
            handle.className = 've-drag-handle';
            handle.setAttribute('aria-hidden', 'true');
            handle.title = 'Verschieben';

            // Gear button
            const btn = document.createElement('button');
            btn.className   = 've-box-btn';
            btn.textContent = '⚙';
            btn.title       = 'Bearbeiten (Box ' + boxId + ')';
            btn.type        = 'button';
            btn.addEventListener('click', e => {
                e.stopPropagation();
                openPanel(boxId);
            });

            // Assemble DOM
            el.parentNode.insertBefore(wrap, el);
            wrap.appendChild(inner);
            inner.appendChild(handle);
            inner.appendChild(el);
            inner.appendChild(btn);
        });
    }

    // ── Gridstack ──────────────────────────────────────────────────────────────
    function initGridstack() {
        if (typeof GridStack === 'undefined') return;

        document.querySelectorAll('[data-ve-section]').forEach(sec => {
            sec.classList.add('grid-stack');

            const gs = GridStack.init({
                column:               24,
                cellHeight:           60,
                cellHeightUnit:       'px',
                margin:               6,
                float:                true,   // free positioning
                animate:              true,
                resizable:            { handles: 'se,sw,ne,nw,e,w,s,n' },
                draggable:            { handle: '.ve-drag-handle' },
                disableOneColumnMode: true,
            }, sec);

            // Debounced save on every positional change
            gs.on('change', () => {
                clearTimeout(gridSaveTimer);
                gridSaveTimer = setTimeout(saveGridLayout, 800);
            });

            gridInstances.push(gs);
        });
    }

    function saveGridLayout() {
        if (!PAGE_ID || PAGE_ID === '0') {
            setStatus('Kein Page-ID — Layout nicht gespeichert', '');
            return;
        }

        const items = [];
        document.querySelectorAll('.grid-stack-item[data-ve-box-id]').forEach(el => {
            const id = parseInt(el.dataset.veBoxId, 10);
            if (!id) return;
            items.push({
                box_id: id,
                x:      parseInt(el.getAttribute('gs-x') || '0',  10),
                y:      parseInt(el.getAttribute('gs-y') || '0',  10),
                w:      parseInt(el.getAttribute('gs-w') || '24', 10),
                h:      parseInt(el.getAttribute('gs-h') || '4',  10),
            });
        });

        if (!items.length) return;

        setStatus('Speichere Layout…', 'is-saving');

        fetch('/admin/pages/' + PAGE_ID + '/grid/save', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF,
            },
            body: JSON.stringify({ grid: items, _csrf: CSRF }),
        })
            .then(r => r.json())
            .then(result => {
                if (result.ok) setStatus('✓ Layout gespeichert', 'is-saved');
                else           setStatus('Fehler beim Speichern!', '');
            })
            .catch(() => setStatus('Netzwerkfehler!', ''));
    }

    // ── Edit Panel ─────────────────────────────────────────────────────────────
    function buildPanel() {
        const panel = document.createElement('div');
        panel.id        = 've-panel';
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

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closePanel();
        });
    }

    function openPanel(boxId) {
        activeBoxId   = boxId;
        activeBoxData = null;

        const panel   = document.getElementById('ve-panel');
        const body    = document.getElementById('ve-panel-body');
        const saveBtn = document.getElementById('ve-save-btn');

        document.querySelectorAll('.ve-box-wrap').forEach(w => w.classList.remove('is-active'));
        const wrap = document.querySelector(`[data-ve-box="${boxId}"]`)?.closest('.ve-box-wrap');
        if (wrap) wrap.classList.add('is-active');

        panel.classList.add('is-open');
        body.innerHTML   = '<p class="ve-loading">Lade…</p>';
        saveBtn.disabled = true;

        fetch('/admin/pages/box/' + boxId + '/data', {
            headers: { 'X-CSRF-Token': CSRF },
        })
            .then(r => r.json())
            .then(data => {
                if (!data.ok) {
                    body.innerHTML = '<p class="ve-loading">Fehler beim Laden.</p>';
                    return;
                }
                activeBoxData    = data;
                renderFields(data.content || {});
                saveBtn.disabled = false;
                initFieldSortable();
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

        const items = Object.entries(content).map(([key, value]) => {
            const label      = fieldLabel(key);
            const val        = value === null || value === undefined ? '' : String(value);
            const isHtml     = key === 'html';
            const isLongText = val.length > 80 || val.includes('\n');
            const isItems    = key === 'items';

            let fieldHtml;
            if (isHtml || isLongText || isItems) {
                const rows = isHtml ? 8 : (isItems ? 5 : 4);
                const hint = isHtml
                    ? '<p class="ve-field-hint">HTML erlaubt — wird über safe_html() ausgegeben.</p>'
                    : (isItems ? '<p class="ve-field-hint">JSON-Array, z.B. ["Punkt 1","Punkt 2"]</p>' : '');
                fieldHtml = `<div class="ve-field">
                    <label class="ve-label">${escHtml(label)}</label>
                    <textarea class="ve-textarea" data-field="${escHtml(key)}" rows="${rows}">${escHtml(val)}</textarea>
                    ${hint}
                </div>`;
            } else {
                fieldHtml = `<div class="ve-field">
                    <label class="ve-label">${escHtml(label)}</label>
                    <input type="text" class="ve-input" data-field="${escHtml(key)}" value="${escHtml(val)}">
                </div>`;
            }

            return `<li class="ve-field-item" data-field-key="${escHtml(key)}">
                        <span class="ve-field-drag" title="Reihenfolge ändern" aria-hidden="true">⠿</span>
                        ${fieldHtml}
                    </li>`;
        });

        body.innerHTML = '<ul class="ve-field-list" id="ve-field-list">' + items.join('') + '</ul>';
    }

    // SortableJS on panel fields — reorder fields within a box
    function initFieldSortable() {
        if (typeof Sortable === 'undefined') return;
        const list = document.getElementById('ve-field-list');
        if (!list) return;
        Sortable.create(list, {
            handle:     '.ve-field-drag',
            animation:  150,
            ghostClass: 've-field-ghost',
        });
    }

    // ── Save box content ───────────────────────────────────────────────────────
    function saveBox() {
        if (!activeBoxId || !activeBoxData) return;

        const saveBtn = document.getElementById('ve-save-btn');
        saveBtn.disabled = true;
        setStatus('Speichern…', 'is-saving');

        // Collect in current DOM order (respects SortableJS reordering)
        const updatedContent = {};
        document.querySelectorAll('#ve-panel-body [data-field]').forEach(el => {
            updatedContent[el.dataset.field] = el.value;
        });

        const payload = Object.assign({}, activeBoxData.data || {}, {
            content: Object.assign({}, (activeBoxData.data || {}).content || {}, updatedContent),
        });

        fetch('/admin/pages/box/' + activeBoxId + '/save', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': CSRF,
            },
            body: JSON.stringify(Object.assign({ _csrf: CSRF }, payload)),
        })
            .then(r => r.json())
            .then(result => {
                saveBtn.disabled = false;
                if (result.ok) {
                    setStatus('✓ Gespeichert', 'is-saved');
                    closePanel();
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

    // ── Bootstrap ──────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        if (!IS_EMBEDDED) buildToolbar();
        buildSectionBadges();
        buildBoxOverlays();   // must run before initGridstack
        buildPanel();
        initGridstack();
    });

})();
