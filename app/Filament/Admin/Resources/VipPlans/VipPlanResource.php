<?php

namespace App\Filament\Admin\Resources\VipPlans;

use App\Filament\Admin\Resources\VipPlans\Pages\CreateVipPlan;
use App\Filament\Admin\Resources\VipPlans\Pages\EditVipPlan;
use App\Filament\Admin\Resources\VipPlans\Pages\ListVipPlans;
use App\Filament\Admin\Resources\VipPlans\Schemas\VipPlanForm;
use App\Filament\Admin\Resources\VipPlans\Tables\VipPlansTable;
use App\Models\VipPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VipPlanResource extends Resource
{
    protected static ?string $model = VipPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'VIP套餐管理';

    protected static ?string $modelLabel = 'VIP套餐';

    protected static ?string $pluralModelLabel = 'VIP套餐';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return VipPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VipPlansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVipPlans::route('/'),
            'create' => CreateVipPlan::route('/create'),
            'edit' => EditVipPlan::route('/{record}/edit'),
        ];
    }
}
