<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 100)->comment('播放器名称');
            $table->string('slug', 50)->unique()->comment('唯一标识');
            
            // 外观配置
            $table->string('theme_color', 20)->default('#6366f1')->comment('主题色');
            $table->string('logo_url', 500)->nullable()->comment('Logo URL');
            $table->string('watermark_text', 50)->nullable()->comment('水印文字');
            $table->string('watermark_position', 20)->default('bottom-right')->comment('水印位置');
            $table->boolean('show_title')->default(true)->comment('显示标题');
            $table->boolean('show_controls')->default(true)->comment('显示控制栏');
            $table->boolean('autoplay')->default(false)->comment('自动播放');
            $table->boolean('loop_play')->default(false)->comment('循环播放');
            $table->boolean('muted')->default(false)->comment('静音');
            $table->boolean('show_danmaku')->default(false)->comment('显示弹幕');
            $table->boolean('allow_danmaku')->default(false)->comment('允许发弹幕');
            
            // 功能配置
            $table->boolean('show_quality')->default(true)->comment('画质切换');
            $table->boolean('show_speed')->default(true)->comment('倍速');
            $table->boolean('show_fullscreen')->default(true)->comment('全屏按钮');
            $table->boolean('show_pip')->default(true)->comment('画中画');
            $table->boolean('show_download')->default(false)->comment('下载按钮');
            $table->boolean('show_share')->default(true)->comment('分享按钮');
            
            // 尺寸配置
            $table->string('width', 20)->default('100%')->comment('宽度');
            $table->string('height', 20)->default('auto')->comment('高度');
            $table->string('aspect_ratio', 10)->default('16:9')->comment('宽高比');
            $table->string('border_radius', 10)->default('12px')->comment('圆角');
            
            // 广告配置
            $table->boolean('show_ads')->default(true)->comment('显示广告');
            $table->unsignedBigInteger('ad_decoration_id')->nullable()->comment('广告装饰方案');
            
            // 状态
            $table->boolean('is_active')->default(true)->comment('启用');
            $table->integer('view_count')->default(0)->comment('播放次数');
            $table->integer('video_count')->default(0)->comment('视频数量');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'is_active']);
            $table->index('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_players');
    }
};
