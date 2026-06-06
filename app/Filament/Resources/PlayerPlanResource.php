<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerPlanResource\Pages;
use App\Models\PlayerPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlayerPlanResource extends Resource
{
    protected static ?string $model = PlayerPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = '播放器套餐';

    protected static ?string $navigationGroup = '播放器商城';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('套餐名称')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('code')
                            ->label('套餐编码')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('level')
                            ->label('版本等级')
                            ->options([
                                1 => '基础版',
                                2 => '专业版',
                                3 => '旗舰版',
                            ])
                            ->required(),

                        Forms\Components\Select::make('duration_type')
                            ->label('时长类型')
                            ->options([
                                1 => '月',
                                2 => '季',
                                3 => '年',
                                4 => '永久',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (Forms\Set $set, $state) => 
                                $set('duration_days', match ((int) $state) {
                                    1 => 30,
                                    2 => 90,
                                    3 => 365,
                                    4 => 36500,
                                    default => 30,
                                })
                            ),

                        Forms\Components\TextInput::make('duration_days')
                            ->label('有效天数')
                            ->numeric()
                            ->required(),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('原价')
                            ->numeric()
                            ->prefix('¥')
                            ->required(),

                        Forms\Components\TextInput::make('sale_price')
                            ->label('售价')
                            ->numeric()
                            ->prefix('¥')
                            ->required(),
                    ]),

                Forms\Components\KeyValue::make('features')
                    ->label('特权说明')
                    ->keyLabel('特权')
                    ->valueLabel('说明'),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('badge')
                            ->label('角标文字')
                            ->maxLength(50),

                        Forms\Components\Toggle::make('is_active')
                            ->label('是否启用')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('套餐名称')
                    ->searchable(),

                Tables\Columns\TextColumn::make('level_text')
                    ->label('版本')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        '基础版' => 'info',
                        '专业版' => 'warning',
                        '旗舰版' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('duration_type_text')
                    ->label('时长'),

                Tables\Columns\TextColumn::make('price')
                    ->label('原价')
                    ->prefix('¥'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('售价')
                    ->prefix('¥')
                    ->sortable(),

                Tables\Columns\TextColumn::make('badge')
                    ->label('角标')
                    ->placeholder('-'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('启用'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->label('版本')
                    ->options([
                        1 => '基础版',
                        2 => '专业版',
                        3 => '旗舰版',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayerPlans::route('/'),
            'create' => Pages\CreatePlayerPlan::route('/create'),
            'edit' => Pages\EditPlayerPlan::route('/{record}/edit'),
        ];
    }
}
