<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('short_name')->nullable();
            $table->string('designation');
            $table->string('tagline')->nullable();
            $table->string('hero_title');
            $table->text('hero_subtitle')->nullable();
            $table->text('summary')->nullable();
            $table->string('location')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->boolean('show_phone')->default(false);
            $table->string('work_preference')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->unsignedTinyInteger('years_primary_stack')->nullable();
            $table->string('primary_stack_label')->nullable();
            $table->json('hero_technologies')->nullable();
            $table->boolean('availability_enabled')->default(false);
            $table->string('availability_text')->nullable();
            $table->foreignId('photo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->text('about_intro')->nullable();
            $table->text('philosophy')->nullable();
            $table->text('career_overview')->nullable();
            $table->text('specialization')->nullable();
            $table->text('interests')->nullable();
            $table->text('collaboration')->nullable();
            $table->json('languages')->nullable();
            $table->timestamps();
        });

        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 50)->unique();
            $table->string('label');
            $table->string('title')->nullable();
            $table->string('description', 320)->nullable();
            $table->foreignId('og_image_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('noindex')->default(false);
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('label');
            $table->string('eyebrow')->nullable();
            $table->string('heading')->nullable();
            $table->text('intro')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_enabled', 'sort_order']);
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('label', 60);
            $table->string('url');
            $table->string('location', 20)->default('header');
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['location', 'is_visible', 'sort_order']);
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 40);
            $table->string('label', 60);
            $table->string('url');
            $table->boolean('show_in_hero')->default(true);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('website_settings');
        Schema::dropIfExists('profiles');
    }
};
