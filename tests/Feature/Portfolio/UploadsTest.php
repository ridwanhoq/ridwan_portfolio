<?php

use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use App\Services\Settings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedPortfolio();
    Storage::fake('public');
    Storage::fake('local');
});

it('re-encodes uploaded images and creates WebP variants', function () {
    $this->actingAs(admin())
        ->postJson('/admin/media', ['file' => UploadedFile::fake()->image('Screen Shot.png', 1600, 900), 'alt' => 'Dashboard'])
        ->assertCreated()
        ->assertJsonPath('alt', 'Dashboard')
        ->assertJsonPath('width', 1600);

    $media = Media::query()->sole();
    expect($media->variants)->toHaveKeys(['webp', 'thumb'])
        ->and($media->filename)->not->toContain('Screen');

    foreach ($media->allPaths() as $path) {
        Storage::disk('public')->assertExists($path);
    }
});

it('rejects SVG and non-image files', function (UploadedFile $file) {
    $this->actingAs(admin())->postJson('/admin/media', ['file' => $file])->assertUnprocessable()->assertJsonValidationErrors('file');

    expect(Media::query()->count())->toBe(0);
})->with([
    'svg' => fn () => UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
    'php disguised as jpg' => fn () => UploadedFile::fake()->createWithContent('shell.jpg', '<?php echo 1;'),
    'pdf' => fn () => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
]);

it('rejects images over the size limit', function () {
    $this->actingAs(admin())
        ->postJson('/admin/media', ['file' => UploadedFile::fake()->image('big.jpg')->size(9000)])
        ->assertJsonValidationErrors('file');
});

it('does not let non-admins upload', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/admin/media', ['file' => UploadedFile::fake()->image('a.png', 200, 200)])
        ->assertForbidden();
});

it('deletes files and detaches the image from projects', function () {
    $this->actingAs(admin())->postJson('/admin/media', ['file' => UploadedFile::fake()->image('a.png', 900, 600)]);
    $media = Media::query()->sole();
    $project = Project::query()->firstOrFail();
    $project->update(['thumbnail_media_id' => $media->id]);
    $project->images()->create(['media_id' => $media->id, 'kind' => 'screenshot']);

    $this->delete("/admin/media/{$media->id}")->assertRedirect();

    foreach ($media->allPaths() as $path) {
        Storage::disk('public')->assertMissing($path);
    }

    expect($project->fresh()->thumbnail_media_id)->toBeNull()
        ->and($project->images()->count())->toBe(0);
});

it('updates alt text', function () {
    $this->actingAs(admin())->postJson('/admin/media', ['file' => UploadedFile::fake()->image('a.png', 200, 200)]);
    $media = Media::query()->sole();

    $this->put("/admin/media/{$media->id}", ['alt' => 'Architecture diagram', 'caption' => 'v2'])->assertSessionHasNoErrors();

    expect($media->fresh()->alt)->toBe('Architecture diagram');
});

it('serves an uploaded résumé from private storage', function () {
    $pdf = UploadedFile::fake()->createWithContent('My CV.pdf', "%PDF-1.4\n% test\n");

    $this->actingAs(admin())->post('/admin/resume', ['file' => $pdf])->assertSessionHasNoErrors();

    $path = app(Settings::class)->get('resume_path');
    Storage::disk('local')->assertExists($path);
    Storage::disk('public')->assertMissing($path);

    $response = $this->get('/resume/download')->assertOk();
    expect($response->streamedContent())->toStartWith('%PDF-1.4');
});

it('rejects files that are not real PDFs', function () {
    $fake = UploadedFile::fake()->createWithContent('cv.pdf', 'not a pdf at all');

    $this->actingAs(admin())->post('/admin/resume', ['file' => $fake])->assertSessionHasErrors('file');
});

it('falls back to the generated résumé after the upload is removed', function () {
    $this->actingAs(admin())->post('/admin/resume', ['file' => UploadedFile::fake()->createWithContent('cv.pdf', "%PDF-1.4\n")]);
    $path = app(Settings::class)->get('resume_path');

    $this->delete('/admin/resume')->assertRedirect('/admin/resume');

    Storage::disk('local')->assertMissing($path);
    expect(app(Settings::class)->get('resume_source'))->toBe('generated');
    $this->get('/resume/download')->assertOk()->assertHeader('Content-Type', 'application/pdf');
});

it('will not switch to an uploaded résumé that does not exist', function () {
    $this->actingAs(admin())->put('/admin/resume/source', ['source' => 'upload']);

    expect(app(Settings::class)->get('resume_source'))->toBe('generated');
});
