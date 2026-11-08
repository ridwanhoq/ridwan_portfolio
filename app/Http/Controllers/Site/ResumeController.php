<?php

namespace App\Http\Controllers\Site;

use App\Services\PortfolioContent;
use App\Services\ResumeService;
use App\Services\Seo;
use App\Services\Settings;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResumeController extends SiteController
{
    public function __construct(PortfolioContent $content, Seo $seo, Settings $settings, private readonly ResumeService $resumes)
    {
        parent::__construct($content, $seo, $settings);
    }

    public function show(): Response
    {
        return $this->page('site/Resume', [
            'profile' => $this->content->profile(),
            'experiences' => $this->content->experiences(),
            'skills' => $this->content->skills(),
            'education' => $this->content->education(),
            'certifications' => $this->content->certifications(),
            'projects' => $this->content->featuredProjects(),
        ], $this->seo->forPage('resume', ['json_ld' => [$this->seo->personSchema()]]));
    }

    public function download(): HttpResponse
    {
        return $this->resumes->downloadResponse();
    }
}
