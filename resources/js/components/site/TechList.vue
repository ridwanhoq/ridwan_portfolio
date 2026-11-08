<script setup lang="ts">
defineProps<{ items: string[]; label?: string }>();
</script>

<template>
    <ul
        v-if="items.length"
        class="flex flex-wrap gap-1.5"
        :aria-label="label ?? 'Technologies'"
    >
        <li v-for="item in items" :key="item" class="chip">{{ item }}</li>
    </ul>
</template>
