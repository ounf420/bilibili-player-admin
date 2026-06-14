<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            if (!Schema::hasColumn('user_players', 'version')) {
                $table->enum('version', ['free', 'basic', 'advanced', 'flagship'])
                    ->default('free')
                    ->after('plan_id')
                    ->comment('播放器版本: free=免费 basic=基础 advanced=高级 flagship=旗舰');
            }
            if (!Schema::hasColumn('user_players', 'custom_domain')) {
                $table->string('custom_domain', 255)->nullable()->after('version')
                    ->comment('自定义域名');
            }
            if (!Schema::hasColumn('user_players', 'has_super_ad')) {
                $table->boolean('has_super_ad')->default(false)->after('custom_domain')
                    ->comment('是否已开通超级广告');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn(['version', 'custom_domain', 'has_super_ad']);
        });
    }
};
