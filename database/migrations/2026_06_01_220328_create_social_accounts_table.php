<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform', 20)->comment('平台: qq,wx,alipay,sina,baidu,douyin,huawei,xiaomi,microsoft,feishu,dingtalk,gitee,github');
            $table->string('social_uid')->comment('第三方用户ID');
            $table->string('access_token')->nullable()->comment('第三方Token');
            $table->string('nickname')->nullable()->comment('第三方昵称');
            $table->string('avatar')->nullable()->comment('第三方头像');
            $table->string('gender')->nullable()->comment('性别');
            $table->string('location')->nullable()->comment('所在地');
            $table->timestamps();

            $table->unique(['platform', 'social_uid']);
            $table->index(['user_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
