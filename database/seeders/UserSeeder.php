<?php

namespace Database\Seeders;

use App\Models\Plat;
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
            'password' => Hash::make('ryan'),
        ])->assignRole('admin');;
        foreach(User::all() as $user) {
            $user->favoritePlats()->attach(Plat::inRandomOrder()->take(rand(1,10))->pluck('id')->toArray());
            $user->assignRole('user');
        }
    }
}
