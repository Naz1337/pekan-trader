<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'attribute_key_id',
        'value',
    ];

    /**
     * Get the product that owns this attribute.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute key for this attribute.
     */
    public function attributeKey(): BelongsTo
    {
        return $this->belongsTo(ProductAttributeKey::class, 'attribute_key_id');
    }

    /**
     * Get the formatted value based on the attribute key's data type.
     */
    public function getFormattedValueAttribute(): string
    {
        $attributeKey = $this->attributeKey;

        if (!$attributeKey) {
            return $this->value;
        }

        $value = $this->value;

        switch ($attributeKey->data_type) {
            case 'integer':
                $formatted = number_format((int) $value);
                break;
            case 'decimal':
                $formatted = number_format((float) $value, 2);
                break;
            case 'boolean':
                $formatted = $value ? 'Yes' : 'No';
                break;
            default:
                $formatted = $value;
        }

        // Add unit if available
        if ($attributeKey->unit) {
            $formatted .= ' ' . $attributeKey->unit;
        }

        return $formatted;
    }

    /**
     * Get the raw value cast to the appropriate type.
     */
    public function getCastValueAttribute()
    {
        $attributeKey = $this->attributeKey;

        if (!$attributeKey) {
            return $this->value;
        }

        switch ($attributeKey->data_type) {
            case 'integer':
                return (int) $this->value;
            case 'decimal':
                return (float) $this->value;
            case 'boolean':
                return (bool) $this->value;
            default:
                return $this->value;
        }
    }
}
