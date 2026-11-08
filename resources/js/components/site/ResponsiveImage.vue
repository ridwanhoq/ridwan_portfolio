<script setup lang="ts">
import { computed } from 'vue';
import type { MediaItem } from '@/types';

const props = withDefaults(
    defineProps<{
        media: MediaItem;
        alt?: string | null;
        sizes?: string;
        eager?: boolean;
        imgClass?: string;
    }>(),
    { sizes: '(min-width: 1024px) 50vw, 100vw', eager: false },
);

const webpSrcset = computed(() => {
    const parts: string[] = [];

    if (props.media.thumb_url && props.media.thumb_url !== props.media.url) {
        parts.push(`${props.media.thumb_url} 720w`);
    }

    const full =
        props.media.webp_url ??
        (props.media.url.endsWith('.webp') ? props.media.url : null);

    if (full) {
        parts.push(`${full} ${props.media.width ?? 1600}w`);
    }

    return parts.join(', ');
});
</script>

<template>
    <picture>
        <source
            v-if="webpSrcset"
            type="image/webp"
            :srcset="webpSrcset"
            :sizes="sizes"
        />
        <img
            :src="media.url"
            :alt="alt ?? media.alt ?? ''"
            :width="media.width ?? undefined"
            :height="media.height ?? undefined"
            :loading="eager ? 'eager' : 'lazy'"
            :fetchpriority="eager ? 'high' : undefined"
            decoding="async"
            :class="imgClass"
        />
    </picture>
</template>
