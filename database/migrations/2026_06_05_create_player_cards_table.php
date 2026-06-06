<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_no', 32)->unique()->comment('卡号');
            $table->string('card_secret', 64)->unique()->comment('卡密');
            $table->string('card_type', 20)->comment('卡类型: plan=版本卡 quota=额度卡');
            $table->unsignedBigInteger('plan_id')->nullable()->comment('版本卡关联套餐ID');
            $table->integer('quota_amount')->default(0)->comment('额度卡增加的数量');
            $table->unsignedTinyInteger('status')->default(0)->comment('0=未使用 1=已使用 2=已禁用');
            $table->unsignedBigInteger('used_by')->nullable()->comment('使用者用户ID');
            $table->timestamp('used_at')->nullable()->comment('使用时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建者(管理员)');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('card_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_cards');
    }
};
