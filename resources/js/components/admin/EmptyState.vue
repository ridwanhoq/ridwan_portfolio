<script setup lang="ts">
import { Inbox } from '@lucide/vue';

defineProps<{ title: string; description?: string }>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-center rounded-lg border border-dashed border-border px-6 py-16 text-center"
        data-test="empty-state"
    >
        <Inbox class="size-8 text-muted-foreground" aria-hidden="true" />
        <p class="mt-4 font-medium">{{ title }}</p>
        <p
            v-if="description"
            class="mt-1 max-w-sm text-sm text-muted-foreground"
        >
            {{ description }}
        </p>
        <div v-if="$slots.default" class="mt-5"><slot /></div>
    </div>
</template>
