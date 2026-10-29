<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /** ユーザーが持つレビューの情報を取得 */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** ユーザーが持つお気に入り書籍の取得 */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites');
    }

    /** ユーザーが持ついいね情報取得 */
    public function reviewLikes(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_likes');
    }

    /** ユーザーが登録した書籍情報を取得 */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
