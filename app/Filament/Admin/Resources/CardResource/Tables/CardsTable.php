<?php

namespace App\Filament\Admin\Resources\CardResource\Tables;

use App\Models\Card;
use Filament\Actions\Action;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('card_no')
                    ->label('卡号')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('card_secret')
                    ->label('卡密')
                    ->fontFamily('mono'),

                TextColumn::make('amount')
                    ->label('面值')
                    ->prefix('¥')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'unused' => 'success',
                        'used' => 'gray',
                        'disabled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'unused' => '未使用',
                        'used' => '已使用',
                        'disabled' => '已禁用',
                        default => $state,
                    }),

                TextColumn::make('user.name')
                    ->label('使用者')
                    ->placeholder('-'),

                TextColumn::make('used_at')
                    ->label('使用时间')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),

                TextColumn::make('batch_id')
                    ->label('批次')
                    ->placeholder('-'),

                TextColumn::make('remark')
                    ->label('备注')
                    ->limit(20)
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
                        'unused' => '未使用',
                        'used' => '已使用',
                        'disabled' => '已禁用',
                    ]),

                SelectFilter::make('batch_id')
                    ->label('批次')
                    ->options(function () {
                        return Card::whereNotNull('batch_id')
                            ->distinct()
                            ->pluck('batch_id', 'batch_id')
                            ->toArray();
                    }),
            ])
            ->headerActions([
                Action::make('generate')
                    ->label('生成卡密')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        TextInput::make('amount')
                            ->label('面值金额 (元)')
                            ->numeric()
                            ->required()
                            ->minValue(0.01),

                        TextInput::make('count')
                            ->label('生成数量')
                            ->numeric()
                            ->required()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(1000),

                        Textarea::make('remark')
                            ->label('备注')
                            ->rows(2),
                    ])
                    ->action(function (array $data): void {
                        $batchId = time();
                        $cards = [];

                        for ($i = 0; $i < $data['count']; $i++) {
                            $cards[] = [
                                'card_no' => Card::generateCardNo(),
                                'card_secret' => Card::generateSecret(),
                                'amount' => $data['amount'],
                                'status' => 'unused',
                                'batch_id' => $batchId,
                                'remark' => $data['remark'] ?? null,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }

                        Card::insert($cards);
                    })
                    ->after(function () {
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('卡密生成成功')
                            ->send();
                    }),

                Action::make('export')
                    ->label('导出未使用')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn () => route('admin.cards.export'))
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('disable')
                    ->label('禁用')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Card $record) => $record->status === 'unused')
                    ->action(fn (Card $record) => $record->update(['status' => 'disabled'])),

                Action::make('enable')
                    ->label('启用')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Card $record) => $record->status === 'disabled')
                    ->action(fn (Card $record) => $record->update(['status' => 'unused'])),
            ])
            ->toolbarActions([
                                    DeleteBulkAction::make(),
            ]);
    }
}
