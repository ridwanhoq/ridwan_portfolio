<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Briefcase,
    CircleAlert,
    CircleCheck,
    FolderKanban,
    Images,
    Inbox,
    Mail,
    Star,
} from '@lucide/vue';
import { computed } from 'vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import { Button } from '@/components/ui/button';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Dashboard', href: '/admin' }] },
});

const props = defineProps<{
    stats: Record<
        | 'projects'
        | 'published_projects'
        | 'featured_projects'
        | 'experiences'
        | 'skills'
        | 'media'
        | 'posts'
        | 'messages'
        | 'unread_messages',
        number
    >;
    recentMessages: {
        id: number;
        name: string;
        subject: string;
        is_read: boolean;
        created_at: string;
    }[];
    recentProjects: {
        id: number;
        title: string;
        status: string;
        is_featured: boolean;
        updated_at: string;
    }[];
    checks: {
        resume: string;
        notifications: boolean;
        blog: boolean;
        mail_driver: string;
        placeholder_count: number;
    };
    setup: { label: string; done: boolean; href: string }[];
}>();

const cards = computed(() => [
    {
        label: 'Total projects',
        value: props.stats.projects,
        icon: FolderKanban,
        href: '/admin/projects',
    },
    {
        label: 'Published projects',
        value: props.stats.published_projects,
        icon: CircleCheck,
        href: '/admin/projects?status=published',
    },
    {
        label: 'Featured projects',
        value: props.stats.featured_projects,
        icon: Star,
        href: '/admin/projects',
    },
    {
        label: 'Experience records',
        value: props.stats.experiences,
        icon: Briefcase,
        href: '/admin/experiences',
    },
    {
        label: 'Contact messages',
        value: props.stats.messages,
        icon: Mail,
        href: '/admin/messages',
    },
    {
        label: 'Unread messages',
        value: props.stats.unread_messages,
        icon: Inbox,
        href: '/admin/messages?status=unread',
    },
]);

const pending = computed(() => props.setup.filter((s) => !s.done));

function relative(value: string): string {
    const diff = (Date.now() - new Date(value).getTime()) / 1000;
    const rtf = new Intl.RelativeTimeFormat(undefined, { numeric: 'auto' });

    if (diff < 3600)
        return rtf.format(-Math.max(1, Math.round(diff / 60)), 'minute');
    if (diff < 86400) return rtf.format(-Math.round(diff / 3600), 'hour');

    return rtf.format(-Math.round(diff / 86400), 'day');
}
</script>

