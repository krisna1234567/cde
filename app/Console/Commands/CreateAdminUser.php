<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--name=} {--email=} {--password=}';
    protected $description = 'Create or update an active administrator account';

    public function handle(): int
    {
        $name = (string) ($this->option('name') ?: $this->ask('Admin name', 'Administrator'));
        $email = (string) ($this->option('email') ?: $this->ask('Admin email'));
        $password = (string) ($this->option('password') ?: $this->secret('Admin password'));

        $validator = Validator::make(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => strtolower($email)]);
        $user->forceFill([
            'name' => $name,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_active' => true,
        ])->save();

        $this->info('Administrator account is ready: '.$user->email);

        return self::SUCCESS;
    }
}
