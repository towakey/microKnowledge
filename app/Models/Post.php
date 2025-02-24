<?php

namespace App\Models;

use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Post;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class Post extends Model
{
    use HasFactory, Taggable, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'visibility',
        'user_id',
        'parent_id',
        'display_type'
    ];

    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_CONFIDENTIAL = 'confidential';

    // 表示形式の定数
    const DISPLAY_TYPE_TEXT = 0;
    const DISPLAY_TYPE_MARKDOWN = 1;

    public static function getVisibilityOptions()
    {
        return [
            self::VISIBILITY_PUBLIC => '公開',
            self::VISIBILITY_PRIVATE => '非公開',
            self::VISIBILITY_CONFIDENTIAL => '機密'
        ];
    }

    // 表示形式のオプション
    public static function getDisplayTypeOptions(): array
    {
        return [
            self::DISPLAY_TYPE_TEXT => 'テキスト',
            self::DISPLAY_TYPE_MARKDOWN => 'Markdown'
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

    public function getFormattedContent(): string
    {
        if ($this->display_type === self::DISPLAY_TYPE_MARKDOWN) {
            $environment = new Environment();
            $environment->addExtension(new CommonMarkCoreExtension());
            
            $converter = new MarkdownConverter($environment);
            return $converter->convert($this->content)->getContent();
        }

        return nl2br(e($this->content));
    }
}
