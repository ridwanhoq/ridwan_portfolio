<script setup lang="ts">
import { computed, inject } from 'vue';
import type { Field, Option } from '@/admin/types';
import ToggleSwitch from '@/components/admin/ToggleSwitch.vue';
import { inputClass, optionsKey, textareaClass } from './context';
import ListField from './ListField.vue';
import MarkdownField from './MarkdownField.vue';
import MediaField from './MediaField.vue';
import MultiSelectField from './MultiSelectField.vue';
import RepeaterField from './RepeaterField.vue';

const props = defineProps<{
    field: Field;
    modelValue: unknown;
    /** Error key, e.g. "images.0.caption". */
    path: string;
    errors: Record<string, string>;
    compact?: boolean;
}>();
const emit = defineEmits<{ 'update:modelValue': [value: unknown] }>();

const allOptions = inject(optionsKey, {});
const id = computed(() => `field-${props.path.replace(/\./g, '-')}`);
const error = computed(() => props.errors[props.path]);
const describedby = computed(
    () =>
        [
            error.value ? `${id.value}-error` : null,
            props.field.help ? `${id.value}-help` : null,
        ]
            .filter(Boolean)
            .join(' ') || undefined,
);

const options = computed<Option[]>(() => {
    if (!('options' in props.field)) {
        return [];
    }

    return typeof props.field.options === 'string'
        ? (allOptions[props.field.options] ?? [])
        : props.field.options;
});

const text = computed(() => (props.modelValue ?? '') as string | number);
const length = computed(() => String(props.modelValue ?? '').length);

function set(value: unknown): void {
    emit('update:modelValue', value);
}

function onSelect(event: Event): void {
    const raw = (event.target as HTMLSelectElement).value;

    if (raw === '') {
        set(null);

        return;
    }

    const match = options.value.find((o) => String(o.value) === raw);
    set(match ? match.value : raw);
}

function onNumber(event: Event): void {
    const raw = (event.target as HTMLInputElement).value;
    set(raw === '' ? null : Number(raw));
}
</script>

<template>
    <div class="grid gap-2">
        <div
            v-if="field.type !== 'switch'"
            class="flex items-baseline justify-between gap-3"
        >
            <label :for="id" class="text-sm font-medium">
                {{ field.label
                }}<span
                    v-if="field.required"
                    class="text-destructive"
                    aria-hidden="true"
                >
                    *</span
                >
            </label>
            <span
                v-if="field.maxLength"
                class="text-[11px] tabular-nums"
                :class="
                    length > field.maxLength
                        ? 'text-destructive'
                        : 'text-muted-foreground'
                "
            >
                {{ length }} / {{ field.maxLength }}
            </span>
        </div>

        <ToggleSwitch
            v-if="field.type === 'switch'"
            :id="id"
            :model-value="Boolean(modelValue)"
            :label="field.label"
            show-label
            @update:model-value="set"
        />

        <p
            v-else-if="field.type === 'readonly'"
            :id="id"
            class="text-sm text-foreground/80"
        >
            {{ modelValue }}
        </p>

        <textarea
            v-else-if="field.type === 'textarea'"
            :id="id"
            :value="text"
            :rows="field.rows ?? 3"
            :placeholder="field.placeholder"
            :class="textareaClass"
            :aria-invalid="error ? true : undefined"
            :aria-describedby="describedby"
            :required="field.required"
            @input="set(($event.target as HTMLTextAreaElement).value)"
        />

        <MarkdownField
            v-else-if="field.type === 'markdown'"
            :id="id"
            :model-value="modelValue as string | null"
            :rows="field.rows"
            :placeholder="field.placeholder"
            :invalid="Boolean(error)"
            :describedby="describedby"
            @update:model-value="set"
        />

        <select
            v-else-if="field.type === 'select'"
            :id="id"
            :value="
                modelValue === null || modelValue === undefined
                    ? ''
                    : String(modelValue)
            "
            :class="inputClass"
            :aria-invalid="error ? true : undefined"
            :aria-describedby="describedby"
            @change="onSelect"
        >
            <option v-if="field.nullable || !field.required" value="">
                — None —
            </option>
            <option
                v-for="option in options"
                :key="option.value"
                :value="String(option.value)"
            >
                {{ option.label }}
            </option>
        </select>

        <MultiSelectField
            v-else-if="field.type === 'multiselect'"
            :id="id"
            :label="field.label"
            :model-value="modelValue as number[] | null"
            :options="options"
            @update:model-value="set"
        />

        <ListField
            v-else-if="field.type === 'list'"
            :id="id"
            :label="field.label"
            :model-value="modelValue as string[] | null"
            :invalid="Boolean(error)"
            @update:model-value="set"
        />

        <MediaField
            v-else-if="field.type === 'media'"
            :id="id"
            :compact="compact"
            :model-value="modelValue as number | null"
            @update:model-value="set"
        />

        <RepeaterField
            v-else-if="field.type === 'repeater'"
            :field="field"
            :path="path"
            :errors="errors"
            :model-value="modelValue as Record<string, unknown>[] | null"
            @update:model-value="set"
        />

        <input
            v-else-if="field.type === 'number'"
            :id="id"
            :value="text"
            type="number"
            inputmode="numeric"
            :class="inputClass"
            :aria-invalid="error ? true : undefined"
            :aria-describedby="describedby"
            :required="field.required"
            @input="onNumber"
        />

        <input
            v-else
            :id="id"
            :value="text"
            :type="
                field.type === 'datetime'
                    ? 'datetime-local'
                    : field.type === 'slug'
                      ? 'text'
                      : field.type
            "
            :placeholder="
                field.type === 'month' ? 'YYYY-MM' : field.placeholder
            "
            :maxlength="field.maxLength"
            :class="[inputClass, field.type === 'slug' ? 'font-mono' : '']"
            :aria-invalid="error ? true : undefined"
            :aria-describedby="describedby"
            :required="field.required"
            :autocomplete="field.type === 'email' ? 'email' : 'off'"
            @input="set(($event.target as HTMLInputElement).value)"
        />

        <p
            v-if="field.help"
            :id="`${id}-help`"
            class="text-xs text-muted-foreground"
        >
            {{ field.help }}
        </p>
        <p
            v-if="error"
            :id="`${id}-error`"
            class="text-sm text-destructive"
            data-test="field-error"
        >
            {{ error }}
        </p>
    </div>
</template>
