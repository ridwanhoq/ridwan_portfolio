<script setup lang="ts">
import PageHeader from '@/components/site/PageHeader.vue';
import Prose from '@/components/site/Prose.vue';
import ResponsiveImage from '@/components/site/ResponsiveImage.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import type { PostItem, Seo } from '@/types';

defineProps<{ seo: Seo; post: PostItem }>();
</script>

<template>
    <SeoHead :seo="seo" />
    <article>
        <PageHeader
            :title="post.title"
            :intro="post.excerpt"
            :breadcrumbs="[
                { label: 'Home', href: '/' },
                { label: 'Blog', href: '/blog' },
                { label: post.title },
            ]"
        >
            <p class="mt-6 font-mono text-xs text-muted-foreground">
                <time v-if="post.published_at" :datetime="post.published_at">{{
                    post.published_label
                }}</time>
                · {{ post.reading_minutes }} min read
            </p>
        </PageHeader>
        <div class="container-site max-w-3xl py-14">
            <div
                v-if="post.cover"
                class="mb-10 overflow-hidden rounded-lg border border-border"
            >
                <ResponsiveImage
                    :media="post.cover"
                    :alt="post.cover.alt || post.title"
                    eager
                    img-class="w-full"
                />
            </div>
            <Prose :html="post.body_html" />
        </div>
    </article>
</template>
