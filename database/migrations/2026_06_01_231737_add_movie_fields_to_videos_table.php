<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('region', 50)->default('')->after('category')->comment('地区');
            $table->string('year', 10)->default('')->after('region')->comment('年份');
            $table->string('genre', 100)->default('')->after('year')->comment('类型/风格');
            $table->string('director', 200)->default('')->after('genre')->comment('导演');
            $table->text('actors')->nullable()->after('director')->comment('演员');
            $table->string('language', 50)->default('')->after('actors')->comment('语言');
            $table->float('score', 3, 1)->default(0)->after('language')->comment('评分');
            $table->integer('episode_count')->default(1)->after('score')->comment('集数');
            $table->boolean('is_ending')->default(true)->after('episode_count')->comment('是否完结');
            $table->string('quality', 20)->default('HD')->after('is_ending')->comment('画质: SD/HD/FHD/4K');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['region','year','genre','director','actors','language','score','episode_count','is_ending','quality']);
        });
    }
};
