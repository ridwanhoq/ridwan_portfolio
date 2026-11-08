<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import type { Paginated } from '@/lib/api';

defineProps<{
    meta: Pick<
        Paginated<unknown>,
        | 'from'
        | 'to'
        | 'total'
        | 'prev_page_url'
        | 'next_page_url'
        | 'current_page'
        | 'last_page'
    >;
}>();
</script>

<template>
    <nav
        v-if="meta.total > 0"
        class="flex items-center justify-between gap-4 text-sm"
        aria-label="Pagination"
    >
        <p class="text-muted-foreground">
            Showing
            <span class="font-medium text-foreground"
                >{{ meta.from }}–{{ meta.to }}</span
            >
            of
            <span class="font-medium text-foreground">{{ meta.total }}</span>
        </p>
        <div v-if="meta.last_page > 1" class="flex items-center gap-1">
            <component
                :is="meta.prev_page_url ? Link : 'span'"
                :href="meta.prev_page_url ?? undefined"
                preserve-scroll
                class="inline-flex size-8 items-center justify-center rounded-md border border-border"
                :class="meta.prev_page_url ? 'hover:bg-muted' : 'opacity-40'"
                aria-label="Previous page"
            >
                <ChevronLeft class="size-4" />
            </component>
            <span class="px-2 text-muted-foreground"
                >{{ meta.current_page }} / {{ meta.last_page }}</span
            >
            <component
                :is="meta.next_page_url ? Link : 'span'"
                :href="meta.next_page_url ?? undefined"
                preserve-scroll
                class="inline-flex size-8 items-center justify-center rounded-md border border-border"
                :class="meta.next_page_url ? 'hover:bg-muted' : 'opacity-40'"
                aria-label="Next page"
            >
                <ChevronRight class="size-4" />
            </component>
        </div>
    </nav>
</template>
