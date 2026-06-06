<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_quotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('total_quota')->default(1)->comment('总播放器额度（含免费）');
            $table->integer('used_quota')->default(0)->comment('已使用额度');
            $table->integer('bonus_quota')->default(0)->comment('购买/卡密获得的额外额度');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_quotas');
    }
};
