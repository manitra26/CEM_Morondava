<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateMessageReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_message_id',
        'user_id',
        'reaction',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(PrivateMessage::class, 'private_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
