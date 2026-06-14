<x-filament-panels::page>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
        @php
        $cards = [
            ['💰', '平台总余额', '¥' . number_format($data['totalBalance'], 2), '#3b82f6'],
            ['📈', '累计充值', '¥' . number_format($data['totalRecharged'], 2), '#10b981'],
            ['📉', '累计消费', '¥' . number_format($data['totalSpent'], 2), '#f59e0b'],
            ['📅', '今日充值', '¥' . number_format($data['todayRecharge'], 2), '#8b5cf6'],
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

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
        <!-- 卡密统计 -->
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">🎫 卡密统计</h3>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <span style="color:rgba(255,255,255,0.6);">总卡密数</span>
                <span style="font-weight:600;">{{ $data['totalCards'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <span style="color:rgba(255,255,255,0.6);">未使用</span>
                <span style="font-weight:600;color:#10b981;">{{ $data['unusedCards'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <span style="color:rgba(255,255,255,0.6);">已使用</span>
                <span style="font-weight:600;color:rgba(255,255,255,0.4);">{{ $data['usedCards'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;">
                <span style="color:rgba(255,255,255,0.6);">未使用总面值</span>
                <span style="font-weight:700;color:#3b82f6;">¥{{ number_format($data['totalCardValue'], 2) }}</span>
            </div>
        </div>

        <!-- 订单统计 -->
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">📦 订单统计</h3>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <span style="color:rgba(255,255,255,0.6);">总订单数</span>
                <span style="font-weight:600;">{{ $data['totalOrders'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <span style="color:rgba(255,255,255,0.6);">已支付</span>
                <span style="font-weight:600;color:#10b981;">{{ $data['paidOrders'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:8px 0;">
                <span style="color:rgba(255,255,255,0.6);">总成交金额</span>
                <span style="font-weight:700;color:#3b82f6;">¥{{ number_format($data['totalOrderAmount'], 2) }}</span>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <!-- 最近交易 -->
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">💱 最近交易</h3>
            @forelse($data['recentTransactions'] as $t)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <div>
                    <div style="font-weight:500;">{{ $t->user->name ?? '未知用户' }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.4);">{{ $t->description }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:700;color:{{ $t->amount >= 0 ? '#10b981' : '#ef4444' }};">
                        {{ $t->amount >= 0 ? '+' : '' }}¥{{ number_format($t->amount, 2) }}
                    </div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.3);">{{ $t->created_at->format('m-d H:i') }}</div>
                </div>
            </div>
            @empty
            <div style="color:rgba(255,255,255,0.3);font-size:13px;text-align:center;padding:20px;">暂无交易记录</div>
            @endforelse
        </div>

        <!-- 最近订单 -->
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px;">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px;">🛒 最近订单</h3>
            @forelse($data['recentOrders'] as $o)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.05);">
                <div>
                    <div style="font-weight:500;">{{ $o->user->name ?? '未知用户' }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.4);">{{ $o->plan->name ?? '未知套餐' }}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:700;">¥{{ number_format($o->amount, 2) }}</div>
                    <div style="font-size:11px;">
                        @if($o->status === 'paid')
                            <span style="color:#10b981;">已支付</span>
                        @elseif($o->status === 'pending')
                            <span style="color:#f59e0b;">待支付</span>
                        @else
                            <span style="color:rgba(255,255,255,0.3);">{{ $o->status_name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="color:rgba(255,255,255,0.3);font-size:13px;text-align:center;padding:20px;">暂无订单</div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
