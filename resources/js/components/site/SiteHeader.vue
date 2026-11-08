<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { FileDown, Menu, X } from '@lucide/vue';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import { useActiveSection } from '@/composables/useActiveSection';
import SmartLink from './SmartLink.vue';
import ThemeToggle from './ThemeToggle.vue';

const page = usePage();
const site = computed(() => page.props.site);
const links = computed(() => site.value?.nav.header ?? []);
const currentPath = computed(() => page.url.split(/[?#]/)[0] || '/');

const open = ref(false);
const scrolled = ref(false);
const menuButton = ref<HTMLButtonElement | null>(null);
const menuPanel = ref<HTMLElement | null>(null);

const anchorIds = () =>
    links.value
        .map((l) => l.url.split('#')[1])
        .filter((id): id is string => Boolean(id));

const activeSection = useActiveSection(anchorIds);

function isActive(url: string): boolean {
    const [path, hash] = url.split('#');
    const normalized = path || '/';

    if (hash) {
        return currentPath.value === normalized && activeSection.value === hash;
    }

    if (normalized === '/') {
        return currentPath.value === '/' && activeSection.value === null;
    }

    return currentPath.value.startsWith(normalized);
}

function onScroll(): void {
    scrolled.value = window.scrollY > 8;
}

function onKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape' && open.value) {
        open.value = false;
        menuButton.value?.focus();
    }
}

watch(open, async (value) => {
    document.body.style.overflow = value ? 'hidden' : '';

    if (value) {
        await nextTick();
        menuPanel.value?.querySelector<HTMLElement>('a, button')?.focus();
    }
});

watch(
    () => page.url,
    () => (open.value = false),
);

onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <header
        class="no-print sticky top-0 z-40 border-b transition-[background-color,border-color] duration-300"
        :class="
            scrolled || open
                ? 'border-border bg-background/90 backdrop-blur-md supports-[backdrop-filter]:bg-background/75'
                : 'border-transparent bg-background'
        "
    >
        <a
            href="#main"
            class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:rounded-md focus:bg-foreground focus:px-3 focus:py-2 focus:text-background"
        >
            Skip to content
        </a>

        <div
            class="container-site flex h-16 items-center justify-between gap-6"
        >
            <SmartLink
                href="/"
                class="group flex items-center gap-3 font-semibold tracking-tight"
            >
                <span
                    class="inline-flex size-8 items-center justify-center rounded-md bg-foreground font-mono text-[13px] font-semibold text-background"
                    aria-hidden="true"
                    >{{
                        (site?.short_name ?? 'P')
                            .split(' ')
                            .map((w) => w[0])
                            .join('')
                            .slice(0, 2)
                    }}</span
                >
                <span class="text-[15px]">{{ site?.short_name }}</span>
            </SmartLink>

            <nav aria-label="Primary" class="hidden lg:block">
                <ul class="flex items-center gap-1">
                    <li v-for="link in links" :key="link.url">
                        <SmartLink
                            :href="link.url"
                            :new-tab="link.new_tab"
                            class="relative rounded-md px-3 py-2 text-sm font-medium transition-colors hover:text-foreground"
                            :class="
                                isActive(link.url)
                                    ? 'text-foreground'
                                    : 'text-muted-foreground'
                            "
                            :aria-current="
                                isActive(link.url) ? 'page' : undefined
                            "
                        >
                            {{ link.label }}
                            <span
                                v-if="isActive(link.url)"
                                class="absolute inset-x-3 -bottom-[13px] h-px bg-brand"
                                aria-hidden="true"
                            />
                        </SmartLink>
                    </li>
                </ul>
            </nav>

            <div class="flex items-center gap-1.5">
                <ThemeToggle />
                <a
                    href="/resume/download"
                    class="hidden items-center gap-2 rounded-md bg-foreground px-3.5 py-2 text-sm font-medium text-background transition-opacity hover:opacity-90 sm:inline-flex"
                    data-test="header-resume"
                >
                    <FileDown class="size-4" aria-hidden="true" />
                    Résumé
                </a>
                <button
                    ref="menuButton"
                    type="button"
                    class="inline-flex size-9 items-center justify-center rounded-md text-foreground hover:bg-muted lg:hidden"
                    :aria-expanded="open"
                    aria-controls="mobile-menu"
                    :aria-label="open ? 'Close menu' : 'Open menu'"
                    data-test="mobile-menu-button"
                    @click="open = !open"
                >
                    <X v-if="open" class="size-5" aria-hidden="true" />
                    <Menu v-else class="size-5" aria-hidden="true" />
                </button>
            </div>
        </div>

        <div
            v-show="open"
            id="mobile-menu"
            ref="menuPanel"
            class="absolute inset-x-0 top-full z-40 h-[calc(100dvh-4rem)] overflow-y-auto border-t border-border bg-background lg:hidden"
            data-test="mobile-menu"
        >
            <nav aria-label="Mobile" class="container-site py-6">
                <ul class="divide-y divide-border">
                    <li v-for="link in links" :key="link.url">
                        <SmartLink
                            :href="link.url"
                            :new-tab="link.new_tab"
                            class="flex items-center justify-between py-4 text-lg font-medium"
                            :class="
                                isActive(link.url)
                                    ? 'text-brand'
                                    : 'text-foreground'
                            "
                            @click="open = false"
                        >
                            {{ link.label }}
                        </SmartLink>
                    </li>
                </ul>
                <a
                    href="/resume/download"
                    class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-md bg-foreground px-4 py-3 font-medium text-background"
                >
                    <FileDown class="size-4" aria-hidden="true" />
                    Download résumé
                </a>
            </nav>
        </div>
    </header>
</template>
