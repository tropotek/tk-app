<?php

namespace Database\Seeders;

use App\Enum\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => Roles::Admin,
            ]
        );

        // Ensure the role is correct even if the row already existed
        $user->update(['role' => Roles::Admin]);
    }
}
