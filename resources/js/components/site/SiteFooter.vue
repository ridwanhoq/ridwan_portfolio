<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ArrowUp } from '@lucide/vue';
import { computed } from 'vue';
import BrandIcon from './BrandIcon.vue';
import SmartLink from './SmartLink.vue';

const page = usePage();
const site = computed(() => page.props.site);
const year = new Date().getFullYear();

function backToTop(): void {
    const reduced = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;
    window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
    document.getElementById('main')?.focus({ preventScroll: true });
}
</script>

<template>
    <footer class="no-print border-t border-border bg-surface" v-if="site">
        <div
            class="container-site grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_auto]"
        >
            <div class="max-w-sm">
                <p class="font-semibold tracking-tight">{{ site.name }}</p>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ site.designation
                    }}<template v-if="site.location">
                        · {{ site.location }}</template
                    >
                </p>
                <ul
                    v-if="site.socials.length"
                    class="mt-5 flex gap-2"
                    aria-label="Social profiles"
                >
                    <li v-for="social in site.socials" :key="social.url">
                        <a
                            :href="social.url"
                            class="inline-flex size-9 items-center justify-center rounded-md border border-border text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground"
                            :aria-label="social.label"
                            :title="social.label"
                            target="_blank"
                            rel="noopener noreferrer me"
                        >
                            <BrandIcon
                                :platform="social.platform"
                                class="size-4"
                            />
                        </a>
                    </li>
                </ul>
            </div>

            <nav v-if="site.nav.footer.length" aria-label="Footer">
                <p class="eyebrow mb-4">Explore</p>
                <ul class="grid grid-cols-2 gap-x-6 gap-y-2.5 text-sm">
                    <li v-for="link in site.nav.footer" :key="link.url">
                        <SmartLink
                            :href="link.url"
                            :new-tab="link.new_tab"
                            class="text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ link.label }}
                        </SmartLink>
                    </li>
                    <li v-if="site.blog_enabled">
                        <SmartLink
                            href="/blog"
                            class="text-muted-foreground transition-colors hover:text-foreground"
                        >
                            Blog
                        </SmartLink>
                    </li>
                </ul>
            </nav>

            <div class="md:text-right">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-md border border-border px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                    data-test="back-to-top"
                    @click="backToTop"
                >
                    <ArrowUp class="size-4" aria-hidden="true" />
                    Back to top
                </button>
            </div>
        </div>

        <div class="border-t border-border">
            <div
                class="container-site flex flex-col gap-2 py-6 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
            >
                <p>© {{ year }} {{ site.name }}. All rights reserved.</p>
                <p v-if="site.footer_note">{{ site.footer_note }}</p>
            </div>
        </div>
    </footer>
</template>
