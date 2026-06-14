<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserBalanceResource\Pages;
use App\Filament\Admin\Resources\UserBalanceResource\Schemas\UserBalanceForm;
use App\Filament\Admin\Resources\UserBalanceResource\Tables\UserBalancesTable;
use App\Models\UserBalance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserBalanceResource extends Resource
{
    protected static ?string $model = UserBalance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static ?string $navigationLabel = '用户余额';

    protected static string|\UnitEnum|null $navigationGroup = '财务管理';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return UserBalanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserBalancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserBalances::route('/'),
            'create' => Pages\CreateUserBalance::route('/create'),
            'edit' => Pages\EditUserBalance::route('/{record}/edit'),
        ];
    }
}
