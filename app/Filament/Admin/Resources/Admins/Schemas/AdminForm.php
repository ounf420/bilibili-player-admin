<?php

namespace App\Filament\Admin\Resources\Admins\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('姓名')
                    ->required()
                    ->maxLength(255),

                TextInput::make('username')
                    ->label('用户名')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('email')
                    ->label('邮箱')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                TextInput::make('phone')
                    ->label('手机号')
                    ->tel()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                TextInput::make('nickname')
                    ->label('昵称')
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('密码')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('状态')
                    ->options([
                        0 => '禁用',
                        1 => '正常',
                    ])
                    ->required()
                    ->default(1),

                TextInput::make('avatar')
                    ->label('头像URL')
                    ->maxLength(255)
                    ->columnSpanFull(),

                DateTimePicker::make('last_login_at')
                    ->label('最后登录时间')
                    ->disabled(),

                TextInput::make('last_login_ip')
                    ->label('最后登录IP')
                    ->disabled(),
            ]);
    }
}
