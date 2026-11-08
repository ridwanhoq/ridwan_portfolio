<script setup lang="ts">
import { Check, Search } from '@lucide/vue';
import { computed, ref } from 'vue';
import type { Option } from '@/admin/types';

const props = defineProps<{
    id: string;
    modelValue: (number | string)[] | null;
    options: Option[];
    label: string;
}>();
const emit = defineEmits<{
    'update:modelValue': [value: (number | string)[]];
}>();

const filter = ref('');
const selected = computed(() => new Set(props.modelValue ?? []));
const visible = computed(() => {
    const term = filter.value.trim().toLowerCase();

    return term
        ? props.options.filter((o) => o.label.toLowerCase().includes(term))
        : props.options;
});

function toggle(value: number | string): void {
    const next = new Set(selected.value);

    if (next.has(value)) {
        next.delete(value);
    } else {
        next.add(value);
    }

    emit('update:modelValue', [...next]);
}
</script>

<template>
    <div
        :id="id"
        class="rounded-md border border-input p-3"
        role="group"
        :aria-label="label"
    >
        <div v-if="options.length > 12" class="relative mb-3">
            <Search
                class="pointer-events-none absolute top-1/2 left-2.5 size-3.5 -translate-y-1/2 text-muted-foreground"
                aria-hidden="true"
            />
            <input
                v-model="filter"
                type="search"
                :aria-label="`Filter ${label}`"
                placeholder="Filter…"
                class="h-8 w-full rounded-md border border-input bg-transparent pr-2 pl-8 text-sm focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
            />
        </div>
        <div class="flex flex-wrap gap-1.5">
            <button
                v-for="option in visible"
                :key="option.value"
                type="button"
                class="inline-flex items-center gap-1 rounded-md border px-2.5 py-1 text-xs font-medium transition-colors"
                :class="
                    selected.has(option.value)
                        ? 'border-primary bg-primary/10 text-primary'
                        : 'border-border text-muted-foreground hover:text-foreground'
                "
                :aria-pressed="selected.has(option.value)"
                @click="toggle(option.value)"
            >
                <Check
                    v-if="selected.has(option.value)"
                    class="size-3"
                    aria-hidden="true"
                />
                {{ option.label }}
            </button>
            <p v-if="!options.length" class="text-sm text-muted-foreground">
                No options yet.
            </p>
        </div>
        <p class="mt-2 text-xs text-muted-foreground">
            {{ selected.size }} selected
        </p>
    </div>
</template>
