<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->string('progress_icon_url')->nullable()->after('logo_url');
        });
    }

    public function down(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn('progress_icon_url');
        });
    }
};
