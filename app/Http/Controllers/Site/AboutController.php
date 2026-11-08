<?php

namespace App\Http\Controllers\Site;

use Inertia\Response;

class AboutController extends SiteController
{
    public function __invoke(): Response
    {
        return $this->page('site/About', [
            'profile' => $this->content->profile(),
            'experiences' => $this->content->experiences(),
            'skills' => $this->content->skills(),
            'principles' => $this->content->principles(),
            'education' => $this->content->education(),
            'certifications' => $this->content->certifications(),
        ], $this->seo->forPage('about', [
            'json_ld' => [$this->seo->personSchema(), $this->seo->breadcrumbSchema([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'About', 'url' => route('about')],
            ])],
            'type' => 'profile',
        ]));
    }
}
