(function () {
    'use strict';

    // Elements to refresh from the fetched page on language switch
    const SWAP = [
        '#vcms-content',
        '.vcms-nav',
        '.vcms-nav-list',
        '.vcms-mobile-nav__list',
    ];

    async function switchLang(lang) {
        const secure = location.protocol === 'https:' ? ';Secure' : '';
        document.cookie = 'vcms_lang=' + encodeURIComponent(lang) + ';path=/;SameSite=Lax' + secure;

        document.querySelectorAll('.vcms-lang-switcher').forEach(function (el) {
            el.setAttribute('aria-busy', 'true');
        });

        try {
            const res  = await fetch(location.href, { credentials: 'include' });
            if (!res.ok) { location.reload(); return; }
            const html = await res.text();
            const doc  = new DOMParser().parseFromString(html, 'text/html');

            SWAP.forEach(function (sel) {
                var newEl = doc.querySelector(sel);
                var curEl = document.querySelector(sel);
                if (newEl && curEl) { curEl.innerHTML = newEl.innerHTML; }
            });

            document.documentElement.lang = lang;

            // Update switcher button state (event delegation survives the swap)
            document.querySelectorAll('.vcms-lang-btn').forEach(function (btn) {
                var active = btn.dataset.lang === lang;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-pressed', String(active));
            });

        } catch (_) {
            location.reload();
        } finally {
            document.querySelectorAll('.vcms-lang-switcher').forEach(function (el) {
                el.removeAttribute('aria-busy');
            });
        }
    }

    // Event delegation — survives DOM swaps
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.vcms-lang-btn');
        if (btn && btn.dataset.lang) {
            e.preventDefault();
            switchLang(btn.dataset.lang);
        }
    });
})();
