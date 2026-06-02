<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'content', 'type', 'status', 'is_top', 'is_popup',
        'is_marquee', 'bg_color', 'icon', 'target_users', 'position', 'read_count',
        'published_at', 'expires_at', 'sort_order',
    ];

    protected $casts = [
        'is_top' => 'boolean',
        'is_popup' => 'boolean',
        'is_marquee' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // 已读用户
    public function readUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notice_reads')->withTimestamps();
    }

    // 类型名称
    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'system' => '系统公告',
            'activity' => '活动公告',
            'update' => '更新日志',
            'maintenance' => '维护通知',
            default => '其他',
        };
    }

    // 状态名称
    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            0 => '草稿',
            1 => '已发布',
            2 => '已下线',
            default => '未知',
        };
    }

    // 是否已过期
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // 是否有效（已发布且未过期）
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 1 && !$this->is_expired;
    }

    // 作用域：已发布
    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    // 作用域：有效（已发布且未过期）
    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    // 作用域：置顶优先
    public function scopeOrderByTop($query)
    {
        return $query->orderBy('is_top', 'desc')
            ->orderBy('sort_order', 'desc')
            ->orderBy('published_at', 'desc');
    }
}
