<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('growth_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->integer('amount')->comment('成长值变动(正数增加)');
            $table->string('type', 30)->index()->comment('类型: sign/watch/vip/comment/streak/reward');
            $table->string('description', 100)->nullable();
            $table->integer('balance')->default(0)->comment('变动后余额');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('growth_logs'); }
};
