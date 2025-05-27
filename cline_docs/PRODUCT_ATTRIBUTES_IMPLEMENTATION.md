# Product Attributes System Implementation

This document explains the implementation of the key-value attribute system for products in the Pekan Trader application.

## Overview

The product attributes system allows products to have flexible key-value pairs for specifications like DPI, Weight, Color, etc. This enables:

- Shared attribute keys across multiple products
- Unique values per product for each attribute
- Filterable search capabilities
- Flexible product specifications without schema changes

## Database Structure

### Tables Created

1. **`product_attribute_keys`** - Stores the shared attribute types
2. **`product_attributes`** - Stores the actual values for each product-attribute combination

### Migration Files

- `2025_05_25_032929_create_product_attribute_keys_table.php`
- `2025_05_25_032936_create_product_attributes_table.php`

## Models

### ProductAttributeKey Model

Represents the shared attribute types (DPI, Weight, etc.)

**Key Fields:**
- `name` - Internal identifier (e.g., 'dpi', 'weight')
- `display_name` - Human-readable name (e.g., 'DPI', 'Weight')
- `data_type` - Data validation type ('string', 'integer', 'decimal', 'boolean')
- `unit` - Display unit (e.g., 'DPI', 'grams', 'Hz')
- `is_filterable` - Whether this attribute appears in search filters
- `sort_order` - Display order in UI

**Key Methods:**
- `scopeFilterable()` - Get only filterable attributes
- `scopeOrdered()` - Order by sort_order
- `getUniqueValuesAttribute()` - Get all unique values for this attribute

### ProductAttribute Model

Represents the actual attribute values for products.

**Key Fields:**
- `product_id` - Foreign key to products table
- `attribute_key_id` - Foreign key to product_attribute_keys table
- `value` - The actual attribute value (stored as text)

**Key Methods:**
- `getFormattedValueAttribute()` - Returns formatted value with units
- `getCastValueAttribute()` - Returns value cast to appropriate data type

### Product Model (Updated)

Added new relationships and methods for attribute handling.

**New Relationships:**
- `attributes()` - HasMany relationship to ProductAttribute
- `attributeKeys()` - BelongsToMany relationship through pivot table

**New Methods:**
- `getProductAttributeValue($keyName)` - Get specific attribute value by key name
- `getFormattedAttributesAttribute()` - Get all attributes as formatted key-value pairs

## Usage Examples

### 1. Creating Attribute Keys

```php
use App\Models\ProductAttributeKey;

ProductAttributeKey::create([
    'name' => 'dpi',
    'display_name' => 'DPI',
    'data_type' => 'integer',
    'unit' => 'DPI',
    'is_filterable' => true,
    'sort_order' => 1,
]);
```

### 2. Adding Attributes to Products

```php
use App\Models\ProductAttribute;

ProductAttribute::create([
    'product_id' => 1,
    'attribute_key_id' => 1, // DPI key
    'value' => '1600'
]);
```

### 3. Querying Products by Attributes

```php
// Find products with DPI > 1000
$products = Product::whereHas('attributes', function ($query) {
    $query->whereHas('attributeKey', function ($subQuery) {
        $subQuery->where('name', 'dpi');
    })->where('value', '>', 1000);
})->get();

// Get a specific attribute value
$dpiValue = $product->getProductAttributeValue('dpi');

// Get all formatted attributes
$attributes = $product->formatted_attributes;
// Returns: ['DPI' => '1600 DPI', 'Weight' => '85.50 grams', ...]
```

### 4. Getting Filterable Attributes for Search UI

```php
$filterableAttributes = ProductAttributeKey::filterable()->ordered()->get();

foreach ($filterableAttributes as $attribute) {
    echo $attribute->display_name . ': ';
    $uniqueValues = $attribute->unique_values;
    // Display as filter options
}
```

### 5. Advanced Filtering

```php
// Filter products by multiple attributes
$products = Product::whereHas('attributes', function ($query) {
    $query->whereHas('attributeKey', function ($subQuery) {
        $subQuery->where('name', 'dpi');
    })->whereBetween('value', [800, 1600]);
})->whereHas('attributes', function ($query) {
    $query->whereHas('attributeKey', function ($subQuery) {
        $subQuery->where('name', 'color');
    })->where('value', 'Black');
})->get();
```

## Sample Data

The system includes seeders with sample attribute keys:

- **DPI** (integer, DPI) - Mouse sensitivity
- **Weight** (decimal, grams) - Product weight
- **Number of Keys** (integer, keys) - Keyboard/mouse buttons
- **Color** (string) - Product color
- **Wireless** (boolean) - Wireless capability
- **Battery Life** (integer, hours) - Battery duration
- **Connectivity** (string) - Connection type
- **Sensor Type** (string) - Mouse sensor type
- **Polling Rate** (integer, Hz) - Response rate
- **Dimensions** (string, mm) - Physical dimensions

## Running the System

1. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

2. **Seed Attribute Keys:**
   ```bash
   php artisan db:seed --class=ProductAttributeKeySeeder
   ```

3. **Seed Sample Product Attributes (optional):**
   ```bash
   php artisan db:seed --class=ProductAttributeSeeder
   ```

## Benefits

1. **Flexibility** - Add new attribute types without database schema changes
2. **Consistency** - Shared attribute keys ensure consistent naming
3. **Searchability** - Easy filtering and searching by attributes
4. **Type Safety** - Data type validation and proper formatting
5. **Scalability** - Efficient queries with proper indexing
6. **User Experience** - Rich product specifications and filtering

## Database Constraints

- Unique constraint on (`product_id`, `attribute_key_id`) ensures one value per product per attribute
- Foreign key constraints maintain data integrity
- Cascade deletes ensure cleanup when products or attribute keys are removed

## Future Enhancements

- Add validation rules based on data_type
- Implement attribute groups/categories
- Add support for multi-value attributes
- Create admin interface for managing attribute keys
- Add attribute-based product recommendations
