<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// 品牌首页
Route::get('/', function () {
    return view('home');
});

// 影视页面
Route::get('/v', function () {
    return view('videos');
});

// 视频播放页（伪静态）
Route::get("/v/{token}-{id}.html", function ($token, $id) {
    return view("play", ["videoId" => $id]);
});

// 播放器页面
Route::get('/player', function () {
    return view('play');
});

// 账号中心
Route::get('/account', function () {
    return view('account');
});

// 登录
Route::get('/login', function () {
    return view('login');
});

// 注册
Route::get('/register', function () {
    return view('register');
});

// 找回密码
Route::get('/forgot-password', function () {
    return view('forgot-password');
});

// 视频详情页
Route::get('/v/{id}', function ($id) {
    return view('video-detail', ['videoId' => $id]);
});

// VIP会员中心
// 用户中心
Route::get('/user', function () {
    return view('user');
});

Route::get('/vip', function () {
    return view('vip');
});

// 获取视频列表 API
Route::get('/api/videos', function () {
    $videos = DB::table('videos')
        ->where('enabled', 1)
        ->select('id', 'title', 'url', 'cover', 'type', 'duration', 'views')
        ->orderBy('created_at', 'desc')
        ->get();
    return response()->json($videos);
});

// 获取单个视频
Route::get('/api/videos/{id}', function ($id) {
    $video = DB::table('videos')
        ->where('id', $id)
        ->where('enabled', 1)
        ->first();
    if (!$video) {
        return response()->json(['error' => '视频不存在'], 404);
    }
    DB::table('videos')->where('id', $id)->increment('views');
    return response()->json($video);
});

// 获取播放器设置
Route::get('/api/settings', function () {
    $settings = DB::table('player_settings')
        ->select('setting_key', 'setting_value')
        ->get()
        ->pluck('setting_value', 'setting_key')
        ->toArray();
    
    foreach (['autoplay', 'loop', 'show_danmaku', 'show_screenshot', 'show_setting', 'vip_skip_ads_gold', 'vip_skip_ads_diamond', 'vip_skip_ads_star'] as $key) {
        if (isset($settings[$key])) {
            $settings[$key] = (bool) $settings[$key];
        }
    }
    
    foreach (['volume', 'preroll_duration', 'midroll_duration', 'postroll_duration'] as $key) {
        if (isset($settings[$key])) {
            $settings[$key] = (float) $settings[$key];
        }
    }
    
    // 用户VIP状态
    $settings['user_vip_level'] = 0;
    $settings['user_vip_expire'] = null;
    $settings['user_is_vip'] = false;
    try {
        $token = request()->bearerToken();
        if ($token) {
            $cacheKey = 'auth_token_' . md5($token);
            $userId = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($userId) {
                $user = DB::table('users')->where('id', $userId)
                    ->select('vip_level', 'vip_expire_at')
                    ->first();
                if ($user) {
                    $settings['user_vip_level'] = (int) $user->vip_level;
                    $settings['user_vip_expire'] = $user->vip_expire_at;
                    $settings['user_is_vip'] = $user->vip_level > 0 && $user->vip_expire_at && strtotime($user->vip_expire_at) > time();
                }
            }
        }
    } catch (\Exception $e) {}
    
    return response()->json($settings);
});
