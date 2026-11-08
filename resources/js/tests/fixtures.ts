import type { ProjectCard } from '@/types';

export function project(overrides: Partial<ProjectCard> = {}): ProjectCard {
    return {
        id: 1,
        title: 'Booking Platform',
        slug: 'booking-platform',
        summary: 'A booking platform.',
        client: 'Acme',
        project_type: 'Web application',
        period: null,
        contribution: 'Built the API.',
        is_featured: true,
        live_url: null,
        repo_url: null,
        category: { name: 'Web applications', slug: 'web-applications' },
        technologies: ['Laravel', 'MySQL'],
        thumbnail: null,
        ...overrides,
    };
}
