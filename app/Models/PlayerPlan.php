<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerPlan extends Model
{
    protected $fillable = [
        'name', 'code', 'type', 'level', 'duration_type', 'duration_days',
        'price', 'sale_price', 'price_monthly', 'price_yearly',
        'price_permanent', 'player_limit', 'features', 'description',
        'badge', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    const LEVEL_FREE = 0;
    const LEVEL_BASIC = 1;
    const LEVEL_PREMIUM = 2;
    const LEVEL_ULTIMATE = 3;

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * 检查是否支持某功能（基于 features 数组的 key-value）
     */
    public function canDo(string $feature): bool
    {
        $features = $this->features ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }

    /**
     * 获取版本等级文本
     */
    public function getLevelTextAttribute(): string
    {
        return match ($this->level) {
            self::LEVEL_FREE => '免费版',
            self::LEVEL_BASIC => '基础版',
            self::LEVEL_PREMIUM => '高级版',
            self::LEVEL_ULTIMATE => '旗舰版',
            default => '未知',
        };
    }

    /**
     * 获取时长类型文本
     */
    public function getDurationTypeTextAttribute(): string
    {
        return match ($this->duration_type) {
            0 => '无期限',
            1 => '月卡',
            2 => '季卡',
            3 => '年卡',
            4 => '永久',
            default => '-',
        };
    }

    /**
     * 获取版本颜色
     */
    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            self::LEVEL_FREE => '#94a3b8',
            self::LEVEL_BASIC => '#3b82f6',
            self::LEVEL_PREMIUM => '#8b5cf6',
            self::LEVEL_ULTIMATE => '#f59e0b',
            default => '#94a3b8',
        };
    }
}
