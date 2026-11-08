<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PageHeader from '@/components/site/PageHeader.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import type { PostItem, Seo } from '@/types';

defineProps<{
    seo: Seo;
    posts: PostItem[];
    pagination: {
        current: number;
        last: number;
        prev: string | null;
        next: string | null;
    };
}>();
</script>

<template>
    <SeoHead :seo="seo" />
    <PageHeader
        eyebrow="Writing"
        title="Notes on software engineering"
        :breadcrumbs="[{ label: 'Home', href: '/' }, { label: 'Blog' }]"
    />
    <section class="container-site max-w-3xl py-14">
        <ul
            v-if="posts.length"
            class="divide-y divide-border border-y border-border"
        >
            <li v-for="post in posts" :key="post.id" class="py-8">
                <p class="font-mono text-xs text-muted-foreground">
                    <time
                        v-if="post.published_at"
                        :datetime="post.published_at"
                        >{{ post.published_label }}</time
                    >
                    · {{ post.reading_minutes }} min read
                </p>
                <h2 class="mt-2 text-xl font-semibold tracking-tight">
                    <Link
                        :href="`/blog/${post.slug}`"
                        class="hover:text-brand"
                        >{{ post.title }}</Link
                    >
                </h2>
                <p class="mt-2 text-muted-foreground">{{ post.excerpt }}</p>
            </li>
        </ul>
        <p v-else class="text-muted-foreground">
            No articles have been published yet.
        </p>

        <nav
            v-if="pagination.last > 1"
            aria-label="Pagination"
            class="mt-10 flex justify-between text-sm font-semibold"
        >
            <Link
                v-if="pagination.prev"
                :href="pagination.prev"
                class="hover:text-brand"
                >← Newer</Link
            ><span v-else />
            <Link
                v-if="pagination.next"
                :href="pagination.next"
                class="hover:text-brand"
                >Older →</Link
            >
        </nav>
    </section>
</template>
