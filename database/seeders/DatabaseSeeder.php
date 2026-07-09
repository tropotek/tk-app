<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CreateAdminUserSeeder::class,
        ]);

        User::factory(76)->create();

        // NOTE: Look for a solution to load this from within the module ???
        if (class_exists(\Demo\DemoServiceProvider::class) && app()->getProvider(\Demo\DemoServiceProvider::class)) {
            $this->call(\Demo\Database\Seeders\IdeaSeeder::class);
        }
    }
}
