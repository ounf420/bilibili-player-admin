<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlayerAd extends Model
{
    protected $fillable = [
        'player_id', 'target_type', 'target_player_ids',
        'name', 'media_url', 'media_type', 'content',
        'cover_url', 'title', 'description',
        'cta_text', 'cta_url', 'logo_url',
        'click_url', 'position', 'duration', 'skippable',
        'skip_after', 'progress_icon', 'enabled', 'sort_order',
        'start_at', 'end_at', 'frequency_cap', 'priority',
    ];

    protected $casts = [
        'target_player_ids' => 'array',
        'skippable' => 'boolean',
        'enabled' => 'boolean',
        'duration' => 'integer',
        'skip_after' => 'integer',
        'sort_order' => 'integer',
        'frequency_cap' => 'integer',
        'priority' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // 投放类型常量
    const TARGET_SINGLE = 'single';
    const TARGET_MULTIPLE = 'multiple';
    const TARGET_ALL = 'all';

    public function player()
    {
        return $this->belongsTo(UserPlayer::class, 'player_id');
    }

    /**
     * 获取目标播放器列表
     */
    public function getTargetPlayers($userId)
    {
        if ($this->target_type === self::TARGET_ALL) {
            return UserPlayer::where('user_id', $userId)->active()->get();
        }
        
        if ($this->target_type === self::TARGET_MULTIPLE && $this->target_player_ids) {
            return UserPlayer::whereIn('id', $this->target_player_ids)->active()->get();
        }
        
        // single
        return UserPlayer::where('id', $this->player_id)->active()->get();
    }

    /**
     * 检查是否针对某个播放器
     */
    public function targetsPlayer(int $playerId): bool
    {
        if ($this->target_type === self::TARGET_ALL) {
            return true;
        }
        
        if ($this->target_type === self::TARGET_MULTIPLE) {
            return in_array($playerId, $this->target_player_ids ?? []);
        }
        
        return $this->player_id === $playerId;
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeForPlayer($query, int $playerId)
    {
        return $query->where(function ($q) use ($playerId) {
            $q->where('target_type', self::TARGET_ALL)
              ->orWhere(function ($q2) use ($playerId) {
                  $q2->where('target_type', self::TARGET_SINGLE)
                     ->where('player_id', $playerId);
              })
              ->orWhere(function ($q2) use ($playerId) {
                  $q2->where('target_type', self::TARGET_MULTIPLE)
                     ->whereJsonContains('target_player_ids', $playerId);
              });
        });
    }
}
