<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vip_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('套餐名称');
            $table->tinyInteger('level')->default(1)->comment('VIP等级: 1=VIP 2=SVIP');
            $table->integer('duration_days')->comment('有效天数');
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
        Schema::dropIfExists('vip_plans');
    }
};
