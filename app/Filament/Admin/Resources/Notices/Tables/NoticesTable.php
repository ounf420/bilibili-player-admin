<?php

namespace App\Filament\Admin\Resources\Notices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class NoticesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                IconColumn::make('is_top')
                    ->label('置顶')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'system' => 'info',
                        'activity' => 'success',
                        'update' => 'warning',
                        'maintenance' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'system' => '系统公告',
                        'activity' => '活动公告',
                        'update' => '更新日志',
                        'maintenance' => '维护通知',
                        default => $state,
                    }),

                TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',
                        1 => 'success',
                        2 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => '草稿',
                        1 => '已发布',
                        2 => '已下线',
                        default => '未知',
                    }),

                TextColumn::make('target_users')
                    ->label('目标')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all' => '全部',
                        'new' => '新用户',
                        default => $state,
                    }),

                TextColumn::make('position')
                    ->label('位置')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all' => 'info',
                        'home' => 'success',
                        'v' => 'warning',
                        'account' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'all' => '全站',
                        'home' => '首页',
                        
                        'account' => '账号中心',
                        default => $state,
                    }),

                TextColumn::make('read_count')
                    ->label('阅读量')
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('发布时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('过期时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'system' => '系统公告',
                        'activity' => '活动公告',
                        'update' => '更新日志',
                        'maintenance' => '维护通知',
                    ]),

                SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        0 => '草稿',
                        1 => '已发布',
                        2 => '已下线',
                    ]),

                TrashedFilter::make(),
            ])
            ->defaultSort('is_top', 'desc')
            ->defaultSort('sort_order', 'desc')
            ->defaultSort('published_at', 'desc')
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
