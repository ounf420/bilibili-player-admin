<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VipController extends Controller
{
    private function getUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;
        $userId = \Illuminate\Support\Facades\Cache::get('token_' . $token);
        if (!$userId) return null;
        return DB::table('users')->where('id', $userId)->first();
    }

    // 获取VIP套餐列表
    public function plans()
    {
        $plans = DB::table('vip_plans')->where('is_active', 1)->orderBy('sort_order')->get();
        return response()->json(['success' => true, 'data' => $plans]);
    }

    // 获取用户VIP状态
    public function status(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $isVip = $user->vip_expire_at && strtotime($user->vip_expire_at) > time();
        return response()->json(['success' => true, 'data' => [
            'vip_level' => (int)$user->vip_level,
            'vip_expire_at' => $user->vip_expire_at,
            'is_vip' => $isVip,
            'level_name' => $user->vip_level == 2 ? 'SVIP' : ($user->vip_level == 1 ? 'VIP' : '普通用户'),
        ]]);
    }

    // 创建VIP订单（模拟支付）
    public function createOrder(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $planId = $request->input('plan_id');
        $plan = DB::table('vip_plans')->where('id', $planId)->where('is_active', 1)->first();
        if (!$plan) return response()->json(['success' => false, 'message' => '套餐不存在'], 404);

        $orderNo = 'VIP' . date('YmdHis') . strtoupper(Str::random(6));
        $now = now();

        DB::table('vip_orders')->insert([
            'order_no' => $orderNo,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->sale_price,
            'payment_method' => 'demo',
            'status' => 1, // 模拟直接支付成功
            'paid_at' => $now,
            'start_at' => $now,
            'expire_at' => $now->addDays($plan->duration_days),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 更新用户VIP
        $currentExpire = $user->vip_expire_at && strtotime($user->vip_expire_at) > time() ? $user->vip_expire_at : $now;
        $newExpire = date('Y-m-d H:i:s', strtotime($currentExpire) + $plan->duration_days * 86400);

        DB::table('users')->where('id', $user->id)->update([
            'vip_level' => max((int)$user->vip_level, $plan->level),
            'vip_expire_at' => $newExpire,
            'updated_at' => $now,
        ]);

        return response()->json(['success' => true, 'message' => '开通成功', 'data' => [
            'order_no' => $orderNo,
            'vip_level' => max((int)$user->vip_level, $plan->level),
            'vip_expire_at' => $newExpire,
        ]]);
    }

    // VIP订单记录
    public function orders(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $orders = DB::table('vip_orders')
            ->join('vip_plans', 'vip_orders.plan_id', '=', 'vip_plans.id')
            ->where('vip_orders.user_id', $user->id)
            ->orderByDesc('vip_orders.created_at')
            ->select('vip_orders.*', 'vip_plans.name as plan_name', 'vip_plans.level as plan_level', 'vip_plans.duration_days')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $orders]);
    }
}
