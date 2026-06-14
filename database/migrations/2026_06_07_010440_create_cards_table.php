<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 充值卡密表
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_no', 32)->unique()->comment('卡号');
            $table->string('card_secret', 64)->comment('卡密/密码');
            $table->decimal('amount', 10, 2)->comment('面值金额');
            $table->enum('status', ['unused', 'used', 'disabled'])->default('unused')->comment('状态');
            $table->unsignedBigInteger('used_by')->nullable()->comment('使用者ID');
            $table->timestamp('used_at')->nullable()->comment('使用时间');
            $table->unsignedBigInteger('batch_id')->nullable()->comment('批次ID');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();
            
            $table->index('status');
            $table->index('batch_id');
        });

        // 用户余额表
        Schema::create('user_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->decimal('balance', 10, 2)->default(0)->comment('当前余额');
            $table->decimal('total_recharged', 10, 2)->default(0)->comment('累计充值');
            $table->decimal('total_spent', 10, 2)->default(0)->comment('累计消费');
            $table->timestamps();
        });

        // 交易记录表
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['recharge', 'purchase', 'refund'])->comment('类型');
            $table->decimal('amount', 10, 2)->comment('金额');
            $table->decimal('balance_after', 10, 2)->comment('交易后余额');
            $table->string('description')->comment('描述');
            $table->unsignedBigInteger('related_id')->nullable()->comment('关联ID');
            $table->string('related_type')->nullable()->comment('关联类型');
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
        });

        // 订单表
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 32)->unique()->comment('订单号');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id')->comment('套餐ID');
            $table->decimal('amount', 10, 2)->comment('订单金额');
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending');
            $table->enum('pay_method', ['balance', 'card'])->nullable()->comment('支付方式');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('user_balances');
        Schema::dropIfExists('cards');
    }
};
