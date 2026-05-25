/* VeloCMS Admin JS — Visual Editor */
'use strict';

const VCMS = {
    csrfToken: () => document.querySelector('meta[name="csrf-token"]')?.content ?? '',

    async post(url, data = {}) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': VCMS.csrfToken(),
            },
            body: JSON.stringify(data),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    },

    flash(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `vcms-alert vcms-alert--${type}`;
        el.textContent = msg;
        const content = document.querySelector('.vcms-content');
        content?.prepend(el);
        setTimeout(() => el.remove(), 3500);
    },

    confirm(msg) {
        return window.confirm(msg);
    },
};

/* ── SECTION ── */
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    const action = btn.dataset.action;

    // Add Section
    if (action === 'add-section') {
        const pageId = btn.dataset.pageId;
        try {
            const res = await VCMS.post(`/admin/pages/${pageId}/section/add`);
            if (res.ok) location.reload();
        } catch { VCMS.flash('Fehler beim Hinzufügen der Sektion', 'error'); }
    }

    // Delete Section
    if (action === 'delete-section') {
        if (!VCMS.confirm('Sektion und alle Inhalte löschen?')) return;
        const sectionId = btn.dataset.sectionId;
        try {
            const res = await VCMS.post(`/admin/pages/section/${sectionId}/delete`);
            if (res.ok) btn.closest('.vcms-section-block')?.remove();
        } catch { VCMS.flash('Fehler beim Löschen', 'error'); }
    }

    // Toggle Section Settings
    if (action === 'toggle-section-settings') {
        const sectionId = btn.dataset.sectionId;
        document.getElementById(`section-settings-${sectionId}`)?.classList.toggle('open');
    }

    // Add Row
    if (action === 'add-row') {
        const sectionId = btn.dataset.sectionId;
        try {
            const res = await VCMS.post(`/admin/pages/section/${sectionId}/row/add`);
            if (res.ok) location.reload();
        } catch { VCMS.flash('Fehler beim Hinzufügen der Zeile', 'error'); }
    }

    // Delete Row
    if (action === 'delete-row') {
        if (!VCMS.confirm('Zeile und alle Boxes löschen?')) return;
        const rowId = btn.dataset.rowId;
        try {
            const res = await VCMS.post(`/admin/pages/row/${rowId}/delete`);
            if (res.ok) btn.closest('.vcms-row-block')?.remove();
        } catch { VCMS.flash('Fehler beim Löschen', 'error'); }
    }

    // Add Box
    if (action === 'add-box') {
        const rowId = btn.dataset.rowId;
        const type  = btn.dataset.boxType ?? 'text';
        try {
            const res = await VCMS.post(`/admin/pages/row/${rowId}/box/add`, { type });
            if (res.ok) location.reload();
        } catch { VCMS.flash('Fehler beim Hinzufügen der Box', 'error'); }
    }

    // Open Box Editor Modal
    if (action === 'edit-box') {
        const boxId = btn.dataset.boxId;
        openBoxModal(boxId);
    }

    // Delete Box
    if (action === 'delete-box') {
        if (!VCMS.confirm('Box löschen?')) return;
        const boxId = btn.dataset.boxId;
        try {
            const res = await VCMS.post(`/admin/pages/box/${boxId}/delete`);
            if (res.ok) btn.closest('.vcms-box-block')?.remove();
        } catch { VCMS.flash('Fehler beim Löschen', 'error'); }
    }

    // Close Modal
    if (action === 'close-modal') {
        closeBoxModal();
    }

    // Save Box from Modal
    if (action === 'save-box') {
        const boxId = btn.dataset.boxId;
        await saveBox(boxId);
    }
});

/* ── SECTION SETTINGS SAVE ── */
document.addEventListener('submit', async (e) => {
    const form = e.target.closest('.vcms-section-settings-form');
    if (!form) return;
    e.preventDefault();
    const sectionId = form.dataset.sectionId;
    const data = Object.fromEntries(new FormData(form));
    try {
        const res = await VCMS.post(`/admin/pages/section/${sectionId}/settings`, data);
        if (res.ok) VCMS.flash('Einstellungen gespeichert');
    } catch { VCMS.flash('Fehler beim Speichern', 'error'); }
});

/* ── BOX EDITOR MODAL ── */
let currentBoxId = null;

function openBoxModal(boxId) {
    currentBoxId = boxId;
    const tpl = document.getElementById(`box-editor-${boxId}`);
    if (!tpl) return;
    const modal = document.getElementById('vcms-modal');
    if (!modal) return;
    document.getElementById('vcms-modal-content').innerHTML = tpl.innerHTML;
    modal.classList.add('open');
}

function closeBoxModal() {
    document.getElementById('vcms-modal')?.classList.remove('open');
    currentBoxId = null;
}

async function saveBox(boxId) {
    const modal = document.getElementById('vcms-modal');
    const inputs = modal.querySelectorAll('input, textarea, select');
    const data = {};
    inputs.forEach(inp => { if (inp.name) data[inp.name] = inp.value; });
    try {
        const res = await VCMS.post(`/admin/pages/box/${boxId}/save`, data);
        if (res.ok) { closeBoxModal(); location.reload(); }
    } catch { VCMS.flash('Fehler beim Speichern', 'error'); }
}

// Close modal on overlay click
document.getElementById('vcms-modal')?.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closeBoxModal();
});

// BOX TYPE SELECTOR (quick-add)
document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-add-box-trigger]');
    if (!trigger) return;
    const menu = trigger.nextElementSibling;
    menu?.classList.toggle('open');
});
