<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { Seo } from '@/types';

/**
 * Mirrors the server-rendered meta tags (resources/views/app.blade.php)
 * so they stay correct after client-side navigation.
 */
defineProps<{ seo: Seo }>();
</script>

<template>
    <Head>
        <title>{{ seo.title }}</title>
        <meta
            v-if="seo.description"
            head-key="description"
            name="description"
            :content="seo.description"
        />
        <meta head-key="robots" name="robots" :content="seo.robots" />
        <link head-key="canonical" rel="canonical" :href="seo.canonical" />
        <meta head-key="og:type" property="og:type" :content="seo.type" />
        <meta
            head-key="og:site_name"
            property="og:site_name"
            :content="seo.site_name"
        />
        <meta head-key="og:title" property="og:title" :content="seo.title" />
        <meta
            v-if="seo.description"
            head-key="og:description"
            property="og:description"
            :content="seo.description"
        />
        <meta head-key="og:url" property="og:url" :content="seo.canonical" />
        <meta head-key="og:image" property="og:image" :content="seo.image" />
        <meta head-key="og:locale" property="og:locale" content="en_US" />
        <meta
            head-key="twitter:card"
            name="twitter:card"
            content="summary_large_image"
        />
        <meta
            head-key="twitter:title"
            name="twitter:title"
            :content="seo.title"
        />
        <meta
            v-if="seo.description"
            head-key="twitter:description"
            name="twitter:description"
            :content="seo.description"
        />
        <meta
            head-key="twitter:image"
            name="twitter:image"
            :content="seo.image"
        />
    </Head>
</template>
