<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear existing non-seller users if needed,
        // but be careful if you have other non-seller admin users etc.
        // User::where('is_seller', false)->delete();

        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@mail.com',
            'password' => Hash::make('123'),
            'is_seller' => false, // Explicitly set, though it's the default
        ]);

        User::create([
            'name' => 'Ahmad Faizal',
            'email' => 'ahmad@mail.com',
            'password' => Hash::make('123'),
            'is_seller' => false,
        ]);

        User::create([
            'name' => 'Lee Wei Ling',
            'email' => 'leewl@mail.com',
            'password' => Hash::make('123'),
            'is_seller' => false,
        ]);
    }
}
