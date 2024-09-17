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
            'password' => Hash::make('password'),
        ])->assignRole('admin');
        User::factory()->count(19)->create();


    }
}
