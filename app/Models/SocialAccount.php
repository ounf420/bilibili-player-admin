<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id', 'platform', 'social_uid', 'access_token',
        'nickname', 'avatar', 'gender', 'location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 平台名称映射
    public static function platformName(string $platform): string
    {
        return match ($platform) {
            'qq' => 'QQ',
            'wx' => '微信',
            'alipay' => '支付宝',
            'sina' => '微博',
            'baidu' => '百度',
            'douyin' => '抖音',
            'huawei' => '华为',
            'xiaomi' => '小米',
            'microsoft' => '微软',
            'feishu' => '飞书',
            'dingtalk' => '钉钉',
            'gitee' => 'Gitee',
            'github' => 'GitHub',
            default => $platform,
        };
    }

    // 平台图标
    public static function platformIcon(string $platform): string
    {
        return match ($platform) {
            'qq' => 'fab fa-qq',
            'wx' => 'fab fa-weixin',
            'alipay' => 'fab fa-alipay',
            'sina' => 'fab fa-weibo',
            'baidu' => 'fab fa-baidu',
            'douyin' => 'fab fa-tiktok',
            'github' => 'fab fa-github',
            'gitee' => 'fab fa-git-alt',
            'microsoft' => 'fab fa-microsoft',
            'dingtalk' => 'fab fa-dingtalk',
            default => 'fas fa-link',
        };
    }
}
