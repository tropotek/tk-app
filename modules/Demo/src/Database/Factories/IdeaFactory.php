<?php

namespace Demo\Database\Factories;

use App\Models\User;
use Demo\Models\Idea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Idea>
 */
class IdeaFactory extends Factory
{
    protected $model = Idea::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'title' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'description' => $this->faker->paragraph(),
        ];
    }
}
