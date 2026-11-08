<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import ContactDetails from '@/components/site/ContactDetails.vue';
import ContactForm from '@/components/site/ContactForm.vue';
import EducationList from '@/components/site/EducationList.vue';
import ExperienceTimeline from '@/components/site/ExperienceTimeline.vue';
import HeroSection from '@/components/site/home/HeroSection.vue';
import HomeSection from '@/components/site/home/HomeSection.vue';
import PrincipleGrid from '@/components/site/PrincipleGrid.vue';
import ProjectCard from '@/components/site/ProjectCard.vue';
import Prose from '@/components/site/Prose.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import SkillGroups from '@/components/site/SkillGroups.vue';
import type {
    CertificationItem,
    ContactConfig,
    EducationItem,
    ExperienceItem,
    PrincipleItem,
    Profile,
    ProjectCard as ProjectCardType,
    Section,
    Seo,
    SkillGroup,
} from '@/types';

const props = defineProps<{
    seo: Seo;
    profile: Profile;
    sections: Section[];
    experiences: ExperienceItem[];
    projects: ProjectCardType[];
    projectCount: number;
    skills: SkillGroup[];
    principles: PrincipleItem[];
    education: EducationItem[];
    certifications: CertificationItem[];
    contact: ContactConfig;
}>();

/** Only render sections that are enabled and have something to show. */
function hasContent(key: string): boolean {
    switch (key) {
        case 'about':
            return Object.keys(props.profile.about).length > 0;
        case 'experience':
            return props.experiences.length > 0;
        case 'projects':
            return props.projects.length > 0;
        case 'skills':
            return props.skills.length > 0;
        case 'approach':
            return props.principles.length > 0;
        case 'education':
            return props.education.length + props.certifications.length > 0;
        default:
            return true;
    }
}

const visibleSections = props.sections.filter((s) => hasContent(s.key));
</script>

<template>
    <SeoHead :seo="seo" />

    <HeroSection
        :profile="profile"
        :project-count="projectCount"
        :explore-href="
            visibleSections.some((s) => s.key === 'projects')
                ? '#projects'
                : '/projects'
        "
    />

    <template v-for="(section, i) in visibleSections" :key="section.key">
        <HomeSection
            v-if="section.key === 'about'"
            :section="section"
            :index="i + 1"
            split
        >
            <div class="grid gap-10">
                <Prose
                    :html="profile.about.intro"
                    class="text-lead !text-foreground/85"
                />
                <div class="grid gap-10 sm:grid-cols-2">
                    <div v-if="profile.about.philosophy" v-reveal>
                        <h3 class="eyebrow mb-3">Engineering philosophy</h3>
                        <Prose
                            :html="profile.about.philosophy"
                            class="!text-[15px]"
                        />
                    </div>
                    <div v-if="profile.about.collaboration" v-reveal>
                        <h3 class="eyebrow mb-3">Working together</h3>
                        <Prose
                            :html="profile.about.collaboration"
                            class="!text-[15px]"
                        />
                    </div>
                </div>
                <Link
                    href="/about"
                    class="inline-flex w-fit items-center gap-1.5 text-sm font-semibold text-foreground hover:text-brand"
                >
                    More about my background
                    <ArrowRight class="size-4" aria-hidden="true" />
                </Link>
            </div>
        </HomeSection>

        <HomeSection
            v-else-if="section.key === 'experience'"
            :section="section"
            :index="i + 1"
            muted
        >
            <ExperienceTimeline :items="experiences" compact />
        </HomeSection>

        <HomeSection
            v-else-if="section.key === 'projects'"
            :section="section"
            :index="i + 1"
        >
            <div class="grid gap-6 md:grid-cols-2">
                <div v-for="project in projects" :key="project.id" v-reveal>
                    <ProjectCard :project="project" />
                </div>
            </div>
            <div class="mt-12">
                <Link
                    href="/projects"
                    class="inline-flex items-center gap-1.5 rounded-md border border-border px-4 py-2.5 text-sm font-semibold transition-colors hover:border-foreground/30"
                >
                    View all projects
                    <ArrowRight class="size-4" aria-hidden="true" />
                </Link>
            </div>
        </HomeSection>

        <HomeSection
            v-else-if="section.key === 'skills'"
            :section="section"
            :index="i + 1"
            muted
        >
            <SkillGroups :groups="skills" />
        </HomeSection>

        <HomeSection
            v-else-if="section.key === 'approach'"
            :section="section"
            :index="i + 1"
            split
        >
            <PrincipleGrid :items="principles" />
        </HomeSection>

        <HomeSection
            v-else-if="section.key === 'education'"
            :section="section"
            :index="i + 1"
            muted
        >
            <EducationList
                :education="education"
                :certifications="certifications"
            />
        </HomeSection>

        <HomeSection
            v-else-if="section.key === 'contact'"
            :section="section"
            :index="i + 1"
            split
        >
            <template #aside>
                <p v-if="contact.intro" class="mt-4 text-muted-foreground">
                    {{ contact.intro }}
                </p>
                <div class="mt-10">
                    <ContactDetails :profile="profile" />
                </div>
            </template>
            <div class="rounded-lg border border-border bg-card p-6 sm:p-8">
                <ContactForm
                    v-if="$page.props.site?.contact_enabled"
                    :contact="contact"
                />
                <p v-else class="text-muted-foreground">
                    The contact form is currently unavailable. Please use the
                    details listed here.
                </p>
            </div>
        </HomeSection>
    </template>
</template>
