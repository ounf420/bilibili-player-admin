<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WatchHistoryController extends Controller
{
    private function getUser(Request $request)
    {
        $token = $request->header('Authorization', '');
        if (str_starts_with($token, 'Bearer ')) $token = substr($token, 7);
        $record = DB::table('personal_access_tokens')->where('token', hash('sha256', $token))->first();
        if (!$record) return null;
        return DB::table('users')->where('id', $record->tokenable_id)->first();
    }

    // 观看历史列表
    public function index(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $history = DB::table('watch_history')
            ->join('videos', 'watch_history.video_id', '=', 'videos.id')
            ->where('watch_history.user_id', $user->id)
            ->where('videos.enabled', 1)
            ->orderByDesc('watch_history.updated_at')
            ->select('videos.id', 'videos.title', 'videos.cover', 'videos.category', 'videos.duration', 'videos.vip_level',
                'watch_history.progress', 'watch_history.updated_at as watched_at')
            ->paginate(24);

        return response()->json(['success' => true, 'data' => $history]);
    }

    // 更新播放进度
    public function update(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $videoId = $request->input('video_id');
        $progress = (int) $request->input('progress', 0);
        $duration = (int) $request->input('duration', 0);

        $existing = DB::table('watch_history')
            ->where('user_id', $user->id)->where('video_id', $videoId)->first();

        if ($existing) {
            DB::table('watch_history')->where('id', $existing->id)->update([
                'progress' => $progress, 'duration' => $duration, 'updated_at' => now()
            ]);
        } else {
            DB::table('watch_history')->insert([
                'user_id' => $user->id, 'video_id' => $videoId,
                'progress' => $progress, 'duration' => $duration,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }

    // 清空历史
    public function clear(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        DB::table('watch_history')->where('user_id', $user->id)->delete();
        return response()->json(['success' => true, 'message' => '已清空观看历史']);
    }

    // 获取单个视频播放进度
    public function getProgress(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => true, 'data' => ['progress' => 0]]);

        $record = DB::table('watch_history')
            ->where('user_id', $user->id)->where('video_id', $request->input('video_id'))->first();

        return response()->json(['success' => true, 'data' => ['progress' => $record->progress ?? 0]]);
    }
}
