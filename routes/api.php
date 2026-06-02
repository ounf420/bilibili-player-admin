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
    
    // 映射类型
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

// 获取视频列表
Route::get('/videos', function () {
    $videos = DB::table('videos')
        ->where('enabled', 1)
        ->select('id', 'title', 'cover', 'url', 'type', 'duration', 'description', 'category', 'tags', 'views', 'likes')
        ->orderBy('created_at', 'desc')
        ->get();
    return response()->json($videos);
});

// 获取单个视频
Route::get('/videos/{id}', function ($id) {
    $video = DB::table('videos')
        ->where('id', $id)
        ->where('enabled', 1)
        ->select('id', 'title', 'cover', 'url', 'type', 'duration', 'description', 'category', 'tags', 'views', 'likes')
        ->first();
    if (!$video) {
        return response()->json(['error' => '视频不存在'], 404);
    }
    return response()->json($video);
});

// 视频播放地址
Route::get('/videos/{id}/play', function ($id) {
    $video = DB::table('videos')
        ->where('id', $id)
        ->where('enabled', 1)
        ->first();
    if (!$video) {
        return response()->json(['error' => '视频不存在'], 404);
    }

    // VIP专属内容权限校验
    $vipLevel = $video->vip_level ?? 0;
    $userVipLevel = 0;
    $userIsVip = false;
    if ($vipLevel > 0) {
        try {
            $token = request()->bearerToken();
            if ($token) {
                $cacheKey = 'auth_token_' . md5($token);
                $userId = \Illuminate\Support\Facades\Cache::get($cacheKey);
                if ($userId) {
                    $user = DB::table('users')->where('id', $userId)
                        ->select('vip_level', 'vip_expire_at')->first();
                    if ($user) {
                        $userVipLevel = (int) $user->vip_level;
                        $userIsVip = $userVipLevel > 0 && $user->vip_expire_at && strtotime($user->vip_expire_at) > time();
                    }
                }
            }
        } catch (\Exception $e) {}

        if (!$userIsVip || $userVipLevel < $vipLevel) {
            $levelNames = [1 => '黄金VIP', 2 => '钻石VIP', 3 => '星钻VIP'];
            return response()->json([
                'error' => 'vip_required',
                'message' => '本片为' . ($levelNames[$vipLevel] ?? 'VIP') . '专属内容',
                'required_vip_level' => $vipLevel,
                'user_vip_level' => $userVipLevel,
            ], 403);
        }
    }

    // 增加播放次数
    DB::table('videos')->where('id', $id)->increment('views');
    return response()->json([
        'success' => true,
        'video' => $video
    ]);
});

// ========== 认证API ==========
use App\Http\Controllers\Api\AuthController;

// 公开接口
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/send-code', [AuthController::class, 'sendVerifyCode']);
Route::post('/auth/verify-code', [AuthController::class, 'verifyCode']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// ========== 第三方登录API ==========
use App\Http\Controllers\Api\SocialiteController;

// 公开接口
Route::get('/socialite/login', [SocialiteController::class, 'login']);
Route::get('/socialite/callback', [SocialiteController::class, 'callback']);
Route::post('/socialite/login-with-temp', [SocialiteController::class, 'loginWithTemp']);

// 需要登录的接口
Route::middleware('auth.token')->group(function () {
    Route::post('/socialite/bind', [SocialiteController::class, 'bind']);
    Route::post('/socialite/unbind', [SocialiteController::class, 'unbind']);
    Route::get('/socialite/bindings', [SocialiteController::class, 'bindings']);
});

// 需要登录的接口
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

// ========== 影视API ==========
use App\Http\Controllers\Api\VideoController;

Route::get('/movie/list', [VideoController::class, 'index']);
Route::get('/movie/recommend', [VideoController::class, 'recommend']);
Route::get('/movie/ranking', [VideoController::class, 'ranking']);
Route::get('/movie/filters', [VideoController::class, 'filters']);
Route::get('/movie/{id}', [VideoController::class, 'show']);

// ========== 收藏API ==========
use App\Http\Controllers\Api\FavoriteController;

Route::middleware('auth.token')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
});
Route::post('/favorites/check', [FavoriteController::class, 'check']);

