<?php

namespace App\Http\Controllers\Site;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends SiteController
{
    private const MAX_PER_HOUR = 5;

    public function show(): Response
    {
        return $this->page('site/Contact', [
            'profile' => $this->content->profile(),
            'contact' => [
                'intro' => $this->settings->get('contact_intro'),
                'token' => ContactRequest::freshToken(),
            ],
        ], $this->seo->forPage('contact'));
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        abort_unless((bool) $this->settings->get('contact_enabled'), 404);

        $key = 'contact:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_PER_HOUR)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            throw ValidationException::withMessages([
                'message' => "You have sent several messages recently. Please try again in {$minutes} minute(s).",
            ]);
        }

        RateLimiter::hit($key, 3600);

        // Honeypot filled: pretend success so bots learn nothing, store nothing.
        if (filled($request->input('website'))) {
            return $this->sent();
        }

        $message = ContactMessage::query()->create([
            ...$request->safe()->only(['name', 'email', 'subject', 'message']),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 490, ''),
        ]);

        $this->notifyAdmin($message);

        return $this->sent();
    }

    private function sent(): RedirectResponse
    {
        Inertia::flash('toast', ['type' => 'success', 'message' => (string) $this->settings->get('contact_success_message')]);

        return back();
    }

    private function notifyAdmin(ContactMessage $message): void
    {
        $email = $this->settings->get('contact_notify_email');

        if (! $this->settings->get('contact_notify_enabled') || ! is_string($email) || $email === '') {
            return;
        }

        // Sent after the response so a slow or failing mail server never blocks the visitor.
        dispatch(function () use ($email, $message) {
            try {
                Notification::route('mail', $email)->notify(new NewContactMessage($message));
            } catch (Throwable $e) {
                Log::warning('Contact notification failed: '.$e->getMessage(), ['message_id' => $message->id]);
            }
        })->afterResponse();
    }
}
