/**
 * VeloCMS Visual Editor  — v3.0
 *
 * Global — works with any tenant layout that includes ve_head() + ve_scripts().
 *
 * Features:
 *  - Floating popover positioned directly on the clicked box (inline feel)
 *  - Popover is draggable via its header
 *  - Gridstack drag-and-resize of boxes within sections (24-column grid)
 *  - SortableJS field reordering within the popover
 *  - Embedded mode (?ve_embedded=1) suppresses the top toolbar
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
    let gridInstances = [];
    let gridSaveTimer = null;
    let popover       = null;

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
            items:    'Punkte (JSON)',
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
                <span class="ve-hint">Box anklicken → direkt bearbeiten · ⠿ zum Verschieben</span>
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
    function buildBoxOverlays() {
        document.querySelectorAll('[data-ve-box]').forEach(el => {
            const boxId = el.dataset.veBox;
            if (!boxId || boxId === '0') return;

            const gsX       = el.dataset.gsX            ?? '0';
            const gsY       = el.dataset.gsY            ?? '0';
            const gsW       = el.dataset.gsW            ?? '24';
            const gsH       = el.dataset.gsH            ?? '4';
            const gsAutoPos = el.dataset.gsAutoPosition ?? null;

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

            const inner = document.createElement('div');
            inner.className = 've-box-inner grid-stack-item-content';

            const handle = document.createElement('div');
            handle.className = 've-drag-handle';
            handle.setAttribute('aria-hidden', 'true');
            handle.title = 'Verschieben';

            // Click anywhere on box content → open popover on that box
            inner.addEventListener('click', e => {
                if (e.target.closest('.ve-drag-handle')) return;
                e.stopPropagation();
                openPopover(boxId, wrap);
            });

            el.parentNode.insertBefore(wrap, el);
            wrap.appendChild(inner);
            inner.appendChild(handle);
            inner.appendChild(el);
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
                float:                true,
                animate:              true,
                resizable:            { handles: 'se,sw,ne,nw,e,w,s,n' },
                draggable:            { handle: '.ve-drag-handle' },
                disableOneColumnMode: true,
            }, sec);

            gs.on('change', () => {
                clearTimeout(gridSaveTimer);
                gridSaveTimer = setTimeout(saveGridLayout, 800);
            });

            gridInstances.push(gs);
        });
    }

    function saveGridLayout() {
        if (!PAGE_ID || PAGE_ID === '0') return;

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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
            body:    JSON.stringify({ grid: items, _csrf: CSRF }),
        })
            .then(r => r.json())
            .then(result => {
                if (result.ok) setStatus('✓ Layout gespeichert', 'is-saved');
                else           setStatus('Fehler beim Speichern!', '');
            })
            .catch(() => setStatus('Netzwerkfehler!', ''));
    }

    // ── Floating Popover ───────────────────────────────────────────────────────
    function buildPopover() {
        popover = document.createElement('div');
        popover.id        = 've-popover';
        popover.className = 've-popover';
        popover.innerHTML = `
            <div class="ve-popover__header">
                <span class="ve-popover__title">Bearbeiten</span>
                <button class="ve-popover__close" id="ve-pop-close" title="Schließen" type="button">✕</button>
            </div>
            <div class="ve-popover__body" id="ve-pop-body">
                <p class="ve-loading">Wähle eine Box.</p>
            </div>
            <div class="ve-popover__footer">
                <button class="ve-btn-save" id="ve-pop-save" type="button" disabled>Speichern</button>
            </div>
        `;
        document.body.appendChild(popover);

        document.getElementById('ve-pop-close').addEventListener('click', closePopover);
        document.getElementById('ve-pop-save').addEventListener('click', saveBox);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closePopover(); });

        // Close on outside click
        document.addEventListener('click', e => {
            if (popover.classList.contains('is-open') &&
                !popover.contains(e.target) &&
                !e.target.closest('.ve-box-wrap')) {
                closePopover();
            }
        });

        makeDraggable(popover);
    }

    function positionPopover(anchorEl) {
        const rect   = anchorEl.getBoundingClientRect();
        const vw     = window.innerWidth;
        const vh     = window.innerHeight;
        const pw     = 340;
        const offset = 12;

        // Prefer right side of box; fall back to left
        let left = rect.right + offset;
        if (left + pw > vw - 8) left = rect.left - pw - offset;
        if (left < 8)           left = vw - pw - 8;

        // Align to top of box, clamped to viewport
        let top = rect.top + window.scrollY;
        if (top < window.scrollY + 8)          top = window.scrollY + 8;
        if (top > window.scrollY + vh - 60)    top = window.scrollY + vh - 60;

        popover.style.left = left + 'px';
        popover.style.top  = top  + 'px';
    }

    function openPopover(boxId, anchorEl) {
        // Toggle: click same box again → close
        if (activeBoxId === boxId && popover.classList.contains('is-open')) {
            closePopover();
            return;
        }

        activeBoxId   = boxId;
        activeBoxData = null;

        document.querySelectorAll('.ve-box-wrap').forEach(w => w.classList.remove('is-active'));
        anchorEl.classList.add('is-active');

        const body    = document.getElementById('ve-pop-body');
        const saveBtn = document.getElementById('ve-pop-save');

        body.innerHTML   = '<p class="ve-loading">Lade…</p>';
        saveBtn.disabled = true;
        popover.classList.add('is-open');
        positionPopover(anchorEl);

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
                requestAnimationFrame(() => positionPopover(anchorEl));
            })
            .catch(() => {
                body.innerHTML = '<p class="ve-loading">Netzwerkfehler.</p>';
            });
    }

    function closePopover() {
        popover?.classList.remove('is-open');
        document.querySelectorAll('.ve-box-wrap').forEach(w => w.classList.remove('is-active'));
        activeBoxId   = null;
        activeBoxData = null;
    }

    function renderFields(content) {
        const body = document.getElementById('ve-pop-body');

        if (!content || Object.keys(content).length === 0) {
            body.innerHTML = '<p class="ve-loading">Keine editierbaren Felder.</p>';
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
                const rows = isHtml ? 7 : (isItems ? 4 : 3);
                const hint = isHtml
                    ? '<p class="ve-field-hint">HTML erlaubt</p>'
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
                        <span class="ve-field-drag" title="Reihenfolge" aria-hidden="true">⠿</span>
                        ${fieldHtml}
                    </li>`;
        });

        body.innerHTML = '<ul class="ve-field-list" id="ve-field-list">' + items.join('') + '</ul>';
    }

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

    // ── Save box ───────────────────────────────────────────────────────────────
    function saveBox() {
        if (!activeBoxId || !activeBoxData) return;

        const saveBtn = document.getElementById('ve-pop-save');
        saveBtn.disabled = true;
        setStatus('Speichern…', 'is-saving');

        const updatedContent = {};
        document.querySelectorAll('#ve-pop-body [data-field]').forEach(el => {
            updatedContent[el.dataset.field] = el.value;
        });

        const payload = Object.assign({}, activeBoxData.data || {}, {
            content: Object.assign({}, (activeBoxData.data || {}).content || {}, updatedContent),
        });

        fetch('/admin/pages/box/' + activeBoxId + '/save', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
            body:    JSON.stringify(Object.assign({ _csrf: CSRF }, payload)),
        })
            .then(r => r.json())
            .then(result => {
                saveBtn.disabled = false;
                if (result.ok) {
                    setStatus('✓ Gespeichert', 'is-saved');
                    closePopover();
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

    // ── Draggable popover ──────────────────────────────────────────────────────
    function makeDraggable(el) {
        const header = el.querySelector('.ve-popover__header');
        if (!header) return;
        header.style.cursor = 'grab';

        header.addEventListener('mousedown', e => {
            if (e.target.closest('.ve-popover__close')) return;
            const startX   = e.clientX;
            const startY   = e.clientY;
            const origLeft = parseInt(el.style.left || '0', 10);
            const origTop  = parseInt(el.style.top  || '0', 10);
            header.style.cursor = 'grabbing';

            function onMove(e) {
                el.style.left = (origLeft + e.clientX - startX) + 'px';
                el.style.top  = (origTop  + e.clientY - startY) + 'px';
            }
            function onUp() {
                header.style.cursor = 'grab';
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup',   onUp);
            }
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup',   onUp);
        });
    }

    // ── Bootstrap ──────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        if (!IS_EMBEDDED) buildToolbar();
        buildSectionBadges();
        buildBoxOverlays();
        buildPopover();
        initGridstack();
    });

})();
