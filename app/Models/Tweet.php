<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    /** @use HasFactory<\Database\Factories\TweetFactory> */
    use HasFactory;

    // 🔽 2つ追加
    protected $fillable = ['tweet', 'image_path'];

    // （Tweet から見て User は 多対1）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function liked()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? \Storage::disk('public')->url($this->image_path) : null;
    }

    public function scopeKeyword(Builder $query, ?string $keyword): Builder
    {
        // キーワードが指定されている場合のみ絞り込む
        return $query->when($keyword, function (Builder $query, string $keyword) {
            // 部分一致（キーワードがどこかに含まれる）
            $query->where('tweet', 'like', '%' . $keyword . '%');

            // 前方一致（キーワードで始まる）
            // $query->where('tweet', 'like', $keyword . '%');

            // 後方一致（キーワードで終わる）
            // $query->where('tweet', 'like', '%' . $keyword);

            // 完全一致（キーワードと同じ文字列）
            // $query->where('tweet', $keyword);
            });
    }
}
