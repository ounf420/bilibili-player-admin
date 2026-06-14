<?php

namespace App\Filament\Admin\Pages;

use App\Models\Card;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\UserBalance;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class FinancialStats extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static ?string $navigationLabel = '财务总览';
    protected static ?string $title = '财务总览';
    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.admin.pages.financial-stats';

    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            // 总余额
            'totalBalance' => UserBalance::sum('balance'),
            'totalRecharged' => UserBalance::sum('total_recharged'),
            'totalSpent' => UserBalance::sum('total_spent'),

            // 今日数据
            'todayRecharge' => Transaction::where('type', 'recharge')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'todayPurchase' => Transaction::where('type', 'purchase')
                ->whereDate('created_at', today())
                ->sum('amount'),

            // 卡密统计
            'totalCards' => Card::count(),
            'unusedCards' => Card::where('status', 'unused')->count(),
            'usedCards' => Card::where('status', 'used')->count(),
            'totalCardValue' => Card::where('status', 'unused')->sum('amount'),

            // 订单统计
            'totalOrders' => Order::count(),
            'paidOrders' => Order::where('status', 'paid')->count(),
            'totalOrderAmount' => Order::where('status', 'paid')->sum('amount'),

            // 最近交易
            'recentTransactions' => Transaction::with('user')
                ->latest()
                ->limit(10)
                ->get(),

            // 最近订单
            'recentOrders' => Order::with(['user', 'plan'])
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }
}
