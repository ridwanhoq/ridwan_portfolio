import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it } from 'vitest';
import ThemeToggle from '@/components/site/ThemeToggle.vue';

describe('ThemeToggle', () => {
    beforeEach(() => {
        localStorage.clear();
        document.documentElement.classList.remove('dark');
    });

    it('switches between light and dark and remembers the choice', async () => {
        const wrapper = mount(ThemeToggle);
        const button = wrapper.get('[data-test="theme-toggle"]');

        expect(button.attributes('aria-label')).toBe('Switch to dark theme');

        await button.trigger('click');
        expect(document.documentElement.classList.contains('dark')).toBe(true);
        expect(localStorage.getItem('appearance')).toBe('dark');
        expect(document.cookie).toContain('appearance=dark');
        expect(button.attributes('aria-label')).toBe('Switch to light theme');
        expect(button.attributes('aria-pressed')).toBe('true');

        await button.trigger('click');
        expect(document.documentElement.classList.contains('dark')).toBe(false);
        expect(localStorage.getItem('appearance')).toBe('light');
    });
});
