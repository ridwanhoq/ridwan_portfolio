<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { FileDown, Mail, MapPin, Phone, Globe2 } from '@lucide/vue';
import { computed } from 'vue';
import type { Profile } from '@/types';
import BrandIcon from './BrandIcon.vue';

const props = defineProps<{ profile: Profile }>();
const page = usePage();
const socials = computed(() =>
    (page.props.site?.socials ?? []).filter((s) => s.platform !== 'email'),
);
const email = computed(() => props.profile.email);
</script>

<template>
    <dl class="grid gap-6 text-[15px]">
        <div v-if="email" class="flex gap-4">
            <dt>
                <Mail
                    class="mt-0.5 size-5 text-muted-foreground"
                    aria-hidden="true"
                /><span class="sr-only">Email</span>
            </dt>
            <dd>
                <a
                    :href="`mailto:${email}`"
                    class="font-medium hover:text-brand"
                    >{{ email }}</a
                >
            </dd>
        </div>
        <div v-if="profile.phone" class="flex gap-4">
            <dt>
                <Phone
                    class="mt-0.5 size-5 text-muted-foreground"
                    aria-hidden="true"
                /><span class="sr-only">Phone</span>
            </dt>
            <dd>
                <a
                    :href="`tel:${profile.phone.replace(/\s+/g, '')}`"
                    class="font-medium hover:text-brand"
                    >{{ profile.phone }}</a
                >
            </dd>
        </div>
        <div v-if="profile.location" class="flex gap-4">
            <dt>
                <MapPin
                    class="mt-0.5 size-5 text-muted-foreground"
                    aria-hidden="true"
                /><span class="sr-only">Location</span>
            </dt>
            <dd>{{ profile.location }}</dd>
        </div>
        <div v-if="profile.work_preference" class="flex gap-4">
            <dt>
                <Globe2
                    class="mt-0.5 size-5 text-muted-foreground"
                    aria-hidden="true"
                /><span class="sr-only">Work preference</span>
            </dt>
            <dd>{{ profile.work_preference }}</dd>
        </div>
        <div v-for="social in socials" :key="social.url" class="flex gap-4">
            <dt>
                <BrandIcon
                    :platform="social.platform"
                    class="mt-0.5 size-5 text-muted-foreground"
                /><span class="sr-only">{{ social.label }}</span>
            </dt>
            <dd>
                <a
                    :href="social.url"
                    target="_blank"
                    rel="noopener noreferrer me"
                    class="font-medium hover:text-brand"
                >
                    {{ social.label
                    }}<span class="sr-only"> (opens in new tab)</span>
                </a>
            </dd>
        </div>
        <div class="flex gap-4">
            <dt>
                <FileDown
                    class="mt-0.5 size-5 text-muted-foreground"
                    aria-hidden="true"
                /><span class="sr-only">Résumé</span>
            </dt>
            <dd>
                <a href="/resume/download" class="font-medium hover:text-brand"
                    >Download résumé (PDF)</a
                >
            </dd>
        </div>
    </dl>
</template>
