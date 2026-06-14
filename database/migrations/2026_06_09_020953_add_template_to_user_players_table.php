<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->string('template', 20)->default('standard')->after('show_controls');
        });
    }

    public function down(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
