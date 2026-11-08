<script setup lang="ts">
defineProps<{ value: string }>();

const styles: Record<string, string> = {
    published: 'bg-success/10 text-success ring-success/25',
    draft: 'bg-muted text-muted-foreground ring-border',
    unread: 'bg-primary/10 text-primary ring-primary/25',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium capitalize ring-1 ring-inset"
        :class="styles[value] ?? 'bg-muted text-foreground/80 ring-border'"
    >
        {{ value.replace(/_/g, ' ') }}
    </span>
</template>
