import type { Directive } from 'vue';

/**
 * v-reveal: fades elements in once they scroll into view.
 * Skipped entirely when the user prefers reduced motion.
 */
let observer: IntersectionObserver | null = null;

function getObserver(): IntersectionObserver | null {
    if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
        return null;
    }

    observer ??= new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer?.unobserve(entry.target);
                }
            }
        },
        { rootMargin: '0px 0px -8% 0px' },
    );

    return observer;
}

export const vReveal: Directive<HTMLElement> = {
    mounted(el) {
        const reduced = window.matchMedia(
            '(prefers-reduced-motion: reduce)',
        ).matches;
        const io = getObserver();

        if (reduced || !io) {
            return;
        }

        // Never hide content that is already on screen.
        if (el.getBoundingClientRect().top < window.innerHeight) {
            return;
        }

        el.classList.add('reveal');
        io.observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};

if (typeof window !== 'undefined') {
    // Printing or saving as PDF must include every section.
    window.addEventListener('beforeprint', () => {
        document
            .querySelectorAll('.reveal')
            .forEach((el) => el.classList.add('is-visible'));
    });
}
