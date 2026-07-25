<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = (string) env('ADMIN_PASSWORD');

        if ($password === '') {
            throw new RuntimeException('ADMIN_PASSWORD must be configured before running AdminSeeder.');
        }

        $email = strtolower((string) env('ADMIN_EMAIL', 'admin@cde.test'));
        $user = User::query()->firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => (string) env('ADMIN_NAME', 'CDE Administrator'),
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'is_admin' => true,
            'is_active' => true,
        ])->save();
    }
}
