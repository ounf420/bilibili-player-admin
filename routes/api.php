<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// 类型映射：后台细分类型 → 前端基础类型
function mapCampaignType($type) {
    $map = [
        'preroll_5s' => 'preroll', 'preroll_15s' => 'preroll', 'preroll_30s' => 'preroll',
        'preroll_60s' => 'preroll', 'preroll_trueview' => 'preroll',
        'midroll' => 'midroll',
        'postroll' => 'postroll',
        'pause_max' => 'pause', 'pause_mini' => 'pause',
        'splash' => 'splash',
        'marquee' => 'marquee',
        'overlay' => 'overlay', 'corner' => 'overlay',
        'qrcode' => 'qrcode',
        'banner' => 'banner',
        'brand' => 'brand',
        'interactive' => 'interactive',
        'shake' => 'shake',
    ];
    return $map[$type] ?? $type;
}

// 获取广告列表
Route::get('/campaigns', function () {
    $ads = DB::table('ads')
        ->where('enabled', 1)
        ->select('id', 'name', 'type', 'media_url', 'media_type', 'brand_name', 'brand_logo', 'click_url', 'cta_text', 'duration', 'skippable', 'fullscreen', 'closable', 'enabled', 'trigger_time', 'skip_after', 'text_content', 'text_color', 'qrcode_url', 'priority', 'impressions', 'clicks', 'skips')
        ->orderBy('priority', 'desc')
        ->get();
    
    foreach ($ads as $ad) {
        $ad->type = mapCampaignType($ad->type);
    }
    
    return response()->json($ads);
});

// 广告展示追踪
Route::post('/campaigns/{id}/impression', function ($id) {
    DB::table('ads')->where('id', $id)->increment('impressions');
    return response()->json(['ok' => true]);
});

// 广告点击追踪
Route::post('/campaigns/{id}/click', function ($id) {
    DB::table('ads')->where('id', $id)->increment('clicks');
    return response()->json(['ok' => true]);
});

// 广告跳过追踪
Route::post('/campaigns/{id}/skip', function ($id) {
    DB::table('ads')->where('id', $id)->increment('skips');
    return response()->json(['ok' => true]);
});

// ========== 视频加载API（播放器用）==========
Route::get('/videos', function () {
    $videos = DB::table('videos')
        ->where('enabled', 1)
        ->select('id', 'title', 'cover', 'url', 'type', 'duration', 'description', 'category', 'tags', 'views', 'likes')
        ->orderBy('created_at', 'desc')
        ->get();
    return response()->json($videos);
});

Route::get('/videos/{id}', function ($id) {
    $video = DB::table('videos')
        ->where('id', $id)
        ->where('enabled', 1)
        ->select('id', 'title', 'cover', 'url', 'type', 'duration', 'description', 'category', 'tags', 'views', 'likes')
        ->first();
    if (!$video) {
        return response()->json(['error' => '视频不存在'], 404);
    }
    DB::table('videos')->where('id', $id)->increment('views');
    return response()->json($video);
});

Route::get('/recommend', function (\Illuminate\Http\Request $request) {
    $limit = min((int)$request->get('limit', 12), 24);
    $videos = DB::table('videos')
        ->where('enabled', 1)
        ->select('id', 'title', 'cover', 'category', 'score', 'views', 'duration')
        ->orderByRaw('RAND()')
        ->limit($limit)
        ->get();
    return response()->json($videos);
});


