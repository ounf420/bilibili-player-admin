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
        ->select('id', 'name', 'type', 'media_url', 'media_type', 'brand_name', 'brand_logo', 'click_url', 'cta_text', 'duration', 'skippable', 'fullscreen', 'closable', 'enabled', 'trigger_time', 'skip_after', 'text_content', 'text_color', 'qrcode_url', 'priority', 'impressions', 'clicks', 'skips', 'badge_text', 'badge_color', 'progress_color', 'overlay_opacity', 'animation', 'text_stroke', 'decoration_id')
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

// ========== 装饰方案API ==========
Route::get('/decorations', function () {
    $decorations = DB::table('decorations')
        ->where('enabled', 1)
        ->select('id', 'name', 'badge_text', 'badge_color', 'badge_text_color', 'progress_color', 'progress_bg', 'overlay_opacity', 'overlay_gradient', 'animation', 'text_stroke', 'text_shadow_color', 'cta_style', 'cta_color', 'cta_text_color', 'close_btn_style', 'countdown_style', 'show_brand_area', 'show_progress_bar', 'custom_css')
        ->orderBy('sort_order', 'desc')
        ->get();
    return response()->json($decorations);
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

// ========== 视频解析API ==========
use App\Http\Controllers\Api\VideoParserController;

Route::post('/video/parse', [VideoParserController::class, 'parse'])->name('video.parse');
Route::get('/video/platforms', [VideoParserController::class, 'platforms'])->name('video.platforms');

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

// 公开：获取套餐列表
Route::get('/plans', function () {
    $plans = \App\Models\PlayerPlan::active()
        ->orderBy('type')
        ->orderBy('level')
        ->orderBy('sort_order')
        ->get(['id', 'name', 'code', 'type', 'level', 'duration_type', 'duration_days', 'price', 'sale_price', 'features', 'badge', 'description']);
    
    return response()->json(['data' => $plans]);
});

// 用户播放器管理
Route::middleware('auth.token')->group(function () {
    Route::get('/user/players', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $players = \App\Models\UserPlayer::where('user_id', $user->id)
            ->withCount('videos')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // 获取额度信息
        $quota = \App\Models\PlayerQuota::where('user_id', $user->id)->first();
        $quotaInfo = $quota ? [
            'total' => $quota->total_quota,
            'used' => $quota->used_quota,
            'available' => $quota->available_quota,
        ] : ['total' => 0, 'used' => 0, 'available' => 0];
        
        // 获取配置信息
        $config = [
            'enable_purchase' => \Illuminate\Support\Facades\Cache::get('player_enable_purchase', false),
            'price_per_quota' => \Illuminate\Support\Facades\Cache::get('player_price_per_quota', 9.9),
            'max_quota' => \Illuminate\Support\Facades\Cache::get('player_max_quota', 10),
        ];
        
        return response()->json(array_merge((array)$players, ['data' => $players->items(), 'quota' => $quotaInfo, 'config' => $config]));
    });

    Route::post('/user/players', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $request->validate(['name' => 'required|string|max:100']);
        
        // 检查并消耗额度
        $quota = \App\Models\PlayerQuota::where('user_id', $user->id)->first();
        if (!$quota || $quota->available_quota <= 0) {
            $enablePurchase = \Illuminate\Support\Facades\Cache::get('player_enable_purchase', false);
            $message = $enablePurchase ? '播放器额度不足，请先购买' : '播放器额度不足';
            return response()->json(['message' => $message], 403);
        }
        
        // 检查是否超过最大额度限制
        $maxQuota = \Illuminate\Support\Facades\Cache::get('player_max_quota', 10);
        $currentPlayers = \App\Models\UserPlayer::where('user_id', $user->id)->count();
        if ($currentPlayers >= $maxQuota) {
            return response()->json(['message' => '已达最大播放器数量限制'], 403);
        }
        
        $quota->useQuota();
        
        // 从后台配置获取默认值
        $player = \App\Models\UserPlayer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::random(16),
            'theme_color' => $request->theme_color ?? Cache::get('default_theme_color', '#6366f1'),
            'logo_url' => $request->logo_url ?? Cache::get('default_logo_url', ''),
            'progress_icon_url' => $request->progress_icon_url,
            'watermark_text' => $request->watermark_text ?? Cache::get('default_watermark_text', ''),
            'watermark_position' => $request->watermark_position ?? Cache::get('default_watermark_position', 'bottom-right'),
            'watermark_font_size' => $request->watermark_font_size ?? Cache::get('default_watermark_font_size', 14),
            'watermark_color' => $request->watermark_color ?? Cache::get('default_watermark_color', '#ffffff'),
            'watermark_opacity' => $request->watermark_opacity ?? (Cache::get('default_watermark_opacity', 30) / 100),
            'watermark_x' => $request->watermark_x,
            'watermark_y' => $request->watermark_y,
            'aspect_ratio' => $request->aspect_ratio ?? Cache::get('default_aspect_ratio', '16:9'),
            'border_radius' => $request->border_radius ?? Cache::get('default_border_radius', '12px'),
            'width' => Cache::get('default_width', '100%'),
            'height' => Cache::get('default_height', 'auto'),
            'autoplay' => $request->boolean('autoplay', Cache::get('default_autoplay', false)),
            'loop_play' => $request->boolean('loop_play', Cache::get('default_loop_play', false)),
            'muted' => $request->boolean('muted', Cache::get('default_muted', false)),
            'show_controls' => Cache::get('default_show_controls', true),
            'show_danmaku' => $request->boolean('show_danmaku', Cache::get('default_show_danmaku', false)),
            'show_download' => $request->boolean('show_download', false),
            'show_share' => $request->boolean('show_share', true),
            'version' => $request->version ?? Cache::get('default_version', 'free'),
            'template' => $request->template ?? 'standard',
        ]);
        return response()->json(['data' => $player, 'message' => '创建成功']);
    });

    Route::get('/user/players/{id}', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $player = \App\Models\UserPlayer::where('user_id', $user->id)->withCount('videos')->findOrFail($id);

        // 根据模板生成embed链接
        $template = $player->template ?: 'standard';
        $pathPrefix = $template === 'youku' ? '/youku/player/' : '/embed/player/';
        $embedUrl = url($pathPrefix . $player->slug);

        $player->embed_url = $embedUrl;
        $player->embed_code = '<iframe src="' . $embedUrl . '?id=' . $player->player_code . '&key=' . $player->player_key . '" width="100%" height="auto" frameborder="0" allowfullscreen></iframe>';
        $player->access_url = $embedUrl . '?id=' . $player->player_code . '&key=' . $player->player_key;
        
        // 添加版本信息
        $player->version_info = [
            'version' => $player->version,
            'effective_version' => $player->getEffectiveVersion(),
            'version_active' => $player->isVersionActive(),
            'can_customize' => $player->canCustomizeAppearance(),
            'can_custom_domain' => $player->canUseCustomDomain(),
            'has_ad_module' => $player->hasAdModule(),
            'has_ad_free' => $player->has_ad_free || $player->version === 'flagship',
            'has_super_ad' => $player->has_super_ad ?? false,
            'ad_free_expires_at' => $player->ad_free_expires_at?->toDateTimeString(),
            'ad_module_expires_at' => $player->ad_module_expires_at?->toDateTimeString(),
            'version_expire_at' => $player->version_expire_at?->toDateTimeString(),
        ];
        
        return response()->json(['data' => $player]);
    });

    Route::put('/user/players/{id}', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $player = \App\Models\UserPlayer::where('user_id', $user->id)->findOrFail($id);
        
        $updateData = $request->only([
            'name', 'theme_color', 'logo_url', 'progress_icon_url', 'logo_position', 'logo_size', 'logo_opacity',
            'background_image', 'background_image_mobile',
            'watermark_text', 'watermark_position', 'watermark_font_size',
            'watermark_color', 'watermark_opacity', 'watermark_x', 'watermark_y',
            'aspect_ratio', 'border_radius',
            'autoplay', 'loop_play', 'muted', 'show_danmaku', 'show_download', 'show_share',
            'version', 'ad_mode',
            'preroll_duration', 'midroll_duration', 'postroll_duration',
            'show_marquee', 'marquee_text', 'marquee_speed', 'marquee_color',
            'parse_url',
        ]);
        
        // 版本权限检查：免费版不能自定义外观，但可以升级版本
        if (!$player->canCustomizeAppearance()) {
            // 免费版只能改名称和版本，不能改外观
            $allowedForFree = ['name', 'autoplay', 'loop_play', 'muted', 'show_danmaku', 'show_download', 'show_share', 'version'];
            $updateData = array_intersect_key($updateData, array_flip($allowedForFree));
        }
        
        $player->update($updateData);
        return response()->json(['data' => $player, 'message' => '更新成功']);
    });

    Route::delete('/user/players/{id}', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        \App\Models\UserPlayer::where('user_id', $user->id)->findOrFail($id)->delete();
        
        // 释放额度
        $quota = \App\Models\PlayerQuota::where('user_id', $user->id)->first();
        if ($quota) {
            $quota->releaseQuota();
        }
        
        return response()->json(['message' => '删除成功']);
    });
    
    // 用户广告管理
    Route::get('/user/players/{id}/ads', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $player = \App\Models\UserPlayer::where('user_id', $user->id)->findOrFail($id);
        
        // 检查是否开通广告模块
        if (!$player->has_ad_module) {
            return response()->json(['message' => '请先开通广告模块', 'code' => 403], 403);
        }
        
        $ads = $player->ads()->orderBy('sort_order')->get();
        return response()->json(['data' => $ads]);
    });

    Route::post('/user/players/{id}/ads', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $player = \App\Models\UserPlayer::where('user_id', $user->id)->findOrFail($id);
        
        // 检查是否开通广告模块
        if (!$player->has_ad_module) {
            return response()->json(['message' => '请先开通广告模块', 'code' => 403], 403);
        }
        
        $request->validate([
            'name' => 'required|string|max:100',
            'media_url' => 'required|url',
            'position' => 'required|in:preroll,midroll,postroll,pause',
        ]);

        $adData = $request->only([
            'name', 'media_url', 'media_type', 'content',
            'cover_url', 'title', 'description',
            'cta_text', 'cta_url', 'logo_url',
            'click_url', 'position', 'duration', 'skippable',
            'skip_after', 'progress_icon', 'enabled', 'sort_order',
            'start_at', 'end_at', 'frequency_cap', 'priority',
        ]);
        
        $targetType = $request->input('target_type', 'single');
        $targetPlayerIds = $request->input('target_player_ids');
        
        if ($targetType === 'all') {
            // 投放到所有有广告模块的播放器
            $players = \App\Models\UserPlayer::where('user_id', $user->id)
                ->where('has_ad_module', true)
                ->get();
            
            $createdAds = [];
            foreach ($players as $p) {
                $createdAds[] = $p->ads()->create($adData);
            }
            
            return response()->json(['data' => $createdAds, 'message' => '广告已投放到 ' . count($createdAds) . ' 个播放器']);
        } elseif ($targetType === 'multiple' && is_array($targetPlayerIds)) {
            // 投放到指定播放器（只投放有广告模块的）
            $players = \App\Models\UserPlayer::where('user_id', $user->id)
                ->whereIn('id', $targetPlayerIds)
                ->where('has_ad_module', true)
                ->get();
            
            $createdAds = [];
            foreach ($players as $p) {
                $createdAds[] = $p->ads()->create($adData);
            }
            
            return response()->json(['data' => $createdAds, 'message' => '广告已投放到 ' . count($createdAds) . ' 个播放器']);
        } else {
            // 单个播放器
            $ad = $player->ads()->create($adData);
            return response()->json(['data' => $ad, 'message' => '广告创建成功']);
        }
    });

    Route::put('/user/players/{playerId}/ads/{adId}', function ($playerId, $adId, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $player = \App\Models\UserPlayer::where('user_id', $user->id)->findOrFail($playerId);
        
        // 检查是否开通广告模块
        if (!$player->has_ad_module) {
            return response()->json(['message' => '请先开通广告模块', 'code' => 403], 403);
        }
        
        $ad = $player->ads()->findOrFail($adId);
        
        $ad->update($request->only([
            'name', 'media_url', 'media_type', 'content',
            'cover_url', 'title', 'description',
            'cta_text', 'cta_url', 'logo_url',
            'click_url', 'position', 'duration', 'skippable',
            'skip_after', 'progress_icon', 'enabled', 'sort_order',
            'start_at', 'end_at', 'frequency_cap', 'priority',
        ]));

        return response()->json(['data' => $ad, 'message' => '广告更新成功']);
    });

    Route::delete('/user/players/{playerId}/ads/{adId}', function ($playerId, $adId, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $player = \App\Models\UserPlayer::where('user_id', $user->id)->findOrFail($playerId);
        
        // 检查是否开通广告模块
        if (!$player->has_ad_module) {
            return response()->json(['message' => '请先开通广告模块', 'code' => 403], 403);
        }
        
        $player->ads()->findOrFail($adId)->delete();

        return response()->json(['message' => '广告删除成功']);
    });

    // 更新播放器广告模式
    Route::put('/user/players/{id}/ad-mode', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        $player = \App\Models\UserPlayer::where('user_id', $user->id)->findOrFail($id);
        
        $request->validate([
            'ad_mode' => 'required|in:user,platform,mixed,none',
        ]);

        $player->update(['ad_mode' => $request->ad_mode]);

        return response()->json(['message' => '广告模式已更新', 'ad_mode' => $player->ad_mode]);
    });

    // 生成部署代码（支持广告）
    Route::get('/user/players/{id}/deploy', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $player = \App\Models\UserPlayer::where('user_id', $user->id)
            ->with(['engine', 'plan', 'videos'])
            ->findOrFail($id);
        
        // 获取该播放器的广告
        $materialAds = $player->enabledAds()->get()->map(function($ad) {
            return [
                'id' => $ad->id,
                'name' => $ad->name,
                'type' => $ad->type ?? 'video',
                'position' => $ad->position ?? 'preroll',
                'duration' => $ad->duration ?? 5,
                'click_url' => $ad->click_url,
                'media_url' => $ad->media_url,
                'content' => $ad->content,
                'skip_seconds' => $ad->skip_seconds ?? 0,
            ];
        })->toArray();
        
        // 生成部署代码
        $deployCode = \App\Services\Engines\EngineFactory::generateDeployCode(
            $player->engine_code,
            [
                'name' => $player->name,
                'player_id' => $player->player_code ?: $player->id,
                'player_key' => $player->player_key,
                'api_url' => url('/api/player'),
                'video_url' => $player->videos->first()->url ?? '',
                'cover_url' => $player->videos->first()->cover_url ?? '',
                'theme_color' => $player->theme_color ?? '#ff6b00',
                'autoplay' => $player->autoplay,
                'loop_play' => $player->loop_play,
                'muted' => $player->muted,
                'watermark_text' => $player->watermark_text,
                'watermark_position' => $player->watermark_position ?? 'bottom-right',
                'width' => $player->width ?: '100%',
                'height' => $player->height ?: '500px',
            ],
            $materialAds
        );
        
        return response()->json([
            'data' => [
                'html' => $deployCode['html'],
                'js' => $deployCode['js'],
                'cdn' => $deployCode['cdn'],
                'embed_url' => $player->embed_url,
                'embed_code' => $player->embed_code,
                'access_url' => $player->embed_url,
                'player_id' => $player->player_code ?: $player->id,
                'player_key' => $player->player_key,
                'has_ads' => count($materialAds) > 0,
                'ads_count' => count($materialAds),
            ]
        ]);
    });

    // 获取用户套餐等级
    Route::get('/user/plan-level', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        // 查询用户最高的有效套餐（PlayerOrder用product_id和product_type='plan'）
        $highestPlan = \App\Models\PlayerOrder::where('user_id', $user->id)
            ->where('status', \App\Models\PlayerOrder::STATUS_PAID)
            ->where('product_type', 'plan')
            ->join('player_plans', 'player_orders.product_id', '=', 'player_plans.id')
            ->orderBy('player_plans.level', 'desc')
            ->first();
        
        $level = $highestPlan ? $highestPlan->level : 0;
        
        // 获取可选版本配置
        $availableVersions = \Illuminate\Support\Facades\Cache::get('default_versions', ['free', 'basic', 'advanced', 'flagship']);
        
        return response()->json([
            'level' => $level,
            'available_versions' => $availableVersions,
        ]);
    });

    // 获取播放器列表（用于广告投放选择）
    Route::get('/user/players-list', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $query = \App\Models\UserPlayer::where('user_id', $user->id)
            ->active()
            ->select('id', 'name', 'slug', 'engine_code', 'has_ad_module');
        
        // 如果是素材管理页面请求，只返回有广告模块的播放器
        if ($request->query('ad_module_only') === '1') {
            $query->where('has_ad_module', true);
        }
        
        $players = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json(['data' => $players]);
    });
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
Route::get('/notices/list', [NoticeController::class, 'list']);
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

