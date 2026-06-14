<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'balance_after',
        'description', 'related_id', 'related_type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 类型中文
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'recharge' => '充值',
            'purchase' => '消费',
            'refund' => '退款',
            default => $this->type
        };
    }
}
