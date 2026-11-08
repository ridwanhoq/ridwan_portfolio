<?php

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use App\Notifications\TestNotificationEmail;
use App\Services\Settings;
use App\Support\ContentCache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    seedPortfolio();
    RateLimiter::clear('contact:127.0.0.1');
});

function contactPayload(array $overrides = []): array
{
    return [
        'name' => 'Jane Recruiter',
        'email' => 'jane@example.com',
        'subject' => 'Senior Laravel role',
        'message' => 'We would like to talk to you about a remote position.',
        'website' => '',
        'token' => Crypt::encryptString((string) (time() - 30)),
        ...$overrides,
    ];
}

it('stores a valid message', function () {
    $this->from('/contact')->post('/contact', contactPayload())
        ->assertRedirect('/contact')
        ->assertSessionHasNoErrors();

    $message = ContactMessage::query()->sole();
    expect($message->email)->toBe('jane@example.com')
        ->and($message->read_at)->toBeNull()
        ->and($message->ip_address)->toBe('127.0.0.1');
});

it('validates input on the server', function () {
    $this->post('/contact', contactPayload(['name' => '', 'email' => 'not-an-email', 'subject' => '', 'message' => 'short']))
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

    expect(ContactMessage::query()->count())->toBe(0);
});

it('silently drops submissions that fill the honeypot', function () {
    $this->post('/contact', contactPayload(['website' => 'http://spam.example']))
        ->assertSessionHasErrors('website');

    expect(ContactMessage::query()->count())->toBe(0);
});

it('rejects forms submitted faster than a person could type', function () {
    $this->post('/contact', contactPayload(['token' => ContactRequest::freshToken()]))
        ->assertSessionHasErrors('token');

    expect(ContactMessage::query()->count())->toBe(0);
});

it('rejects a tampered token', function () {
    $this->post('/contact', contactPayload(['token' => 'forged']))->assertSessionHasErrors('token');
});

it('rate limits repeated submissions', function () {
    foreach (range(1, 5) as $i) {
        $this->post('/contact', contactPayload(['subject' => "Message {$i}"]))->assertSessionHasNoErrors();
    }

    $this->post('/contact', contactPayload())->assertSessionHasErrors('message');

    expect(ContactMessage::query()->count())->toBe(5);
});

it('emails the administrator when notifications are enabled', function () {
    Notification::fake();
    app(Settings::class)->set(['contact_notify_enabled' => true, 'contact_notify_email' => 'owner@example.com']);

    $this->post('/contact', contactPayload())->assertSessionHasNoErrors();

    Notification::assertSentOnDemand(NewContactMessage::class, fn ($n, $channels, $notifiable) => $notifiable->routes['mail'] === 'owner@example.com');
});

it('does not email anyone when notifications are disabled', function () {
    Notification::fake();

    $this->post('/contact', contactPayload());

    Notification::assertNothingSent();
});

it('refuses messages when the contact form is disabled', function () {
    app(Settings::class)->set(['contact_enabled' => false]);

    $this->post('/contact', contactPayload())->assertNotFound();
});

it('emails the address from CONTACT_NOTIFY_EMAIL by default', function () {
    Notification::fake();
    config(['portfolio.contact_notify_email' => 'owner@example.com']);
    ContentCache::flush();
    app()->forgetInstance(Settings::class);

    $this->post('/contact', contactPayload())->assertSessionHasNoErrors();

    Notification::assertSentOnDemand(NewContactMessage::class, fn ($n, $channels, $notifiable) => $notifiable->routes['mail'] === 'owner@example.com');
});

it('lets the admin send a test email to the notification address', function () {
    Notification::fake();
    app(Settings::class)->set(['contact_notify_email' => 'owner@example.com']);

    $this->actingAs(admin())->post('/admin/settings/test-email')->assertRedirect();

    Notification::assertSentOnDemand(TestNotificationEmail::class, fn ($n, $channels, $notifiable) => $notifiable->routes['mail'] === 'owner@example.com');
});

it('warns instead of sending a test email when no address is set', function () {
    Notification::fake();
    app(Settings::class)->set(['contact_notify_email' => null]);

    $this->actingAs(admin())->post('/admin/settings/test-email')->assertRedirect();

    Notification::assertNothingSent();
});
