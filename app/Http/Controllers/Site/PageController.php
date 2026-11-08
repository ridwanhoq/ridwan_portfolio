<?php

namespace App\Http\Controllers\Site;

use App\Services\Markdown;
use Inertia\Response;

class PageController extends SiteController
{
    public function privacy(): Response
    {
        $body = $this->settings->get('privacy_policy');

        return $this->page('site/Privacy', [
            'html' => Markdown::toHtml(is_string($body) ? $body : null),
        ], $this->seo->forPage('privacy'));
    }
}
