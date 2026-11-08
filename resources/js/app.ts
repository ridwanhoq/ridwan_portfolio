import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import { vReveal } from '@/composables/useReveal';
import { defineAsyncComponent } from 'vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

// Admin and auth layouts are split out so public visitors never download them.
const AppLayout = defineAsyncComponent(() => import('@/layouts/AppLayout.vue'));
const AuthLayout = defineAsyncComponent(
    () => import('@/layouts/AuthLayout.vue'),
);
const SettingsLayout = defineAsyncComponent(
    () => import('@/layouts/settings/Layout.vue'),
);

const appName = import.meta.env.VITE_APP_NAME || 'Portfolio';

void createInertiaApp({
    // Public pages provide complete titles via <SeoHead>; admin pages pass short titles.
    title: (title) => title || appName,
    layout: (name) => {
        switch (true) {
            case name.startsWith('site/'):
                return SiteLayout;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    withApp: (app) => {
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
        app.directive('reveal', vReveal);
    },
    progress: {
        color: '#2563EB',
        showSpinner: false,
    },
});

initializeTheme();
initializeFlashToast();
