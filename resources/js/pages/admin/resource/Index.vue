<script setup lang="ts">
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import {
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    ExternalLink,
    LoaderCircle,
    Pencil,
    Plus,
    Search,
    Trash2,
} from '@lucide/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { resources } from '@/admin/resources';
import type { Column, Option } from '@/admin/types';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PaginationBar from '@/components/admin/PaginationBar.vue';
import StatusBadge from '@/components/admin/StatusBadge.vue';
import ToggleSwitch from '@/components/admin/ToggleSwitch.vue';
import { Button } from '@/components/ui/button';
import { confirm } from '@/composables/useConfirm';
import type { Paginated } from '@/lib/api';

type Row = Record<string, unknown> & { id: number };

const props = defineProps<{
    resource: string;
    items: Paginated<Row>;
    query: Record<string, string>;
    options: Record<string, Option[]>;
    abilities: { create: boolean; delete: boolean };
}>();

const config = computed(() => resources[props.resource]);
const base = computed(() => `/admin/${props.resource}`);
const titleColumn = computed(() =>
    config.value.columns.find((c) => c.type === 'title'),
);

setLayoutProps({
    breadcrumbs: [{ title: config.value.title, href: base.value }],
});

const search = ref(props.query.search ?? '');
const loading = ref(false);
const busyRow = ref<number | null>(null);

function visit(params: Record<string, string | undefined>): void {
    const merged = { ...props.query, ...params };
    const clean = Object.fromEntries(
        Object.entries(merged).filter(([, v]) => v !== undefined && v !== ''),
    );

    router.get(base.value, clean, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onStart: () => (loading.value = true),
        onFinish: () => (loading.value = false),
    });
}

let timer: ReturnType<typeof setTimeout> | undefined;
watch(search, (value) => {
    clearTimeout(timer);
    timer = setTimeout(() => visit({ search: value, page: undefined }), 300);
});
onBeforeUnmount(() => clearTimeout(timer));

function sortBy(column: Column): void {
    if (!column.sortable) {
        return;
    }

    const direction =
        props.query.sort === column.key && props.query.direction === 'asc'
            ? 'desc'
            : 'asc';
    visit({ sort: column.key, direction, page: undefined });
}

function ariaSort(
    column: Column,
): 'ascending' | 'descending' | 'none' | undefined {
    if (!column.sortable) {
        return undefined;
    }

    if (props.query.sort !== column.key) {
        return 'none';
    }

    return props.query.direction === 'desc' ? 'descending' : 'ascending';
}

function toggle(row: Row, field: string): void {
    busyRow.value = row.id;
    router.patch(
        `${base.value}/${row.id}/toggle`,
        { field },
        {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => (busyRow.value = null),
        },
    );
}

async function destroy(row: Row): Promise<void> {
    const name = String(row[titleColumn.value?.key ?? 'id'] ?? `#${row.id}`);
    const ok = await confirm({
        title: `Delete this ${config.value.singular}?`,
        description: `“${name}” will be permanently removed. This cannot be undone.`,
        confirmLabel: 'Delete',
        destructive: true,
    });

    if (!ok) {
        return;
    }

    busyRow.value = row.id;
    router.delete(`${base.value}/${row.id}`, {
        preserveScroll: true,
        onFinish: () => (busyRow.value = null),
    });
}

function formatDate(value: unknown): string {
    if (typeof value !== 'string' || !value) {
        return '—';
    }

    return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(
        new Date(value),
    );
}

const hasFilters = computed(
    () =>
        Boolean(props.query.search) ||
        (config.value.filters ?? []).some((f) => props.query[f.key]),
);
</script>

