<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ArrowDown, FileDown } from '@lucide/vue';
import { computed } from 'vue';
import BrandIcon from '@/components/site/BrandIcon.vue';
import ResponsiveImage from '@/components/site/ResponsiveImage.vue';
import type { Profile } from '@/types';

const props = defineProps<{
    profile: Profile;
    projectCount: number;
    exploreHref: string;
}>();

const page = usePage();
const site = computed(() => page.props.site);
const heroSocials = computed(() =>
    (site.value?.socials ?? []).filter((s) => s.show_in_hero),
);

const stats = computed(() =>
    [
        props.profile.years_experience
            ? {
                  value: `${props.profile.years_experience}+`,
                  label: 'Years of web development',
              }
            : null,
        props.profile.years_primary_stack
            ? {
                  value: `${props.profile.years_primary_stack}+`,
                  label: `Years with ${props.profile.primary_stack_label ?? 'my primary stack'}`,
              }
            : null,
        props.projectCount > 0
            ? {
                  value: String(props.projectCount),
                  label:
                      props.projectCount === 1
                          ? 'Published case study'
                          : 'Published case studies',
              }
            : null,
    ].filter((s): s is { value: string; label: string } => s !== null),
);
</script>

<template>
    <section
        aria-labelledby="hero-heading"
        class="relative overflow-hidden border-b border-border"
    >
        <div
            class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(to_right,var(--border)_1px,transparent_1px)] [mask-image:linear-gradient(to_bottom,black,transparent_85%)] bg-[size:calc(100%/12)_100%] opacity-40"
            aria-hidden="true"
        />

        <div
            class="container-site grid gap-12 pt-16 pb-16 sm:pt-24 lg:grid-cols-12 lg:gap-8 lg:pt-28 lg:pb-24"
        >
            <div class="lg:col-span-8">
                <p
                    v-if="site?.availability.enabled && site.availability.text"
                    class="mb-8 inline-flex items-center gap-2 rounded-full border border-border bg-background px-3 py-1 text-xs font-medium text-foreground/80"
                    data-test="availability"
                >
                    <span class="relative flex size-2" aria-hidden="true">
                        <span
                            class="absolute inline-flex size-full animate-ping rounded-full bg-success opacity-50 motion-reduce:hidden"
                        />
                        <span
                            class="relative inline-flex size-2 rounded-full bg-success"
                        />
                    </span>
                    {{ site.availability.text }}
                </p>

                <p class="eyebrow">
                    {{ profile.designation
                    }}<template v-if="profile.tagline">
                        · {{ profile.tagline }}</template
                    >
                </p>

                <h1
                    id="hero-heading"
                    class="text-display mt-5 max-w-[16ch] text-balance"
                >
                    {{ profile.hero_title }}
                </h1>

                <p
                    v-if="profile.hero_subtitle"
                    class="text-lead mt-7 max-w-2xl text-muted-foreground"
                >
                    {{ profile.hero_subtitle }}
                </p>

                <div class="mt-10 flex flex-wrap items-center gap-3">
                    <a
                        :href="exploreHref"
                        class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90"
                        data-test="hero-explore"
                    >
                        Explore my work
                        <ArrowDown class="size-4" aria-hidden="true" />
                    </a>
                    <a
                        href="/resume/download"
                        class="inline-flex items-center gap-2 rounded-md border border-border bg-background px-5 py-3 text-sm font-semibold transition-colors hover:border-foreground/30"
                        data-test="hero-resume"
                    >
                        <FileDown class="size-4" aria-hidden="true" />
                        Download résumé
                    </a>
                    <ul
                        v-if="heroSocials.length"
                        class="ml-1 flex items-center gap-1"
                        aria-label="Social profiles"
                    >
                        <li v-for="social in heroSocials" :key="social.url">
                            <a
                                :href="social.url"
                                target="_blank"
                                rel="noopener noreferrer me"
                                class="inline-flex size-11 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                                :aria-label="`${social.label} (opens in new tab)`"
                            >
                                <BrandIcon
                                    :platform="social.platform"
                                    class="size-5"
                                />
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <aside class="lg:col-span-4 lg:pt-2" aria-label="At a glance">
                <div
                    v-if="profile.photo"
                    class="mb-6 overflow-hidden rounded-lg border border-border bg-surface"
                >
                    <ResponsiveImage
                        :media="profile.photo"
                        :alt="
                            profile.photo.alt ||
                            `Portrait of ${profile.full_name}`
                        "
                        sizes="(min-width: 1024px) 360px, 100vw"
                        eager
                        img-class="aspect-[4/5] w-full object-cover"
                    />
                </div>

                <div class="rounded-lg border border-border bg-background/80">
                    <dl v-if="stats.length" class="grid divide-y divide-border">
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="flex items-baseline justify-between gap-4 px-5 py-4"
                        >
                            <dt class="text-sm text-muted-foreground">
                                {{ stat.label }}
                            </dt>
                            <dd
                                class="font-mono text-2xl font-semibold tracking-tight"
                            >
                                {{ stat.value }}
                            </dd>
                        </div>
                    </dl>
                    <div
                        v-if="profile.hero_technologies.length"
                        class="border-t border-border px-5 py-4"
                    >
                        <p class="eyebrow mb-3">Core stack</p>
                        <ul class="flex flex-wrap gap-1.5">
                            <li
                                v-for="tech in profile.hero_technologies"
                                :key="tech"
                                class="chip"
                            >
                                {{ tech }}
                            </li>
                        </ul>
                    </div>
                    <div
                        v-if="profile.location || profile.work_preference"
                        class="border-t border-border px-5 py-4 text-sm"
                    >
                        <p v-if="profile.location" class="font-medium">
                            {{ profile.location }}
                        </p>
                        <p
                            v-if="profile.work_preference"
                            class="mt-0.5 text-muted-foreground"
                        >
                            {{ profile.work_preference }}
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</template>
