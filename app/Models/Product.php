<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $with = ['seller'];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
