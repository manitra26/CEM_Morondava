<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'posting_mode',
        'image_path',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'discussion_group_user')
            ->withPivot('joined_at', 'can_post');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
