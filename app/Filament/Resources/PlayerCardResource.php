<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerCardResource\Pages;
use App\Models\PlayerCard;
use App\Models\PlayerPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlayerCardResource extends Resource
{
    protected static ?string $model = PlayerCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = '播放器卡密';

    protected static ?string $navigationGroup = '播放器商城';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('card_type')
                    ->label('卡类型')
                    ->options([
                        'plan' => '版本卡',
                        'quota' => '额度卡',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('plan_id', null)),

                Forms\Components\Select::make('plan_id')
                    ->label('关联套餐')
                    ->options(PlayerPlan::active()->pluck('name', 'id'))
                    ->visible(fn (Forms\Get $get) => $get('card_type') === 'plan')
                    ->requiredIf('card_type', 'plan'),

                Forms\Components\TextInput::make('quota_amount')
                    ->label('额度数量')
                    ->numeric()
                    ->visible(fn (Forms\Get $get) => $get('card_type') === 'quota')
                    ->requiredIf('card_type', 'quota'),

                Forms\Components\TextInput::make('card_no')
                    ->label('卡号')
                    ->default(fn () => PlayerCard::generateCardNo())
                    ->required()
                    ->unique(),

                Forms\Components\TextInput::make('card_secret')
                    ->label('卡密')
                    ->default(fn () => PlayerCard::generateCardSecret())
                    ->required()
                    ->unique(),

                Forms\Components\Textarea::make('remark')
                    ->label('备注')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('card_no')
                    ->label('卡号')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('card_secret')
                    ->label('卡密')
                    ->limit(10)
                    ->copyable(),

                Tables\Columns\TextColumn::make('card_type_text')
                    ->label('类型')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        '版本卡' => 'primary',
                        '额度卡' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('plan.name')
                    ->label('关联套餐')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('quota_amount')
                    ->label('额度数量')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status_text')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        '未使用' => 'success',
                        '已使用' => 'gray',
                        '已禁用' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('usedByUser.username')
                    ->label('使用者')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('used_at')
                    ->label('使用时间')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('card_type')
                    ->label('卡类型')
                    ->options([
                        'plan' => '版本卡',
                        'quota' => '额度卡',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        0 => '未使用',
                        1 => '已使用',
                        2 => '已禁用',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('disable')
                    ->label('禁用')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn (PlayerCard $record) => $record->status === PlayerCard::STATUS_UNUSED)
                    ->requiresConfirmation()
                    ->action(fn (PlayerCard $record) => $record->update(['status' => PlayerCard::STATUS_DISABLED])),

                Tables\Actions\Action::make('enable')
                    ->label('启用')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->visible(fn (PlayerCard $record) => $record->status === PlayerCard::STATUS_DISABLED)
                    ->action(fn (PlayerCard $record) => $record->update(['status' => PlayerCard::STATUS_UNUSED])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('batchDisable')
                        ->label('批量禁用')
                        ->icon('heroicon-o-lock-closed')
                        ->action(fn ($records) => $records->each->update(['status' => PlayerCard::STATUS_DISABLED])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayerCards::route('/'),
            'create' => Pages\CreatePlayerCard::route('/create'),
            'edit' => Pages\EditPlayerCard::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', PlayerCard::STATUS_UNUSED)->count();
    }
}
