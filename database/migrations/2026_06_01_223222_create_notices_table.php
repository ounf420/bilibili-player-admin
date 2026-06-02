<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('公告标题');
            $table->text('content')->comment('公告内容');
            $table->string('type', 20)->default('system')->comment('类型: system/activity/update/maintenance');
            $table->tinyInteger('status')->default(0)->comment('状态: 0草稿 1已发布 2已下线');
            $table->boolean('is_top')->default(false)->comment('是否置顶');
            $table->boolean('is_popup')->default(false)->comment('是否弹窗显示');
            $table->boolean('is_marquee')->default(false)->comment('是否滚动显示');
            $table->string('bg_color', 20)->nullable()->comment('背景色');
            $table->string('icon', 50)->nullable()->comment('图标');
            $table->string('target_users', 20)->default('all')->comment('目标用户: all/vip/new');
            $table->integer('read_count')->default(0)->comment('阅读量');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->timestamp('expires_at')->nullable()->comment('过期时间');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['type', 'status']);
            $table->index('is_top');
        });

        // 公告已读记录表
        Schema::create('notice_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['notice_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_reads');
        Schema::dropIfExists('notices');
    }
};
