<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->timestamp('ad_free_expires_at')->nullable()->after('has_ad_free');
            $table->timestamp('ad_module_expires_at')->nullable()->after('has_ad_module');
        });
    }

    public function down(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn(['ad_free_expires_at', 'ad_module_expires_at']);
        });
    }
};
