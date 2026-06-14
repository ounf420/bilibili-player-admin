<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VideoParserController extends Controller
{
    /**
     * 解析视频链接，获取真实播放地址
     */
    public function parse(Request $request)
    {
        $url = $request->input('url');
        
        if (empty($url)) {
            return response()->json([
                'success' => false,
                'message' => '请输入视频链接'
            ], 400);
        }

        // 验证URL格式
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => '请输入有效的视频链接'
            ], 400);
        }

        // 缓存key，避免重复解析
        $cacheKey = 'video_parse_' . md5($url);
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return response()->json([
                'success' => true,
                'data' => $cached
            ]);
        }

        try {
            // 构建yt-dlp命令
            $command = 'yt-dlp --no-check-certificates';
            
            // 添加User-Agent
            $command .= ' --user-agent "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"';
            
            // 添加referer
            $command .= ' --referer "https://www.google.com"';
            
            // 获取视频信息
            $command .= sprintf(' -j %s 2>&1', escapeshellarg($url));
            
            $output = shell_exec($command);
            
            if (empty($output)) {
                return response()->json([
                    'success' => false,
                    'message' => '解析失败，请检查链接是否正确'
                ], 500);
            }

            // 检查是否是错误信息
            if (strpos($output, 'ERROR') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => '解析失败：' . $output
                ], 500);
            }

            $videoInfo = json_decode($output, true);
            
            if (!$videoInfo) {
                return response()->json([
                    'success' => false,
                    'message' => '解析失败，无法获取视频信息'
                ], 500);
            }

            // 提取关键信息
            $result = [
                'title' => $videoInfo['title'] ?? '未知标题',
                'thumbnail' => $videoInfo['thumbnail'] ?? '',
                'duration' => $videoInfo['duration'] ?? 0,
                'uploader' => $videoInfo['uploader'] ?? '未知',
                'url' => $videoInfo['url'] ?? '',
                'formats' => []
            ];

            // 提取可用的视频格式
            if (isset($videoInfo['formats']) && is_array($videoInfo['formats'])) {
                foreach ($videoInfo['formats'] as $format) {
                    if (isset($format['url']) && !empty($format['url'])) {
                        $result['formats'][] = [
                            'url' => $format['url'],
                            'quality' => $format['format_note'] ?? '未知',
                            'ext' => $format['ext'] ?? 'mp4',
                            'filesize' => $format['filesize'] ?? 0
                        ];
                    }
                }
            }

            // 如果没有formats，直接用url
            if (empty($result['formats']) && !empty($result['url'])) {
                $result['formats'][] = [
                    'url' => $result['url'],
                    'quality' => '默认',
                    'ext' => 'mp4',
                    'filesize' => 0
                ];
            }

            // 缓存结果（1小时）
            Cache::put($cacheKey, $result, 3600);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '解析失败：' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 获取支持的平台列表
     */
    public function platforms()
    {
        $platforms = [
            ['name' => '优酷', 'domain' => 'youku.com', 'icon' => 'fa-video'],
            ['name' => '爱奇艺', 'domain' => 'iqiyi.com', 'icon' => 'fa-play-circle'],
            ['name' => 'B站', 'domain' => 'bilibili.com', 'icon' => 'fa-tv'],
            ['name' => '腾讯视频', 'domain' => 'v.qq.com', 'icon' => 'fa-film'],
            ['name' => '芒果TV', 'domain' => 'mgtv.com', 'icon' => 'fa-tv'],
            ['name' => '抖音', 'domain' => 'douyin.com', 'icon' => 'fa-music'],
            ['name' => '西瓜视频', 'domain' => 'ixigua.com', 'icon' => 'fa-play'],
        ];

        return response()->json([
            'success' => true,
            'data' => $platforms
        ]);
    }
}