// ========== 用户统计API ==========
Route::middleware('auth.token')->get('/user/stats', function (\Illuminate\Http\Request $request) {
    $user = $request->user();
    if (!$user) return response()->json(['message' => '用户未登录'], 401);
    $userId = $user->id;
    
    // 用户的播放器ID列表
    $playerIds = DB::table('user_players')->where('user_id', $userId)->pluck('id')->toArray();
    
    // 基础统计
    $playerCount = count($playerIds);
    $videoCount = $playerCount > 0 ? DB::table('user_player_videos')
        ->whereIn('player_id', $playerIds)
        ->count() : 0;
    
    // 播放器总播放量
    $totalViews = $playerCount > 0 ? DB::table('user_players')
        ->where('user_id', $userId)
        ->sum('view_count') : 0;
    
    // 订单统计
    $orderCount = DB::table('orders')->where('user_id', $userId)->count();
    $totalSpent = DB::table('orders')
        ->where('user_id', $userId)
        ->where('status', 'paid')
        ->sum('amount');
    
    // 最近7天每日播放量（从ad_track的event=impression统计）
    $dailyViews = [];
    if ($playerCount > 0) {
        $dailyViews = DB::table('ad_track')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->whereIn('video_id', function($q) use ($playerIds) {
                $q->select('video_id')->from('user_player_videos')->whereIn('player_id', $playerIds);
            })
            ->where('event', 'impression')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    // 热门播放器TOP5
    $topPlayers = DB::table('user_players')
        ->where('user_id', $userId)
        ->select('id', 'name', 'view_count')
        ->orderByDesc('view_count')
        ->limit(5)
        ->get();
    
    // 广告收入统计（ad_track表无revenue字段，暂用0）
    $adRevenueTotal = $playerCount > 0 ? DB::table('ad_track')
        ->whereIn('video_id', function($q) use ($playerIds) {
            $q->select('video_id')->from('user_player_videos')->whereIn('player_id', $playerIds);
        })
        ->where('event', 'impression')
        ->count() : 0;
    
    // 版本信息
    $player = $playerCount > 0 ? DB::table('user_players')->where('user_id', $userId)->first() : null;
    $version = $player ? $player->version : 'free';
    $versionExpire = $player ? $player->version_expire_at : null;
    
    return response()->json([
        'basic' => [
            'player_count' => $playerCount,
            'video_count' => $videoCount,
            'total_views' => (int)$totalViews,
            'order_count' => $orderCount,
            'total_spent' => round($totalSpent, 2),
        ],
        'daily_views' => $dailyViews,
        'top_players' => $topPlayers,
        'ad_revenue' => [
            'preroll' => 0,
            'midroll' => 0,
            'postroll' => 0,
            'total' => 0,
        ],
        'version' => [
            'name' => $version ?? 'free',
            'expire_at' => $versionExpire,
        ],
    ]);
});

// ========== 财务系统API ==========

// 获取套餐列表（购买用）
Route::get('/plans/purchase', function () {
    $plans = \App\Models\PlayerPlan::active()
        ->orderBy('type')
        ->orderBy('level')
        ->orderBy('sort_order')
        ->get(['id', 'name', 'code', 'type', 'level', 'duration_type', 'duration_days', 'price', 'sale_price', 'features', 'badge', 'description']);
    
    return response()->json(['data' => $plans]);
});

Route::middleware('auth.token')->group(function () {
    // 兑换卡密
    Route::post('/finance/redeem', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $request->validate([
            'card_no' => 'required|string',
            'card_secret' => 'required|string'
        ]);
        
        $card = \App\Models\Card::where('card_no', $request->card_no)
            ->where('card_secret', $request->card_secret)
            ->first();
        
        if (!$card) {
            return response()->json(['message' => '卡号或卡密错误'], 400);
        }
        
        if ($card->status === 'used') {
            return response()->json(['message' => '该卡密已被使用'], 400);
        }
        
        if ($card->status === 'disabled') {
            return response()->json(['message' => '该卡密已被禁用'], 400);
        }
        
        // 使用卡密
        $card->update([
            'status' => 'used',
            'used_by' => $user->id,
            'used_at' => now()
        ]);
        
        // 增加用户余额
        $balance = \App\Models\UserBalance::getOrCreate($user->id);
        $balance->recharge($card->amount);
        
        // 记录交易
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'recharge',
            'amount' => $card->amount,
            'balance_after' => $balance->balance,
            'description' => "兑换卡密 {$card->card_no}",
            'related_id' => $card->id,
            'related_type' => 'card'
        ]);
        
        return response()->json([
            'message' => "兑换成功，余额增加 ¥{$card->amount}",
            'balance' => $balance->balance
        ]);
    });
    
    // 获取余额
    Route::get('/finance/balance', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $balance = \App\Models\UserBalance::getOrCreate($user->id);
        
        return response()->json([
            'balance' => $balance->balance,
            'total_recharged' => $balance->total_recharged,
            'total_spent' => $balance->total_spent
        ]);
    });
    
    // 交易记录
    Route::get('/finance/transactions', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $transactions = \App\Models\Transaction::where('user_id', $user->id)
            ->latest()
            ->paginate(20);
        
        return response()->json($transactions);
    });
    
    // 创建订单
    Route::post('/orders', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $request->validate([
            'plan_id' => 'required|exists:player_plans,id'
        ]);
        
        $plan = \App\Models\PlayerPlan::findOrFail($request->plan_id);
        
        // 创建订单
        $order = \App\Models\Order::create([
            'order_no' => \App\Models\Order::generateOrderNo(),
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'player_id' => $request->player_id,  // 可选，关联播放器
            'amount' => $plan->sale_price ?: $plan->price,
            'status' => 'pending'
        ]);
        
        return response()->json([
            'message' => '订单创建成功',
            'data' => $order
        ]);
    });
    
    // 余额支付订单
    Route::post('/orders/{id}/pay', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $order = \App\Models\Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $balance = \App\Models\UserBalance::getOrCreate($user->id);
        
        if ($balance->balance < $order->amount) {
            return response()->json(['message' => '余额不足，请先充值'], 400);
        }
        
        // 扣款
        $balance->spend($order->amount);
        
        // 更新订单状态
        $order->update([
            'status' => 'paid',
            'pay_method' => 'balance',
            'paid_at' => now()
        ]);
        
        // 记录交易
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount' => -$order->amount,
            'balance_after' => $balance->balance,
            'description' => "购买套餐：{$order->plan->name}",
            'related_id' => $order->id,
            'related_type' => 'order'
        ]);
        
        // 计算过期时间
        $expiresAt = null;
        if ($order->plan->duration_days > 0) {
            $expiresAt = now()->addDays($order->plan->duration_days);
        }
        
        // 升级用户版本（创建PlayerOrder记录）
        $plan = $order->plan;
        \App\Models\PlayerOrder::create([
            'order_no' => \App\Models\PlayerOrder::generateOrderNo(),
            'user_id' => $user->id,
            'product_type' => 'plan',
            'product_id' => $order->plan_id,
            'product_name' => $plan->name,
            'amount' => $order->amount,
            'payment_method' => 'balance',
            'status' => \App\Models\PlayerOrder::STATUS_PAID,
            'paid_at' => now(),
        ]);
        
        // 处理额度购买（不需要关联播放器）
        if (str_starts_with($plan->code, 'quota_')) {
            $quotaAmount = (int) str_replace('quota_', '', $plan->code);
            $quota = \App\Models\PlayerQuota::firstOrCreate(
                ['user_id' => $user->id],
                ['total_quota' => 1, 'used_quota' => 0, 'bonus_quota' => 0]
            );
            $quota->addQuota($quotaAmount);
            
            return response()->json([
                'message' => "支付成功，已增加 {$quotaAmount} 个播放器额度",
                'balance' => $balance->balance,
                'quota' => [
                    'total' => $quota->total_quota,
                    'used' => $quota->used_quota,
                    'available' => $quota->available_quota,
                ],
            ]);
        }
        
        // 如果订单关联了播放器，更新该播放器
        if ($order->player_id) {
            $player = \App\Models\UserPlayer::where('user_id', $user->id)
                ->where('id', $order->player_id)
                ->first();
            if ($player) {
                if ($plan->type === 'ad_module') {
                    // 购买广告模块 - 根据套餐时长设置到期时间
                    $expiresAt = null;
                    if ($plan->duration_type === 4) {
                        // 永久
                        $expiresAt = null;
                    } elseif ($plan->duration_days > 0) {
                        // 月卡/季卡/年卡 - 在原到期时间基础上续期
                        $baseTime = ($player->ad_module_expires_at && $player->ad_module_expires_at->isFuture())
                            ? $player->ad_module_expires_at
                            : now();
                        $expiresAt = $baseTime->addDays($plan->duration_days);
                    }
                    $player->update([
                        'has_ad_module' => true,
                        'ad_module_expires_at' => $expiresAt,
                    ]);
                } elseif ($plan->type === 'ad_free') {
                    // 购买去广告功能 - 根据套餐时长设置到期时间
                    $expiresAt = null;
                    if ($plan->duration_type === 4) {
                        // 永久
                        $expiresAt = null;
                    } elseif ($plan->duration_days > 0) {
                        // 月卡/季卡/年卡 - 在原到期时间基础上续期
                        $baseTime = ($player->ad_free_expires_at && $player->ad_free_expires_at->isFuture())
                            ? $player->ad_free_expires_at
                            : now();
                        $expiresAt = $baseTime->addDays($plan->duration_days);
                    }
                    $player->update([
                        'has_ad_free' => true,
                        'ad_free_expires_at' => $expiresAt,
                    ]);
                } else {
                    // 购买版本套餐
                    // 套餐code到版本code的映射
                    $versionMap = [
                        'basic' => 'basic', 'basic_month' => 'basic', 'basic_quarter' => 'basic',
                        'basic_year' => 'basic', 'basic_forever' => 'basic',
                        'premium' => 'advanced', 'pro_month' => 'advanced', 'pro_quarter' => 'advanced',
                        'pro_year' => 'advanced', 'pro_forever' => 'advanced',
                        'ultimate' => 'flagship', 'ultimate_month' => 'flagship', 'ultimate_quarter' => 'flagship',
                        'ultimate_year' => 'flagship', 'ultimate_forever' => 'flagship',
                    ];
                    $versionCode = $versionMap[$plan->code] ?? 'free';
                    
                    // 计算版本到期时间
                    $versionExpireAt = null;
                    if ($plan->duration_type === 4) {
                        // 永久
                        $versionExpireAt = null;
                    } elseif ($plan->duration_days > 0) {
                        // 月卡/季卡/年卡 - 在原到期时间基础上续期
                        $baseTime = ($player->version_expire_at && $player->version_expire_at->isFuture())
                            ? $player->version_expire_at
                            : now();
                        $versionExpireAt = $baseTime->addDays($plan->duration_days);
                    }
                    
                    $player->update([
                        'version' => $versionCode,
                        'version_expire_at' => $versionExpireAt,
                    ]);
                }
            }
        }
        
        return response()->json([
            'message' => '支付成功，版本已升级',
            'balance' => $balance->balance,
            'plan' => $order->plan->name,
            'player_id' => $order->player_id
        ]);
    });
    
    // 用户订单列表
    Route::get('/orders', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '用户未登录'], 401);
        
        $orders = \App\Models\Order::where('user_id', $user->id)
            ->with('plan')
            ->latest()
            ->paginate(20);
        
        return response()->json($orders);
    });
});

