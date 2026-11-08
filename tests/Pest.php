<?php

use App\Models\User;
use Database\Seeders\ProfileContentSeeder;
use Database\Seeders\SiteStructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class)->in('Feature/Portfolio');
pest()->extend(TestCase::class)->in('Unit');

/**
 * Seed the structural records and CV content used by the public site.
 */
function seedPortfolio(): void
{
    test()->seed([SiteStructureSeeder::class, ProfileContentSeeder::class]);
}

function admin(): User
{
    return User::factory()->admin()->create();
}
