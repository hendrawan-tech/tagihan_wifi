<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Mohammad Fiki',
            'email' => 'fiki@gmail.com',
            'password' => Hash::make('admin'),
            'role' => 'Admin',
        ]);

        User::create([
            'name' => 'Ridwan',
            'email' => 'ridwan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Teknisi',
        ]);

        User::create([
            'name' => 'Riyanto',
            'email' => 'riyanto@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Teknisi',
        ]);
    }
}
