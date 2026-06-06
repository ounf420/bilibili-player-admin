<?php

namespace App\Services;

use App\Models\PlayerPlan;
use App\Models\PlayerQuota;
use App\Models\PlayerOrder;
use App\Models\PlayerCard;
use Illuminate\Support\Facades\DB;

class PlayerShopService
{
    /**
     * 获取所有激活的套餐
     */
    public function getActivePlans()
    {
        return PlayerPlan::active()
            ->orderBy('level')
            ->orderBy('duration_type')
            ->get();
    }

    /**
     * 按版本分组获取套餐
     */
    public function getPlansByLevel()
    {
        $plans = $this->getActivePlans();
        return $plans->groupBy('level');
    }

    /**
     * 获取用户额度信息
     */
    public function getUserQuota($userId)
    {
        return PlayerQuota::firstOrCreate(
            ['user_id' => $userId],
            ['total_quota' => 1, 'used_quota' => 0, 'bonus_quota' => 0]
        );
    }

    /**
     * 创建订单
     */
    public function createOrder($userId, $productType, $productId, $productName, $amount, $paymentMethod)
    {
        return PlayerOrder::create([
            'order_no' => PlayerOrder::generateOrderNo(),
            'user_id' => $userId,
            'product_type' => $productType,
            'product_id' => $productId,
            'product_name' => $productName,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => PlayerOrder::STATUS_PENDING,
        ]);
    }

    /**
     * 卡密兑换
     */
    public function redeemCard($userId, $cardNo, $cardSecret)
    {
        return DB::transaction(function () use ($userId, $cardNo, $cardSecret) {
            $card = PlayerCard::where('card_no', $cardNo)
                ->where('card_secret', $cardSecret)
                ->where('status', PlayerCard::STATUS_UNUSED)
                ->first();

            if (!$card) {
                return ['success' => false, 'message' => '卡密无效或已使用'];
            }

            // 标记卡密已使用
            $card->update([
                'status' => PlayerCard::STATUS_USED,
                'used_by' => $userId,
                'used_at' => now(),
            ]);

            // 根据卡类型处理
            if ($card->card_type === PlayerCard::TYPE_PLAN) {
                // 版本卡 - 激活套餐
                $plan = PlayerPlan::find($card->plan_id);
                if (!$plan) {
                    return ['success' => false, 'message' => '套餐不存在'];
                }

                // 创建订单
                $order = $this->createOrder(
                    $userId,
                    PlayerOrder::TYPE_PLAN,
                    $plan->id,
                    $plan->name,
                    0,
                    PlayerOrder::PAY_CARD
                );

                // 标记订单已支付
                $order->update([
                    'status' => PlayerOrder::STATUS_PAID,
                    'paid_at' => now(),
                    'card_no' => $cardNo,
                ]);

                // 激活用户版本
                $this->activatePlan($userId, $plan, $order);

                return [
                    'success' => true,
                    'message' => '版本激活成功',
                    'plan' => $plan,
                    'order' => $order,
                ];
            } else {
                // 额度卡 - 增加额度
                $quota = $this->getUserQuota($userId);
                $quota->addQuota($card->quota_amount);

                // 创建订单
                $order = $this->createOrder(
                    $userId,
                    PlayerOrder::TYPE_QUOTA,
                    null,
                    "播放器额度+{$card->quota_amount}",
                    0,
                    PlayerOrder::PAY_CARD
                );

                // 标记订单已支付
                $order->update([
                    'status' => PlayerOrder::STATUS_PAID,
                    'paid_at' => now(),
                    'card_no' => $cardNo,
                ]);

                return [
                    'success' => true,
                    'message' => "额度增加成功，已增加{$card->quota_amount}个",
                    'quota' => $quota,
                    'order' => $order,
                ];
            }
        });
    }

    /**
     * 激活用户套餐版本
     */
    public function activatePlan($userId, $plan, $order)
    {
        $user = \App\Models\User::find($userId);
        if (!$user) return false;

        // 更新用户版本等级
        $user->level = $plan->level;

        // 计算到期时间
        if ($plan->duration_type === PlayerPlan::DURATION_PERMANENT) {
            $user->expire_at = now()->addYears(100);
        } else {
            // 如果已有到期时间且未过期，在原基础上加
            $baseTime = ($user->expire_at && $user->expire_at->isFuture()) 
                ? $user->expire_at 
                : now();
            $user->expire_at = $baseTime->addDays($plan->duration_days);
        }

        $user->save();

        // 更新订单的开始和结束时间
        $order->update([
            'start_at' => now(),
            'expire_at' => $user->expire_at,
        ]);

        return true;
    }

    /**
     * 检查用户是否可以新建播放器
     */
    public function canCreatePlayer($userId)
    {
        $user = \App\Models\User::find($userId);
        
        // 付费版用户不限数量
        if ($user && $user->level > 0 && $user->expire_at && $user->expire_at->isFuture()) {
            return ['can' => true, 'reason' => 'paid_user'];
        }

        // 免费用户检查额度
        $quota = $this->getUserQuota($userId);
        if ($quota->available_quota > 0) {
            return ['can' => true, 'reason' => 'has_quota', 'available' => $quota->available_quota];
        }

        return ['can' => false, 'reason' => 'no_quota'];
    }

    /**
     * 获取用户订单列表
     */
    public function getUserOrders($userId, $perPage = 15)
    {
        return PlayerOrder::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * 获取用户当前版本信息
     */
    public function getUserVersionInfo($userId)
    {
        $user = \App\Models\User::find($userId);
        $quota = $this->getUserQuota($userId);

        $isActive = $user->level > 0 && $user->expire_at && $user->expire_at->isFuture();

        return [
            'level' => $user->level,
            'expire_at' => $user->expire_at,
            'is_active' => $isActive,
            'level_text' => $isActive ? ['免费版', '基础版', '专业版', '旗舰版'][$user->level] ?? '免费版' : '免费版',
            'total_quota' => $quota->total_quota,
            'used_quota' => $quota->used_quota,
            'available_quota' => $quota->available_quota,
        ];
    }
}
