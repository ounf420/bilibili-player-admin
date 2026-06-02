<?php

namespace App\Filament\Admin\Resources\VipPlans\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VipPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('基本信息')->components([
                TextInput::make('name')->label('套餐名称')->required()->maxLength(100),
                Select::make('level')->label('VIP等级')->options([
                    1 => 'VIP', 2 => 'SVIP',
                ])->required(),
                TextInput::make('duration_days')->label('有效天数')->required()->numeric()->minValue(1),
                TextInput::make('price')->label('原价')->required()->numeric()->prefix('¥'),
                TextInput::make('sale_price')->label('售价')->required()->numeric()->prefix('¥'),
            ])->columns(2),
            Section::make('展示设置')->components([
                TextInput::make('badge')->label('角标文字')->placeholder('如：年卡·推荐'),
                Textarea::make('features')->label('特权说明')->placeholder('每行一个特权')->rows(4)
                    ->afterStateHydrated(fn ($state, $set) => is_array($state) ? $set('features', implode("\n", $state)) : null)
                    ->dehydrateStateUsing(fn ($state) => array_filter(array_map('trim', explode("\n", $state)))),
                Checkbox::make('is_active')->label('是否启用')->default(true),
                TextInput::make('sort_order')->label('排序权重')->numeric()->default(0),
            ])->columns(2),
        ]);
    }
}
