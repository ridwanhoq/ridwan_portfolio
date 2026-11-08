<?php

use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\BlogController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\ProjectController;
use App\Http\Controllers\Site\ResumeController;
use App\Http\Controllers\Site\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('about', AboutController::class)->name('about');
Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{slug}', [ProjectController::class, 'show'])->where('slug', '[A-Za-z0-9\-_]+')->name('projects.show');
Route::get('resume', [ResumeController::class, 'show'])->name('resume');
Route::get('resume/download', [ResumeController::class, 'download'])->middleware('throttle:30,1')->name('resume.download');
Route::get('contact', [ContactController::class, 'show'])->name('contact');
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:10,1')->name('contact.store');
Route::get('privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogController::class, 'show'])->where('slug', '[A-Za-z0-9\-_]+')->name('blog.show');
Route::get('sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
