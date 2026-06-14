<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlayerVideo extends Model
{
    protected $table = 'user_player_videos';

    protected $fillable = [
        'player_id',
        'video_id',
        'sort_order',
        'is_featured',
    ];

    public function player()
    {
        return $this->belongsTo(UserPlayer::class, 'player_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
