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
        try {
            $sender = $this->sender();

            if ($this->sender_type === 'user') {
                return $sender->name ?? 'Unknown User';
            }

            if ($this->sender_type === 'seller') {
                return $sender->business_name ?? 'Unknown Seller';
            }

            return 'Unknown Sender';
        } catch (\Exception $e) {
            // Log detailed error for debugging
            \Log::error("Sender name error for message {$this->id}: " . $e->getMessage());
            return 'Error: Sender Unknown';
        }
    }
}
