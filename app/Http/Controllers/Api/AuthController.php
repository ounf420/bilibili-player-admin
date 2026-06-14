<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 注册
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:20|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|max:50',
            'phone' => 'nullable|string|unique:users,phone',
        ], [
            'username.required' => '请输入用户名',
            'username.string' => '用户名格式不正确',
            'username.min' => '用户名至少3个字符',
            'username.max' => '用户名最多20个字符',
            'username.unique' => '该用户名已被注册',
            'email.required' => '请输入邮箱',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '该邮箱已被注册',
            'password.required' => '请输入密码',
            'password.string' => '密码格式不正确',
            'password.min' => '密码至少6个字符',
            'password.max' => '密码最多50个字符',
            'phone.unique' => '该手机号已被注册',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'nickname' => $request->username,
            'name' => $request->username,
            'is_admin' => 0, // 前端注册永远是普通用户
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // 刷新获取数据库默认值
        $user->refresh();

        // 赠送免费播放器额度
        $defaultQuota = \Illuminate\Support\Facades\Cache::get('player_default_quota', 1);
        if ($defaultQuota > 0) {
            \App\Models\PlayerQuota::create([
                'user_id' => $user->id,
                'total_quota' => $defaultQuota,
                'used_quota' => 0,
                'bonus_quota' => $defaultQuota,
            ]);
        }

        // 自动登录
        $token = $this->generateToken($user);
        
        return response()->json([
            'success' => true,
            'message' => '注册成功',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
            ],
        ]);
    }

    // 登录
    public function login(Request $request)
    {
        $request->validate([
            'account' => 'required|string',
            'password' => 'required|string',
        ], [
            'account.required' => '请输入账号',
            'password.required' => '请输入密码',
        ]);

        $account = $request->account;
        
        // 支持用户名/邮箱/手机号登录
        $user = User::where('username', $account)
            ->orWhere('email', $account)
            ->orWhere('phone', $account)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => '账号或密码错误',
            ], 401);
        }

        if ($user->status === 0) {
            return response()->json([
                'success' => false,
                'message' => '账号已被禁用',
            ], 403);
        }

        if ($user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => '管理员账号请前往后台登录',
            ], 403);
        }

        // 更新登录信息
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $this->generateToken($user);

        return response()->json([
            'success' => true,
            'message' => '登录成功',
            'data' => [
                'user' => $this->formatUser($user),
                'token' => $token,
            ],
        ]);
    }

    // 退出登录
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            Cache::forget('token_' . $token);
        }

        return response()->json([
            'success' => true,
            'message' => '已退出登录',
        ]);
    }

    // 获取当前用户信息
    public function me(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $this->formatUser($user),
            ],
        ]);
    }

    // 更新个人资料
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'nickname' => 'nullable|string|max:50',
            'avatar' => 'nullable|string|max:255',
            'gender' => 'nullable|integer|in:0,1,2',
            'birthday' => 'nullable|date',
        ], [
            'nickname.max' => '昵称最多50个字符',
            'gender.in' => '性别值无效',
        ]);

        $data = array_filter($request->only(['nickname', 'avatar', 'gender', 'birthday']), function ($v) {
            return $v !== null;
        });

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => '更新成功',
            'data' => [
                'user' => $this->formatUser($user),
            ],
        ]);
    }

    // 修改密码
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|max:50|confirmed',
        ], [
            'old_password.required' => '请输入原密码',
            'new_password.required' => '请输入新密码',
            'new_password.min' => '新密码至少6个字符',
            'new_password.max' => '新密码最多50个字符',
            'new_password.confirmed' => '两次密码输入不一致',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => '原密码错误',
            ], 400);
        }

        $user->update([
            'password' => $request->new_password,
        ]);

        return response()->json([
            'success' => true,
            'message' => '密码修改成功',
        ]);
    }

    // 绑定手机号
    public function bindPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:users,phone',
            'code' => 'required|string',
        ], [
            'phone.required' => '请输入手机号',
            'phone.unique' => '该手机号已被绑定',
            'code.required' => '请输入验证码',
        ]);

        $user = $request->user();
        
        // 验证验证码
        $cacheKey = 'verify_' . $request->phone;
        $cachedCode = Cache::get($cacheKey);
        
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => '验证码错误或已过期',
            ], 400);
        }

        $user->update(['phone' => $request->phone]);
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => '手机号绑定成功',
        ]);
    }

    // 绑定邮箱
    public function bindEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'code' => 'required|string',
        ], [
            'email.required' => '请输入邮箱',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '该邮箱已被绑定',
            'code.required' => '请输入验证码',
        ]);

        $user = $request->user();
        
        // 验证验证码
        $cacheKey = 'verify_' . $request->email;
        $cachedCode = Cache::get($cacheKey);
        
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => '验证码错误或已过期',
            ], 400);
        }

        $user->update(['email' => $request->email]);
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => '邮箱绑定成功',
        ]);
    }

    // 绑定第三方账号
    public function bindSocial(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:wechat,qq,weibo,github',
            'openid' => 'required|string',
        ], [
            'type.required' => '请选择绑定类型',
            'type.in' => '不支持的绑定类型',
            'openid.required' => '第三方账号信息缺失',
        ]);

        $user = $request->user();
        
        $field = match ($request->type) {
            'wechat' => 'wechat_openid',
            'qq' => 'qq_openid',
            'weibo' => 'weibo_uid',
            'github' => 'github_id',
        };

        // 检查是否已被其他账号绑定
        $exists = User::where($field, $request->openid)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => '该第三方账号已被其他用户绑定',
            ], 400);
        }

        $user->update([$field => $request->openid]);

        return response()->json([
            'success' => true,
            'message' => '绑定成功',
        ]);
    }

    // 解绑第三方账号
    public function unbindSocial(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:wechat,qq,weibo,github',
        ], [
            'type.required' => '请选择解绑类型',
            'type.in' => '不支持的解绑类型',
        ]);

        $user = $request->user();
        
        $field = match ($request->type) {
            'wechat' => 'wechat_openid',
            'qq' => 'qq_openid',
            'weibo' => 'weibo_uid',
            'github' => 'github_id',
        };

        $user->update([$field => null]);

        return response()->json([
            'success' => true,
            'message' => '解绑成功',
        ]);
    }

    // 发送验证码
    public function sendVerifyCode(Request $request)
    {
        $request->validate([
            'target' => 'required|string', // 手机号或邮箱
            'type' => 'required|string|in:phone,email',
        ], [
            'target.required' => '请输入手机号或邮箱',
            'type.required' => '请选择验证类型',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'verify_' . $request->target;
        
        // 缓存验证码，5分钟有效
        Cache::put($cacheKey, $code, 300);

        // TODO: 实际发送短信或邮件
        // 这里暂时返回验证码（开发模式）
        return response()->json([
            'success' => true,
            'message' => '验证码已发送',
            'data' => [
                'code' => $code, // 生产环境应移除
            ],
        ]);
    }

    // 验证验证码
    public function verifyCode(Request $request)
    {
        $request->validate([
            'target' => 'required|string',
            'code' => 'required|string',
        ]);

        $cacheKey = 'verify_' . $request->target;
        $cachedCode = Cache::get($cacheKey);
        
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => '验证码错误或已过期',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => '验证成功',
        ]);
    }

    // 重置密码
    public function resetPassword(Request $request)
    {
        $request->validate([
            'target' => 'required|string', // 手机号或邮箱
            'code' => 'required|string',
            'password' => 'required|string|min:6|max:50|confirmed',
        ], [
            'target.required' => '请输入手机号或邮箱',
            'code.required' => '请输入验证码',
            'password.required' => '请输入新密码',
            'password.min' => '密码至少6个字符',
            'password.max' => '密码最多50个字符',
            'password.confirmed' => '两次密码输入不一致',
        ]);

        $cacheKey = 'verify_' . $request->target;
        $cachedCode = Cache::get($cacheKey);
        
        if (!$cachedCode || $cachedCode !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => '验证码错误或已过期',
            ], 400);
        }

        // 查找用户
        $user = User::where('email', $request->target)
            ->orWhere('phone', $request->target)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '用户不存在',
            ], 404);
        }

        $user->update(['password' => $request->password]);
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => '密码重置成功',
        ]);
    }

    // 登录历史
    public function loginHistory(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'last_login_at' => $user->last_login_at,
                'last_login_ip' => $user->last_login_ip,
            ],
        ]);
    }

    // 导出数据
    public function exportData(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $this->formatUser($user),
                'exported_at' => now()->toISOString(),
            ],
        ]);
    }

    // 注销账号
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => '请输入密码确认',
        ]);

        $user = $request->user();

        // 管理员不能注销
        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => '管理员账号不能注销',
            ], 403);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => '密码错误',
            ], 400);
        }

        // 清除token
        $token = $request->bearerToken();
        if ($token) {
            Cache::forget('token_' . $token);
        }

        // 软删除用户
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => '账号已注销',
        ]);
    }

    // 生成Token
    public function generateToken(User $user): string
    {
        $token = Str::random(60);
        Cache::put('token_' . $token, $user->id, 60 * 24 * 30); // 30天
        
        return $token;
    }

    // 格式化用户数据
    public function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'status' => $user->status,
            'status_name' => $user->status == 1 ? '正常' : ($user->status == 0 ? '禁用' : '待验证'),
            'is_admin' => $user->isAdmin(),
            'gender' => $user->gender,
            'gender_name' => match ($user->gender) {
                1 => '男',
                2 => '女',
                default => '未知',
            },
            'birthday' => $user->birthday,
            'real_name' => $user->real_name,
            'wechat_bound' => !empty($user->wechat_openid),
            'qq_bound' => !empty($user->qq_openid),
            'weibo_bound' => !empty($user->weibo_uid),
            'github_bound' => !empty($user->github_id),
            'last_login_at' => $user->last_login_at,
            'last_login_ip' => $user->last_login_ip,
            'created_at' => $user->created_at,
        ];
    }
}
