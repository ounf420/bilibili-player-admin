<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBalance extends Model
{
    protected $fillable = ['user_id', 'balance', 'total_recharged', 'total_spent'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 获取或创建用户余额
    public static function getOrCreate(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'total_recharged' => 0, 'total_spent' => 0]
        );
    }

    // 充值
    public function recharge(float $amount): bool
    {
        $this->balance += $amount;
        $this->total_recharged += $amount;
        return $this->save();
    }

    // 消费
    public function spend(float $amount): bool
    {
        if ($this->balance < $amount) {
            return false;
        }
        $this->balance -= $amount;
        $this->total_spent += $amount;
        return $this->save();
    }
}
