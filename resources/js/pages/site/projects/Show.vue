<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowUpRight, ExternalLink } from '@lucide/vue';
import { computed } from 'vue';
import BrandIcon from '@/components/site/BrandIcon.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import ProjectCard from '@/components/site/ProjectCard.vue';
import Prose from '@/components/site/Prose.vue';
import ResponsiveImage from '@/components/site/ResponsiveImage.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import TechList from '@/components/site/TechList.vue';
import type {
    ProjectCard as ProjectCardType,
    ProjectDetail,
    Seo,
} from '@/types';

const props = defineProps<{
    seo: Seo;
    project: ProjectDetail;
    related: ProjectCardType[];
}>();

const facts = computed(() =>
    [
        { label: 'Client', value: props.project.client },
        { label: 'Type', value: props.project.project_type },
        { label: 'Category', value: props.project.category?.name },
        { label: 'Period', value: props.project.period },
    ].filter((f) => f.value),
);

const screenshots = computed(() =>
    props.project.images.filter((i) => i.kind === 'screenshot'),
);
const diagrams = computed(() =>
    props.project.images.filter((i) => i.kind === 'diagram'),
);
const toc = computed(() => [
    ...props.project.sections.map((s) => ({ id: s.key, title: s.title })),
    ...(props.project.images.length
        ? [{ id: 'gallery', title: 'Screenshots and diagrams' }]
        : []),
    ...(props.project.links.length ? [{ id: 'links', title: 'Links' }] : []),
]);
</script>

