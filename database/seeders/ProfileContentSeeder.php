<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Principle;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Portfolio content taken from the owner's CV.
 *
 * Nothing here is invented: no metrics, clients or outcomes beyond what the
 * CV states. Unknown values are left empty, or unpublished with an obvious
 * PLACEHOLDER marker, so they never appear on the public site.
 */
class ProfileContentSeeder extends Seeder
{
    /** @var array<string, int> */
    private array $tech = [];

    public function run(): void
    {
        if (Profile::query()->exists()) {
            $this->command?->info('Portfolio content already seeded. Skipped.');

            return;
        }

        $this->profile();
        $this->socialLinks();
        $this->technologies();
        $this->experiences();
        $this->projects();
        $this->skills();
        $this->principles();
        $this->education();
    }

    private function profile(): void
    {
        Profile::query()->create([
            'full_name' => 'Mohammad Ridwanul Haque',
            'short_name' => 'Ridwanul Haque',
            'designation' => 'Senior Software Engineer',
            'tagline' => 'PHP & Laravel Developer',
            'hero_title' => 'Building Reliable Software. Solving Complex Problems.',
            'hero_subtitle' => 'Senior Software Engineer with 9+ years of web development experience, specializing in PHP, Laravel, REST APIs, MySQL, and modern web technologies. Focused on building secure, scalable, and maintainable applications that solve real-world business problems.',
            'summary' => 'Senior Software Engineer with 9+ years of experience in web development and 5+ years of professional PHP and Laravel development. Builds secure, maintainable REST APIs and MVC applications backed by MySQL, with hands-on experience in authentication and middleware, database schema design, query optimization, background queues, automated testing (Pest, Jest) and CI/CD deployment. Works independently, reviews code, and delivers on deadlines.',
            'location' => 'Dhaka, Bangladesh',
            'email' => null, // Add a public contact address in Admin → Profile.
            'phone' => null,
            'show_phone' => false,
            'work_preference' => 'Open to remote, hybrid and on-site roles',
            'years_experience' => 9,
            'years_primary_stack' => 5,
            'primary_stack_label' => 'PHP & Laravel',
            'hero_technologies' => ['PHP 8', 'Laravel', 'MySQL', 'REST APIs', 'Vue.js 3', 'Pest'],
            'availability_enabled' => true,
            'availability_text' => 'Open to new opportunities',
            'about_intro' => "I am a Senior Software Engineer based in Dhaka, Bangladesh, with more than nine years of web development experience and over five years of professional PHP and Laravel work.\n\nI build REST APIs and MVC applications backed by MySQL: the kind of systems that run training programmes, monitor field operations and power content-managed websites.",
            'philosophy' => "Good software is boring in production. I aim for code that is easy to read, covered by meaningful tests and structured so the next developer can change it with confidence.\n\nI prefer simple, well-understood tools over clever abstractions, and I treat security and performance as part of the design rather than something added at the end.",
            'career_overview' => "I started out in 2014 delivering front-end and back-end features for client websites, then moved into PHP and MySQL application development at Southtech Limited and Smart Software Ltd.\n\nSince 2022 I have worked as a Senior Software Engineer at Riseup Labs, building Laravel applications and APIs, reviewing code and supporting CI/CD deployments, alongside NestJS and Next.js on full-stack projects.",
            'specialization' => "- Laravel and PHP application architecture (MVC, modular, reusable code)\n- REST API design, authentication and middleware\n- MySQL schema design and query optimization\n- Background queues and jobs for long-running work\n- Automated testing with Pest and Jest",
            'interests' => 'I am particularly interested in booking and transaction-based platforms, where data integrity, clear domain modelling and reliable background processing matter most.',
            'collaboration' => 'I am open to remote, hybrid and on-site roles, and comfortable working with international teams across time zones. I work independently, communicate progress clearly and deliver on agreed deadlines.',
            'languages' => ['English (professional working proficiency)', 'Bangla (native)'],
        ]);
    }

    private function socialLinks(): void
    {
        // Real URLs were not provided. These stay unpublished until edited in Admin → Social links.
        SocialLink::query()->create(['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/in/PLACEHOLDER', 'show_in_hero' => true, 'is_published' => false, 'sort_order' => 10]);
        SocialLink::query()->create(['platform' => 'github', 'label' => 'GitHub', 'url' => 'https://github.com/PLACEHOLDER', 'show_in_hero' => true, 'is_published' => false, 'sort_order' => 20]);
    }

