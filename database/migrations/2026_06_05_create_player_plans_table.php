<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('套餐名称：基础版/专业版/旗舰版');
            $table->string('code', 30)->unique()->comment('套餐编码：basic/pro/ultimate');
            $table->tinyInteger('level')->default(1)->comment('版本等级: 1=基础 2=专业 3=旗舰');
            $table->tinyInteger('duration_type')->comment('时长类型: 1=月 2=季 3=年 4=永久');
            $table->integer('duration_days')->comment('有效天数（永久=36500）');
            $table->decimal('price', 10, 2)->comment('原价');
            $table->decimal('sale_price', 10, 2)->comment('售价');
            $table->text('features')->nullable()->comment('特权说明JSON');
            $table->string('badge', 50)->nullable()->comment('角标文字');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_plans');
    }
};
