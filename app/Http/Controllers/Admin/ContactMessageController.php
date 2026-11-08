<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ContactMessage::class);

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $messages = ContactMessage::query()
            ->when($search !== '', function ($q) use ($search) {
                $term = '%'.addcslashes($search, '%_\\').'%';
                $q->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('subject', 'like', $term));
            })
            ->when($status === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($status === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->latest('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (ContactMessage $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'subject' => $m->subject,
                'preview' => Str::limit($m->message, 90),
                'is_read' => $m->read_at !== null,
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        return Inertia::render('admin/messages/Index', [
            'items' => $messages,
            'query' => ['search' => $search, 'status' => is_string($status) ? $status : ''],
            'unread' => ContactMessage::query()->unread()->count(),
        ]);
    }

    public function show(ContactMessage $message): Response
    {
        $this->authorize('view', $message);

        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return Inertia::render('admin/messages/Show', [
            'message' => [
                ...$message->only(['id', 'name', 'email', 'subject', 'message', 'ip_address', 'user_agent']),
                'created_at' => $message->created_at?->toIso8601String(),
                'read_at' => $message->read_at?->toIso8601String(),
            ],
        ]);
    }

    public function toggleRead(ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $message);

        $message->forceFill(['read_at' => $message->read_at ? null : now()])->save();
        Inertia::flash('toast', ['type' => 'success', 'message' => $message->read_at ? 'Marked as read.' : 'Marked as unread.']);

        return back();
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $this->authorize('delete', $message);

        $message->delete();
        Inertia::flash('toast', ['type' => 'success', 'message' => 'Message deleted.']);

        return to_route('admin.messages.index');
    }
}
