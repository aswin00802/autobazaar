/**
 * Scroll-triggered entrance animations.
 *
 * Anything marked [data-reveal] fades and slides in the first time it enters
 * the viewport, then stops being observed. Children of [data-reveal-group]
 * are staggered so a grid arrives as a wave rather than all at once.
 *
 * Degrades safely: if IntersectionObserver is missing, or the visitor has
 * asked for reduced motion, everything is shown immediately with no movement.
 */
const STAGGER_MS = 70;
const MAX_STAGGER = 6; // beyond this the wait starts to feel like a bug

export default function initReveal() {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const targets = document.querySelectorAll('[data-reveal]');

    if (prefersReduced || !('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    // Stagger within each group.
    document.querySelectorAll('[data-reveal-group]').forEach((group) => {
        [...group.children].forEach((child, i) => {
            const el = child.matches('[data-reveal]') ? child : child.querySelector('[data-reveal]');
            if (el) el.style.setProperty('--reveal-delay', `${Math.min(i, MAX_STAGGER) * STAGGER_MS}ms`);
        });
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // reveal once, never re-hide
            });
        },
        // Fire slightly before the element is fully on screen.
        { rootMargin: '0px 0px -8% 0px', threshold: 0.05 },
    );

    targets.forEach((el) => {
        // Anything already on screen at load should not wait for a scroll.
        const box = el.getBoundingClientRect();
        if (box.top < window.innerHeight && box.bottom > 0) {
            el.classList.add('is-visible');
        } else {
            observer.observe(el);
        }
    });
}
