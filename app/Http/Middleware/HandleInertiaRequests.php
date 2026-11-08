<?php

namespace App\Http\Middleware;

use App\Models\ContactMessage;
use App\Services\PortfolioContent;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $isAdminArea = $request->is('admin', 'admin/*', 'settings', 'settings/*');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user()?->only(['id', 'name', 'email', 'email_verified_at', 'is_admin', 'created_at', 'updated_at']),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            // Public chrome (navigation, socials, footer). Not needed inside the admin.
            'site' => fn () => $isAdminArea ? null : app(PortfolioContent::class)->site(),
            'admin' => fn () => $isAdminArea && $request->user()?->isAdmin()
                ? ['unread_messages' => ContactMessage::query()->unread()->count()]
                : null,
        ];
    }
}
