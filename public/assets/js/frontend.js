/* VeloCMS Frontend JS
   - Sticky header shadow
   - Mobile navigation (hamburger)
   - Video consent (DSGVO 2-click)
   - Scroll-reveal animation
*/
'use strict';

document.addEventListener('DOMContentLoaded', () => {

    // ── Sticky header shadow ─────────────────────────────────────────────────
    const header = document.querySelector('.vcms-header');
    if (header) {
        const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ── Mobile navigation ────────────────────────────────────────────────────
    const hamburger  = document.querySelector('.vcms-hamburger');
    const mobileNav  = document.querySelector('.vcms-mobile-nav');

    if (hamburger && mobileNav) {
        const toggle = (open) => {
            hamburger.classList.toggle('is-open', open);
            mobileNav.classList.toggle('is-open', open);
            hamburger.setAttribute('aria-expanded', String(open));
            document.body.style.overflow = open ? 'hidden' : '';
        };

        hamburger.addEventListener('click', () => {
            toggle(!hamburger.classList.contains('is-open'));
        });

        // Close on nav link click
        mobileNav.querySelectorAll('.vcms-mobile-nav__link').forEach(link => {
            link.addEventListener('click', () => toggle(false));
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && hamburger.classList.contains('is-open')) {
                toggle(false);
                hamburger.focus();
            }
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (
                hamburger.classList.contains('is-open') &&
                !hamburger.contains(e.target) &&
                !mobileNav.contains(e.target)
            ) {
                toggle(false);
            }
        });
    }

    // ── Video consent (DSGVO 2-click) ────────────────────────────────────────
    document.querySelectorAll('.vcms-video-consent').forEach(wrapper => {
        const btn      = wrapper.querySelector('.vcms-video-consent-btn');
        const videoId  = wrapper.dataset.videoId;
        const provider = wrapper.dataset.provider || 'youtube';

        if (!btn || !videoId) return;

        btn.addEventListener('click', () => {
            let src = '';
            if (provider === 'vimeo') {
                src = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
            } else {
                // Default: YouTube
                src = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`;
            }

            const iframe = document.createElement('iframe');
            iframe.src             = src;
            iframe.width           = '100%';
            iframe.height          = '400';
            iframe.frameBorder     = '0';
            iframe.allow           = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.style.cssText   = 'border-radius: 8px; display: block;';

            wrapper.replaceWith(iframe);
        });
    });

    // ── Scroll-reveal ────────────────────────────────────────────────────────
    const revealEls = document.querySelectorAll('.vcms-reveal');
    if (revealEls.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
        );
        revealEls.forEach(el => observer.observe(el));
    } else {
        // Fallback: show all immediately (no IntersectionObserver support)
        revealEls.forEach(el => el.classList.add('is-visible'));
    }

});
