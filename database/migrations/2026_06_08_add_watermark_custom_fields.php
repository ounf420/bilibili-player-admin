<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            if (!Schema::hasColumn('user_players', 'watermark_font_size')) {
                $table->integer('watermark_font_size')->default(14)->after('watermark_position');
            }
            if (!Schema::hasColumn('user_players', 'watermark_color')) {
                $table->string('watermark_color', 20)->default('#ffffff')->after('watermark_font_size');
            }
            if (!Schema::hasColumn('user_players', 'watermark_opacity')) {
                $table->decimal('watermark_opacity', 3, 2)->default(0.30)->after('watermark_color');
            }
            if (!Schema::hasColumn('user_players', 'watermark_x')) {
                $table->integer('watermark_x')->nullable()->after('watermark_opacity');
            }
            if (!Schema::hasColumn('user_players', 'watermark_y')) {
                $table->integer('watermark_y')->nullable()->after('watermark_x');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn(['watermark_font_size', 'watermark_color', 'watermark_opacity', 'watermark_x', 'watermark_y']);
        });
    }
};
