/* VeloCMS Frontend JS — 2-Click Video Consent (DSGVO) */
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.vcms-video-consent').forEach(wrapper => {
        const btn = wrapper.querySelector('.vcms-video-consent__btn');
        if (!btn) return;
        btn.addEventListener('click', () => {
            const src = wrapper.dataset.src;
            if (!src) return;
            const iframe = document.createElement('iframe');
            iframe.src = src;
            iframe.width = '100%';
            iframe.height = '400';
            iframe.frameBorder = '0';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.style.borderRadius = '6px';
            wrapper.replaceWith(iframe);
        });
    });
});
