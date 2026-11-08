<script setup lang="ts">
import { ArrowUpRight } from '@lucide/vue';
import type { ExperienceItem } from '@/types';
import Prose from './Prose.vue';
import TechList from './TechList.vue';

defineProps<{ items: ExperienceItem[]; compact?: boolean }>();
</script>

<template>
    <ol class="relative" data-test="experience-timeline">
        <li
            v-for="(job, i) in items"
            :key="job.id"
            class="relative grid gap-4 pb-12 pl-8 last:pb-0 md:grid-cols-[13rem_1fr] md:gap-10 md:pl-0"
        >
            <!-- Timeline rail -->
            <span
                class="absolute top-2 bottom-0 left-[5px] w-px bg-border md:left-[calc(13rem+1.25rem-0.5px)]"
                :class="{ hidden: i === items.length - 1 }"
                aria-hidden="true"
            />
            <span
                class="absolute top-1.5 left-0 size-[11px] rounded-full border-2 md:left-[calc(13rem+1.25rem-5.5px)]"
                :class="
                    job.is_current
                        ? 'border-brand bg-brand'
                        : 'border-border bg-background'
                "
                aria-hidden="true"
            />

            <div class="text-sm md:pt-0.5 md:text-right">
                <p class="font-mono text-[13px] text-muted-foreground">
                    <time :datetime="job.start">{{
                        job.period.split(' – ')[0]
                    }}</time>
                    –
                    <time v-if="job.end" :datetime="job.end">{{
                        job.period.split(' – ')[1]
                    }}</time>
                    <span v-else>Present</span>
                </p>
                <p
                    v-if="job.is_current"
                    class="mt-1 text-xs font-medium text-brand"
                >
                    Current role
                </p>
            </div>

            <div class="md:pl-10">
                <h3 class="text-lg font-semibold tracking-tight">
                    {{ job.title }}
                </h3>
                <p class="mt-0.5 text-[15px] text-foreground/80">
                    <a
                        v-if="job.company_url"
                        :href="job.company_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-0.5 font-medium hover:text-brand"
                    >
                        {{ job.company
                        }}<ArrowUpRight class="size-3.5" aria-hidden="true" />
                    </a>
                    <span v-else class="font-medium">{{ job.company }}</span>
                    <span v-if="job.location" class="text-muted-foreground">
                        · {{ job.location }}</span
                    >
                </p>
                <p
                    v-if="job.focus || job.employment_type"
                    class="mt-1 text-sm text-muted-foreground"
                >
                    {{
                        [job.focus, job.employment_type]
                            .filter(Boolean)
                            .join(' · ')
                    }}
                </p>

                <Prose
                    v-if="job.description_html"
                    :html="job.description_html"
                    class="mt-4 !text-[15px]"
                />

                <ul
                    v-if="job.responsibilities.length"
                    class="mt-4 space-y-2 text-[15px] leading-relaxed text-foreground/80"
                    :aria-label="`Responsibilities at ${job.company}`"
                >
                    <li
                        v-for="item in compact
                            ? job.responsibilities.slice(0, 4)
                            : job.responsibilities"
                        :key="item"
                        class="relative pl-4 before:absolute before:top-[0.7em] before:left-0 before:h-px before:w-2 before:bg-muted-foreground/60"
                    >
                        {{ item }}
                    </li>
                </ul>

                <div v-if="job.contributions.length" class="mt-5">
                    <p class="eyebrow mb-2">Key contributions</p>
                    <ul
                        class="space-y-2 text-[15px] leading-relaxed text-foreground/80"
                    >
                        <li
                            v-for="item in job.contributions"
                            :key="item"
                            class="relative pl-4 before:absolute before:top-[0.7em] before:left-0 before:size-1.5 before:-translate-y-1/2 before:rounded-full before:bg-brand"
                        >
                            {{ item }}
                        </li>
                    </ul>
                </div>

                <div class="mt-5">
                    <TechList
                        :items="job.technologies"
                        :label="`Technologies used at ${job.company}`"
                    />
                </div>
            </div>
        </li>
    </ol>
</template>