// ========== 认证API ==========
use App\Http\Controllers\Api\AuthController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/send-code', [AuthController::class, 'sendVerifyCode']);
Route::post('/auth/verify-code', [AuthController::class, 'verifyCode']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// ========== 第三方登录API ==========
use App\Http\Controllers\Api\SocialiteController;

Route::get('/socialite/login', [SocialiteController::class, 'login']);
Route::get('/socialite/callback', [SocialiteController::class, 'callback']);
Route::post('/socialite/login-with-temp', [SocialiteController::class, 'loginWithTemp']);

Route::middleware('auth.token')->group(function () {
    Route::post('/socialite/bind', [SocialiteController::class, 'bind']);
    Route::post('/socialite/unbind', [SocialiteController::class, 'unbind']);
    Route::get('/socialite/bindings', [SocialiteController::class, 'bindings']);
});

Route::middleware('auth.token')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::post('/auth/bind-phone', [AuthController::class, 'bindPhone']);
    Route::post('/auth/bind-email', [AuthController::class, 'bindEmail']);
    Route::post('/auth/bind-social', [AuthController::class, 'bindSocial']);
    Route::post('/auth/unbind-social', [AuthController::class, 'unbindSocial']);
    Route::get('/auth/login-history', [AuthController::class, 'loginHistory']);
    Route::get('/auth/export-data', [AuthController::class, 'exportData']);
    Route::post('/auth/delete-account', [AuthController::class, 'deleteAccount']);
});

// 获取启用的第三方登录平台
Route::get('/socialite/platforms', function () {
    $platforms = config('services.socialite.platforms', []);
    $platformNames = [
        'qq' => 'QQ', 'wx' => '微信', 'alipay' => '支付宝', 'sina' => '微博',
        'baidu' => '百度', 'douyin' => '抖音', 'huawei' => '华为', 'xiaomi' => '小米',
        'microsoft' => '微软', 'feishu' => '飞书', 'dingtalk' => '钉钉', 'gitee' => 'Gitee', 'github' => 'GitHub'
    ];
    $platformIcons = [
        'qq' => 'fab fa-qq', 'wx' => 'fab fa-weixin', 'alipay' => 'fab fa-alipay', 'sina' => 'fab fa-weibo',
        'baidu' => 'fab fa-baidu', 'douyin' => 'fab fa-tiktok', 'huawei' => 'fas fa-mobile-alt', 'xiaomi' => 'fas fa-mobile',
        'microsoft' => 'fab fa-microsoft', 'feishu' => 'fas fa-paper-plane', 'dingtalk' => 'fab fa-dingtalk', 'gitee' => 'fab fa-git-alt', 'github' => 'fab fa-github'
    ];
    $platformColors = [
        'qq' => '#12b7f5', 'wx' => '#07c160', 'alipay' => '#1677ff', 'sina' => '#e6162d',
        'baidu' => '#2319dc', 'douyin' => '#000', 'huawei' => '#cf0a2c', 'xiaomi' => '#ff6900',
        'microsoft' => '#00a4ef', 'feishu' => '#3370ff', 'dingtalk' => '#0089ff', 'gitee' => '#c71d23', 'github' => '#333'
    ];
    
    $result = [];
    foreach ($platforms as $key => $isEnabled) {
        if ($isEnabled) {
            $result[] = [
                'key' => $key,
                'name' => $platformNames[$key] ?? $key,
                'icon' => $platformIcons[$key] ?? 'fas fa-link',
                'color' => $platformColors[$key] ?? '#666',
            ];
        }
    }
    
    return response()->json(['success' => true, 'data' => $result]);
});

// ========== 公告API ==========
use App\Http\Controllers\Api\NoticeController;

Route::get('/notices', [NoticeController::class, 'index']);
Route::get('/notices/popup', [NoticeController::class, 'popup']);
Route::get('/notices/marquee', [NoticeController::class, 'marquee']);
Route::get('/notices/{id}', [NoticeController::class, 'show']);
Route::middleware('auth.token')->group(function () {
    Route::post('/notices/{id}/read', [NoticeController::class, 'markRead']);
});

// ========== 弹幕API ==========
Route::get('/danmaku', function (\Illuminate\Http\Request $request) {
    $videoId = $request->get('id');
    if (!$videoId) return response()->json([]);
    $list = DB::table('danmaku')
        ->where('video_id', $videoId)
        ->where('enabled', 1)
        ->where('status', 1)
        ->select('id', 'time', 'type', 'color', 'content', 'user')
        ->orderBy('time')
        ->get()
        ->map(function($d) {
            return [
                'id' => $d->id,
                'time' => (float) $d->time,
                'type' => $d->type ?: 'scroll',
                'color' => $d->color ?: '#ffffff',
                'text' => $d->content,
                'author' => $d->user ?: 'anonymous',
            ];
        });
    return response()->json($list);
});

