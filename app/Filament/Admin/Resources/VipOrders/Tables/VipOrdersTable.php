<?php

namespace App\Filament\Admin\Resources\VipOrders\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VipOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_no')->label('订单号')->searchable()->copyable(),
                TextColumn::make('user.username')->label('用户')->searchable(),
                TextColumn::make('plan.name')->label('套餐'),
                TextColumn::make('amount')->label('金额')->prefix('¥')->sortable(),
                TextColumn::make('payment_method')->label('支付方式'),
                TextColumn::make('status')->label('状态')->badge()
                    ->formatStateUsing(fn (int $state): string => match($state) {
                        0 => '待支付', 1 => '已支付', 2 => '已取消', 3 => '已退款',
                        default => '未知'
                    })
                    ->color(fn (int $state): string => match($state) {
                        1 => 'success', 0 => 'warning', 2 => 'gray', 3 => 'danger',
                        default => 'gray'
                    }),
                TextColumn::make('paid_at')->label('支付时间')->dateTime(),
                TextColumn::make('expire_at')->label('到期时间')->dateTime(),
                TextColumn::make('created_at')->label('创建时间')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('状态')->options([
                    0 => '待支付', 1 => '已支付', 2 => '已取消', 3 => '已退款',
                ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
