<?php

namespace App\Filament\Admin\Resources\UserBalanceResource;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserBalanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('余额信息')
                    ->schema([
                        TextInput::make('user_id')
                            ->label('用户ID')
                            ->numeric()
                            ->required(),

                        TextInput::make('balance')
                            ->label('当前余额')
                            ->prefix('¥')
                            ->numeric()
                            ->required(),

                        TextInput::make('total_recharged')
                            ->label('累计充值')
                            ->prefix('¥')
                            ->numeric()
                            ->disabled(),

                        TextInput::make('total_spent')
                            ->label('累计消费')
                            ->prefix('¥')
                            ->numeric()
                            ->disabled(),
                    ])->columns(2),
            ]);
    }
}
