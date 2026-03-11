<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage-system',
            'manage-users',
            'view-forms',
            'view-tables',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create admin role
        $role = Role::firstOrCreate(['name' => 'Admin']);
        // Get all permissions
        $permissions = Permission::pluck('id','id')->all();
        // Sync all permissions to admin role
        $role->syncPermissions($permissions);

        $role = Role::firstOrCreate(['name' => 'Staff']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);

        $role = Role::firstOrCreate(['name' => 'Member']);

    }
}
