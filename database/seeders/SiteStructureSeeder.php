<?php

namespace Database\Seeders;

use App\Models\NavigationItem;
use App\Models\PageSection;
use App\Models\SeoSetting;
use App\Services\Settings;
use Illuminate\Database\Seeder;

/**
 * Structural records: homepage sections, navigation, SEO pages and settings.
 */
class SiteStructureSeeder extends Seeder
{
    public function run(Settings $settings): void
    {
        $sections = [
            ['key' => 'about', 'label' => 'About', 'eyebrow' => 'About', 'heading' => 'Engineering software that businesses can rely on', 'intro' => null],
            ['key' => 'experience', 'label' => 'Experience', 'eyebrow' => 'Experience', 'heading' => 'Where I have worked', 'intro' => 'Nine years of building and maintaining production web applications, most recently as a Senior Software Engineer.'],
            ['key' => 'projects', 'label' => 'Featured projects', 'eyebrow' => 'Selected work', 'heading' => 'Featured projects', 'intro' => 'A selection of platforms I have helped build, from national e-learning systems to corporate websites with custom CMS.'],
            ['key' => 'skills', 'label' => 'Technical skills', 'eyebrow' => 'Skills', 'heading' => 'Technical toolkit', 'intro' => 'The languages, frameworks and practices I use day to day.'],
            ['key' => 'approach', 'label' => 'Engineering approach', 'eyebrow' => 'Approach', 'heading' => 'How I build software', 'intro' => null],
            ['key' => 'education', 'label' => 'Education & certifications', 'eyebrow' => 'Education', 'heading' => 'Education and training', 'intro' => null],
            ['key' => 'contact', 'label' => 'Contact', 'eyebrow' => 'Contact', 'heading' => 'Let’s build something reliable together', 'intro' => null],
        ];

        foreach ($sections as $i => $section) {
            PageSection::query()->updateOrCreate(['key' => $section['key']], [...$section, 'sort_order' => ($i + 1) * 10, 'is_enabled' => true]);
        }

        $navigation = [
            ['label' => 'Home', 'url' => '/', 'location' => 'header'],
            ['label' => 'About', 'url' => '/#about', 'location' => 'header'],
            ['label' => 'Experience', 'url' => '/#experience', 'location' => 'header'],
            ['label' => 'Projects', 'url' => '/#projects', 'location' => 'header'],
            ['label' => 'Skills', 'url' => '/#skills', 'location' => 'header'],
            ['label' => 'Contact', 'url' => '/#contact', 'location' => 'header'],
            ['label' => 'About', 'url' => '/about', 'location' => 'footer'],
            ['label' => 'Projects', 'url' => '/projects', 'location' => 'footer'],
            ['label' => 'Résumé', 'url' => '/resume', 'location' => 'footer'],
            ['label' => 'Contact', 'url' => '/contact', 'location' => 'footer'],
            ['label' => 'Privacy', 'url' => '/privacy', 'location' => 'footer'],
        ];

        if (NavigationItem::query()->doesntExist()) {
            foreach ($navigation as $i => $item) {
                NavigationItem::query()->create([...$item, 'sort_order' => ($i + 1) * 10, 'is_visible' => true]);
            }
        }

        $seo = [
            'home' => ['Home', 'Mohammad Ridwanul Haque — Senior Software Engineer (PHP & Laravel)', 'Senior Software Engineer with 9+ years of web development experience, specialising in PHP, Laravel, REST APIs and MySQL. Open to remote and international roles.'],
            'about' => ['About', 'About', 'Background, engineering philosophy and career overview of Mohammad Ridwanul Haque, a Senior PHP and Laravel developer based in Dhaka, Bangladesh.'],
            'projects' => ['Projects', 'Projects and case studies', 'Case studies of Laravel and PHP platforms: e-learning, real-time monitoring, corporate websites and custom CMS development.'],
            'resume' => ['Résumé', 'Résumé', 'Résumé of Mohammad Ridwanul Haque, Senior Software Engineer: experience, skills, education and certifications. Downloadable as PDF.'],
            'contact' => ['Contact', 'Contact', 'Get in touch about Senior Laravel Developer, Backend Engineer or remote contract opportunities.'],
            'privacy' => ['Privacy policy', 'Privacy policy', 'How this website handles personal data submitted through the contact form.'],
            'blog' => ['Blog', 'Writing', 'Articles on PHP, Laravel, API design and software engineering practice.'],
        ];

        foreach ($seo as $key => [$label, $title, $description]) {
            SeoSetting::query()->firstOrCreate(['page_key' => $key], ['label' => $label, 'title' => $title, 'description' => $description]);
        }

        $settings->set([
            'privacy_policy' => <<<'MD'
## What this site collects

When you send a message through the contact form, the following is stored so that I can reply:

- your name, email address, subject and message
- the IP address and browser user agent of the request, to help prevent spam and abuse

This information is only used to respond to your enquiry. It is not sold, shared with third parties or used for marketing.

## Cookies

This site uses only essential cookies:

- a session cookie and a security (CSRF) token, required for forms to work
- an `appearance` cookie that remembers your light or dark theme choice

No analytics, advertising or tracking cookies are used.

## Retention and your rights

Messages are kept for as long as needed to handle the conversation and are then deleted. You can ask for your message to be deleted at any time using the contact form.
MD,
        ]);
    }
}
