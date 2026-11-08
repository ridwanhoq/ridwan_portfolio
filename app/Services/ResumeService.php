<?php

namespace App\Services;

use App\Support\ContentCache;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Résumé downloads: an uploaded PDF (kept in private storage) or an
 * ATS-friendly PDF generated from the portfolio content.
 */
class ResumeService
{
    private const DISK = 'local';

    public function __construct(private readonly Settings $settings, private readonly PortfolioContent $content) {}

    public function downloadResponse(): Response
    {
        $upload = $this->uploadedFile();

        if ($this->settings->get('resume_source') === 'upload' && $upload !== null) {
            return Storage::disk(self::DISK)->download($upload['path'], $this->downloadName(), [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-cache',
            ]);
        }

        return $this->generatedResponse();
    }

    public function generatedResponse(bool $inline = false): Response
    {
        $pdf = ContentCache::remember('resume.pdf', fn () => base64_encode($this->render()));
        $disposition = ($inline ? 'inline' : 'attachment').'; filename="'.$this->downloadName().'"';

        return response(base64_decode($pdf), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition,
            'Cache-Control' => 'no-cache',
        ]);
    }

    /** @return array{path: string, name: string, size: int, uploaded_at: string|null}|null */
    public function uploadedFile(): ?array
    {
        $path = $this->settings->get('resume_path');

        if (! is_string($path) || ! Storage::disk(self::DISK)->exists($path)) {
            return null;
        }

        return [
            'path' => $path,
            'name' => (string) $this->settings->get('resume_original_name', 'resume.pdf'),
            'size' => Storage::disk(self::DISK)->size($path),
            'uploaded_at' => $this->settings->get('resume_uploaded_at'),
        ];
    }

    public function storeUpload(UploadedFile $file): void
    {
        $previous = $this->settings->get('resume_path');
        $path = $file->storeAs('resume', Str::random(32).'.pdf', self::DISK);

        $this->settings->set([
            'resume_path' => $path,
            'resume_original_name' => Str::limit(preg_replace('/[^\w.\- ]+/u', '', $file->getClientOriginalName()) ?: 'resume.pdf', 200, ''),
            'resume_uploaded_at' => now()->toIso8601String(),
            'resume_source' => 'upload',
        ]);

        if (is_string($previous) && $previous !== $path) {
            Storage::disk(self::DISK)->delete($previous);
        }
    }

    public function deleteUpload(): void
    {
        $path = $this->settings->get('resume_path');

        if (is_string($path)) {
            Storage::disk(self::DISK)->delete($path);
        }

        $this->settings->set([
            'resume_path' => null,
            'resume_original_name' => null,
            'resume_uploaded_at' => null,
            'resume_source' => 'generated',
        ]);
    }

    public function downloadName(): string
    {
        return Str::slug($this->content->site()['name']).'-resume.pdf';
    }

    /** @return array<string, mixed> */
    public function data(): array
    {
        return [
            'site' => $this->content->site(),
            'profile' => $this->content->profile(),
            'experiences' => $this->content->experiences(),
            'skills' => $this->content->skills(),
            'education' => $this->content->education(),
            'certifications' => $this->content->certifications(),
            'projects' => $this->content->featuredProjects(),
        ];
    }

    private function render(): string
    {
        $options = new Options;
        $options->setIsRemoteEnabled(false);
        $options->setIsPhpEnabled(false);
        $options->setDefaultFont('DejaVu Sans');
        $options->setChroot(resource_path('views'));

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('resume.pdf', $this->data())->render());
        $dompdf->setPaper('A4');
        $dompdf->render();

        return (string) $dompdf->output();
    }
}
