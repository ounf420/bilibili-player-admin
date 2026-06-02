<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    private function getUser(Request $request)
    {
        $token = $request->header('Authorization', '');
        if (str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        $record = DB::table('personal_access_tokens')->where('token', hash('sha256', $token))->first();
        if (!$record) return null;
        return DB::table('users')->where('id', $record->tokenable_id)->first();
    }

    // 收藏列表
    public function index(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $favorites = DB::table('favorites')
            ->join('videos', 'favorites.video_id', '=', 'videos.id')
            ->where('favorites.user_id', $user->id)
            ->where('videos.enabled', 1)
            ->orderByDesc('favorites.created_at')
            ->select('videos.id', 'videos.title', 'videos.cover', 'videos.category', 'videos.score', 'videos.views', 'videos.vip_level', 'favorites.created_at as favorited_at')
            ->paginate(24);

        return response()->json(['success' => true, 'data' => $favorites]);
    }

    // 收藏/取消收藏
    public function toggle(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => false, 'message' => '请先登录'], 401);

        $videoId = $request->input('video_id');
        $exists = DB::table('favorites')->where('user_id', $user->id)->where('video_id', $videoId)->first();

        if ($exists) {
            DB::table('favorites')->where('user_id', $user->id)->where('video_id', $videoId)->delete();
            return response()->json(['success' => true, 'data' => ['favorited' => false], 'message' => '已取消收藏']);
        } else {
            DB::table('favorites')->insert([
                'user_id' => $user->id,
                'video_id' => $videoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['success' => true, 'data' => ['favorited' => true], 'message' => '已收藏']);
        }
    }

    // 检查是否已收藏
    public function check(Request $request)
    {
        $user = $this->getUser($request);
        if (!$user) return response()->json(['success' => true, 'data' => ['favorited' => false]]);

        $exists = DB::table('favorites')->where('user_id', $user->id)->where('video_id', $request->input('video_id'))->exists();
        return response()->json(['success' => true, 'data' => ['favorited' => $exists]]);
    }
}
