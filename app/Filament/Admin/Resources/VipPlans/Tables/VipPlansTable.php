<?php

namespace App\Filament\Admin\Resources\VipPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VipPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('套餐名称')->searchable()->sortable(),
                TextColumn::make('level')->label('等级')->badge()
                    ->formatStateUsing(fn (int $state): string => $state == 2 ? 'SVIP' : 'VIP')
                    ->color(fn (int $state): string => $state == 2 ? 'warning' : 'info'),
                TextColumn::make('duration_days')->label('天数')->sortable(),
                TextColumn::make('price')->label('原价')->prefix('¥')->sortable(),
                TextColumn::make('sale_price')->label('售价')->prefix('¥')->sortable(),
                TextColumn::make('badge')->label('角标'),
                IconColumn::make('is_active')->label('启用')->boolean()->sortable(),
                TextColumn::make('sort_order')->label('排序')->sortable(),
                TextColumn::make('created_at')->label('创建时间')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order', 'desc')
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
