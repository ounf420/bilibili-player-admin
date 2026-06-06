<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlayerShopService;
use App\Models\PlayerPlan;
use Illuminate\Http\Request;

class PlayerShopController extends Controller
{
    protected $shopService;

    public function __construct(PlayerShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    /**
     * 获取套餐列表
     */
    public function plans()
    {
        $plans = $this->shopService->getActivePlans();
        
        return response()->json([
            'code' => 0,
            'data' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'code' => $plan->code,
                    'level' => $plan->level,
                    'level_text' => $plan->level_text,
                    'duration_type' => $plan->duration_type,
                    'duration_type_text' => $plan->duration_type_text,
                    'duration_days' => $plan->duration_days,
                    'price' => $plan->price,
                    'sale_price' => $plan->sale_price,
                    'features' => $plan->features,
                    'badge' => $plan->badge,
                ];
            }),
        ]);
    }

    /**
     * 获取用户商城信息（当前版本+额度+订单）
     */
    public function myInfo(Request $request)
    {
        $userId = $request->user()->id;
        $versionInfo = $this->shopService->getUserVersionInfo($userId);
        $orders = $this->shopService->getUserOrders($userId, 10);

        return response()->json([
            'code' => 0,
            'data' => [
                'version' => $versionInfo,
                'orders' => $orders->items(),
                'orders_total' => $orders->total(),
            ],
        ]);
    }

    /**
     * 卡密兑换
     */
    public function redeemCard(Request $request)
    {
        $request->validate([
            'card_no' => 'required|string',
            'card_secret' => 'required|string',
        ]);

        $userId = $request->user()->id;
        $result = $this->shopService->redeemCard($userId, $request->card_no, $request->card_secret);

        if ($result['success']) {
            return response()->json([
                'code' => 0,
                'message' => $result['message'],
                'data' => $result,
            ]);
        }

        return response()->json([
            'code' => 1,
            'message' => $result['message'],
        ], 400);
    }

    /**
     * 创建订单（支付宝/微信）
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:player_plans,id',
            'payment_method' => 'required|in:alipay,wechat',
        ]);

        $userId = $request->user()->id;
        $plan = PlayerPlan::find($request->plan_id);

        $order = $this->shopService->createOrder(
            $userId,
            'plan',
            $plan->id,
            $plan->name,
            $plan->sale_price,
            $request->payment_method
        );

        // TODO: 对接支付宝/微信支付，获取支付链接
        
        return response()->json([
            'code' => 0,
            'message' => '订单创建成功',
            'data' => [
                'order_no' => $order->order_no,
                'amount' => $order->amount,
                'payment_method' => $order->payment_method,
                // 'pay_url' => $payUrl, // 支付链接
            ],
        ]);
    }

    /**
     * 查询订单状态
     */
    public function orderStatus(Request $request, $orderNo)
    {
        $userId = $request->user()->id;
        $order = \App\Models\PlayerOrder::where('order_no', $orderNo)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json(['code' => 1, 'message' => '订单不存在'], 404);
        }

        return response()->json([
            'code' => 0,
            'data' => [
                'order_no' => $order->order_no,
                'status' => $order->status,
                'status_text' => $order->status_text,
                'amount' => $order->amount,
                'paid_at' => $order->paid_at,
            ],
        ]);
    }
}
