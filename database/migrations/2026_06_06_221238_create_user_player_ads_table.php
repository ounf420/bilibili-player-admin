<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_player_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('user_players')->onDelete('cascade');
            $table->string('name', 100)->comment('广告名称');
            $table->text('media_url')->comment('素材地址');
            $table->enum('media_type', ['video', 'image'])->default('video')->comment('素材类型');
            $table->text('click_url')->nullable()->comment('点击链接');
            $table->enum('position', ['preroll', 'midroll', 'postroll', 'pause'])->default('preroll')->comment('展示位置');
            $table->integer('duration')->default(15)->comment('广告时长(秒)');
            $table->boolean('skippable')->default(true)->comment('可跳过');
            $table->integer('skip_after')->default(5)->comment('几秒后可跳过');
            $table->boolean('enabled')->default(true)->comment('启用状态');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
        });

        // 给user_players表添加广告模式字段
        Schema::table('user_players', function (Blueprint $table) {
            $table->enum('ad_mode', ['user', 'platform', 'mixed', 'none'])->default('platform')->after('ad_decoration_id')->comment('广告模式');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_player_ads');
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn('ad_mode');
        });
    }
};
