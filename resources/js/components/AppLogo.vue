<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const name = computed(() => String(usePage().props.name ?? 'Portfolio'));
const initials = computed(() =>
    name.value
        .split(/\s+/)
        .filter(Boolean)
        .map((w) => w[0])
        .join('')
        .slice(0, 2)
        .toUpperCase(),
);
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary font-mono text-[13px] font-semibold text-sidebar-primary-foreground"
        aria-hidden="true"
    >
        {{ initials }}
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{
            name
        }}</span>
        <span class="truncate text-xs text-muted-foreground"
            >Portfolio admin</span
        >
    </div>
</template>
