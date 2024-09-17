<?php

namespace Database\Seeders;

use App\Models\Plat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plat::factory()->count(10)->create();
        foreach (User::all() as $user) {
            $platIds = Plat::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $user->favoritePlats()->syncWithoutDetaching($platIds);
            $user->assignRole('user');
        }
    }
}
