<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerPlansEnginesSeeder extends Seeder
{
    public function run(): void
    {
        // 播放器版本
        $plans = [
            [
                'name' => '免费版',
                'code' => 'free',
                'level' => 0,
                'duration_type' => 0,
                'duration_days' => 0,
                'price' => 0,
                'sale_price' => 0,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'price_permanent' => 0,
                'player_limit' => 1,
                'features' => json_encode([
                    'custom_appearance' => false,
                    'custom_logo' => false,
                    'custom_domain' => false,
                    'material_module' => false,
                    'super_material' => false,
                ]),
                'description' => '注册即送，基础播放功能',
                'badge' => '免费',
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'name' => '基础版',
                'code' => 'basic',
                'level' => 1,
                'duration_type' => 1,
                'duration_days' => 30,
                'price' => 9.9,
                'sale_price' => 9.9,
                'price_monthly' => 9.9,
                'price_yearly' => 99,
                'price_permanent' => 299,
                'player_limit' => 3,
                'features' => json_encode([
                    'custom_appearance' => true,
                    'custom_logo' => true,
                    'custom_domain' => false,
                    'material_module' => false,
                    'super_material' => false,
                ]),
                'description' => '自定义外观，打造专属播放器',
                'badge' => '推荐',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '高级版',
                'code' => 'premium',
                'level' => 2,
                'duration_type' => 1,
                'duration_days' => 30,
                'price' => 29.9,
                'sale_price' => 29.9,
                'price_monthly' => 29.9,
                'price_yearly' => 299,
                'price_permanent' => 899,
                'player_limit' => 10,
                'features' => json_encode([
                    'custom_appearance' => true,
                    'custom_logo' => true,
                    'custom_domain' => true,
                    'material_module' => false,
                    'super_material' => false,
                ]),
                'description' => '支持自定义域名，品牌独立',
                'badge' => '热门',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '旗舰版',
                'code' => 'ultimate',
                'level' => 3,
                'duration_type' => 1,
                'duration_days' => 30,
                'price' => 59.9,
                'sale_price' => 59.9,
                'price_monthly' => 59.9,
                'price_yearly' => 599,
                'price_permanent' => 1999,
                'player_limit' => 50,
                'features' => json_encode([
                    'custom_appearance' => true,
                    'custom_logo' => true,
                    'custom_domain' => true,
                    'material_module' => true,
                    'super_material' => true,
                ]),
                'description' => '全部功能，含广告模块',
                'badge' => '旗舰',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('player_plans')->updateOrInsert(
                ['code' => $plan['code']],
                array_merge($plan, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // 播放器引擎
        $engines = [
            [
                'name' => 'DPlayer',
                'code' => 'dplayer',
                'icon' => '🎬',
                'description' => '轻量级HTML5播放器，支持弹幕',
                'cdn_js' => 'https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js',
                'cdn_css' => 'https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css',
                'hls_js' => 'https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js',
                'default_config' => json_encode(['theme' => '#b7daff']),
                'capabilities' => json_encode(['danmaku', 'quality', 'screenshot', 'pip']),
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'ArtPlayer',
                'code' => 'artplayer',
                'icon' => '🎨',
                'description' => '功能丰富，高度可定制',
                'cdn_js' => 'https://cdn.jsdelivr.net/npm/artplayer@5.1.1/dist/artplayer.min.js',
                'cdn_css' => null,
                'hls_js' => 'https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js',
                'default_config' => json_encode(['theme' => '#00a1d6']),
                'capabilities' => json_encode(['quality', 'screenshot', 'pip', 'subtitle', 'miniplayer']),
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'CKPlayer',
                'code' => 'ckplayer',
                'icon' => '📺',
                'description' => '国产老牌播放器，兼容性好',
                'cdn_js' => 'https://cdn.jsdelivr.net/npm/ckplayer@2.1.0/ckplayer.min.js',
                'cdn_css' => null,
                'hls_js' => null,
                'default_config' => json_encode(['theme' => '#00a1d6']),
                'capabilities' => json_encode(['quality', 'advertisement']),
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'xgplayer',
                'code' => 'xgplayer',
                'icon' => '⚡',
                'description' => '西瓜视频开源，性能优秀',
                'cdn_js' => 'https://cdn.jsdelivr.net/npm/xgplayer@3.0.9/dist/index.min.js',
                'cdn_css' => 'https://cdn.jsdelivr.net/npm/xgplayer@3.0.9/dist/index.min.css',
                'hls_js' => 'https://cdn.jsdelivr.net/npm/xgplayer-hls.js@3.0.9/dist/index.min.js',
                'default_config' => json_encode(['themeColor' => '#00a1d6']),
                'capabilities' => json_encode(['quality', 'pip', 'miniplayer', 'download']),
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Clappr',
                'code' => 'clappr',
                'icon' => '👏',
                'description' => '插件化架构，扩展性强',
                'cdn_js' => 'https://cdn.jsdelivr.net/npm/clappr@0.4.3/dist/clappr.min.js',
                'cdn_css' => null,
                'hls_js' => 'https://cdn.jsdelivr.net/npm/@clappr/hls-plugin@0.6.0/dist/clappr-hls-plugin.min.js',
                'default_config' => json_encode(['theme' => '#2e74b5']),
                'capabilities' => json_encode(['quality', 'pip', 'poster']),
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'MUIPlayer',
                'code' => 'muiplayer',
                'icon' => '📱',
                'description' => '移动端优化，触控友好',
                'cdn_js' => 'https://cdn.jsdelivr.net/npm/mui-player@2.6.3/dist/mui-player.min.js',
                'cdn_css' => 'https://cdn.jsdelivr.net/npm/mui-player@2.6.3/dist/mui-player.min.css',
                'hls_js' => 'https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js',
                'default_config' => json_encode(['theme' => '#00a1d6']),
                'capabilities' => json_encode(['quality', 'screenshot', 'gesture', 'miniplayer']),
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($engines as $engine) {
            DB::table('player_engines')->updateOrInsert(
                ['code' => $engine['code']],
                array_merge($engine, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
