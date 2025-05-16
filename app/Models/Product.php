<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $with = ['seller'];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class)->withTimestamps()->withPivot('quantity');
    }
}
