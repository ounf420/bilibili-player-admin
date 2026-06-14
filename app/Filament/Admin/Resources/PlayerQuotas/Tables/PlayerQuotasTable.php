<?php

namespace App\Filament\Admin\Resources\PlayerQuotas\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlayerQuotasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.username')
                    ->label('用户名')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('user.email')
                    ->label('邮箱')
                    ->searchable(),
                
                TextColumn::make('total_quota')
                    ->label('总额度')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('used_quota')
                    ->label('已使用')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                
                TextColumn::make('available_quota')
                    ->label('可用额度')
                    ->getStateUsing(fn ($record) => $record->available_quota)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                
                TextColumn::make('bonus_quota')
                    ->label('赠送额度')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'username')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
