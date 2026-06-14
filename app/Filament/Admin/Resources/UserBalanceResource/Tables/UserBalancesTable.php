<?php

namespace App\Filament\Admin\Resources\UserBalanceResource\Tables;

use App\Models\UserBalance;
use Filament\Actions\Action;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class UserBalancesTable
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

                TextColumn::make('user.username')
                    ->label('用户名')
                    ->searchable(),

                TextColumn::make('balance')
                    ->label('当前余额')
                    ->prefix('¥')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('total_recharged')
                    ->label('累计充值')
                    ->prefix('¥')
                    ->sortable(),

                TextColumn::make('total_spent')
                    ->label('累计消费')
                    ->prefix('¥')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('最后更新')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('balance', 'desc')
            ->filters([
                Filter::make('has_balance')
                    ->label('有余额')
                    ->query(fn ($query) => $query->where('balance', '>', 0)),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('add_balance')
                    ->label('加余额')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->label('充值金额')
                            ->numeric()
                            ->required()
                            ->minValue(0.01),

                        TextInput::make('remark')
                            ->label('备注')
                            ->default('管理员充值'),
                    ])
                    ->action(function (UserBalance $record, array $data): void {
                        $record->recharge($data['amount']);
                        
                        \App\Models\Transaction::create([
                            'user_id' => $record->user_id,
                            'type' => 'recharge',
                            'amount' => $data['amount'],
                            'balance_after' => $record->balance,
                            'description' => $data['remark'] ?? '管理员充值',
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('充值成功')
                            ->body("已为用户充值 ¥{$data['amount']}")
                            ->send();
                    }),
            ])
            ->toolbarActions([
                                    DeleteBulkAction::make(),
            ]);
    }
}
