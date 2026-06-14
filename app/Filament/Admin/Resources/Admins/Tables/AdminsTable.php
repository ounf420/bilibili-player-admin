<?php

namespace App\Filament\Admin\Resources\Admins\Tables;


use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                ImageColumn::make('avatar')
                    ->label('头像')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'A') . '&color=FFFFFF&background=6366F1'),

                TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),

                TextColumn::make('username')
                    ->label('用户名')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('手机号')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (int $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (int $state): string => $state ? '正常' : '禁用'),

                TextColumn::make('last_login_at')
                    ->label('最后登录')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('last_login_ip')
                    ->label('登录IP')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
            ]);
    }
}
