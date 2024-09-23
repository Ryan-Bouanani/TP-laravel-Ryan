<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Seeder;

class DishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dish::factory()->count(10)->create();
    }
}
