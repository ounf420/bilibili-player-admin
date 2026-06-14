<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Series extends Model
{
    protected $fillable = [
        'user_id', 'title', 'cover', 'description',
        'episode_count', 'is_ending', 'sort_order',
    ];

    protected $casts = [
        'is_ending' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class)->orderBy('episode_number');
    }

    // 获取该剧在指定播放器中的视频列表
    public function videosForPlayer($playerId)
    {
        return $this->videos()
            ->whereHas('playerVideos', function ($q) use ($playerId) {
                $q->where('player_id', $playerId);
            })
            ->orderBy('episode_number')
            ->get();
    }
}
