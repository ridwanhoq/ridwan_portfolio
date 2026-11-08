<script setup lang="ts">
import { LoaderCircle } from '@lucide/vue';
import { ref } from 'vue';
import Prose from '@/components/site/Prose.vue';
import { api, ApiError } from '@/lib/api';
import { textareaClass } from './context';

const props = defineProps<{
    id: string;
    modelValue: string | null;
    rows?: number;
    invalid?: boolean;
    describedby?: string;
    placeholder?: string;
}>();
const emit = defineEmits<{ 'update:modelValue': [value: string] }>();

const tab = ref<'write' | 'preview'>('write');
const html = ref('');
const loading = ref(false);
const failed = ref<string | null>(null);

async function preview(): Promise<void> {
    tab.value = 'preview';
    loading.value = true;
    failed.value = null;

    try {
        const result = await api.post<{ html: string }>(
            '/admin/markdown/preview',
            { markdown: props.modelValue ?? '' },
        );
        html.value = result.html;
    } catch (error) {
        failed.value =
            error instanceof ApiError ? error.message : 'Preview failed.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="overflow-hidden rounded-md border border-input">
        <div
            class="flex items-center justify-between border-b border-input bg-muted/40 px-2 py-1"
        >
            <div class="flex gap-1" role="tablist" aria-label="Editor mode">
                <button
                    type="button"
                    role="tab"
                    :aria-selected="tab === 'write'"
                    class="rounded px-2.5 py-1 text-xs font-medium"
                    :class="
                        tab === 'write'
                            ? 'bg-background shadow-xs'
                            : 'text-muted-foreground'
                    "
                    @click="tab = 'write'"
                >
                    Write
                </button>
                <button
                    type="button"
                    role="tab"
                    :aria-selected="tab === 'preview'"
                    class="rounded px-2.5 py-1 text-xs font-medium"
                    :class="
                        tab === 'preview'
                            ? 'bg-background shadow-xs'
                            : 'text-muted-foreground'
                    "
                    @click="preview"
                >
                    Preview
                </button>
            </div>
            <span class="text-[11px] text-muted-foreground"
                >Markdown: **bold**, - lists, ## headings, `code`,
                [links](https://…)</span
            >
        </div>
        <textarea
            v-show="tab === 'write'"
            :id="id"
            :value="modelValue ?? ''"
            :rows="rows ?? 6"
            :placeholder="placeholder"
            :class="[
                textareaClass,
                'rounded-none border-0 font-mono text-[13px] shadow-none focus-visible:ring-0',
            ]"
            :aria-invalid="invalid || undefined"
            :aria-describedby="describedby"
            @input="
                emit(
                    'update:modelValue',
                    ($event.target as HTMLTextAreaElement).value,
                )
            "
        />
        <div v-if="tab === 'preview'" class="min-h-24 p-4" role="tabpanel">
            <LoaderCircle
                v-if="loading"
                class="size-4 animate-spin text-muted-foreground"
            />
            <p v-else-if="failed" class="text-sm text-destructive">
                {{ failed }}
            </p>
            <Prose v-else-if="html" :html="html" class="!text-sm" />
            <p v-else class="text-sm text-muted-foreground">
                Nothing to preview.
            </p>
        </div>
    </div>
</template>
