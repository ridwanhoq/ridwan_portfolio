<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Markdown;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Renders Markdown with the same sanitiser used on the public site.
 */
class MarkdownPreviewController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate(['markdown' => ['nullable', 'string', 'max:100000']]);

        return response()->json(['html' => Markdown::toHtml($data['markdown'] ?? null) ?? '']);
    }
}
