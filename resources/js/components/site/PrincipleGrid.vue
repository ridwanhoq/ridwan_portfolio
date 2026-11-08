<script setup lang="ts">
import {
    Code,
    Database,
    FlaskConical,
    Gauge,
    GitBranch,
    Layers,
    ShieldCheck,
    Users,
} from '@lucide/vue';
import type { PrincipleItem } from '@/types';
import Prose from './Prose.vue';

defineProps<{ items: PrincipleItem[] }>();

const icons = {
    layers: Layers,
    'shield-check': ShieldCheck,
    gauge: Gauge,
    'flask-conical': FlaskConical,
    'git-branch': GitBranch,
    database: Database,
    code: Code,
    users: Users,
} as const;
</script>

<template>
    <div class="grid gap-x-12 gap-y-10 md:grid-cols-2">
        <article
            v-for="(item, i) in items"
            :key="item.title"
            v-reveal
            class="border-t border-border pt-6"
        >
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex size-9 items-center justify-center rounded-md bg-surface text-brand ring-1 ring-border"
                >
                    <component
                        :is="
                            icons[
                                (item.icon ?? 'layers') as keyof typeof icons
                            ] ?? Layers
                        "
                        class="size-[18px]"
                        aria-hidden="true"
                    />
                </span>
                <span class="font-mono text-xs text-muted-foreground">{{
                    String(i + 1).padStart(2, '0')
                }}</span>
            </div>
            <h3 class="mt-5 text-xl font-semibold tracking-tight">
                {{ item.title }}
            </h3>
            <Prose
                :html="item.html"
                class="mt-3 !text-[15px] text-muted-foreground"
            />
        </article>
    </div>
</template>
