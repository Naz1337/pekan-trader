<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributeKey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'data_type',
        'unit',
        'is_filterable',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_filterable' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all product attributes for this key.
     */
    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'attribute_key_id');
    }

    /**
     * Get all products that have this attribute key.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attributes', 'attribute_key_id', 'product_id')
                    ->withPivot('value')
                    ->withTimestamps();
    }

    /**
     * Scope to get only filterable attributes.
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    /**
     * Get unique values for this attribute key across all products.
     */
    public function getUniqueValuesAttribute()
    {
        return $this->productAttributes()
                    ->distinct('value')
                    ->orderBy('value')
                    ->pluck('value')
                    ->toArray();
    }
}
