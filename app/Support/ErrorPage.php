<?php

namespace App\Support;

use App\Services\PortfolioContent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Renders branded error pages through Inertia.
 */
class ErrorPage
{
    private const MESSAGES = [
        403 => ['Access denied', 'You do not have permission to view this page.'],
        404 => ['Page not found', 'The page you are looking for does not exist or has moved.'],
        429 => ['Too many requests', 'Please slow down and try again in a minute.'],
        500 => ['Something went wrong', 'An unexpected error occurred. It has been logged and will be looked at.'],
        503 => ['Back soon', 'The site is undergoing maintenance. Please check back shortly.'],
    ];

    public static function render(Response $response, Request $request): Response
    {
        $status = $response->getStatusCode();

        if ($status === 419) {
            Inertia::flash('toast', ['type' => 'warning', 'message' => 'The page expired. Please try again.']);

            return back();
        }

        $isServerError = in_array($status, [500, 503], true);
        $handled = array_key_exists($status, self::MESSAGES);

        if (! $handled || $request->expectsJson() || ($isServerError && config('app.debug'))) {
            return $response;
        }

        [$title, $message] = self::MESSAGES[$status];

        try {
            $site = app(PortfolioContent::class)->site();
        } catch (Throwable) {
            $site = null; // Database may be unavailable during a 500/503.
        }

        $seo = [
            'title' => $site ? "{$title} — {$site['name']}" : $title,
            'description' => $message,
            'canonical' => $request->url(),
            'image' => asset('images/og-default.png'),
            'type' => 'website',
            'site_name' => $site['name'] ?? config('app.name'),
            'robots' => 'noindex, nofollow',
            'json_ld' => [],
            'google_site_verification' => null,
        ];

        return Inertia::render('site/Error', [
            'status' => $status,
            'title' => $title,
            'message' => $message,
            'site' => $site,
            'seo' => array_diff_key($seo, array_flip(['json_ld', 'google_site_verification'])),
        ])->withViewData(['seo' => $seo])->toResponse($request)->setStatusCode($status);
    }
}
