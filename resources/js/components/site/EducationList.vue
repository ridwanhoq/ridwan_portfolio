<script setup lang="ts">
import { ArrowUpRight } from '@lucide/vue';
import type { CertificationItem, EducationItem } from '@/types';

defineProps<{
    education: EducationItem[];
    certifications: CertificationItem[];
}>();
</script>

<template>
    <div class="grid gap-12 lg:grid-cols-2">
        <div v-if="education.length">
            <h3 class="eyebrow mb-5">Degrees</h3>
            <ul class="divide-y divide-border border-y border-border">
                <li
                    v-for="item in education"
                    :key="item.id"
                    class="grid gap-1 py-5 sm:grid-cols-[1fr_auto] sm:gap-6"
                >
                    <div>
                        <p class="font-semibold tracking-tight">
                            {{ item.degree
                            }}<template v-if="item.subject">
                                in {{ item.subject }}</template
                            >
                        </p>
                        <p class="mt-1 text-[15px] text-muted-foreground">
                            {{ item.institution }}
                        </p>
                        <p
                            v-if="item.grade"
                            class="mt-1 text-sm text-muted-foreground"
                        >
                            {{ item.grade }}
                        </p>
                        <p
                            v-if="item.description"
                            class="mt-2 text-sm text-foreground/80"
                        >
                            {{ item.description }}
                        </p>
                    </div>
                    <p
                        v-if="item.graduation_year"
                        class="font-mono text-sm text-muted-foreground"
                    >
                        <template v-if="item.start_year"
                            >{{ item.start_year }} – </template
                        >{{ item.graduation_year }}
                    </p>
                </li>
            </ul>
        </div>

        <div v-if="certifications.length">
            <h3 class="eyebrow mb-5">Certifications &amp; training</h3>
            <ul class="divide-y divide-border border-y border-border">
                <li
                    v-for="cert in certifications"
                    :key="cert.id"
                    class="grid gap-1 py-5 sm:grid-cols-[1fr_auto] sm:gap-6"
                >
                    <div>
                        <p class="font-semibold tracking-tight">
                            <a
                                v-if="cert.credential_url"
                                :href="cert.credential_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 hover:text-brand"
                            >
                                {{ cert.name
                                }}<ArrowUpRight
                                    class="size-3.5"
                                    aria-hidden="true"
                                />
                                <span class="sr-only"
                                    >(view credential, opens in new tab)</span
                                >
                            </a>
                            <template v-else>{{ cert.name }}</template>
                        </p>
                        <p class="mt-1 text-[15px] text-muted-foreground">
                            {{
                                [cert.issuer, cert.type]
                                    .filter(Boolean)
                                    .join(' · ')
                            }}
                        </p>
                        <p
                            v-if="cert.description"
                            class="mt-1 text-sm text-muted-foreground"
                        >
                            {{ cert.description }}
                        </p>
                        <p
                            v-if="cert.credential_id"
                            class="mt-1 font-mono text-xs text-muted-foreground"
                        >
                            Credential ID: {{ cert.credential_id }}
                        </p>
                    </div>
                    <p
                        v-if="cert.issued"
                        class="font-mono text-sm text-muted-foreground"
                    >
                        {{ cert.issued }}
                    </p>
                </li>
            </ul>
        </div>
    </div>
</template>
