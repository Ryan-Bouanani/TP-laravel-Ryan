<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Ryan Bouanani',
            'email' => 'ryan@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('admin');
        User::factory()->count(19)->create();

        foreach (User::all() as $user) {
            $dishesId = Dish::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $user->favoriteDishes()->syncWithoutDetaching($dishesId);
            $user->assignRole('user');
        }


    }
}
