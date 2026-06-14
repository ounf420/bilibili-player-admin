<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'card_no', 'card_secret', 'amount', 'status',
        'used_by', 'used_at', 'batch_id', 'remark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    // 生成卡号
    public static function generateCardNo(): string
    {
        return strtoupper(str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT));
    }

    // 生成卡密
    public static function generateSecret(): string
    {
        return strtoupper(bin2hex(random_bytes(4)));
    }
}
