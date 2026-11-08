<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight } from '@lucide/vue';
import type { ProjectCard } from '@/types';
import ResponsiveImage from './ResponsiveImage.vue';
import TechList from './TechList.vue';

defineProps<{ project: ProjectCard; headingLevel?: 'h3' | 'h2' }>();
</script>

<template>
    <article
        class="group relative flex h-full flex-col overflow-hidden rounded-lg border border-border bg-card transition-[border-color,box-shadow] duration-300 hover:border-foreground/20 hover:shadow-[0_8px_30px_-12px_rgb(15_23_42/0.18)]"
        data-test="project-card"
    >
        <div
            class="aspect-[16/9] overflow-hidden border-b border-border bg-surface"
        >
            <ResponsiveImage
                v-if="project.thumbnail"
                :media="project.thumbnail"
                :alt="project.thumbnail.alt || `${project.title} screenshot`"
                sizes="(min-width: 1024px) 560px, 100vw"
                img-class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
            />
            <div
                v-else
                class="flex size-full flex-col justify-between bg-[linear-gradient(to_right,var(--border)_1px,transparent_1px),linear-gradient(to_bottom,var(--border)_1px,transparent_1px)] bg-[size:28px_28px] p-6"
                aria-hidden="true"
            >
                <span class="eyebrow">{{
                    project.category?.name ?? 'Project'
                }}</span>
                <span
                    class="max-w-[85%] text-xl font-semibold tracking-tight text-foreground/80"
                >
                    {{ project.project_type ?? project.title }}
                </span>
            </div>
        </div>

        <div class="flex flex-1 flex-col p-6">
            <div
                class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground"
            >
                <span v-if="project.category" class="font-medium text-brand">{{
                    project.category.name
                }}</span>
                <span v-if="project.client">{{ project.client }}</span>
            </div>

            <component
                :is="headingLevel ?? 'h3'"
                class="mt-3 text-lg leading-snug font-semibold tracking-tight"
            >
                <Link
                    :href="`/projects/${project.slug}`"
                    class="after:absolute after:inset-0 after:content-[''] focus-visible:outline-none"
                >
                    {{ project.title }}
                </Link>
            </component>

            <p class="mt-2 text-[15px] leading-relaxed text-muted-foreground">
                {{ project.summary }}
            </p>

            <p
                v-if="project.contribution"
                class="mt-4 border-l-2 border-brand/60 pl-3 text-sm leading-relaxed text-foreground/80"
            >
                <span class="sr-only">My contribution: </span
                >{{ project.contribution }}
            </p>

            <div class="mt-5">
                <TechList :items="project.technologies.slice(0, 6)" />
            </div>

            <div
                class="relative z-10 mt-auto flex flex-wrap items-center gap-4 pt-6 text-sm font-medium"
            >
                <span class="inline-flex items-center gap-1 text-foreground">
                    Read case study
                    <ArrowUpRight
                        class="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                        aria-hidden="true"
                    />
                </span>
                <a
                    v-if="project.live_url"
                    :href="project.live_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="link-underline text-muted-foreground hover:text-foreground"
                >
                    Live site<span class="sr-only">
                        for {{ project.title }} (opens in new tab)</span
                    >
                </a>
                <a
                    v-if="project.repo_url"
                    :href="project.repo_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="link-underline text-muted-foreground hover:text-foreground"
                >
                    Source<span class="sr-only">
                        for {{ project.title }} (opens in new tab)</span
                    >
                </a>
            </div>
        </div>
    </article>
</template>
