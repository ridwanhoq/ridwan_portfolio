<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileRequest;
use App\Http\Resources\MediaResource;
use App\Models\Profile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function edit(): Response
    {
        $profile = Profile::query()->with('photo')->firstOrNew();
        $this->authorize('update', $profile);

        return Inertia::render('admin/resource/Form', [
            'resource' => 'profile',
            'item' => [
                ...Arr::except($profile->attributesToArray(), ['id', 'created_at', 'updated_at']),
                'hero_technologies' => $profile->hero_technologies ?? [],
                'languages' => $profile->languages ?? [],
                'media' => $profile->photo ? [$profile->photo->id => MediaResource::make($profile->photo)->resolve()] : [],
            ],
            'itemId' => $profile->id,
            'options' => [],
            'abilities' => ['create' => false, 'delete' => false],
            'action' => ['url' => route('admin.profile.update'), 'method' => 'put'],
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $profile = Profile::query()->firstOrNew();
        $this->authorize('update', $profile);

        $profile->fill($request->validated())->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Profile saved.']);

        return to_route('admin.profile.edit');
    }
}
