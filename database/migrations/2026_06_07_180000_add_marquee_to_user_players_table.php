<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->boolean('show_marquee')->default(false)->after('postroll_duration');
            $table->string('marquee_text', 200)->nullable()->after('show_marquee');
        });
    }

    public function down()
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn(['show_marquee', 'marquee_text']);
        });
    }
};
