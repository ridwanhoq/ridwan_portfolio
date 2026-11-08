<script setup lang="ts">
import { Search, X } from '@lucide/vue';
import { computed, onMounted, ref } from 'vue';
import PageHeader from '@/components/site/PageHeader.vue';
import ProjectCard from '@/components/site/ProjectCard.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import type { ProjectCard as ProjectCardType, Seo } from '@/types';

const props = defineProps<{
    seo: Seo;
    projects: ProjectCardType[];
    categories: { name: string; slug: string; count: number }[];
    showFilters: boolean;
}>();

const initial =
    typeof window !== 'undefined'
        ? new URLSearchParams(window.location.search)
        : null;
const category = ref<string>(initial?.get('category') ?? '');
const query = ref<string>(initial?.get('q') ?? '');

/** Filters only make sense with several categories and enough projects. */
const canFilter = computed(
    () =>
        props.showFilters &&
        props.categories.length > 1 &&
        props.projects.length >= 4,
);
const canSearch = computed(() => props.projects.length >= 6);

const filtered = computed(() => {
    const term = query.value.trim().toLowerCase();

    return props.projects.filter((p) => {
        if (category.value && p.category?.slug !== category.value) {
            return false;
        }

        if (!term) {
            return true;
        }

        return [
            p.title,
            p.summary,
            p.client ?? '',
            p.category?.name ?? '',
            ...p.technologies,
        ]
            .join(' ')
            .toLowerCase()
            .includes(term);
    });
});

function syncUrl(): void {
    const params = new URLSearchParams();

    if (category.value) params.set('category', category.value);
    if (query.value.trim()) params.set('q', query.value.trim());

    const qs = params.toString();
    window.history.replaceState(
        window.history.state,
        '',
        qs ? `?${qs}` : window.location.pathname,
    );
}

function selectCategory(slug: string): void {
    category.value = slug;
    syncUrl();
}

function clear(): void {
    category.value = '';
    query.value = '';
    syncUrl();
}
</script>

<template>
    <SeoHead :seo="seo" />

    <PageHeader
        eyebrow="Projects"
        title="Projects and case studies"
        intro="Platforms I have designed and built, with notes on the problems, my role and the technology behind each one."
        :breadcrumbs="[{ label: 'Home', href: '/' }, { label: 'Projects' }]"
    />

    <section class="py-14 sm:py-20" aria-label="Project list">
        <div class="container-site">
            <div
                v-if="canFilter || canSearch"
                class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div
                    v-if="canFilter"
                    role="group"
                    aria-label="Filter by category"
                    class="flex flex-wrap gap-2"
                >
                    <button
                        type="button"
                        class="rounded-md border px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="
                            category === ''
                                ? 'border-foreground bg-foreground text-background'
                                : 'border-border text-muted-foreground hover:text-foreground'
                        "
                        :aria-pressed="category === ''"
                        @click="selectCategory('')"
                    >
                        All
                        <span class="ml-1 opacity-60">{{
                            projects.length
                        }}</span>
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat.slug"
                        type="button"
                        class="rounded-md border px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="
                            category === cat.slug
                                ? 'border-foreground bg-foreground text-background'
                                : 'border-border text-muted-foreground hover:text-foreground'
                        "
                        :aria-pressed="category === cat.slug"
                        data-test="category-filter"
                        @click="selectCategory(cat.slug)"
                    >
                        {{ cat.name }}
                        <span class="ml-1 opacity-60">{{ cat.count }}</span>
                    </button>
                </div>
                <div v-if="canSearch" class="relative sm:w-72">
                    <label for="project-search" class="sr-only"
                        >Search projects</label
                    >
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <input
                        id="project-search"
                        v-model="query"
                        type="search"
                        placeholder="Search by name or technology"
                        class="w-full rounded-md border border-input bg-background py-2 pr-3 pl-9 text-sm focus:border-ring focus:ring-2 focus:ring-ring/25 focus:outline-none"
                        @input="syncUrl"
                    />
                </div>
            </div>

            <p class="sr-only" role="status" aria-live="polite">
                {{ filtered.length }} projects shown
            </p>

            <div v-if="filtered.length" class="grid gap-6 md:grid-cols-2">
                <ProjectCard
                    v-for="project in filtered"
                    :key="project.id"
                    :project="project"
                    heading-level="h2"
                />
            </div>

            <div
                v-else
                class="rounded-lg border border-dashed border-border px-6 py-16 text-center"
            >
                <p class="font-medium">No projects match your filters.</p>
                <button
                    type="button"
                    class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand"
                    @click="clear"
                >
                    <X class="size-4" aria-hidden="true" /> Clear filters
                </button>
            </div>
        </div>
    </section>
</template>
