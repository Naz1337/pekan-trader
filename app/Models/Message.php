<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'content',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        if ($this->sender_type === 'user') {
            return User::find($this->sender_id);
        } elseif ($this->sender_type === 'seller') {
            return Seller::find($this->sender_id);
        }

        return null;
    }

    public function getSenderNameAttribute(): string
    {
        $sender = $this->sender();

        if ($this->sender_type === 'user') {
            return $sender ? $sender->name : 'Unknown User';
        } elseif ($this->sender_type === 'seller') {
            return $sender ? $sender->shop_name : 'Unknown Seller';
        }

        return 'Unknown';
    }
}
