<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure a super admin exists. Use env vars SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD if provided.
        $email = env('SUPER_ADMIN_EMAIL', 'admin@example.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');

        $super = User::where('email', $email)->first();
        if (!$super) {
            $super = User::create([
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'email' => $email,
                'password' => bcrypt($password),
                'is_admin' => true,
            ]);
            $this->command->info('Created super admin: '.$email);
        } elseif (!$super->is_admin) {
            $super->is_admin = true;
            $super->save();
            $this->command->info('Promoted existing user to super admin: '.$email);
        }

        // If there are no users at all, and env vars weren't used, also mark the first user as admin for convenience
        if (User::count() === 1 && !$super->is_admin) {
            $first = User::first();
            $first->is_admin = true;
            $first->save();
            $this->command->info('Set first user ('.$first->email.') as admin.');
        }
    }
}
