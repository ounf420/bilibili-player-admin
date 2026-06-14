<?php

// ========== 用户端：剧集管理 ==========
Route::middleware('auth.token')->group(function () {
    // 批量导入剧集（快速导入）
    Route::post('/user/series/import', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '未登录'], 401);
        
        $title = $request->title;
        $lines = $request->lines; // [['name' => '第01集', 'url' => 'https://...'], ...]
        
        if (!$title || empty($lines)) {
            return response()->json(['message' => '请填写剧名和视频列表'], 400);
        }
        
        // 创建剧集
        $series = \App\Models\Series::create([
            'user_id' => $user->id,
            'title' => $title,
            'episode_count' => count($lines),
            'is_ending' => false,
        ]);
        
        // 批量创建视频
        $count = 0;
        foreach ($lines as $i => $line) {
            $epNum = $i + 1;
            $videoTitle = $line['name'] ?: ('第' . str_pad($epNum, 2, '0', STR_PAD_LEFT) . '集');
            $videoUrl = $line['url'];
            if (empty($videoUrl)) continue;
            
            \App\Models\Video::create([
                'series_id' => $series->id,
                'episode_number' => $epNum,
                'title' => $videoTitle,
                'url' => $videoUrl,
                'enabled' => true,
            ]);
            $count++;
        }
        
        $series->update(['episode_count' => $count]);
        
        return response()->json([
            'message' => "导入成功！剧集「{$title}」共 {$count} 集",
            'data' => $series,
        ]);
    });

    // 剧集列表
    Route::get('/user/series', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '未登录'], 401);
        $series = \App\Models\Series::where('user_id', $user->id)
            ->withCount('videos')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
        return response()->json(['data' => $series]);
    });

    // 创建剧集
    Route::post('/user/series', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '未登录'], 401);
        $series = \App\Models\Series::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'cover' => $request->cover,
            'description' => $request->description,
        ]);
        return response()->json(['data' => $series, 'message' => '创建成功']);
    });

    // 更新剧集
    Route::put('/user/series/{id}', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        $series = \App\Models\Series::where('user_id', $user->id)->findOrFail($id);
        $series->update($request->only(['title', 'cover', 'description', 'is_ending', 'sort_order']));
        return response()->json(['data' => $series, 'message' => '更新成功']);
    });

    // 删除剧集
    Route::delete('/user/series/{id}', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        $series = \App\Models\Series::where('user_id', $user->id)->findOrFail($id);
        // 解除视频关联（不删视频）
        \App\Models\Video::where('series_id', $id)->update(['series_id' => null]);
        $series->delete();
        return response()->json(['message' => '删除成功']);
    });

    // 获取剧集详情（含视频列表）
    Route::get('/user/series/{id}', function ($id, \Illuminate\Http\Request $request) {
        $user = $request->user();
        $series = \App\Models\Series::where('user_id', $user->id)
            ->with('videos')
            ->findOrFail($id);
        return response()->json(['data' => $series]);
    });

    // ========== 用户端：视频管理 ==========
    // 视频列表（支持按剧集筛选）
    Route::get('/user/videos', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '未登录'], 401);
        $query = \App\Models\Video::query();
        if ($request->series_id) {
            $query->where('series_id', $request->series_id);
        }
        $videos = $query->orderBy('series_id')
            ->orderBy('episode_number')
            ->orderByDesc('id')
            ->paginate(20);
        return response()->json($videos);
    });

    // 创建视频
    Route::post('/user/videos', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['message' => '未登录'], 401);
        $video = \App\Models\Video::create([
            'series_id' => $request->series_id,
            'episode_number' => $request->episode_number ?? 1,
            'title' => $request->title,
            'url' => $request->url,
            'cover' => $request->cover,
            'description' => $request->description,
            'duration' => $request->duration,
            'enabled' => true,
        ]);
        // 更新剧集集数
        if ($video->series_id) {
            $series = \App\Models\Series::find($video->series_id);
            if ($series) {
                $series->update(['episode_count' => $series->videos()->count()]);
            }
        }
        return response()->json(['data' => $video, 'message' => '创建成功']);
    });

    // 更新视频
    Route::put('/user/videos/{id}', function ($id, \Illuminate\Http\Request $request) {
        $video = \App\Models\Video::findOrFail($id);
        $video->update($request->only([
            'title', 'url', 'cover', 'description', 'duration',
            'series_id', 'episode_number', 'enabled',
        ]));
        // 更新剧集集数
        if ($video->series_id) {
            $series = \App\Models\Series::find($video->series_id);
            if ($series) {
                $series->update(['episode_count' => $series->videos()->count()]);
            }
        }
        return response()->json(['data' => $video, 'message' => '更新成功']);
    });

    // 删除视频
    Route::delete('/user/videos/{id}', function ($id, \Illuminate\Http\Request $request) {
        $video = \App\Models\Video::findOrFail($id);
        $seriesId = $video->series_id;
        $video->delete();
        // 更新剧集集数
        if ($seriesId) {
            $series = \App\Models\Series::find($seriesId);
            if ($series) {
                $series->update(['episode_count' => $series->videos()->count()]);
            }
        }
        return response()->json(['message' => '删除成功']);
    });

    // 绑定视频到播放器
    Route::post('/user/videos/{id}/bind-player', function ($id, \Illuminate\Http\Request $request) {
        $video = \App\Models\Video::findOrFail($id);
        $playerId = $request->player_id;
        if (!$playerId) return response()->json(['message' => '缺少player_id'], 400);
        $exists = \App\Models\UserPlayerVideo::where('player_id', $playerId)
            ->where('video_id', $video->id)->exists();
        if (!$exists) {
            \App\Models\UserPlayerVideo::create([
                'player_id' => $playerId,
                'video_id' => $video->id,
                'sort_order' => $video->episode_number ?? 0,
            ]);
            // 更新播放器视频数
            $player = \App\Models\UserPlayer::find($playerId);
            if ($player) $player->update(['video_count' => $player->videos()->count()]);
        }
        return response()->json(['message' => '绑定成功']);
    });

    // 解绑视频与播放器
    Route::delete('/user/videos/{id}/unbind-player', function ($id, \Illuminate\Http\Request $request) {
        $playerId = $request->player_id;
        \App\Models\UserPlayerVideo::where('player_id', $playerId)
            ->where('video_id', $id)->delete();
        $player = \App\Models\UserPlayer::find($playerId);
        if ($player) $player->update(['video_count' => $player->videos()->count()]);
        return response()->json(['message' => '解绑成功']);
    });

    // 批量绑定剧集到播放器
    Route::post('/user/series/{id}/bind-player', function ($id, \Illuminate\Http\Request $request) {
        $series = \App\Models\Series::where('user_id', $request->user()->id)->findOrFail($id);
        $playerId = $request->player_id;
        if (!$playerId) return response()->json(['message' => '缺少player_id'], 400);
        $videos = $series->videos()->get();
        foreach ($videos as $video) {
            $exists = \App\Models\UserPlayerVideo::where('player_id', $playerId)
                ->where('video_id', $video->id)->exists();
            if (!$exists) {
                \App\Models\UserPlayerVideo::create([
                    'player_id' => $playerId,
                    'video_id' => $video->id,
                    'sort_order' => $video->episode_number ?? 0,
                ]);
            }
        }
        $player = \App\Models\UserPlayer::find($playerId);
        if ($player) $player->update(['video_count' => $player->videos()->count()]);
        return response()->json(['message' => '绑定成功，共绑定 ' . $videos->count() . ' 个视频']);
    });
});

