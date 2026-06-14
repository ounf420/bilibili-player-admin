<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 创建剧集分组表
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->comment('剧名');
            $table->string('cover')->nullable()->comment('封面');
            $table->text('description')->nullable()->comment('简介');
            $table->integer('episode_count')->default(0)->comment('已添加集数');
            $table->boolean('is_ending')->default(false)->comment('是否完结');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
        });

        // videos表添加剧集关联
        Schema::table('videos', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->after('id')->constrained('series')->nullOnDelete();
            $table->integer('episode_number')->default(1)->after('title')->comment('第几集');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropColumn(['series_id', 'episode_number']);
        });
        Schema::dropIfExists('series');
    }
};
