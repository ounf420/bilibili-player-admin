<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CardResource\Pages;
use App\Filament\Admin\Resources\CardResource\Schemas\CardForm;
use App\Filament\Admin\Resources\CardResource\Tables\CardsTable;
use App\Models\Card;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = '卡密管理';

    protected static string|\UnitEnum|null $navigationGroup = '财务管理';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return CardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CardsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
