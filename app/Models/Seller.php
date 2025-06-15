<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Seller extends Model
{
    protected $fillable = [
        "business_name",
        "business_description",
        "business_address",
        "business_phone",
        "business_email",
        "logo_url",
        "opening_hour",
        "closing_hour",
        "facebook",
        "instagram",
        "ic_number",
        "business_cert_url",
        "bank_name",
        "bank_account_name",
        "bank_account_number",
        "approved",
        "approved_at"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->logo_url ? Storage::url($this->logo_url) : '/imgs/user-icon.png';
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
