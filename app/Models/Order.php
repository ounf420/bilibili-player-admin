<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_no', 'user_id', 'plan_id', 'player_id', 'amount',
        'status', 'pay_method', 'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(PlayerPlan::class, 'plan_id');
    }

    // 生成订单号
    public static function generateOrderNo(): string
    {
        return date('YmdHis') . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // 状态中文
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'pending' => '待支付',
            'paid' => '已支付',
            'cancelled' => '已取消',
            'refunded' => '已退款',
            default => $this->status
        };
    }
}
