<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlayerOrderResource\Pages;
use App\Models\PlayerOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlayerOrderResource extends Resource
{
    protected static ?string $model = PlayerOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = '播放器订单';

    protected static ?string $navigationGroup = '播放器商城';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('order_no')
                            ->label('订单号')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options([
                                0 => '待支付',
                                1 => '已支付',
                                2 => '已取消',
                                3 => '已退款',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('user.username')
                            ->label('用户')
                            ->disabled(),

                        Forms\Components\TextInput::make('product_name')
                            ->label('产品')
                            ->disabled(),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('金额')
                            ->prefix('¥')
                            ->disabled(),

                        Forms\Components\TextInput::make('payment_method_text')
                            ->label('支付方式')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('支付时间')
                            ->disabled(),
                    ]),

                Forms\Components\Textarea::make('remark')
                    ->label('备注')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label('订单号')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.username')
                    ->label('用户')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('产品')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('金额')
                    ->prefix('¥')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method_text')
                    ->label('支付方式')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        '卡密兑换' => 'info',
                        '支付宝' => 'primary',
                        '微信支付' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status_text')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        '待支付' => 'warning',
                        '已支付' => 'success',
                        '已取消' => 'gray',
                        '已退款' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('支付时间')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        0 => '待支付',
                        1 => '已支付',
                        2 => '已取消',
                        3 => '已退款',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('支付方式')
                    ->options([
                        'card' => '卡密兑换',
                        'alipay' => '支付宝',
                        'wechat' => '微信支付',
                    ]),

                Tables\Filters\SelectFilter::make('product_type')
                    ->label('产品类型')
                    ->options([
                        'plan' => '版本套餐',
                        'quota' => '播放器额度',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirmPay')
                    ->label('确认支付')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PlayerOrder $record) => $record->status === PlayerOrder::STATUS_PENDING && $record->payment_method !== 'card')
                    ->requiresConfirmation()
                    ->action(function (PlayerOrder $record) {
                        $record->update([
                            'status' => PlayerOrder::STATUS_PAID,
                            'paid_at' => now(),
                        ]);

                        // 激活版本或增加额度
                        $shopService = app(\App\Services\PlayerShopService::class);
                        if ($record->product_type === 'plan') {
                            $plan = \App\Models\PlayerPlan::find($record->product_id);
                            if ($plan) {
                                $shopService->activatePlan($record->user_id, $plan, $record);
                            }
                        }
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlayerOrders::route('/'),
            'view' => Pages\ViewPlayerOrder::route('/{record}'),
            'edit' => Pages\EditPlayerOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', PlayerOrder::STATUS_PENDING)->count() ?: null;
    }
}
