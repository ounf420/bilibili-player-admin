<?php

namespace App\Http\Controllers;

use App\Models\UserPlayer;
use App\Models\Video;
use App\Models\Decoration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserPlayerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $players = UserPlayer::where('user_id', $user->id)
            ->withCount('videos')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('user.player.index', compact('players'));
    }

    public function create()
    {
        $decorations = Decoration::where('is_active', true)->get();
        return view('user.player.create', compact('decorations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'theme_color' => 'required|string|max:20',
            'aspect_ratio' => 'required|string|max:10',
        ]);

        $user = Auth::user();
        
        $player = UserPlayer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'slug' => Str::random(16),
            'theme_color' => $request->theme_color,
            'logo_url' => $request->logo_url,
            'progress_icon_url' => $request->progress_icon_url,
            'watermark_text' => $request->watermark_text,
            'watermark_position' => $request->watermark_position ?? 'bottom-right',
            'show_title' => $request->boolean('show_title', true),
            'show_controls' => $request->boolean('show_controls', true),
            'autoplay' => $request->boolean('autoplay', false),
            'loop_play' => $request->boolean('loop_play', false),
            'muted' => $request->boolean('muted', false),
            'show_danmaku' => $request->boolean('show_danmaku', false),
            'allow_danmaku' => $request->boolean('allow_danmaku', false),
            'show_quality' => $request->boolean('show_quality', true),
            'show_speed' => $request->boolean('show_speed', true),
            'show_fullscreen' => $request->boolean('show_fullscreen', true),
            'show_pip' => $request->boolean('show_pip', true),
            'show_download' => $request->boolean('show_download', false),
            'show_share' => $request->boolean('show_share', true),
            'width' => $request->width ?? '100%',
            'height' => $request->height ?? 'auto',
            'aspect_ratio' => $request->aspect_ratio ?? '16:9',
            'border_radius' => $request->border_radius ?? '12px',
            'show_ads' => $request->boolean('show_ads', true),
            'ad_decoration_id' => $request->ad_decoration_id,
        ]);

        return redirect()->route('user.player.show', $player->id)
            ->with('success', '播放器创建成功！');
    }

    public function show($id)
    {
        $user = Auth::user();
        $player = UserPlayer::where('user_id', $user->id)
            ->where('id', $id)
            ->with('videos', 'decoration')
            ->firstOrFail();
        
        $availableVideos = Video::where('status', 1)
            ->whereNotIn('id', $player->videos->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return view('user.player.show', compact('player', 'availableVideos'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $player = UserPlayer::where('user_id', $user->id)->findOrFail($id);
        $decorations = Decoration::where('is_active', true)->get();
        
        return view('user.player.edit', compact('player', 'decorations'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $player = UserPlayer::where('user_id', $user->id)->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100',
            'theme_color' => 'required|string|max:20',
        ]);

        $player->update([
            'name' => $request->name,
            'theme_color' => $request->theme_color,
            'logo_url' => $request->logo_url,
            'background_image' => $request->background_image,
            'background_image_mobile' => $request->background_image_mobile,
            'progress_icon_url' => $request->progress_icon_url,
            'watermark_text' => $request->watermark_text,
            'watermark_position' => $request->watermark_position ?? 'bottom-right',
            'show_title' => $request->boolean('show_title', true),
            'show_controls' => $request->boolean('show_controls', true),
            'autoplay' => $request->boolean('autoplay', false),
            'loop_play' => $request->boolean('loop_play', false),
            'muted' => $request->boolean('muted', false),
            'show_danmaku' => $request->boolean('show_danmaku', false),
            'allow_danmaku' => $request->boolean('allow_danmaku', false),
            'show_quality' => $request->boolean('show_quality', true),
            'show_speed' => $request->boolean('show_speed', true),
            'show_fullscreen' => $request->boolean('show_fullscreen', true),
            'show_pip' => $request->boolean('show_pip', true),
            'show_download' => $request->boolean('show_download', false),
            'show_share' => $request->boolean('show_share', true),
            'width' => $request->width ?? '100%',
            'height' => $request->height ?? 'auto',
            'aspect_ratio' => $request->aspect_ratio ?? '16:9',
            'border_radius' => $request->border_radius ?? '12px',
            'show_ads' => $request->boolean('show_ads', true),
            'ad_decoration_id' => $request->ad_decoration_id,
        ]);

        return redirect()->route('user.player.show', $player->id)
            ->with('success', '播放器更新成功！');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $player = UserPlayer::where('user_id', $user->id)->findOrFail($id);
        $player->delete();

        return redirect()->route('user.player.index')
            ->with('success', '播放器已删除！');
    }

    public function addVideo(Request $request, $id)
    {
        $user = Auth::user();
        $player = UserPlayer::where('user_id', $user->id)->findOrFail($id);
        
        $request->validate([
            'video_id' => 'required|exists:videos,id',
        ]);

        $maxOrder = $player->videos()->max('pivot_sort_order') ?? 0;
        
        $player->videos()->attach($request->video_id, [
            'sort_order' => $maxOrder + 1,
        ]);
        
        $player->increment('video_count');

        return back()->with('success', '视频已添加！');
    }

    public function removeVideo(Request $request, $id)
    {
        $user = Auth::user();
        $player = UserPlayer::where('user_id', $user->id)->findOrFail($id);
        
        $request->validate([
            'video_id' => 'required|exists:videos,id',
        ]);

        $player->videos()->detach($request->video_id);
        $player->decrement('video_count');

        return back()->with('success', '视频已移除！');
    }

    public function embed($slug, Request $request)
    {
        // 同时支持 slug 和 player_code 访问
        $player = UserPlayer::where(function($q) use ($slug) {
                $q->where('slug', $slug)->orWhere('player_code', $slug);
            })
            ->where('is_active', true)
            ->with(['videos', 'engine', 'plan'])
            ->firstOrFail();
        
        // 验证播放器密钥（支持 pid/pkey 或 id/key 参数）
        $pid = $request->query('pid') ?: $request->query('id');
        $pkey = $request->query('pkey') ?: $request->query('key');
        
        if ($pid && $pkey) {
            // 有密钥验证，不需要登录
            if ($player->player_code != $pid || !$player->verifyKey($pkey)) {
                abort(403, '播放器验证失败');
            }
        } else {
            // 没有密钥，必须登录
            if (!Auth::check()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => '请先登录'], 401);
                }
                return redirect()->guest(url('/login'));
            }
        }
        
        $player->incrementViewCount();
        
        // 版本过期降级处理
        if (!$player->isVersionActive()) {
            $player->version = 'free';
        }
        
        // 获取该播放器的广告（支持多播放器投放）
        $materialAds = \App\Models\UserPlayerAd::enabled()
            ->forPlayer($player->id)
            ->orderBy('sort_order')
            ->get();

        // 根据模板选择视图
        $template = $player->template ?: 'standard';
        $view = $template === 'youku' ? 'player.embed-youku' : 'player.embed';

        return view($view, compact('player', 'materialAds'));
    }

    /**
     * 优酷风格播放器
     */
    public function embedYouku($slug, Request $request)
    {
        $player = UserPlayer::where(function($q) use ($slug) {
                $q->where('slug', $slug)->orWhere('player_code', $slug);
            })
            ->where('is_active', true)
            ->with(['videos', 'engine', 'plan'])
            ->firstOrFail();
        
        $pid = $request->query('pid') ?: $request->query('id');
        $pkey = $request->query('pkey') ?: $request->query('key');
        
        if ($pid && $pkey) {
            if ($player->player_code != $pid || !$player->verifyKey($pkey)) {
                abort(403, '播放器验证失败');
            }
        } else {
            if (!Auth::check()) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => '请先登录'], 401);
                }
                return redirect()->guest(url('/login'));
            }
        }
        
        $player->incrementViewCount();
        
        if (!$player->isVersionActive()) {
            $player->version = 'free';
        }
        
        $materialAds = \App\Models\UserPlayerAd::enabled()
            ->forPlayer($player->id)
            ->orderBy('sort_order')
            ->get();
        
        return view('player.embed-youku', compact('player', 'materialAds'));
    }
}
