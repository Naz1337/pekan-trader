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

        // Define default image paths
        $defaultImagePaths = [
            database_path('seeders/product_images/default_product_image_1.jpg'),
            database_path('seeders/product_images/default_product_image_2.jpg'),
            // Add more default image paths if needed
        ];

        // Create a map for category names to IDs for efficient lookup
        $categoryNameToIdMap = ProductCategory::all()->pluck('id', 'name')->toArray();

        // Get image files from the product_images directory
        $imageFiles = File::files(database_path('seeders/product_images'));
        $imagePaths = [];
        foreach ($imageFiles as $file) {
            $imagePaths[] = $file->getPathname();
        }

        if (empty($imagePaths)) {
            $this->command->warn('No images found in database/seeders/product_images/. Please add some images to seed products with images.');
        }


        // --- Custom Products for Pahang SMEs ---
        $allCustomProducts = [
            // Agricultural & Fresh Produce
            [
                'name' => 'Organic Pahang Highland Strawberry',
                'description' => 'Plump, naturally sweet strawberries grown in the cool highlands of Pahang using organic farming practices. Perfect for fresh consumption, smoothies, or jams.',
                'price' => 28.00,
                'stock_quantity' => 200,
                'category_name' => 'Agricultural & Fresh Produce',
            ],
            [
                'name' => 'Pahang Air Nanas Pineapple',
                'description' => 'Sweet, juicy Air Nanas pineapples harvested from local farms in Pahang. Known for its distinct flavor and low acidity, great for snacking or juicing.',
                'price' => 15.00,
                'stock_quantity' => 500,
                'category_name' => 'Agricultural & Fresh Produce',
            ],
            [
                'name' => 'Pahang Red Dragon Fruit',
                'description' => 'Vibrant red-fleshed dragon fruit grown sustainably in Pahang. Rich in antioxidants and fiber, perfect for healthy snacks or smoothie bowls.',
                'price' => 12.00,
                'stock_quantity' => 400,
                'category_name' => 'Agricultural & Fresh Produce',
            ],
            [
                'name' => 'Pahang Black Glutinous Rice',
                'description' => 'Nutritious black glutinous rice cultivated in the fertile plains of Pahang. Ideal for making desserts like bubur pulut hitam or steamed cakes.',
                'price' => 18.00,
                'stock_quantity' => 300,
                'category_name' => 'Agricultural & Fresh Produce',
            ],
            [
                'name' => 'Pahang Jackfruit Seeds',
                'description' => 'Freshly harvested jackfruit seeds from Pahang-grown trees, cleaned and ready for roasting or boiling. A crunchy, nutritious snack.',
                'price' => 6.00,
                'stock_quantity' => 250,
                'category_name' => 'Agricultural & Fresh Produce',
            ],

            // Food & Beverage (F&B)
            [
                'name' => 'Sambal Tempoyak Pahang (Fermented Durian Chili Paste)',
                'description' => 'Authentic tempoyak made from fermented durian pulp and fresh chilies, following traditional Pahang recipes. Tangy, spicy, and deeply flavorful.',
                'price' => 20.00,
                'stock_quantity' => 200,
                'category_name' => 'Food & Beverage (F&B)',
            ],
            [
                'name' => 'Teh Tarik Pahang Blend',
                'description' => 'Premium tea blend specially curated for authentic Teh Tarik, sourced from Pahang plantations. Rich aroma and strong flavor perfect for pulling.',
                'price' => 12.00,
                'stock_quantity' => 300,
                'category_name' => 'Food & Beverage (F&B)',
            ],
            [
                'name' => 'Pahang Coconut Sugar',
                'description' => 'Natural, unrefined coconut sugar made from fresh coconut flower nectar collected from Pahang villages. Low glycemic index alternative to regular sugar.',
                'price' => 15.00,
                'stock_quantity' => 250,
                'category_name' => 'Food & Beverage (F&B)',
            ],
            [
                'name' => 'Pahang Kuih Tradisional Assorted Pack',
                'description' => 'Handmade assortment of traditional Pahang kuih such as serimuka, lapis legit, and onde-onde. Preserved in eco-friendly packaging for freshness.',
                'price' => 25.00,
                'stock_quantity' => 150,
                'category_name' => 'Food & Beverage (F&B)',
            ],
            [
                'name' => 'Pahang Cempedak Chips',
                'description' => 'Crispy, golden cempedak chips made from young fruits harvested in Pahang. Lightly salted and fried to perfection. Great snack or garnish.',
                'price' => 8.00,
                'stock_quantity' => 400,
                'category_name' => 'Food & Beverage (F&B)',
            ],

            // Fashion & Accessories
            [
                'name' => 'Tenun Pahang Diraja Tote Bag',
                'description' => 'Stylish tote bag woven using the Tenun Pahang Diraja technique, featuring traditional motifs passed down through generations. Durable and elegant.',
                'price' => 150.00,
                'stock_quantity' => 60,
                'category_name' => 'Fashion & Accessories',
            ],
            [
                'name' => 'Batik Pahang Silk Scarf',
                'description' => 'Luxurious silk scarf dyed with traditional Batik Pahang patterns using natural dyes and hand-drawn techniques. Lightweight and versatile for any outfit.',
                'price' => 120.00,
                'stock_quantity' => 50,
                'category_name' => 'Fashion & Accessories',
            ],
            [
                'name' => 'Pahang Tribal Beaded Earrings',
                'description' => 'Handcrafted earrings using traditional beadwork techniques of the indigenous tribes of Pahang. Each piece tells a story and celebrates local culture.',
                'price' => 45.00,
                'stock_quantity' => 100,
                'category_name' => 'Fashion & Accessories',
            ],
            [
                'name' => 'Pahang Songket Shawl',
                'description' => 'Delicate songket shawl woven with gold or silver threads in traditional Pahang patterns. Worn during formal events or weddings.',
                'price' => 220.00,
                'stock_quantity' => 30,
                'category_name' => 'Fashion & Accessories',
            ],
            [
                'name' => 'Rattan Bangle Set',
                'description' => 'Set of lightweight rattan bangles handwoven using traditional Pahang designs. Eco-friendly and stylish accessory for everyday or festive wear.',
                'price' => 35.00,
                'stock_quantity' => 120,
                'category_name' => 'Fashion & Accessories',
            ],

            // Handicrafts & Souvenirs
            [
                'name' => 'Pahang Wooden Miniature Boat',
                'description' => 'Hand-carved miniature wooden boat inspired by traditional fishing boats from Pahang’s coastal towns. Beautiful decorative item or souvenir.',
                'price' => 60.00,
                'stock_quantity' => 50,
                'category_name' => 'Handicrafts & Souvenirs',
            ],
            [
                'name' => 'Pahang Tribal Clay Pottery',
                'description' => 'Hand-molded clay pots using techniques from Pahang’s indigenous communities. Ideal for decoration or holding plants and herbs.',
                'price' => 75.00,
                'stock_quantity' => 40,
                'category_name' => 'Handicrafts & Souvenirs',
            ],
            [
                'name' => 'Hand-Painted Pahang Cultural Canvas Art',
                'description' => 'Canvas art depicting iconic scenes or motifs from Pahang’s culture, painted by local artists. Adds a touch of regional charm to any space.',
                'price' => 180.00,
                'stock_quantity' => 25,
                'category_name' => 'Handicrafts & Souvenirs',
            ],
            [
                'name' => 'Pahang Wayang Kulit Keychain',
                'description' => 'Miniaturized Wayang Kulit puppet keychains carved from wood and painted with vibrant colors. Fun and meaningful keepsake.',
                'price' => 20.00,
                'stock_quantity' => 200,
                'category_name' => 'Handicrafts & Souvenirs',
            ],
            [
                'name' => 'Pahang Bamboo Wind Chime',
                'description' => 'Handcrafted wind chime made from natural bamboo found in Pahang forests. Produces soothing sounds, ideal for home decor or gardens.',
                'price' => 40.00,
                'stock_quantity' => 150,
                'category_name' => 'Handicrafts & Souvenirs',
            ],

            // Health & Beauty
            [
                'name' => 'Pahang Rainforest Propolis',
                'description' => 'Pure propolis collected from wild bee hives in Pahang rainforests. Known for immune-boosting properties and natural healing benefits.',
                'price' => 50.00,
                'stock_quantity' => 100,
                'category_name' => 'Health & Beauty',
            ],
            [
                'name' => 'Pahang Herbal Hair Oil',
                'description' => 'Natural hair oil infused with Pahang-grown herbs like betel leaf, turmeric, and moringa. Promotes scalp health and hair growth.',
                'price' => 35.00,
                'stock_quantity' => 120,
                'category_name' => 'Health & Beauty',
            ],
            [
                'name' => 'Pahang Belimbing Buluh Face Toner',
                'description' => 'Facial toner made from Pahang-grown belimbing buluh (Averrhoa bilimbi), known for its astringent and clarifying properties.',
                'price' => 28.00,
                'stock_quantity' => 150,
                'category_name' => 'Health & Beauty',
            ],
            [
                'name' => 'Pahang Rainforest Body Butter',
                'description' => 'Rich body butter made with shea butter, cocoa butter, and Pahang rainforest oils. Deeply nourishing and great for dry skin.',
                'price' => 45.00,
                'stock_quantity' => 100,
                'category_name' => 'Health & Beauty',
            ],
            [
                'name' => 'Pahang Herbal Foot Soak Pack',
                'description' => 'Natural foot soak made from Pahang-grown ginger, lemongrass, and eucalyptus. Helps relieve stress and soothe tired feet.',
                'price' => 22.00,
                'stock_quantity' => 200,
                'category_name' => 'Health & Beauty',
            ],

            // Home & Living
            [
                'name' => 'Pahang Rattan Wall Hanging',
                'description' => 'Handwoven rattan wall décor showcasing traditional Pahang weaving patterns. Adds rustic charm and artisanal flair to any room.',
                'price' => 90.00,
                'stock_quantity' => 60,
                'category_name' => 'Home & Living',
            ],
            [
                'name' => 'Tenun Pahang Cushion Cover',
                'description' => 'Decorative cushion cover made from Tenun Pahang fabric, displaying traditional motifs. Combines luxury with cultural heritage.',
                'price' => 80.00,
                'stock_quantity' => 70,
                'category_name' => 'Home & Living',
            ],
            [
                'name' => 'Pahang Teak Wood Serving Tray',
                'description' => 'Handcrafted serving tray made from durable teak wood sourced from Pahang forests. Elegant and functional for meals or decor.',
                'price' => 130.00,
                'stock_quantity' => 40,
                'category_name' => 'Home & Living',
            ],
            [
                'name' => 'Pahang Bamboo Planters',
                'description' => 'Eco-friendly planters made from Pahang bamboo, suitable for indoor or outdoor use. Available in various sizes and designs.',
                'price' => 45.00,
                'stock_quantity' => 100,
                'category_name' => 'Home & Living',
            ],
            [
                'name' => 'Pahang Ceramic Tableware Set',
                'description' => 'Hand-thrown ceramic tableware featuring earthy tones and motifs inspired by Pahang’s natural scenery. Microwave and dishwasher safe.',
                'price' => 160.00,
                'stock_quantity' => 50,
                'category_name' => 'Home & Living',
            ],

            // Livestock & Animal Products
            [
                'name' => 'Pahang Goat Cheese',
                'description' => 'Locally produced goat cheese made from milk of goats raised in Pahang. Mild, creamy, and perfect for gourmet spreads or cooking.',
                'price' => 35.00,
                'stock_quantity' => 100,
                'category_name' => 'Livestock & Animal Products',
            ],
            [
                'name' => 'Pahang Free-Range Duck Eggs',
                'description' => 'Fresh duck eggs from free-range ducks raised on small Pahang farms. Great for making nasi lemak or custards.',
                'price' => 3.50,
                'stock_quantity' => 300,
                'category_name' => 'Livestock & Animal Products',
            ],
            [
                'name' => 'Pahang Buffalo Milk Yogurt',
                'description' => 'Thick, creamy yogurt made from buffalo milk sourced from Pahang farms. Rich in nutrients and probiotics.',
                'price' => 10.00,
                'stock_quantity' => 200,
                'category_name' => 'Livestock & Animal Products',
            ],
            [
                'name' => 'Pahang Organic Chicken',
                'description' => 'Free-range, organic chicken raised without antibiotics or artificial feed. Ideal for soups, grilling, or roasting.',
                'price' => 22.00,
                'stock_quantity' => 200,
                'category_name' => 'Livestock & Animal Products',
            ],
            [
                'name' => 'Pahang Bee Pollen',
                'description' => 'Pure bee pollen collected from wild bees in Pahang rainforests. Known for energy-boosting and immunity-enhancing properties.',
                'price' => 60.00,
                'stock_quantity' => 80,
                'category_name' => 'Livestock & Animal Products',
            ],

            // Books & Educational Materials
            [
                'name' => 'History of Pahang: From Ancient Kingdom to Modern State',
                'description' => 'Comprehensive book detailing the history and evolution of Pahang from ancient times to the present day.',
                'price' => 75.00,
                'stock_quantity' => 100,
                'category_name' => 'Books & Educational Materials',
            ],
            [
                'name' => 'Learn Tenun Pahang Diraja Weaving (Workbook + DVD)',
                'description' => 'Step-by-step guide to learning the Tenun Pahang Diraja weaving technique, including video tutorials and hands-on exercises.',
                'price' => 90.00,
                'stock_quantity' => 50,
                'category_name' => 'Books & Educational Materials',
            ],
            [
                'name' => 'Pahang Flora & Fauna Coloring Book',
                'description' => 'Educational coloring book featuring illustrations of Pahang’s native plants and animals. Designed for children and nature lovers.',
                'price' => 25.00,
                'stock_quantity' => 150,
                'category_name' => 'Books & Educational Materials',
            ],
            [
                'name' => 'Pahang Dialect Phrasebook',
                'description' => 'Pocket-sized phrasebook teaching basic Pahang dialect expressions and greetings. Includes audio QR codes for pronunciation.',
                'price' => 18.00,
                'stock_quantity' => 200,
                'category_name' => 'Books & Educational Materials',
            ],
            [
                'name' => 'Pahang Folktales: Myths and Legends Retold',
                'description' => 'Collection of retold folktales from Pahang, illustrated and written for modern audiences while preserving traditional storytelling.',
                'price' => 45.00,
                'stock_quantity' => 120,
                'category_name' => 'Books & Educational Materials',
            ],

            // Local Services (voucher-based)
            [
                'name' => 'Pahang Homestay Experience Voucher',
                'description' => 'One-night stay at a Pahang homestay, including meals and cultural activities like batik painting or jungle trekking.',
                'price' => 150.00,
                'stock_quantity' => 100,
                'category_name' => 'Local Services (voucher-based)',
            ],
            [
                'name' => 'Pahang Guided Jungle Trekking Tour',
                'description' => 'Full-day guided jungle trek in Pahang rainforests, including waterfall visits and local storytelling.',
                'price' => 120.00,
                'stock_quantity' => 80,
                'category_name' => 'Local Services (voucher-based)',
            ],
            [
                'name' => 'Tenun Pahang Weaving Workshop Voucher',
                'description' => 'Hands-on workshop where participants learn the basics of Tenun Pahang weaving from master artisans.',
                'price' => 180.00,
                'stock_quantity' => 60,
                'category_name' => 'Local Services (voucher-based)',
            ],
            [
                'name' => 'Pahang Farm-to-Table Cooking Class',
                'description' => 'Cooking class using fresh ingredients from Pahang farms. Participants prepare traditional Pahang dishes under expert guidance.',
                'price' => 130.00,
                'stock_quantity' => 90,
                'category_name' => 'Local Services (voucher-based)',
            ],
            [
                'name' => 'Pahang Heritage Walking Tour Voucher',
                'description' => 'Guided walking tour through historic sites in Pahang towns like Pekan and Kuala Lipis, exploring architecture and local stories.',
                'price' => 90.00,
                'stock_quantity' => 100,
                'category_name' => 'Local Services (voucher-based)',
            ],
        ];

        foreach ($allCustomProducts as $product) {
            $categoryId = $categoryNameToIdMap[$product['category_name']] ?? null;
            if (is_null($categoryId)) {
                $this->command->warn("Product category '{$product['category_name']}' not found in map. Skipping product: {$product['name']}");
                continue;
            }

            $imagesToUse = !empty($product['images']) ? $product['images'] : $defaultImagePaths;

            $productData = [
                'seller_id' => $faker->randomElement($sellerIds),
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock_quantity' => $product['stock_quantity'],
                'delivery_fee' => $faker->randomFloat(2, 5, 10), // Random delivery fee
                'is_published' => true,
                'product_category_id' => $categoryId,
                'images' => $imagesToUse,
            ];

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
