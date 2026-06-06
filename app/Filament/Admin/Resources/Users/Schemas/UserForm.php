<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
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
                        2 => '待验证',
                    ])
                    ->required()
                    ->default(1),

                TextInput::make('avatar')
                    ->label('头像URL')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('gender')
                    ->label('性别')
                    ->options([
                        0 => '未知',
                        1 => '男',
                        2 => '女',
                    ])
                    ->default(0),

                DatePicker::make('birthday')
                    ->label('生日'),

                TextInput::make('real_name')
                    ->label('真实姓名')
                    ->maxLength(255),

                TextInput::make('id_card')
                    ->label('身份证号')
                    ->maxLength(255),

                DateTimePicker::make('email_verified_at')
                    ->label('邮箱验证时间'),

                DateTimePicker::make('last_login_at')
                    ->label('最后登录时间')
                    ->disabled(),

                TextInput::make('last_login_ip')
                    ->label('最后登录IP')
                    ->disabled(),

                TextInput::make('wechat_openid')
                    ->label('微信OpenID')
                    ->maxLength(255),

                TextInput::make('qq_openid')
                    ->label('QQ OpenID')
                    ->maxLength(255),

                TextInput::make('weibo_uid')
                    ->label('微博UID')
                    ->maxLength(255),

                TextInput::make('github_id')
                    ->label('GitHub ID')
                    ->maxLength(255),
            ]);
    }
}
