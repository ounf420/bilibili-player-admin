<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('player_plans', 'price_monthly')) {
                $table->decimal('price_monthly', 8, 2)->default(0)->after('level');
            }
            if (!Schema::hasColumn('player_plans', 'price_yearly')) {
                $table->decimal('price_yearly', 8, 2)->default(0)->after('price_monthly');
            }
            if (!Schema::hasColumn('player_plans', 'price_permanent')) {
                $table->decimal('price_permanent', 8, 2)->default(0)->after('price_yearly');
            }
            if (!Schema::hasColumn('player_plans', 'player_limit')) {
                $table->integer('player_limit')->default(1)->after('price_permanent');
            }
            if (!Schema::hasColumn('player_plans', 'description')) {
                $table->text('description')->nullable()->after('features');
            }
        });
    }

    public function down(): void
    {
        Schema::table('player_plans', function (Blueprint $table) {
            $table->dropColumn(['price_monthly', 'price_yearly', 'price_permanent', 'player_limit', 'description']);
        });
    }
};
