<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, FileDown } from '@lucide/vue';
import EducationList from '@/components/site/EducationList.vue';
import ExperienceTimeline from '@/components/site/ExperienceTimeline.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import PrincipleGrid from '@/components/site/PrincipleGrid.vue';
import Prose from '@/components/site/Prose.vue';
import ResponsiveImage from '@/components/site/ResponsiveImage.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import SkillGroups from '@/components/site/SkillGroups.vue';
import type {
    CertificationItem,
    EducationItem,
    ExperienceItem,
    PrincipleItem,
    Profile,
    Seo,
    SkillGroup,
} from '@/types';

const props = defineProps<{
    seo: Seo;
    profile: Profile;
    experiences: ExperienceItem[];
    skills: SkillGroup[];
    principles: PrincipleItem[];
    education: EducationItem[];
    certifications: CertificationItem[];
}>();

const blocks = [
    { key: 'philosophy', title: 'Engineering philosophy' },
    { key: 'career_overview', title: 'Career overview' },
    { key: 'specialization', title: 'Technical specialisation' },
    { key: 'interests', title: 'Professional interests' },
    { key: 'collaboration', title: 'Remote and international collaboration' },
] as const;

const visibleBlocks = blocks.filter((b) => props.profile.about[b.key]);
</script>

<template>
    <SeoHead :seo="seo" />

    <PageHeader
        eyebrow="About"
        :title="`${profile.full_name}`"
        :intro="profile.summary"
        :breadcrumbs="[{ label: 'Home', href: '/' }, { label: 'About' }]"
    >
        <div class="mt-8 flex flex-wrap gap-3">
            <a
                href="/resume/download"
                class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground hover:opacity-90"
            >
                <FileDown class="size-4" aria-hidden="true" /> Download résumé
            </a>
            <Link
                href="/contact"
                class="inline-flex items-center gap-2 rounded-md border border-border px-5 py-3 text-sm font-semibold hover:border-foreground/30"
            >
                Get in touch <ArrowRight class="size-4" aria-hidden="true" />
            </Link>
        </div>
    </PageHeader>

    <section
        class="border-b border-border py-20"
        aria-labelledby="intro-heading"
    >
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-4">
                <h2 id="intro-heading" class="sr-only">Introduction</h2>
                <div
                    v-if="profile.photo"
                    class="overflow-hidden rounded-lg border border-border"
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
                <dl
                    class="grid gap-4 text-sm"
                    :class="{ 'mt-8': profile.photo }"
                >
                    <div>
                        <dt class="eyebrow">Role</dt>
                        <dd class="mt-1 font-medium">
                            {{ profile.designation }}
                        </dd>
                    </div>
                    <div v-if="profile.location">
                        <dt class="eyebrow">Based in</dt>
                        <dd class="mt-1 font-medium">{{ profile.location }}</dd>
                    </div>
                    <div v-if="profile.work_preference">
                        <dt class="eyebrow">Availability</dt>
                        <dd class="mt-1 font-medium">
                            {{ profile.work_preference }}
                        </dd>
                    </div>
                    <div v-if="profile.languages.length">
                        <dt class="eyebrow">Languages</dt>
                        <dd class="mt-1 font-medium">
                            {{ profile.languages.join(', ') }}
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="grid gap-14 lg:col-span-8">
                <Prose
                    :html="profile.about.intro"
                    class="text-lead !text-foreground/85"
                />
                <div v-for="block in visibleBlocks" :key="block.key" v-reveal>
                    <h2 class="text-2xl font-semibold tracking-tight">
                        {{ block.title }}
                    </h2>
                    <Prose :html="profile.about[block.key]" class="mt-4" />
                </div>
            </div>
        </div>
    </section>

    <section
        v-if="principles.length"
        class="border-b border-border bg-surface py-20"
        aria-labelledby="approach-heading"
    >
        <div class="container-site">
            <h2 id="approach-heading" class="text-title mb-12">
                How I build software
            </h2>
            <PrincipleGrid :items="principles" />
        </div>
    </section>

    <section
        v-if="experiences.length"
        class="border-b border-border py-20"
        aria-labelledby="experience-heading"
    >
        <div class="container-site">
            <h2 id="experience-heading" class="text-title mb-12">Experience</h2>
            <ExperienceTimeline :items="experiences" />
        </div>
    </section>

    <section
        v-if="skills.length"
        class="border-b border-border bg-surface py-20"
        aria-labelledby="skills-heading"
    >
        <div class="container-site">
            <h2 id="skills-heading" class="text-title mb-12">
                Technical skills
            </h2>
            <SkillGroups :groups="skills" />
        </div>
    </section>

    <section
        v-if="education.length || certifications.length"
        class="py-20"
        aria-labelledby="education-heading"
    >
        <div class="container-site">
            <h2 id="education-heading" class="text-title mb-12">
                Education and training
            </h2>
            <EducationList
                :education="education"
                :certifications="certifications"
            />
        </div>
    </section>
</template>
