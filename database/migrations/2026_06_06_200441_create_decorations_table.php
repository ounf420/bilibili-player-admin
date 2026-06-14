<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decorations', function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->string('name', 100)->comment('方案名称');
            $table->string('badge_text', 30)->default('推广')->comment('角标文字');
            $table->string('badge_color', 50)->default('rgba(255,255,255,0.15)')->comment('角标背景色');
            $table->string('badge_text_color', 30)->default('#ffffff')->comment('角标文字色');
            $table->string('progress_color', 30)->default('#00c853')->comment('进度条颜色');
            $table->string('progress_bg', 30)->default('rgba(255,255,255,0.2)')->comment('进度条背景色');
            $table->string('overlay_opacity', 10)->default('0.7')->comment('遮罩透明度');
            $table->string('overlay_gradient', 20)->default('bottom')->comment('遮罩方向: top/bottom/both');
            $table->string('animation', 30)->default('fade')->comment('入场动画');
            $table->string('text_stroke', 10)->default('0')->comment('文字描边');
            $table->string('text_shadow_color', 30)->default('rgba(0,0,0,0.8)')->comment('文字阴影色');
            $table->string('cta_style', 20)->default('rounded')->comment('按钮样式: rounded/pill/rect');
            $table->string('cta_color', 30)->default('#00c853')->comment('按钮颜色');
            $table->string('cta_text_color', 30)->default('#ffffff')->comment('按钮文字色');
            $table->string('close_btn_style', 20)->default('icon')->comment('关闭按钮样式: icon/text/both');
            $table->string('countdown_style', 20)->default('text')->comment('倒计时样式: text/bar/circular');
            $table->boolean('show_brand_area')->default(true)->comment('显示品牌区域');
            $table->boolean('show_progress_bar')->default(true)->comment('显示进度条');
            $table->text('custom_css')->nullable()->comment('自定义CSS');
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decorations');
    }
};
