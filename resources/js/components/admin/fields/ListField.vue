<script setup lang="ts">
import { ArrowDown, ArrowUp, Plus, X } from '@lucide/vue';
import { nextTick, ref } from 'vue';
import { inputClass } from './context';

const props = defineProps<{
    id: string;
    modelValue: string[] | null;
    invalid?: boolean;
    label: string;
}>();
const emit = defineEmits<{ 'update:modelValue': [value: string[]] }>();
const list = ref<HTMLElement | null>(null);

function items(): string[] {
    return [...(props.modelValue ?? [])];
}

function update(index: number, value: string): void {
    const next = items();
    next[index] = value;
    emit('update:modelValue', next);
}

async function add(): Promise<void> {
    emit('update:modelValue', [...items(), '']);
    await nextTick();
    const inputs = list.value?.querySelectorAll('input');
    inputs?.[inputs.length - 1]?.focus();
}

function remove(index: number): void {
    emit(
        'update:modelValue',
        items().filter((_, i) => i !== index),
    );
}

function move(index: number, delta: number): void {
    const next = items();
    const target = index + delta;

    if (target < 0 || target >= next.length) {
        return;
    }

    [next[index], next[target]] = [next[target], next[index]];
    emit('update:modelValue', next);
}

function onEnter(event: KeyboardEvent, index: number): void {
    if (index === items().length - 1) {
        event.preventDefault();
        void add();
    }
}
</script>

<template>
    <div ref="list" class="grid gap-2">
        <div
            v-for="(item, index) in modelValue ?? []"
            :key="index"
            class="flex items-center gap-1.5"
        >
            <input
                :id="index === 0 ? id : undefined"
                :value="item"
                type="text"
                :class="inputClass"
                :aria-label="`${label} item ${index + 1}`"
                :aria-invalid="invalid || undefined"
                @input="
                    update(index, ($event.target as HTMLInputElement).value)
                "
                @keydown.enter="onEnter($event, index)"
            />
            <button
                type="button"
                class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-muted-foreground hover:bg-muted disabled:opacity-30"
                :disabled="index === 0"
                aria-label="Move up"
                @click="move(index, -1)"
            >
                <ArrowUp class="size-4" />
            </button>
            <button
                type="button"
                class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-muted-foreground hover:bg-muted disabled:opacity-30"
                :disabled="index === (modelValue?.length ?? 0) - 1"
                aria-label="Move down"
                @click="move(index, 1)"
            >
                <ArrowDown class="size-4" />
            </button>
            <button
                type="button"
                class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                aria-label="Remove item"
                @click="remove(index)"
            >
                <X class="size-4" />
            </button>
        </div>
        <button
            type="button"
            class="inline-flex w-fit items-center gap-1.5 rounded-md border border-dashed border-border px-3 py-1.5 text-sm font-medium text-muted-foreground hover:border-foreground/30 hover:text-foreground"
            @click="add"
        >
            <Plus class="size-4" /> Add item
        </button>
    </div>
</template>
