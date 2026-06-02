<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 重命名name为username
            $table->renameColumn('name', 'username');
            
            // 添加新字段
            $table->string('phone', 20)->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('password');
            $table->string('nickname')->default('')->after('avatar');
            $table->tinyInteger('status')->default(1)->after('nickname')->comment('状态: 0=禁用 1=正常');
            $table->tinyInteger('vip_level')->default(0)->after('status')->comment('VIP等级: 0=普通 1=白银 2=黄金 3=钻石');
            $table->timestamp('vip_expire_at')->nullable()->after('vip_level')->comment('VIP到期时间');
            $table->string('real_name')->nullable()->after('vip_expire_at')->comment('真实姓名');
            $table->string('id_card')->nullable()->after('real_name')->comment('身份证号');
            $table->tinyInteger('gender')->default(0)->after('id_card')->comment('性别: 0=未知 1=男 2=女');
            $table->date('birthday')->nullable()->after('gender')->comment('生日');
            $table->string('wechat_openid')->nullable()->after('birthday')->comment('微信OpenID');
            $table->string('qq_openid')->nullable()->after('wechat_openid')->comment('QQ OpenID');
            $table->string('weibo_uid')->nullable()->after('qq_openid')->comment('微博UID');
            $table->string('github_id')->nullable()->after('weibo_uid')->comment('GitHub ID');
            $table->string('verify_code')->nullable()->after('github_id')->comment('验证码');
            $table->timestamp('verify_code_expire')->nullable()->after('verify_code')->comment('验证码过期时间');
            $table->timestamp('last_login_at')->nullable()->after('remember_token')->comment('最后登录时间');
            $table->string('last_login_ip')->nullable()->after('last_login_at')->comment('最后登录IP');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->dropColumn([
                'phone', 'avatar', 'nickname', 'status', 'vip_level', 'vip_expire_at',
                'real_name', 'id_card', 'gender', 'birthday', 'wechat_openid', 'qq_openid',
                'weibo_uid', 'github_id', 'verify_code', 'verify_code_expire',
                'last_login_at', 'last_login_ip', 'deleted_at'
            ]);
        });
    }
};
