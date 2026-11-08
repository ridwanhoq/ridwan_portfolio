<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Inertia <Link> for internal pages; plain <a> for external URLs,
 * same-page anchors, mailto: links and downloads.
 */
const props = defineProps<{
    href: string;
    newTab?: boolean;
    download?: boolean;
}>();

const page = usePage();

const isPlainAnchor = computed(() => {
    const href = props.href;

    if (
        props.download ||
        props.newTab ||
        /^(https?:)?\/\//.test(href) ||
        /^(mailto|tel):/.test(href) ||
        href.startsWith('#')
    ) {
        return true;
    }

    // "/#about" while already on "/" should scroll, not trigger a visit.
    const [path, hash] = href.split('#');
    const currentPath = page.url.split(/[?#]/)[0] || '/';

    return hash !== undefined && (path || '/') === currentPath;
});
</script>

<template>
    <a
        v-if="isPlainAnchor"
        :href="href"
        :target="newTab ? '_blank' : undefined"
        :rel="newTab ? 'noopener noreferrer' : undefined"
        :download="download || undefined"
    >
        <slot />
    </a>
    <Link v-else :href="href">
        <slot />
    </Link>
</template>
