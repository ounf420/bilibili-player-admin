<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vip_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_no', 32)->unique()->comment('卡号');
            $table->string('card_secret', 64)->unique()->comment('卡密');
            $table->unsignedTinyInteger('plan_id')->comment('关联套餐ID');
            $table->unsignedTinyInteger('vip_level')->default(1)->comment('VIP等级');
            $table->unsignedInteger('duration_days')->default(30)->comment('天数');
            $table->unsignedTinyInteger('status')->default(0)->comment('0=未使用 1=已使用 2=已禁用');
            $table->unsignedBigInteger('used_by')->nullable()->comment('使用者用户ID');
            $table->timestamp('used_at')->nullable()->comment('使用时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建者(管理员)');
            $table->timestamps();
            
            $table->index('status');
            $table->index('vip_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vip_cards');
    }
};
