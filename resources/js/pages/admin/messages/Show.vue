<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { ArrowLeft, Mail, MailOpen, Reply, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { Button } from '@/components/ui/button';
import { confirm } from '@/composables/useConfirm';

const props = defineProps<{
    message: {
        id: number;
        name: string;
        email: string;
        subject: string;
        message: string;
        ip_address: string | null;
        user_agent: string | null;
        created_at: string;
        read_at: string | null;
    };
}>();

setLayoutProps({
    breadcrumbs: [
        { title: 'Messages', href: '/admin/messages' },
        {
            title: props.message.subject,
            href: `/admin/messages/${props.message.id}`,
        },
    ],
});

const replyHref = computed(
    () =>
        `mailto:${props.message.email}?subject=${encodeURIComponent(`Re: ${props.message.subject}`)}`,
);
const date = computed(() =>
    new Intl.DateTimeFormat(undefined, {
        dateStyle: 'full',
        timeStyle: 'short',
    }).format(new Date(props.message.created_at)),
);

function toggleRead(): void {
    router.patch(
        `/admin/messages/${props.message.id}/read`,
        {},
        { preserveScroll: true },
    );
}

async function destroy(): Promise<void> {
    const ok = await confirm({
        title: 'Delete this message?',
        description: 'This cannot be undone.',
        confirmLabel: 'Delete',
        destructive: true,
    });

    if (ok) router.delete(`/admin/messages/${props.message.id}`);
}
</script>

<template>
    <Head :title="`${message.subject} · Messages`" />

    <div class="flex max-w-4xl flex-col gap-6 p-4 md:p-6">
        <AdminPageHeader
            :title="message.subject"
            :description="`From ${message.name} · ${date}`"
        >
            <template #actions>
                <Button variant="ghost" as-child
                    ><Link href="/admin/messages"
                        ><ArrowLeft /> Back</Link
                    ></Button
                >
                <Button variant="outline" @click="toggleRead">
                    <Mail v-if="message.read_at" /><MailOpen v-else />
                    {{ message.read_at ? 'Mark unread' : 'Mark read' }}
                </Button>
                <Button
                    variant="outline"
                    class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                    @click="destroy"
                    ><Trash2 /> Delete</Button
                >
                <Button as-child
                    ><a :href="replyHref"><Reply /> Reply by email</a></Button
                >
            </template>
        </AdminPageHeader>

        <dl
            class="grid gap-4 rounded-lg border border-border bg-card p-5 text-sm sm:grid-cols-2"
        >
            <div>
                <dt class="text-muted-foreground">Name</dt>
                <dd class="mt-0.5 font-medium">{{ message.name }}</dd>
            </div>
            <div>
                <dt class="text-muted-foreground">Email</dt>
                <dd class="mt-0.5 font-medium">
                    <a
                        :href="`mailto:${message.email}`"
                        class="hover:text-primary"
                        >{{ message.email }}</a
                    >
                </dd>
            </div>
        </dl>

        <article class="rounded-lg border border-border bg-card p-6">
            <h2 class="sr-only">Message</h2>
            <p class="text-[15px] leading-relaxed whitespace-pre-line">
                {{ message.message }}
            </p>
        </article>

        <details class="text-sm text-muted-foreground">
            <summary class="cursor-pointer">Technical details</summary>
            <dl class="mt-3 grid gap-2">
                <div>
                    <dt class="inline font-medium">IP address:</dt>
                    <dd class="inline font-mono">
                        {{ message.ip_address ?? '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="inline font-medium">User agent:</dt>
                    <dd class="inline font-mono break-all">
                        {{ message.user_agent ?? '—' }}
                    </dd>
                </div>
            </dl>
        </details>
    </div>
</template>
