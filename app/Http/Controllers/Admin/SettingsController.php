<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Notifications\TestNotificationEmail;
use App\Services\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SettingsController extends Controller
{
    private const EDITABLE = [
        'site_title', 'footer_note', 'google_site_verification', 'contact_enabled', 'contact_intro',
        'contact_success_message', 'contact_notify_enabled', 'contact_notify_email', 'blog_enabled',
        'projects_show_filters', 'privacy_policy',
    ];

    public function edit(Settings $settings): Response
    {
        return Inertia::render('admin/resource/Form', [
            'resource' => 'settings',
            'item' => Arr::only($settings->all(), self::EDITABLE),
            'itemId' => null,
            'options' => [],
            'abilities' => ['create' => false, 'delete' => false],
            'action' => ['url' => route('admin.settings.update'), 'method' => 'put'],
            'extraActions' => [
                ['label' => 'Send test email', 'url' => route('admin.settings.test-email'), 'method' => 'post'],
            ],
        ]);
    }

    public function update(SettingsRequest $request, Settings $settings): RedirectResponse
    {
        $settings->set(Arr::only($request->validated(), self::EDITABLE));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Settings saved.']);

        return to_route('admin.settings.edit');
    }

    /**
     * Sends a test message to the notification address so delivery can be confirmed.
     */
    public function testEmail(Settings $settings): RedirectResponse
    {
        $email = $settings->get('contact_notify_email');

        if (! is_string($email) || $email === '') {
            Inertia::flash('toast', ['type' => 'warning', 'message' => 'Add a notification email and save first.']);

            return back();
        }

        try {
            Notification::route('mail', $email)->notifyNow(new TestNotificationEmail);
        } catch (Throwable $e) {
            Log::warning('Test email failed: '.$e->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Sending failed: '.$e->getMessage()]);

            return back();
        }

        $mailer = (string) config('mail.default');

        Inertia::flash('toast', in_array($mailer, ['log', 'array'], true)
            ? ['type' => 'warning', 'message' => "MAIL_MAILER is “{$mailer}”, so the test email was written to the log instead of being sent. Configure SMTP in .env."]
            : ['type' => 'success', 'message' => "Test email sent to {$email}."]);

        return back();
    }
}