// ========== 观看历史API ==========
use App\Http\Controllers\Api\WatchHistoryController;

Route::middleware('auth.token')->group(function () {
    Route::get('/history', [WatchHistoryController::class, 'index']);
    Route::post('/history/update', [WatchHistoryController::class, 'update']);
    Route::post('/history/clear', [WatchHistoryController::class, 'clear']);
    Route::get('/history/progress', [WatchHistoryController::class, 'getProgress']);
});

// ========== 点赞API ==========
use App\Http\Controllers\Api\LikeController;

Route::middleware('auth.token')->group(function () {
    Route::post('/likes/toggle', [LikeController::class, 'toggle']);
});
Route::post('/likes/check', [LikeController::class, 'check']);

// ========== VIP API ==========
use App\Http\Controllers\Api\VipController;

Route::get('/vip/plans', [VipController::class, 'plans']);
Route::middleware('auth.token')->group(function () {
    Route::get('/vip/status', [VipController::class, 'status']);
    Route::post('/vip/order', [VipController::class, 'createOrder']);
    Route::get('/vip/orders', [VipController::class, 'orders']);
});

// 成长体系
use App\Http\Controllers\Api\GrowthController;

Route::middleware('auth.token')->group(function () {
    Route::get('/growth/info', [GrowthController::class, 'info']);
    Route::post('/growth/sign', [GrowthController::class, 'sign']);
    Route::get('/growth/logs', [GrowthController::class, 'logs']);
});

// ========== 支付 API ==========
use App\Http\Controllers\Api\AlipayController;
use App\Http\Controllers\Api\PaymentController;

// 支付宝当面付（需登录）
Route::middleware('auth.token')->group(function () {
    Route::post('/payment/alipay/create', [AlipayController::class, 'createOrder']);
    Route::post('/payment/alipay/query', [AlipayController::class, 'queryOrder']);
});

// 支付宝异步通知（无需登录）
Route::post('/payment/alipay/notify', [AlipayController::class, 'notify']);

// 卡密兑换（需登录）
Route::middleware('auth.token')->group(function () {
    Route::post('/payment/card/redeem', [PaymentController::class, 'redeemCard']);
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
            // 查询弹幕用户VIP信息
            $vipLevel = 0;
            $userData = DB::table('users')->where('username', $d->user)
                ->orWhere('nickname', $d->user)
                ->select('vip_level', 'vip_expire_at')->first();
            if ($userData && $userData->vip_level > 0 && $userData->vip_expire_at && strtotime($userData->vip_expire_at) > time()) {
                $vipLevel = (int) $userData->vip_level;
            }
            return [
                'id' => $d->id,
                'time' => (float) $d->time,
                'type' => $d->type ?: 'scroll',
                'color' => $d->color ?: '#ffffff',
                'text' => $d->content,
                'author' => $d->user ?: 'anonymous',
                'vip_level' => $vipLevel,
            ];
        });
    return response()->json($list);
});

