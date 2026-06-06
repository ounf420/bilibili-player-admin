<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'user_id',
        'product_type',
        'product_id',
        'product_name',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'card_no',
        'trade_no',
        'remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // 状态常量
    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_REFUNDED = 3;

    // 产品类型常量
    const TYPE_PLAN = 'plan';
    const TYPE_QUOTA = 'quota';

    // 支付方式常量
    const PAY_CARD = 'card';
    const PAY_ALIPAY = 'alipay';
    const PAY_WECHAT = 'wechat';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => '待支付',
            self::STATUS_PAID => '已支付',
            self::STATUS_CANCELLED => '已取消',
            self::STATUS_REFUNDED => '已退款',
            default => '未知',
        };
    }

    public function getPaymentMethodTextAttribute()
    {
        return match ($this->payment_method) {
            self::PAY_CARD => '卡密兑换',
            self::PAY_ALIPAY => '支付宝',
            self::PAY_WECHAT => '微信支付',
            default => '未知',
        };
    }

    /**
     * 生成订单号
     */
    public static function generateOrderNo()
    {
        return 'PO' . date('YmdHis') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
