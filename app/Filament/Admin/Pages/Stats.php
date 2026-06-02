<?php
namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class Stats extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static ?string $navigationLabel = '数据统计';
    protected static ?string $title = '数据统计';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.admin.pages.stats';

    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'total_videos' => DB::table('videos')->where('enabled', 1)->count(),
            'total_users' => DB::table('users')->where('is_admin', 0)->count(),
            'total_vip' => DB::table('users')->where('vip_level', '>', 0)->where('vip_expire_at', '>', now())->count(),
            'total_views' => DB::table('videos')->sum('views'),
            'total_comments' => DB::table('comments')->count(),
            'total_danmaku' => DB::table('danmaku')->count(),
            'total_ads' => DB::table('ads')->where('enabled', 1)->count(),
            'hot_searches' => DB::table('search_logs')
                ->where('created_at', '>=', now()->subDays(7))
                ->select('keyword', DB::raw('COUNT(*) as cnt'))
                ->groupBy('keyword')
                ->orderByDesc('cnt')
                ->limit(10)
                ->get(),
            'top_videos' => DB::table('videos')->where('enabled', 1)
                ->select('id', 'title', 'views', 'likes', 'category')
                ->orderByDesc('views')->limit(10)->get(),
            'recent_users' => DB::table('users')->where('is_admin', 0)
                ->select('id', 'nickname', 'username', 'vip_level', 'created_at')
                ->orderByDesc('id')->limit(10)->get(),
        ];
    }
}
