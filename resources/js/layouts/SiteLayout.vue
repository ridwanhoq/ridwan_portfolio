<script setup lang="ts">
import { defineAsyncComponent } from 'vue';
import SiteFooter from '@/components/site/SiteFooter.vue';
import SiteHeader from '@/components/site/SiteHeader.vue';

// Toasts only appear after an interaction, so their styles stay off the critical path.
const Toaster = defineAsyncComponent(
    () => import('@/components/ui/sonner/Sonner.vue'),
);
</script>

<template>
    <div class="flex min-h-svh flex-col">
        <SiteHeader />
        <main id="main" tabindex="-1" class="flex-1 outline-none">
            <slot />
        </main>
        <SiteFooter />
        <Toaster position="bottom-right" rich-colors close-button />
    </div>
</template>
