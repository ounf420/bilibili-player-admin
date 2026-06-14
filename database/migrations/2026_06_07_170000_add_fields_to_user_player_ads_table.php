<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_player_ads', function (Blueprint $table) {
            $table->text('content')->nullable()->after('media_url');
            $table->string('cover_url', 500)->nullable()->after('content');
            $table->string('title', 200)->nullable()->after('cover_url');
            $table->text('description')->nullable()->after('title');
            $table->string('cta_text', 50)->nullable()->after('description');
            $table->string('cta_url', 500)->nullable()->after('cta_text');
            $table->string('logo_url', 500)->nullable()->after('cta_url');
            $table->timestamp('start_at')->nullable()->after('sort_order');
            $table->timestamp('end_at')->nullable()->after('start_at');
            $table->integer('frequency_cap')->default(0)->after('end_at');
            $table->integer('priority')->default(0)->after('frequency_cap');
        });
    }

    public function down(): void
    {
        Schema::table('user_player_ads', function (Blueprint $table) {
            $table->dropColumn(['content','cover_url','title','description','cta_text','cta_url','logo_url','start_at','end_at','frequency_cap','priority']);
        });
    }
};
