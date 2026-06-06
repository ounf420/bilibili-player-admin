<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// 品牌首页
Route::get('/', function () {
    return view('home');
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

// 用户中心
Route::get('/user', function () {
    return view('user');
});

// 获取播放器设置
Route::get('/api/settings', function () {
    $settings = DB::table('player_settings')
        ->select('setting_key', 'setting_value')
        ->get()
        ->pluck('setting_value', 'setting_key')
        ->toArray();
    
    foreach (['autoplay', 'loop', 'show_danmaku', 'show_screenshot', 'show_setting'] as $key) {
        if (isset($settings[$key])) {
            $settings[$key] = (bool) $settings[$key];
        }
    }
    
    foreach (['volume', 'preroll_duration', 'midroll_duration', 'postroll_duration'] as $key) {
        if (isset($settings[$key])) {
            $settings[$key] = (float) $settings[$key];
        }
    }
    
    return response()->json($settings);
});
