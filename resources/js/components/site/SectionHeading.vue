<script setup lang="ts">
defineProps<{
    id?: string;
    eyebrow?: string | null;
    heading?: string | null;
    intro?: string | null;
    index?: number;
    as?: 'h1' | 'h2';
}>();
</script>

<template>
    <div class="max-w-2xl">
        <p v-if="eyebrow" class="eyebrow flex items-center gap-3">
            <span v-if="index" class="text-brand">{{
                String(index).padStart(2, '0')
            }}</span>
            <span v-if="index" class="h-px w-6 bg-border" aria-hidden="true" />
            {{ eyebrow }}
        </p>
        <component
            :is="as ?? 'h2'"
            :id="id"
            class="text-title mt-4 text-balance"
        >
            {{ heading }}
        </component>
        <p v-if="intro" class="text-lead mt-4 text-muted-foreground">
            {{ intro }}
        </p>
    </div>
</template>
