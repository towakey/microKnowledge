<?php

namespace App\Models;

use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Post;

class Post extends Model
{
    use HasFactory, Taggable, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'visibility',
        'user_id',
        'parent_id'
    ];

    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_CONFIDENTIAL = 'confidential';

    public static function getVisibilityOptions()
    {
        return [
            self::VISIBILITY_PUBLIC => '公開',
            self::VISIBILITY_PRIVATE => '非公開',
            self::VISIBILITY_CONFIDENTIAL => '機密'
        ];
    }

    public function isVisibleTo($user)
    {
        if ($this->visibility === self::VISIBILITY_PUBLIC) {
            return true;
        }
        
        return $user && $this->user_id === $user->id;
    }

    public function shouldHideContent($user)
    {
        return $this->visibility === self::VISIBILITY_CONFIDENTIAL && (!$user || $this->user_id !== $user->id);
    }

    public function getTagListAttribute()
    {
        return $this->tagNames();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Post::class, 'parent_id');
    }
}