// ========== 苹果CMS播放器接口 ==========
// 格式1: /api/player/vod?id=xxx
Route::get('/player/vod', function (\Illuminate\Http\Request $request) {
    $id = $request->query('id');
    if (!$id) {
        return response()->json([
            'code' => 400,
            'msg' => '缺少视频ID参数',
            'data' => null
        ]);
    }
    
    // 查找视频
    $video = \App\Models\Video::find($id);
    if (!$video) {
        return response()->json([
            'code' => 404,
            'msg' => '视频不存在',
            'data' => null
        ]);
    }
    
    // 苹果CMS格式响应
    return response()->json([
        'code' => 200,
        'msg' => 'success',
        'data' => [
            'url' => $video->url,
            'type' => 'auto',
            'pic' => $video->cover_url ?? '',
            'title' => $video->title ?? '',
        ]
    ]);
});

// 格式2: /api/player/url?vid=xxx
Route::get('/player/url', function (\Illuminate\Http\Request $request) {
    $vid = $request->query('vid');
    if (!$vid) {
        return response()->json([
            'code' => 400,
            'msg' => '缺少vid参数',
            'data' => null
        ]);
    }
    
    $video = \App\Models\Video::find($vid);
    if (!$video) {
        return response()->json([
            'code' => 404,
            'msg' => '视频不存在',
            'data' => null
        ]);
    }
    
    return response()->json([
        'code' => 200,
        'msg' => 'success',
        'data' => [
            'url' => $video->url,
            'type' => 'auto',
            'pic' => $video->cover_url ?? '',
            'title' => $video->title ?? '',
        ]
    ]);
});

