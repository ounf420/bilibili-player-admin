<x-filament-panels::page>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
        @php
        $cards = [
            
            ['👥', '用户总数', $data['total_users'], '#3b82f6'],
            
            ['👁️', '总播放量', number_format($data['total_views']), '#8b5cf6'],
            ['💬', '评论数', $data['total_comments'], '#ef4444'],
            ['🔤', '弹幕数', $data['total_danmaku'], '#f97316'],
            ['📢', '活跃广告', $data['total_ads'], '#06b6d4'],
        ];
        @endphp
        @foreach($cards as $card)
        <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;text-align:center;">
            <div style="font-size:28px;margin-bottom:8px;">{{ $card[0] }}</div>
            <div style="font-size:24px;font-weight:700;color:{{ $card[3] }};">{{ $card[2] }}</div>
            <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:4px;">{{ $card[1] }}</div>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">🔥 7天热搜词</h3>
            @forelse($data['hot_searches'] as $i => $s)
            <div style="display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <span style="width:20px;text-align:center;font-size:13px;font-weight:600;color:{{ $i < 3 ? '#ef4444' : 'rgba(255,255,255,0.4)' }};">{{ $i + 1 }}</span>
                <span style="flex:1;font-size:14px;">{{ $s->keyword }}</span>
                <span style="font-size:12px;color:rgba(255,255,255,0.4);">{{ $s->cnt }}次</span>
            </div>
            @empty
            <div style="color:rgba(255,255,255,0.3);font-size:13px;">暂无搜索数据</div>
            @endforelse
        </div>

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">🏆 播放量TOP10</h3>
            @foreach([] as $i => $v)
            <div style="display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <span style="width:20px;text-align:center;font-size:13px;font-weight:600;color:{{ $i < 3 ? '#ffd700' : 'rgba(255,255,255,0.4)' }};">{{ $i + 1 }}</span>
                <span style="flex:1;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $v->title }}</span>
                <span style="font-size:12px;color:rgba(255,255,255,0.4);">{{ $v->views }}次</span>
            </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
