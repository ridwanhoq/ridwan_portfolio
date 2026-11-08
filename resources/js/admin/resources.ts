import type { Field, ResourceConfig } from './types';

const sortOrder: Field = {
    key: 'sort_order',
    label: 'Sort order',
    type: 'number',
    help: 'Lower numbers appear first.',
    span: 1,
};

const published: Field = {
    key: 'is_published',
    label: 'Published',
    type: 'switch',
    help: 'Show on the public website.',
    span: 1,
};

/**
 * Table and form definitions for every admin module rendered by the
 * generic admin/resource/Index and admin/resource/Form pages.
 * Validation lives in the matching Laravel Form Request.
 */
export const resources: Record<string, ResourceConfig> = {
    experiences: {
        title: 'Experience',
        singular: 'experience',
        description:
            'Employment history shown on the homepage timeline, About page and résumé. Sorted by most recent first.',
        columns: [
            {
                key: 'title',
                label: 'Role',
                type: 'title',
                sub: 'company',
                sortable: true,
            },
            { key: 'period', label: 'Period' },
            { key: 'employment_type', label: 'Type' },
            { key: 'is_published', label: 'Published', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Role',
                fields: [
                    {
                        key: 'title',
                        label: 'Job title',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'company',
                        label: 'Company',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'employment_type',
                        label: 'Employment type',
                        type: 'select',
                        options: 'employment_types',
                        span: 1,
                    },
                    {
                        key: 'location',
                        label: 'Location',
                        type: 'text',
                        span: 1,
                        placeholder: 'Dhaka, Bangladesh',
                    },
                    {
                        key: 'focus',
                        label: 'Focus / summary line',
                        type: 'text',
                        placeholder: 'Laravel / PHP backend development',
                    },
                    {
                        key: 'company_url',
                        label: 'Company website',
                        type: 'url',
                        placeholder: 'https://',
                    },
                ],
            },
            {
                title: 'Dates',
                fields: [
                    {
                        key: 'start_date',
                        label: 'Start',
                        type: 'month',
                        required: true,
                        span: 1,
                    },
                    {
                        key: 'end_date',
                        label: 'End',
                        type: 'month',
                        span: 1,
                        showIf: { field: 'is_current', equals: false },
                    },
                    {
                        key: 'is_current',
                        label: 'I currently work here',
                        type: 'switch',
                    },
                ],
            },
            {
                title: 'Details',
                fields: [
                    {
                        key: 'description',
                        label: 'Description',
                        type: 'markdown',
                        rows: 4,
                        help: 'Optional short paragraph.',
                    },
                    {
                        key: 'responsibilities',
                        label: 'Responsibilities',
                        type: 'list',
                        help: 'One item per line.',
                    },
                    {
                        key: 'contributions',
                        label: 'Key contributions',
                        type: 'list',
                        help: 'Concrete outcomes you can stand behind.',
                    },
                    {
                        key: 'technology_ids',
                        label: 'Technology stack',
                        type: 'multiselect',
                        options: 'technologies',
                    },
                ],
            },
            { title: 'Visibility', fields: [published, sortOrder] },
        ],
    },

    projects: {
        title: 'Projects',
        singular: 'project',
        description:
            'Portfolio projects and their case studies. Featured, published projects appear on the homepage.',
        columns: [
            { key: 'thumbnail', label: '', type: 'image', class: 'w-16' },
            {
                key: 'title',
                label: 'Project',
                type: 'title',
                sub: 'category',
                sortable: true,
            },
            { key: 'status', label: 'Status', type: 'status', sortable: true },
            { key: 'is_featured', label: 'Featured', type: 'toggle' },
            {
                key: 'updated_at',
                label: 'Updated',
                type: 'date',
                sortable: true,
            },
        ],
        filters: [
            { key: 'status', label: 'Status', options: 'statuses' },
            { key: 'category', label: 'Category', options: 'categories' },
        ],
        publicUrl: (row) => (row.public_url as string | null) ?? null,
        groups: [
            {
                title: 'Basics',
                fields: [
                    {
                        key: 'title',
                        label: 'Title',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'slug',
                        label: 'URL slug',
                        type: 'slug',
                        help: 'Leave blank to generate from the title.',
                    },
                    {
                        key: 'project_category_id',
                        label: 'Category',
                        type: 'select',
                        options: 'categories',
                        nullable: true,
                        span: 1,
                    },
                    {
                        key: 'period',
                        label: 'Period',
                        type: 'text',
                        span: 1,
                        placeholder: '2023 – 2024',
                    },
                    {
                        key: 'client',
                        label: 'Client / organisation',
                        type: 'text',
                        span: 1,
                    },
                    {
                        key: 'project_type',
                        label: 'Project type',
                        type: 'text',
                        span: 1,
                        placeholder: 'Web application',
                    },
                    {
                        key: 'summary',
                        label: 'Short description',
                        type: 'textarea',
                        rows: 3,
                        required: true,
                        maxLength: 500,
                        help: 'Shown on project cards.',
                    },
                    {
                        key: 'contribution',
                        label: 'Personal contribution',
                        type: 'textarea',
                        rows: 3,
                        maxLength: 1000,
                        help: 'What you personally did. Shown on cards and the case study.',
                    },
                    {
                        key: 'technology_ids',
                        label: 'Technologies',
                        type: 'multiselect',
                        options: 'technologies',
                    },
                ],
            },
            {
                title: 'Media and links',
                fields: [
                    {
                        key: 'thumbnail_media_id',
                        label: 'Thumbnail',
                        type: 'media',
                        help: '16:9 works best. Used on cards and social previews.',
                    },
                    {
                        key: 'images',
                        label: 'Screenshots and diagrams',
                        type: 'repeater',
                        addLabel: 'Add image',
                        max: 30,
                        fields: [
                            { key: 'media_id', label: 'Image', type: 'media' },
                            {
                                key: 'kind',
                                label: 'Type',
                                type: 'select',
                                options: 'image_kinds',
                                span: 1,
                            },
                            {
                                key: 'caption',
                                label: 'Caption',
                                type: 'text',
                                span: 1,
                            },
                        ],
                    },
                    {
                        key: 'live_url',
                        label: 'Live demo URL',
                        type: 'url',
                        span: 1,
                        placeholder: 'https://',
                    },
                    {
                        key: 'repo_url',
                        label: 'Source code URL',
                        type: 'url',
                        span: 1,
                        placeholder: 'https://github.com/…',
                    },
                    {
                        key: 'links',
                        label: 'Other external links',
                        type: 'repeater',
                        addLabel: 'Add link',
                        max: 10,
                        fields: [
                            {
                                key: 'label',
                                label: 'Label',
                                type: 'text',
                                span: 1,
                            },
                            {
                                key: 'url',
                                label: 'URL',
                                type: 'url',
                                span: 1,
                                placeholder: 'https://',
                            },
                        ],
                    },
                ],
            },
            {
                title: 'Case study',
                description:
                    'All sections are optional and use Markdown. Only sections with content are shown. Do not include confidential client details or proprietary code.',
                fields: [
                    {
                        key: 'overview',
                        label: 'Project overview',
                        type: 'markdown',
                    },
                    {
                        key: 'business_context',
                        label: 'Business context',
                        type: 'markdown',
                    },
                    {
                        key: 'problem',
                        label: 'Problem statement',
                        type: 'markdown',
                    },
                    {
                        key: 'challenges',
                        label: 'Technical challenges and solutions',
                        type: 'markdown',
                    },
                    {
                        key: 'role_details',
                        label: 'My role and responsibilities',
                        type: 'markdown',
                    },
                    {
                        key: 'architecture',
                        label: 'Architecture decisions',
                        type: 'markdown',
                    },
                    {
                        key: 'database_design',
                        label: 'Database design',
                        type: 'markdown',
                    },
                    {
                        key: 'api_development',
                        label: 'API development',
                        type: 'markdown',
                    },
                    {
                        key: 'security',
                        label: 'Security implementation',
                        type: 'markdown',
                    },
                    {
                        key: 'performance',
                        label: 'Performance optimization',
                        type: 'markdown',
                    },
                    {
                        key: 'testing_deployment',
                        label: 'Testing and deployment',
                        type: 'markdown',
                    },
                    {
                        key: 'results',
                        label: 'Results and measurable outcomes',
                        type: 'markdown',
                        help: 'Only include results you can verify.',
                    },
                ],
            },
            {
                title: 'Publishing',
                fields: [
                    {
                        key: 'status',
                        label: 'Status',
                        type: 'select',
                        options: 'statuses',
                        span: 1,
                    },
                    {
                        key: 'published_at',
                        label: 'Publish date',
                        type: 'datetime',
                        span: 1,
                        help: 'Leave blank to publish immediately.',
                    },
                    {
                        key: 'is_featured',
                        label: 'Feature on homepage',
                        type: 'switch',
                        span: 1,
                    },
                    sortOrder,
                ],
            },
            {
                title: 'SEO',
                fields: [
                    {
                        key: 'meta_title',
                        label: 'Meta title',
                        type: 'text',
                        maxLength: 255,
                        help: 'Defaults to “Title — Case study”.',
                    },
                    {
                        key: 'meta_description',
                        label: 'Meta description',
                        type: 'textarea',
                        rows: 2,
                        maxLength: 320,
                        help: 'Defaults to the short description.',
                    },
                ],
            },
        ],
    },

    'project-categories': {
        title: 'Project categories',
        singular: 'category',
        description:
            'Used to group and filter projects. Filters appear when at least two categories have published projects.',
        columns: [
            {
                key: 'name',
                label: 'Name',
                type: 'title',
                sub: 'slug',
                sortable: true,
            },
            { key: 'projects_count', label: 'Projects', type: 'count' },
            { key: 'sort_order', label: 'Order', sortable: true },
        ],
        groups: [
            {
                title: 'Category',
                fields: [
                    {
                        key: 'name',
                        label: 'Name',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'slug',
                        label: 'Slug',
                        type: 'slug',
                        help: 'Leave blank to generate from the name.',
                    },
                    {
                        key: 'description',
                        label: 'Description',
                        type: 'textarea',
                        rows: 2,
                    },
                    sortOrder,
                ],
            },
        ],
    },

    technologies: {
        title: 'Technologies',
        singular: 'technology',
        description:
            'Shared list of technologies linked to projects and experience.',
        columns: [
            {
                key: 'name',
                label: 'Name',
                type: 'title',
                sub: 'slug',
                sortable: true,
            },
            { key: 'projects_count', label: 'Projects', type: 'count' },
            { key: 'experiences_count', label: 'Roles', type: 'count' },
            { key: 'sort_order', label: 'Order', sortable: true },
        ],
        groups: [
            {
                title: 'Technology',
                fields: [
                    {
                        key: 'name',
                        label: 'Name',
                        type: 'text',
                        required: true,
                    },
                    { key: 'slug', label: 'Slug', type: 'slug' },
                    {
                        key: 'url',
                        label: 'Website',
                        type: 'url',
                        placeholder: 'https://',
                    },
                    sortOrder,
                ],
            },
        ],
    },

    'skill-categories': {
        title: 'Skill categories',
        singular: 'skill category',
        description:
            'Groups shown in the Technical Skills section, such as Backend or Testing.',
        columns: [
            {
                key: 'name',
                label: 'Name',
                type: 'title',
                sub: 'description',
                sortable: true,
            },
            { key: 'skills_count', label: 'Skills', type: 'count' },
            { key: 'sort_order', label: 'Order', sortable: true },
            { key: 'is_published', label: 'Published', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Category',
                fields: [
                    {
                        key: 'name',
                        label: 'Name',
                        type: 'text',
                        required: true,
                    },
                    { key: 'slug', label: 'Slug', type: 'slug' },
                    {
                        key: 'description',
                        label: 'Description',
                        type: 'textarea',
                        rows: 2,
                    },
                    published,
                    sortOrder,
                ],
            },
        ],
    },

    skills: {
        title: 'Skills',
        singular: 'skill',
        description:
            'Individual skills listed under each category. No proficiency percentages, by design.',
        columns: [
            { key: 'name', label: 'Skill', type: 'title', sortable: true },
            { key: 'category', label: 'Category' },
            { key: 'sort_order', label: 'Order', sortable: true },
            { key: 'is_published', label: 'Published', type: 'toggle' },
        ],
        filters: [
            { key: 'category', label: 'Category', options: 'categories' },
        ],
        groups: [
            {
                title: 'Skill',
                fields: [
                    {
                        key: 'name',
                        label: 'Name',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'skill_category_id',
                        label: 'Category',
                        type: 'select',
                        options: 'categories',
                        required: true,
                    },
                    published,
                    sortOrder,
                ],
            },
        ],
    },

    principles: {
        title: 'Engineering approach',
        singular: 'approach block',
        description: 'Content blocks for the “Engineering approach” section.',
        columns: [
            { key: 'title', label: 'Title', type: 'title', sortable: true },
            { key: 'sort_order', label: 'Order', sortable: true },
            { key: 'is_published', label: 'Published', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Block',
                fields: [
                    {
                        key: 'title',
                        label: 'Title',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'icon',
                        label: 'Icon',
                        type: 'select',
                        options: 'icons',
                    },
                    {
                        key: 'body',
                        label: 'Body',
                        type: 'markdown',
                        required: true,
                        rows: 5,
                    },
                    published,
                    sortOrder,
                ],
            },
        ],
    },

    education: {
        title: 'Education',
        singular: 'education record',
        description:
            'Degrees and academic qualifications. The section hides itself when empty.',
        columns: [
            {
                key: 'degree',
                label: 'Degree',
                type: 'title',
                sub: 'institution',
                sortable: false,
            },
            { key: 'subject', label: 'Subject' },
            { key: 'graduation_year', label: 'Year', sortable: true },
            { key: 'is_published', label: 'Published', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Qualification',
                fields: [
                    {
                        key: 'institution',
                        label: 'Institution',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'degree',
                        label: 'Degree',
                        type: 'text',
                        required: true,
                        span: 1,
                        placeholder: 'M.Sc.',
                    },
                    { key: 'subject', label: 'Subject', type: 'text', span: 1 },
                    {
                        key: 'start_year',
                        label: 'Start year',
                        type: 'number',
                        span: 1,
                    },
                    {
                        key: 'graduation_year',
                        label: 'Graduation year',
                        type: 'number',
                        span: 1,
                    },
                    {
                        key: 'grade',
                        label: 'Grade / CGPA',
                        type: 'text',
                        span: 1,
                    },
                    {
                        key: 'description',
                        label: 'Notes',
                        type: 'textarea',
                        rows: 2,
                    },
                    published,
                    sortOrder,
                ],
            },
        ],
    },

    certifications: {
        title: 'Certifications',
        singular: 'certification',
        description: 'Certifications, training and awards.',
        columns: [
            {
                key: 'name',
                label: 'Name',
                type: 'title',
                sub: 'issuer',
                sortable: true,
            },
            { key: 'type', label: 'Type', type: 'badge' },
            { key: 'issued_on', label: 'Issued' },
            { key: 'is_published', label: 'Published', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Certification',
                fields: [
                    {
                        key: 'name',
                        label: 'Name',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'issuer',
                        label: 'Issuing organisation',
                        type: 'text',
                        span: 1,
                    },
                    {
                        key: 'type',
                        label: 'Type',
                        type: 'select',
                        options: 'types',
                        span: 1,
                    },
                    {
                        key: 'issued_on',
                        label: 'Issued',
                        type: 'month',
                        span: 1,
                        help: 'Only the year is shown publicly.',
                    },
                    {
                        key: 'expires_on',
                        label: 'Expires',
                        type: 'month',
                        span: 1,
                    },
                    {
                        key: 'credential_id',
                        label: 'Credential ID',
                        type: 'text',
                        span: 1,
                    },
                    {
                        key: 'credential_url',
                        label: 'Credential URL',
                        type: 'url',
                        span: 1,
                        placeholder: 'https://',
                    },
                    { key: 'description', label: 'Short note', type: 'text' },
                    published,
                    sortOrder,
                ],
            },
        ],
    },

    'social-links': {
        title: 'Social links',
        singular: 'social link',
        description:
            'Profiles shown in the hero, contact section and footer, and used in structured data.',
        columns: [
            {
                key: 'label',
                label: 'Label',
                type: 'title',
                sub: 'url',
                sortable: true,
            },
            { key: 'platform', label: 'Platform', type: 'badge' },
            { key: 'show_in_hero', label: 'In hero', type: 'toggle' },
            { key: 'is_published', label: 'Published', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Link',
                fields: [
                    {
                        key: 'platform',
                        label: 'Platform',
                        type: 'select',
                        options: 'platforms',
                        span: 1,
                    },
                    {
                        key: 'label',
                        label: 'Label',
                        type: 'text',
                        required: true,
                        span: 1,
                    },
                    {
                        key: 'url',
                        label: 'URL',
                        type: 'text',
                        required: true,
                        placeholder: 'https://www.linkedin.com/in/…',
                    },
                    {
                        key: 'show_in_hero',
                        label: 'Show in hero',
                        type: 'switch',
                        span: 1,
                    },
                    published,
                    sortOrder,
                ],
            },
        ],
    },

    navigation: {
        title: 'Navigation',
        singular: 'navigation item',
        description:
            'Header and footer menus. Use /#section for homepage anchors (about, experience, projects, skills, approach, education, contact).',
        columns: [
            {
                key: 'label',
                label: 'Label',
                type: 'title',
                sub: 'url',
                sortable: true,
            },
            {
                key: 'location',
                label: 'Location',
                type: 'badge',
                sortable: true,
            },
            { key: 'sort_order', label: 'Order', sortable: true },
            { key: 'is_visible', label: 'Visible', type: 'toggle' },
        ],
        filters: [{ key: 'location', label: 'Location', options: 'locations' }],
        groups: [
            {
                title: 'Menu item',
                fields: [
                    {
                        key: 'label',
                        label: 'Label',
                        type: 'text',
                        required: true,
                        span: 1,
                    },
                    {
                        key: 'location',
                        label: 'Menu',
                        type: 'select',
                        options: 'locations',
                        span: 1,
                    },
                    {
                        key: 'url',
                        label: 'URL',
                        type: 'text',
                        required: true,
                        placeholder: '/#projects or /projects',
                    },
                    {
                        key: 'open_in_new_tab',
                        label: 'Open in new tab',
                        type: 'switch',
                        span: 1,
                    },
                    {
                        key: 'is_visible',
                        label: 'Visible',
                        type: 'switch',
                        span: 1,
                    },
                    sortOrder,
                ],
            },
        ],
    },

    posts: {
        title: 'Blog posts',
        singular: 'post',
        description:
            'Optional blog. Enable it in Settings to make /blog public.',
        columns: [
            {
                key: 'title',
                label: 'Title',
                type: 'title',
                sub: 'slug',
                sortable: true,
            },
            { key: 'status', label: 'Status', type: 'status', sortable: true },
            {
                key: 'published_at',
                label: 'Published',
                type: 'date',
                sortable: true,
            },
            {
                key: 'updated_at',
                label: 'Updated',
                type: 'date',
                sortable: true,
            },
        ],
        filters: [{ key: 'status', label: 'Status', options: 'statuses' }],
        groups: [
            {
                title: 'Post',
                fields: [
                    {
                        key: 'title',
                        label: 'Title',
                        type: 'text',
                        required: true,
                    },
                    { key: 'slug', label: 'Slug', type: 'slug' },
                    {
                        key: 'excerpt',
                        label: 'Excerpt',
                        type: 'textarea',
                        rows: 2,
                        maxLength: 500,
                    },
                    {
                        key: 'body',
                        label: 'Body',
                        type: 'markdown',
                        required: true,
                        rows: 16,
                    },
                    {
                        key: 'cover_media_id',
                        label: 'Cover image',
                        type: 'media',
                    },
                ],
            },
            {
                title: 'Publishing',
                fields: [
                    {
                        key: 'status',
                        label: 'Status',
                        type: 'select',
                        options: 'statuses',
                        span: 1,
                    },
                    {
                        key: 'published_at',
                        label: 'Publish date',
                        type: 'datetime',
                        span: 1,
                    },
                    {
                        key: 'meta_description',
                        label: 'Meta description',
                        type: 'textarea',
                        rows: 2,
                        maxLength: 320,
                    },
                ],
            },
        ],
    },

    seo: {
        title: 'SEO',
        singular: 'page',
        description:
            'Search and social metadata for each public page. Project case studies have their own SEO fields.',
        searchable: false,
        columns: [
            {
                key: 'label',
                label: 'Page',
                type: 'title',
                sub: 'title',
                sortable: true,
            },
            { key: 'description_length', label: 'Description', type: 'count' },
            { key: 'noindex', label: 'Hidden from search', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Search appearance',
                description:
                    'Aim for titles under 60 characters and descriptions of 120–160 characters.',
                fields: [
                    { key: 'label', label: 'Page', type: 'readonly' },
                    {
                        key: 'title',
                        label: 'Title',
                        type: 'text',
                        maxLength: 90,
                    },
                    {
                        key: 'description',
                        label: 'Meta description',
                        type: 'textarea',
                        rows: 3,
                        maxLength: 320,
                    },
                    {
                        key: 'og_image_media_id',
                        label: 'Social share image',
                        type: 'media',
                        help: '1200 × 630 recommended. Falls back to the homepage image, then a default.',
                    },
                    {
                        key: 'noindex',
                        label: 'Hide this page from search engines',
                        type: 'switch',
                    },
                ],
            },
        ],
    },

    sections: {
        title: 'Homepage sections',
        singular: 'section',
        description:
            'Turn homepage sections on or off, change their order and edit their headings.',
        searchable: false,
        columns: [
            {
                key: 'label',
                label: 'Section',
                type: 'title',
                sub: 'heading',
                sortable: true,
            },
            { key: 'sort_order', label: 'Order', sortable: true },
            { key: 'is_enabled', label: 'Enabled', type: 'toggle' },
        ],
        groups: [
            {
                title: 'Section',
                fields: [
                    { key: 'label', label: 'Section', type: 'readonly' },
                    {
                        key: 'eyebrow',
                        label: 'Eyebrow label',
                        type: 'text',
                        span: 1,
                    },
                    {
                        key: 'sort_order',
                        label: 'Order',
                        type: 'number',
                        span: 1,
                        help: 'Lower numbers appear first.',
                    },
                    { key: 'heading', label: 'Heading', type: 'text' },
                    {
                        key: 'intro',
                        label: 'Intro text',
                        type: 'textarea',
                        rows: 3,
                    },
                    {
                        key: 'is_enabled',
                        label: 'Show this section',
                        type: 'switch',
                    },
                ],
            },
        ],
    },

    profile: {
        title: 'Profile',
        singular: 'profile',
        description:
            'Your professional identity, hero content and About page copy.',
        columns: [],
        groups: [
            {
                title: 'Identity',
                fields: [
                    {
                        key: 'full_name',
                        label: 'Full name',
                        type: 'text',
                        required: true,
                        span: 1,
                    },
                    {
                        key: 'short_name',
                        label: 'Short name',
                        type: 'text',
                        span: 1,
                        help: 'Used in the header logo.',
                    },
                    {
                        key: 'designation',
                        label: 'Professional title',
                        type: 'text',
                        required: true,
                        span: 1,
                    },
                    {
                        key: 'tagline',
                        label: 'Secondary title',
                        type: 'text',
                        span: 1,
                    },
                    {
                        key: 'photo_media_id',
                        label: 'Profile photo',
                        type: 'media',
                        help: 'Optional. Use a real, recent photo. Shown in the hero and About page.',
                    },
                ],
            },
            {
                title: 'Hero',
                fields: [
                    {
                        key: 'hero_title',
                        label: 'Headline',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'hero_subtitle',
                        label: 'Supporting description',
                        type: 'textarea',
                        rows: 3,
                    },
                    {
                        key: 'years_experience',
                        label: 'Years of experience',
                        type: 'number',
                        span: 1,
                    },
                    {
                        key: 'years_primary_stack',
                        label: 'Years with primary stack',
                        type: 'number',
                        span: 1,
                    },
                    {
                        key: 'primary_stack_label',
                        label: 'Primary stack label',
                        type: 'text',
                        span: 1,
                        placeholder: 'PHP & Laravel',
                    },
                    {
                        key: 'hero_technologies',
                        label: 'Core stack (hero)',
                        type: 'list',
                        span: 1,
                        help: 'Up to 12 short items.',
                    },
                    {
                        key: 'availability_enabled',
                        label: 'Show availability badge',
                        type: 'switch',
                        span: 1,
                    },
                    {
                        key: 'availability_text',
                        label: 'Availability text',
                        type: 'text',
                        span: 1,
                        showIf: { field: 'availability_enabled', equals: true },
                    },
                ],
            },
            {
                title: 'Contact details',
                fields: [
                    {
                        key: 'email',
                        label: 'Public email',
                        type: 'email',
                        span: 1,
                    },
                    {
                        key: 'location',
                        label: 'Location',
                        type: 'text',
                        span: 1,
                    },
                    { key: 'phone', label: 'Phone', type: 'text', span: 1 },
                    {
                        key: 'show_phone',
                        label: 'Show phone publicly',
                        type: 'switch',
                        span: 1,
                    },
                    {
                        key: 'work_preference',
                        label: 'Work preference',
                        type: 'text',
                        placeholder: 'Open to remote, hybrid and on-site roles',
                    },
                    { key: 'languages', label: 'Languages', type: 'list' },
                ],
            },
            {
                title: 'About content',
                description: 'Markdown. Empty blocks are hidden.',
                fields: [
                    {
                        key: 'summary',
                        label: 'Professional summary',
                        type: 'textarea',
                        rows: 4,
                        help: 'Used on the About page, résumé and structured data.',
                    },
                    {
                        key: 'about_intro',
                        label: 'Introduction',
                        type: 'markdown',
                    },
                    {
                        key: 'philosophy',
                        label: 'Engineering philosophy',
                        type: 'markdown',
                    },
                    {
                        key: 'career_overview',
                        label: 'Career overview',
                        type: 'markdown',
                    },
                    {
                        key: 'specialization',
                        label: 'Technical specialisation',
                        type: 'markdown',
                    },
                    {
                        key: 'interests',
                        label: 'Professional interests',
                        type: 'markdown',
                    },
                    {
                        key: 'collaboration',
                        label: 'Remote / international collaboration',
                        type: 'markdown',
                    },
                ],
            },
        ],
    },

    settings: {
        title: 'Website settings',
        singular: 'settings',
        description:
            'Site-wide options, contact form behaviour and legal content.',
        columns: [],
        groups: [
            {
                title: 'General',
                fields: [
                    {
                        key: 'site_title',
                        label: 'Default page title',
                        type: 'text',
                        required: true,
                        maxLength: 90,
                    },
                    { key: 'footer_note', label: 'Footer note', type: 'text' },
                    {
                        key: 'google_site_verification',
                        label: 'Google Search Console verification code',
                        type: 'text',
                        help: 'Paste only the content value of the meta tag.',
                    },
                ],
            },
            {
                title: 'Contact form',
                fields: [
                    {
                        key: 'contact_enabled',
                        label: 'Accept messages through the contact form',
                        type: 'switch',
                    },
                    {
                        key: 'contact_intro',
                        label: 'Contact intro',
                        type: 'textarea',
                        rows: 2,
                    },
                    {
                        key: 'contact_success_message',
                        label: 'Success message',
                        type: 'text',
                        required: true,
                    },
                    {
                        key: 'contact_notify_enabled',
                        label: 'Email me when a message arrives',
                        type: 'switch',
                        help: 'Requires mail to be configured in .env (MAIL_*).',
                    },
                    {
                        key: 'contact_notify_email',
                        label: 'Notification email',
                        type: 'email',
                        showIf: {
                            field: 'contact_notify_enabled',
                            equals: true,
                        },
                    },
                ],
            },
            {
                title: 'Features',
                fields: [
                    {
                        key: 'projects_show_filters',
                        label: 'Show category filters on the Projects page',
                        type: 'switch',
                    },
                    {
                        key: 'blog_enabled',
                        label: 'Enable the blog (/blog)',
                        type: 'switch',
                    },
                ],
            },
            {
                title: 'Privacy policy',
                fields: [
                    {
                        key: 'privacy_policy',
                        label: 'Privacy policy',
                        type: 'markdown',
                        rows: 14,
                    },
                ],
            },
        ],
    },
};
