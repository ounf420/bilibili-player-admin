<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'videos';
    
    protected $fillable = [
        'series_id',
        'episode_number',
        'title',
        'url',
        'cover',
        'type',
        'duration',
        'description',
        'category',
        'genre',
        'region',
        'year',
        'language',
        'tags',
        'actors',
        'director',
        'score',
        'views',
        'likes',
        'quality',
        'episode_count',
        'is_ending',
        'is_recommend',
        'sort_order',
        'enabled',
    ];
    
    protected $casts = [
        'is_ending' => 'boolean',
        'is_recommend' => 'boolean',
        'enabled' => 'boolean',
        'score' => 'decimal:1',
    ];

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function playerVideos()
    {
        return $this->hasMany(UserPlayerVideo::class);
    }

    public function players()
    {
        return $this->belongsToMany(UserPlayer::class, 'user_player_videos')
            ->withPivot('sort_order', 'is_featured')
            ->withTimestamps();
    }
}
