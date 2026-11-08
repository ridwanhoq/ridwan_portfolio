<script setup lang="ts">
import { Moon, Sun } from '@lucide/vue';
import { computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance, updateAppearance } = useAppearance();

const isDark = computed(() => resolvedAppearance.value === 'dark');
const label = computed(() =>
    isDark.value ? 'Switch to light theme' : 'Switch to dark theme',
);

function toggle(): void {
    updateAppearance(isDark.value ? 'light' : 'dark');
}
</script>

<template>
    <button
        type="button"
        class="inline-flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
        :aria-label="label"
        :title="label"
        :aria-pressed="isDark"
        data-test="theme-toggle"
        @click="toggle"
    >
        <!-- Both icons render; CSS picks one so server and client markup always match. -->
        <Sun class="hidden size-[18px] dark:block" aria-hidden="true" />
        <Moon class="size-[18px] dark:hidden" aria-hidden="true" />
    </button>
</template>
