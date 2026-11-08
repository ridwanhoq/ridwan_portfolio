<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

    Route::get('settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test-email', [Admin\SettingsController::class, 'testEmail'])->middleware('throttle:5,1')->name('settings.test-email');

    Route::get('resume', [Admin\ResumeController::class, 'edit'])->name('resume.edit');
    Route::post('resume', [Admin\ResumeController::class, 'upload'])->name('resume.upload');
    Route::put('resume/source', [Admin\ResumeController::class, 'source'])->name('resume.source');
    Route::delete('resume', [Admin\ResumeController::class, 'destroy'])->name('resume.destroy');
    Route::get('resume/preview', [Admin\ResumeController::class, 'preview'])->name('resume.preview');

    Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
    Route::get('media/browse', [Admin\MediaController::class, 'browse'])->name('media.browse');
    Route::post('media', [Admin\MediaController::class, 'store'])->middleware('throttle:60,1')->name('media.store');
    Route::put('media/{media}', [Admin\MediaController::class, 'update'])->name('media.update');
    Route::delete('media/{media}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');

    Route::post('markdown/preview', Admin\MarkdownPreviewController::class)->middleware('throttle:120,1')->name('markdown.preview');

    Route::get('messages', [Admin\ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [Admin\ContactMessageController::class, 'show'])->name('messages.show');
    Route::patch('messages/{message}/read', [Admin\ContactMessageController::class, 'toggleRead'])->name('messages.read');
    Route::delete('messages/{message}', [Admin\ContactMessageController::class, 'destroy'])->name('messages.destroy');

    // Generic CRUD modules (see ResourceController).
    $resources = [
        'experiences' => Admin\ExperienceController::class,
        'projects' => Admin\ProjectController::class,
        'project-categories' => Admin\ProjectCategoryController::class,
        'technologies' => Admin\TechnologyController::class,
        'skill-categories' => Admin\SkillCategoryController::class,
        'skills' => Admin\SkillController::class,
        'principles' => Admin\PrincipleController::class,
        'education' => Admin\EducationController::class,
        'certifications' => Admin\CertificationController::class,
        'social-links' => Admin\SocialLinkController::class,
        'navigation' => Admin\NavigationItemController::class,
        'posts' => Admin\PostController::class,
        'seo' => Admin\SeoSettingController::class,
        'sections' => Admin\PageSectionController::class,
    ];

    foreach ($resources as $uri => $controller) {
        Route::controller($controller)->prefix($uri)->name("{$uri}.")->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{id}/edit', 'edit')->whereNumber('id')->name('edit');
            Route::put('{id}', 'update')->whereNumber('id')->name('update');
            Route::delete('{id}', 'destroy')->whereNumber('id')->name('destroy');
            Route::patch('{id}/toggle', 'toggle')->whereNumber('id')->name('toggle');
        });
    }
});