// 格式3: /api/player/proxy?url=xxx (代理播放，可选)
Route::get('/player/proxy', function (\Illuminate\Http\Request $request) {
    $url = $request->query('url');
    if (!$url) {
        return response()->json([
            'code' => 400,
            'msg' => '缺少url参数',
            'data' => null
        ]);
    }
    
    // 验证URL格式
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return response()->json([
            'code' => 400,
            'msg' => '无效的URL格式',
            'data' => null
        ]);
    }
    
    // 直接返回URL（不代理，让客户端直接播放）
    return response()->json([
        'code' => 200,
        'msg' => 'success',
        'data' => [
            'url' => $url,
            'type' => 'auto',
        ]
    ]);
});

// 格式4: /api/player/maccms (苹果CMS标准接口)
Route::get('/player/maccms', function (\Illuminate\Http\Request $request) {
    $id = $request->query('id');
    $url = $request->query('url');
    
    // 支持id或url参数
    if ($id) {
        $video = \App\Models\Video::find($id);
        if (!$video) {
            return response()->json([
                'code' => 404,
                'msg' => '视频不存在',
                'data' => null
            ]);
        }
        $videoUrl = $video->url;
        $pic = $video->cover_url ?? '';
        $title = $video->title ?? '';
    } elseif ($url) {
        $videoUrl = $url;
        $pic = '';
        $title = '';
    } else {
        return response()->json([
            'code' => 400,
            'msg' => '缺少id或url参数',
            'data' => null
        ]);
    }
    
    // 苹果CMS标准响应格式
    return response()->json([
        'code' => 200,
        'msg' => 'success',
        'data' => [
            'url' => $videoUrl,
            'type' => 'auto',
            'pic' => $pic,
            'title' => $title,
        ]
    ]);
});

// 苹果CMS播放器页面（可选）
Route::get('/player/play/{id}', function ($id) {
    $video = \App\Models\Video::find($id);
    if (!$video) {
        abort(404, '视频不存在');
    }
    
    // 返回播放器页面
    return view('player.maccms', [
        'video' => $video,
        'api_url' => url('/api/player/maccms?id=' . $id),
    ]);
});

// 剧集和视频管理API
require __DIR__ . '/api_series.php';
