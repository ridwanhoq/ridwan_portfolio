<script setup lang="ts">
import { ImageIcon, RefreshCw, X } from '@lucide/vue';
import { computed, inject, ref } from 'vue';
import MediaPicker from '@/components/admin/MediaPicker.vue';
import { Button } from '@/components/ui/button';
import type { MediaItem } from '@/types';
import { mediaCacheKey } from './context';

const props = defineProps<{
    id: string;
    modelValue: number | null;
    compact?: boolean;
}>();
const emit = defineEmits<{ 'update:modelValue': [value: number | null] }>();

const cache = inject(mediaCacheKey, {});
const open = ref(false);
const media = computed(() =>
    props.modelValue ? (cache[props.modelValue] ?? null) : null,
);

function select(item: MediaItem): void {
    cache[item.id] = item;
    emit('update:modelValue', item.id);
}
</script>

<template>
    <div :id="id" class="flex items-center gap-4">
        <div
            class="flex shrink-0 items-center justify-center overflow-hidden rounded-md border border-border bg-muted"
            :class="compact ? 'size-16' : 'h-24 w-40'"
        >
            <img
                v-if="media"
                :src="media.thumb_url"
                :alt="media.alt ?? ''"
                class="size-full object-cover"
            />
            <span
                v-else-if="modelValue"
                class="px-2 text-center text-xs text-muted-foreground"
                >Image #{{ modelValue }}</span
            >
            <ImageIcon
                v-else
                class="size-6 text-muted-foreground"
                aria-hidden="true"
            />
        </div>
        <div class="flex flex-wrap gap-2">
            <Button
                type="button"
                variant="outline"
                size="sm"
                data-test="media-choose"
                @click="open = true"
            >
                <RefreshCw v-if="modelValue" /><ImageIcon v-else />
                {{ modelValue ? 'Replace' : 'Choose image' }}
            </Button>
            <Button
                v-if="modelValue"
                type="button"
                variant="ghost"
                size="sm"
                @click="emit('update:modelValue', null)"
            >
                <X /> Remove
            </Button>
        </div>
        <MediaPicker v-model:open="open" @select="select" />
    </div>
</template>
