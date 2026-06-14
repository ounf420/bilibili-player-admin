<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_player_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('user_players')->onDelete('cascade');
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['player_id', 'video_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_player_videos');
    }
};
