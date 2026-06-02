<?php

namespace App\Filament\Admin\Resources\Videos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),

                TextColumn::make('category')
                    ->label('分类')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('region')
                    ->label('地区')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('year')
                    ->label('年份')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('score')
                    ->label('评分')
                    ->numeric()
                    ->sortable()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 8.5 => 'success',
                        $state >= 7.0 => 'info',
                        $state >= 5.0 => 'warning',
                        default => 'gray',
                    })
                    ->placeholder('-'),

                TextColumn::make('vip_level')
                    ->label('VIP')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'VIP', 2 => 'SVIP',
                        default => '免费'
                    })
                    ->color(fn (int $state): string => match ($state) {
                        2 => 'warning', 1 => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('quality')
                    ->label('画质')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '4K' => 'warning', 'FHD' => 'success', 'HD' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('views')
                    ->label('播放')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('likes')
                    ->label('点赞')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_recommend')
                    ->label('推荐')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('enabled')
                    ->label('启用')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label('分类')
                    ->options([
                        '电影' => '电影', '电视剧' => '电视剧', '动漫' => '动漫',
                        '综艺' => '综艺', '纪录片' => '纪录片',
                    ]),

                SelectFilter::make('vip_level')
                    ->label('VIP等级')
                    ->options([0 => '免费', 1 => 'VIP', 2 => 'SVIP']),

                TernaryFilter::make('enabled')
                    ->label('启用状态')
                    ->boolean()
                    ->trueLabel('已启用')
                    ->falseLabel('已禁用'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
