<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';

defineProps<{
    eyebrow?: string | null;
    title: string;
    intro?: string | null;
    breadcrumbs?: { label: string; href?: string }[];
}>();
</script>

<template>
    <header class="border-b border-border">
        <div class="container-site pt-14 pb-14 sm:pt-20 sm:pb-16">
            <nav
                v-if="breadcrumbs?.length"
                aria-label="Breadcrumb"
                class="no-print mb-8"
            >
                <ol
                    class="flex flex-wrap items-center gap-1.5 text-sm text-muted-foreground"
                >
                    <li
                        v-for="(crumb, i) in breadcrumbs"
                        :key="crumb.label"
                        class="flex items-center gap-1.5"
                    >
                        <Link
                            v-if="crumb.href"
                            :href="crumb.href"
                            class="hover:text-foreground"
                            >{{ crumb.label }}</Link
                        >
                        <span
                            v-else
                            aria-current="page"
                            class="text-foreground"
                            >{{ crumb.label }}</span
                        >
                        <ChevronRight
                            v-if="i < breadcrumbs.length - 1"
                            class="size-3.5"
                            aria-hidden="true"
                        />
                    </li>
                </ol>
            </nav>
            <p v-if="eyebrow" class="eyebrow">{{ eyebrow }}</p>
            <h1
                class="text-display mt-4 max-w-4xl !text-[clamp(2.25rem,1.5rem+3vw,3.75rem)] text-balance"
            >
                {{ title }}
            </h1>
            <p
                v-if="intro"
                class="text-lead mt-6 max-w-2xl text-muted-foreground"
            >
                {{ intro }}
            </p>
            <slot />
        </div>
    </header>
</template>
