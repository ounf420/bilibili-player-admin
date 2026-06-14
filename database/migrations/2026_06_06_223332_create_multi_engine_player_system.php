<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 播放器版本表（如果不存在则创建）
        if (!Schema::hasTable('player_plans')) {
            Schema::create('player_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->comment('版本名称');
                $table->string('code', 20)->unique()->comment('版本代码');
                $table->integer('level')->default(0)->comment('等级：0免费 1基础 2高级 3旗舰');
                $table->decimal('price_monthly', 8, 2)->default(0)->comment('月费');
                $table->decimal('price_yearly', 8, 2)->default(0)->comment('年费');
                $table->decimal('price_permanent', 8, 2)->default(0)->comment('永久价格');
                $table->integer('player_limit')->default(1)->comment('播放器数量限制');
                $table->json('features')->comment('功能配置JSON');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 播放器引擎表
        Schema::create('player_engines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('引擎名称');
            $table->string('code', 20)->unique()->comment('引擎代码');
            $table->string('icon', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('cdn_js', 500)->comment('JS CDN地址');
            $table->string('cdn_css', 500)->nullable();
            $table->string('hls_js', 500)->nullable()->comment('HLS.js地址');
            $table->json('default_config')->nullable();
            $table->json('capabilities')->nullable()->comment('支持的功能');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 更新 user_players 表
        Schema::table('user_players', function (Blueprint $table) {
            $table->string('engine_code', 20)->default('dplayer')->after('ad_decoration_id');
            $table->string('player_key', 64)->nullable()->after('engine_code')->comment('播放器密钥');
            $table->foreignId('plan_id')->nullable()->after('player_key');
            $table->boolean('custom_domain_enabled')->default(false)->after('plan_id');
            $table->string('custom_domain', 255)->nullable()->after('custom_domain_enabled');
            $table->boolean('super_material_enabled')->default(false)->after('custom_domain');
            $table->json('engine_config')->nullable()->after('super_material_enabled');
            $table->index('player_key');
        });

        // 更新 user_player_ads 表，支持多播放器投放
        Schema::table('user_player_ads', function (Blueprint $table) {
            $table->enum('target_type', ['single', 'multiple', 'all'])->default('single')->after('player_id')->comment('投放目标');
            $table->json('target_player_ids')->nullable()->after('target_type')->comment('目标播放器ID列表');
        });
    }

    public function down(): void
    {
        Schema::table('user_player_ads', function (Blueprint $table) {
            $table->dropColumn(['target_type', 'target_player_ids']);
        });
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn([
                'engine_code', 'player_key', 'plan_id',
                'custom_domain_enabled', 'custom_domain',
                'super_material_enabled', 'engine_config'
            ]);
        });
        Schema::dropIfExists('player_engines');
    }
};
