<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VideoDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $videos = [
            [
                'id' => 'v002', 'title' => '流浪地球2', 'cover' => 'https://picsum.photos/seed/v002/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 173,
                'description' => '太阳即将毁灭，人类在地球表面建造出巨大的推进器，寻找新家园。', 'category' => '电影', 'tags' => '科幻,冒险',
                'region' => '中国大陆', 'year' => '2023', 'genre' => '科幻', 'director' => '郭帆',
                'actors' => '吴京,刘德华,李雪健', 'language' => '国语', 'score' => 8.3, 'episode_count' => 1,
                'is_ending' => 1, 'quality' => 'FHD', 'vip_level' => 0, 'is_recommend' => 1, 'sort_order' => 100,
                'views' => 15800, 'likes' => 4200, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v003', 'title' => '三体 第一季', 'cover' => 'https://picsum.photos/seed/v003/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 55,
                'description' => '纳米材料科学家汪淼被警官史强带到联合作战中心，开始接近三体文明的秘密。', 'category' => '电视剧', 'tags' => '科幻,悬疑',
                'region' => '中国大陆', 'year' => '2023', 'genre' => '科幻', 'director' => '杨磊',
                'actors' => '张鲁一,于和伟,陈瑾', 'language' => '国语', 'score' => 7.8, 'episode_count' => 30,
                'is_ending' => 1, 'quality' => 'FHD', 'vip_level' => 1, 'is_recommend' => 1, 'sort_order' => 90,
                'views' => 28500, 'likes' => 6800, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v004', 'title' => '铃芽之旅', 'cover' => 'https://picsum.photos/seed/v004/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 122,
                'description' => '生活在九州乡间的17岁少女铃芽，遇到了一位四处寻找门的青年。', 'category' => '动漫', 'tags' => '动画,奇幻',
                'region' => '日本', 'year' => '2023', 'genre' => '动画', 'director' => '新海诚',
                'actors' => '原菜乃华,松村北斗', 'language' => '日语', 'score' => 7.4, 'episode_count' => 1,
                'is_ending' => 1, 'quality' => 'FHD', 'vip_level' => 0, 'is_recommend' => 0, 'sort_order' => 80,
                'views' => 12600, 'likes' => 3500, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v005', 'title' => '庆余年 第二季', 'cover' => 'https://picsum.photos/seed/v005/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 45,
                'description' => '范闲在使团回归途中遭遇二皇子的威胁，庆国朝堂暗流涌动。', 'category' => '电视剧', 'tags' => '古装,权谋',
                'region' => '中国大陆', 'year' => '2024', 'genre' => '古装', 'director' => '孙皓',
                'actors' => '张若昀,李沁,陈道明', 'language' => '国语', 'score' => 7.9, 'episode_count' => 36,
                'is_ending' => 1, 'quality' => '4K', 'vip_level' => 1, 'is_recommend' => 1, 'sort_order' => 95,
                'views' => 52000, 'likes' => 12800, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v006', 'title' => '熊出没·逆转时空', 'cover' => 'https://picsum.photos/seed/v006/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 108,
                'description' => '光头强是一名普通程序员，却常梦到陌生的森林和两头狗熊。', 'category' => '动漫', 'tags' => '动画,喜剧',
                'region' => '中国大陆', 'year' => '2024', 'genre' => '动画', 'director' => '丁亮',
                'actors' => '张秉君,谭笑', 'language' => '国语', 'score' => 7.2, 'episode_count' => 1,
                'is_ending' => 1, 'quality' => 'FHD', 'vip_level' => 0, 'is_recommend' => 0, 'sort_order' => 70,
                'views' => 8900, 'likes' => 2100, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v007', 'title' => '极限挑战 第十季', 'cover' => 'https://picsum.photos/seed/v007/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 90,
                'description' => '极限男团再度集结，全新挑战带来欢乐与感动。', 'category' => '综艺', 'tags' => '真人秀,搞笑',
                'region' => '中国大陆', 'year' => '2024', 'genre' => '真人秀', 'director' => '严敏',
                'actors' => '黄渤,孙红雷,黄磊', 'language' => '国语', 'score' => 6.8, 'episode_count' => 12,
                'is_ending' => 0, 'quality' => 'FHD', 'vip_level' => 0, 'is_recommend' => 0, 'sort_order' => 60,
                'views' => 6700, 'likes' => 1800, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v008', 'title' => '舌尖上的中国 第四季', 'cover' => 'https://picsum.photos/seed/v008/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 50,
                'description' => '探索中国各地美食文化的纪录片，展示食材与人情。', 'category' => '纪录片', 'tags' => '美食,文化',
                'region' => '中国大陆', 'year' => '2024', 'genre' => '纪录片', 'director' => '陈晓卿',
                'actors' => '李立宏', 'language' => '国语', 'score' => 8.6, 'episode_count' => 7,
                'is_ending' => 1, 'quality' => '4K', 'vip_level' => 1, 'is_recommend' => 1, 'sort_order' => 85,
                'views' => 18200, 'likes' => 5600, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v009', 'title' => '鬼灭之刃 柱训练篇', 'cover' => 'https://picsum.photos/seed/v009/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 24,
                'description' => '为了迎战最终决战，炭治郎在柱们的指导下进行特训。', 'category' => '动漫', 'tags' => '热血,战斗',
                'region' => '日本', 'year' => '2024', 'genre' => '热血', 'director' => '外崎春雄',
                'actors' => '花江夏树,鬼头明里', 'language' => '日语', 'score' => 8.1, 'episode_count' => 8,
                'is_ending' => 1, 'quality' => 'FHD', 'vip_level' => 2, 'is_recommend' => 1, 'sort_order' => 88,
                'views' => 34000, 'likes' => 9200, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'v010', 'title' => '奥本海默', 'cover' => 'https://picsum.photos/seed/v010/400/225',
                'url' => 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8', 'type' => 'mp4', 'duration' => 180,
                'description' => '讲述了美国原子弹之父罗伯特·奥本海默的传奇人生。', 'category' => '电影', 'tags' => '传记,历史',
                'region' => '美国', 'year' => '2023', 'genre' => '传记', 'director' => '克里斯托弗·诺兰',
                'actors' => '基里安·墨菲,小罗伯特·唐尼', 'language' => '英语', 'score' => 8.9, 'episode_count' => 1,
                'is_ending' => 1, 'quality' => '4K', 'vip_level' => 2, 'is_recommend' => 1, 'sort_order' => 92,
                'views' => 22000, 'likes' => 7500, 'enabled' => 1, 'created_at' => $now, 'updated_at' => $now,
            ],
        ];

        DB::table('videos')->insert($videos);
        echo 'Seeded ' . count($videos) . ' demo videos. Total: ' . DB::table('videos')->where('enabled', 1)->count();
    }
}
