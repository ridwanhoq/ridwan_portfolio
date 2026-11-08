<script setup lang="ts">
import { ImagePlus, LoaderCircle, Search } from '@lucide/vue';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { api, ApiError } from '@/lib/api';
import type { Paginated } from '@/lib/api';
import type { MediaItem } from '@/types';

const open = defineModel<boolean>('open', { required: true });
const emit = defineEmits<{ select: [media: MediaItem] }>();

const items = ref<MediaItem[]>([]);
const page = ref(1);
const lastPage = ref(1);
const search = ref('');
const loading = ref(false);
const uploading = ref(false);
const failed = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

async function load(reset = false): Promise<void> {
    if (reset) {
        page.value = 1;
    }

    loading.value = true;
    failed.value = null;

    try {
        const params = new URLSearchParams({ page: String(page.value) });

        if (search.value.trim()) {
            params.set('search', search.value.trim());
        }

        const result = await api.get<Paginated<MediaItem>>(
            `/admin/media/browse?${params}`,
        );
        items.value =
            reset || page.value === 1
                ? result.data
                : [...items.value, ...result.data];
        lastPage.value = result.last_page;
    } catch (error) {
        failed.value =
            error instanceof ApiError
                ? error.message
                : 'Could not load the media library.';
    } finally {
        loading.value = false;
    }
}

async function upload(event: Event): Promise<void> {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (!file) {
        return;
    }

    uploading.value = true;

    try {
        const data = new FormData();
        data.append('file', file);
        const media = await api.post<MediaItem>('/admin/media', data);
        toast.success('Image uploaded.');
        choose(media);
    } catch (error) {
        toast.error(
            error instanceof ApiError ? error.message : 'Upload failed.',
        );
    } finally {
        uploading.value = false;

        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
}

function choose(media: MediaItem): void {
    emit('select', media);
    open.value = false;
}

function more(): void {
    page.value += 1;
    void load();
}

let timer: ReturnType<typeof setTimeout> | undefined;

watch(search, () => {
    clearTimeout(timer);
    timer = setTimeout(() => void load(true), 300);
});

watch(open, (value) => {
    if (value) {
        void load(true);
    }
});
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="max-h-[85vh] overflow-hidden sm:max-w-3xl"
            data-test="media-picker"
        >
            <DialogHeader>
                <DialogTitle>Choose an image</DialogTitle>
                <DialogDescription
                    >Select from the media library or upload a new image (JPG,
                    PNG, WebP or GIF, up to 8 MB).</DialogDescription
                >
            </DialogHeader>

            <div class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
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
                <input
                    ref="fileInput"
                    type="file"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    class="sr-only"
                    data-test="media-picker-file"
                    @change="upload"
                />
                <Button
                    type="button"
                    :disabled="uploading"
                    @click="fileInput?.click()"
                >
                    <LoaderCircle v-if="uploading" class="animate-spin" />
                    <ImagePlus v-else />
                    Upload new
                </Button>
            </div>

            <div class="-mx-1 max-h-[52vh] overflow-y-auto px-1">
                <p
                    v-if="failed"
                    class="rounded-md border border-destructive/30 bg-destructive/5 p-4 text-sm text-destructive"
                    role="alert"
                >
                    {{ failed }}
                    <button
                        type="button"
                        class="ml-2 underline"
                        @click="load(true)"
                    >
                        Retry
                    </button>
                </p>
                <div
                    v-else-if="loading && !items.length"
                    class="grid grid-cols-3 gap-3 sm:grid-cols-4"
                >
                    <div
                        v-for="n in 8"
                        :key="n"
                        class="aspect-square animate-pulse rounded-md bg-muted"
                    />
                </div>
                <p
                    v-else-if="!items.length"
                    class="py-12 text-center text-sm text-muted-foreground"
                >
                    No images yet. Upload one to get started.
                </p>
                <ul v-else class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                    <li v-for="media in items" :key="media.id">
                        <button
                            type="button"
                            class="group block w-full overflow-hidden rounded-md border border-border bg-muted text-left focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            :aria-label="`Select ${media.alt || 'image ' + media.id}`"
                            @click="choose(media)"
                        >
                            <img
                                :src="media.thumb_url"
                                :alt="media.alt ?? ''"
                                class="aspect-square w-full object-cover transition-opacity group-hover:opacity-80"
                                loading="lazy"
                            />
                        </button>
                    </li>
                </ul>
                <div v-if="page < lastPage" class="mt-4 text-center">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="loading"
                        @click="more"
                    >
                        <LoaderCircle v-if="loading" class="animate-spin" />
                        Load more
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
