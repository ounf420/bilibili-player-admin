<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 64)->unique()->comment('订单号');
            $table->unsignedBigInteger('user_id');
            $table->string('product_type', 20)->comment('产品类型: plan=版本套餐 quota=额度');
            $table->unsignedBigInteger('product_id')->nullable()->comment('关联产品ID');
            $table->string('product_name', 100)->comment('产品名称');
            $table->decimal('amount', 10, 2)->comment('支付金额');
            $table->string('payment_method', 50)->nullable()->comment('支付方式: card=卡密 alipay=支付宝 wechat=微信');
            $table->tinyInteger('status')->default(0)->comment('0=待支付 1=已支付 2=已取消 3=已退款');
            $table->timestamp('paid_at')->nullable();
            $table->string('card_no', 32)->nullable()->comment('使用的卡号');
            $table->string('trade_no', 100)->nullable()->comment('第三方交易号');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('status');
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_orders');
    }
};
