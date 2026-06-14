<?php

namespace App\Filament\Admin\Resources\OrderResource;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('订单信息')
                    ->schema([
                        TextInput::make('order_no')
                            ->label('订单号')
                            ->disabled(),

                        Select::make('status')
                            ->label('状态')
                            ->options([
                                'pending' => '待支付',
                                'paid' => '已支付',
                                'cancelled' => '已取消',
                                'refunded' => '已退款',
                            ])
                            ->required(),

                        Select::make('pay_method')
                            ->label('支付方式')
                            ->options([
                                'balance' => '余额支付',
                                'card' => '卡密支付',
                            ])
                            ->disabled(),

                        TextInput::make('amount')
                            ->label('金额')
                            ->prefix('¥')
                            ->disabled(),
                    ])->columns(2),

                Section::make('关联信息')
                    ->schema([
                        TextInput::make('user.name')
                            ->label('用户')
                            ->disabled(),

                        TextInput::make('plan.name')
                            ->label('套餐')
                            ->disabled(),

                        TextInput::make('paid_at')
                            ->label('支付时间')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }
}
