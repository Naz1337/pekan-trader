<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeKey;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Seller;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::query()->delete(); // Clear existing products to prevent duplicates
        ProductImage::query()->delete();
        ProductAttribute::query()->delete();

        $faker = Faker::create();
        $sellerIds = Seller::pluck('id')->toArray();
        $productAttributeKeys = ProductAttributeKey::all();
        $productCategoryIds = ProductCategory::pluck('id')->toArray();

        if (empty($sellerIds)) {
            $this->command->warn('No sellers found. Please run SellerSeeder first.');
            return;
        }

        if (empty($productCategoryIds)) {
            $this->command->warn('No product categories found. Please run ProductCategorySeeder first.');
            return;
        }

        // Get image files from the product_images directory
        $imageFiles = File::files(database_path('seeders/product_images'));
        $imagePaths = [];
        foreach ($imageFiles as $file) {
            $imagePaths[] = $file->getPathname();
        }

        if (empty($imagePaths)) {
            $this->command->warn('No images found in database/seeders/product_images/. Please add some images to seed products with images.');
        }

        for ($i = 0; $i < 100; $i++) {
            $productData = [
                'seller_id' => $faker->randomElement($sellerIds),
                'name' => $faker->words($faker->numberBetween(2, 5), true),
                'description' => $faker->paragraph(),
                'price' => $faker->randomFloat(2, 10, 1000),
                'stock_quantity' => $faker->numberBetween(0, 200),
                'delivery_fee' => $faker->randomFloat(2, 0, 50),
                'is_published' => $faker->boolean(80), // 80% chance to be published
                'product_category_id' => $faker->randomElement($productCategoryIds),
            ];

            // Add images
            if (!empty($imagePaths)) {
                $numImages = $faker->numberBetween(1, min(3, count($imagePaths)));
                $selectedImages = $faker->randomElements($imagePaths, $numImages);
                $productData['images'] = $selectedImages;
            }

            // Add attributes
            $attributesData = [];
            foreach ($productAttributeKeys as $key) {
                if ($faker->boolean(70)) { // 70% chance to add an attribute
                    $value = null;
                    switch ($key->data_type) {
                        case 'string':
                            $value = $faker->word();
                            break;
                        case 'integer':
                            $value = $faker->numberBetween(1, 100);
                            break;
                        case 'decimal':
                            $value = $faker->randomFloat(2, 0.1, 100.0);
                            break;
                        case 'boolean':
                            $value = $faker->boolean();
                            break;
                        default:
                            $value = $faker->word();
                            break;
                    }
                    $attributesData[] = [
                        'attribute_key_id' => $key->id,
                        'value' => (string) $value,
                    ];
                }
            }
            if (!empty($attributesData)) {
                $productData['attributes'] = $attributesData;
            }

            self::createProductFromArray($productData);
        }
    }

    /**
     * Creates a product with relationships (attributes, images, etc.)
     * Handles image files from database/seeders/product_images/
     * Validates required fields
     *
     * @param array $data
     * @return Product
     * @throws \Exception
     */
    public static function createProductFromArray(array $data): Product
    {
        // Validate required fields
        $requiredFields = ['seller_id', 'name', 'description', 'price', 'stock_quantity', 'product_category_id'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        // Create product
        $product = Product::create([
            'seller_id' => $data['seller_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
            'delivery_fee' => $data['delivery_fee'] ?? 0.00,
            'is_published' => $data['is_published'] ?? true,
            'product_category_id' => $data['product_category_id'],
        ]);

        // Handle product images
        if (isset($data['images']) && is_array($data['images'])) {
            $order = 0;
            foreach ($data['images'] as $imagePath) {
                // Ensure the file exists before attempting to store
                if (!File::exists($imagePath)) {
                    // Log or handle error for non-existent image file
                    continue;
                }

                $fileName = basename($imagePath);
                $storagePath = 'product_images/' . Str::random(40) . '_' . $fileName;

                // Copy the image from the seeder directory to public storage
                Storage::disk('public')->put($storagePath, File::get($imagePath));

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $storagePath,
                    'is_thumbnail' => ($order === 0), // First image as thumbnail
                    'order' => $order,
                ]);
                $order++;
            }
        }

        // Handle product attributes
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $order = 0;
            foreach ($data['attributes'] as $attributeData) {
                ProductAttribute::create([
                    'product_id' => $product->id,
                    'attribute_key_id' => $attributeData['attribute_key_id'],
                    'value' => $attributeData['value'],
                    'order_column' => $order,
                ]);
                $order++;
            }
        }

        return $product;
    }
}