Route::middleware('auth.token')->post('/danmaku', function (\Illuminate\Http\Request $request) {
    $vid = $request->get('id') ?: $request->get('video_id');
    $text = $request->get('text') ?: $request->get('content');
    if (!$vid || !$text) return response()->json(['success' => false, 'error' => '参数不完整']);
    $id = 'd' . bin2hex(random_bytes(15));

    $authorName = 'anonymous';
    $token = request()->bearerToken();
    if ($token) {
        $userId = \Illuminate\Support\Facades\Cache::get('auth_token_' . md5($token));
        if ($userId) {
            $user = DB::table('users')->where('id', $userId)->select('nickname', 'username')->first();
            $authorName = $user->nickname ?: $user->username ?: 'anonymous';
        }
    }

    DB::table('danmaku')->insert([
        'id' => $id,
        'video_id' => $vid,
        'content' => mb_substr($text, 0, 200),
        'time' => (float) ($request->get('time', 0)),
        'color' => $request->get('color', '#ffffff'),
        'type' => $request->get('type', 'scroll'),
        'user' => $authorName,
        'enabled' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return response()->json(['success' => true, 'id' => $id]);
});

// ========== 评论API ==========

Route::get('/comments', function (\Illuminate\Http\Request $request) {
    $videoId = $request->get('video_id');
    if (!$videoId) return response()->json(['data' => []]);
    $comments = DB::table('comments')
        ->where('video_id', $videoId)
        ->where('status', 1)
        ->orderByDesc('created_at')
        ->limit(50)
        ->get()
        ->map(function($c) {
            $user = null;
            if ($c->user_id) {
                $user = DB::table('users')->where('id', $c->user_id)
                    ->select('id', 'nickname', 'username', 'avatar')->first();
            }
            return [
                'id' => $c->id,
                'content' => $c->content,
                'likes' => $c->likes,
                'parent_id' => $c->parent_id,
                'created_at' => $c->created_at ? date('Y-m-d H:i', strtotime($c->created_at)) : '',
                'user' => $user ? [
                    'id' => $user->id,
                    'nickname' => $user->nickname ?: $user->username,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ] : ['nickname' => '匿名用户'],
            ];
        });
    return response()->json(['data' => $comments]);
});

Route::middleware('auth.token')->post('/comments', function (\Illuminate\Http\Request $request) {
    $vid = $request->get('video_id');
    $text = $request->get('content');
    if (!$vid || !$text) return response()->json(['success' => false, 'error' => '参数不完整']);

    $token = request()->bearerToken();
    $cacheKey = 'auth_token_' . md5($token);
    $userId = \Illuminate\Support\Facades\Cache::get($cacheKey);
    if (!$userId) return response()->json(['success' => false, 'error' => '未登录'], 401);

    DB::table('comments')->insert([
        'video_id' => $vid,
        'user_id' => $userId,
        'content' => mb_substr($text, 0, 500),
        'parent_id' => $request->get('parent_id', 0),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return response()->json(['success' => true]);
});

// ========== 评论点赞 ==========

Route::middleware('auth.token')->post('/comments/{id}/like', function ($id) {
    $token = request()->bearerToken();
    $cacheKey = 'auth_token_' . md5($token);
    $userId = \Illuminate\Support\Facades\Cache::get($cacheKey);
    if (!$userId) return response()->json(['success' => false], 401);

    $exists = DB::table('comment_likes')->where('comment_id', $id)->where('user_id', $userId)->first();
    if ($exists) {
        DB::table('comment_likes')->where('id', $exists->id)->delete();
        DB::table('comments')->where('id', $id)->decrement('likes');
        return response()->json(['success' => true, 'liked' => false]);
    }
    DB::table('comment_likes')->insert(['comment_id' => $id, 'user_id' => $userId, 'created_at' => now()]);
    DB::table('comments')->where('id', $id)->increment('likes');
    return response()->json(['success' => true, 'liked' => true]);
});

// 引入商城API
require __DIR__ . '/api_shop.php';
