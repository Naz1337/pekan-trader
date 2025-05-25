<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $with = ['seller'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'price',
        'stock_quantity',
        'image_path',
        'delivery_fee',
        'is_published',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'is_published' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class)->withTimestamps()->withPivot('quantity');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the thumbnail image for this product.
     */
    public function thumbnail()
    {
        return $this->hasOne(ProductImage::class)->where('is_thumbnail', true);
    }

    /**
     * Get all images ordered by their order field.
     */
    public function getOrderedImagesAttribute()
    {
        return $this->images()->ordered()->get();
    }

    /**
     * Get the main image URL (backward compatible).
     * Returns thumbnail if available, otherwise falls back to image_path.
     */
    public function getMainImageUrlAttribute(): string
    {
        // First try to get thumbnail from product_images table
        $thumbnail = $this->thumbnail;
        if ($thumbnail) {
            return $thumbnail->image_url;
        }

        // Fall back to the old image_path column if it exists
        if ($this->image_path) {
            if (str_starts_with($this->image_path, 'http')) {
                return $this->image_path;
            }
            return asset('storage/' . $this->image_path);
        }

        // Return a default image if no image is found
        return asset('imgs/placeholder.png');
    }

    /**
     * Get all image URLs for this product.
     */
    public function getAllImageUrlsAttribute(): array
    {
        $imageUrls = $this->ordered_images->pluck('image_url')->toArray();

        // If no images in product_images table, fall back to image_path
        if (empty($imageUrls) && $this->image_path) {
            $imageUrls[] = $this->main_image_url;
        }

        return $imageUrls;
    }

    /**
     * Check if product has multiple images.
     */
    public function hasMultipleImages(): bool
    {
        return $this->images()->count() > 1;
    }

    /**
     * Get the first image (thumbnail or first ordered image).
     */
    public function getFirstImageAttribute()
    {
        $thumbnail = $this->thumbnail;
        if ($thumbnail) {
            return $thumbnail;
        }

        return $this->images()->ordered()->first();
    }
}
