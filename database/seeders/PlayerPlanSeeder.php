<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerPlan;
use Carbon\Carbon;

class PlayerPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // 基础版
            [
                'name' => '基础版·月卡',
                'code' => 'basic_month',
                'level' => 1,
                'duration_type' => 1,
                'duration_days' => 30,
                'price' => 29.00,
                'sale_price' => 19.00,
                'features' => ['高清播放', '无水印', '5个播放器'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '基础版·季卡',
                'code' => 'basic_quarter',
                'level' => 1,
                'duration_type' => 2,
                'duration_days' => 90,
                'price' => 79.00,
                'sale_price' => 49.00,
                'features' => ['高清播放', '无水印', '5个播放器'],
                'badge' => '省30%',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '基础版·年卡',
                'code' => 'basic_year',
                'level' => 1,
                'duration_type' => 3,
                'duration_days' => 365,
                'price' => 299.00,
                'sale_price' => 149.00,
                'features' => ['高清播放', '无水印', '5个播放器'],
                'badge' => '省50%',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => '基础版·永久',
                'code' => 'basic_forever',
                'level' => 1,
                'duration_type' => 4,
                'duration_days' => 36500,
                'price' => 599.00,
                'sale_price' => 299.00,
                'features' => ['高清播放', '无水印', '5个播放器'],
                'badge' => '买断',
                'is_active' => true,
                'sort_order' => 4,
            ],

            // 专业版
            [
                'name' => '专业版·月卡',
                'code' => 'pro_month',
                'level' => 2,
                'duration_type' => 1,
                'duration_days' => 30,
                'price' => 99.00,
                'sale_price' => 69.00,
                'features' => ['4K播放', '无水印', '20个播放器', '弹幕功能', '自定义LOGO'],
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => '专业版·季卡',
                'code' => 'pro_quarter',
                'level' => 2,
                'duration_type' => 2,
                'duration_days' => 90,
                'price' => 269.00,
                'sale_price' => 169.00,
                'features' => ['4K播放', '无水印', '20个播放器', '弹幕功能', '自定义LOGO'],
                'badge' => '省30%',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => '专业版·年卡',
                'code' => 'pro_year',
                'level' => 2,
                'duration_type' => 3,
                'duration_days' => 365,
                'price' => 999.00,
                'sale_price' => 499.00,
                'features' => ['4K播放', '无水印', '20个播放器', '弹幕功能', '自定义LOGO'],
                'badge' => '省50%',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => '专业版·永久',
                'code' => 'pro_forever',
                'level' => 2,
                'duration_type' => 4,
                'duration_days' => 36500,
                'price' => 1999.00,
                'sale_price' => 999.00,
                'features' => ['4K播放', '无水印', '20个播放器', '弹幕功能', '自定义LOGO'],
                'badge' => '买断',
                'is_active' => true,
                'sort_order' => 8,
            ],

            // 旗舰版
            [
                'name' => '旗舰版·月卡',
                'code' => 'ultimate_month',
                'level' => 3,
                'duration_type' => 1,
                'duration_days' => 30,
                'price' => 299.00,
                'sale_price' => 199.00,
                'features' => ['8K播放', '无水印', '无限播放器', '弹幕功能', '自定义LOGO', 'API接口', '专属客服'],
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => '旗舰版·季卡',
                'code' => 'ultimate_quarter',
                'level' => 3,
                'duration_type' => 2,
                'duration_days' => 90,
                'price' => 799.00,
                'sale_price' => 499.00,
                'features' => ['8K播放', '无水印', '无限播放器', '弹幕功能', '自定义LOGO', 'API接口', '专属客服'],
                'badge' => '省30%',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => '旗舰版·年卡',
                'code' => 'ultimate_year',
                'level' => 3,
                'duration_type' => 3,
                'duration_days' => 365,
                'price' => 2999.00,
                'sale_price' => 1499.00,
                'features' => ['8K播放', '无水印', '无限播放器', '弹幕功能', '自定义LOGO', 'API接口', '专属客服'],
                'badge' => '省50%',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => '旗舰版·永久',
                'code' => 'ultimate_forever',
                'level' => 3,
                'duration_type' => 4,
                'duration_days' => 36500,
                'price' => 5999.00,
                'sale_price' => 2999.00,
                'features' => ['8K播放', '无水印', '无限播放器', '弹幕功能', '自定义LOGO', 'API接口', '专属客服'],
                'badge' => '买断',
                'is_active' => true,
                'sort_order' => 12,
            ],

            // 额度包
            [
                'name' => '播放器额度·10个',
                'code' => 'quota_10',
                'level' => 0,
                'duration_type' => 4,
                'duration_days' => 36500,
                'price' => 49.00,
                'sale_price' => 29.00,
                'features' => ['永久增加10个播放器额度'],
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => '播放器额度·50个',
                'code' => 'quota_50',
                'level' => 0,
                'duration_type' => 4,
                'duration_days' => 36500,
                'price' => 199.00,
                'sale_price' => 99.00,
                'features' => ['永久增加50个播放器额度'],
                'badge' => '省50%',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => '播放器额度·100个',
                'code' => 'quota_100',
                'level' => 0,
                'duration_type' => 4,
                'duration_days' => 36500,
                'price' => 349.00,
                'sale_price' => 169.00,
                'features' => ['永久增加100个播放器额度'],
                'badge' => '最划算',
                'is_active' => true,
                'sort_order' => 15,
            ],
        ];

        foreach ($plans as $plan) {
            PlayerPlan::updateOrCreate(
                ['code' => $plan['code']],
                $plan
            );
        }

        $this->command->info('播放器套餐数据初始化完成');
    }
}
