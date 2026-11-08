<script setup lang="ts">
defineProps<{
    modelValue: boolean;
    label: string;
    disabled?: boolean;
    id?: string;
    showLabel?: boolean;
}>();
const emit = defineEmits<{ 'update:modelValue': [value: boolean] }>();
</script>

<template>
    <button
        :id="id"
        type="button"
        role="switch"
        :aria-checked="modelValue"
        :aria-label="showLabel ? undefined : label"
        :disabled="disabled"
        class="group inline-flex items-center gap-3 disabled:cursor-not-allowed disabled:opacity-50"
        @click="emit('update:modelValue', !modelValue)"
    >
        <span
            class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full border border-transparent transition-colors"
            :class="modelValue ? 'bg-primary' : 'bg-input'"
        >
            <span
                class="inline-block size-4 rounded-full bg-background shadow-sm transition-transform"
                :class="modelValue ? 'translate-x-4' : 'translate-x-0.5'"
            />
        </span>
        <span v-if="showLabel" class="text-sm font-medium">{{ label }}</span>
    </button>
</template>
