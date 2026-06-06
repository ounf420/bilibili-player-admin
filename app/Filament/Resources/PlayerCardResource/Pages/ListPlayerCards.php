<?php

namespace App\Filament\Resources\PlayerCardResource\Pages;

use App\Filament\Resources\PlayerCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Notifications\Notification;

class ListPlayerCards extends ListRecords
{
    protected static string $resource = PlayerCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('batchCreate')
                ->label('批量生成')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    Forms\Components\Select::make('card_type')
                        ->label('卡类型')
                        ->options([
                            'plan' => '版本卡',
                            'quota' => '额度卡',
                        ])
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('plan_id')
                        ->label('关联套餐')
                        ->options(\App\Models\PlayerPlan::active()->pluck('name', 'id'))
                        ->visible(fn (Forms\Get $get) => $get('card_type') === 'plan')
                        ->requiredIf('card_type', 'plan'),

                    Forms\Components\TextInput::make('quota_amount')
                        ->label('额度数量')
                        ->numeric()
                        ->visible(fn (Forms\Get $get) => $get('card_type') === 'quota')
                        ->requiredIf('card_type', 'quota'),

                    Forms\Components\TextInput::make('count')
                        ->label('生成数量')
                        ->numeric()
                        ->default(10)
                        ->required()
                        ->maxValue(1000),
                ])
                ->action(function (array $data) {
                    $count = (int) $data['count'];
                    $cards = [];

                    for ($i = 0; $i < $count; $i++) {
                        $cards[] = [
                            'card_no' => \App\Models\PlayerCard::generateCardNo(),
                            'card_secret' => \App\Models\PlayerCard::generateCardSecret(),
                            'card_type' => $data['card_type'],
                            'plan_id' => $data['plan_id'] ?? null,
                            'quota_amount' => $data['quota_amount'] ?? 0,
                            'status' => 0,
                            'created_by' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    \App\Models\PlayerCard::insert($cards);

                    Notification::make()
                        ->title("成功生成 {$count} 张卡密")
                        ->success()
                        ->send();
                }),
        ];
    }
}
