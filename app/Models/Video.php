<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Video extends Model
{
    protected $table = 'videos';
    
    protected $keyType = 'string';
    
    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = 'v' . Str::random(8);
            }
        });
    }
    
    protected $fillable = [
        'id', 'title', 'cover', 'url', 'type', 'duration', 'description',
        'category', 'tags', 'enabled', 'views', 'likes',
        'region', 'year', 'genre', 'director', 'actors', 'language',
        'score', 'episode_count', 'is_ending', 'quality',
        'vip_level', 'is_recommend', 'sort_order',
    ];
    
    protected $casts = [
        'enabled' => 'boolean',
        'is_ending' => 'boolean',
        'is_recommend' => 'boolean',
        'score' => 'float',
    ];
    
    // 自动检测视频类型
    public static function detectType(string $url): string
    {
        $url = strtolower(parse_url($url, PHP_URL_PATH) ?? $url);
        if (str_contains($url, '.m3u8')) return 'm3u8';
        if (str_contains($url, '.flv')) return 'flv';
        if (str_contains($url, '.mpd')) return 'dash';
        return 'mp4';
    }
}
