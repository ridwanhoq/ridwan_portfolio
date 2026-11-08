<script setup lang="ts">
import { Head, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import {
    Copy,
    ImagePlus,
    LoaderCircle,
    Search,
    Trash2,
    Upload,
} from '@lucide/vue';
import { onBeforeUnmount, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import EmptyState from '@/components/admin/EmptyState.vue';
import PaginationBar from '@/components/admin/PaginationBar.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { confirm } from '@/composables/useConfirm';
import { api, ApiError } from '@/lib/api';
import type { Paginated } from '@/lib/api';
import type { MediaItem } from '@/types';

type MediaRow = MediaItem & {
    original_name: string;
    mime_type: string;
    size: number;
    caption: string | null;
    created_at: string;
};

const props = defineProps<{
    items: Paginated<MediaRow>;
    query: { search: string };
    limits: { max_kb: number; types: string[] };
}>();

setLayoutProps({
    breadcrumbs: [{ title: 'Media library', href: '/admin/media' }],
});

const search = ref(props.query.search);
const uploading = ref(0);
const dragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const editing = ref<MediaRow | null>(null);
const editForm = useForm({ alt: '', caption: '' });

let timer: ReturnType<typeof setTimeout> | undefined;
watch(search, (value) => {
    clearTimeout(timer);
    timer = setTimeout(
        () =>
            router.get('/admin/media', value ? { search: value } : {}, {
                preserveState: true,
                replace: true,
            }),
        300,
    );
});
onBeforeUnmount(() => clearTimeout(timer));

async function uploadFiles(files: FileList | File[]): Promise<void> {
    const list = Array.from(files);

    if (!list.length) return;

    uploading.value = list.length;
    let ok = 0;

    for (const file of list) {
        if (file.size > props.limits.max_kb * 1024) {
            toast.error(
                `${file.name} is larger than ${Math.round(props.limits.max_kb / 1024)} MB.`,
            );
        } else {
            try {
                const data = new FormData();
                data.append('file', file);
                await api.post('/admin/media', data);
                ok++;
            } catch (error) {
                toast.error(
                    `${file.name}: ${error instanceof ApiError ? error.message : 'upload failed.'}`,
                );
            }
        }

        uploading.value--;
    }

    if (ok) {
        toast.success(ok === 1 ? 'Image uploaded.' : `${ok} images uploaded.`);
        router.reload({ only: ['items'] });
    }

    if (fileInput.value) fileInput.value.value = '';
}

function onDrop(event: DragEvent): void {
    dragging.value = false;

    if (event.dataTransfer?.files) void uploadFiles(event.dataTransfer.files);
}

function edit(item: MediaRow): void {
    editing.value = item;
    editForm.alt = item.alt ?? '';
    editForm.caption = item.caption ?? '';
    editForm.clearErrors();
}

function save(): void {
    if (!editing.value) return;

    editForm.put(`/admin/media/${editing.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (editing.value = null),
    });
}

async function destroy(item: MediaRow): Promise<void> {
    const ok = await confirm({
        title: 'Delete this image?',
        description:
            'It will be removed from any project, profile or page that uses it. This cannot be undone.',
        confirmLabel: 'Delete image',
        destructive: true,
    });

    if (ok) {
        router.delete(`/admin/media/${item.id}`, {
            preserveScroll: true,
            onSuccess: () => (editing.value = null),
        });
    }
}

async function copyUrl(item: MediaRow): Promise<void> {
    try {
        await navigator.clipboard.writeText(
            new URL(item.url, window.location.origin).toString(),
        );
        toast.success('Image URL copied.');
    } catch {
        toast.warning(
            'Could not copy automatically. Select the URL and copy it manually.',
        );
    }
}

function size(bytes: number): string {
    return bytes > 1024 * 1024
        ? `${(bytes / 1024 / 1024).toFixed(1)} MB`
        : `${Math.round(bytes / 1024)} KB`;
}
</script>

<template>
    <Head title="Media library · Admin" />

    <div class="flex flex-col gap-6 p-4 md:p-6">
        <AdminPageHeader
            title="Media library"
            description="Images used across the site. Uploads are re-encoded and converted to WebP automatically."
        >
            <template #actions>
                <input
                    ref="fileInput"
                    type="file"
                    multiple
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    class="sr-only"
                    data-test="media-file"
                    @change="
                        uploadFiles(
                            ($event.target as HTMLInputElement).files ?? [],
                        )
                    "
                />
                <Button
                    :disabled="uploading > 0"
                    data-test="media-upload"
                    @click="fileInput?.click()"
                >
                    <LoaderCircle
                        v-if="uploading"
                        class="animate-spin"
                    /><Upload v-else />
                    {{
                        uploading ? `Uploading ${uploading}…` : 'Upload images'
                    }}
                </Button>
            </template>
        </AdminPageHeader>

        <div
            class="rounded-lg border-2 border-dashed px-6 py-8 text-center text-sm transition-colors"
            :class="
                dragging
                    ? 'border-primary bg-primary/5'
                    : 'border-border text-muted-foreground'
            "
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="onDrop"
        >
            <ImagePlus class="mx-auto mb-2 size-6" aria-hidden="true" />
            Drag and drop images here, or use the upload button. JPG, PNG, WebP
            or GIF up to {{ Math.round(limits.max_kb / 1024) }} MB. SVG is not
            accepted.
        </div>

        <div class="relative sm:w-80">
            <Search
                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                aria-hidden="true"
            />
            <input
                v-model="search"
                type="search"
                aria-label="Search media"
                placeholder="Search by file name or alt text"
                class="h-9 w-full rounded-md border border-input bg-transparent pr-3 pl-9 text-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            />
        </div>

        <EmptyState
            v-if="!items.data.length"
            :title="
                query.search
                    ? 'No images match your search.'
                    : 'No images uploaded yet.'
            "
            description="Uploaded images can be used for project thumbnails, galleries, your profile photo and social previews."
        />

        <ul
            v-else
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
            data-test="media-grid"
        >
            <li
                v-for="item in items.data"
                :key="item.id"
                class="group overflow-hidden rounded-lg border border-border bg-card"
            >
                <button
                    type="button"
                    class="block w-full bg-muted"
                    :aria-label="`Edit details for ${item.original_name}`"
                    @click="edit(item)"
                >
                    <img
                        :src="item.thumb_url"
                        :alt="item.alt ?? ''"
                        loading="lazy"
                        class="aspect-square w-full object-cover"
                    />
                </button>
                <div class="p-2.5">
                    <p
                        class="truncate text-xs font-medium"
                        :title="item.original_name"
                    >
                        {{ item.original_name }}
                    </p>
                    <p class="text-[11px] text-muted-foreground">
                        {{ item.width }}×{{ item.height }} ·
                        {{ size(item.size) }}
                    </p>
                    <p v-if="!item.alt" class="mt-1 text-[11px] text-warning">
                        Missing alt text
                    </p>
                </div>
            </li>
        </ul>

        <PaginationBar :meta="items" />
    </div>

    <Dialog
        :open="editing !== null"
        @update:open="
            (open: boolean) => {
                if (!open) editing = null;
            }
        "
    >
        <DialogContent v-if="editing" class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>Image details</DialogTitle>
                <DialogDescription
                    >{{ editing.original_name }} · {{ editing.width }}×{{
                        editing.height
                    }}
                    · {{ size(editing.size) }}</DialogDescription
                >
            </DialogHeader>
            <form
                class="grid gap-5 sm:grid-cols-[200px_1fr]"
                @submit.prevent="save"
            >
                <img
                    :src="editing.thumb_url"
                    :alt="editing.alt ?? ''"
                    class="w-full rounded-md border border-border object-cover"
                />
                <div class="grid gap-4">
                    <div class="grid gap-1.5">
                        <label for="media-alt" class="text-sm font-medium"
                            >Alt text</label
                        >
                        <input
                            id="media-alt"
                            v-model="editForm.alt"
                            maxlength="255"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                        />
                        <p class="text-xs text-muted-foreground">
                            Describe the image for screen reader users and
                            search engines.
                        </p>
                        <p
                            v-if="editForm.errors.alt"
                            class="text-sm text-destructive"
                        >
                            {{ editForm.errors.alt }}
                        </p>
                    </div>
                    <div class="grid gap-1.5">
                        <label for="media-caption" class="text-sm font-medium"
                            >Caption</label
                        >
                        <input
                            id="media-caption"
                            v-model="editForm.caption"
                            maxlength="255"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                        />
                    </div>
                    <button
                        type="button"
                        class="inline-flex w-fit items-center gap-1.5 text-xs font-medium text-muted-foreground hover:text-foreground"
                        @click="copyUrl(editing)"
                    >
                        <Copy class="size-3.5" /> Copy image URL
                    </button>
                </div>
                <DialogFooter class="gap-2 sm:col-span-2 sm:justify-between">
                    <Button
                        type="button"
                        variant="ghost"
                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                        @click="destroy(editing)"
                        ><Trash2 /> Delete</Button
                    >
                    <Button type="submit" :disabled="editForm.processing"
                        ><LoaderCircle
                            v-if="editForm.processing"
                            class="animate-spin"
                        />
                        Save details</Button
                    >
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
