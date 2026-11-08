<script setup lang="ts">
import type { SkillGroup } from '@/types';

defineProps<{ groups: SkillGroup[] }>();
</script>

<template>
    <div
        class="grid gap-px overflow-hidden rounded-lg border border-border bg-border sm:grid-cols-2 lg:grid-cols-4"
        data-test="skill-groups"
    >
        <section
            v-for="group in groups"
            :key="group.name"
            class="bg-background p-6"
            :aria-label="group.name"
        >
            <h3 class="text-sm font-semibold tracking-tight">
                {{ group.name }}
            </h3>
            <p
                v-if="group.description"
                class="mt-1 text-xs text-muted-foreground"
            >
                {{ group.description }}
            </p>
            <ul class="mt-4 space-y-2 text-[15px] text-foreground/80">
                <li v-for="skill in group.skills" :key="skill">{{ skill }}</li>
            </ul>
        </section>
    </div>
</template>
