<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some attribute keys
        $dpiKey = ProductAttributeKey::where('name', 'dpi')->first();
        $weightKey = ProductAttributeKey::where('name', 'weight')->first();
        $colorKey = ProductAttributeKey::where('name', 'color')->first();
        $wirelessKey = ProductAttributeKey::where('name', 'wireless')->first();
        $numKeysKey = ProductAttributeKey::where('name', 'num_keys')->first();

        // Get first few products (assuming they exist)
        $products = Product::take(3)->get();

        if ($products->count() > 0 && $dpiKey && $weightKey && $colorKey && $wirelessKey) {
            // Example: Mouse A
            if (isset($products[0])) {
                ProductAttribute::create([
                    'product_id' => $products[0]->id,
                    'attribute_key_id' => $dpiKey->id,
                    'value' => '800'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[0]->id,
                    'attribute_key_id' => $weightKey->id,
                    'value' => '85.5'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[0]->id,
                    'attribute_key_id' => $colorKey->id,
                    'value' => 'Black'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[0]->id,
                    'attribute_key_id' => $wirelessKey->id,
                    'value' => '0' // false
                ]);
            }

            // Example: Mouse B
            if (isset($products[1])) {
                ProductAttribute::create([
                    'product_id' => $products[1]->id,
                    'attribute_key_id' => $dpiKey->id,
                    'value' => '1600'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[1]->id,
                    'attribute_key_id' => $weightKey->id,
                    'value' => '92.3'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[1]->id,
                    'attribute_key_id' => $colorKey->id,
                    'value' => 'White'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[1]->id,
                    'attribute_key_id' => $wirelessKey->id,
                    'value' => '1' // true
                ]);
            }

            // Example: Keyboard (if third product exists)
            if (isset($products[2]) && $numKeysKey) {
                ProductAttribute::create([
                    'product_id' => $products[2]->id,
                    'attribute_key_id' => $numKeysKey->id,
                    'value' => '104'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[2]->id,
                    'attribute_key_id' => $colorKey->id,
                    'value' => 'RGB'
                ]);

                ProductAttribute::create([
                    'product_id' => $products[2]->id,
                    'attribute_key_id' => $wirelessKey->id,
                    'value' => '0' // false
                ]);
            }
        }
    }
}
