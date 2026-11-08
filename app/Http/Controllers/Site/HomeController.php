<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests\ContactRequest;
use Inertia\Response;

class HomeController extends SiteController
{
    public function __invoke(): Response
    {
        $sections = $this->content->sections();
        $has = fn (string $key) => array_key_exists($key, $sections);

        return $this->page('site/Home', [
            'profile' => $this->content->profile(),
            'sections' => array_values(array_map(fn ($s) => $s, $sections)),
            'experiences' => $has('experience') ? $this->content->experiences() : [],
            'projects' => $has('projects') ? $this->content->featuredProjects() : [],
            'projectCount' => $this->content->publishedProjectCount(),
            'skills' => $has('skills') ? $this->content->skills() : [],
            'principles' => $has('approach') ? $this->content->principles() : [],
            'education' => $has('education') ? $this->content->education() : [],
            'certifications' => $has('education') ? $this->content->certifications() : [],
            'contact' => [
                'intro' => $this->settings->get('contact_intro'),
                'token' => ContactRequest::freshToken(),
            ],
        ], $this->seo->forPage('home', [
            'json_ld' => [$this->seo->personSchema(), $this->seo->websiteSchema()],
            'type' => 'profile',
        ]));
    }
}
