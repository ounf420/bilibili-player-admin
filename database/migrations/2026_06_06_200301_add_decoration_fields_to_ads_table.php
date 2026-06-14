<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->string('badge_text', 30)->default('推广')->after('cta_text');
            $table->string('badge_color', 30)->default('rgba(255,255,255,0.15)')->after('badge_text');
            $table->string('progress_color', 30)->default('#00c853')->after('badge_color');
            $table->string('overlay_opacity', 10)->default('0.7')->after('progress_color');
            $table->string('animation', 30)->default('fade')->after('overlay_opacity');
            $table->string('text_stroke', 10)->default('0')->after('animation');
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['badge_text', 'badge_color', 'progress_color', 'overlay_opacity', 'animation', 'text_stroke']);
        });
    }
};
