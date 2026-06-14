<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    private string $apiUrl = 'https://login.cxavn.cn/connect.php';
    private string $appId;
    private string $appKey;
    private string $callbackUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.socialite.api_url', 'https://login.cxavn.cn') . '/connect.php';
        $this->appId = config('services.socialite.appid');
        $this->appKey = config('services.socialite.appkey');
        // 自动识别HTTP/HTTPS，跟随当前请求协议
        $callback = config('services.socialite.callback');
        $callback = preg_replace('#^https?://#', request()->getScheme() . '://', $callback);
        $this->callbackUrl = $callback;
    }

    /**
     * 获取登录跳转地址
     * GET /api/socialite/login?type=qq
     */
    public function login(Request $request)
    {
        $type = $request->input('type', 'qq');
        $state = Str::random(32);

        // 缓存state防CSRF，5分钟有效
        Cache::put('socialite_state_' . $state, $type, 300);

        $params = [
            'act' => 'login',
            'appid' => $this->appId,
            'appkey' => $this->appKey,
            'type' => $type,
            'redirect_uri' => $this->callbackUrl,
            'state' => $state,
        ];

        $response = Http::timeout(10)->get($this->apiUrl, $params);
        $data = $response->json();

        if (isset($data['code']) && $data['code'] == 0) {
            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $data['url'],
                    'qrcode' => $data['qrcode'] ?? null,
                    'type' => $type,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $data['msg'] ?? '获取登录地址失败',
        ]);
    }

    /**
     * 登录回调处理
     * GET /api/socialite/callback?type=qq&code=xxx&state=xxx
     */
    public function callback(Request $request)
    {
        $code = $request->input('code');
        $type = $request->input('type');
        $state = $request->input('state');

        // 调试日志
        \Illuminate\Support\Facades\Log::info('Socialite callback', [
            'code' => $code,
            'type' => $type,
            'state' => $state,
            'platform' => $platform ?? null,
            'url' => $request->fullUrl(),
        ]);

        if (!$code) {
            // 返回JSON而非HTML，让login.cxavn.cn验证URL时能正常识别
            if (!$state && !$type) {
                return response()->json(['success' => true, 'message' => 'callback ready']);
            }
            return $this->callbackError('缺少授权码');
        }

        // 验证state（login.cxavn.cn可能不会原样传回state，所以做容错处理）
        $platform = $type ?: 'qq';
        if ($state) {
            $stateKey = 'socialite_state_' . $state;
            $cachedType = Cache::get($stateKey);
            if ($cachedType) {
                $platform = $cachedType;
                Cache::forget($stateKey);
            }
        }

        // 用code换取用户信息
        $params = [
            'act' => 'callback',
            'appid' => $this->appId,
            'appkey' => $this->appKey,
            'type' => $platform,
            'code' => $code,
        ];

        $response = Http::timeout(10)->get($this->apiUrl, $params);
        $data = $response->json();

        \Illuminate\Support\Facades\Log::info('Socialite API response', [
            'params' => $params,
            'response' => $data,
            'status' => $response->status(),
        ]);

        if (!isset($data['code']) || $data['code'] != 0) {
            \Illuminate\Support\Facades\Log::error('Socialite callback failed', ['data' => $data]);
            return $this->callbackError($data['msg'] ?? '登录失败');
        }

        $socialUid = $data['social_uid'];

        // 查找是否已有绑定
        $account = SocialAccount::where('platform', $platform)
            ->where('social_uid', $socialUid)
            ->first();

        if ($account) {
            // 已绑定 → 更新token并登录
            $account->update([
                'access_token' => $data['access_token'] ?? null,
                'nickname' => $data['nickname'] ?? null,
                'avatar' => $data['faceimg'] ?? null,
                'gender' => $data['gender'] ?? null,
                'location' => $data['location'] ?? null,
            ]);

            $user = $account->user;
            if ($user->status === 0) {
                return $this->callbackError('账号已被禁用');
            }
            if ($user->is_admin) {
                return $this->callbackError('管理员账号请前往后台登录');
            }

            // 更新登录信息
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            $token = app(AuthController::class)->generateToken($user);

            return $this->callbackSuccess($user, $token, $platform);
        }

        // 未绑定 → 存储到缓存，等待前端绑定/注册
        $tempKey = 'socialite_temp_' . Str::random(32);
        Cache::put($tempKey, [
            'platform' => $platform,
            'social_uid' => $socialUid,
            'access_token' => $data['access_token'] ?? null,
            'nickname' => $data['nickname'] ?? null,
            'avatar' => $data['faceimg'] ?? null,
            'gender' => $data['gender'] ?? null,
            'location' => $data['location'] ?? null,
        ], 600); // 10分钟

        // 返回页面，前端根据tempKey处理绑定/注册
        return $this->callbackRedirect($tempKey, $platform, $data['nickname'] ?? '');
    }

    /**
     * 绑定第三方账号到当前用户
     * POST /api/socialite/bind
     */
    public function bind(Request $request)
    {
        $request->validate([
            'temp_key' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $tempData = Cache::get($request->temp_key);
        if (!$tempData) {
            return response()->json(['success' => false, 'message' => '授权已过期，请重试'], 400);
        }

        // 检查是否已被其他用户绑定
        $exists = SocialAccount::where('platform', $tempData['platform'])
            ->where('social_uid', $tempData['social_uid'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => '该账号已被其他用户绑定'], 400);
        }

        // 检查当前用户是否已绑定该平台
        $alreadyBound = SocialAccount::where('user_id', $user->id)
            ->where('platform', $tempData['platform'])
            ->exists();

        if ($alreadyBound) {
            return response()->json(['success' => false, 'message' => '您已绑定该平台'], 400);
        }

        SocialAccount::create([
            'user_id' => $user->id,
            'platform' => $tempData['platform'],
            'social_uid' => $tempData['social_uid'],
            'access_token' => $tempData['access_token'],
            'nickname' => $tempData['nickname'],
            'avatar' => $tempData['avatar'],
            'gender' => $tempData['gender'],
            'location' => $tempData['location'],
        ]);

        Cache::forget('socialite_temp_' . $request->temp_key);

        return response()->json([
            'success' => true,
            'message' => SocialAccount::platformName($tempData['platform']) . '绑定成功',
        ]);
    }

    /**
     * 第三方账号直接登录/注册
     * POST /api/socialite/login-with-temp
     */
    public function loginWithTemp(Request $request)
    {
        $request->validate([
            'temp_key' => 'required|string',
        ]);

        $tempData = Cache::get($request->temp_key);
        if (!$tempData) {
            return response()->json(['success' => false, 'message' => '授权已过期，请重试'], 400);
        }

        // 查找已有绑定
        $account = SocialAccount::where('platform', $tempData['platform'])
            ->where('social_uid', $tempData['social_uid'])
            ->first();

        if ($account) {
            $user = $account->user;
            if ($user->status === 0) {
                return response()->json(['success' => false, 'message' => '账号已被禁用'], 403);
            }
            if ($user->is_admin) {
                return response()->json(['success' => false, 'message' => '管理员账号请前往后台登录'], 403);
            }

            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
            $token = app(AuthController::class)->generateToken($user);
            Cache::forget('socialite_temp_' . $request->temp_key);

            return response()->json([
                'success' => true,
                'message' => '登录成功',
                'data' => [
                    'user' => app(AuthController::class)->formatUser($user),
                    'token' => $token,
                ],
            ]);
        }

        // 未绑定 → 自动注册新用户
        $nickname = $tempData['nickname'] ?: ($tempData['platform'] . '用户');
        $username = 'social_' . $tempData['platform'] . '_' . substr($tempData['social_uid'], 0, 8);

        // 确保用户名唯一
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $user = User::create([
            'username' => $username,
            'nickname' => $nickname,
            'name' => $nickname,
            'avatar' => $tempData['avatar'],
            'gender' => $tempData['gender'] === '男' ? 1 : ($tempData['gender'] === '女' ? 2 : 0),
            'password' => bcrypt(Str::random(16)),
            'is_admin' => 0,
            'status' => 1,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        SocialAccount::create([
            'user_id' => $user->id,
            'platform' => $tempData['platform'],
            'social_uid' => $tempData['social_uid'],
            'access_token' => $tempData['access_token'],
            'nickname' => $tempData['nickname'],
            'avatar' => $tempData['avatar'],
            'gender' => $tempData['gender'],
            'location' => $tempData['location'],
        ]);

        Cache::forget('socialite_temp_' . $request->temp_key);

        $token = app(AuthController::class)->generateToken($user);

        return response()->json([
            'success' => true,
            'message' => '注册并登录成功',
            'data' => [
                'user' => app(AuthController::class)->formatUser($user),
                'token' => $token,
            ],
        ]);
    }

    /**
     * 解绑第三方账号
     * POST /api/socialite/unbind
     */
    public function unbind(Request $request)
    {
        $request->validate([
            'platform' => 'required|string',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        // 检查是否有密码，没密码不能解绑最后一个社交账号
        $hasPassword = !empty($user->password) && $user->password !== '';
        $socialCount = SocialAccount::where('user_id', $user->id)->count();

        if (!$hasPassword && $socialCount <= 1) {
            return response()->json([
                'success' => false,
                'message' => '请先设置密码再解绑，否则将无法登录',
            ], 400);
        }

        $deleted = SocialAccount::where('user_id', $user->id)
            ->where('platform', $request->platform)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => '解绑成功']);
        }

        return response()->json(['success' => false, 'message' => '未找到绑定记录'], 400);
    }

    /**
     * 获取当前用户的绑定列表
     * GET /api/socialite/bindings
     */
    public function bindings(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $accounts = SocialAccount::where('user_id', $user->id)->get();

        $allPlatforms = config('services.socialite.platforms', []);
        $enabledPlatforms = array_keys(array_filter($allPlatforms));

        $bindings = [];
        foreach ($enabledPlatforms as $platform) {
            $account = $accounts->firstWhere('platform', $platform);
            $bindings[] = [
                'platform' => $platform,
                'name' => SocialAccount::platformName($platform),
                'icon' => SocialAccount::platformIcon($platform),
                'bound' => $account ? true : false,
                'nickname' => $account?->nickname,
                'avatar' => $account?->avatar,
                'bound_at' => $account?->created_at?->format('Y-m-d H:i'),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $bindings,
        ]);
    }

    private function callbackSuccess(User $user, string $token, string $platform)
    {
        // 返回HTML页面，通过postMessage通知父窗口
        $html = $this->buildCallbackHtml([
            'success' => true,
            'token' => $token,
            'user' => app(AuthController::class)->formatUser($user),
            'platform' => $platform,
        ]);
        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    private function callbackRedirect(string $tempKey, string $platform, string $nickname)
    {
        $html = $this->buildCallbackHtml([
            'success' => true,
            'need_bind' => true,
            'temp_key' => $tempKey,
            'platform' => $platform,
            'nickname' => $nickname,
        ]);
        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    private function callbackError(string $message)
    {
        $html = $this->buildCallbackHtml([
            'success' => false,
            'message' => $message,
        ]);
        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    private function buildCallbackHtml(array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        return <<<HTML
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>登录中...</title></head>
<body>
<script>
try {{
    if (window.opener) {{
        window.opener.postMessage({$json}, '*');
        window.close();
    }} else if (parent !== window) {{
        parent.postMessage({$json}, '*');
    }} else {{
        localStorage.setItem('socialite_callback', JSON.stringify({$json}));
        window.location.href = '/';
    }}
}} catch(e) {{
    localStorage.setItem('socialite_callback', JSON.stringify({$json}));
    window.location.href = '/';
}}
</script>
<p>登录处理中，请稍候...</p>
</body></html>
HTML;
    }
}
