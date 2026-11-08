<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import SeoHead from '@/components/site/SeoHead.vue';
import type { Seo } from '@/types';

defineProps<{ status: number; title: string; message: string; seo: Seo }>();
</script>

<template>
    <SeoHead :seo="seo" />
    <section
        class="container-site flex min-h-[60vh] flex-col justify-center py-24"
        aria-labelledby="error-title"
    >
        <p class="font-mono text-sm text-brand">Error {{ status }}</p>
        <h1 id="error-title" class="text-display mt-4">{{ title }}</h1>
        <p class="text-lead mt-6 max-w-xl text-muted-foreground">
            {{ message }}
        </p>
        <div class="mt-10 flex flex-wrap gap-3">
            <Link
                href="/"
                class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground hover:opacity-90"
            >
                <ArrowLeft class="size-4" aria-hidden="true" /> Back to home
            </Link>
            <Link
                href="/projects"
                class="inline-flex items-center gap-2 rounded-md border border-border px-5 py-3 text-sm font-semibold hover:border-foreground/30"
            >
                View projects
            </Link>
        </div>
    </section>
</template>
