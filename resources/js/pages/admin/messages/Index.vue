<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PaginationBar from '@/components/admin/PaginationBar.vue';
import type { Paginated } from '@/lib/api';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Messages', href: '/admin/messages' }] },
});

const props = defineProps<{
    items: Paginated<{
        id: number;
        name: string;
        email: string;
        subject: string;
        preview: string;
        is_read: boolean;
        created_at: string;
    }>;
    query: { search: string; status: string };
    unread: number;
}>();

const search = ref(props.query.search);

function visit(params: Record<string, string>): void {
    const merged = Object.fromEntries(
        Object.entries({ ...props.query, ...params }).filter(([, v]) => v),
    );
    router.get('/admin/messages', merged, {
        preserveState: true,
        replace: true,
    });
}

let timer: ReturnType<typeof setTimeout> | undefined;
watch(search, (value) => {
    clearTimeout(timer);
    timer = setTimeout(() => visit({ search: value }), 300);
});
onBeforeUnmount(() => clearTimeout(timer));

function date(value: string): string {
    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

const tabs = [
    { value: '', label: 'All' },
    { value: 'unread', label: 'Unread' },
    { value: 'read', label: 'Read' },
];
</script>

<template>
    <Head title="Messages · Admin" />

    <div class="flex flex-col gap-6 p-4 md:p-6">
        <AdminPageHeader
            title="Messages"
            :description="`Messages sent through the contact form. ${unread} unread.`"
        />

        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div
                class="inline-flex rounded-md border border-border p-0.5"
                role="group"
                aria-label="Filter messages"
            >
                <button
                    v-for="tab in tabs"
                    :key="tab.value"
                    type="button"
                    class="rounded px-3 py-1.5 text-sm font-medium"
                    :class="
                        query.status === tab.value
                            ? 'bg-muted text-foreground'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    :aria-pressed="query.status === tab.value"
                    @click="visit({ status: tab.value })"
                >
                    {{ tab.label }}
                </button>
            </div>
            <div class="relative sm:w-80">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    aria-hidden="true"
                />
                <input
                    v-model="search"
                    type="search"
                    aria-label="Search messages"
                    placeholder="Search name, email or subject"
                    class="h-9 w-full rounded-md border border-input bg-transparent pr-3 pl-9 text-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                />
            </div>
        </div>

        <EmptyState
            v-if="!items.data.length"
            :title="
                query.search || query.status
                    ? 'No messages match.'
                    : 'No messages yet.'
            "
            description="Messages from the contact form will appear here."
        />

        <ul
            v-else
            class="divide-y divide-border overflow-hidden rounded-lg border border-border"
            data-test="messages-list"
        >
            <li v-for="message in items.data" :key="message.id">
                <Link
                    :href="`/admin/messages/${message.id}`"
                    class="flex gap-4 px-4 py-3.5 transition-colors hover:bg-muted/40"
                    :class="{ 'bg-primary/[0.03]': !message.is_read }"
                >
                    <span
                        class="mt-1.5 size-2 shrink-0 rounded-full"
                        :class="
                            message.is_read ? 'bg-transparent' : 'bg-primary'
                        "
                        :aria-label="message.is_read ? undefined : 'Unread'"
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline justify-between gap-4">
                            <p
                                class="truncate text-sm"
                                :class="message.is_read ? '' : 'font-semibold'"
                            >
                                {{ message.name }}
                                <span class="font-normal text-muted-foreground"
                                    >&lt;{{ message.email }}&gt;</span
                                >
                            </p>
                            <time
                                :datetime="message.created_at"
                                class="shrink-0 text-xs text-muted-foreground"
                                >{{ date(message.created_at) }}</time
                            >
                        </div>
                        <p
                            class="truncate text-sm"
                            :class="
                                message.is_read
                                    ? 'text-foreground/80'
                                    : 'font-medium'
                            "
                        >
                            {{ message.subject }}
                        </p>
                        <p class="truncate text-sm text-muted-foreground">
                            {{ message.preview }}
                        </p>
                    </div>
                </Link>
            </li>
        </ul>

        <PaginationBar :meta="items" />
    </div>
</template>
