<?php

namespace App\Filament\Admin\Resources\Decorations\Tables;


use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DecorationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('方案名称')
                    ->searchable(),

                TextColumn::make('badge_text')
                    ->label('角标'),

                TextColumn::make('badge_color')
                    ->label('角标色')
                    ->formatStateUsing(fn ($state) => $state ?: '默认'),

                TextColumn::make('progress_color')
                    ->label('进度条色'),

                TextColumn::make('animation')
                    ->label('动画')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'none' => '无',
                        'fade' => '淡入',
                        'slide' => '滑入',
                        'zoom' => '缩放',
                        default => $state,
                    }),

                TextColumn::make('cta_style')
                    ->label('按钮形状')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'rounded' => '圆角',
                        'pill' => '胶囊',
                        'rect' => '方形',
                        default => $state,
                    }),

                IconColumn::make('enabled')
                    ->label('启用')
                    ->boolean(),

                TextColumn::make('ads_count')
                    ->label('使用中')
                    ->counts('ads')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                                    DeleteBulkAction::make(),
            ]);
    }
}
