<script setup lang="ts">
/**
 * Renders Markdown HTML that was sanitised on the server
 * (raw HTML stripped, unsafe links removed). Never pass user input here directly.
 */
defineProps<{ html: string | null | undefined }>();
</script>

<template>
    <!-- eslint-disable-next-line vue/no-v-html -->
    <div v-if="html" class="prose-content" v-html="html" />
</template>
