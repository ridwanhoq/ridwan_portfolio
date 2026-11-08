<?php

namespace App\Providers;

use App\Models\Certification;
use App\Models\ContactMessage;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Media;
use App\Models\NavigationItem;
use App\Models\PageSection;
use App\Models\Post;
use App\Models\Principle;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SeoSetting;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Technology;
use App\Policies\ContactMessagePolicy;
use App\Policies\ContentPolicy;
use App\Policies\SingletonPolicy;
use App\Services\Settings;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configurePolicies();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        // Surface N+1 queries during development and tests.
        Model::preventLazyLoading(! app()->isProduction());

        if (app()->isProduction()) {
            URL::forceHttps();
        }

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configurePolicies(): void
    {
        $content = [
            Certification::class, Education::class, Experience::class, Media::class, NavigationItem::class,
            Post::class, Principle::class, Project::class, ProjectCategory::class, Skill::class,
            SkillCategory::class, SocialLink::class, Technology::class,
        ];

        foreach ($content as $model) {
            Gate::policy($model, ContentPolicy::class);
        }

        foreach ([Profile::class, SeoSetting::class, PageSection::class] as $model) {
            Gate::policy($model, SingletonPolicy::class);
        }

        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);
    }
}
