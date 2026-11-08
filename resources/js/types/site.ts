export type MediaItem = {
    id: number;
    url: string;
    webp_url: string | null;
    thumb_url: string;
    width: number | null;
    height: number | null;
    alt: string | null;
};

export type SocialLinkItem = {
    platform: string;
    label: string;
    url: string;
    show_in_hero: boolean;
};

export type NavLink = { label: string; url: string; new_tab: boolean };

export type Site = {
    name: string;
    short_name: string;
    designation: string | null;
    email: string | null;
    location: string | null;
    availability: { enabled: boolean; text: string | null };
    socials: SocialLinkItem[];
    nav: { header: NavLink[]; footer: NavLink[] };
    footer_note: string | null;
    blog_enabled: boolean;
    contact_enabled: boolean;
};

export type Seo = {
    title: string;
    description: string | null;
    canonical: string;
    image: string;
    type: string;
    site_name: string;
    robots: string;
};

export type Profile = {
    full_name: string;
    designation: string;
    tagline: string | null;
    hero_title: string;
    hero_subtitle: string | null;
    summary: string | null;
    location: string | null;
    email: string | null;
    phone: string | null;
    work_preference: string | null;
    years_experience: number | null;
    years_primary_stack: number | null;
    primary_stack_label: string | null;
    hero_technologies: string[];
    languages: string[];
    photo: MediaItem | null;
    about: Partial<
        Record<
            | 'intro'
            | 'philosophy'
            | 'career_overview'
            | 'specialization'
            | 'interests'
            | 'collaboration',
            string
        >
    >;
};

export type Section = {
    key: string;
    eyebrow: string | null;
    heading: string | null;
    intro: string | null;
};

export type ExperienceItem = {
    id: number;
    company: string;
    company_url: string | null;
    title: string;
    employment_type: string | null;
    location: string | null;
    focus: string | null;
    is_current: boolean;
    start: string;
    end: string | null;
    period: string;
    description_html: string | null;
    responsibilities: string[];
    contributions: string[];
    technologies: string[];
};

export type ProjectCard = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    client: string | null;
    project_type: string | null;
    period: string | null;
    contribution: string | null;
    is_featured: boolean;
    live_url: string | null;
    repo_url: string | null;
    category: { name: string; slug: string } | null;
    technologies: string[];
    thumbnail: MediaItem | null;
};

export type ProjectDetail = ProjectCard & {
    sections: { key: string; title: string; html: string }[];
    images: {
        id: number;
        kind: 'screenshot' | 'diagram';
        caption: string | null;
        media: MediaItem;
    }[];
    links: { label: string; url: string }[];
    updated_at: string;
};

export type SkillGroup = {
    name: string;
    description: string | null;
    skills: string[];
};

export type PrincipleItem = {
    title: string;
    icon: string | null;
    html: string;
};

export type EducationItem = {
    id: number;
    institution: string;
    degree: string;
    subject: string | null;
    start_year: number | null;
    graduation_year: number | null;
    grade: string | null;
    description: string | null;
};

export type CertificationItem = {
    id: number;
    name: string;
    issuer: string | null;
    type: string;
    issued: string | null;
    credential_id: string | null;
    credential_url: string | null;
    description: string | null;
};

export type PostItem = {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    published_at: string | null;
    published_label: string | null;
    reading_minutes: number;
    cover: MediaItem | null;
    body_html?: string | null;
};

export type ContactConfig = { intro: string | null; token: string };