    private function technologies(): void
    {
        $names = [
            'PHP', 'Laravel', 'MySQL', 'PostgreSQL', 'Redis', 'REST APIs', 'Vue.js', 'JavaScript', 'Next.js',
            'NestJS', 'Node.js', 'Express.js', 'Prisma', 'jQuery', 'Bootstrap', 'Tailwind CSS', 'HTML5', 'CSS3',
            'Flutter', 'Google Maps API', 'YouTube API', 'Pest', 'Jest', 'Git', 'GitHub', 'CI/CD', 'Linux', 'Stripe',
        ];

        foreach ($names as $i => $name) {
            $this->tech[$name] = Technology::query()->create(['name' => $name, 'slug' => Str::slug($name), 'sort_order' => $i * 10])->id;
        }
    }

    /**
     * @param  list<string>  $names
     * @return list<int>
     */
    private function techIds(array $names): array
    {
        return array_map(fn ($n) => $this->tech[$n], $names);
    }

    private function experiences(): void
    {
        $jobs = [
            [
                'company' => 'Riseup Labs', 'title' => 'Senior Software Engineer', 'focus' => 'Laravel / PHP backend development',
                'location' => 'Dhaka, Bangladesh', 'start_date' => '2022-03-01', 'end_date' => null, 'is_current' => true,
                'responsibilities' => [
                    'Develop and maintain Laravel applications using MVC architecture, reusable code and modular design for maintainable, scalable systems.',
                    'Design and build REST APIs, including authentication and middleware, and integrate them with front-end and third-party services.',
                    'Design MySQL database schemas and optimize queries to improve application performance.',
                    'Implement background queues and jobs for asynchronous processing of long-running tasks.',
                    'Write automated tests with Pest and Jest to protect code quality and reduce regressions.',
                    'Perform code reviews and support CI/CD deployments, following secure and maintainable coding practices.',
                    'Work with NestJS and Next.js alongside Laravel across full-stack projects.',
                ],
                'tech' => ['PHP', 'Laravel', 'MySQL', 'REST APIs', 'NestJS', 'Next.js', 'Pest', 'Jest', 'Git', 'CI/CD'],
            ],
            [
                'company' => 'Smart Software Ltd.', 'title' => 'Programmer', 'focus' => 'Laravel / MySQL development',
                'location' => 'Dhaka, Bangladesh', 'start_date' => '2019-06-01', 'end_date' => '2020-10-01', 'is_current' => false,
                'responsibilities' => [
                    'Constructed and maintained web applications with the Laravel framework, PHP OOP and MySQL.',
                    'Designed relational database structures and wrote efficient queries for application features.',
                    'Tested and debugged applications to resolve defects and keep releases stable.',
                ],
                'tech' => ['PHP', 'Laravel', 'MySQL'],
            ],
            [
                'company' => 'Southtech Limited', 'title' => 'Analyst Programmer', 'focus' => 'PHP / MySQL development',
                'location' => 'Dhaka, Bangladesh', 'start_date' => '2017-05-01', 'end_date' => '2018-06-01', 'is_current' => false,
                'responsibilities' => [
                    'Developed web-based applications in PHP with MySQL databases, using reusable classes and libraries.',
                    'Designed program architecture, then tested and debugged new features before release.',
                ],
                'tech' => ['PHP', 'MySQL'],
            ],
            [
                'company' => 'Moon Networks Ltd.', 'title' => 'Programmer', 'focus' => 'Web and database development',
                'location' => 'Dhaka, Bangladesh', 'start_date' => '2014-03-01', 'end_date' => '2016-09-01', 'is_current' => false,
                'responsibilities' => [
                    'Delivered front-end and back-end features for client websites, adding new functionality and maintaining existing sites.',
                    'Programmed MySQL databases, identified and fixed bugs, and tested sites before delivery.',
                    'Documented code and technical requirements, and supported teammates in solving technical problems.',
                ],
                'tech' => ['MySQL'],
            ],
        ];

        foreach ($jobs as $i => $job) {
            $experience = Experience::query()->create([
                ...collect($job)->except('tech')->all(),
                'employment_type' => 'full_time',
                'contributions' => [],
                'is_published' => true,
                'sort_order' => $i * 10,
            ]);

            $experience->technologies()->sync($this->techIds($job['tech']));
        }
    }

