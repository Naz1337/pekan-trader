# Product Images Implementation

This document describes the implementation of multiple images support for products in the Pekan Trader application.

## Database Structure

### Tables

1. **products** - Existing table with `image_path` column (now nullable for backward compatibility)
2. **product_images** - New table for storing multiple product images

### product_images Table Schema

```sql
- id (primary key)
- product_id (foreign key to products table, cascade delete)
- image_path (string, stores the image file path)
- is_thumbnail (boolean, marks the main/thumbnail image)
- order (integer, for image ordering, nullable)
- created_at, updated_at (timestamps)
```

## Eloquent Models

### ProductImage Model

**Location:** `app/Models/ProductImage.php`

**Features:**
- Mass assignable attributes: `product_id`, `image_path`, `is_thumbnail`, `order`
- Proper type casting for boolean and integer fields
- Relationship to Product model
- Scopes for filtering and ordering
- Image URL accessor methods

**Key Methods:**
- `product()` - BelongsTo relationship to Product
- `scopeThumbnail($query)` - Scope for thumbnail images
- `scopeOrdered($query)` - Scope for ordered images
- `getImageUrlAttribute()` - Accessor for full image URL
- `getFullImagePathAttribute()` - Accessor for image path

### Product Model Updates

**Location:** `app/Models/Product.php`

**New Features Added:**
- Mass assignable attributes defined
- Type casting for price, delivery_fee, etc.
- Multiple image relationships and helper methods
- Backward compatibility with existing `image_path` column

**Key Methods:**
- `images()` - HasMany relationship to ProductImage
- `thumbnail()` - HasOne relationship for thumbnail image
- `getOrderedImagesAttribute()` - Accessor for ordered images
- `getMainImageUrlAttribute()` - Backward compatible main image URL
- `getAllImageUrlsAttribute()` - Array of all image URLs
- `hasMultipleImages()` - Check if product has multiple images
- `getFirstImageAttribute()` - Get first/thumbnail image

## Usage Examples

### Basic Usage

```php
// Get a product
$product = Product::find(1);

// Get all images
$images = $product->images;

// Get thumbnail image
$thumbnail = $product->thumbnail;

// Get ordered images
$orderedImages = $product->ordered_images;

// Get main image URL (backward compatible)
$mainImageUrl = $product->main_image_url;

// Get all image URLs as array
$allImageUrls = $product->all_image_urls;

// Check if product has multiple images
if ($product->hasMultipleImages()) {
    // Handle multiple images display
}
```

### Creating Product Images

```php
// Create a new product image
ProductImage::create([
    'product_id' => $product->id,
    'image_path' => 'products/image1.jpg',
    'is_thumbnail' => true,
    'order' => 1
]);

// Add multiple images
$imageData = [
    ['image_path' => 'products/image1.jpg', 'is_thumbnail' => true, 'order' => 1],
    ['image_path' => 'products/image2.jpg', 'is_thumbnail' => false, 'order' => 2],
    ['image_path' => 'products/image3.jpg', 'is_thumbnail' => false, 'order' => 3],
];

foreach ($imageData as $data) {
    $product->images()->create($data);
}
```

### Querying Images

```php
// Get only thumbnail images
$thumbnails = ProductImage::thumbnail()->get();

// Get images ordered by order field
$orderedImages = ProductImage::ordered()->get();

// Get product with images
$product = Product::with('images')->find(1);

// Get product with only thumbnail
$product = Product::with('thumbnail')->find(1);
```

## Backward Compatibility

The implementation maintains full backward compatibility:

1. **Existing `image_path` column** - Still works and is used as fallback
2. **Main image URL accessor** - Automatically falls back to `image_path` if no thumbnail exists
3. **Gradual migration** - Products can use either old or new image system

## Migration Path

1. **Immediate** - All existing functionality continues to work
2. **Gradual** - New products can use multiple images
3. **Optional** - Existing products can be migrated to use multiple images when needed

## Image URL Handling

The system handles different image path formats:

1. **Relative paths** - Automatically prefixed with `asset('storage/')`
2. **Absolute URLs** - Used as-is (for external images)
3. **Fallback** - Default placeholder image if no image found

## Best Practices

1. **Always set one thumbnail** - Mark one image as `is_thumbnail = true`
2. **Use order field** - For consistent image display order
3. **Optimize images** - Store different sizes if needed
4. **Validate uploads** - Ensure proper image formats and sizes
5. **Clean up** - Remove orphaned images when products are deleted (handled by cascade delete)

## Database Relationships

```
Product (1) -----> (Many) ProductImage
- id                - id
- name              - product_id (FK)
- description       - image_path
- price             - is_thumbnail
- image_path        - order
- ...               - timestamps
```

## Testing

All relationships and methods have been tested and verified to work correctly:
- ✓ Model instantiation
- ✓ Eloquent relationships
- ✓ Query scopes
- ✓ Accessor methods
- ✓ Backward compatibility

## Next Steps

1. Update product creation/editing forms to handle multiple images
2. Update product display views to show multiple images
3. Implement image upload handling for multiple files
4. Add image management interface for sellers
5. Consider image optimization and resizing
