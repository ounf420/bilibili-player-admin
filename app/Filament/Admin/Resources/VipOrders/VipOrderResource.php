<?php

namespace App\Filament\Admin\Resources\VipOrders;

use App\Filament\Admin\Resources\VipOrders\Pages\ListVipOrders;
use App\Filament\Admin\Resources\VipOrders\Tables\VipOrdersTable;
use App\Models\VipOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VipOrderResource extends Resource
{
    protected static ?string $model = VipOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'VIP订单';

    protected static ?string $modelLabel = 'VIP订单';

    protected static ?string $pluralModelLabel = 'VIP订单';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return VipOrdersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVipOrders::route('/'),
        ];
    }
}
