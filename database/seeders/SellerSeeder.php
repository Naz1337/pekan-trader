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
                'approved' => true, // Set approved to true for seeded sellers
            ]);
        }

        // Create a specific Malaysian company seller
        $malaysianCompanyUser = User::firstOrCreate(
            ['email' => 'admin@majujaya.com.my'],
            [
                'name' => 'Maju Jaya Admin',
                'password' => Hash::make('password'),
                'is_seller' => true,
            ]
        );

        Seller::firstOrCreate(
            ['user_id' => $malaysianCompanyUser->id],
            [
                'business_name' => 'Syarikat Maju Jaya Sdn Bhd',
                'business_description' => 'Supplier of quality local goods',
                'business_address' => 'No. 123, Jalan Perniagaan, 50480 Kuala Lumpur',
                'business_phone' => '03-12345678',
                'business_email' => 'admin@majujaya.com.my',
                'logo_url' => 'https://via.placeholder.com/150/0000FF/808080?Text=MajuJaya',
                'opening_hour' => '09:00',
                'closing_hour' => '18:00',
                'facebook' => 'https://facebook.com/majujaya',
                'instagram' => 'https://instagram.com/majujaya',
                'ic_number' => '800101105555',
                'business_cert_url' => 'https://via.placeholder.com/400x300/CCCCCC/808080?Text=BusinessCert',
                'bank_name' => 'Maybank',
                'bank_account_name' => 'Syarikat Maju Jaya Sdn Bhd',
                'bank_account_number' => '123456789012',
                'approved' => true, // Set approved to true for this specific seller
            ]
        );
    }
}
