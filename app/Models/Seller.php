<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        "bank_account_number"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
