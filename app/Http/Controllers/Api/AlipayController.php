<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlipayController extends Controller
{
    private function getAlipayConfig(): array
    {
        $settings = DB::table('settings')
            ->whereIn('setting_key', ['alipay_app_id', 'alipay_private_key', 'alipay_public_key', 'alipay_sandbox'])
            ->pluck('setting_value', 'setting_key')
            ->map(fn($v) => json_decode($v, true))
            ->toArray();

        return [
            'app_id' => $settings['alipay_app_id'] ?? '',
            'private_key' => $settings['alipay_private_key'] ?? '',
            'public_key' => $settings['alipay_public_key'] ?? '',
            'sandbox' => ($settings['alipay_sandbox'] ?? false) === true,
        ];
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:vip_plans,id',
        ]);

        $user = $this->getAuthUser($request);
        if (!$user) {
            return response()->json(['error' => '请先登录'], 401);
        }

        $plan = DB::table('vip_plans')->where('id', $request->plan_id)->first();
        if (!$plan) {
            return response()->json(['error' => '套餐不存在'], 404);
        }

        $config = $this->getAlipayConfig();
        if (empty($config['app_id'])) {
            return response()->json(['error' => '支付宝未配置，请联系管理员'], 500);
        }

        $orderNo = 'VIP' . date('YmdHis') . strtoupper(Str::random(6));
        $amount = $plan->price;

        DB::table('vip_orders')->insert([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'order_no' => $orderNo,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'alipay',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $qrUrl = $this->createAlipayQr($orderNo, $amount, $plan->name, $config);

        if (!$qrUrl) {
            return response()->json(['error' => '创建支付订单失败，请检查支付宝配置'], 500);
        }

        return response()->json([
            'order_no' => $orderNo,
            'qr_url' => $qrUrl,
            'amount' => $amount,
        ]);
    }

    private function createAlipayQr(string $orderNo, float $amount, string $subject, array $config): ?string
    {
        try {
            $gateway = $config['sandbox']
                ? 'https://openapi-sandbox.dl.alipaydev.com/gateway.do'
                : 'https://openapi.alipay.com/gateway.do';

            $bizContent = json_encode([
                'out_trade_no' => $orderNo,
                'total_amount' => number_format($amount, 2, '.', ''),
                'subject' => 'VIP会员 - ' . $subject,
                'product_code' => 'QUICK_MSECURITY_PAY',
            ]);

            $params = [
                'app_id' => $config['app_id'],
                'method' => 'alipay.trade.precreate',
                'charset' => 'utf-8',
                'sign_type' => 'RSA2',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0',
                'biz_content' => $bizContent,
            ];

            $params['sign'] = $this->generateSign($params, $config['private_key']);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $gateway);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            
            if (isset($result['alipay_trade_precreate_response']['qr_code'])) {
                return $result['alipay_trade_precreate_response']['qr_code'];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function generateSign(array $params, string $privateKey): string
    {
        ksort($params);
        $signStr = '';
        foreach ($params as $k => $v) {
            if ($v !== '' && $v !== null && $k !== 'sign') {
                $signStr .= $k . '=' . $v . '&';
            }
        }
        $signStr = rtrim($signStr, '&');

        $key = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($privateKey, 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
        
        openssl_sign($signStr, $sign, $key, OPENSSL_ALGO_SHA256);
        return base64_encode($sign);
    }

    public function queryOrder(Request $request)
    {
        $request->validate([
            'order_no' => 'required|string',
        ]);

        $order = DB::table('vip_orders')
            ->where('order_no', $request->order_no)
            ->first();

        if (!$order) {
            return response()->json(['error' => '订单不存在'], 404);
        }

        if ($order->status === 'paid') {
            $user = DB::table('users')->where('id', $order->user_id)->first();
            return response()->json([
                'status' => 'paid',
                'vip_expired_at' => $user->vip_expired_at ?? null,
            ]);
        }

        $config = $this->getAlipayConfig();
        if (!empty($config['app_id'])) {
            $gateway = $config['sandbox']
                ? 'https://openapi-sandbox.dl.alipaydev.com/gateway.do'
                : 'https://openapi.alipay.com/gateway.do';

            $bizContent = json_encode([
                'out_trade_no' => $order->order_no,
            ]);

            $params = [
                'app_id' => $config['app_id'],
                'method' => 'alipay.trade.query',
                'charset' => 'utf-8',
                'sign_type' => 'RSA2',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0',
                'biz_content' => $bizContent,
            ];

            $params['sign'] = $this->generateSign($params, $config['private_key']);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $gateway);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            
            if (isset($result['alipay_trade_query_response']['trade_status'])) {
                $tradeStatus = $result['alipay_trade_query_response']['trade_status'];
                
                if (in_array($tradeStatus, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                    $this->completeOrder($order);
                    $user = DB::table('users')->where('id', $order->user_id)->first();
                    return response()->json([
                        'status' => 'paid',
                        'vip_expired_at' => $user->vip_expired_at ?? null,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'pending']);
    }

    public function notify(Request $request)
    {
        $params = $request->all();
        
        if (!isset($params['sign']) || !isset($params['out_trade_no'])) {
            return 'fail';
        }

        $config = $this->getAlipayConfig();
        
        $sign = $params['sign'];
        unset($params['sign']);
        unset($params['sign_type']);
        
        ksort($params);
        $signStr = '';
        foreach ($params as $k => $v) {
            if ($v !== '' && $v !== null) {
                $signStr .= $k . '=' . $v . '&';
            }
        }
        $signStr = rtrim($signStr, '&');

        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($config['public_key'], 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        
        $verify = openssl_verify($signStr, base64_decode($sign), $publicKey, OPENSSL_ALGO_SHA256);
        
        if ($verify !== 1) {
            return 'fail';
        }

        if (in_array($params['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            $order = DB::table('vip_orders')
                ->where('order_no', $params['out_trade_no'])
                ->where('status', 'pending')
                ->first();

            if ($order) {
                $this->completeOrder($order);
            }
        }

        return 'success';
    }

    private function completeOrder($order)
    {
        $plan = DB::table('vip_plans')->where('id', $order->plan_id)->first();
        
        if (!$plan) {
            return;
        }

        DB::table('vip_orders')
            ->where('id', $order->id)
            ->update([
                'status' => 'paid',
                'paid_at' => now(),
                'updated_at' => now(),
            ]);

        $user = DB::table('users')->where('id', $order->user_id)->first();
        $now = now();
        $currentExpired = $user->vip_expired_at ? strtotime($user->vip_expired_at) : 0;
        $startTime = $currentExpired > time() ? $currentExpired : time();
        $newExpired = date('Y-m-d H:i:s', $startTime + ($plan->days * 86400));

        DB::table('users')
            ->where('id', $order->user_id)
            ->update([
                'vip_level' => $plan->level,
                'vip_expired_at' => $newExpired,
                'updated_at' => now(),
            ]);
    }

    private function getAuthUser(Request $request)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            $token = $request->query('token');
        }
        if (!$token) {
            return null;
        }
        $token = str_replace('Bearer ', '', $token);

        $session = DB::table('user_sessions')
            ->where('token', $token)
            ->where('expired_at', '>', now())
            ->first();

        if (!$session) {
            return null;
        }

        return DB::table('users')->where('id', $session->user_id)->first();
    }
}