<template>
    <Head title="Dashboard · Admin" />

    <div class="flex flex-col gap-6 p-4 md:p-6">
        <AdminPageHeader
            title="Dashboard"
            description="An overview of your portfolio content and incoming messages."
        >
            <template #actions>
                <Button variant="outline" as-child
                    ><a href="/" target="_blank" rel="noopener"
                        >View site</a
                    ></Button
                >
                <Button as-child
                    ><Link href="/admin/projects/create"
                        >New project</Link
                    ></Button
                >
            </template>
        </AdminPageHeader>

        <ul
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
            aria-label="Statistics"
        >
            <li v-for="card in cards" :key="card.label">
                <Link
                    :href="card.href"
                    class="flex items-center justify-between rounded-lg border border-border bg-card p-5 transition-colors hover:border-foreground/20"
                >
                    <div>
                        <p class="text-sm text-muted-foreground">
                            {{ card.label }}
                        </p>
                        <p
                            class="mt-1 text-3xl font-semibold tracking-tight tabular-nums"
                            data-test="stat-value"
                        >
                            {{ card.value }}
                        </p>
                    </div>
                    <component
                        :is="card.icon"
                        class="size-5 text-muted-foreground"
                        aria-hidden="true"
                    />
                </Link>
            </li>
        </ul>

        <section
            v-if="pending.length"
            class="rounded-lg border border-warning/40 bg-warning/5 p-5"
            aria-labelledby="setup-heading"
        >
            <h2
                id="setup-heading"
                class="flex items-center gap-2 font-semibold"
            >
                <CircleAlert class="size-4 text-warning" aria-hidden="true" />
                Finish setting up
            </h2>
            <p class="mt-1 text-sm text-muted-foreground">
                These items use placeholders or are empty, so they are hidden
                from the public site until completed.
            </p>
            <ul class="mt-4 grid gap-2 text-sm">
                <li v-for="item in pending" :key="item.label">
                    <Link
                        :href="item.href"
                        class="inline-flex items-center gap-1.5 font-medium hover:text-primary"
                        >{{ item.label }}
                        <ArrowRight class="size-3.5" aria-hidden="true"
                    /></Link>
                </li>
            </ul>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section
                class="rounded-lg border border-border bg-card"
                aria-labelledby="messages-heading"
            >
                <header
                    class="flex items-center justify-between border-b border-border px-5 py-4"
                >
                    <h2 id="messages-heading" class="font-semibold">
                        Recent messages
                    </h2>
                    <Link
                        href="/admin/messages"
                        class="text-sm font-medium text-primary hover:underline"
                        >View all</Link
                    >
                </header>
                <ul v-if="recentMessages.length" class="divide-y divide-border">
                    <li v-for="message in recentMessages" :key="message.id">
                        <Link
                            :href="`/admin/messages/${message.id}`"
                            class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-muted/40"
                        >
                            <div class="min-w-0">
                                <p
                                    class="truncate text-sm"
                                    :class="
                                        message.is_read ? '' : 'font-semibold'
                                    "
                                >
                                    {{ message.subject }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ message.name }} ·
                                    {{ relative(message.created_at) }}
                                </p>
                            </div>
                            <StatusBadge
                                v-if="!message.is_read"
                                value="unread"
                            />
                        </Link>
                    </li>
                </ul>
                <p
                    v-else
                    class="px-5 py-10 text-center text-sm text-muted-foreground"
                >
                    No messages yet.
                </p>
            </section>

            <section
                class="rounded-lg border border-border bg-card"
                aria-labelledby="projects-heading"
            >
                <header
                    class="flex items-center justify-between border-b border-border px-5 py-4"
                >
                    <h2 id="projects-heading" class="font-semibold">
                        Recently updated projects
                    </h2>
                    <Link
                        href="/admin/projects"
                        class="text-sm font-medium text-primary hover:underline"
                        >View all</Link
                    >
                </header>
                <ul v-if="recentProjects.length" class="divide-y divide-border">
                    <li v-for="project in recentProjects" :key="project.id">
                        <Link
                            :href="`/admin/projects/${project.id}/edit`"
                            class="flex items-center justify-between gap-4 px-5 py-3 hover:bg-muted/40"
                        >
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">
                                    {{ project.title }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Updated {{ relative(project.updated_at) }}
                                </p>
                            </div>
                            <StatusBadge :value="project.status" />
                        </Link>
                    </li>
                </ul>
                <p
                    v-else
                    class="px-5 py-10 text-center text-sm text-muted-foreground"
                >
                    No projects yet.
                </p>
            </section>
        </div>

        <section
            class="rounded-lg border border-border bg-card p-5"
            aria-labelledby="config-heading"
        >
            <h2 id="config-heading" class="font-semibold">Configuration</h2>
            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-muted-foreground">Résumé download</dt>
                    <dd class="mt-0.5 font-medium">{{ checks.resume }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Message notifications</dt>
                    <dd class="mt-0.5 font-medium">
                        {{
                            checks.notifications
                                ? `On (${checks.mail_driver})`
                                : 'Off'
                        }}
                    </dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Blog</dt>
                    <dd class="mt-0.5 font-medium">
                        {{ checks.blog ? 'Enabled' : 'Disabled' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Media files</dt>
                    <dd class="mt-0.5 font-medium">{{ stats.media }}</dd>
                </div>
            </dl>
            <p
                v-if="checks.notifications && checks.mail_driver === 'log'"
                class="mt-4 text-sm text-warning"
            >
                Notifications are on but MAIL_MAILER is “log”, so emails are
                written to the log instead of being sent.
            </p>
        </section>
    </div>
</template>
