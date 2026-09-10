<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'reply_to_id',
        'content',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'read_at',
        'deleted_for_sender_at',
        'deleted_for_recipient_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'deleted_for_sender_at' => 'datetime',
            'deleted_for_recipient_at' => 'datetime',
        ];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user): void {
            $query->where(function (Builder $query) use ($user): void {
                $query->where('sender_id', $user->id)->whereNull('deleted_for_sender_at');
            })->orWhere(function (Builder $query) use ($user): void {
                $query->where('recipient_id', $user->id)->whereNull('deleted_for_recipient_at');
            });
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PrivateMessageReaction::class);
    }
}
