<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('video_id', 32);
            $table->integer('progress')->default(0)->comment('播放进度(秒)');
            $table->integer('duration')->default(0)->comment('视频总时长(秒)');
            $table->timestamps();

            $table->unique(['user_id', 'video_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_history');
    }
};