// ========== 公开API：播放器获取剧集列表 ==========
Route::get('/player/{playerId}/series', function ($playerId) {
    $player = \App\Models\UserPlayer::where('player_code', $playerId)
        ->orWhere('id', $playerId)
        ->first();
    if (!$player) return response()->json(['data' => []]);
    
    // 获取该播放器绑定的视频所属的剧集
    $seriesIds = \App\Models\UserPlayerVideo::where('player_id', $player->id)
        ->join('videos', 'user_player_videos.video_id', '=', 'videos.id')
        ->whereNotNull('videos.series_id')
        ->pluck('videos.series_id')
        ->unique()
        ->toArray();
    
    $series = \App\Models\Series::whereIn('id', $seriesIds)
        ->withCount('videos')
        ->get();
    
    return response()->json(['data' => $series]);
});

// ========== 公开API：播放器获取视频列表 ==========
Route::get('/player/{playerId}/videos', function ($playerId, \Illuminate\Http\Request $request) {
    $player = \App\Models\UserPlayer::where('player_code', $playerId)
        ->orWhere('id', $playerId)
        ->first();
    if (!$player) return response()->json(['data' => []]);
    
    $query = \App\Models\Video::whereHas('playerVideos', function ($q) use ($player) {
        $q->where('player_id', $player->id);
    });
    
    if ($request->series_id) {
        $query->where('series_id', $request->series_id);
    }
    
    $videos = $query->orderBy('series_id')
        ->orderBy('episode_number')
        ->get(['id', 'series_id', 'episode_number', 'title', 'url', 'cover', 'duration']);
    
    return response()->json(['data' => $videos]);
});
