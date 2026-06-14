<?php

namespace App\Filament\Admin\Resources\Ads\Tables;


use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('广告名称')
                    ->searchable()
                    ->limit(20),

                TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_starts_with($state, 'preroll') => 'danger',
                        $state === 'midroll' => 'warning',
                        str_starts_with($state, 'pause') => 'info',
                        $state === 'postroll' => 'success',
                        $state === 'splash' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'preroll_5s' => '⚡ 5秒',
                        'preroll_15s' => '🎬 15秒',
                        'preroll_30s' => '🎬 30秒',
                        'preroll_60s' => '🎬 60秒',
                        'preroll_trueview' => '⏭️ 可跳过',
                        'midroll' => '⏸️ 中插',
                        'postroll' => '🏁 后贴',
                        'pause_max' => '🖥️ MAX',
                        'pause_mini' => '🔲 浮窗',
                        'splash' => '💫 开屏',
                        'overlay' => '🏷️ 角标',
                        'marquee' => '📜 跑马灯',
                        'qrcode' => '📱 扫码',
                        'interactive' => '🎮 互动',
                        'shake' => '📳 摇一摇',
                        'banner' => '🖼️ 横幅',
                        'brand' => '💎 品牌',
                        default => $state,
                    }),

                TextColumn::make('media_type')
                    ->label('素材')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'video' => 'info',
                        'html' => 'warning',
                        'text' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'image' => '🖼️',
                        'video' => '🎬',
                        'html' => '📄',
                        'text' => '📝',
                        default => $state,
                    }),

                TextColumn::make('brand_name')
                    ->label('品牌')
                    ->placeholder('-'),

                TextColumn::make('duration')
                    ->label('时长')
                    ->suffix('s')
                    ->sortable(),

                TextColumn::make('trigger_time')
                    ->label('触发')
                    ->suffix('s')
                    ->placeholder('-'),

                IconColumn::make('skippable')
                    ->label('跳过')
                    ->boolean(),

                TextColumn::make('decoration.name')
                    ->label('装饰方案')
                    ->placeholder('默认')
                    ->sortable(),

                IconColumn::make('enabled')
                    ->label('启用')
                    ->boolean(),

                TextColumn::make('priority')
                    ->label('优先')
                    ->sortable(),

                TextColumn::make('impressions')
                    ->label('展示')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('clicks')
                    ->label('点击')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('skips')
                    ->label('跳过')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('广告类型')
                    ->options([
                        'preroll_5s' => '⚡ 5秒极速前贴',
                        'preroll_15s' => '🎬 15秒标准前贴',
                        'preroll_30s' => '🎬 30秒标准前贴',
                        'preroll_60s' => '🎬 60秒标准前贴',
                        'preroll_trueview' => '⏭️ TrueView可跳过',
                        'midroll' => '⏸️ 中贴片',
                        'postroll' => '🏁 后贴片',
                        'pause_max' => '🖥️ 暂停MAX全屏',
                        'pause_mini' => '🔲 迷你暂停浮窗',
                        'splash' => '💫 开屏广告',
                        'overlay' => '🏷️ 视频角标',
                        'marquee' => '📜 跑马灯',
                        'qrcode' => '📱 扫码贴片',
                        'interactive' => '🎮 互动贴片',
                        'shake' => '📳 摇一摇',
                        'banner' => '🖼️ 横幅',
                        'brand' => '💎 品牌',
                    ]),

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
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }
}
