<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class UsersTable
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
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? $record->nickname ?? 'U') . '&color=7F9CF5&background=EBF4FF'),

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

                TextColumn::make('nickname')
                    ->label('昵称')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'danger',
                        1 => 'success',
                        2 => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => '禁用',
                        1 => '正常',
                        2 => '待验证',
                        default => '未知',
                    }),

                TextColumn::make('vip_level')
                    ->label('VIP等级')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',
                        1 => 'info',
                        2 => 'warning',
                        3 => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => '普通用户',
                        1 => 'VIP会员',
                        2 => '高级VIP',
                        3 => '年费VIP',
                        default => '未知',
                    }),

                TextColumn::make('vip_expire_at')
                    ->label('VIP到期')
                    ->dateTime('Y-m-d')
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('性别')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => '未知',
                        1 => '男',
                        2 => '女',
                        default => '未知',
                    }),

                TextColumn::make('last_login_at')
                    ->label('最后登录')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('last_login_ip')
                    ->label('登录IP')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('注册时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        0 => '禁用',
                        1 => '正常',
                        2 => '待验证',
                    ]),

                SelectFilter::make('vip_level')
                    ->label('VIP等级')
                    ->options([
                        0 => '普通用户',
                        1 => 'VIP会员',
                        2 => '高级VIP',
                        3 => '年费VIP',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
