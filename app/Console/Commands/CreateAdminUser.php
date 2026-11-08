<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('portfolio:admin {email? : Administrator email} {--name= : Display name}')]
#[Description('Create an administrator, or reset the password of an existing one')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email') ?: text('Email address', required: true);
        $name = $this->option('name') ?: text('Name', default: 'Administrator', required: true);
        $secret = password('Password (min. 12 characters)', required: true);

        $validator = Validator::make(
            ['email' => $email, 'password' => $secret],
            ['email' => ['required', 'email:rfc'], 'password' => ['required', Password::min(12)->letters()->numbers()]],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $existed = $user->exists;

        $user->fill(['name' => $name, 'password' => $secret]);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        $this->info($existed ? "Updated administrator {$email}." : "Created administrator {$email}.");

        return self::SUCCESS;
    }
}
