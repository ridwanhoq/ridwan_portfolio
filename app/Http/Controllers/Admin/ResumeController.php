<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResumeUploadRequest;
use App\Services\ResumeService;
use App\Services\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ResumeController extends Controller
{
    public function __construct(private readonly ResumeService $resumes, private readonly Settings $settings) {}

    public function edit(): Response
    {
        return Inertia::render('admin/Resume', [
            'source' => $this->settings->get('resume_source'),
            'upload' => $this->resumes->uploadedFile(),
        ]);
    }

    public function upload(ResumeUploadRequest $request): RedirectResponse
    {
        $this->resumes->storeUpload($request->file('file'));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Résumé uploaded and set as the download.']);

        return to_route('admin.resume.edit');
    }

    public function source(Request $request): RedirectResponse
    {
        $data = $request->validate(['source' => ['required', Rule::in(['upload', 'generated'])]]);

        if ($data['source'] === 'upload' && $this->resumes->uploadedFile() === null) {
            Inertia::flash('toast', ['type' => 'warning', 'message' => 'Upload a PDF before using it as the download.']);

            return back();
        }

        $this->settings->set(['resume_source' => $data['source']]);
        Inertia::flash('toast', ['type' => 'success', 'message' => 'Résumé source updated.']);

        return back();
    }

    public function destroy(): RedirectResponse
    {
        $this->resumes->deleteUpload();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Uploaded résumé removed. Visitors now get the generated PDF.']);

        return to_route('admin.resume.edit');
    }

    public function preview(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->resumes->generatedResponse(inline: true);
    }
}
