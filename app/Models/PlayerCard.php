<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_no',
        'card_secret',
        'card_type',
        'plan_id',
        'quota_amount',
        'status',
        'used_by',
        'used_at',
        'created_by',
        'remark',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    // 状态常量
    const STATUS_UNUSED = 0;
    const STATUS_USED = 1;
    const STATUS_DISABLED = 2;

    // 卡类型常量
    const TYPE_PLAN = 'plan';
    const TYPE_QUOTA = 'quota';

    public function plan()
    {
        return $this->belongsTo(PlayerPlan::class, 'plan_id');
    }

    public function usedByUser()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeUnused($query)
    {
        return $query->where('status', self::STATUS_UNUSED);
    }

    public function scopePlanCards($query)
    {
        return $query->where('card_type', self::TYPE_PLAN);
    }

    public function scopeQuotaCards($query)
    {
        return $query->where('card_type', self::TYPE_QUOTA);
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_UNUSED => '未使用',
            self::STATUS_USED => '已使用',
            self::STATUS_DISABLED => '已禁用',
            default => '未知',
        };
    }

    public function getCardTypeTextAttribute()
    {
        return match ($this->card_type) {
            self::TYPE_PLAN => '版本卡',
            self::TYPE_QUOTA => '额度卡',
            default => '未知',
        };
    }

    /**
     * 生成卡号
     */
    public static function generateCardNo()
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 16));
    }

    /**
     * 生成卡密
     */
    public static function generateCardSecret()
    {
        return strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 24));
    }
}
