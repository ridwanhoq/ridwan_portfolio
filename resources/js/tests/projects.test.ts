import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ProjectCard from '@/components/site/ProjectCard.vue';
import ProjectsIndex from '@/pages/site/projects/Index.vue';
import { project } from './fixtures';

const seo = {
    title: 'Projects',
    description: null,
    canonical: '/',
    image: '',
    type: 'website',
    site_name: 'x',
    robots: 'index',
};

describe('ProjectCard', () => {
    it('links to the case study and only shows links that exist', () => {
        const wrapper = mount(ProjectCard, { props: { project: project() } });

        expect(wrapper.get('h3 a').attributes('href')).toBe(
            '/projects/booking-platform',
        );
        expect(wrapper.text()).toContain('Built the API.');
        expect(wrapper.text()).not.toContain('Live site');

        const withLinks = mount(ProjectCard, {
            props: { project: project({ live_url: 'https://example.com' }) },
        });
        const live = withLinks.get('a[href="https://example.com"]');
        expect(live.attributes('rel')).toBe('noopener noreferrer');
    });
});

describe('Projects page', () => {
    const projects = [
        project({ id: 1, slug: 'a', title: 'Alpha' }),
        project({ id: 2, slug: 'b', title: 'Beta' }),
        project({
            id: 3,
            slug: 'c',
            title: 'Gamma',
            category: { name: 'Websites', slug: 'websites' },
        }),
        project({
            id: 4,
            slug: 'd',
            title: 'Delta',
            category: { name: 'Websites', slug: 'websites' },
        }),
    ];
    const categories = [
        { name: 'Web applications', slug: 'web-applications', count: 2 },
        { name: 'Websites', slug: 'websites', count: 2 },
    ];

    it('filters by category', async () => {
        const wrapper = mount(ProjectsIndex, {
            props: { seo, projects, categories, showFilters: true },
        });

        expect(wrapper.findAll('[data-test="project-card"]')).toHaveLength(4);

        await wrapper
            .findAll('[data-test="category-filter"]')[1]
            .trigger('click');

        const titles = wrapper
            .findAll('[data-test="project-card"] h2')
            .map((h) => h.text());
        expect(titles).toEqual(['Gamma', 'Delta']);
        expect(window.location.search).toBe('?category=websites');
    });

    it('hides filters when there are too few projects to justify them', () => {
        const wrapper = mount(ProjectsIndex, {
            props: {
                seo,
                projects: projects.slice(0, 2),
                categories,
                showFilters: true,
            },
        });

        expect(wrapper.find('[data-test="category-filter"]').exists()).toBe(
            false,
        );
        expect(wrapper.find('#project-search').exists()).toBe(false);
    });
});
