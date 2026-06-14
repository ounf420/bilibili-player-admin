<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Installation routes
Route::get('/install', [\App\Http\Controllers\InstallController::class, 'index'])->name('install.index');
Route::get('/install/database', [\App\Http\Controllers\InstallController::class, 'database'])->name('install.database');
Route::post('/install/test-database', [\App\Http\Controllers\InstallController::class, 'testDatabase'])->name('install.test-database');
Route::get('/install/admin', [\App\Http\Controllers\InstallController::class, 'admin'])->name('install.admin');
Route::post('/install', [\App\Http\Controllers\InstallController::class, 'install'])->name('install.run');
Route::get('/install/complete', [\App\Http\Controllers\InstallController::class, 'complete'])->name('install.complete');

// 品牌首页
Route::get('/', function () {
    return view('home');
});

// 用户中心（兼容旧链接）
Route::get('/user-center', function () {
    return redirect('/user');
});

// 播放器页面
Route::get('/player', function () {
    return view('play');
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
})->name('user.index');

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
    
    foreach (['volume', 'preroll_duration', 'midroll_duration', 'postroll_duration', 'splash_duration'] as $key) {
        if (isset($settings[$key])) {
            $settings[$key] = (float) $settings[$key];
        }
    }
    
    return response()->json($settings);
});

// 用户播放器管理
use App\Http\Controllers\UserPlayerController;

Route::middleware(['auth'])->prefix('user/player')->name('user.player.')->group(function () {
    Route::get('/', [UserPlayerController::class, 'index'])->name('index');
    Route::get('/create', [UserPlayerController::class, 'create'])->name('create');
    Route::post('/', [UserPlayerController::class, 'store'])->name('store');
    Route::get('/{id}', [UserPlayerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [UserPlayerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UserPlayerController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserPlayerController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/video', [UserPlayerController::class, 'addVideo'])->name('video.add');
    Route::post('/{id}/video/remove', [UserPlayerController::class, 'removeVideo'])->name('video.remove');
});

// 播放器嵌入
Route::get('/embed/player/{slug}', [UserPlayerController::class, 'embed'])->name('player.embed');

// 优酷风格播放器
Route::get('/youku/player/{slug}', [UserPlayerController::class, 'embedYouku'])->name('player.embed.youku');

// 卡密导出
Route::get('/admin/cards/export', function () {
    $cards = \App\Models\Card::where('status', 'unused')
        ->orderBy('batch_id')
        ->orderBy('created_at')
        ->get();
    
    $csv = "卡号,卡密,面值,批次号,备注\n";
    foreach ($cards as $card) {
        $csv .= "{$card->card_no},{$card->card_secret},{$card->amount},{$card->batch_id},{$card->remark}\n";
    }

    return response($csv)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename=cards_' . date('YmdHis') . '.csv');
})->name('admin.cards.export');

// ========== 苹果CMS播放器页面 ==========
// 格式: /player/vod-xxx.html 或 /player/xxx.html
Route::get('/player/vod-{id}.html', function ($id) {
    $video = \App\Models\Video::find($id);
    if (!$video) {
        abort(404, '视频不存在');
    }
    
    // 查找包含该视频的播放器
    $player = \App\Models\UserPlayer::whereHas('videos', function($q) use ($id) {
        $q->where('video_id', $id);
    })->first();
    
    return view('player.maccms', [
        'video' => $video,
        'player' => $player,
    ]);
})->name('player.vod');

// 格式: /player/play-xxx.html
Route::get('/player/play-{id}.html', function ($id) {
    $video = \App\Models\Video::find($id);
    if (!$video) {
        abort(404, '视频不存在');
    }
    
    return view('player.maccms', [
        'video' => $video,
    ]);
})->name('player.play');

// ========== 视频解析API ==========
// 移至api.php，无需CSRF验证
