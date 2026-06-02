<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function getUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;
        $userId = Cache::get('token_' . $token);
        if (!$userId) return null;
        return DB::table('users')->where('id', $userId)->first();
    }

    // ==================== 支付宝当面付 ====================
    
    /**
     * 创建支付宝当面付订单（生成二维码）
     */
    public function alipayCreate(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $request->validate([
            'plan_id' => 'required|integer',
        ]);

        $plan = DB::table('vip_plans')->where('id', $request->plan_id)->where('is_active', 1)->first();
        if (!$plan) return response()->json(['success' => false, 'message' => '套餐不存在'], 404);

        $orderNo = 'ALI' . date('YmdHis') . strtoupper(Str::random(6));
        $now = now();

        // 创建待支付订单
        DB::table('vip_orders')->insert([
            'order_no' => $orderNo,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->sale_price,
            'payment_method' => 'alipay_face',
            'status' => 0, // 待支付
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 调用支付宝当面付 API 生成二维码
        $qrCodeUrl = $this->alipayPreCreate($orderNo, $plan->sale_price, "VIP会员-{$plan->name}");

        if (!$qrCodeUrl) {
            return response()->json(['success' => false, 'message' => '支付创建失败，请稍后重试'], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_no' => $orderNo,
                'qr_code' => $qrCodeUrl,
                'amount' => $plan->sale_price,
                'plan_name' => $plan->name,
            ],
        ]);
    }

    /**
     * 查询支付宝订单状态
     */
    public function alipayQuery(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $orderNo = $request->input('order_no');
        $order = DB::table('vip_orders')
            ->where('order_no', $orderNo)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) return response()->json(['success' => false, 'message' => '订单不存在'], 404);

        if ($order->status == 1) {
            return response()->json([
                'success' => true,
                'data' => ['status' => 'paid', 'message' => '支付成功'],
            ]);
        }

        // 主动查询支付宝
        $tradeStatus = $this->alipayQueryTrade($orderNo);

        if ($tradeStatus === 'TRADE_SUCCESS' || $tradeStatus === 'TRADE_FINISHED') {
            $this->handleAlipaySuccess($orderNo);
            return response()->json([
                'success' => true,
                'data' => ['status' => 'paid', 'message' => '支付成功'],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => ['status' => 'waiting', 'message' => '等待支付'],
        ]);
    }

    /**
     * 支付宝异步通知
     */
    public function alipayNotify(Request $request)
    {
        $params = $request->all();
        
        // 验签（生产环境必须启用）
        // $verified = $this->alipayVerifySign($params);
        // if (!$verified) return response('fail');
        
        if (($params['trade_status'] ?? '') === 'TRADE_SUCCESS') {
            $this->handleAlipaySuccess($params['out_trade_no'] ?? '');
        }

        return response('success');
    }

    /**
     * 调用支付宝当面付预下单
     */
    private function alipayPreCreate(string $orderNo, float $amount, string $subject): ?string
    {
        try {
            $config = $this->getAlipayConfig();
            if (!$config) return null;

            // 使用 EasySDK
            \Alipay\EasySDK\Kernel\Factory::setOptions($config);
            $result = \Alipay\EasySDK\Kernel\Factory::payment()->face()
                ->preCreate($subject, $orderNo, number_format($amount, 2, '.', ''));

            if ($result->code === '10000') {
                return $result->qr_code;
            }

            \Log::error('支付宝预下单失败', ['result' => (array) $result]);
            return null;
        } catch (\Exception $e) {
            \Log::error('支付宝预下单异常', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 查询支付宝交易状态
     */
    private function alipayQueryTrade(string $orderNo): ?string
    {
        try {
            $config = $this->getAlipayConfig();
            if (!$config) return null;

            \Alipay\EasySDK\Kernel\Factory::setOptions($config);
            $result = \Alipay\EasySDK\Kernel\Factory::payment()->common()
                ->query($orderNo);

            if ($result->code === '10000') {
                return $result->trade_status ?? null;
            }
            return null;
        } catch (\Exception $e) {
            \Log::error('支付宝查询异常', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 支付成功处理
     */
    private function handleAlipaySuccess(string $orderNo)
    {
        $order = DB::table('vip_orders')->where('order_no', $orderNo)->where('status', 0)->first();
        if (!$order) return;

        $plan = DB::table('vip_plans')->where('id', $order->plan_id)->first();
        if (!$plan) return;

        $now = now();
        DB::table('vip_orders')->where('order_no', $orderNo)->update([
            'status' => 1,
            'paid_at' => $now,
            'start_at' => $now,
            'expire_at' => $now->copy()->addDays($plan->duration_days),
            'updated_at' => $now,
        ]);

        $user = DB::table('users')->where('id', $order->user_id)->first();
        if ($user) {
            $currentExpire = $user->vip_expire_at && strtotime($user->vip_expire_at) > time() ? $user->vip_expire_at : $now;
            $newExpire = date('Y-m-d H:i:s', strtotime($currentExpire) + $plan->duration_days * 86400);

            DB::table('users')->where('id', $user->id)->update([
                'vip_level' => max((int) $user->vip_level, $plan->level),
                'vip_expire_at' => $newExpire,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * 获取支付宝配置
     */
    private function getAlipayConfig(): ?array
    {
        $appId = config('services.alipay.app_id') ?: env('ALIPAY_APP_ID');
        $privateKey = config('services.alipay.private_key') ?: env('ALIPAY_PRIVATE_KEY');
        $alipayPublicKey = config('services.alipay.alipay_public_key') ?: env('ALIPAY_PUBLIC_KEY');

        if (!$appId || !$privateKey || !$alipayPublicKey) {
            \Log::warning('支付宝未配置');
            return null;
        }

        return [
            'protocol' => 'https',
            'gatewayHost' => 'openapi.alipay.com',
            'signType' => 'RSA2',
            'appId' => $appId,
            'privateKey' => $privateKey,
            'alipayPublicKey' => $alipayPublicKey,
            'notifyUrl' => url('/api/payment/alipay/notify'),
        ];
    }

    // ==================== 卡密兑换 ====================

    /**
     * 兑换卡密
     */
    public function redeemCard(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $request->validate([
            'card_no' => 'required|string|max:32',
            'card_secret' => 'required|string|max:64',
        ], [
            'card_no.required' => '请输入卡号',
            'card_secret.required' => '请输入卡密',
        ]);

        $card = DB::table('vip_cards')
            ->where('card_no', $request->card_no)
            ->where('card_secret', $request->card_secret)
            ->first();

        if (!$card) {
            return response()->json(['success' => false, 'message' => '卡号或卡密错误'], 400);
        }

        if ($card->status == 1) {
            return response()->json(['success' => false, 'message' => '该卡密已被使用'], 400);
        }

        if ($card->status == 2) {
            return response()->json(['success' => false, 'message' => '该卡密已被禁用'], 400);
        }

        $plan = DB::table('vip_plans')->where('id', $card->plan_id)->first();
        if (!$plan) {
            return response()->json(['success' => false, 'message' => '关联套餐不存在'], 500);
        }

        $now = now();
        $orderNo = 'CARD' . date('YmdHis') . strtoupper(Str::random(6));

        DB::transaction(function () use ($card, $user, $plan, $now, $orderNo) {
            // 标记卡密已使用
            DB::table('vip_cards')->where('id', $card->id)->update([
                'status' => 1,
                'used_by' => $user->id,
                'used_at' => $now,
                'updated_at' => $now,
            ]);

            // 创建订单
            DB::table('vip_orders')->insert([
                'order_no' => $orderNo,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => 0, // 卡密兑换免费
                'payment_method' => 'card',
                'status' => 1,
                'paid_at' => $now,
                'start_at' => $now,
                'expire_at' => $now->copy()->addDays($plan->duration_days),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 更新用户VIP
            $currentExpire = $user->vip_expire_at && strtotime($user->vip_expire_at) > time() ? $user->vip_expire_at : $now;
            $newExpire = date('Y-m-d H:i:s', strtotime($currentExpire) + $plan->duration_days * 86400);

            DB::table('users')->where('id', $user->id)->update([
                'vip_level' => max((int) $user->vip_level, $plan->level),
                'vip_expire_at' => $newExpire,
                'updated_at' => $now,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "兑换成功！已开通{$plan->name}（{$plan->duration_days}天）",
            'data' => [
                'order_no' => $orderNo,
                'plan_name' => $plan->name,
                'vip_level' => max((int) $user->vip_level, $plan->level),
                'duration_days' => $plan->duration_days,
            ],
        ]);
    }

    /**
     * 批量生成卡密（管理员）
     */
    public function generateCards(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer',
            'count' => 'required|integer|min:1|max:500',
        ]);

        $plan = DB::table('vip_plans')->where('id', $request->plan_id)->first();
        if (!$plan) return response()->json(['success' => false, 'message' => '套餐不存在'], 404);

        $cards = [];
        for ($i = 0; $i < $request->count; $i++) {
            $cards[] = [
                'card_no' => strtoupper(Str::random(16)),
                'card_secret' => strtoupper(Str::random(24)),
                'plan_id' => $plan->id,
                'vip_level' => $plan->level,
                'duration_days' => $plan->duration_days,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('vip_cards')->insert($cards);

        return response()->json([
            'success' => true,
            'message' => "成功生成 {$request->count} 张卡密",
            'data' => $cards,
        ]);
    }
}
