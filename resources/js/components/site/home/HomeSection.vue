<script setup lang="ts">
import SectionHeading from '@/components/site/SectionHeading.vue';
import type { Section } from '@/types';

defineProps<{
    section: Section;
    index: number;
    muted?: boolean;
    split?: boolean;
}>();
</script>

<template>
    <section
        :id="section.key"
        :aria-labelledby="`${section.key}-heading`"
        class="border-b border-border py-20 sm:py-28"
        :class="{ 'bg-surface': muted }"
    >
        <div
            class="container-site"
            :class="split ? 'grid gap-12 lg:grid-cols-12 lg:gap-8' : ''"
        >
            <div :class="split ? 'lg:col-span-4' : 'mb-14'">
                <div :class="split ? 'lg:sticky lg:top-28' : ''">
                    <SectionHeading
                        :id="`${section.key}-heading`"
                        :eyebrow="section.eyebrow"
                        :heading="section.heading"
                        :intro="section.intro"
                        :index="index"
                    />
                    <slot name="aside" />
                </div>
            </div>
            <div :class="split ? 'lg:col-span-8' : ''">
                <slot />
            </div>
        </div>
    </section>
</template>
