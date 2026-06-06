<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_quota',
        'used_quota',
        'bonus_quota',
    ];

    protected $casts = [
        'total_quota' => 'integer',
        'used_quota' => 'integer',
        'bonus_quota' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取剩余可用额度
     */
    public function getAvailableQuotaAttribute()
    {
        return $this->total_quota - $this->used_quota;
    }

    /**
     * 增加额度
     */
    public function addQuota($amount)
    {
        $this->bonus_quota += $amount;
        $this->total_quota += $amount;
        $this->save();
    }

    /**
     * 使用额度
     */
    public function useQuota()
    {
        if ($this->available_quota <= 0) {
            return false;
        }
        $this->used_quota++;
        $this->save();
        return true;
    }

    /**
     * 释放额度
     */
    public function releaseQuota()
    {
        if ($this->used_quota > 0) {
            $this->used_quota--;
            $this->save();
            return true;
        }
        return false;
    }
}
