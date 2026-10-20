<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'published_at',
        'image_url',
        'isbn',
        'description',
    ];

    /** 書籍が属するお気に入り登録ユーザーを取得する */
    public function favoriteUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /** 書籍が持つジャンル情報を取得する */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    /** 書籍が持つレビュー情報を取得する */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
