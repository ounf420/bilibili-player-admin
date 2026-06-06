<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username', 'email', 'phone', 'password', 'avatar', 'nickname',
        'status', 'is_admin',  'real_name', 'id_card',
        'gender', 'birthday', 'wechat_openid', 'qq_openid', 'weibo_uid',
        'github_id', 'verify_code', 'verify_code_expire',
        'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token', 'verify_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'verify_code_expire' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }



    // 状态名称
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            0 => '禁用',
            default => '正常',
        };
    }

    // 性别名称
    public function getGenderNameAttribute(): string
    {
        return match($this->gender) {
            1 => '男',
            2 => '女',
            default => '未知',
        };
    }

    // 是否是管理员
    public function isAdmin(): bool
    {
        return $this->is_admin == 1;
    }

    // 已读公告
    public function readNotices()
    {
        return $this->belongsToMany(Notice::class, 'notice_reads')->withTimestamps();
    }
}
