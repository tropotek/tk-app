<?php

namespace Demo\Database\Seeders;

use Demo\Database\Factories\IdeaFactory;
use Illuminate\Database\Seeder;

class IdeaSeeder extends Seeder
{
    public function run(): void
    {
        IdeaFactory::times(512)->create();
    }
}
