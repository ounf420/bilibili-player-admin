<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'level',
        'duration_type',
        'duration_days',
        'price',
        'sale_price',
        'features',
        'badge',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // 时长类型常量
    const DURATION_MONTH = 1;
    const DURATION_QUARTER = 2;
    const DURATION_YEAR = 3;
    const DURATION_PERMANENT = 4;

    // 版本等级常量
    const LEVEL_BASIC = 1;
    const LEVEL_PRO = 2;
    const LEVEL_ULTIMATE = 3;

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getDurationTypeTextAttribute()
    {
        return match ($this->duration_type) {
            self::DURATION_MONTH => '月',
            self::DURATION_QUARTER => '季',
            self::DURATION_YEAR => '年',
            self::DURATION_PERMANENT => '永久',
            default => '未知',
        };
    }

    public function getLevelTextAttribute()
    {
        return match ($this->level) {
            self::LEVEL_BASIC => '基础版',
            self::LEVEL_PRO => '专业版',
            self::LEVEL_ULTIMATE => '旗舰版',
            default => '未知',
        };
    }
}
