<?php

namespace App\Filament\Admin\Resources\Videos\Tables;


use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
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
                ImageColumn::make('cover')
                    ->label('封面')
                    ->width(80)
                    ->height(45)
                    ->defaultImageUrl('https://via.placeholder.com/160x90/1a1a2e/fff?text=Video'),

                TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('category')
                    ->label('分类')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '电影' => 'primary',
                        '电视剧' => 'success',
                        '动漫' => 'info',
                        '综艺' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('genre')
                    ->label('题材')
                    ->placeholder('-'),

                TextColumn::make('quality')
                    ->label('画质')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '4K' => 'danger',
                        'FHD' => 'warning',
                        'HD' => 'success',
                        'SD' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('duration')
                    ->label('时长')
                    ->formatStateUsing(fn ($state) => $state ? gmdate('i:s', $state) : '-')
                    ->sortable(),

                TextColumn::make('score')
                    ->label('评分')
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('views')
                    ->label('播放')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state >= 10000 ? round($state/10000, 1).'万' : $state),

                TextColumn::make('year')
                    ->label('年份')
                    ->placeholder('-'),

                IconColumn::make('is_recommend')
                    ->label('推荐')
                    ->boolean(),

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
                        '电影' => '电影',
                        '电视剧' => '电视剧',
                        '动漫' => '动漫',
                        '综艺' => '综艺',
                        '纪录片' => '纪录片',
                        '短片' => '短片',
                    ]),

                SelectFilter::make('quality')
                    ->label('画质')
                    ->options([
                        'SD' => '标清',
                        'HD' => '高清',
                        'FHD' => '全高清',
                        '4K' => '4K',
                    ]),

                TernaryFilter::make('enabled')
                    ->label('启用状态')
                    ->boolean()
                    ->trueLabel('已启用')
                    ->falseLabel('已禁用'),

                TernaryFilter::make('is_recommend')
                    ->label('推荐状态')
                    ->boolean()
                    ->trueLabel('已推荐')
                    ->falseLabel('未推荐'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                                    DeleteBulkAction::make(),
            ]);
    }
}
