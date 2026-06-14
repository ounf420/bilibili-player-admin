<?php

namespace App\Filament\Admin\Resources\CardResource;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('卡密信息')
                    ->schema([
                        TextInput::make('card_no')
                            ->label('卡号')
                            ->required()
                            ->maxLength(32)
                            ->unique(ignoreRecord: true),

                        TextInput::make('card_secret')
                            ->label('卡密')
                            ->required()
                            ->maxLength(64),

                        TextInput::make('amount')
                            ->label('面值金额')
                            ->numeric()
                            ->prefix('¥')
                            ->required(),

                        Select::make('status')
                            ->label('状态')
                            ->options([
                                'unused' => '未使用',
                                'used' => '已使用',
                                'disabled' => '已禁用',
                            ])
                            ->default('unused')
                            ->required(),

                        TextInput::make('batch_id')
                            ->label('批次号')
                            ->numeric(),

                        Textarea::make('remark')
                            ->label('备注')
                            ->rows(2),
                    ])->columns(2),

                Section::make('使用信息')
                    ->schema([
                        TextInput::make('used_by')
                            ->label('使用者ID')
                            ->numeric()
                            ->disabled(),

                        TextInput::make('used_at')
                            ->label('使用时间')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }
}
