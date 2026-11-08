<script setup lang="ts">
import PageHeader from '@/components/site/PageHeader.vue';
import Prose from '@/components/site/Prose.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import type { Seo } from '@/types';

defineProps<{ seo: Seo; html: string | null }>();
</script>

<template>
    <SeoHead :seo="seo" />
    <PageHeader
        title="Privacy policy"
        :breadcrumbs="[
            { label: 'Home', href: '/' },
            { label: 'Privacy policy' },
        ]"
    />
    <section class="container-site max-w-3xl py-16">
        <Prose v-if="html" :html="html" />
        <p v-else class="text-muted-foreground">
            The privacy policy has not been published yet.
        </p>
    </section>
</template>
