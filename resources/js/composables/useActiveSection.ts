import { onBeforeUnmount, onMounted, ref } from 'vue';
import type { Ref } from 'vue';

/**
 * Tracks which page section (by element id) is currently in view.
 */
export function useActiveSection(ids: () => string[]): Ref<string | null> {
    const active = ref<string | null>(null);
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (!('IntersectionObserver' in window)) {
            return;
        }

        const visible = new Map<string, number>();

        observer = new IntersectionObserver(
            (entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        visible.set(entry.target.id, entry.intersectionRatio);
                    } else {
                        visible.delete(entry.target.id);
                    }
                }

                const ordered = ids().filter((id) => visible.has(id));
                active.value = ordered[0] ?? null;
            },
            { rootMargin: '-35% 0px -55% 0px', threshold: [0, 0.01] },
        );

        for (const id of ids()) {
            const element = document.getElementById(id);

            if (element) {
                observer.observe(element);
            }
        }
    });

    onBeforeUnmount(() => observer?.disconnect());

    return active;
}
