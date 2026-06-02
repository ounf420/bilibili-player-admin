<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    private function getUser(Request $request)
    {
        $token = $request->header('Authorization', '');
        if (str_starts_with($token, 'Bearer ')) $token = substr($token, 7);
        $record = DB::table('personal_access_tokens')->where('token', hash('sha256', $token))->first();
        if (!$record) return null;
        return DB::table('users')->where('id', $record->tokenable_id)->first();
    }

    // 点赞/取消点赞
    public function toggle(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $videoId = $request->input('video_id');
        $type = (int) $request->input('type', 1); // 1=赞 -1=踩
        $existing = DB::table('video_likes')->where('user_id', $user->id)->where('video_id', $videoId)->first();

        if ($existing) {
            if ($existing->type == $type) {
                // 取消
                DB::table('video_likes')->where('id', $existing->id)->delete();
                DB::table('videos')->where('id', $videoId)->decrement('likes');
                return response()->json(['success' => true, 'data' => ['liked' => false, 'type' => 0]]);
            } else {
                // 切换
                DB::table('video_likes')->where('id', $existing->id)->update(['type' => $type, 'updated_at' => now()]);
                if ($type == 1) {
                    DB::table('videos')->where('id', $videoId)->increment('likes');
                } else {
                    DB::table('videos')->where('id', $videoId)->decrement('likes');
                }
                return response()->json(['success' => true, 'data' => ['liked' => true, 'type' => $type]]);
            }
        } else {
            DB::table('video_likes')->insert([
                'user_id' => $user->id, 'video_id' => $videoId, 'type' => $type,
                'created_at' => now(), 'updated_at' => now()
            ]);
            if ($type == 1) DB::table('videos')->where('id', $videoId)->increment('likes');
            return response()->json(['success' => true, 'data' => ['liked' => true, 'type' => $type]]);
        }
    }

    // 检查用户点赞状态
    public function check(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => true, 'data' => ['liked' => false, 'type' => 0]]);

        $record = DB::table('video_likes')->where('user_id', $user->id)->where('video_id', $request->input('video_id'))->first();
        return response()->json(['success' => true, 'data' => ['liked' => $record ? true : false, 'type' => $record->type ?? 0]]);
    }
}
