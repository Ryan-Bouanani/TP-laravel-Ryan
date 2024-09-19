<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use \Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DishFactory extends Factory
{
    protected $model = Dish::class;
    /**
     * Define the model's default state.
     *
     * @return array-<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => fake()->unique()->name(), // ->foodName()
            'description' => fake()->paragraph(5),
            'image' => fake()->imageUrl($width = 640, $height = 480),
            'user_id' => User::pluck('id')->random(), // Associe un utilisateur aléatoire
        ];
    }
}

