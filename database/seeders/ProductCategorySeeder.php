<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Agricultural & Fresh Produce'],
            ['name' => 'Food & Beverage (F&B)'],
            ['name' => 'Fashion & Accessories'],
            ['name' => 'Handicrafts & Souvenirs'],
            ['name' => 'Health & Beauty'],
            ['name' => 'Home & Living'],
            ['name' => 'Livestock & Animal Products'],
            ['name' => 'Books & Educational Materials'],
            ['name' => 'Local Services (voucher-based)'],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
