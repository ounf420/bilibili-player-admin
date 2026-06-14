<?php

namespace App\Filament\Admin\Resources\OrderResource\Tables;


use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_no')
                    ->label('订单号')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('user.name')
                    ->label('用户')
                    ->searchable(),

                TextColumn::make('plan.name')
                    ->label('套餐')
                    ->placeholder('-'),

                TextColumn::make('amount')
                    ->label('金额')
                    ->prefix('¥')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'gray',
                        'refunded' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => '待支付',
                        'paid' => '已支付',
                        'cancelled' => '已取消',
                        'refunded' => '已退款',
                        default => $state,
                    }),

                TextColumn::make('pay_method')
                    ->label('支付方式')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'balance' => '余额',
                        'card' => '卡密',
                        default => '-',
                    }),

                TextColumn::make('paid_at')
                    ->label('支付时间')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        'pending' => '待支付',
                        'paid' => '已支付',
                        'cancelled' => '已取消',
                        'refunded' => '已退款',
                    ]),

                SelectFilter::make('pay_method')
                    ->label('支付方式')
                    ->options([
                        'balance' => '余额支付',
                        'card' => '卡密支付',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                                    DeleteBulkAction::make(),
            ]);
    }
}
