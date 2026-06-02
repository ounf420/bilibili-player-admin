<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    private function success($data, $code = 200)
    {
        return response()->json(['success' => true, 'data' => $data], $code);
    }

    private function error($msg, $code = 400)
    {
        return response()->json(['success' => false, 'message' => $msg], $code);
    }
    // 影视列表（筛选+搜索+分页）
    public function index(Request $request)
    {
        $query = DB::table('videos')->where('enabled', 1);

        // 分类筛选
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 地区筛选
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // 年份筛选
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // 类型筛选
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        // 画质筛选
        if ($request->filled('quality')) {
            $query->where('quality', $request->quality);
        }

        // VIP等级筛选
        if ($request->filled('vip_level')) {
            $query->where('vip_level', $request->vip_level);
        }

        // 搜索
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('actors', 'LIKE', "%{$keyword}%")
                  ->orWhere('director', 'LIKE', "%{$keyword}%")
                  ->orWhere('tags', 'LIKE', "%{$keyword}%");
            });
        }

        // 排序
        $sort = $request->input('sort', 'new');
        switch ($sort) {
            case 'hot':
                $query->orderByDesc('views');
                break;
            case 'score':
                $query->orderByDesc('score');
                break;
            case 'new':
            default:
                $query->orderByDesc('sort_order')->orderByDesc('created_at');
                break;
        }

        $perPage = min((int) $request->input('per_page', 24), 100);
        $videos = $query->select('id','title','cover','category','region','year','genre','score','duration','views','likes','vip_level','quality','episode_count','is_ending')
            ->paginate($perPage);

        return $this->success($videos);
    }

    // 推荐视频（首页Hero用）
    public function recommend()
    {
        $videos = DB::table('videos')
            ->where('enabled', 1)
            ->where('is_recommend', 1)
            ->orderByDesc('sort_order')
            ->limit(5)
            ->select('id','title','cover','description','category','score','views','vip_level','quality')
            ->get();

        return $this->success($videos);
    }

    // 热门排行
    public function ranking(Request $request)
    {
        $limit = min((int) $request->input('limit', 10), 50);
        $videos = DB::table('videos')
            ->where('enabled', 1)
            ->orderByDesc('views')
            ->limit($limit)
            ->select('id','title','cover','category','views','likes','score','vip_level')
            ->get();

        return $this->success($videos);
    }

    // 视频详情
    public function show($id)
    {
        $video = DB::table('videos')->where('id', $id)->where('enabled', 1)->first();
        if (!$video) {
            return $this->error('视频不存在', 404);
        }

        DB::table('videos')->where('id', $id)->increment('views');
        $video->views += 1;

        // 相似推荐
        $related = DB::table('videos')
            ->where('enabled', 1)
            ->where('id', '!=', $id)
            ->where('category', $video->category)
            ->orderByDesc('views')
            ->limit(6)
            ->select('id','title','cover','score','views','vip_level')
            ->get();

        return $this->success(['video' => $video, 'related' => $related]);
    }

    // 筛选选项（分类/地区/年份/类型去重值）
    public function filters()
    {
        $categories = DB::table('videos')->where('enabled', 1)->where('category', '!=', '')->distinct()->pluck('category');
        $regions = DB::table('videos')->where('enabled', 1)->where('region', '!=', '')->distinct()->pluck('region');
        $years = DB::table('videos')->where('enabled', 1)->where('year', '!=', '')->distinct()->orderByDesc('year')->pluck('year');
        $genres = DB::table('videos')->where('enabled', 1)->where('genre', '!=', '')->distinct()->pluck('genre');

        return $this->success([
            'categories' => $categories,
            'regions' => $regions,
            'years' => $years,
            'genres' => $genres,
        ]);
    }
}
