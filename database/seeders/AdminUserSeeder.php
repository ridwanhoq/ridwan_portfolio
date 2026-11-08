<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Creates the administrator from ADMIN_* environment variables.
 * Without ADMIN_PASSWORD a random password is generated and printed once.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('portfolio.admin.email');
        $password = config('portfolio.admin.password');

        if (app()->isProduction() && ($password === null || $email === 'admin@example.com')) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD before seeding in production, or use `php artisan portfolio:admin`.');
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->command?->info("Admin user {$email} already exists. Skipped.");

            return;
        }

        $generated = $password === null;
        $password = is_string($password) ? $password : Str::password(20);

        $user = User::query()->create([
            'name' => (string) config('portfolio.admin.name'),
            'email' => $email,
            'password' => $password,
        ]);

        $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

        $this->command?->info("Admin user created: {$email}");

        if ($generated) {
            $this->command?->warn("Generated password (shown once): {$password}");
        }
    }
}