Route::middleware('auth.token')->post('/danmaku', function (\Illuminate\Http\Request $request) {
    $vid = $request->get('id') ?: $request->get('video_id');
    $text = $request->get('text') ?: $request->get('content');
    if (!$vid || !$text) return response()->json(['success' => false, 'error' => '参数不完整']);
    $id = 'd' . bin2hex(random_bytes(15));

    // 获取用户VIP状态
    $userVipLevel = 0;
    $userId = null;
    try {
        $token = request()->bearerToken();
        if ($token) {
            $cacheKey = 'auth_token_' . md5($token);
            $userId = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($userId) {
                $user = DB::table('users')->where('id', $userId)
                    ->select('vip_level', 'vip_expire_at', 'nickname', 'username')->first();
                if ($user && $user->vip_level > 0 && $user->vip_expire_at && strtotime($user->vip_expire_at) > time()) {
                    $userVipLevel = (int) $user->vip_level;
                }
            }
        }
    } catch (\Exception $e) {}

    // 颜色限制：非VIP只能用白色
    $color = $request->get('color', '#ffffff');
    $vipOnlyColors = ['#ffd700', '#ff69b4', '#00ffff', '#ff4500', '#7cfc00', '#ff1493', '#00bfff'];
    if ($userVipLevel <= 0 && !in_array($color, ['#ffffff', '#fff'])) {
        $color = '#ffffff'; // 非VIP强制白色
    }

    // 获取用户名
    $authorName = 'anonymous';
    if ($userId) {
        $user = DB::table('users')->where('id', $userId)->select('nickname', 'username')->first();
        $authorName = $user->nickname ?: $user->username ?: 'anonymous';
    }

    DB::table('danmaku')->insert([
        'id' => $id,
        'video_id' => $vid,
        'content' => mb_substr($text, 0, 200),
        'time' => (float) ($request->get('time', 0)),
        'color' => $color,
        'type' => $request->get('type', 'scroll'),
        'user' => $authorName,
        'enabled' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    return response()->json(['success' => true, 'id' => $id, 'vip_level' => $userVipLevel]);
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
            $user = null; $vipLevel = 0;
            if ($c->user_id) {
                $user = DB::table('users')->where('id', $c->user_id)
                    ->select('id', 'nickname', 'username', 'avatar', 'vip_level', 'vip_expire_at')->first();
                if ($user && $user->vip_level > 0 && $user->vip_expire_at && strtotime($user->vip_expire_at) > time()) {
                    $vipLevel = (int) $user->vip_level;
                }
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
                    'vip_level' => $vipLevel,
                ] : ['nickname' => '匿名用户', 'vip_level' => 0],
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

// ========== 搜索建议 ==========

Route::get('/search/suggest', function (\Illuminate\Http\Request $request) {
    $q = $request->get('q', '');
    if (mb_strlen($q) < 1) return response()->json([]);
    $videos = DB::table('videos')
        ->where('enabled', 1)
        ->where(function($w) use ($q) {
            $w->where('title', 'like', "%{$q}%")
              ->orWhere('tags', 'like', "%{$q}%")
              ->orWhere('actors', 'like', "%{$q}%");
        })
        ->select('id', 'title', 'cover', 'category', 'score', 'vip_level')
        ->orderByDesc('views')
        ->limit(8)
        ->get();
    return response()->json($videos);
});

// 记录搜索
Route::post('/search/log', function (\Illuminate\Http\Request $request) {
    $keyword = $request->get('q', '');
    if (!$keyword) return response()->json(['success' => false]);
    $userId = null;
    try {
        $token = request()->bearerToken();
        if ($token) $userId = \Illuminate\Support\Facades\Cache::get('auth_token_' . md5($token));
    } catch(\Exception $e) {}
    DB::table('search_logs')->insert([
        'keyword' => mb_substr($keyword, 0, 200),
        'user_id' => $userId,
        'created_at' => now(),
    ]);
    return response()->json(['success' => true]);
});

// ========== 智能推荐 ==========

Route::get('/recommend', function (\Illuminate\Http\Request $request) {
    $videoId = $request->get('video_id');
    $limit = min((int)$request->get('limit', 12), 24);

    $video = null;
    if ($videoId) $video = DB::table('videos')->where('id', $videoId)->select('category', 'tags', 'genre')->first();

    $query = DB::table('videos')->where('enabled', 1);
    if ($videoId) $query->where('id', '!=', $videoId);

    if ($video && $video->category) {
        $query->where(function($q) use ($video) {
            $q->where('category', $video->category);
            if ($video->tags) {
                $tags = explode(',', $video->tags);
                foreach (array_slice($tags, 0, 3) as $tag) {
                    $q->orWhere('tags', 'like', '%' . trim($tag) . '%');
                }
            }
        });
    }

    $videos = $query->select('id', 'title', 'cover', 'category', 'score', 'views', 'duration', 'vip_level')
        ->orderByRaw('RAND()')
        ->limit($limit)
        ->get();

    return response()->json($videos);
});