    private function projects(): void
    {
        $apps = ProjectCategory::query()->create(['name' => 'Web applications', 'slug' => 'web-applications', 'sort_order' => 10]);
        $sites = ProjectCategory::query()->create(['name' => 'Websites & CMS', 'slug' => 'websites-cms', 'sort_order' => 20]);

        $projects = [
            [
                'category' => $apps,
                'title' => 'Integrated Training and Learning Management System (ITLMS)',
                'slug' => 'itlms-training-and-learning-management-system',
                'client' => 'UNICEF, NAEM, Ministry of Education',
                'project_type' => 'Web and mobile learning management platform',
                'summary' => 'An end-to-end training and e-learning platform that replaced manual training administration for a national education academy.',
                'contribution' => 'Developed the platform with Laravel and PHP, including course management, registration, progress tracking, certification and reporting modules.',
                'overview' => 'An end-to-end training and e-learning platform built with Laravel and PHP, with a companion Flutter mobile app for learning access.',
                'business_context' => 'Training administration for a national education academy was handled manually. The platform was built for UNICEF, NAEM and the Ministry of Education to replace that manual process.',
                'role_details' => "- Developed the end-to-end training and e-learning platform with Laravel and PHP.\n- Built course management, online registration with government ID-based login, progress tracking, certification management and real-time reporting modules.\n- Implemented trainee and instructor profiles, communication tools (messaging, forums, document sharing), and automated notifications and waitlists.\n- Integrated the platform with government databases and education systems, and delivered a Flutter mobile app for learning access.",
                'tech' => ['Laravel', 'PHP', 'MySQL', 'Bootstrap', 'HTML5', 'CSS3', 'Flutter'],
            ],
            [
                'category' => $apps,
                'title' => 'Out of School Children in Bangladesh (OSCBD) — Real-Time Monitoring',
                'slug' => 'oscbd-real-time-monitoring-system',
                'client' => 'UNICEF Bangladesh, “Let Us Learn” project',
                'project_type' => 'Real-time monitoring system (Android and web)',
                'summary' => 'A real-time monitoring system that tracks individual children’s learning progress for a UNICEF education programme serving out-of-school children.',
                'contribution' => 'Developed role-based features, the admin dashboard with Google Maps integration, and analysis and export tools.',
                'overview' => 'A real-time monitoring (RTM) system, available on Android and the web, that tracks individual children’s learning progress.',
                'business_context' => 'Part of UNICEF Bangladesh’s “Let Us Learn” project, an education programme serving out-of-school children.',
                'role_details' => "- Developed the real-time monitoring system to track individual children’s learning progress.\n- Built role-based features for admins, teachers and field staff, including student and attendance management, assessment tracking and center visit tracking.\n- Implemented an admin dashboard with Google Maps integration, result analysis and comparison tools, and data export for reporting.\n- Supported the project through multiple User Acceptance Testing (UAT) rounds with the client.",
                'testing_deployment' => 'The system went through multiple User Acceptance Testing (UAT) rounds with the client.',
                'tech' => ['Laravel', 'PHP', 'MySQL', 'Redis', 'JavaScript', 'Bootstrap', 'Google Maps API'],
            ],
            [
                'category' => $sites,
                'title' => 'Sirajganj Economic Zone Limited (SEZL) Corporate Website',
                'slug' => 'sezl-corporate-website-and-cms',
                'client' => 'Sirajganj Economic Zone Limited',
                'project_type' => 'Corporate website with custom CMS',
                'summary' => 'A responsive, investor-focused corporate website built with Next.js and backed by a Laravel API, with a custom admin CMS and career portal.',
                'contribution' => 'Built the Next.js front end, the Laravel API and MySQL database, and the custom admin CMS.',
                'overview' => 'A responsive investor-focused corporate website with Next.js on the front end, backed by a Laravel API and MySQL database.',
                'role_details' => "- Built the responsive investor-focused website with Next.js, backed by a Laravel API and MySQL database.\n- Developed a custom admin CMS to manage pages, media library, blog and news, job listings, applications and form submissions without developer intervention.\n- Implemented an integrated career portal with job posting and application tracking, plus one-click social sharing for news.",
                'architecture' => 'A decoupled setup: a Next.js front end consuming a Laravel API, with MySQL as the data store. Content is managed through a custom admin CMS so the client can update pages, media, news and jobs without developer involvement.',
                'tech' => ['Next.js', 'Laravel', 'PHP', 'MySQL', 'REST APIs'],
            ],
            [
                'category' => $sites,
                'title' => 'HITSqwad Interactive Gaming Website',
                'slug' => 'hitsqwad-b2b-game-showcase-platform',
                'client' => 'HITSqwad',
                'project_type' => 'B2B game showcase platform with admin panel',
                'summary' => 'A media-rich B2B game showcase website with structured game data, video previews, downloadable game sheets and geo-compliance features.',
                'contribution' => 'Developed the showcase site and admin panel, including partner API integration for restricted territories and GDPR-compliant legal pages.',
                'overview' => 'A media-rich B2B game showcase website with structured game data, YouTube preview integration and downloadable PDF game sheets.',
                'role_details' => "- Developed the media-rich game showcase website with structured game data, YouTube preview integration and downloadable PDF game sheets.\n- Built an admin panel with full CRUD for the game library, partner management, WYSIWYG content editing, and certification and badge management.\n- Integrated partner API-driven restricted territories lists for geo-compliance, plus LeadFeeder analytics for B2B lead tracking.\n- Delivered GDPR-compliant, fully editable legal pages (privacy, terms, cookies).",
                'security' => 'Geo-compliance is handled through restricted territories lists supplied by a partner API, and the legal pages (privacy, terms, cookies) were delivered as GDPR-compliant, fully editable content.',
                'tech' => ['Laravel', 'PHP', 'MySQL', 'Tailwind CSS', 'HTML5', 'YouTube API'],
            ],
        ];

        foreach ($projects as $i => $data) {
            $project = Project::query()->create([
                ...collect($data)->except(['category', 'tech'])->all(),
                'project_category_id' => $data['category']->id,
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => $i * 10,
            ]);

            $project->technologies()->sync($this->techIds($data['tech']));
        }
    }

