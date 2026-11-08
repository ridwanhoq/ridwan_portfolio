<script setup lang="ts">
import { FileDown, Printer } from '@lucide/vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SeoHead from '@/components/site/SeoHead.vue';
import type {
    CertificationItem,
    EducationItem,
    ExperienceItem,
    Profile,
    ProjectCard,
    Seo,
    SkillGroup,
} from '@/types';

defineProps<{
    seo: Seo;
    profile: Profile;
    experiences: ExperienceItem[];
    skills: SkillGroup[];
    education: EducationItem[];
    certifications: CertificationItem[];
    projects: ProjectCard[];
}>();

const page = usePage();
const links = computed(() =>
    (page.props.site?.socials ?? []).filter((s) => s.platform !== 'email'),
);

function print(): void {
    window.print();
}
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="no-print border-b border-border bg-surface">
        <div
            class="container-site flex flex-col gap-4 py-6 sm:flex-row sm:items-center sm:justify-between"
        >
            <p class="text-sm text-muted-foreground">
                An ATS-friendly PDF of this résumé is available to download.
            </p>
            <div class="flex gap-2">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-md border border-border bg-background px-4 py-2 text-sm font-semibold hover:border-foreground/30"
                    @click="print"
                >
                    <Printer class="size-4" aria-hidden="true" /> Print
                </button>
                <a
                    href="/resume/download"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground hover:opacity-90"
                    data-test="resume-download"
                >
                    <FileDown class="size-4" aria-hidden="true" /> Download PDF
                </a>
            </div>
        </div>
    </div>

    <article
        class="container-site max-w-4xl py-14 print:max-w-none print:py-0"
        aria-label="Résumé"
    >
        <header class="border-b border-border pb-8">
            <h1 class="text-4xl font-semibold tracking-tight">
                {{ profile.full_name }}
            </h1>
            <p class="mt-2 text-lg text-foreground/80">
                {{ profile.designation
                }}<template v-if="profile.tagline">
                    · {{ profile.tagline }}</template
                >
            </p>
            <ul
                class="mt-4 flex flex-wrap gap-x-5 gap-y-1 text-sm text-muted-foreground"
            >
                <li v-if="profile.location">{{ profile.location }}</li>
                <li v-if="profile.work_preference">
                    {{ profile.work_preference }}
                </li>
                <li v-if="profile.email">
                    <a
                        :href="`mailto:${profile.email}`"
                        class="hover:text-foreground"
                        >{{ profile.email }}</a
                    >
                </li>
                <li v-if="profile.phone">{{ profile.phone }}</li>
                <li v-for="link in links" :key="link.url">
                    <a :href="link.url" class="hover:text-foreground">{{
                        link.url.replace(/^https?:\/\/(www\.)?/, '')
                    }}</a>
                </li>
            </ul>
        </header>

        <section
            v-if="profile.summary"
            class="border-b border-border py-8"
            aria-labelledby="r-summary"
        >
            <h2 id="r-summary" class="eyebrow mb-3 !text-brand">
                Professional summary
            </h2>
            <p class="leading-relaxed text-foreground/85">
                {{ profile.summary }}
            </p>
        </section>

        <section
            v-if="skills.length"
            class="border-b border-border py-8"
            aria-labelledby="r-skills"
        >
            <h2 id="r-skills" class="eyebrow mb-4 !text-brand">Core skills</h2>
            <dl class="grid gap-2.5 text-[15px]">
                <div
                    v-for="group in skills"
                    :key="group.name"
                    class="grid gap-1 sm:grid-cols-[10rem_1fr]"
                >
                    <dt class="font-semibold">{{ group.name }}</dt>
                    <dd class="text-foreground/80">
                        {{ group.skills.join(', ') }}
                    </dd>
                </div>
            </dl>
        </section>

        <section
            v-if="experiences.length"
            class="border-b border-border py-8"
            aria-labelledby="r-experience"
        >
            <h2 id="r-experience" class="eyebrow mb-6 !text-brand">
                Professional experience
            </h2>
            <div class="grid gap-8">
                <div
                    v-for="job in experiences"
                    :key="job.id"
                    class="break-inside-avoid"
                >
                    <div
                        class="flex flex-col justify-between gap-1 sm:flex-row sm:items-baseline"
                    >
                        <h3 class="font-semibold">
                            {{ job.title }} · {{ job.company }}
                        </h3>
                        <p class="font-mono text-sm text-muted-foreground">
                            {{ job.period }}
                        </p>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        {{
                            [job.focus, job.location]
                                .filter(Boolean)
                                .join(' · ')
                        }}
                    </p>
                    <ul
                        class="mt-3 list-disc space-y-1.5 pl-5 text-[15px] text-foreground/85 marker:text-muted-foreground"
                    >
                        <li
                            v-for="item in [
                                ...job.responsibilities,
                                ...job.contributions,
                            ]"
                            :key="item"
                        >
                            {{ item }}
                        </li>
                    </ul>
                    <p
                        v-if="job.technologies.length"
                        class="mt-2 text-sm text-muted-foreground"
                    >
                        Technologies: {{ job.technologies.join(', ') }}
                    </p>
                </div>
            </div>
        </section>

        <section
            v-if="projects.length"
            class="border-b border-border py-8"
            aria-labelledby="r-projects"
        >
            <h2 id="r-projects" class="eyebrow mb-6 !text-brand">
                Selected projects
            </h2>
            <div class="grid gap-6">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="break-inside-avoid"
                >
                    <h3 class="font-semibold">
                        <a
                            :href="`/projects/${project.slug}`"
                            class="hover:text-brand"
                            >{{ project.title }}</a
                        >
                    </h3>
                    <p
                        v-if="project.client"
                        class="text-sm text-muted-foreground"
                    >
                        {{ project.client }}
                    </p>
                    <p class="mt-1.5 text-[15px] text-foreground/85">
                        {{ project.summary }}
                    </p>
                    <p
                        v-if="project.technologies.length"
                        class="mt-1 text-sm text-muted-foreground"
                    >
                        Technologies: {{ project.technologies.join(', ') }}
                    </p>
                </div>
            </div>
        </section>

        <section
            v-if="education.length"
            class="border-b border-border py-8"
            aria-labelledby="r-education"
        >
            <h2 id="r-education" class="eyebrow mb-6 !text-brand">Education</h2>
            <div class="grid gap-5">
                <div
                    v-for="item in education"
                    :key="item.id"
                    class="flex flex-col justify-between gap-1 sm:flex-row sm:items-baseline"
                >
                    <div>
                        <h3 class="font-semibold">
                            {{ item.degree
                            }}<template v-if="item.subject">
                                in {{ item.subject }}</template
                            >
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            {{
                                [item.institution, item.grade]
                                    .filter(Boolean)
                                    .join(' · ')
                            }}
                        </p>
                    </div>
                    <p class="font-mono text-sm text-muted-foreground">
                        {{ item.graduation_year }}
                    </p>
                </div>
            </div>
        </section>

        <section
            v-if="certifications.length"
            class="border-b border-border py-8"
            aria-labelledby="r-certs"
        >
            <h2 id="r-certs" class="eyebrow mb-6 !text-brand">
                Certifications &amp; training
            </h2>
            <div class="grid gap-4">
                <div
                    v-for="cert in certifications"
                    :key="cert.id"
                    class="flex flex-col justify-between gap-1 sm:flex-row sm:items-baseline"
                >
                    <div>
                        <h3 class="font-semibold">{{ cert.name }}</h3>
                        <p class="text-sm text-muted-foreground">
                            {{
                                [cert.issuer, cert.description]
                                    .filter(Boolean)
                                    .join(' · ')
                            }}
                        </p>
                    </div>
                    <p class="font-mono text-sm text-muted-foreground">
                        {{ cert.issued }}
                    </p>
                </div>
            </div>
        </section>

        <section
            v-if="profile.languages.length"
            class="py-8"
            aria-labelledby="r-languages"
        >
            <h2 id="r-languages" class="eyebrow mb-3 !text-brand">Languages</h2>
            <p class="text-[15px] text-foreground/85">
                {{ profile.languages.join(' · ') }}
            </p>
        </section>
    </article>
</template>
