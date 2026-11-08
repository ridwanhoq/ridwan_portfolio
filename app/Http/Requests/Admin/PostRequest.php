<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use Illuminate\Validation\Rule;

class PostRequest extends AdminRequest
{
    protected function prepareForValidation(): void
    {
        $this->slugFrom('title');
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required_with:title', 'string', 'max:255', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($this->routeId())],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:100000'],
            'cover_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'status' => ['required', Rule::in(array_keys(Post::STATUSES))],
            'published_at' => ['nullable', 'date'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ];
    }
}
