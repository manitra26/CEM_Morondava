<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @use HasFactory<UserFactory>
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'department',
        'phone',
        'bio',
        'domicile',
        'avatar_path',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isDirector(): bool
    {
        return $this->role === 'directeur';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employe';
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(DiscussionGroup::class, 'discussion_group_user')
            ->withPivot('joined_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(InternalNotification::class);
    }

    public function getFormattedPhoneAttribute(): string
    {
        return self::formatPhone($this->phone);
    }

    public static function formatPhone(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '261')) {
            $digits = '0'.substr($digits, 3);
        }
        if (strlen($digits) > 10) {
            $digits = substr($digits, 0, 10);
        }

        $parts = [];
        if (strlen($digits) > 0) {
            $parts[] = substr($digits, 0, 3);
        }
        if (strlen($digits) > 3) {
            $parts[] = substr($digits, 3, 2);
        }
        if (strlen($digits) > 5) {
            $parts[] = substr($digits, 5, 3);
        }
        if (strlen($digits) > 8) {
            $parts[] = substr($digits, 8, 2);
        }

        return implode(' ', $parts);
    }
}
