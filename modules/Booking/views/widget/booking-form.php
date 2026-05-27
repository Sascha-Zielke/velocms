<?php declare(strict_types=1);
/**
 * Frontend Booking Widget
 *
 * Include in any frontend view:
 *   <?php include 'path/to/booking-form.php'; ?>
 *
 * Required: $resourceId (int)
 * Optional: $timezone (string, default UTC)
 */
$resourceId = (int) ($resourceId ?? 0);
$timezone   = (string) ($timezone ?? 'UTC');
if ($resourceId <= 0) {
    return;
}
?>
<div class="vcms-booking-widget" data-resource-id="<?= $resourceId ?>" data-timezone="<?= e($timezone) ?>">

    <div class="vcms-booking-step" id="vcms-step-date">
        <label for="vcms-booking-date">Datum wählen</label>
        <input type="date" id="vcms-booking-date" class="vcms-input"
               min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+90 days')) ?>">
    </div>

    <div class="vcms-booking-step" id="vcms-step-slot" style="display:none">
        <p>Verfügbare Zeiten:</p>
        <div id="vcms-slot-list" class="vcms-slot-list"></div>
    </div>

    <form id="vcms-booking-form" class="vcms-booking-step" style="display:none" novalidate>
        <input type="hidden" name="resource_id" value="<?= $resourceId ?>">
        <input type="hidden" name="start_at" id="vcms-booking-start">
        <input type="hidden" name="end_at"   id="vcms-booking-end">
        <input type="hidden" name="timezone" value="<?= e($timezone) ?>">

        <div class="vcms-form-group">
            <label><?= t('booking.field_customer_name') ?> *</label>
            <input type="text" name="customer_name" required class="vcms-input">
        </div>
        <div class="vcms-form-group">
            <label><?= t('booking.field_customer_email') ?> *</label>
            <input type="email" name="customer_email" required class="vcms-input">
        </div>
        <div class="vcms-form-group">
            <label><?= t('booking.field_customer_phone') ?></label>
            <input type="tel" name="customer_phone" class="vcms-input">
        </div>
        <div class="vcms-form-group">
            <label><?= t('booking.field_notes') ?></label>
            <textarea name="notes" rows="3" class="vcms-input"></textarea>
        </div>

        <div id="vcms-template-fields"></div>

        <button type="submit" class="vcms-btn vcms-btn--primary"><?= t('contact.submit') ?></button>
    </form>

    <div id="vcms-booking-success" class="vcms-booking-step" style="display:none">
        <p class="vcms-alert vcms-alert--success" id="vcms-success-msg"></p>
    </div>

    <div id="vcms-booking-error" class="vcms-alert vcms-alert--error" style="display:none"></div>
</div>

<script>
(function () {
    const widget   = document.querySelector('.vcms-booking-widget');
    const resId    = widget.dataset.resourceId;
    const tz       = widget.dataset.timezone;
    const dateInput = document.getElementById('vcms-booking-date');
    let templateFields = [];

    // Load template form fields on init
    fetch(`/api/booking/resources`)
        .then(r => r.json())
        .then(data => {
            const res = (data.resources || []).find(r => r.id == resId);
            templateFields = res?.form_fields ?? [];
        });

    dateInput.addEventListener('change', () => {
        const date = dateInput.value;
        if (!date) return;
        fetch(`/api/booking/availability?resource_id=${resId}&date=${date}&timezone=${encodeURIComponent(tz)}`)
            .then(r => r.json())
            .then(data => {
                const list = document.getElementById('vcms-slot-list');
                list.innerHTML = '';
                (data.slots || []).forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'vcms-slot-btn';
                    btn.textContent = slot.start.slice(11, 16) + ' – ' + slot.end.slice(11, 16);
                    btn.dataset.start = slot.start;
                    btn.dataset.end   = slot.end;
                    btn.addEventListener('click', () => selectSlot(slot));
                    list.appendChild(btn);
                });
                document.getElementById('vcms-step-slot').style.display = data.slots?.length ? '' : 'none';
                if (!data.slots?.length) {
                    list.innerHTML = '<p style="color:#888">Keine Termine verfügbar.</p>';
                    document.getElementById('vcms-step-slot').style.display = '';
                }
                document.getElementById('vcms-booking-form').style.display = 'none';
            });
    });

    function selectSlot(slot) {
        document.getElementById('vcms-booking-start').value = slot.start;
        document.getElementById('vcms-booking-end').value   = slot.end;

        // Render template-specific fields
        const container = document.getElementById('vcms-template-fields');
        container.innerHTML = '';
        templateFields.forEach(f => {
            const group = document.createElement('div');
            group.className = 'vcms-form-group';
            const label = document.createElement('label');
            label.textContent = f.label + (f.required ? ' *' : '');
            group.appendChild(label);

            let input;
            if (f.type === 'textarea') {
                input = document.createElement('textarea');
                input.rows = 3;
            } else {
                input = document.createElement('input');
                input.type = f.type || 'text';
                if (f.attrs) Object.entries(f.attrs).forEach(([k, v]) => input.setAttribute(k, v));
            }
            input.name = f.name;
            input.className = 'vcms-input';
            if (f.required) input.required = true;
            group.appendChild(input);
            container.appendChild(group);
        });

        document.getElementById('vcms-booking-form').style.display = '';
    }

    document.getElementById('vcms-booking-form').addEventListener('submit', e => {
        e.preventDefault();
        const form = e.target;
        const data = new URLSearchParams(new FormData(form));
        const errBox = document.getElementById('vcms-booking-error');
        errBox.style.display = 'none';

        fetch('/api/booking/book', { method: 'POST', body: data })
            .then(r => r.json().then(d => ({ status: r.status, data: d })))
            .then(({ status, data }) => {
                if (status === 201) {
                    form.style.display = 'none';
                    document.getElementById('vcms-step-slot').style.display = 'none';
                    document.getElementById('vcms-booking-success').style.display = '';
                    document.getElementById('vcms-success-msg').textContent =
                        status === 'pending'
                            ? 'Ihre Anfrage wurde eingereicht und wird bestätigt.'
                            : 'Buchung erfolgreich! Sie erhalten eine Bestätigung per E-Mail.';
                } else {
                    errBox.textContent = (data.errors || ['Ein Fehler ist aufgetreten.']).join(' ');
                    errBox.style.display = '';
                }
            })
            .catch(() => {
                errBox.textContent = 'Netzwerkfehler. Bitte versuchen Sie es erneut.';
                errBox.style.display = '';
            });
    });
})();
</script>
