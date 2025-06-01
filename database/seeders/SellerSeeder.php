<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Seller::query()->delete(); // Clear existing sellers
        User::where('is_seller', true)->delete(); // Clear users associated with sellers

        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) { // Create 10 sample sellers
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'is_seller' => true,
            ]);

            Seller::create([
                'user_id' => $user->id,
                'business_name' => $faker->company,
                'business_description' => $faker->paragraph,
                'business_address' => $faker->address,
                'business_phone' => $faker->phoneNumber,
                'business_email' => $user->email,
                'logo_url' => $faker->imageUrl(640, 480, 'business', true),
                'opening_hour' => '09:00',
                'closing_hour' => '18:00',
                'facebook' => 'https://facebook.com/' . $faker->userName,
                'instagram' => 'https://instagram.com/' . $faker->userName,
                'ic_number' => $faker->numerify('############'),
                'business_cert_url' => $faker->imageUrl(640, 480, 'certification', true),
                'bank_name' => $faker->randomElement(['Maybank', 'CIMB', 'Public Bank']),
                'bank_account_name' => $faker->name,
                'bank_account_number' => $faker->bankAccountNumber,
            ]);
        }
    }
}
