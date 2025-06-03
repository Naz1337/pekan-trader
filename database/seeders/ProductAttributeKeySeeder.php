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
            [
                'name' => 'material',
                'display_name' => 'Material',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'size',
                'display_name' => 'Size',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'packaging',
                'display_name' => 'Packaging',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => false,
                'sort_order' => 13,
            ],
            [
                'name' => 'volume',
                'display_name' => 'Volume',
                'data_type' => 'decimal',
                'unit' => 'ml',
                'is_filterable' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'pages',
                'display_name' => 'Pages',
                'data_type' => 'integer',
                'unit' => null,
                'is_filterable' => false,
                'sort_order' => 15,
            ],
            [
                'name' => 'format',
                'display_name' => 'Format',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 16,
            ],
            [
                'name' => 'duration',
                'display_name' => 'Duration',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => false,
                'sort_order' => 17,
            ],
            [
                'name' => 'capacity',
                'display_name' => 'Capacity',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 18,
            ],
            [
                'name' => 'type',
                'display_name' => 'Type',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 19,
            ],
            [
                'name' => 'flavor',
                'display_name' => 'Flavor',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'origin',
                'display_name' => 'Origin',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 21,
            ],
            [
                'name' => 'style',
                'display_name' => 'Style',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => true,
                'sort_order' => 22,
            ],
            [
                'name' => 'occasion',
                'display_name' => 'Occasion',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => false,
                'sort_order' => 23,
            ],
            [
                'name' => 'target_audience',
                'display_name' => 'Target Audience',
                'data_type' => 'string',
                'unit' => null,
                'is_filterable' => false,
                'sort_order' => 24,
            ],
        ];

        foreach ($attributeKeys as $attributeKey) {
            ProductAttributeKey::firstOrCreate(
                ['name' => $attributeKey['name']],
                $attributeKey
            );
        }
    }
}
