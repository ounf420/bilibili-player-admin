<?php

namespace App\Filament\Admin\Resources\TransactionResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('用户')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'recharge' => 'success',
                        'purchase' => 'warning',
                        'refund' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'recharge' => '充值',
                        'purchase' => '消费',
                        'refund' => '退款',
                        default => $state,
                    }),

                TextColumn::make('amount')
                    ->label('金额')
                    ->prefix('¥')
                    ->sortable()
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger'),

                TextColumn::make('balance_after')
                    ->label('余额')
                    ->prefix('¥'),

                TextColumn::make('description')
                    ->label('描述')
                    ->limit(30),

                TextColumn::make('created_at')
                    ->label('时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'recharge' => '充值',
                        'purchase' => '消费',
                        'refund' => '退款',
                    ]),
            ]);
    }
}
