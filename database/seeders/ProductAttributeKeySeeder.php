<?php

namespace Database\Seeders;

use App\Models\ProductAttributeKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAttributeKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributeKeys = [
            [
                'name' => 'dpi',
                'display_name' => 'DPI',
                'data_type' => 'integer',
                'unit' => 'DPI',
                'is_filterable' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'weight',
                'display_name' => 'Weight',
                'data_type' => 'decimal',
                'unit' => 'grams',
                'is_filterable' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'num_keys',
                'display_name' => 'Number of Keys',
                'data_type' => 'integer',
                'unit' => 'keys',
                'is_filterable' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'color',
                'display_name' => 'Color',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'wireless',
                'display_name' => 'Wireless',
                'data_type' => 'boolean',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'battery_life',
                'display_name' => 'Battery Life',
                'data_type' => 'integer',
                'unit' => 'hours',
                'is_filterable' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'connectivity',
                'display_name' => 'Connectivity',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'sensor_type',
                'display_name' => 'Sensor Type',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'polling_rate',
                'display_name' => 'Polling Rate',
                'data_type' => 'integer',
                'unit' => 'Hz',
                'is_filterable' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'dimensions',
                'display_name' => 'Dimensions',
                'data_type' => 'string',
                'unit' => 'mm',
                'is_filterable' => false,
                'sort_order' => 10,
            ],
        ];

        foreach ($attributeKeys as $attributeKey) {
            ProductAttributeKey::create($attributeKey);
        }
    }
}
