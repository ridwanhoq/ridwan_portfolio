import { config } from '@vue/test-utils';
import { vi } from 'vitest';

// Inertia's <Link> and <Head> need a running Inertia app; render simple stand-ins.
config.global.stubs = {
    Link: { props: ['href'], template: '<a :href="href"><slot /></a>' },
    Head: { template: '<div />' },
    SeoHead: { template: '<div />' },
};
config.global.directives = { reveal: {} };

if (!window.matchMedia) {
    Object.defineProperty(window, 'matchMedia', {
        writable: true,
        value: vi.fn().mockImplementation((query: string) => ({
            matches: false,
            media: query,
            addEventListener: vi.fn(),
            removeEventListener: vi.fn(),
        })),
    });
}
