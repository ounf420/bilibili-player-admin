<?php

namespace App\Filament\Admin\Resources\VipCards;

use App\Filament\Admin\Resources\VipCards\Pages;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class VipCardResource extends Resource
{
    protected static ?string $model = null;
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedTicket;
    protected static ?string $navigationLabel = '卡密管理';
    protected static string | \UnitEnum | null $navigationGroup = 'VIP管理';
    protected static ?int $navigationSort = 8;

    public static function table(Table $table): Table
    {
        return $table
            ->query(DB::table('vip_cards')
                ->leftJoin('vip_plans', 'vip_cards.plan_id', '=', 'vip_plans.id')
                ->leftJoin('users as u', 'vip_cards.used_by', '=', 'u.id')
                ->select('vip_cards.*', 'vip_plans.name as plan_name', 'u.nickname as used_by_name', 'u.username as used_by_username'))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('card_no')->label('卡号')->copyable()->fontFamily('mono'),
                TextColumn::make('card_secret')->label('卡密')->copyable()->fontFamily('mono'),
                TextColumn::make('plan_name')->label('套餐'),
                TextColumn::make('vip_level')->label('等级'),
                TextColumn::make('duration_days')->label('天数'),
                TextColumn::make('status')->label('状态')
                    ->formatStateUsing(fn ($state) => match($state) {
                        0 => '🟢 未使用',
                        1 => '🔴 已使用',
                        2 => '⚪ 已禁用',
                        default => '未知',
                    }),
                TextColumn::make('used_by_name')->label('使用者')
                    ->formatStateUsing(fn ($state, $record) => $record->used_by_name ?: $record->used_by_username ?: '-'),
                TextColumn::make('used_at')->label('使用时间')->dateTime('Y-m-d H:i'),
                TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([0 => '未使用', 1 => '已使用', 2 => '已禁用']),
                \Filament\Tables\Filters\SelectFilter::make('vip_level')
                    ->label('VIP等级')
                    ->options([1 => '黄金', 2 => '钻石', 3 => '星钻']),
            ])
            ->headerActions([
                Action::make('generate')
                    ->label('批量生成')
                    ->icon(Heroicon::OutlinedPlus)
                    ->form([
                        \Filament\Forms\Components\Select::make('plan_id')
                            ->label('选择套餐')
                            ->options(DB::table('vip_plans')->where('is_active', 1)->pluck('name', 'id'))
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('count')
                            ->label('数量')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(500)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $plan = DB::table('vip_plans')->where('id', $data['plan_id'])->first();
                        if (!$plan) return;
                        
                        $cards = [];
                        for ($i = 0; $i < $data['count']; $i++) {
                            $cards[] = [
                                'card_no' => strtoupper(\Illuminate\Support\Str::random(16)),
                                'card_secret' => strtoupper(\Illuminate\Support\Str::random(24)),
                                'plan_id' => $plan->id,
                                'vip_level' => $plan->level,
                                'duration_days' => $plan->duration_days,
                                'status' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        DB::table('vip_cards')->insert($cards);
                        
                        \Filament\Notifications\Notification::make()
                            ->title("成功生成 {$data['count']} 张卡密")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('disable')
                    ->label('禁用')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status == 0)
                    ->action(fn ($record) => DB::table('vip_cards')->where('id', $record->id)->update(['status' => 2, 'updated_at' => now()])),
                Action::make('enable')
                    ->label('启用')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->visible(fn ($record) => $record->status == 2)
                    ->action(fn ($record) => DB::table('vip_cards')->where('id', $record->id)->update(['status' => 0, 'updated_at' => now()])),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVipCards::route('/'),
        ];
    }
}
