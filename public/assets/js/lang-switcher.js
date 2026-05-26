(function () {
    'use strict';

    const IS_ADMIN   = location.pathname.startsWith('/admin');
    const COOKIE_KEY = IS_ADMIN ? 'vcms_admin_lang' : 'vcms_lang';

    const SWAP = [
        '#vcms-content',
        '.vcms-nav',
        '.vcms-nav-list',
        '.vcms-mobile-nav__list',
    ];

    async function switchLang(lang) {
        const secure = location.protocol === 'https:' ? ';Secure' : '';
        document.cookie = COOKIE_KEY + '=' + encodeURIComponent(lang) + ';path=/;SameSite=Lax' + secure;

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

            document.querySelectorAll('.vcms-lang-btn').forEach(function (btn) {
                var active = btn.dataset.lang === lang;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-pressed', String(active));
            });

            document.querySelectorAll('.vcms-lang-select').forEach(function (sel) {
                sel.value = lang;
            });

        } catch (_) {
            location.reload();
        } finally {
            document.querySelectorAll('.vcms-lang-switcher').forEach(function (el) {
                el.removeAttribute('aria-busy');
            });
        }
    }

    // Button click (2-lang toggle)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.vcms-lang-btn');
        if (btn && btn.dataset.lang) {
            e.preventDefault();
            switchLang(btn.dataset.lang);
        }
    });

    // Select change (3+ langs dropdown)
    document.addEventListener('change', function (e) {
        var sel = e.target.closest('.vcms-lang-select');
        if (sel) {
            switchLang(sel.value);
        }
    });
})();
