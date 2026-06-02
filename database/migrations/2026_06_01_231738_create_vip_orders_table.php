<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vip_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 64)->unique()->comment('订单号');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->decimal('amount', 10, 2)->comment('支付金额');
            $table->string('payment_method', 50)->nullable()->comment('支付方式');
            $table->tinyInteger('status')->default(0)->comment('0=待支付 1=已支付 2=已取消 3=已退款');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('vip_plans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vip_orders');
    }
};
