<?php

namespace App\Filament\Admin\Resources\PlayerQuotas\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlayerQuotaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('用户')
                ->options(User::where('is_admin', 0)->pluck('username', 'id'))
                ->searchable()
                ->required()
                ->createOptionForm([
                    TextInput::make('username')
                        ->label('用户名')
                        ->required(),
                ])
                ->preload(),
            
            TextInput::make('total_quota')
                ->label('总额度')
                ->numeric()
                ->default(1)
                ->minValue(0)
                ->required(),
            
            TextInput::make('used_quota')
                ->label('已使用')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->required(),
            
            TextInput::make('bonus_quota')
                ->label('赠送额度')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->required()
                ->helperText('注册赠送或其他赠送的额度数量'),
        ]);
    }
}