<template>
    <Head :title="`${config.title} · Admin`" />

    <div class="flex flex-col gap-6 p-4 md:p-6">
        <AdminPageHeader
            :title="config.title"
            :description="config.description"
        >
            <template #actions>
                <Button
                    v-if="abilities.create"
                    as-child
                    data-test="resource-create"
                >
                    <Link :href="`${base}/create`"
                        ><Plus /> New {{ config.singular }}</Link
                    >
                </Button>
            </template>
        </AdminPageHeader>

        <div
            v-if="config.searchable !== false || config.filters?.length"
            class="flex flex-col gap-3 sm:flex-row sm:items-center"
        >
            <div v-if="config.searchable !== false" class="relative sm:w-80">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    aria-hidden="true"
                />
                <input
                    v-model="search"
                    type="search"
                    :aria-label="`Search ${config.title.toLowerCase()}`"
                    placeholder="Search…"
                    class="h-9 w-full rounded-md border border-input bg-transparent pr-3 pl-9 text-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                    data-test="resource-search"
                />
            </div>
            <select
                v-for="filter in config.filters ?? []"
                :key="filter.key"
                :value="query[filter.key] ?? ''"
                :aria-label="`Filter by ${filter.label.toLowerCase()}`"
                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none dark:bg-input/30"
                @change="
                    visit({
                        [filter.key]: ($event.target as HTMLSelectElement)
                            .value,
                        page: undefined,
                    })
                "
            >
                <option value="">All {{ filter.label.toLowerCase() }}</option>
                <option
                    v-for="option in typeof filter.options === 'string'
                        ? (options[filter.options] ?? [])
                        : filter.options"
                    :key="option.value"
                    :value="String(option.value)"
                >
                    {{ option.label }}
                </option>
            </select>
            <LoaderCircle
                v-if="loading"
                class="size-4 animate-spin text-muted-foreground"
                aria-label="Loading"
            />
        </div>

        <EmptyState
            v-if="!items.data.length"
            :title="
                hasFilters
                    ? 'No results match your search.'
                    : `No ${config.title.toLowerCase()} yet.`
            "
            :description="
                hasFilters
                    ? 'Try a different search term or clear the filters.'
                    : abilities.create
                      ? `Create your first ${config.singular} to get started.`
                      : undefined
            "
        >
            <Button
                v-if="hasFilters"
                variant="outline"
                size="sm"
                @click="
                    search = '';
                    visit(
                        Object.fromEntries(
                            (config.filters ?? []).map((f) => [
                                f.key,
                                undefined,
                            ]),
                        ),
                    );
                "
                >Clear filters</Button
            >
            <Button v-else-if="abilities.create" size="sm" as-child>
                <Link :href="`${base}/create`"
                    ><Plus /> New {{ config.singular }}</Link
                >
            </Button>
        </EmptyState>

        <div
            v-else
            class="overflow-x-auto rounded-lg border border-border"
            :class="{ 'opacity-60 transition-opacity': loading }"
        >
            <table
                class="w-full min-w-[640px] text-sm"
                data-test="resource-table"
            >
                <thead
                    class="border-b border-border bg-muted/40 text-left text-xs text-muted-foreground"
                >
                    <tr>
                        <th
                            v-for="column in config.columns"
                            :key="column.key"
                            scope="col"
                            class="px-4 py-2.5 font-medium"
                            :class="column.class"
                            :aria-sort="ariaSort(column)"
                        >
                            <button
                                v-if="column.sortable"
                                type="button"
                                class="inline-flex items-center gap-1 hover:text-foreground"
                                @click="sortBy(column)"
                            >
                                {{ column.label }}
                                <ArrowUp
                                    v-if="
                                        query.sort === column.key &&
                                        query.direction === 'asc'
                                    "
                                    class="size-3"
                                />
                                <ArrowDown
                                    v-else-if="query.sort === column.key"
                                    class="size-3"
                                />
                                <ArrowUpDown v-else class="size-3 opacity-40" />
                            </button>
                            <span
                                v-else
                                :class="{ 'sr-only': !column.label }"
                                >{{ column.label || 'Image' }}</span
                            >
                        </th>
                        <th
                            scope="col"
                            class="px-4 py-2.5 text-right font-medium"
                        >
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr
                        v-for="row in items.data"
                        :key="row.id"
                        class="transition-colors hover:bg-muted/30"
                        :class="{ 'opacity-50': busyRow === row.id }"
                    >
                        <td
                            v-for="column in config.columns"
                            :key="column.key"
                            class="px-4 py-3 align-middle"
                            :class="column.class"
                        >
                            <template v-if="column.type === 'title'">
                                <Link
                                    :href="`${base}/${row.id}/edit`"
                                    class="font-medium hover:text-primary"
                                    >{{ row[column.key] }}</Link
                                >
                                <p
                                    v-if="column.sub && row[column.sub]"
                                    class="mt-0.5 max-w-md truncate text-xs text-muted-foreground"
                                >
                                    {{ row[column.sub] }}
                                </p>
                            </template>
                            <ToggleSwitch
                                v-else-if="column.type === 'toggle'"
                                :model-value="Boolean(row[column.key])"
                                :label="`${column.label}: ${row[titleColumn?.key ?? 'id']}`"
                                :disabled="busyRow === row.id"
                                @update:model-value="toggle(row, column.key)"
                            />
                            <StatusBadge
                                v-else-if="column.type === 'status'"
                                :value="String(row[column.key])"
                            />
                            <span
                                v-else-if="column.type === 'badge'"
                                class="inline-flex rounded-md bg-muted px-2 py-0.5 text-xs font-medium capitalize"
                                >{{ row[column.key] }}</span
                            >
                            <span
                                v-else-if="column.type === 'date'"
                                class="whitespace-nowrap text-muted-foreground"
                                >{{ formatDate(row[column.key]) }}</span
                            >
                            <span
                                v-else-if="column.type === 'count'"
                                class="text-muted-foreground tabular-nums"
                                >{{ row[column.key] ?? 0 }}</span
                            >
                            <template v-else-if="column.type === 'image'">
                                <img
                                    v-if="row[column.key]"
                                    :src="String(row[column.key])"
                                    alt=""
                                    class="size-10 rounded object-cover"
                                />
                                <span
                                    v-else
                                    class="block size-10 rounded bg-muted"
                                />
                            </template>
                            <span v-else class="text-foreground/80">{{
                                row[column.key] ?? '—'
                            }}</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1">
                                <a
                                    v-if="config.publicUrl?.(row)"
                                    :href="config.publicUrl(row)!"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex size-8 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground"
                                    aria-label="View on site"
                                    title="View on site"
                                >
                                    <ExternalLink class="size-4" />
                                </a>
                                <Link
                                    :href="`${base}/${row.id}/edit`"
                                    class="inline-flex size-8 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground"
                                    aria-label="Edit"
                                    title="Edit"
                                >
                                    <Pencil class="size-4" />
                                </Link>
                                <button
                                    v-if="abilities.delete"
                                    type="button"
                                    class="inline-flex size-8 items-center justify-center rounded-md text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    aria-label="Delete"
                                    title="Delete"
                                    data-test="resource-delete"
                                    @click="destroy(row)"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <PaginationBar :meta="items" />
    </div>
</template>
