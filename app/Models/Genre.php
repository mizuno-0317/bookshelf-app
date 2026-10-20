<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /** 書籍に属するジャンルの取得 */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}
