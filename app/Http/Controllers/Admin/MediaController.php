<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaUpdateRequest;
use App\Http\Requests\Admin\MediaUploadRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class MediaController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly MediaService $media) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Media::class);

        return Inertia::render('admin/Media', [
            'items' => $this->query($request, 24),
            'query' => ['search' => (string) $request->query('search', '')],
            'limits' => ['max_kb' => MediaService::MAX_KILOBYTES, 'types' => MediaService::ALLOWED_MIME_TYPES],
        ]);
    }

    /**
     * JSON listing used by the media picker dialog.
     */
    public function browse(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Media::class);

        return response()->json($this->query($request, 18));
    }

    /**
     * Upload a single image. Responds with JSON for the picker, or redirects for the library page.
     */
    public function store(MediaUploadRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Media::class);

        try {
            $media = $this->media->store($request->file('file'), $request->validated('alt'), $request->user()?->id);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['file' => $e->getMessage()]);
        }

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json($media, 201);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Image uploaded.']);

        return back();
    }

    public function update(MediaUpdateRequest $request, Media $media): RedirectResponse
    {
        $this->authorize('update', $media);

        $media->update($request->validated());
        Inertia::flash('toast', ['type' => 'success', 'message' => 'Image details saved.']);

        return back();
    }

    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        $this->media->delete($media);
        Inertia::flash('toast', ['type' => 'success', 'message' => 'Image deleted.']);

        return back();
    }

    /** @return LengthAwarePaginator<int, Media> */
    private function query(Request $request, int $perPage): LengthAwarePaginator
    {
        $search = trim((string) $request->query('search', ''));

        return Media::query()
            ->when($search !== '', function ($q) use ($search) {
                $term = '%'.addcslashes($search, '%_\\').'%';
                $q->where(fn ($q) => $q->where('original_name', 'like', $term)->orWhere('alt', 'like', $term));
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
