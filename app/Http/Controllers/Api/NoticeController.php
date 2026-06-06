<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * 获取有效公告列表
     * GET /api/notices?position=home
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $type = $request->input('type');
        $position = $request->input('position');
        $limit = min($request->input('limit', 20), 50);

        $query = Notice::active()->orderByTop();

        // 类型筛选
        if ($type) {
            $query->where('type', $type);
        }

        // 位置筛选
        if ($position) {
            $query->where(function ($q) use ($position) {
                $q->where('position', 'all')
                    ->orWhere('position', $position);
            });
        }

        // 目标用户筛选
        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('target_users', 'all');
            });
        } else {
            $query->where('target_users', 'all');
        }

        $notices = $query->limit($limit)->get();

        // 标记已读状态
        if ($user) {
            $readIds = $user->readNotices()->pluck('notice_id')->toArray();
            $notices->each(function ($notice) use ($readIds) {
                $notice->is_read = in_array($notice->id, $readIds);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $notices,
        ]);
    }

    /**
     * 获取弹窗公告
     * GET /api/notices/popup
     */
    public function popup(Request $request)
    {
        $user = $request->user();

        $query = Notice::active()
            ->where('is_popup', true)
            ->orderByTop();

        if ($user) {
            // 排除已读
            $readIds = $user->readNotices()->pluck('notice_id')->toArray();
            $query->whereNotIn('id', $readIds);
        }

        $notice = $query->first();

        if (!$notice) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $notice,
        ]);
    }

    /**
     * 获取滚动公告
     * GET /api/notices/marquee
     */
    public function marquee()
    {
        $notices = Notice::active()
            ->where('is_marquee', true)
            ->orderByTop()
            ->limit(5)
            ->get(['id', 'title', 'type', 'bg_color', 'icon']);

        return response()->json([
            'success' => true,
            'data' => $notices,
        ]);
    }

    /**
     * 标记公告已读
     * POST /api/notices/{id}/read
     */
    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $notice = Notice::find($id);
        if (!$notice) {
            return response()->json(['success' => false, 'message' => '公告不存在'], 404);
        }

        // 标记已读
        if (!$user->readNotices()->where('notice_id', $id)->exists()) {
            $user->readNotices()->attach($id);
            $notice->increment('read_count');
        }

        return response()->json(['success' => true]);
    }

    /**
     * 获取公告详情
     * GET /api/notices/{id}
     */
    public function show(Request $request, $id)
    {
        $notice = Notice::active()->find($id);

        if (!$notice) {
            return response()->json(['success' => false, 'message' => '公告不存在'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $notice,
        ]);
    }
}
