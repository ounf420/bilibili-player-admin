<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PlayerPlanResource\Pages;
use App\Models\PlayerPlan;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class PlayerPlanResource extends Resource
{
    protected static ?string $model = PlayerPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = '播放器套餐';

    protected static string|\UnitEnum|null $navigationGroup = '播放器管理';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('基本信息')
                    ->schema([
                        TextInput::make('name')
                            ->label('套餐名称')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('code')
                            ->label('套餐编码')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->helperText('如: free, basic, premium, ultimate'),

                        Select::make('type')
                            ->label('套餐类型')
                            ->options([
                                'plan' => '版本套餐',
                                'ad_module' => '广告模块',
                                'ad_free' => '去广告',
                            ])
                            ->default('plan')
                            ->required(),

                        Select::make('level')
                            ->label('版本等级')
                            ->options([
                                0 => '免费版',
                                1 => '基础版',
                                2 => '高级版',
                                3 => '旗舰版',
                            ])
                            ->required(),

                        Toggle::make('is_active')
                            ->label('是否启用')
                            ->default(true),
                    ])->columns(2),

                Section::make('时长与价格')
                    ->schema([
                        Select::make('duration_type')
                            ->label('时长类型')
                            ->options([
                                0 => '无期限',
                                1 => '月',
                                2 => '季',
                                3 => '年',
                                4 => '永久',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, $set) => 
                                $set('duration_days', match ((int) $state) {
                                    0 => 0,
                                    1 => 30,
                                    2 => 90,
                                    3 => 365,
                                    4 => 36500,
                                    default => 30,
                                })
                            ),

                        TextInput::make('duration_days')
                            ->label('有效天数')
                            ->numeric()
                            ->required(),

                        TextInput::make('price')
                            ->label('原价')
                            ->numeric()
                            ->prefix('¥')
                            ->required(),

                        TextInput::make('sale_price')
                            ->label('售价')
                            ->numeric()
                            ->prefix('¥')
                            ->required(),

                        TextInput::make('price_monthly')
                            ->label('月付价格')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0),

                        TextInput::make('price_yearly')
                            ->label('年付价格')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0),

                        TextInput::make('price_permanent')
                            ->label('永久价格')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0),
                    ])->columns(2),

                Section::make('功能权限')
                    ->schema([
                        Toggle::make('features.custom_appearance')
                            ->label('自定义外观')
                            ->helperText('主题色、水印、Logo等'),

                        Toggle::make('features.custom_logo')
                            ->label('自定义Logo'),

                        Toggle::make('features.custom_domain')
                            ->label('自定义域名')
                            ->helperText('绑定自己的域名'),

                        Toggle::make('features.material_module')
                            ->label('广告模块')
                            ->helperText('自定义广告投放'),

                        Toggle::make('features.super_material')
                            ->label('超级广告')
                            ->helperText('高级广告功能'),
                    ])->columns(3),

                Section::make('其他设置')
                    ->schema([
                        TextInput::make('player_limit')
                            ->label('播放器数量限制')
                            ->numeric()
                            ->default(1),

                        TextInput::make('badge')
                            ->label('角标文字')
                            ->maxLength(50)
                            ->helperText('如: 推荐、热门、买断'),

                        Textarea::make('description')
                            ->label('套餐描述')
                            ->rows(3),

                        TextInput::make('sort_order')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('套餐名称')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'plan' => 'info',
                        'ad_module' => 'warning',
                        'ad_free' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'plan' => '版本套餐',
                        'ad_module' => '广告模块',
                        'ad_free' => '去广告',
                        default => '未知',
                    }),

                Tables\Columns\TextColumn::make('level')
                    ->label('版本')
                    ->badge()
                    ->color(fn (int $state) => match ($state) {
                        0 => 'gray',
                        1 => 'info',
                        2 => 'warning',
                        3 => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        0 => '免费版',
                        1 => '基础版',
                        2 => '高级版',
                        3 => '旗舰版',
                        default => '未知',
                    }),

                Tables\Columns\TextColumn::make('duration_type')
                    ->label('时长')
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        0 => '无期限',
                        1 => '月卡',
                        2 => '季卡',
                        3 => '年卡',
                        4 => '永久',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('原价')
                    ->prefix('¥'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('售价')
                    ->prefix('¥')
                    ->sortable(),

                Tables\Columns\TextColumn::make('player_limit')
                    ->label('播放器数')
                    ->numeric(),

                Tables\Columns\TextColumn::make('badge')
                    ->label('角标')
                    ->placeholder('-'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('启用'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'plan' => '版本套餐',
                        'ad_module' => '广告模块',
                        'ad_free' => '去广告',
                    ]),
                Tables\Filters\SelectFilter::make('level')
                    ->label('版本')
                    ->options([
                        0 => '免费版',
                        1 => '基础版',
                        2 => '高级版',
                        3 => '旗舰版',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
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