    private function skills(): void
    {
        $groups = [
            'Backend' => ['PHP 8+', 'Laravel', 'PHP OOP', 'MVC architecture', 'REST API development', 'Authentication & middleware', 'NestJS', 'Node.js', 'Express.js'],
            'Frontend' => ['Vue.js 3', 'JavaScript', 'Next.js', 'jQuery', 'HTML5', 'CSS3'],
            'Database' => ['MySQL', 'PostgreSQL', 'Relational database design', 'Schema design', 'Query optimization', 'Prisma'],
            'Architecture' => ['SOLID principles', 'Modular architecture', 'Authentication', 'Authorization', 'Clean, reusable code'],
            'Performance' => ['Caching', 'Laravel queues', 'Background jobs', 'Database optimization'],
            'Integrations' => ['Third-party API integration', 'Consuming REST APIs', 'Payment gateways (Stripe)'],
            'Testing' => ['Pest', 'PHPUnit', 'Jest', 'Feature tests', 'Unit tests'],
            'DevOps' => ['Git', 'GitHub', 'CI/CD', 'Linux', 'Deployment', 'Code review'],
        ];

        $order = 0;

        foreach ($groups as $name => $skills) {
            $category = SkillCategory::query()->create(['name' => $name, 'slug' => Str::slug($name), 'sort_order' => $order += 10]);

            foreach ($skills as $i => $skill) {
                $category->skills()->create(['name' => $skill, 'sort_order' => $i * 10]);
            }
        }
    }

    private function principles(): void
    {
        $blocks = [
            ['Clean Architecture', 'layers', 'Thin controllers, validation in Form Requests, and business logic in focused services. I keep modules small and reusable so features can change without ripple effects, and add abstractions only when they earn their place.'],
            ['Security First', 'shield-check', 'Authentication, authorization and input validation are designed in from the start. I rely on framework protections against SQL injection, XSS and CSRF, guard against mass assignment, and keep secrets out of the codebase.'],
            ['Performance Optimization', 'gauge', 'I design schemas and indexes around real query patterns, eliminate N+1 queries, cache where it is safe to do so, and move long-running work to background queues so users never wait on it.'],
            ['Quality Engineering', 'flask-conical', 'Automated tests with Pest and Jest protect behaviour and reduce regressions. Code review and CI/CD pipelines keep every change reviewable, repeatable and safe to deploy.'],
        ];

        foreach ($blocks as $i => [$title, $icon, $body]) {
            Principle::query()->create(['title' => $title, 'icon' => $icon, 'body' => $body, 'sort_order' => ($i + 1) * 10]);
        }
    }

    private function education(): void
    {
        Education::query()->create(['institution' => 'Jahangirnagar University', 'degree' => 'M.Sc.', 'subject' => 'Computer Science & Engineering', 'graduation_year' => 2015, 'grade' => 'CGPA 3.35 / 4.00', 'sort_order' => 10]);
        Education::query()->create(['institution' => 'Darul Ihsan University', 'degree' => 'B.Sc.', 'subject' => 'Information Technology & Engineering (Telecommunication)', 'graduation_year' => 2012, 'grade' => 'CGPA 3.61 / 4.00', 'sort_order' => 20]);

        Certification::query()->create(['name' => 'Linux Training', 'issuer' => 'New Horizons, Dhaka', 'type' => 'training', 'issued_on' => '2011-01-01', 'description' => '3-month programme', 'sort_order' => 10]);
    }
}
