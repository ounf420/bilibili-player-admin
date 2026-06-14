<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('user_players', function (Blueprint $table) {
            $table->integer('preroll_duration')->default(0)->after('ad_mode');
            $table->integer('midroll_duration')->default(0)->after('preroll_duration');
            $table->integer('postroll_duration')->default(0)->after('midroll_duration');
        });
    }
    public function down(): void {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn(['preroll_duration','midroll_duration','postroll_duration']);
        });
    }
};
