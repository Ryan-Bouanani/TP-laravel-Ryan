<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use \Faker;

/**
 * @extends Factory<Dish>
 */
class DishFactory extends Factory
{
    protected $model = Dish::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => fake()->unique()->name(), // ->foodName()
            'description' => fake()->paragraph(5),
            'image' => fake()->imageUrl($width = 640, $height = 480),
            // 'created_at' => fake()->dateTimeBetween('-2 months', 'now'),
            // 'user_id' => User::inRandomOrder()->first()->id // Associe un utilisateur aléatoire
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Dish $dish) {
            $dish->user()->existsOr(
                fn()=>$dish->user()->associate(User::factory()->createOne())
            );
        });
    }
}

