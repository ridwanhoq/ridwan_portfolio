<script setup lang="ts">
import { ArrowDown, ArrowUp, Plus, Trash2 } from '@lucide/vue';
import type { Field } from '@/admin/types';
import FormField from './FormField.vue';

const props = defineProps<{
    field: Extract<Field, { type: 'repeater' }>;
    modelValue: Record<string, unknown>[] | null;
    path: string;
    errors: Record<string, string>;
}>();
const emit = defineEmits<{
    'update:modelValue': [value: Record<string, unknown>[]];
}>();

function rows(): Record<string, unknown>[] {
    return [...(props.modelValue ?? [])];
}

function blank(): Record<string, unknown> {
    return Object.fromEntries(
        props.field.fields.map((f) => [
            f.key,
            f.type === 'select' && f.key === 'kind' ? 'screenshot' : null,
        ]),
    );
}

function add(): void {
    emit('update:modelValue', [...rows(), blank()]);
}

function remove(index: number): void {
    emit(
        'update:modelValue',
        rows().filter((_, i) => i !== index),
    );
}

function move(index: number, delta: number): void {
    const next = rows();
    const target = index + delta;

    if (target < 0 || target >= next.length) {
        return;
    }

    [next[index], next[target]] = [next[target], next[index]];
    emit('update:modelValue', next);
}

function update(index: number, key: string, value: unknown): void {
    const next = rows();
    next[index] = { ...next[index], [key]: value };
    emit('update:modelValue', next);
}
</script>

<template>
    <div class="grid gap-3">
        <div
            v-for="(row, index) in modelValue ?? []"
            :key="index"
            class="rounded-md border border-border bg-muted/30 p-4"
        >
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-medium text-muted-foreground"
                    >#{{ index + 1 }}</span
                >
                <div class="flex gap-1">
                    <button
                        type="button"
                        class="inline-flex size-7 items-center justify-center rounded text-muted-foreground hover:bg-muted disabled:opacity-30"
                        :disabled="index === 0"
                        aria-label="Move up"
                        @click="move(index, -1)"
                    >
                        <ArrowUp class="size-4" />
                    </button>
                    <button
                        type="button"
                        class="inline-flex size-7 items-center justify-center rounded text-muted-foreground hover:bg-muted disabled:opacity-30"
                        :disabled="index === (modelValue?.length ?? 0) - 1"
                        aria-label="Move down"
                        @click="move(index, 1)"
                    >
                        <ArrowDown class="size-4" />
                    </button>
                    <button
                        type="button"
                        class="inline-flex size-7 items-center justify-center rounded text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                        aria-label="Remove"
                        @click="remove(index)"
                    >
                        <Trash2 class="size-4" />
                    </button>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="sub in field.fields"
                    :key="sub.key"
                    :class="sub.span === 1 ? '' : 'sm:col-span-2'"
                >
                    <FormField
                        :field="sub"
                        :model-value="row[sub.key]"
                        :path="`${path}.${index}.${sub.key}`"
                        :errors="errors"
                        compact
                        @update:model-value="update(index, sub.key, $event)"
                    />
                </div>
            </div>
        </div>
        <button
            v-if="!field.max || (modelValue?.length ?? 0) < field.max"
            type="button"
            class="inline-flex w-fit items-center gap-1.5 rounded-md border border-dashed border-border px-3 py-1.5 text-sm font-medium text-muted-foreground hover:border-foreground/30 hover:text-foreground"
            @click="add"
        >
            <Plus class="size-4" /> {{ field.addLabel }}
        </button>
    </div>
</template>