<template>
    <SeoHead :seo="seo" />

    <article>
        <PageHeader
            :eyebrow="project.category?.name ?? 'Case study'"
            :title="project.title"
            :intro="project.summary"
            :breadcrumbs="[
                { label: 'Home', href: '/' },
                { label: 'Projects', href: '/projects' },
                { label: project.title },
            ]"
        >
            <dl
                v-if="facts.length"
                class="mt-10 grid gap-6 border-t border-border pt-8 sm:grid-cols-2 lg:grid-cols-4"
            >
                <div v-for="fact in facts" :key="fact.label">
                    <dt class="eyebrow">{{ fact.label }}</dt>
                    <dd class="mt-1.5 text-[15px] font-medium">
                        {{ fact.value }}
                    </dd>
                </div>
            </dl>
            <div
                v-if="project.live_url || project.repo_url"
                class="mt-8 flex flex-wrap gap-3"
            >
                <a
                    v-if="project.live_url"
                    :href="project.live_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2.5 text-sm font-semibold text-primary-foreground hover:opacity-90"
                >
                    <ExternalLink class="size-4" aria-hidden="true" /> Visit
                    live site<span class="sr-only"> (opens in new tab)</span>
                </a>
                <a
                    v-if="project.repo_url"
                    :href="project.repo_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-md border border-border px-4 py-2.5 text-sm font-semibold hover:border-foreground/30"
                >
                    <BrandIcon platform="github" class="size-4" /> View
                    source<span class="sr-only"> (opens in new tab)</span>
                </a>
            </div>
        </PageHeader>

        <div v-if="project.thumbnail" class="border-b border-border bg-surface">
            <div class="container-site py-10">
                <div class="overflow-hidden rounded-lg border border-border">
                    <ResponsiveImage
                        :media="project.thumbnail"
                        :alt="
                            project.thumbnail.alt || `${project.title} overview`
                        "
                        sizes="(min-width: 1152px) 1088px, 100vw"
                        eager
                        img-class="w-full"
                    />
                </div>
            </div>
        </div>

        <div class="container-site grid gap-14 py-16 lg:grid-cols-12 lg:gap-10">
            <aside class="lg:order-2 lg:col-span-4">
                <div class="grid gap-8 lg:sticky lg:top-24">
                    <div v-if="project.technologies.length">
                        <h2 class="eyebrow mb-3">Technology stack</h2>
                        <TechList :items="project.technologies" />
                    </div>
                    <div v-if="project.contribution">
                        <h2 class="eyebrow mb-3">My contribution</h2>
                        <p
                            class="text-[15px] leading-relaxed text-foreground/85"
                        >
                            {{ project.contribution }}
                        </p>
                    </div>
                    <nav
                        v-if="toc.length > 2"
                        aria-label="On this page"
                        class="no-print hidden lg:block"
                    >
                        <h2 class="eyebrow mb-3">On this page</h2>
                        <ul class="grid gap-2 border-l border-border text-sm">
                            <li v-for="item in toc" :key="item.id">
                                <a
                                    :href="`#${item.id}`"
                                    class="-ml-px block border-l border-transparent pl-4 text-muted-foreground hover:border-foreground hover:text-foreground"
                                    >{{ item.title }}</a
                                >
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <div class="grid gap-14 lg:order-1 lg:col-span-8">
                <section
                    v-for="section in project.sections"
                    :id="section.key"
                    :key="section.key"
                    :aria-labelledby="`${section.key}-title`"
                >
                    <h2
                        :id="`${section.key}-title`"
                        class="text-2xl font-semibold tracking-tight"
                    >
                        {{ section.title }}
                    </h2>
                    <Prose :html="section.html" class="mt-4" />
                </section>

                <section
                    v-if="project.images.length"
                    id="gallery"
                    aria-labelledby="gallery-title"
                >
                    <h2
                        id="gallery-title"
                        class="text-2xl font-semibold tracking-tight"
                    >
                        Screenshots and diagrams
                    </h2>
                    <div
                        v-for="group in [
                            { label: 'Screenshots', items: screenshots },
                            { label: 'Diagrams', items: diagrams },
                        ]"
                        :key="group.label"
                    >
                        <template v-if="group.items.length">
                            <h3
                                v-if="screenshots.length && diagrams.length"
                                class="eyebrow mt-8"
                            >
                                {{ group.label }}
                            </h3>
                            <div class="mt-5 grid gap-6">
                                <figure
                                    v-for="image in group.items"
                                    :key="image.id"
                                >
                                    <a
                                        :href="image.media.url"
                                        target="_blank"
                                        rel="noopener"
                                        class="block overflow-hidden rounded-lg border border-border bg-surface"
                                    >
                                        <ResponsiveImage
                                            :media="image.media"
                                            :alt="
                                                image.media.alt ||
                                                image.caption ||
                                                `${project.title} ${image.kind}`
                                            "
                                            sizes="(min-width: 1024px) 720px, 100vw"
                                            img-class="w-full"
                                        />
                                        <span class="sr-only"
                                            >Open full-size image in new
                                            tab</span
                                        >
                                    </a>
                                    <figcaption
                                        v-if="image.caption"
                                        class="mt-2.5 text-sm text-muted-foreground"
                                    >
                                        {{ image.caption }}
                                    </figcaption>
                                </figure>
                            </div>
                        </template>
                    </div>
                </section>

                <section
                    v-if="project.links.length"
                    id="links"
                    aria-labelledby="links-title"
                >
                    <h2
                        id="links-title"
                        class="text-2xl font-semibold tracking-tight"
                    >
                        Links
                    </h2>
                    <ul
                        class="mt-4 divide-y divide-border border-y border-border"
                    >
                        <li v-for="link in project.links" :key="link.url">
                            <a
                                :href="link.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center justify-between gap-4 py-3.5 font-medium hover:text-brand"
                            >
                                {{ link.label }}
                                <ArrowUpRight
                                    class="size-4 shrink-0"
                                    aria-hidden="true"
                                />
                                <span class="sr-only">(opens in new tab)</span>
                            </a>
                        </li>
                    </ul>
                </section>

                <p
                    v-if="!project.sections.length"
                    class="text-muted-foreground"
                >
                    A detailed case study for this project is being prepared.
                </p>
            </div>
        </div>
    </article>

    <section
        v-if="related.length"
        class="no-print border-t border-border bg-surface py-16"
        aria-labelledby="related-heading"
    >
        <div class="container-site">
            <div class="mb-8 flex items-end justify-between gap-4">
                <h2 id="related-heading" class="text-title">More projects</h2>
                <Link
                    href="/projects"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold hover:text-brand"
                >
                    <ArrowLeft class="size-4" aria-hidden="true" /> All projects
                </Link>
            </div>
            <div class="grid gap-6 md:grid-cols-2">
                <ProjectCard
                    v-for="item in related"
                    :key="item.id"
                    :project="item"
                />
            </div>
        </div>
    </section>
</template>
