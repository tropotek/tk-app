<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
                'password' => bcrypt('password')
            ]
        );

        // Create admin role
        $role = Role::firstOrCreate(['name' => 'Admin']);

        // Get all permissions
        $permissions = Permission::pluck('id','id')->all();

        // Sync all permissions to admin role
        $role->syncPermissions($permissions);

        // Assign admin role to user
        $user->assignRole([$role->id]);
    }
}
